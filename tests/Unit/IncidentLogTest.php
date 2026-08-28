<?php

namespace Tests\Unit;

use App\Models\SystemGuard\IncidentLog;
use App\Models\SystemGuard\MonitorConfig;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IncidentLogTest extends TestCase
{
    use RefreshDatabase;

    private function createConfig(): MonitorConfig
    {
        return MonitorConfig::create([
            'name'       => 'Test Target',
            'target_url' => 'https://example.com',
            'type'       => 'http',
            'is_active'  => true,
        ]);
    }

    public function test_generate_incident_id(): void
    {
        $id = IncidentLog::generateIncidentId();

        $this->assertMatchesRegularExpression('/^INC-\d{4}-\d{4}$/', $id);
        $this->assertStringStartsWith('INC-' . date('Y'), $id);
    }

    public function test_generate_incident_id_increments(): void
    {
        $id1 = IncidentLog::generateIncidentId();

        IncidentLog::create([
            'incident_id'       => $id1,
            'monitor_config_id' => $this->createConfig()->id,
            'target'            => 'https://example.com',
            'error_category'    => 'DNS',
            'error_type'        => 'DNS_NXDOMAIN',
            'error_message'     => 'Domain not found',
            'severity'          => 'HIGH',
            'status'            => 'RESOLVED',
            'detected_at'       => now(),
            'retry_count'       => 0,
        ]);

        $id2 = IncidentLog::generateIncidentId();

        $this->assertNotEquals($id1, $id2);
    }

    public function test_mark_recovering(): void
    {
        $config = $this->createConfig();

        $incident = IncidentLog::create([
            'incident_id'       => 'INC-2026-0010',
            'monitor_config_id' => $config->id,
            'target'            => 'https://example.com',
            'error_category'    => 'DNS',
            'error_type'        => 'DNS_NXDOMAIN',
            'error_message'     => 'Domain not found',
            'severity'          => 'HIGH',
            'status'            => 'OPEN',
            'detected_at'       => now(),
            'retry_count'       => 0,
        ]);

        $incident->markRecovering();

        $this->assertEquals('RECOVERING', $incident->fresh()->status);
    }

    public function test_mark_resolved(): void
    {
        $config = $this->createConfig();

        $incident = IncidentLog::create([
            'incident_id'       => 'INC-2026-0011',
            'monitor_config_id' => $config->id,
            'target'            => 'https://example.com',
            'error_category'    => 'HTTP',
            'error_type'        => 'HTTP_5XX',
            'error_message'     => 'Server Error',
            'severity'          => 'MEDIUM',
            'status'            => 'RECOVERING',
            'detected_at'       => now()->subMinutes(5),
            'retry_count'       => 1,
        ]);

        $incident->markResolved(['action' => 'retry_connection']);

        $fresh = $incident->fresh();

        $this->assertEquals('RESOLVED', $fresh->status);
        $this->assertNotNull($fresh->recovered_at);
        $this->assertNotNull($fresh->duration_seconds);
        $this->assertEquals('retry_connection', $fresh->recovery_summary['action']);
    }

    public function test_mark_failed(): void
    {
        $config = $this->createConfig();

        $incident = IncidentLog::create([
            'incident_id'       => 'INC-2026-0012',
            'monitor_config_id' => $config->id,
            'target'            => 'https://example.com',
            'error_category'    => 'CONNECTION',
            'error_type'        => 'CONNECTION_REFUSED',
            'error_message'     => 'Connection refused',
            'severity'          => 'HIGH',
            'status'            => 'RECOVERING',
            'detected_at'       => now(),
            'retry_count'       => 3,
        ]);

        $incident->markFailed();

        $this->assertEquals('FAILED', $incident->fresh()->status);
    }

    public function test_mark_escalated(): void
    {
        $config = $this->createConfig();

        $incident = IncidentLog::create([
            'incident_id'       => 'INC-2026-0013',
            'monitor_config_id' => $config->id,
            'target'            => 'https://example.com',
            'error_category'    => 'TIMEOUT',
            'error_type'        => 'TIMEOUT',
            'error_message'     => 'Request timed out',
            'severity'          => 'CRITICAL',
            'status'            => 'RECOVERING',
            'detected_at'       => now(),
            'retry_count'       => 3,
        ]);

        $incident->markEscalated();

        $this->assertEquals('ESCALATED', $incident->fresh()->status);
    }

    public function test_is_open_for_various_statuses(): void
    {
        $config = $this->createConfig();

        foreach (['OPEN', 'RECOVERING', 'ESCALATED'] as $status) {
            $incident = IncidentLog::create([
                'incident_id'       => "INC-2026-{$status}",
                'monitor_config_id' => $config->id,
                'target'            => 'https://example.com',
                'error_category'    => 'DNS',
                'error_type'        => 'DNS_NXDOMAIN',
                'error_message'     => 'Domain not found',
                'severity'          => 'HIGH',
                'status'            => $status,
                'detected_at'       => now(),
                'retry_count'       => 0,
            ]);

            $this->assertTrue($incident->isOpen(), "Status {$status} should be open");
        }
    }

    public function test_is_open_returns_false_for_resolved(): void
    {
        $config = $this->createConfig();

        $incident = IncidentLog::create([
            'incident_id'       => 'INC-2026-RESOLVED',
            'monitor_config_id' => $config->id,
            'target'            => 'https://example.com',
            'error_category'    => 'DNS',
            'error_type'        => 'DNS_NXDOMAIN',
            'error_message'     => 'Domain not found',
            'severity'          => 'HIGH',
            'status'            => 'RESOLVED',
            'detected_at'       => now(),
            'retry_count'       => 0,
        ]);

        $this->assertFalse($incident->isOpen());
    }

    public function test_increment_retry(): void
    {
        $config = $this->createConfig();

        $incident = IncidentLog::create([
            'incident_id'       => 'INC-2026-RETRY',
            'monitor_config_id' => $config->id,
            'target'            => 'https://example.com',
            'error_category'    => 'DNS',
            'error_type'        => 'DNS_NXDOMAIN',
            'error_message'     => 'Domain not found',
            'severity'          => 'HIGH',
            'status'            => 'OPEN',
            'detected_at'       => now(),
            'retry_count'       => 0,
        ]);

        $incident->incrementRetry();

        $this->assertEquals(1, $incident->fresh()->retry_count);

        $incident->fresh()->incrementRetry();

        $this->assertEquals(2, $incident->fresh()->retry_count);
    }

    public function test_scope_open(): void
    {
        $config = $this->createConfig();

        IncidentLog::create([
            'incident_id'       => 'INC-2026-OPEN1',
            'monitor_config_id' => $config->id,
            'target'            => 'https://example.com',
            'error_category'    => 'DNS',
            'error_type'        => 'DNS_NXDOMAIN',
            'error_message'     => 'Domain not found',
            'severity'          => 'HIGH',
            'status'            => 'OPEN',
            'detected_at'       => now(),
            'retry_count'       => 0,
        ]);

        IncidentLog::create([
            'incident_id'       => 'INC-2026-RES1',
            'monitor_config_id' => $config->id,
            'target'            => 'https://example.com',
            'error_category'    => 'HTTP',
            'error_type'        => 'HTTP_5XX',
            'error_message'     => 'Server Error',
            'severity'          => 'MEDIUM',
            'status'            => 'RESOLVED',
            'detected_at'       => now(),
            'retry_count'       => 0,
        ]);

        $this->assertCount(1, IncidentLog::open()->get());
        $this->assertCount(1, IncidentLog::resolved()->get());
    }
}
