<?php

namespace Tests\Unit;

use App\Models\SystemGuard\IncidentLog;
use App\Models\SystemGuard\MonitorConfig;
use App\Models\SystemGuard\MonitorResult;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SystemGuardCommandTest extends TestCase
{
    use RefreshDatabase;

    private function createConfig(array $overrides = []): MonitorConfig
    {
        return MonitorConfig::create(array_merge([
            'name'       => 'Test Target',
            'target_url' => 'https://example.com',
            'type'       => 'http',
            'is_active'  => true,
            'recovery_actions' => ['retry_connection'],
            'auto_recovery_enabled' => false,
        ], $overrides));
    }

    public function test_command_status_when_no_configs(): void
    {
        $this->artisan('system:guard', ['--status' => true])
            ->expectsOutput('System Guard — Status Summary')
            ->assertExitCode(0);
    }

    public function test_command_report_when_no_incidents(): void
    {
        $this->artisan('system:guard', ['--report' => true])
            ->expectsOutputToContain('Incident Report')
            ->assertExitCode(0);
    }

    public function test_command_recover_when_no_open_incidents(): void
    {
        $this->artisan('system:guard', ['--recover' => true])
            ->expectsOutput('No open incidents to recover.')
            ->assertExitCode(0);
    }

    public function test_command_full_cycle_with_no_active_targets(): void
    {
        $this->artisan('system:guard', ['--full' => true])
            ->expectsOutput('System Guard — Full Monitoring Cycle')
            ->assertExitCode(0);
    }

    public function test_command_disabled_guard(): void
    {
        config(['system-guard.enabled' => false]);

        $this->artisan('system:guard')
            ->expectsOutput('System Guard is disabled.')
            ->assertExitCode(0);
    }

    public function test_command_check_target_not_found(): void
    {
        $this->artisan('system:guard', ['--target' => 'https://nonexistent.example.com'])
            ->expectsOutput('No monitor config found for: https://nonexistent.example.com')
            ->assertExitCode(1);
    }
}
