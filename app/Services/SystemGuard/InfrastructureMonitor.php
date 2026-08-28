<?php

namespace App\Services\SystemGuard;

use App\Models\SystemGuard\DowntimeLog;
use App\Models\SystemGuard\SystemGuardState;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;

class InfrastructureMonitor
{
    /*
    |--------------------------------------------------------------------------
    | INFRASTRUCTURE MONITOR (STEP 4)
    |--------------------------------------------------------------------------
    |
    | Memantau status per-komponen secara terpisah:
    |   - internet : koneksi internet mesin lokal (DNS_PROBE / tidak ada koneksi)
    |   - tunnel   : proses cloudflared + hostname trycloudflare ter-resolve
    |   - origin   : Maintenance System (server lokal) merespons HTTP
    |
    | Tujuan: membedakan sumber masalah. DNS_NXDOMAIN pada hostname tunnel
    | != internet putus. Jangan salahkan kode aplikasi bila tunnel yang mati.
    |
    */

    protected const COMPONENT_INTERNET = 'internet';
    protected const COMPONENT_TUNNEL = 'tunnel';
    protected const COMPONENT_ORIGIN = 'origin';

    protected array $cache = [];

    /**
     * Jalankan pengecekan seluruh komponen.
     *
     * @return array<string, array{status: string, component: string, message: string, online: bool, error_type: ?string}>
     */
    public function checkAll(): array
    {
        $internet = $this->checkInternet();
        $tunnel   = $this->checkTunnel();
        $origin   = $this->checkOrigin();

        $result = [
            'internet' => $internet,
            'tunnel'   => $tunnel,
            'origin'   => $origin,
        ];

        // Catat state & downtime untuk tiap komponen
        $this->recordComponentState(self::COMPONENT_INTERNET, $internet);
        $this->recordComponentState(self::COMPONENT_TUNNEL, $tunnel);
        $this->recordComponentState(self::COMPONENT_ORIGIN, $origin);

        return $result;
    }

    /**
     * Pengecekan tak-terkait-DB (untuk daemon/quick mode yang butuh cepat).
     */
    public function checkAllFast(): array
    {
        $internet = $this->checkInternet();
        $tunnel   = $this->checkTunnel();

        return [
            'internet' => $internet,
            'tunnel'   => $tunnel,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | PER-KOMPONEN
    |--------------------------------------------------------------------------
    */

    public function checkInternet(): array
    {
        $targets = config('system-guard.components.internet.check_targets', [
            'https://1.1.1.1',
            'https://8.8.8.8',
        ]);

        foreach ($targets as $target) {
            try {
                $response = Http::timeout(5)->get($target);

                if ($response->successful()) {
                    return $this->success(Self::COMPONENT_INTERNET, "Internet OK ({$target})");
                }
            } catch (\Throwable $e) {
                // lanjut coba target lain
            }
        }

        // Jika semua target HTTP gagal, coba DNS resolution sederhana
        $dnsTargets = config('system-guard.components.internet.dns_targets', ['1.1.1.1', 'google.com']);

        foreach ($dnsTargets as $dns) {
            try {
                $records = @dns_get_record($dns, DNS_A);
                if (!empty($records)) {
                    return $this->warning(Self::COMPONENT_INTERNET, "HTTP gagal tapi DNS OK ({$dns})", 'DEGRADED', 'PARTIAL_INTERNET');
                }
            } catch (\Throwable $e) {
                // teruskan
            }
        }

        return $this->failure(
            Self::COMPONENT_INTERNET,
            'Semua target internet tidak dapat dijangkau',
            'NETWORK_UNREACHABLE'
        );
    }

    public function checkTunnel(): array
    {
        // 1) Cek proses cloudflared berjalan
        $processCheck = $this->checkTunnelProcess();

        // 2) Cek hostname tunnel ter-resolve (APP_URL)
        $hostname = $this->tunnelHostname();
        $dnsCheck = $hostname ? $this->resolveHostname($hostname) : null;

        if (!$processCheck['running'] && !$dnsCheck['resolved']) {
            return $this->failure(
                Self::COMPONENT_TUNNEL,
                'Proses cloudflared tidak berjalan dan hostname tunnel tidak ter-resolve',
                'TUNNEL_DOWN'
            );
        }

        if (!$processCheck['running']) {
            return $this->failure(
                Self::COMPONENT_TUNNEL,
                'Proses cloudflared tidak berjalan',
                'TUNNEL_PROCESS_DOWN'
            );
        }

        if ($dnsCheck && !$dnsCheck['resolved']) {
            return $this->warning(
                Self::COMPONENT_TUNNEL,
                'Proses berjalan tapi hostname tunnel tidak ter-resolve (DNS_NXDOMAIN)',
                'DEGRADED',
                'DNS_NXDOMAIN'
            );
        }

        return $this->success(Self::COMPONENT_TUNNEL, 'Cloudflare Tunnel OK');
    }

    public function checkOrigin(): array
    {
        if (!config('system-guard.components.origin.enabled', true)) {
            return $this->success(Self::COMPONENT_ORIGIN, 'Origin monitoring disabled');
        }

        $baseUrl = rtrim(config('system-guard.components.origin.base_url', 'http://localhost'), '/');
        $path    = ltrim(config('system-guard.components.origin.check_path', '/'), '/');
        $url     = $baseUrl . '/' . $path;
        $expected = (int) config('system-guard.components.origin.expected_status_code', 200);

        try {
            $response = Http::timeout(8)->get($url);

            if ($response->status() === $expected) {
                return $this->success(Self::COMPONENT_ORIGIN, "Origin OK ({$response->status()})");
            }

            return $this->failure(
                Self::COMPONENT_ORIGIN,
                "Origin HTTP {$response->status()} (expected {$expected})",
                'HTTP_' . (int) floor($response->status() / 100) . 'XX'
            );
        } catch (\Throwable $e) {
            return $this->failure(
                Self::COMPONENT_ORIGIN,
                'Origin tidak dapat dijangkau: ' . $e->getMessage(),
                'ORIGIN_UNREACHABLE'
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | PROCESS / HOSTNAME
    |--------------------------------------------------------------------------
    */

    public function checkTunnelProcess(): array
    {
        $processName = config('system-guard.cloudflared.process_name', 'cloudflared');

        // Deteksi proses via tasklist (Windows) atau pgrep (Linux/mac)
        if (PHP_OS_FAMILY === 'Windows') {
            try {
                $output = Process::run("tasklist /FI \"IMAGENAME eq {$processName}.exe\" /NH")->output();
                $running = str_contains(strtolower($output), strtolower($processName . '.exe'));
            } catch (\Throwable $e) {
                $running = false;
            }
        } else {
            try {
                $result = Process::run("pgrep -x {$processName} >/dev/null 2>&1");
                $running = $result->exitCode() === 0;
            } catch (\Throwable $e) {
                $running = false;
            }
        }

        return ['running' => $running];
    }

    public function resolveHostname(string $hostname): array
    {
        try {
            $records = @dns_get_record($hostname, DNS_A | DNS_AAAA);

            if (empty($records)) {
                return [
                    'resolved' => false,
                    'error'    => "DNS probe finished: NXDOMAIN for {$hostname}",
                    'error_type' => 'DNS_NXDOMAIN',
                ];
            }

            return [
                'resolved' => true,
                'records'  => $records,
                'error'    => null,
                'error_type' => null,
            ];
        } catch (\Throwable $e) {
            return [
                'resolved' => false,
                'error'    => 'DNS resolution failed: ' . $e->getMessage(),
                'error_type' => 'DNS_ERROR',
            ];
        }
    }

    public function tunnelHostname(): ?string
    {
        $appUrl = config('app.url');

        $parsed = parse_url($appUrl);

        if (!$parsed || empty($parsed['host'])) {
            return null;
        }

        return $parsed['host'];
    }

    /*
    |--------------------------------------------------------------------------
    | STATE & DOWNTIME RECORDING
    |--------------------------------------------------------------------------
    */

    protected function recordComponentState(string $component, array $check): void
    {
        $state = SystemGuardState::forComponent($component);

        $newStatus = $check['status']; // ONLINE / DEGRADED / OFFLINE

        $stateChanged = $state->status !== $newStatus;

        if ($stateChanged) {
            // Tutup downtime sebelumnya saat kembali sehat
            if ($check['online'] && ($state->isDegraded() || $state->isOffline())) {
                if ($downtime = DowntimeLog::openFor($component)) {
                    $downtime->close($check['message']);
                }
            }

            // Buka downtime baru saat mulai bermasalah
            if (!$check['online'] && ($state->isHealthy())) {
                DowntimeLog::create([
                    'component'     => $component,
                    'status'        => $newStatus,
                    'error_type'    => $check['error_type'] ?? null,
                    'error_message' => $check['message'],
                    'started_at'    => now(),
                ]);
            }
        }

        $consecutive = $check['online']
            ? 0
            : $state->consecutive_failures + 1;

        $state->update([
            'status'               => $newStatus,
            'message'              => $check['message'],
            'error_type'           => $check['error_type'] ?? null,
            'last_value'           => $this->lastValue($check),
            'state_changed_at'     => $stateChanged ? now() : $state->state_changed_at,
            'last_checked_at'      => now(),
            'consecutive_failures' => $consecutive,
        ]);
    }

    protected function lastValue(array $check): ?string
    {
        return $check['last_value'] ?? $check['message'];
    }

    /*
    |--------------------------------------------------------------------------
    | HELPERS
    |--------------------------------------------------------------------------
    */

    protected function success(string $component, string $message): array
    {
        return [
            'component'  => $component,
            'status'     => 'ONLINE',
            'message'    => $message,
            'online'     => true,
            'error_type' => null,
        ];
    }

    protected function warning(string $component, string $message, string $status = 'DEGRADED', ?string $errorType = null): array
    {
        return [
            'component'  => $component,
            'status'     => $status,
            'message'    => $message,
            'online'     => false,
            'error_type' => $errorType,
        ];
    }

    protected function failure(string $component, string $message, string $errorType): array
    {
        return [
            'component'  => $component,
            'status'     => 'OFFLINE',
            'message'    => $message,
            'online'     => false,
            'error_type' => $errorType,
        ];
    }
}
