<?php

namespace Tests\Unit;

use App\Models\SystemGuard\IncidentLog;
use App\Models\SystemGuard\MonitorConfig;
use App\Models\SystemGuard\MonitorResult;
use App\Services\SystemGuard\MonitoringService;
use App\Services\SystemGuard\RecoveryEngine;
use App\Services\SystemGuard\SystemGuard;
use App\Services\SystemGuard\VerificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SystemGuardTest extends TestCase
{
    use RefreshDatabase;

    private function createConfig(array $overrides = []): MonitorConfig
    {
        return MonitorConfig::create(array_merge([
            'name'       => 'Test Target',
            'target_url' => 'https://guard.example.com',
            'type'       => 'http',
            'is_active'  => true,
            'timeout_seconds' => 5,
            'expected_status_code' => 200,
            'response_time_threshold_ms' => 5000,
            'max_retries' => 3,
            'retry_delay_seconds' => 1,
            'recovery_actions' => ['retry_connection', 'escalate'],
            'auto_recovery_enabled' => true,
        ], $overrides));
    }

    // ==========================================================
    // FULL CYCLE - ALL HEALTHY
    // ==========================================================

    public function test_full_cycle_all_healthy(): void
    {
        $config = $this->createConfig();

        $monitoringService = new class extends MonitoringService {
            public function check(MonitorConfig $config): MonitorResult
            {
                return MonitorResult::create([
                    'monitor_config_id' => $config->id,
                    'status'            => 'ONLINE',
                    'category'          => 'normal',
                    'dns_resolved'      => true,
                    'connection_successful' => true,
                    'http_successful'   => true,
                    'http_status_code'  => 200,
                    'response_time_ms'  => 500,
                    'severity'          => 'normal',
                ]);
            }

            public function checkAll(): array
            {
                return collect($this->checkAllConfigs())->map(
                    fn ($c) => ['config' => $c, 'result' => $this->check($c)]
                )->toArray();
            }

            protected function checkAllConfigs()
            {
                return MonitorConfig::active()->get()->all();
            }
        };

        $recoveryEngine = new RecoveryEngine($monitoringService);
        $verificationService = new VerificationService($monitoringService);
        $guard = new SystemGuard($monitoringService, $recoveryEngine, $verificationService);

        $results = $guard->runFullCycle();

        $this->assertEquals(1, $results['checked']);
        $this->assertEquals(1, $results['healthy']);
        $this->assertEquals(0, $results['unhealthy']);
        $this->assertEquals(0, $results['recovered']);
        $this->assertEmpty($results['incidents']);
    }

    // ==========================================================
    // FULL CYCLE - DNS NXDOMAIN
    // ==========================================================

    public function test_full_cycle_dns_nxdomain_creates_incident(): void
    {
        $config = $this->createConfig([
            'target_url' => 'https://oxygen-jeff-decrease-conclusion.trycloudflare.com',
            'auto_recovery_enabled' => false,
        ]);

        $monitoringService = new class extends MonitoringService {
            public function check(MonitorConfig $config): MonitorResult
            {
                return MonitorResult::create([
                    'monitor_config_id' => $config->id,
                    'status'            => 'GANGGUAN',
                    'category'          => 'DNS',
                    'dns_resolved'      => false,
                    'connection_successful' => false,
                    'http_successful'   => false,
                    'error_type'        => 'DNS_NXDOMAIN',
                    'error_message'     => 'DNS probe finished: NXDOMAIN for ' . $config->domain,
                    'severity'          => 'HIGH',
                ]);
            }

            public function checkAll(): array
            {
                return collect(MonitorConfig::active()->get()->all())->map(
                    fn ($c) => ['config' => $c, 'result' => $this->check($c)]
                )->toArray();
            }
        };

        $recoveryEngine = new RecoveryEngine($monitoringService);
        $verificationService = new VerificationService($monitoringService);
        $guard = new SystemGuard($monitoringService, $recoveryEngine, $verificationService);

        $results = $guard->runFullCycle();

        $this->assertEquals(1, $results['checked']);
        $this->assertEquals(0, $results['healthy']);
        $this->assertEquals(1, $results['unhealthy']);

        $this->assertDatabaseHas('incident_logs', [
            'monitor_config_id' => $config->id,
            'error_type'        => 'DNS_NXDOMAIN',
            'status'            => 'OPEN',
            'severity'          => 'HIGH',
        ]);

        $this->assertDatabaseHas('monitor_results', [
            'monitor_config_id' => $config->id,
            'status'            => 'GANGGUAN',
            'dns_resolved'      => false,
        ]);
    }

    // ==========================================================
    // FULL CYCLE - HTTP 5XX
    // ==========================================================

    public function test_full_cycle_http_5xx_creates_incident(): void
    {
        $config = $this->createConfig([
            'auto_recovery_enabled' => false,
        ]);

        $monitoringService = new class extends MonitoringService {
            public function check(MonitorConfig $config): MonitorResult
            {
                return MonitorResult::create([
                    'monitor_config_id' => $config->id,
                    'status'            => 'GANGGUAN',
                    'category'          => 'HTTP',
                    'dns_resolved'      => true,
                    'connection_successful' => true,
                    'http_successful'   => false,
                    'http_status_code'  => 503,
                    'error_type'        => 'HTTP_5XX',
                    'error_message'     => 'HTTP 503 Service Unavailable',
                    'severity'          => 'HIGH',
                ]);
            }

            public function checkAll(): array
            {
                return collect(MonitorConfig::active()->get()->all())->map(
                    fn ($c) => ['config' => $c, 'result' => $this->check($c)]
                )->toArray();
            }
        };

        $recoveryEngine = new RecoveryEngine($monitoringService);
        $verificationService = new VerificationService($monitoringService);
        $guard = new SystemGuard($monitoringService, $recoveryEngine, $verificationService);

        $results = $guard->runFullCycle();

        $this->assertEquals(1, $results['unhealthy']);

        $this->assertDatabaseHas('incident_logs', [
            'error_type' => 'HTTP_5XX',
            'severity'   => 'HIGH',
        ]);
    }

    // ==========================================================
    // RECOVERY → VERIFY → ONLINE
    // ==========================================================

    public function test_recovery_verify_online_full_flow(): void
    {
        $config = $this->createConfig([
            'auto_recovery_enabled' => true,
            'recovery_actions' => ['retry_connection'],
        ]);

        $checkCount = 0;

        $monitoringService = new class ($checkCount) extends MonitoringService {
            public int $checkCount;

            public function __construct(int &$checkCount)
            {
                $this->checkCount = &$checkCount;
            }

            public function check(MonitorConfig $config): MonitorResult
            {
                $this->checkCount++;

                $healthy = $this->checkCount >= 2;

                return MonitorResult::create([
                    'monitor_config_id' => $config->id,
                    'status'            => $healthy ? 'ONLINE' : 'GANGGUAN',
                    'category'          => $healthy ? 'normal' : 'DNS',
                    'dns_resolved'      => $healthy,
                    'connection_successful' => $healthy,
                    'http_successful'   => $healthy,
                    'http_status_code'  => $healthy ? 200 : null,
                    'response_time_ms'  => $healthy ? 500 : null,
                    'error_type'        => $healthy ? null : 'DNS_NXDOMAIN',
                    'error_message'     => $healthy ? null : 'DNS NXDOMAIN',
                    'severity'          => $healthy ? 'normal' : 'HIGH',
                ]);
            }

            public function checkAll(): array
            {
                return collect(MonitorConfig::active()->get()->all())->map(
                    fn ($c) => ['config' => $c, 'result' => $this->check($c)]
                )->toArray();
            }
        };

        $recoveryEngine = new RecoveryEngine($monitoringService);
        $verificationService = new VerificationService($monitoringService);
        $guard = new SystemGuard($monitoringService, $recoveryEngine, $verificationService);

        $results = $guard->runFullCycle();

        $this->assertEquals(1, $results['checked']);
        $this->assertEquals(1, $results['recovered']);

        $this->assertDatabaseHas('incident_logs', [
            'monitor_config_id' => $config->id,
            'status'            => 'RESOLVED',
        ]);

        $this->assertDatabaseHas('recovery_logs', [
            'monitor_config_id' => $config->id,
            'action'            => 'retry_connection',
            'status'            => 'SUCCESS',
        ]);
    }

    // ==========================================================
    // RECOVERY FAILS → ESCALATE
    // ==========================================================

    public function test_recovery_fails_escalates(): void
    {
        $config = $this->createConfig([
            'auto_recovery_enabled' => true,
            'recovery_actions' => ['retry_connection', 'escalate'],
            'max_retries' => 1,
        ]);

        $monitoringService = new class extends MonitoringService {
            public function check(MonitorConfig $config): MonitorResult
            {
                return MonitorResult::create([
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

            public function checkAll(): array
            {
                return collect(MonitorConfig::active()->get()->all())->map(
                    fn ($c) => ['config' => $c, 'result' => $this->check($c)]
                )->toArray();
            }
        };

        $recoveryEngine = new RecoveryEngine($monitoringService);
        $verificationService = new VerificationService($monitoringService);
        $guard = new SystemGuard($monitoringService, $recoveryEngine, $verificationService);

        $results = $guard->runFullCycle();

        $this->assertEquals(1, $results['failed']);

        $this->assertDatabaseHas('incident_logs', [
            'monitor_config_id' => $config->id,
            'status'            => 'ESCALATED',
        ]);
    }

    // ==========================================================
    // SINGLE CHECK
    // ==========================================================

    public function test_single_check_healthy(): void
    {
        $config = $this->createConfig();

        $monitoringService = new class extends MonitoringService {
            public function check(MonitorConfig $config): MonitorResult
            {
                return MonitorResult::create([
                    'monitor_config_id' => $config->id,
                    'status'            => 'ONLINE',
                    'category'          => 'normal',
                    'dns_resolved'      => true,
                    'connection_successful' => true,
                    'http_successful'   => true,
                    'http_status_code'  => 200,
                    'response_time_ms'  => 200,
                    'severity'          => 'normal',
                ]);
            }
        };

        $recoveryEngine = new RecoveryEngine($monitoringService);
        $verificationService = new VerificationService($monitoringService);
        $guard = new SystemGuard($monitoringService, $recoveryEngine, $verificationService);

        $result = $guard->runSingleCheck($config);

        $this->assertTrue($result['healthy']);
        $this->assertEquals('ONLINE', $result['status']);
        $this->assertNull($result['incident_id']);
        $this->assertFalse($result['recovered']);
    }

    public function test_single_check_unhealthy_creates_incident(): void
    {
        $config = $this->createConfig([
            'auto_recovery_enabled' => false,
        ]);

        $monitoringService = new class extends MonitoringService {
            public function check(MonitorConfig $config): MonitorResult
            {
                return MonitorResult::create([
                    'monitor_config_id' => $config->id,
                    'status'            => 'GANGGUAN',
                    'category'          => 'DNS',
                    'dns_resolved'      => false,
                    'connection_successful' => false,
                    'http_successful'   => false,
                    'error_type'        => 'DNS_NXDOMAIN',
                    'error_message'     => 'DNS NXDOMAIN',
                    'severity'          => 'HIGH',
                ]);
            }
        };

        $recoveryEngine = new RecoveryEngine($monitoringService);
        $verificationService = new VerificationService($monitoringService);
        $guard = new SystemGuard($monitoringService, $recoveryEngine, $verificationService);

        $result = $guard->runSingleCheck($config);

        $this->assertFalse($result['healthy']);
        $this->assertEquals('GANGGUAN', $result['status']);
        $this->assertNotNull($result['incident_id']);
    }

    // ==========================================================
    // STATUS SUMMARY
    // ==========================================================

    public function test_status_summary(): void
    {
        $config1 = $this->createConfig(['name' => 'Service A', 'target_url' => 'https://a.example.com']);
        $config2 = $this->createConfig(['name' => 'Service B', 'target_url' => 'https://b.example.com']);

        MonitorResult::create([
            'monitor_config_id' => $config1->id,
            'status'            => 'ONLINE',
            'category'          => 'normal',
            'dns_resolved'      => true,
            'connection_successful' => true,
            'http_successful'   => true,
            'severity'          => 'normal',
        ]);

        MonitorResult::create([
            'monitor_config_id' => $config2->id,
            'status'            => 'GANGGUAN',
            'category'          => 'DNS',
            'dns_resolved'      => false,
            'connection_successful' => false,
            'http_successful'   => false,
            'severity'          => 'HIGH',
        ]);

        $monitoringService = new class extends MonitoringService {};
        $recoveryEngine = new RecoveryEngine($monitoringService);
        $verificationService = new VerificationService($monitoringService);
        $guard = new SystemGuard($monitoringService, $recoveryEngine, $verificationService);

        $summary = $guard->getStatusSummary();

        $this->assertEquals(2, $summary['total']);
        $this->assertEquals(1, $summary['online']);
        $this->assertEquals(1, $summary['gangguan']);
        $this->assertCount(2, $summary['targets']);
    }

    // ==========================================================
    // INCIDENT REPORT
    // ==========================================================

    public function test_incident_report(): void
    {
        $config = $this->createConfig();

        IncidentLog::create([
            'incident_id'       => 'INC-2026-0001',
            'monitor_config_id' => $config->id,
            'target'            => $config->target_url,
            'error_category'    => 'DNS',
            'error_type'        => 'DNS_NXDOMAIN',
            'error_message'     => 'DNS NXDOMAIN',
            'severity'          => 'HIGH',
            'status'            => 'RESOLVED',
            'detected_at'       => now()->subHours(1),
            'recovered_at'      => now()->subMinutes(50),
            'duration_seconds'  => 600,
            'retry_count'       => 1,
        ]);

        IncidentLog::create([
            'incident_id'       => 'INC-2026-0002',
            'monitor_config_id' => $config->id,
            'target'            => $config->target_url,
            'error_category'    => 'HTTP',
            'error_type'        => 'HTTP_5XX',
            'error_message'     => 'Server Error',
            'severity'          => 'MEDIUM',
            'status'            => 'OPEN',
            'detected_at'       => now()->subMinutes(30),
            'retry_count'       => 0,
        ]);

        $monitoringService = new class extends MonitoringService {};
        $recoveryEngine = new RecoveryEngine($monitoringService);
        $verificationService = new VerificationService($monitoringService);
        $guard = new SystemGuard($monitoringService, $recoveryEngine, $verificationService);

        $report = $guard->getIncidentReport(24);

        $this->assertEquals(2, $report['total_incidents']);
        $this->assertEquals(1, $report['resolved']);
        $this->assertEquals(1, $report['open']);
        $this->assertEquals(50.0, $report['recovery_success_rate']);
        $this->assertEquals(600.0, $report['avg_recovery_time']);
        $this->assertCount(2, $report['incidents']);
    }

    // ==========================================================
    // AUTO-RESOLVE ON NEXT HEALTHY CHECK
    // ==========================================================

    public function test_open_incident_auto_resolves_when_target_recovers(): void
    {
        $config = $this->createConfig([
            'auto_recovery_enabled' => false,
        ]);

        $existingIncident = IncidentLog::create([
            'incident_id'       => 'INC-2026-AUTO',
            'monitor_config_id' => $config->id,
            'target'            => $config->target_url,
            'error_category'    => 'DNS',
            'error_type'        => 'DNS_NXDOMAIN',
            'error_message'     => 'DNS NXDOMAIN',
            'severity'          => 'HIGH',
            'status'            => 'OPEN',
            'detected_at'       => now()->subHours(1),
            'retry_count'       => 0,
        ]);

        $monitoringService = new class extends MonitoringService {
            public function check(MonitorConfig $config): MonitorResult
            {
                return MonitorResult::create([
                    'monitor_config_id' => $config->id,
                    'status'            => 'ONLINE',
                    'category'          => 'normal',
                    'dns_resolved'      => true,
                    'connection_successful' => true,
                    'http_successful'   => true,
                    'http_status_code'  => 200,
                    'response_time_ms'  => 300,
                    'severity'          => 'normal',
                ]);
            }

            public function checkAll(): array
            {
                return collect(MonitorConfig::active()->get()->all())->map(
                    fn ($c) => ['config' => $c, 'result' => $this->check($c)]
                )->toArray();
            }
        };

        $recoveryEngine = new RecoveryEngine($monitoringService);
        $verificationService = new VerificationService($monitoringService);
        $guard = new SystemGuard($monitoringService, $recoveryEngine, $verificationService);

        $results = $guard->runFullCycle();

        $this->assertEquals(1, $results['healthy']);
        $this->assertEquals('RESOLVED', $existingIncident->fresh()->status);
    }
}
