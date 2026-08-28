<?php

namespace Tests\Unit;

use App\Models\SystemGuard\IncidentLog;
use App\Models\SystemGuard\MonitorConfig;
use App\Models\SystemGuard\MonitorResult;
use App\Services\SystemGuard\MonitoringService;
use App\Services\SystemGuard\VerificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VerificationServiceTest extends TestCase
{
    use RefreshDatabase;

    private function createConfig(array $overrides = []): MonitorConfig
    {
        return MonitorConfig::create(array_merge([
            'name'       => 'Test Target',
            'target_url' => 'https://example.com',
            'type'       => 'http',
            'is_active'  => true,
            'timeout_seconds' => 5,
            'response_time_threshold_ms' => 5000,
            'max_retries' => 3,
            'retry_delay_seconds' => 1,
            'recovery_actions' => ['retry_connection'],
            'auto_recovery_enabled' => false,
        ], $overrides));
    }

    private function createIncident(MonitorConfig $config): IncidentLog
    {
        return IncidentLog::create([
            'incident_id'       => 'INC-2026-0001',
            'monitor_config_id' => $config->id,
            'target'            => $config->target_url,
            'error_category'    => 'DNS',
            'error_type'        => 'DNS_NXDOMAIN',
            'error_message'     => 'Domain not found',
            'severity'          => 'HIGH',
            'status'            => 'RECOVERING',
            'detected_at'       => now()->subMinutes(5),
            'retry_count'       => 1,
        ]);
    }

    // ==========================================================
    // VERIFICATION PASSED
    // ==========================================================

    public function test_verify_passed_when_all_checks_ok(): void
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
                    'response_time_ms'  => 820,
                    'severity'          => 'normal',
                ]);
            }
        };

        $service = new VerificationService($monitoringService);

        $verification = $service->verify($config);

        $this->assertTrue($verification['passed']);
        $this->assertTrue($verification['dns_ok']);
        $this->assertTrue($verification['connection_ok']);
        $this->assertTrue($verification['http_ok']);
        $this->assertTrue($verification['response_time_ok']);
        $this->assertEquals(200, $verification['http_status_code']);
        $this->assertEquals(820, $verification['response_time_ms']);
    }

    // ==========================================================
    // VERIFICATION FAILED - DNS
    // ==========================================================

    public function test_verify_failed_when_dns_not_resolved(): void
    {
        $config = $this->createConfig();

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

        $service = new VerificationService($monitoringService);

        $verification = $service->verify($config);

        $this->assertFalse($verification['passed']);
        $this->assertFalse($verification['dns_ok']);
    }

    // ==========================================================
    // VERIFICATION FAILED - HTTP
    // ==========================================================

    public function test_verify_failed_when_http_error(): void
    {
        $config = $this->createConfig();

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
                    'http_status_code'  => 500,
                    'error_type'        => 'HTTP_5XX',
                    'error_message'     => 'Internal Server Error',
                    'severity'          => 'HIGH',
                ]);
            }
        };

        $service = new VerificationService($monitoringService);

        $verification = $service->verify($config);

        $this->assertFalse($verification['passed']);
        $this->assertTrue($verification['dns_ok']);
        $this->assertTrue($verification['connection_ok']);
        $this->assertFalse($verification['http_ok']);
    }

    // ==========================================================
    // VERIFICATION FAILED - RESPONSE TIME
    // ==========================================================

    public function test_verify_failed_when_response_time_exceeds_threshold(): void
    {
        $config = $this->createConfig([
            'response_time_threshold_ms' => 2000,
        ]);

        $monitoringService = new class extends MonitoringService {
            public function check(MonitorConfig $config): MonitorResult
            {
                return MonitorResult::create([
                    'monitor_config_id' => $config->id,
                    'status'            => 'WASPADA',
                    'category'          => 'PERFORMANCE',
                    'dns_resolved'      => true,
                    'connection_successful' => true,
                    'http_successful'   => true,
                    'http_status_code'  => 200,
                    'response_time_ms'  => 6000,
                    'severity'          => 'MEDIUM',
                ]);
            }
        };

        $service = new VerificationService($monitoringService);

        $verification = $service->verify($config);

        $this->assertFalse($verification['passed']);
        $this->assertFalse($verification['response_time_ok']);
    }

    // ==========================================================
    // DETERMINE FINAL STATUS
    // ==========================================================

    public function test_determine_final_status_online_when_all_passed(): void
    {
        $monitoringService = new class extends MonitoringService {};
        $service = new VerificationService($monitoringService);

        $status = $service->determineFinalStatus([
            'passed'           => true,
            'dns_ok'           => true,
            'connection_ok'    => true,
            'http_ok'          => true,
            'response_time_ok' => true,
        ]);

        $this->assertEquals('ONLINE', $status);
    }

    public function test_determine_final_status_gangguan_when_dns_failed(): void
    {
        $monitoringService = new class extends MonitoringService {};
        $service = new VerificationService($monitoringService);

        $status = $service->determineFinalStatus([
            'passed'           => false,
            'dns_ok'           => false,
            'connection_ok'    => false,
            'http_ok'          => false,
            'response_time_ok' => false,
        ]);

        $this->assertEquals('GANGGUAN', $status);
    }

    public function test_determine_final_status_perhatian_when_http_failed(): void
    {
        $monitoringService = new class extends MonitoringService {};
        $service = new VerificationService($monitoringService);

        $status = $service->determineFinalStatus([
            'passed'           => false,
            'dns_ok'           => true,
            'connection_ok'    => true,
            'http_ok'          => false,
            'response_time_ok' => false,
        ]);

        $this->assertEquals('PERHATIAN', $status);
    }

    public function test_determine_final_status_waspada_when_slow(): void
    {
        $monitoringService = new class extends MonitoringService {};
        $service = new VerificationService($monitoringService);

        $status = $service->determineFinalStatus([
            'passed'           => false,
            'dns_ok'           => true,
            'connection_ok'    => true,
            'http_ok'          => true,
            'response_time_ok' => false,
        ]);

        $this->assertEquals('WASPADA', $status);
    }

    // ==========================================================
    // FORMAT VERIFICATION RESULT
    // ==========================================================

    public function test_format_verification_result(): void
    {
        $monitoringService = new class extends MonitoringService {};
        $service = new VerificationService($monitoringService);

        $formatted = $service->formatVerificationResult([
            'dns_ok'           => true,
            'connection_ok'    => true,
            'http_ok'          => true,
            'http_status_code' => 200,
            'response_time_ms' => 820,
            'passed'           => true,
        ]);

        $this->assertStringContainsString('DNS: OK', $formatted);
        $this->assertStringContainsString('Connection: OK', $formatted);
        $this->assertStringContainsString('HTTP: 200', $formatted);
        $this->assertStringContainsString('820 ms', $formatted);
        $this->assertStringContainsString('Result: ONLINE', $formatted);
    }
}
