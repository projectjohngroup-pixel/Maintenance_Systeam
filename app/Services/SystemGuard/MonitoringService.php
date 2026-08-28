<?php

namespace App\Services\SystemGuard;

use App\Models\SystemGuard\MonitorConfig;
use App\Models\SystemGuard\MonitorResult;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;

class MonitoringService
{
    /*
    |--------------------------------------------------------------------------
    | MONITORING SERVICE
    |--------------------------------------------------------------------------
    |
    | Melakukan health check terhadap target yang dikonfigurasi.
    | Mendukung: HTTP check, DNS check, Connection check.
    |
    | Flow:
    | 1. Check DNS resolution
    | 2. Check connection
    | 3. Check HTTP response
    | 4. Evaluate response time
    | 5. Determine status & severity
    | 6. Save result
    |
    */

    /**
     * Jalankan health check terhadap monitor config.
     */
    public function check(MonitorConfig $config): MonitorResult
    {
        $startTime = microtime(true);

        $result = [
            'monitor_config_id'    => $config->id,
            'status'               => 'ONLINE',
            'category'             => 'normal',
            'dns_resolved'         => true,
            'connection_successful' => true,
            'http_successful'      => true,
            'http_status_code'     => null,
            'response_time_ms'     => null,
            'timeout_ms'           => $config->timeout_seconds * 1000,
            'error_message'        => null,
            'error_type'           => null,
            'severity'             => 'normal',
            'raw_response'         => null,
        ];

        // Step 1: DNS Check
        $dnsResult = $this->checkDns($config);
        $result['dns_resolved'] = $dnsResult['resolved'];

        if (!$dnsResult['resolved']) {
            $result['status']         = 'GANGGUAN';
            $result['category']       = 'DNS';
            $result['error_message']  = $dnsResult['error'];
            $result['error_type']     = $dnsResult['error_type'];
            $result['severity']       = $this->determineSeverity('dns', $dnsResult['error_type']);
            $result['raw_response']   = $dnsResult;

            return $this->saveResult($config, $result, $startTime);
        }

        // Step 2: Connection Check
        $connectionResult = $this->checkConnection($config);
        $result['connection_successful'] = $connectionResult['connected'];

        if (!$connectionResult['connected']) {
            $result['status']         = 'GANGGUAN';
            $result['category']       = 'CONNECTION';
            $result['error_message']  = $connectionResult['error'];
            $result['error_type']     = $connectionResult['error_type'];
            $result['severity']       = $this->determineSeverity('connection', $connectionResult['error_type']);
            $result['raw_response']   = $connectionResult;

            return $this->saveResult($config, $result, $startTime);
        }

        // Step 3: HTTP Check
        $httpResult = $this->checkHttp($config);
        $result['http_status_code'] = $httpResult['status_code'];
        $result['response_time_ms'] = $httpResult['response_time_ms'];
        $result['http_successful']  = $httpResult['successful'];
        $result['raw_response']     = $httpResult;

        if (!$httpResult['successful']) {
            $result['status']         = 'GANGGUAN';
            $result['category']       = 'HTTP';
            $result['error_message']  = $httpResult['error'];
            $result['error_type']     = $httpResult['error_type'];
            $result['severity']       = $this->determineSeverity('http', $httpResult['error_type']);

            return $this->saveResult($config, $result, $startTime);
        }

        // Step 4: Response Time Check
        if ($result['response_time_ms'] > $config->response_time_threshold_ms) {
            $result['status']   = 'WASPADA';
            $result['category'] = 'PERFORMANCE';
            $result['severity'] = $result['response_time_ms'] > config('system-guard.thresholds.response_time_critical', 5000)
                ? 'HIGH'
                : 'MEDIUM';
        }

        return $this->saveResult($config, $result, $startTime);
    }

    /**
     * Check DNS resolution untuk target domain.
     */
    public function checkDns(MonitorConfig $config): array
    {
        $domain = $config->domain;

        if (!$domain) {
            return [
                'resolved'   => false,
                'error'      => 'No domain configured',
                'error_type' => 'DNS_NO_DOMAIN',
            ];
        }

        try {
            $records = dns_get_record($domain, DNS_A | DNS_AAAA);

            if (empty($records)) {
                return [
                    'resolved'   => false,
                    'error'      => "DNS probe finished: NXDOMAIN for {$domain}",
                    'error_type' => 'DNS_NXDOMAIN',
                ];
            }

            return [
                'resolved'   => true,
                'records'    => $records,
                'error'      => null,
                'error_type' => null,
            ];
        } catch (\Throwable $e) {
            return [
                'resolved'   => false,
                'error'      => 'DNS resolution failed: ' . $e->getMessage(),
                'error_type' => 'DNS_ERROR',
            ];
        }
    }

    /**
     * Check TCP connection ke target.
     */
    public function checkConnection(MonitorConfig $config): array
    {
        $domain = $config->domain;
        $parsed = parse_url($config->target_url);
        $port   = $parsed['port'] ?? ($parsed['scheme'] === 'https' ? 443 : 80);
        $host   = $domain ?? ($parsed['host'] ?? '');
        $timeout = $config->timeout_seconds;

        if (empty($host)) {
            return [
                'connected'  => false,
                'error'      => 'No host configured',
                'error_type' => 'NO_HOST',
            ];
        }

        try {
            $connection = @fsockopen($host, $port, $errno, $errstr, $timeout);

            if ($connection) {
                fclose($connection);

                return [
                    'connected'  => true,
                    'error'      => null,
                    'error_type' => null,
                ];
            }

            $errorType = 'CONNECTION_REFUSED';

            if ($errno === 110) {
                $errorType = 'TIMEOUT';
            } elseif ($errno === 104) {
                $errorType = 'CONNECTION_RESET';
            }

            return [
                'connected'  => false,
                'error'      => "Connection failed: {$errstr} (errno: {$errno})",
                'error_type' => $errorType,
            ];
        } catch (\Throwable $e) {
            return [
                'connected'  => false,
                'error'      => 'Connection error: ' . $e->getMessage(),
                'error_type' => 'CONNECTION_ERROR',
            ];
        }
    }

    /**
     * Check HTTP response dari target URL.
     */
    public function checkHttp(MonitorConfig $config): array
    {
        $timeout  = $config->timeout_seconds;
        $expected = $config->expected_status_code;

        try {
            $response = Http::timeout($timeout)
                ->withOptions([
                    'verify'        => true,
                    'connect_timeout' => $timeout,
                ])
                ->withHeaders([
                    'User-Agent' => config('system-guard.test.http_user_agent', 'SystemGuard/1.0'),
                ])
                ->get($config->target_url);

            $statusCode   = $response->status();
            $responseTime = (int) ($response->transferStats?->getTransferTime() * 1000 ?? 0);
            $successful   = $response->successful();

            $errorType = null;
            $error     = null;

            if (!$successful) {
                if ($statusCode >= 400 && $statusCode < 500) {
                    $errorType = 'HTTP_4XX';
                    $error     = "HTTP {$statusCode} Client Error";
                } elseif ($statusCode >= 500) {
                    $errorType = 'HTTP_5XX';
                    $error     = "HTTP {$statusCode} Server Error";
                }
            }

            return [
                'status_code'      => $statusCode,
                'response_time_ms' => $responseTime,
                'successful'       => $successful,
                'error'            => $error,
                'error_type'       => $errorType,
                'body'             => $response->body(),
                'headers'          => $response->headers(),
            ];
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            $errorType = 'TIMEOUT';

            if (str_contains($e->getMessage(), 'Connection refused')) {
                $errorType = 'CONNECTION_REFUSED';
            }

            return [
                'status_code'      => null,
                'response_time_ms' => null,
                'successful'       => false,
                'error'            => 'HTTP request failed: ' . $e->getMessage(),
                'error_type'       => $errorType,
                'body'             => null,
                'headers'          => null,
            ];
        } catch (\Throwable $e) {
            return [
                'status_code'      => null,
                'response_time_ms' => null,
                'successful'       => false,
                'error'            => 'HTTP request error: ' . $e->getMessage(),
                'error_type'       => 'HTTP_ERROR',
                'body'             => null,
                'headers'          => null,
            ];
        }
    }

    /**
     * Check semua active monitor configs.
     *
     * @return array<int, array{config: MonitorConfig, result: MonitorResult}>
     */
    public function checkAll(): array
    {
        $configs = MonitorConfig::active()->get();
        $results = [];

        foreach ($configs as $config) {
            $results[] = [
                'config' => $config,
                'result' => $this->check($config),
            ];
        }

        return $results;
    }

    /**
     * Check hanya target tertentu.
     */
    public function checkTarget(string $targetUrl): ?MonitorResult
    {
        $config = MonitorConfig::where('target_url', $targetUrl)
            ->active()
            ->first();

        if (!$config) {
            return null;
        }

        return $this->check($config);
    }

    /*
    |--------------------------------------------------------------------------
    | PRIVATE METHODS
    |--------------------------------------------------------------------------
    */

    private function determineSeverity(string $category, ?string $errorType): string
    {
        return match (true) {
            $category === 'dns' && $errorType === 'DNS_NXDOMAIN' => 'HIGH',
            $category === 'dns' && $errorType === 'DNS_ERROR'   => 'HIGH',
            $category === 'dns'                                   => 'MEDIUM',
            $category === 'connection' && $errorType === 'TIMEOUT' => 'MEDIUM',
            $category === 'connection'                              => 'HIGH',
            $category === 'http' && $errorType === 'HTTP_5XX'   => 'HIGH',
            $category === 'http' && $errorType === 'HTTP_4XX'   => 'MEDIUM',
            $category === 'http'                                   => 'MEDIUM',
            default                                                => 'LOW',
        };
    }

    private function saveResult(MonitorConfig $config, array $result, float $startTime): MonitorResult
    {
        $elapsed = (int) ((microtime(true) - $startTime) * 1000);

        $result['timeout_ms'] = $elapsed;

        Log::info('SystemGuard check completed', [
            'config_id'    => $config->id,
            'target'       => $config->target_url,
            'status'       => $result['status'],
            'error_type'   => $result['error_type'],
            'duration_ms'  => $elapsed,
        ]);

        return MonitorResult::create($result);
    }
}
