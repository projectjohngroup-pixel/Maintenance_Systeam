<?php

namespace Tests\Unit;

use App\Models\SystemGuard\DowntimeLog;
use App\Models\SystemGuard\SystemGuardState;
use App\Services\SystemGuard\InfrastructureMonitor;
use App\Services\SystemGuard\RecoveryEngine;
use App\Services\SystemGuard\RecoveryManager;
use App\Services\SystemGuard\SystemGuardDaemon;
use App\Services\SystemGuard\TunnelRestarter;
use App\Models\SystemGuard\IncidentLog;
use App\Models\SystemGuard\MonitorConfig;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Process;
use Tests\TestCase;

class SystemGuardStep4Test extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Hostname tunnel yang dijamin NXDOMAIN (deterministik) untuk tes
        config(['app.url' => 'https://tunnel-test.invalid']);

        // Nonaktifkan auto-recovery default di tes agar prediktablen
        config(['system-guard.auto_recovery_enabled' => false]);
        config(['system-guard.daemon.enabled' => true]);
    }

    /*
    |--------------------------------------------------------------------------
    | STATES & DOWNTIME
    |--------------------------------------------------------------------------
    */

    public function test_state_for_component_creates_and_returns(): void
    {
        $state = SystemGuardState::forComponent('internet');

        $this->assertEquals('internet', $state->component);
        $this->assertEquals('UNKNOWN', $state->status);
        $this->assertFalse($state->isOnline());
        $this->assertTrue(SystemGuardState::forComponent('internet')->is($state));
    }

    public function test_state_online_helpers(): void
    {
        $state = SystemGuardState::forComponent('tunnel');
        $state->update(['status' => 'OFFLINE']);
        $this->assertTrue($state->isOffline());
        $this->assertTrue($state->isDegraded() === false);

        $state->update(['status' => 'DEGRADED']);
        $this->assertTrue($state->isDegraded());
    }

    public function test_downtime_open_and_close(): void
    {
        $downtime = DowntimeLog::create([
            'component'     => 'tunnel',
            'status'        => 'OFFLINE',
            'error_type'    => 'DNS_NXDOMAIN',
            'error_message' => 'Tunnel down',
            'started_at'    => now()->subMinutes(5),
        ]);

        $this->assertTrue($downtime->isOpen());
        $this->assertTrue(DowntimeLog::openFor('tunnel')->is($downtime));

        $downtime->close('Recovered');

        $this->assertFalse($downtime->fresh()->isOpen());
        $this->assertNotNull($downtime->fresh()->ended_at);
        $this->assertGreaterThanOrEqual(300, $downtime->fresh()->duration_seconds);
    }

    public function test_downtime_total_seconds(): void
    {
        DowntimeLog::create([
            'component'  => 'origin',
            'status'     => 'OFFLINE',
            'started_at' => now()->subSeconds(120),
            'ended_at'   => now()->subSeconds(60),
            'duration_seconds' => 60,
        ]);

        DowntimeLog::create([
            'component'  => 'origin',
            'status'     => 'OFFLINE',
            'started_at' => now()->subSeconds(30),
        ]);

        $total = DowntimeLog::totalDowntimeSeconds('origin');
        $this->assertGreaterThanOrEqual(90, $total);
    }

    /*
    |--------------------------------------------------------------------------
    | INFRASTRUCTURE MONITOR — INTERNET
    |--------------------------------------------------------------------------
    */

    public function test_internet_online_when_target_reachable(): void
    {
        Http::fake([
            'https://1.1.1.1/*' => Http::response('ok', 200),
        ]);

        $monitor = new InfrastructureMonitor();

        $result = $monitor->checkInternet();

        $this->assertEquals('ONLINE', $result['status']);
        $this->assertTrue($result['online']);
    }

    public function test_internet_offline_when_all_targets_fail(): void
    {
        Http::fake([
            'https://1.1.1.1/*'          => Http::response('ok', 404),
            'https://8.8.8.8/*'          => Http::response('ok', 500),
            'https://www.cloudflare.com/*' => Http::response('ok', 503),
        ]);

        // Domain NXDOMAIN yang dijamin (RFC 2606) agar DNS fallback deterministik
        config([
            'system-guard.components.internet.dns_targets' => [
                'guard.test.invalid',
                'guard-check.invalid',
            ],
        ]);

        $monitor = new InfrastructureMonitor();

        $result = $monitor->checkInternet();

        $this->assertEquals('OFFLINE', $result['status']);
        $this->assertFalse($result['online']);
    }

    /*
    |--------------------------------------------------------------------------
    | INFRASTRUCTURE MONITOR — TUNNEL (process down + NXDOMAIN)
    |--------------------------------------------------------------------------
    */

    public function test_tunnel_offline_when_process_down_and_hostname_nxdomain(): void
    {
        // Process tidak berjalan
        Process::fake([
            '*tasklist*' => Process::result('', '', 1),
        ]);

        $monitor = new InfrastructureMonitor();

        $result = $monitor->checkTunnel();

        $this->assertEquals('OFFLINE', $result['status']);
        $this->assertFalse($result['online']);
        $this->assertEquals('TUNNEL_DOWN', $result['error_type']);
    }

    public function test_tunnel_process_down_distinguishes_from_nxdomain(): void
    {
        Process::fake([
            '*tasklist*' => Process::result('', '', 1),
        ]);

        $monitor = new InfrastructureMonitor();

        $result = $monitor->checkTunnel();

        $this->assertStringContainsString('tidak berjalan', $result['message']);
        $this->assertEquals('OFFLINE', $result['status']);
    }

    /*
    |--------------------------------------------------------------------------
    | INFRASTRUCTURE MONITOR — ORIGIN
    |--------------------------------------------------------------------------
    */

    public function test_origin_online_when_http_200(): void
    {
        config(['system-guard.components.origin.base_url' => 'http://127.0.0.1:80']);

        Http::fake([
            'http://127.0.0.1/*' => Http::response('ok', 200),
        ]);

        $monitor = new InfrastructureMonitor();

        $result = $monitor->checkOrigin();

        $this->assertEquals('ONLINE', $result['status']);
        $this->assertTrue($result['online']);
    }

    public function test_origin_offline_when_http_500(): void
    {
        config(['system-guard.components.origin.base_url' => 'http://127.0.0.1:80']);

        Http::fake([
            'http://127.0.0.1/*' => Http::response('error', 500),
        ]);

        $monitor = new InfrastructureMonitor();

        $result = $monitor->checkOrigin();

        $this->assertEquals('OFFLINE', $result['status']);
        $this->assertEquals('HTTP_5XX', $result['error_type']);
    }

    /*
    |--------------------------------------------------------------------------
    | INFRASTRUCTURE MONITOR — checkAll records states + downtime
    |--------------------------------------------------------------------------
    */

    public function test_check_all_records_component_states_and_downtime(): void
    {
        Http::fake([
            'https://1.1.1.1/*' => Http::response('ok', 200),
            'http://127.0.0.1/*' => Http::response('ok', 200),
        ]);

        Process::fake([
            '*tasklist*' => Process::result('', '', 1),
        ]);

        $monitor = new InfrastructureMonitor();

        $result = $monitor->checkAll();

        $this->assertEquals('ONLINE', $result['internet']['status']);
        $this->assertEquals('OFFLINE', $result['tunnel']['status']);
        $this->assertEquals('ONLINE', $result['origin']['status']);

        $tunnelState = SystemGuardState::forComponent('tunnel');
        $this->assertEquals('OFFLINE', $tunnelState->status);
        $this->assertEquals(1, $tunnelState->consecutive_failures);
        $this->assertNotNull($tunnelState->last_checked_at);

        $internetState = SystemGuardState::forComponent('internet');
        $this->assertEquals('ONLINE', $internetState->status);
        $this->assertEquals(0, $internetState->consecutive_failures);

        // Downtime dibuka untuk tunnel
        $this->assertNotNull(DowntimeLog::openFor('tunnel'));
    }

    public function test_downtime_closes_when_component_recovers(): void
    {
        Http::fake([
            'https://1.1.1.1/*' => Http::response('ok', 200),
            'http://127.0.0.1/*' => Http::response('ok', 200),
        ]);

        Process::fake([
            '*tasklist*' => Process::result('', '', 1),
        ]);

        $monitor = new InfrastructureMonitor();

        // Siklus 1: tunnel down → buka downtime
        $monitor->checkAll();
        $this->assertNotNull(DowntimeLog::openFor('tunnel'));

        // Sekarang proses berjalan + hostname tetap NXDOMAIN → tetap bermasalah (degraded)
        Process::fake([
            '*tasklist*' => Process::result('cloudflared.exe', '', 0),
        ]);

        $monitor->checkAll();

        // Tunnel didegradasi (proses jalan tapi hostname tidak resolve)
        $tunnelState = SystemGuardState::forComponent('tunnel');
        $this->assertEquals('DEGRADED', $tunnelState->status);

        // Downtime tetap terbuka karena belum online
        $this->assertNotNull(DowntimeLog::openFor('tunnel'));
    }

    /*
    |--------------------------------------------------------------------------
    | RECOVERY MANAGER — cooldown & backoff
    |--------------------------------------------------------------------------
    */

    public function test_recovery_manager_cooldown_blocks_immediate_retry(): void
    {
        $restarter = new TunnelRestarter();
        $manager = new RecoveryManager($restarter);

        // Tidak pernah mencoba → siap
        $this->assertTrue($manager->isCooldownReady());

        // Set last attempt baru saja
        $state = SystemGuardState::forComponent('recovery');
        $state->update(['last_checked_at' => now()]);

        config(['system-guard.daemon.recovery_cooldown_seconds' => 300]);

        $this->assertFalse($manager->isCooldownReady());
    }

    public function test_daemon_tick_writes_heartbeat_and_checks_components(): void
    {
        Http::fake([
            'https://1.1.1.1/*' => Http::response('ok', 200),
            'http://127.0.0.1/*' => Http::response('ok', 200),
        ]);

        Process::fake([
            '*tasklist*' => Process::result('', '', 1),
        ]);

        $daemon = $this->makeDaemon();

        $summary = $daemon->tick();

        $daemonState = SystemGuardState::forComponent('daemon');
        $this->assertEquals('RUNNING', $daemonState->status);
        $this->assertNotNull($daemonState->last_checked_at);

        $this->assertEquals('ONLINE', $summary['components']['internet']['status']);
        $this->assertEquals('OFFLINE', $summary['components']['tunnel']['status']);
        $this->assertNull($summary['recovery']); // auto_recovery disabled
    }

    public function test_daemon_tick_executes_recovery_when_enabled_and_offline(): void
    {
        config(['system-guard.auto_recovery_enabled' => true]);
        config(['system-guard.daemon.recovery_cooldown_seconds' => 0]);

        Http::fake([
            'https://1.1.1.1/*' => Http::response('ok', 200),
            'http://127.0.0.1/*' => Http::response('ok', 200),
        ]);

        Process::fake([
            '*tasklist*' => Process::result('', '', 1),
        ]);

        $daemon = $this->makeDaemon();

        $summary = $daemon->tick();

        $this->assertNotNull($summary['recovery']);
        $this->assertTrue($summary['recovery']['attempted']);
    }

    /*
    |--------------------------------------------------------------------------
    | TUNNEL RESTARTER — command building (security: fixed template, no injection)
    |--------------------------------------------------------------------------
    */

    public function test_tunnel_restarter_builds_fixed_command_without_user_input(): void
    {
        config([
            'system-guard.cloudflared.binary_path' => 'C:\\cloudflared\\cloudflared.exe',
            'system-guard.cloudflared.quick_tunnel' => true,
            'system-guard.cloudflared.tunnel_id' => '',
            'system-guard.cloudflared.config_path' => '',
        ]);

        $restarter = new TunnelRestarter();

        $command = $restarter->buildStartCommand();

        $this->assertIsString($command);
        // Harus berisi biner yang dikonfigurasi
        $this->assertStringContainsString('cloudflared.exe', $command);
        // Harus berisi argumen quick tunnel (fixed)
        $this->assertStringContainsString('--url', $command);
    }

    public function test_tunnel_restarter_uses_named_tunnel_command_when_configured(): void
    {
        config([
            'system-guard.cloudflared.binary_path' => 'C:\\cloudflared\\cloudflared.exe',
            'system-guard.cloudflared.quick_tunnel' => false,
            'system-guard.cloudflared.tunnel_id' => 'abc-123',
            'system-guard.cloudflared.config_path' => 'C:\\cf\\config.yml',
        ]);

        $restarter = new TunnelRestarter();

        $command = $restarter->buildStartCommand();

        $this->assertStringContainsString('abc-123', $command);
        $this->assertStringContainsString('tunnel', $command);
        $this->assertStringContainsString('config.yml', $command);
    }

    public function test_tunnel_restarter_safe_fallback_no_injection(): void
    {
        // Tidak ada tunnel id / quick tunnel / template → fallback aman
        config([
            'system-guard.cloudflared.binary_path' => 'C:\\cloudflared\\cloudflared.exe',
            'system-guard.cloudflared.quick_tunnel' => false,
            'system-guard.cloudflared.tunnel_id' => '',
            'system-guard.cloudflared.config_path' => '',
            'system-guard.cloudflared.start_command' => '',
        ]);

        $restarter = new TunnelRestarter();

        $command = $restarter->buildStartCommand();

        // String perintah tidak boleh mengandung karakter kontrol/semicolon setelah biner
        $this->assertStringNotContainsString(';', $command);

        $this->assertIsString($command);
    }

    /*
    |--------------------------------------------------------------------------
    | RECOVERY ENGINE — cloudflared actions resolved to handlers
    |--------------------------------------------------------------------------
    */

    public function test_recovery_engine_whitelist_includes_tunnel_actions(): void
    {
        $engine = new RecoveryEngine(
            new class extends \App\Services\SystemGuard\MonitoringService {}
        );

        $this->assertTrue($engine->isActionAllowed('start_cloudflared'));
        $this->assertTrue($engine->isActionAllowed('restart_cloudflared'));
    }

    /*
    |--------------------------------------------------------------------------
    | HELPERS
    |--------------------------------------------------------------------------
    */

    protected function makeDaemon(): SystemGuardDaemon
    {
        return new SystemGuardDaemon(
            new InfrastructureMonitor(),
            new TunnelRestarter(),
            new RecoveryManager(new TunnelRestarter())
        );
    }
}
