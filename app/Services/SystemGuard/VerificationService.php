<?php

namespace App\Services\SystemGuard;

use App\Models\SystemGuard\IncidentLog;
use App\Models\SystemGuard\MonitorConfig;
use App\Models\SystemGuard\MonitorResult;
use Illuminate\Support\Facades\Log;

class VerificationService
{
    /*
    |--------------------------------------------------------------------------
    | VERIFICATION SERVICE
    |--------------------------------------------------------------------------
    |
    | Memvalidasi hasil recovery sebelum menyatakan ONLINE.
    |
    | Flow:
    | 1. Jalankan health check ulang
    | 2. Verify DNS resolve (jika target domain)
    | 3. Verify connection
    | 4. Verify HTTP response
    | 5. Verify response time
    | 6. Tentukan apakah benar-benar ONLINE
    |
    | Jangan menyatakan ONLINE hanya berdasarkan satu indikator.
    | Semua indikator harus valid.
    |
    */

    protected MonitoringService $monitoringService;

    public function __construct(MonitoringService $monitoringService)
    {
        $this->monitoringService = $monitoringService;
    }

    /**
     * Verifikasi apakah target benar-benar ONLINE setelah recovery.
     */
    public function verify(
        MonitorConfig $config,
        ?IncidentLog $incident = null
    ): array {
        Log::info('SystemGuard verification started', [
            'config_id'   => $config->id,
            'target'      => $config->target_url,
            'incident_id' => $incident?->incident_id,
        ]);

        $result = $this->monitoringService->check($config);

        $verification = [
            'passed'           => false,
            'dns_ok'           => $result->dns_resolved,
            'connection_ok'    => $result->connection_successful,
            'http_ok'          => $result->http_successful,
            'response_time_ok' => $this->checkResponseTime($result, $config),
            'status'           => $result->status,
            'http_status_code' => $result->http_status_code,
            'response_time_ms' => $result->response_time_ms,
            'error_message'    => $result->error_message,
            'result'           => $result,
        ];

        // Semua check harus passed
        $verification['passed'] = $verification['dns_ok']
            && $verification['connection_ok']
            && $verification['http_ok']
            && $verification['response_time_ok'];

        Log::info('SystemGuard verification completed', [
            'config_id'   => $config->id,
            'passed'      => $verification['passed'],
            'dns_ok'      => $verification['dns_ok'],
            'connection_ok' => $verification['connection_ok'],
            'http_ok'     => $verification['http_ok'],
            'response_time_ok' => $verification['response_time_ok'],
        ]);

        return $verification;
    }

    /**
     * Verifikasi dengan retry untuk memastikan hasil konsisten.
     */
    public function verifyWithRetry(
        MonitorConfig $config,
        ?IncidentLog $incident = null,
        int $attempts = 3,
        int $delaySeconds = 5
    ): array {
        $lastVerification = null;

        for ($i = 0; $i < $attempts; $i++) {
            $verification = $this->verify($config, $incident);
            $lastVerification = $verification;

            if ($verification['passed']) {
                Log::info('SystemGuard verification passed on attempt', [
                    'config_id' => $config->id,
                    'attempt'   => $i + 1,
                ]);

                return $verification;
            }

            if ($i < $attempts - 1) {
                sleep(min($delaySeconds, 30));
            }
        }

        Log::warning('SystemGuard verification failed after all attempts', [
            'config_id' => $config->id,
            'attempts'  => $attempts,
        ]);

        return $lastVerification;
    }

    /**
     * Verifikasi khusus untuk kasus DNS NXDOMAIN.
     */
    public function verifyDnsRecovery(
        MonitorConfig $config,
        ?IncidentLog $incident = null
    ): array {
        $dnsResult = $this->monitoringService->checkDns($config);

        if (!$dnsResult['resolved']) {
            return [
                'passed'       => false,
                'dns_ok'       => false,
                'error'        => $dnsResult['error'],
                'error_type'   => $dnsResult['error_type'],
            ];
        }

        // DNS resolved, lalu verify HTTP
        $verification = $this->verify($config, $incident);

        return $verification;
    }

    /**
     * Tentukan final status berdasarkan verifikasi.
     */
    public function determineFinalStatus(array $verification): string
    {
        if ($verification['passed']) {
            return config('system-guard.status.online', 'ONLINE');
        }

        if (!$verification['dns_ok']) {
            return config('system-guard.status.gangguan', 'GANGGUAN');
        }

        if (!$verification['connection_ok']) {
            return config('system-guard.status.gangguan', 'GANGGUAN');
        }

        if (!$verification['http_ok']) {
            return config('system-guard.status.perhatian', 'PERHATIAN');
        }

        if (!$verification['response_time_ok']) {
            return config('system-guard.status.waspsda', 'WASPADA');
        }

        return config('system-guard.status.gangguan', 'GANGGUAN');
    }

    /**
     * Format verification result untuk logging/reporting.
     */
    public function formatVerificationResult(array $verification): string
    {
        $lines = [
            'DNS: '         . ($verification['dns_ok'] ? 'OK' : 'FAILED'),
            'Connection: '  . ($verification['connection_ok'] ? 'OK' : 'FAILED'),
            'HTTP: '        . ($verification['http_ok']
                ? ($verification['http_status_code'] ?? 'N/A')
                : 'FAILED'),
            'Response: '    . ($verification['response_time_ms'] !== null
                ? $verification['response_time_ms'] . ' ms'
                : 'N/A'),
            'Result: '      . ($verification['passed'] ? 'ONLINE' : 'GANGGUAN'),
        ];

        return implode(' | ', $lines);
    }

    /*
    |--------------------------------------------------------------------------
    | PRIVATE
    |--------------------------------------------------------------------------
    */

    private function checkResponseTime(
        MonitorResult $result,
        MonitorConfig $config
    ): bool {
        if ($result->response_time_ms === null) {
            return true;
        }

        return $result->response_time_ms <= $config->response_time_threshold_ms;
    }
}
