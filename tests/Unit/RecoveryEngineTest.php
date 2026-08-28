<?php

namespace Tests\Unit;

use App\Models\SystemGuard\IncidentLog;
use App\Models\SystemGuard\MonitorConfig;
use App\Models\SystemGuard\RecoveryLog;
use App\Services\SystemGuard\MonitoringService;
use App\Services\SystemGuard\RecoveryEngine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RecoveryEngineTest extends TestCase
{
    use RefreshDatabase;

    private RecoveryEngine $engine;

    protected function setUp(): void
    {
        parent::setUp();

        $monitoringService = new class extends MonitoringService {
            public bool $healthCheckResult = true;

            public function check(MonitorConfig $config): \App\Models\SystemGuard\MonitorResult
            {
                $status = $this->healthCheckResult ? 'ONLINE' : 'GANGGUAN';
                $dnsResolved = $this->healthCheckResult;
                $connectionOk = $this->healthCheckResult;
                $httpOk = $this->healthCheckResult;

                return \App\Models\SystemGuard\MonitorResult::create([
                    'monitor_config_id' => $config->id,
                    'status'            => $status,
                    'category'          => $this->healthCheckResult ? 'normal' : 'UNKNOWN',
                    'dns_resolved'      => $dnsResolved,
                    'connection_successful' => $connectionOk,
                    'http_successful'   => $httpOk,
                    'http_status_code'  => $this->healthCheckResult ? 200 : null,
                    'response_time_ms'  => $this->healthCheckResult ? 100 : null,
                    'error_type'        => $this->healthCheckResult ? null : 'UNKNOWN',
                    'severity'          => $this->healthCheckResult ? 'normal' : 'HIGH',
                ]);
            }
        };

        $this->engine = new RecoveryEngine($monitoringService);
    }

    private function createConfig(array $overrides = []): MonitorConfig
    {
        return MonitorConfig::create(array_merge([
            'name'       => 'Test Target',
            'target_url' => 'https://example.com',
            'type'       => 'http',
            'is_active'  => true,
            'max_retries' => 3,
            'retry_delay_seconds' => 1,
            'recovery_actions' => ['retry_connection', 'dns_cache_flush', 'wait_and_retry'],
            'auto_recovery_enabled' => true,
        ], $overrides));
    }

    private function createIncident(MonitorConfig $config, array $overrides = []): IncidentLog
    {
        return IncidentLog::create(array_merge([
            'incident_id'       => 'INC-2026-0001',
            'monitor_config_id' => $config->id,
            'target'            => $config->target_url,
            'error_category'    => 'DNS',
            'error_type'        => 'DNS_NXDOMAIN',
            'error_message'     => 'Domain not found',
            'severity'          => 'HIGH',
            'status'            => 'OPEN',
            'detected_at'       => now(),
            'retry_count'       => 0,
        ], $overrides));
    }

    // ==========================================================
    // WHITELIST ENFORCEMENT
    // ==========================================================

    public function test_is_action_allowed_for_whitelisted_action(): void
    {
        $this->assertTrue($this->engine->isActionAllowed('retry_connection'));
        $this->assertTrue($this->engine->isActionAllowed('dns_cache_flush'));
        $this->assertTrue($this->engine->isActionAllowed('wait_and_retry'));
        $this->assertTrue($this->engine->isActionAllowed('notify_admin'));
        $this->assertTrue($this->engine->isActionAllowed('escalate'));
    }

    public function test_is_action_allowed_blocks_unknown_action(): void
    {
        $this->assertFalse($this->engine->isActionAllowed('unknown_action'));
        $this->assertFalse($this->engine->isActionAllowed('random_recovery'));
    }

    public function test_is_action_allowed_blocks_shell_exec(): void
    {
        $this->assertFalse($this->engine->isActionAllowed('shell_exec'));
        $this->assertFalse($this->engine->isActionAllowed('exec'));
        $this->assertFalse($this->engine->isActionAllowed('system'));
        $this->assertFalse($this->engine->isActionAllowed('passthru'));
        $this->assertFalse($this->engine->isActionAllowed('popen'));
        $this->assertFalse($this->engine->isActionAllowed('proc_open'));
        $this->assertFalse($this->engine->isActionAllowed('eval'));
        $this->assertFalse($this->engine->isActionAllowed('sudo'));
        $this->assertFalse($this->engine->isActionAllowed('reboot'));
        $this->assertFalse($this->engine->isActionAllowed('kill'));
    }

    public function test_get_blocked_actions_returns_list(): void
    {
        $blocked = $this->engine->getBlockedActions();

        $this->assertContains('shell_exec', $blocked);
        $this->assertContains('exec', $blocked);
        $this->assertContains('system', $blocked);
        $this->assertContains('eval', $blocked);
        $this->assertContains('sudo', $blocked);
        $this->assertContains('kill', $blocked);
        $this->assertContains('reboot', $blocked);
    }

    public function test_get_allowed_actions_returns_whitelist(): void
    {
        $allowed = $this->engine->getAllowedActions();

        $this->assertArrayHasKey('retry_connection', $allowed);
        $this->assertArrayHasKey('dns_cache_flush', $allowed);
        $this->assertArrayHasKey('wait_and_retry', $allowed);
        $this->assertArrayHasKey('notify_admin', $allowed);
        $this->assertArrayHasKey('escalate', $allowed);
    }

    // ==========================================================
    // RECOVERY EXECUTION
    // ==========================================================

    public function test_recovery_with_no_actions_returns_failure(): void
    {
        $config = $this->createConfig([
            'recovery_actions' => [],
        ]);

        $incident = $this->createIncident($config);

        $result = $this->engine->recover($incident, $config);

        $this->assertFalse($result['success']);
        $this->assertEquals('No recovery actions configured', $result['message']);
    }

    public function test_recovery_retry_connection_success(): void
    {
        $monitoringService = new class extends MonitoringService {
            public function check(MonitorConfig $config): \App\Models\SystemGuard\MonitorResult
            {
                return \App\Models\SystemGuard\MonitorResult::create([
                    'monitor_config_id' => $config->id,
                    'status'            => 'ONLINE',
                    'category'          => 'normal',
                    'dns_resolved'      => true,
                    'connection_successful' => true,
                    'http_successful'   => true,
                    'http_status_code'  => 200,
                    'response_time_ms'  => 100,
                    'severity'          => 'normal',
                ]);
            }
        };

        $engine = new RecoveryEngine($monitoringService);

        $config = $this->createConfig([
            'recovery_actions' => ['retry_connection'],
        ]);

        $incident = $this->createIncident($config);

        $result = $engine->recover($incident, $config);

        $this->assertTrue($result['success']);
        $this->assertEquals('retry_connection', $result['action']);
        $this->assertEquals('RECOVERING', $incident->fresh()->status);
    }

    public function test_recovery_retry_connection_failure(): void
    {
        $monitoringService = new class extends MonitoringService {
            public function check(MonitorConfig $config): \App\Models\SystemGuard\MonitorResult
            {
                return \App\Models\SystemGuard\MonitorResult::create([
                    'monitor_config_id' => $config->id,
                    'status'            => 'GANGGUAN',
                    'category'          => 'CONNECTION',
                    'dns_resolved'      => true,
                    'connection_successful' => false,
                    'http_successful'   => false,
                    'error_type'        => 'CONNECTION_REFUSED',
                    'error_message'     => 'Connection refused',
                    'severity'          => 'HIGH',
                ]);
            }
        };

        $engine = new RecoveryEngine($monitoringService);

        $config = $this->createConfig([
            'recovery_actions' => ['retry_connection'],
        ]);

        $incident = $this->createIncident($config);

        $result = $engine->recover($incident, $config);

        $this->assertTrue($result['executed']);
        $this->assertFalse($result['success']);
    }

    public function test_recovery_creates_recovery_log(): void
    {
        $config = $this->createConfig([
            'recovery_actions' => ['notify_admin'],
        ]);

        $incident = $this->createIncident($config);

        $result = $this->engine->recover($incident, $config);

        $this->assertTrue($result['executed']);

        $this->assertDatabaseHas('recovery_logs', [
            'incident_id'       => $incident->incident_id,
            'monitor_config_id' => $config->id,
            'action'            => 'notify_admin',
            'status'            => 'SUCCESS',
            'verification_passed' => true,
        ]);
    }

    public function test_recovery_escalate_marks_incident(): void
    {
        $config = $this->createConfig([
            'recovery_actions' => ['escalate'],
        ]);

        $incident = $this->createIncident($config);

        $result = $this->engine->recover($incident, $config);

        $this->assertTrue($result['executed']);
        $this->assertTrue($result['success']);
        $this->assertEquals('ESCALATED', $incident->fresh()->status);
    }

    // ==========================================================
    // RECOVERY WITH BLOCKED ACTIONS
    // ==========================================================

    public function test_recovery_skips_blocked_actions(): void
    {
        $config = $this->createConfig([
            'recovery_actions' => ['shell_exec', 'exec', 'notify_admin'],
        ]);

        $incident = $this->createIncident($config);

        $result = $this->engine->recover($incident, $config);

        $this->assertTrue($result['executed']);
        $this->assertEquals('notify_admin', $result['action']);

        $this->assertDatabaseMissing('recovery_logs', [
            'action' => 'shell_exec',
        ]);

        $this->assertDatabaseMissing('recovery_logs', [
            'action' => 'exec',
        ]);
    }

    // ==========================================================
    // RECOVERY ALL
    // ==========================================================

    public function test_recover_all_processes_open_incidents(): void
    {
        $config = $this->createConfig([
            'recovery_actions' => ['escalate'],
            'auto_recovery_enabled' => true,
        ]);

        $this->createIncident($config, [
            'incident_id' => 'INC-2026-REC1',
            'retry_count' => 0,
        ]);

        $this->createIncident($config, [
            'incident_id' => 'INC-2026-REC2',
            'status'      => 'RESOLVED',
            'retry_count' => 0,
        ]);

        $results = $this->engine->recoverAll();

        $this->assertCount(1, $results);
    }

    public function test_recover_all_skips_disabled_auto_recovery(): void
    {
        $config = $this->createConfig([
            'recovery_actions' => ['escalate'],
            'auto_recovery_enabled' => false,
        ]);

        $this->createIncident($config, [
            'incident_id' => 'INC-2026-SKIP',
            'retry_count' => 0,
        ]);

        $results = $this->engine->recoverAll();

        $this->assertCount(0, $results);
    }

    public function test_recover_all_escalates_when_max_retries_reached(): void
    {
        $config = $this->createConfig([
            'recovery_actions' => ['retry_connection'],
            'auto_recovery_enabled' => true,
            'max_retries' => 3,
        ]);

        $incident = $this->createIncident($config, [
            'incident_id' => 'INC-2026-MAX',
            'retry_count' => 3,
        ]);

        $results = $this->engine->recoverAll();

        $this->assertCount(1, $results);
        $this->assertEquals('ESCALATED', $incident->fresh()->status);
    }
}
