<?php

namespace App\Console\Commands;

use App\Services\SystemGuard\SystemGuardDaemon;
use Illuminate\Console\Command;
use Throwable;

class SystemGuardDaemonCommand extends Command
{
    protected $signature = 'system:guard:daemon
        {--once : Jalankan satu siklus saja lalu keluar}
        {--iterations= : Jalankan N siklus lalu keluar (untuk testing)}
        {--interval= : Override interval antar siklus (detik)}
        {--interval-fast : Gunakan interval cepat untuk testing (sleep pendek)}';

    protected $description = 'System Guard 24/7 daemon — monitoring & auto-recovery background supervisor';

    protected SystemGuardDaemon $daemon;

    public function __construct(SystemGuardDaemon $daemon)
    {
        parent::__construct();
        $this->daemon = $daemon;
    }

    public function handle(): int
    {
        if (!config('system-guard.enabled', true) || !config('system-guard.daemon.enabled', true)) {
            $this->warn('System Guard daemon is disabled.');

            return self::SUCCESS;
        }

        $interval = $this->resolveInterval();
        $iterations = $this->resolveIterations();

        if ($iterations === 1) {
            $this->tickWithCatch();

            return self::SUCCESS;
        }

        $this->loop($interval, $iterations);

        return self::SUCCESS;
    }

    /*
    |--------------------------------------------------------------------------
    | PRIVATE
    |--------------------------------------------------------------------------
    */

    protected function loop(int $interval, ?int $maxIterations): void
    {
        $this->info("System Guard daemon started. Interval: {$interval}s"
            . ($maxIterations ? " | max {$maxIterations} iterations" : '')
            . ' | pilih Ctrl+C untuk berhenti.');

        $iteration = 0;

        while (true) {
            $iteration++;

            $started = microtime(true);

            $this->tickWithCatch();

            if ($maxIterations !== null && $iteration >= $maxIterations) {
                $this->info('System Guard daemon reached max iterations. Exiting.');

                break;
            }

            $elapsed = microtime(true) - $started;
            $sleepFor = max(0, $interval - (int) round($elapsed));

            if ($sleepFor > 0) {
                sleep($sleepFor);
            }

            // Reset cycle untuk membuang memory laravel
            if (function_exists('gc_collect_cycles')) {
                gc_collect_cycles();
            }
        }
    }

    protected function tickWithCatch(): void
    {
        try {
            $summary = $this->daemon->tick();

            $this->renderTick($summary);
        } catch (Throwable $e) {
            $this->error('System Guard daemon tick error: ' . $e->getMessage());

            report($e);
        }
    }

    protected function renderTick(array $summary): void
    {
        foreach ($summary['components'] as $component => $check) {
            $label = str_pad(strtoupper($component), 10);
            $status = $check['status'];
            $this->line("  [{$status}] {$label} {$check['message']}");
        }

        if ($summary['recovery'] && $summary['recovery']['attempted']) {
            $this->line('  [RECOVERY] ' . ($summary['recovery']['message'] ?? 'no message'));
        }
    }

    protected function resolveInterval(): int
    {
        if ($this->option('interval') !== null && $this->option('interval') !== '') {
            return max(1, (int) $this->option('interval'));
        }

        if ($this->option('interval-fast')) {
            return 1;
        }

        return max(1, (int) config('system-guard.daemon.interval_seconds', 15));
    }

    protected function resolveIterations(): ?int
    {
        if ($this->option('once')) {
            return 1;
        }

        if ($this->option('iterations') !== null && $this->option('iterations') !== '') {
            return max(1, (int) $this->option('iterations'));
        }

        return null;
    }
}
