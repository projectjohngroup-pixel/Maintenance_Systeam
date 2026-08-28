<?php

namespace App\Services\SystemGuard;

use App\Models\SystemGuard\SystemGuardState;
use Illuminate\Support\Facades\Log;

class SystemGuardDaemon
{
    /*
    |--------------------------------------------------------------------------
    | SYSTEM GUARD DAEMON (STEP 4)
    |--------------------------------------------------------------------------
    |
    | Logika inti supervisor 24/7. Memisahkan pengecekan dari loop tak-terbatas
    | agar mudah diuji. Daemon:
    |   1. Heartbeat komponen 'daemon' (menandakan masih hidup)
    |   2. Pengecekan komponen (internet / tunnel / origin)
    |   3. Quick recovery saat komponen down (dengan cooldown & backoff)
    |   4. Verifikasi
    |
    | Command `system:guard:daemon` menjalankan loop yang memanggil tick() ini.
    |
    */

    protected InfrastructureMonitor $infrastructureMonitor;
    protected TunnelRestarter $tunnelRestarter;
    protected RecoveryManager $recoveryManager;

    public function __construct(
        InfrastructureMonitor $infrastructureMonitor,
        TunnelRestarter $tunnelRestarter,
        RecoveryManager $recoveryManager
    ) {
        $this->infrastructureMonitor = $infrastructureMonitor;
        $this->tunnelRestarter = $tunnelRestarter;
        $this->recoveryManager = $recoveryManager;
    }

    /**
     * Jalankan satu siklus monitoring + recovery (satu "tick").
     */
    public function tick(): array
    {
        $summary = [
            'components' => [],
            'recovery' => null,
            'heartbeat' => null,
        ];

        // 1) Heartbeat
        $summary['heartbeat'] = $this->heartbeat();

        // 2) Component monitoring
        $components = $this->infrastructureMonitor->checkAll();
        $summary['components'] = $components;

        // 3) Recovery (jika ada komponen down kantunnel/internet/origin)
        $summary['recovery'] = $this->maybeRecover($components);

        return $summary;
    }

    /**
     * Kirim heartbeat daemon ke states table.
     */
    public function heartbeat(): array
    {
        $state = SystemGuardState::forComponent('daemon');

        $state->update([
            'status'          => 'RUNNING',
            'message'         => 'System Guard daemon active',
            'last_checked_at' => now(),
            'state_changed_at' => $state->status !== 'RUNNING'
                ? now()
                : $state->state_changed_at,
        ]);

        return [
            'component'  => 'daemon',
            'status'     => 'RUNNING',
            'checked_at' => now()->toIso8601String(),
        ];
    }

    /**
     * Tentukan apakah perlu recovery & jalankan (dengan cooldown/backoff).
     */
    protected function maybeRecover(array $components): ?array
    {
        $recoveryEnabled = config('system-guard.auto_recovery_enabled', false);

        if (!$recoveryEnabled) {
            return null;
        }

        $offline = [];

        foreach ($components as $component => $check) {
            if (!$check['online']) {
                $offline[] = $component;
            }
        }

        if (empty($offline)) {
            return null;
        }

        if (!$this->recoveryManager->isCooldownReady()) {
            return [
                'attempted'  => false,
                'reason'     => 'cooldown',
                'components' => $offline,
            ];
        }

        return $this->recoveryManager->attempt($components);
    }
}
