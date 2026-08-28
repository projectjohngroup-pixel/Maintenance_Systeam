<?php

namespace App\Services\SystemGuard;

use App\Models\SystemGuard\SystemGuardState;
use Illuminate\Support\Facades\Log;

class RecoveryManager
{
    /*
    |--------------------------------------------------------------------------
    | RECOVERY MANAGER (STEP 4)
    |--------------------------------------------------------------------------
    |
    | Mengelola logika auto-recovery dengan safety:
    |   - Cooldown: jarak minimal antar percobaan recovery.
    |   - Backoff: jeda makin lama setelah banyak kegagalan.
    |   - Max consecutive failures: berhenti (escalate) agar tidak loop tak hingga.
    |   - Quick mode: recovery cepat ketika komponen berstatus down.
    |
    */

    protected TunnelRestarter $tunnelRestarter;

    public function __construct(TunnelRestarter $tunnelRestarter)
    {
        $this->tunnelRestarter = $tunnelRestarter;
    }

    /**
     * Apakah sudah waktunya (cooldown/backoff) mencoba recovery lagi?
     */
    public function isCooldownReady(): bool
    {
        $state = SystemGuardState::forComponent('recovery');

        $lastAttemptAt = $state->last_checked_at;

        if (!$lastAttemptAt) {
            return true;
        }

        $cooldown = (int) config('system-guard.daemon.recovery_cooldown_seconds', 300);
        $failures = $state->consecutive_failures;

        $myCooldown = $this->backoffSeconds($cooldown, $failures);

        return $lastAttemptAt->diffInSeconds(now()) >= $myCooldown;
    }

    /**
     * Coba recovery terhadap komponen yang down.
     */
    public function attempt(array $components): array
    {
        $state = SystemGuardState::forComponent('recovery');

        $action = null;
        $performed = false;
        $message = null;

        // Prioritaskan recovery tunnel jika tunnel offline
        if (isset($components['tunnel']) && !$components['tunnel']['online']) {
            $action = $this->recoverTunnel($components['tunnel']);
            $performed = true;
            $message = 'Tunnel recovery performed';
        } elseif (isset($components['origin']) && !$components['origin']['online']) {
            $action = ['name' => 'origin_check', 'success' => false, 'message' => 'Origin offline — ditandai untuk inspeksi manual (recovery otomatis hanya untuk tunnel/DNS)'];
            $performed = true;
            $message = 'Origin offline — menunggu pemeriksaan';
        } elseif (isset($components['internet']) && !$components['internet']['online']) {
            $action = ['name' => 'internet_wait', 'success' => false, 'message' => 'Internet offline — menunggu koneksi kembali (recovery otomatis tidak dapat memulihkan internet)'];
            $performed = true;
            $message = 'Internet offline — menunggu koneksi kembali';
        }

        $success = ($action['success'] ?? false);

        $this->recordAttempt($state, $success, $components);

        if ($success) {
            Log::info('SystemGuard recovery succeeded', [
                'action'     => $action['name'] ?? null,
                'message'    => $action['message'] ?? null,
            ]);
        } else {
            Log::warning('SystemGuard recovery attempted (no full success)', [
                'action'     => $action['name'] ?? null,
                'message'    => $action['message'] ?? null,
                'components' => array_keys($components),
            ]);
        }

        return [
            'attempted'  => true,
            'performed'  => $performed,
            'success'    => $success,
            'action'     => $action,
            'message'    => $message,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | PRIVATE
    |--------------------------------------------------------------------------
    */

    protected function recoverTunnel(array $tunnelCheck): array
    {
        $running = $this->tunnelRestarter->isRunning();

        if (!$running) {
            $result = $this->tunnelRestarter->start();

            return [
                'name'    => 'restart_cloudflared',
                'success' => $result['success'],
                'message' => $result['message'],
            ];
        }

        // Proses berjalan tapi hostname tidak resolve / degraded → restart
        $result = $this->tunnelRestarter->restart();

        return [
            'name'    => 'restart_cloudflared',
            'success' => $result['success'],
            'message' => $result['message'],
        ];
    }

    protected function backoffSeconds(int $base, int $failures): int
    {
        if ($failures <= 0) {
            return $base;
        }

        $max = (int) config('system-guard.daemon.backoff_max_seconds', 900);

        return min($base * (int) pow(2, $failures - 1), $max);
    }

    protected function recordAttempt(SystemGuardState $state, bool $success, array $components): void
    {
        $failures = $success ? 0 : $state->consecutive_failures + 1;
        $maxQuickFail = (int) config('system-guard.daemon.max_consecutive_quick_failures', 6);

        $status = 'READY';
        $message = 'Recovery ' . ($success ? 'succeeded' : 'not fully successful');

        if ($failures >= $maxQuickFail) {
            $status = 'ESCALATED';
            $message = 'Recovery stalled — menunggu inspeksi manual (max consecutive failures tercapai)';
        }

        $state->update([
            'status'               => $status,
            'message'              => $message,
            'last_checked_at'      => now(),
            'state_changed_at'     => $status === 'ESCALATED' ? now() : $state->state_changed_at,
            'consecutive_failures' => $failures,
            'detail'               => ['components' => array_keys($components)],
        ]);
    }
}
