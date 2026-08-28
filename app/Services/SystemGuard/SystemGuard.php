<?php

namespace App\Services\SystemGuard;

use App\Models\SystemGuard\IncidentLog;
use App\Models\SystemGuard\MonitorConfig;
use App\Models\SystemGuard\MonitorResult;
use App\Models\SystemGuard\RecoveryLog;
use Illuminate\Support\Facades\Log;

class SystemGuard
{
    /*
    |--------------------------------------------------------------------------
    | SYSTEM GUARD — ORCHESTRATOR
    |--------------------------------------------------------------------------
    |
    | Mengkoordinasikan seluruh flow:
    |
    | DETECT → IDENTIFY → FOCUS ERROR → SAFE RECOVERY
    | → RETEST → VERIFY → ONLINE / GANGGUAN
    |
    | Flow Lengkap:
    | 1. Monitor semua target aktif
    | 2. Identifikasi error
    | 3. Catat incident
    | 4. Jalankan recovery (jika auto_recovery enabled)
    | 5. Verifikasi hasil recovery
    | 6. Update status
    | 7. Generate laporan
    |
    */

    protected MonitoringService $monitoringService;
    protected RecoveryEngine $recoveryEngine;
    protected VerificationService $verificationService;

    public function __construct(
        MonitoringService $monitoringService,
        RecoveryEngine $recoveryEngine,
        VerificationService $verificationService
    ) {
        $this->monitoringService   = $monitoringService;
        $this->recoveryEngine      = $recoveryEngine;
        $this->verificationService = $verificationService;
    }

    /**
     * Jalankan monitoring cycle penuh untuk semua target.
     *
     * @return array{checked: int, healthy: int, unhealthy: int, recovered: int, failed: int, incidents: array}
     */
    public function runFullCycle(): array
    {
        Log::info('SystemGuard full cycle started');

        $startTime = microtime(true);

        $results = [
            'checked'    => 0,
            'healthy'    => 0,
            'unhealthy'  => 0,
            'recovered'  => 0,
            'failed'     => 0,
            'incidents'  => [],
        ];

        // Step 1: Check semua target
        $checkResults = $this->monitoringService->checkAll();

        foreach ($checkResults as $checkData) {
            /** @var MonitorConfig $config */
            $config = $checkData['config'];
            /** @var MonitorResult $result */
            $result = $checkData['result'];

            $results['checked']++;

            if ($result->isHealthy()) {
                $results['healthy']++;
                $this->handleHealthyTarget($config, $result);
                continue;
            }

            $results['unhealthy']++;

            // Step 2: Create or update incident
            $incident = $this->handleUnhealthyTarget($config, $result);

            // Step 3: Attempt recovery
            if ($config->auto_recovery_enabled && $incident) {
                $recoveryResult = $this->attemptRecovery($incident, $config);

                if ($recoveryResult['recovered']) {
                    $results['recovered']++;
                } else {
                    $results['failed']++;
                }
            }

            $results['incidents'][] = [
                'incident_id' => $incident?->incident_id,
                'target'      => $config->target_url,
                'error'       => $result->error_message,
                'status'      => $incident?->status ?? 'OPEN',
            ];
        }

        $elapsed = round((microtime(true) - $startTime) * 1000);

        Log::info('SystemGuard full cycle completed', [
            'checked'   => $results['checked'],
            'healthy'   => $results['healthy'],
            'unhealthy' => $results['unhealthy'],
            'recovered' => $results['recovered'],
            'failed'    => $results['failed'],
            'elapsed_ms' => $elapsed,
        ]);

        return $results;
    }

    /**
     * Check satu target dan handle hasilnya.
     */
    public function runSingleCheck(MonitorConfig $config): array
    {
        Log::info('SystemGuard single check started', [
            'config_id' => $config->id,
            'target'    => $config->target_url,
        ]);

        $result = $this->monitoringService->check($config);

        $response = [
            'config_id'     => $config->id,
            'target'        => $config->target_url,
            'healthy'       => $result->isHealthy(),
            'status'        => $result->status,
            'error_type'    => $result->error_type,
            'incident_id'   => null,
            'recovered'     => false,
            'verification'  => null,
        ];

        if ($result->isHealthy()) {
            $this->handleHealthyTarget($config, $result);

            return $response;
        }

        // Handle unhealthy
        $incident = $this->handleUnhealthyTarget($config, $result);
        $response['incident_id'] = $incident?->incident_id;

        // Attempt recovery
        if ($config->auto_recovery_enabled && $incident) {
            $recoveryResult = $this->attemptRecovery($incident, $config);
            $response['recovered']     = $recoveryResult['recovered'];
            $response['verification']  = $recoveryResult['verification'] ?? null;
        }

        return $response;
    }

    /**
     * Check target tertentu by URL.
     */
    public function checkTarget(string $targetUrl): ?array
    {
        $config = MonitorConfig::where('target_url', $targetUrl)
            ->active()
            ->first();

        if (!$config) {
            return null;
        }

        return $this->runSingleCheck($config);
    }

    /**
     * Recover semua incident yang masih open.
     *
     * @return array<int, array{incident: IncidentLog, recovered: bool}>
     */
    public function recoverAllOpen(): array
    {
        $recoveryResults = $this->recoveryEngine->recoverAll();
        $results = [];

        foreach ($recoveryResults as $recoveryData) {
            /** @var IncidentLog $incident */
            $incident = $recoveryData['incident'];
            $recovery = $recoveryData['recovery'];

            $recovered = false;

            if ($recovery['success'] && isset($recovery['result']['result'])) {
                // Verify recovery
                $config = MonitorConfig::find($incident->monitor_config_id);

                if ($config) {
                    $verification = $this->verificationService->verify($config, $incident);

                    if ($verification['passed']) {
                        $incident->markResolved([
                            'action'     => $recovery['action'],
                            'message'    => $recovery['message'],
                            'verification' => $this->verificationService->formatVerificationResult($verification),
                        ]);
                        $recovered = true;
                    }
                }
            }

            $results[] = [
                'incident' => $incident,
                'recovered' => $recovered,
            ];
        }

        return $results;
    }

    /**
     * Dapatkan status summary untuk semua target.
     */
    public function getStatusSummary(): array
    {
        $configs = MonitorConfig::active()->get();
        $summary = [
            'total'     => 0,
            'online'    => 0,
            'gangguan'  => 0,
            'waspada'   => 0,
            'targets'   => [],
        ];

        foreach ($configs as $config) {
            $latestResult = $config->latestResult;
            $hasOpenIncident = $config->hasOpenIncident();

            $status = 'UNKNOWN';

            if ($latestResult) {
                $status = $latestResult->status;
            } elseif ($hasOpenIncident) {
                $status = 'GANGGUAN';
            }

            $summary['total']++;

            match ($status) {
                'ONLINE'  => $summary['online']++,
                'GANGGUAN' => $summary['gangguan']++,
                'WASPADA'  => $summary['waspada']++,
                default    => null,
            };

            $summary['targets'][] = [
                'id'              => $config->id,
                'name'            => $config->name,
                'target'          => $config->target_url,
                'status'          => $status,
                'has_open_incident' => $hasOpenIncident,
                'last_check'      => $latestResult?->created_at?->toISOString(),
                'error_type'      => $latestResult?->error_type,
            ];
        }

        return $summary;
    }

    /**
     * Dapatkan laporan incident untuk periode tertentu.
     */
    public function getIncidentReport(int $hours = 24): array
    {
        $incidents = IncidentLog::recent($hours)->get();

        $report = [
            'period_hours'      => $hours,
            'total_incidents'   => $incidents->count(),
            'resolved'          => $incidents->where('status', 'RESOLVED')->count(),
            'open'              => $incidents->where('status', 'OPEN')->count(),
            'recovering'        => $incidents->where('status', 'RECOVERING')->count(),
            'escalated'         => $incidents->where('status', 'ESCALATED')->count(),
            'failed'            => $incidents->where('status', 'FAILED')->count(),
            'recovery_success_rate' => 0,
            'avg_recovery_time' => 0,
            'incidents'         => [],
        ];

        if ($report['total_incidents'] > 0) {
            $report['recovery_success_rate'] = round(
                ($report['resolved'] / $report['total_incidents']) * 100,
                1
            );

            $resolvedDurations = $incidents
                ->where('status', 'RESOLVED')
                ->whereNotNull('duration_seconds')
                ->pluck('duration_seconds');

            if ($resolvedDurations->isNotEmpty()) {
                $report['avg_recovery_time'] = round($resolvedDurations->avg(), 1);
            }
        }

        foreach ($incidents as $incident) {
            $report['incidents'][] = [
                'incident_id'    => $incident->incident_id,
                'target'         => $incident->target,
                'error_category' => $incident->error_category,
                'error_type'     => $incident->error_type,
                'error_message'  => $incident->error_message,
                'severity'       => $incident->severity,
                'status'         => $incident->status,
                'detected_at'    => $incident->detected_at?->toISOString(),
                'recovered_at'   => $incident->recovered_at?->toISOString(),
                'duration_seconds' => $incident->duration_seconds,
                'retry_count'    => $incident->retry_count,
            ];
        }

        return $report;
    }

    /*
    |--------------------------------------------------------------------------
    | PRIVATE METHODS
    |--------------------------------------------------------------------------
    */

    private function handleHealthyTarget(
        MonitorConfig $config,
        MonitorResult $result
    ): void {
        // Close any open incidents for this target
        $openIncident = $config->activeIncident();

        if ($openIncident) {
            $openIncident->markResolved([
                'status'  => $result->status,
                'message' => 'Target recovered during routine check',
            ]);

            Log::info('SystemGuard incident auto-resolved', [
                'incident_id' => $openIncident->incident_id,
                'target'      => $config->target_url,
            ]);
        }
    }

    private function handleUnhealthyTarget(
        MonitorConfig $config,
        MonitorResult $result
    ): ?IncidentLog {
        $existingIncident = $config->activeIncident();

        if ($existingIncident) {
            $existingIncident->update([
                'error_message' => $result->error_message,
                'error_type'    => $result->error_type,
                'severity'      => $result->severity,
            ]);

            return $existingIncident;
        }

        $incident = IncidentLog::create([
            'incident_id'       => IncidentLog::generateIncidentId(),
            'monitor_config_id' => $config->id,
            'target'            => $config->target_url,
            'error_category'    => $result->category,
            'error_type'        => $result->error_type ?? 'UNKNOWN',
            'error_message'     => $result->error_message ?? 'Unknown error',
            'severity'          => $result->severity,
            'status'            => 'OPEN',
            'detected_at'       => now(),
            'retry_count'       => 0,
        ]);

        Log::warning('SystemGuard new incident created', [
            'incident_id' => $incident->incident_id,
            'target'      => $config->target_url,
            'error_type'  => $result->error_type,
            'severity'    => $result->severity,
        ]);

        return $incident;
    }

    private function attemptRecovery(
        IncidentLog $incident,
        MonitorConfig $config
    ): array {
        $maxRetries = $config->max_retries;

        if ($incident->retry_count >= $maxRetries) {
            $incident->markEscalated();

            Log::alert('SystemGuard max retries reached, escalating', [
                'incident_id' => $incident->incident_id,
                'retry_count' => $incident->retry_count,
                'max_retries' => $maxRetries,
            ]);

            return [
                'recovered' => false,
                'message'   => 'Max retries reached',
            ];
        }

        $recoveryResult = $this->recoveryEngine->recover($incident, $config);
        $incident->incrementRetry();

        if (!$recoveryResult['success']) {
            if ($incident->fresh()->retry_count >= $maxRetries) {
                $incident->markEscalated();

                Log::alert('SystemGuard max retries reached after failed recovery', [
                    'incident_id' => $incident->incident_id,
                    'retry_count' => $incident->fresh()->retry_count,
                    'max_retries' => $maxRetries,
                ]);

                return [
                    'recovered' => false,
                    'message'   => 'Max retries reached, escalated',
                ];
            }

            return [
                'recovered' => false,
                'message'   => $recoveryResult['message'] ?? 'Recovery failed',
            ];
        }

        // Verify recovery
        $verification = $this->verificationService->verify($config, $incident);

        if ($verification['passed']) {
            $incident->markResolved([
                'action'       => $recoveryResult['action'],
                'message'      => $recoveryResult['message'],
                'verification' => $this->verificationService->formatVerificationResult($verification),
            ]);

            Log::info('SystemGuard recovery successful', [
                'incident_id' => $incident->incident_id,
                'action'      => $recoveryResult['action'],
                'retry_count' => $incident->retry_count,
            ]);

            return [
                'recovered'    => true,
                'message'      => 'Recovery successful',
                'verification' => $verification,
            ];
        }

        Log::warning('SystemGuard recovery verification failed', [
            'incident_id' => $incident->incident_id,
            'action'      => $recoveryResult['action'],
        ]);

        return [
            'recovered'    => false,
            'message'      => 'Recovery verification failed',
            'verification' => $verification,
        ];
    }
}
