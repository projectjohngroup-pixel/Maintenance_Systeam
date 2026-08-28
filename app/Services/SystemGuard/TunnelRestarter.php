<?php

namespace App\Services\SystemGuard;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;

class TunnelRestarter
{
    /*
    |--------------------------------------------------------------------------
    | TUNNEL RESTARTER (STEP 4)
    |--------------------------------------------------------------------------
    |
    | Mengelola proses cloudflared dengan AMAN:
    |   - Hanya menjalankan command template dari config (FIXED, allowlisted).
    |   - TIDAK pernah menerima input user bebas sebagai perintah.
    |   - Placeholder yang diizinkan: {cloudflared_bin}, {tunnel_arg}, {log_dir}.
    |
    */

    public function isRunning(): bool
    {
        $processName = config('system-guard.cloudflared.process_name', 'cloudflared');

        try {
            if (PHP_OS_FAMILY === 'Windows') {
                $output = Process::run("tasklist /FI \"IMAGENAME eq {$processName}.exe\" /NH")->output();
                return str_contains(strtolower($output), strtolower($processName . '.exe'));
            }

            return Process::run("pgrep -x {$processName} >/dev/null 2>&1")->exitCode() === 0;
        } catch (\Throwable $e) {
            Log::error('SystemGuard tunnel process check failed', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Mulai cloudflared dengan command template config.
     */
    public function start(): array
    {
        $command = $this->buildStartCommand();

        if (!$command) {
            return [
                'success' => false,
                'message' => 'No cloudflared start command configured',
            ];
        }

        return $this->executeAsync($command, 'start');
    }

    /**
     * Restart cloudflared (kill existing lalu start).
     */
    public function restart(): array
    {
        $processName = config('system-guard.cloudflared.process_name', 'cloudflared');

        $killResult = $this->kill();

        if (!$killResult['killed'] && $killResult['running']) {
            return [
                'success' => false,
                'message' => 'Unable to terminate existing cloudflared process',
            ];
        }

        return $this->start();
    }

    /**
     * Hentikan proses cloudflared yang berjalan.
     */
    public function kill(): array
    {
        $processName = config('system-guard.cloudflared.process_name', 'cloudflared');

        $running = $this->isRunning();

        if (!$running) {
            return ['killed' => true, 'running' => false, 'message' => 'No running process'];
        }

        try {
            if (PHP_OS_FAMILY === 'Windows') {
                $result = Process::run("taskkill /IM {$processName}.exe /F");
                $killed = $result->exitCode() === 0;
            } else {
                $result = Process::run("pkill -x {$processName}");
                $killed = $result->exitCode() === 0;
            }
        } catch (\Throwable $e) {
            Log::error('SystemGuard tunnel kill failed', ['error' => $e->getMessage()]);
            $killed = false;
        }

        return [
            'killed'  => $killed,
            'running' => $killed ? false : $running,
            'message' => $killed ? 'Process terminated' : 'Failed to terminate process',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | COMMAND BUILDING (allowlisted, fixed template)
    |--------------------------------------------------------------------------
    */

    public function buildStartCommand(): ?string
    {
        $template = config('system-guard.cloudflared.start_command', '');

        if ($template !== '') {
            return $this->expandTemplate($template);
        }

        // Fallback default (hanya quick tunnel dengan argumen predefined)
        $bin = $this->quotedBinaryPath();
        $tunnelArg = $this->defaultTunnelArg();

        return $this->buildDefaultCommand($bin, $tunnelArg);
    }

    public function buildRestartCommand(): ?string
    {
        $template = config('system-guard.cloudflared.restart_command', '');

        if ($template !== '') {
            return $this->expandTemplate($template);
        }

        return $this->buildStartCommand();
    }

    /*
    |--------------------------------------------------------------------------
    | PRIVATE
    |--------------------------------------------------------------------------
    */

    protected function buildDefaultCommand(string $bin, string $tunnelArg): string
    {
        // Gunakan START (Windows) agar berjalan di background terpisah dari daemon
        if (PHP_OS_FAMILY === 'Windows') {
            return "start \"cloudflared-tunnel\" /B {$bin} {$tunnelArg}";
        }

        $logDir = $this->quotedLogDir();

        return "nohup {$bin} {$tunnelArg} >> {$logDir}/cloudflared.log 2>&1 &";
    }

    protected function defaultTunnelArg(): string
    {
        $config = config('system-guard.cloudflared');

        $tunnelId = trim($config['tunnel_id'] ?? '');
        $configPath = trim($config['config_path'] ?? '');
        $quick = (bool) ($config['quick_tunnel'] ?? false);

        if ($tunnelId !== '' && $configPath !== '') {
            return "tunnel --config " . $this->quote($configPath) . " run " . $this->quote($tunnelId);
        }

        if ($tunnelId !== '') {
            return "tunnel run " . $this->quote($tunnelId);
        }

        if ($quick) {
            return "tunnel --url http://localhost:80";
        }

        // Tidak ada konfigurasi tunnel yang valid → fallback aman dengan flag help
        return "--help";
    }

    protected function expandTemplate(string $template): string
    {
        $replacements = [
            '{cloudflared_bin}' => $this->quotedBinaryPath(),
            '{log_dir}'         => $this->quotedLogDir(),
            '{tunnel_arg}'      => $this->defaultTunnelArg(),
        ];

        return strtr($template, $replacements);
    }

    protected function quotedBinaryPath(): string
    {
        return '"' . rtrim(config('system-guard.cloudflared.binary_path', 'cloudflared'), ' \\') . '"';
    }

    protected function quotedLogDir(): string
    {
        return '"' . rtrim(config('system-guard.cloudflared.log_dir', storage_path('logs/system-guard')), ' \\') . '"';
    }

    protected function quote(string $value): string
    {
        return '"' . $value . '"';
    }

    protected function executeAsync(string $command, string $action): array
    {
        try {
            if (PHP_OS_FAMILY === 'Windows') {
                $result = Process::run($command);
                $exitCode = $result->exitCode();
                $output = trim($result->output() . $result->errorOutput());
            } else {
                $result = Process::run($command);
                $exitCode = $result->exitCode();
                $output = trim($result->output() . $result->errorOutput());
            }

            Log::info('SystemGuard cloudflared ' . $action, [
                'exit_code' => $exitCode,
                'output'    => $output,
            ]);

            return [
                'success' => $exitCode === 0,
                'message' => $output !== '' ? $output : ($exitCode === 0 ? $action . ' OK' : $action . ' failed'),
                'exit_code' => $exitCode,
            ];
        } catch (\Throwable $e) {
            Log::error('SystemGuard cloudflared ' . $action . ' failed', ['error' => $e->getMessage()]);

            return [
                'success' => false,
                'message' => $action . ' failed: ' . $e->getMessage(),
            ];
        }
    }
}
