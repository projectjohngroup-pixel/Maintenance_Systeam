<?php

namespace App\Services\SystemGuard;

use App\Models\SystemGuard\IncidentLog;
use App\Models\SystemGuard\MonitorConfig;
use App\Models\SystemGuard\RecoveryLog;
use Illuminate\Support\Facades\Log;
use App\Services\SystemGuard\TunnelRestarter;

class RecoveryEngine
{
    /*
    |--------------------------------------------------------------------------
    | RECOVERY ENGINE
    |--------------------------------------------------------------------------
    |
    | Mengjalankan recovery action yang sudah di-whitelist.
    | Setiap action harus:
    | 1. Ada di whitelist (config system-guard.recovery_whitelist)
    | 2. Tidak ada di blocked_actions
    | 3. Aman dan tervalidasi
    |
    | PRINSIP: MINIMAL CHANGE — MAXIMUM SAFETY
    |
    */

    protected MonitoringService $monitoringService;
    protected ?TunnelRestarter $tunnelRestarter;

    public function __construct(
        MonitoringService $monitoringService,
        ?TunnelRestarter $tunnelRestarter = null
    ) {
        $this->monitoringService = $monitoringService;
        $this->tunnelRestarter = $tunnelRestarter ?? new TunnelRestarter();
    }

    /**
     * Jalankan recovery untuk incident tertentu.
     */
    public function recover(
        IncidentLog $incident,
        MonitorConfig $config
    ): array {
        $allowedActions = $config->allowedRecoveryActions();

        if (empty($allowedActions)) {
            Log::warning('SystemGuard no recovery actions configured', [
                'incident_id' => $incident->incident_id,
                'config_id'   => $config->id,
            ]);

            return [
                'success'  => false,
                'message'  => 'No recovery actions configured',
                'action'   => null,
                'result'   => null,
            ];
        }

        $incident->markRecovering();

        foreach ($allowedActions as $actionKey) {
            if (!$this->isActionAllowed($actionKey)) {
                Log::warning('SystemGuard blocked action attempted', [
                    'incident_id' => $incident->incident_id,
                    'action'      => $actionKey,
                ]);

                continue;
            }

            $result = $this->executeAction($actionKey, $incident, $config);

            if ($result['executed']) {
                return $result;
            }
        }

        return [
            'success'  => false,
            'message'  => 'All recovery actions failed or were blocked',
            'action'   => null,
            'result'   => null,
        ];
    }

    /**
     * Jalankan recovery untuk semua target yang bermasalah.
     *
     * @return array<int, array{incident: IncidentLog, recovery: array}>
     */
    public function recoverAll(): array
    {
        $openIncidents = IncidentLog::open()->get();
        $results = [];

        foreach ($openIncidents as $incident) {
            $config = MonitorConfig::find($incident->monitor_config_id);

            if (!$config || !$config->auto_recovery_enabled) {
                continue;
            }

            if ($incident->retry_count >= $config->max_retries) {
                $incident->markEscalated();

                $results[] = [
                    'incident' => $incident,
                    'recovery' => [
                        'success'  => false,
                        'message'  => 'Max retries reached, escalated',
                        'action'   => null,
                        'result'   => null,
                    ],
                ];

                continue;
            }

            $recovery = $this->recover($incident, $config);
            $incident->incrementRetry();

            $results[] = [
                'incident' => $incident,
                'recovery' => $recovery,
            ];
        }

        return $results;
    }

    /**
     * Check apakah action diizinkan (ada di whitelist, tidak di-block).
     */
    public function isActionAllowed(string $action): bool
    {
        $whitelist = config('system-guard.recovery_whitelist', []);
        $blocked   = config('system-guard.blocked_actions', []);

        if (in_array($action, $blocked, true)) {
            return false;
        }

        return isset($whitelist[$action]);
    }

    /**
     * Dapatkan daftar semua action yang diizinkan.
     */
    public function getAllowedActions(): array
    {
        return config('system-guard.recovery_whitelist', []);
    }

    /**
     * Dapatkan daftar action yang diblokir.
     */
    public function getBlockedActions(): array
    {
        return config('system-guard.blocked_actions', []);
    }

    /*
    |--------------------------------------------------------------------------
    | ACTION EXECUTION
    |--------------------------------------------------------------------------
    */

    protected function executeAction(
        string $actionKey,
        IncidentLog $incident,
        MonitorConfig $config
    ): array {
        $whitelist = config('system-guard.recovery_whitelist', []);
        $actionDef = $whitelist[$actionKey] ?? null;

        if (!$actionDef) {
            return [
                'executed' => false,
                'success'  => false,
                'message'  => "Unknown action: {$actionKey}",
                'action'   => $actionKey,
                'result'   => null,
            ];
        }

        $log = RecoveryLog::create([
            'incident_id'       => $incident->incident_id,
            'monitor_config_id' => $config->id,
            'action'            => $actionKey,
            'action_type'       => $actionDef['type'],
            'status'            => 'EXECUTING',
            'attempt_number'    => $incident->retry_count + 1,
            'started_at'        => now(),
        ]);

        try {
            $result = match ($actionKey) {
                'dns_cache_flush'   => $this->executeDnsCacheFlush($incident, $config),
                'retry_connection'  => $this->executeRetryConnection($incident, $config),
                'wait_and_retry'    => $this->executeWaitAndRetry($incident, $config),
                'notify_admin'      => $this->executeNotifyAdmin($incident, $config),
                'escalate'          => $this->executeEscalate($incident, $config),
                'start_cloudflared' => $this->executeStartCloudflared($incident, $config),
                'restart_cloudflared' => $this->executeRestartCloudflared($incident, $config),
                default             => [
                    'success' => false,
                    'message' => "Unknown action handler: {$actionKey}",
                ],
            };

            $log->markCompleted(
                $result['success'],
                $result['message'] ?? null
            );

            Log::info('SystemGuard recovery action executed', [
                'incident_id' => $incident->incident_id,
                'action'      => $actionKey,
                'success'     => $result['success'],
                'message'     => $result['message'] ?? null,
            ]);

            return [
                'executed' => true,
                'success'  => $result['success'],
                'message'  => $result['message'] ?? null,
                'action'   => $actionKey,
                'result'   => $result,
            ];
        } catch (\Throwable $e) {
            $log->markCompleted(false, 'Exception: ' . $e->getMessage());

            Log::error('SystemGuard recovery action failed', [
                'incident_id' => $incident->incident_id,
                'action'      => $actionKey,
                'error'       => $e->getMessage(),
            ]);

            return [
                'executed' => true,
                'success'  => false,
                'message'  => 'Action failed: ' . $e->getMessage(),
                'action'   => $actionKey,
                'result'   => null,
            ];
        }
    }

    protected function executeDnsCacheFlush(
        IncidentLog $incident,
        MonitorConfig $config
    ): array {
        $domain = $config->domain;

        if (!$domain) {
            return [
                'success' => false,
                'message' => 'No domain to flush',
            ];
        }

        $platform = PHP_OS_FAMILY;

        $commands = match ($platform) {
            'Windows' => [
                "ipconfig /flushdns",
            ],
            'Linux'   => [
                "sudo systemd-resolve --flush-caches 2>/dev/null || sudo resolvectl flush-caches 2>/dev/null || echo 'DNS flush attempted'",
            ],
            'Darwin'  => [
                "sudo dscacheutil -flushcache",
                "sudo killall -HUP mDNSResponder",
            ],
            default   => [
                "echo 'Platform not supported for DNS flush'",
            ],
        };

        $output = [];
        $exitCode = 0;

        foreach ($commands as $cmd) {
            exec($cmd . ' 2>&1', $output, $exitCode);

            if ($exitCode === 0) {
                break;
            }
        }

        return [
            'success' => true,
            'message' => 'DNS cache flush executed on ' . $platform,
            'output'  => implode("\n", $output),
        ];
    }

    protected function executeRetryConnection(
        IncidentLog $incident,
        MonitorConfig $config
    ): array {
        $result = $this->monitoringService->check($config);

        return [
            'success' => $result->isHealthy(),
            'message' => $result->isHealthy()
                ? 'Connection retry successful, status: ' . $result->status
                : 'Connection retry failed: ' . ($result->error_message ?? 'Unknown error'),
            'result'  => $result,
        ];
    }

    protected function executeWaitAndRetry(
        IncidentLog $incident,
        MonitorConfig $config
    ): array {
        $delay = $config->retry_delay_seconds;

        sleep(min($delay, 60));

        $result = $this->monitoringService->check($config);

        return [
            'success' => $result->isHealthy(),
            'message' => $result->isHealthy()
                ? "Waited {$delay}s, retry successful"
                : "Waited {$delay}s, retry still failed: " . ($result->error_message ?? 'Unknown error'),
            'result'  => $result,
        ];
    }

    protected function executeNotifyAdmin(
        IncidentLog $incident,
        MonitorConfig $config
    ): array {
        $roles = config('system-guard.notification.notify_roles', ['ADMINISTRATOR']);

        Log::alert('SystemGuard incident requires attention', [
            'incident_id' => $incident->incident_id,
            'target'      => $incident->target,
            'error'       => $incident->error_message,
            'severity'    => $incident->severity,
            'retry_count' => $incident->retry_count,
        ]);

        return [
            'success' => true,
            'message' => 'Admin notification sent for roles: ' . implode(', ', $roles),
        ];
    }

    protected function executeEscalate(
        IncidentLog $incident,
        MonitorConfig $config
    ): array {
        $incident->markEscalated();

        Log::alert('SystemGuard incident escalated', [
            'incident_id' => $incident->incident_id,
            'target'      => $incident->target,
            'error'       => $incident->error_message,
            'severity'    => $incident->severity,
            'retry_count' => $incident->retry_count,
        ]);

        return [
            'success' => true,
            'message' => 'Incident escalated to administrator',
        ];
    }

    /**
     * Mulai cloudflared (allowlisted, command template fixed).
     */
    protected function executeStartCloudflared(
        IncidentLog $incident,
        MonitorConfig $config
    ): array {
        if (!$this->tunnelRestarter) {
            return [
                'success' => false,
                'message' => 'Tunnel restarter not available',
            ];
        }

        if ($this->tunnelRestarter->isRunning()) {
            return [
                'success' => true,
                'message' => 'cloudflared already running',
            ];
        }

        $result = $this->tunnelRestarter->start();

        return [
            'success' => $result['success'],
            'message' => ($result['success'] ? 'Tunnel started: ' : 'Tunnel start failed: ') . $result['message'],
            'result'  => $result,
        ];
    }

    /**
     * Restart cloudflared (allowlisted, command template fixed).
     */
    protected function executeRestartCloudflared(
        IncidentLog $incident,
        MonitorConfig $config
    ): array {
        if (!$this->tunnelRestarter) {
            return [
                'success' => false,
                'message' => 'Tunnel restarter not available',
            ];
        }

        $result = $this->tunnelRestarter->restart();

        return [
            'success' => $result['success'],
            'message' => ($result['success'] ? 'Tunnel restarted: ' : 'Tunnel restart failed: ') . $result['message'],
            'result'  => $result,
        ];
    }
}
