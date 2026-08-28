<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SystemGuardInstallCommand extends Command
{
    protected $signature = 'system:guard:install {--uninstall : Hapus scheduled task daemon}';

    protected $description = 'Daftarkan System Guard daemon sebagai Windows Scheduled Task (auto-start saat boot + restart on failure, berjalan saat logout)';

    public function handle(): int
    {
        if (PHP_OS_FAMILY !== 'Windows') {
            $this->warn('Perintah ini khusus Windows. Gunakan `system:guard:daemon` dengan supervisor (systemd/cron) pada platform lain.');

            return self::SUCCESS;
        }

        if ($this->option('uninstall')) {
            return $this->uninstall();
        }

        return $this->install();
    }

    protected function install(): int
    {
        $taskName = 'SystemGuardDaemon';

        $xmlPath = $this->writeTaskXml($taskName);

        if (!$xmlPath) {
            $this->error('Gagal membuat file XML Task Scheduler.');

            return self::FAILURE;
        }

        $this->line('Installing Windows Scheduled Task: ' . $taskName);
        $this->line('  XML  : ' . $xmlPath);
        $this->line('  User : SYSTEM (jalan tanpa login)');

        $cmd = "schtasks /Create /F /TN \"{$taskName}\" /XML \"{$xmlPath}\"";

        $output = [];
        $exitCode = 0;
        exec($cmd . ' 2>&1', $output, $exitCode);

        if ($exitCode !== 0) {
            $this->error('Gagal mendaftarkan scheduled task.');
            $this->error(implode("\n", $output));

            @unlink($xmlPath);

            return self::FAILURE;
        }

        @unlink($xmlPath);

        $this->info('Scheduled task berhasil didaftarkan. Daemon akan otomatis:');
        $this->line('  - start saat boot (tanpa perlu login)');
        $this->line('  - restart otomatis jika gagal (RestartOnFailure)');
        $this->line('  - berjalan meski user logout / browser ditutup');

        return self::SUCCESS;
    }

    protected function uninstall(): int
    {
        $taskName = 'SystemGuardDaemon';

        $this->line('Menghapus Windows Scheduled Task: ' . $taskName);

        $output = [];
        $exitCode = 0;
        exec("schtasks /Delete /TN \"{$taskName}\" /F 2>&1", $output, $exitCode);

        if ($exitCode !== 0) {
            $matches = implode("\n", $output);
            if (str_contains(strtolower($matches), 'tidak ditemukan')
                || str_contains(strtolower($matches), 'not found')
                || str_contains(strtolower($matches), 'does not exist')) {
                $this->warn('Scheduled task tidak ditemukan (mungkin sudah dihapus).');

                return self::SUCCESS;
            }

            $this->error(implode("\n", $output));

            return self::FAILURE;
        }

        $this->info('Scheduled task berhasil dihapus.');

        return self::SUCCESS;
    }

    /*
    |--------------------------------------------------------------------------
    | XML BUILDING
    |--------------------------------------------------------------------------
    */

    protected function writeTaskXml(string $taskName): ?string
    {
        $action = $this->taskArguments();
        $bin = $this->findPhpBinary();

        $xml = <<<XML
<?xml version="1.0" encoding="UTF-16"?>
<Task version="1.2" xmlns="http://schemas.microsoft.com/windows/2004/02/mit/task">
  <RegistrationInfo>
    <Description>System Guard 24/7 daemon - monitoring &amp; auto-recovery Cloudflare Tunnel &amp; Maintenance System</Description>
  </RegistrationInfo>
  <Triggers>
    <BootTrigger>
      <Enabled>true</Enabled>
    </BootTrigger>
  </Triggers>
  <Principals>
    <Principal id="Author">
      <RunLevel>HighestAvailable</RunLevel>
      <UserId>S-1-5-18</UserId>
    </Principal>
  </Principals>
  <Settings>
    <MultipleInstancesPolicy>IgnoreNew</MultipleInstancesPolicy>
    <DisallowStartIfOnBatteries>false</DisallowStartIfOnBatteries>
    <StopIfGoingOnBatteries>false</StopIfGoingOnBatteries>
    <AllowHardTerminate>true</AllowHardTerminate>
    <StartWhenAvailable>true</StartWhenAvailable>
    <RunOnlyIfNetworkAvailable>false</RunOnlyIfNetworkAvailable>
    <IdleSettings>
      <StopOnIdleEnd>false</StopOnIdleEnd>
      <RestartOnIdle>false</RestartOnIdle>
    </IdleSettings>
    <AllowStartOnDemand>true</AllowStartOnDemand>
    <Enabled>true</Enabled>
    <Hidden>false</Hidden>
    <RunOnlyIfIdle>false</RunOnlyIfIdle>
    <WakeToRun>false</WakeToRun>
    <ExecutionTimeLimit>PT0S</ExecutionTimeLimit>
    <Priority>7</Priority>
    <RestartOnFailure>
      <Interval>PT1M</Interval>
      <Count>3</Count>
    </RestartOnFailure>
  </Settings>
  <Actions Context="Author">
    <Exec>
      <Command>{$this->escapeXmlCommand($bin)}</Command>
      <Arguments>{$action}</Arguments>
      <WorkingDirectory>{$this->escapeXmlText(base_path())}</WorkingDirectory>
    </Exec>
  </Actions>
</Task>
XML;

        $dir = storage_path('app/system-guard');

        if (!is_dir($dir)) {
            @mkdir($dir, 0777, true);
        }

        $path = $dir . '\\' . $taskName . '.xml';

        if (file_put_contents($path, $xml) === false) {
            return null;
        }

        return $path;
    }

    protected function taskArguments(): string
    {
        $base = $this->escapeXmlText(base_path());

        return '"' . $base . '\\artisan" system:guard:daemon';
    }

    protected function escapeXmlText(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_XML1, 'UTF-8');
    }

    protected function escapeXmlCommand(string $path): string
    {
        return htmlspecialchars($path, ENT_QUOTES | ENT_XML1, 'UTF-8');
    }

    protected function findPhpBinary(): ?string
    {
        $candidates = [];

        $laragonPath = 'C:\\laragon\\bin\\php';
        if (is_dir($laragonPath)) {
            foreach (glob($laragonPath . '\\php-*\\php.exe') as $p) {
                if (is_file($p)) {
                    $candidates[] = $p;
                }
            }
        }

        $candidates[] = PHP_BINARY;
        $candidates[] = 'C:\\laragon\\bin\\php\\php.exe';

        foreach ($candidates as $candidate) {
            if ($candidate && is_file($candidate)) {
                return $candidate;
            }
        }

        return 'php';
    }
}
