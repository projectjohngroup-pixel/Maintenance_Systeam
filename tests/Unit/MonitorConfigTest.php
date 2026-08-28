<?php

namespace Tests\Unit;

use App\Models\SystemGuard\IncidentLog;
use App\Models\SystemGuard\MonitorConfig;
use App\Models\SystemGuard\MonitorResult;
use App\Models\SystemGuard\RecoveryLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MonitorConfigTest extends TestCase
{
    use RefreshDatabase;

    private function createConfig(array $overrides = []): MonitorConfig
    {
        return MonitorConfig::create(array_merge([
            'name'     => 'Test Target',
            'target_url' => 'https://example.com',
            'type'     => 'http',
            'is_active' => true,
            'check_interval_seconds' => 900,
            'timeout_seconds' => 10,
            'expected_status_code' => 200,
            'response_time_threshold_ms' => 5000,
            'max_retries' => 3,
            'retry_delay_seconds' => 30,
            'recovery_actions' => ['retry_connection'],
            'auto_recovery_enabled' => false,
        ], $overrides));
    }

    public function test_create_monitor_config(): void
    {
        $config = $this->createConfig();

        $this->assertDatabaseHas('monitor_configs', [
            'name'       => 'Test Target',
            'target_url' => 'https://example.com',
            'is_active'  => true,
        ]);
    }

    public function test_domain_accessor_extracts_domain_from_url(): void
    {
        $config = $this->createConfig([
            'target_url' => 'https://example.com/path?q=1',
        ]);

        $this->assertEquals('example.com', $config->domain);
    }

    public function test_domain_accessor_returns_target_domain_if_set(): void
    {
        $config = $this->createConfig([
            'target_url'    => 'https://example.com/path',
            'target_domain' => 'custom.domain.com',
        ]);

        $this->assertEquals('custom.domain.com', $config->domain);
    }

    public function test_scope_active_filters_inactive(): void
    {
        $this->createConfig(['is_active' => true]);
        $this->createConfig(['is_active' => false, 'name' => 'Inactive']);

        $active = MonitorConfig::active()->get();

        $this->assertCount(1, $active);
        $this->assertEquals('Test Target', $active->first()->name);
    }

    public function test_has_open_incident_when_no_incidents(): void
    {
        $config = $this->createConfig();

        $this->assertFalse($config->hasOpenIncident());
    }

    public function test_has_open_incident_when_open_exists(): void
    {
        $config = $this->createConfig();

        IncidentLog::create([
            'incident_id'       => 'INC-2026-0001',
            'monitor_config_id' => $config->id,
            'target'            => $config->target_url,
            'error_category'    => 'HTTP',
            'error_type'        => 'HTTP_5XX',
            'error_message'     => 'Server Error',
            'severity'          => 'HIGH',
            'status'            => 'OPEN',
            'detected_at'       => now(),
            'retry_count'       => 0,
        ]);

        $this->assertTrue($config->hasOpenIncident());
    }

    public function test_allowed_recovery_actions_filters_invalid(): void
    {
        $config = $this->createConfig([
            'recovery_actions' => ['retry_connection', 'invalid_action', 'dns_cache_flush'],
        ]);

        $allowed = $config->allowedRecoveryActions();

        $this->assertContains('retry_connection', $allowed);
        $this->assertContains('dns_cache_flush', $allowed);
        $this->assertNotContains('invalid_action', $allowed);
    }

    public function test_allowed_recovery_actions_empty_when_no_actions(): void
    {
        $config = $this->createConfig([
            'recovery_actions' => [],
        ]);

        $this->assertEmpty($config->allowedRecoveryActions());
    }

    public function test_relationships(): void
    {
        $config = $this->createConfig();

        MonitorResult::create([
            'monitor_config_id' => $config->id,
            'status'            => 'ONLINE',
            'category'          => 'normal',
            'dns_resolved'      => true,
            'connection_successful' => true,
            'http_successful'   => true,
        ]);

        IncidentLog::create([
            'incident_id'       => 'INC-2026-0002',
            'monitor_config_id' => $config->id,
            'target'            => $config->target_url,
            'error_category'    => 'DNS',
            'error_type'        => 'DNS_NXDOMAIN',
            'error_message'     => 'Domain not found',
            'severity'          => 'HIGH',
            'status'            => 'RESOLVED',
            'detected_at'       => now(),
            'retry_count'       => 0,
        ]);

        $this->assertCount(1, $config->results);
        $this->assertCount(1, $config->incidents);
    }
}
