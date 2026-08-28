<?php

namespace App\Console\Commands;

use App\Models\SystemGuard\IncidentLog;
use App\Models\SystemGuard\MonitorConfig;
use App\Services\SystemGuard\SystemGuard;
use Illuminate\Console\Command;

class SystemGuardCommand extends Command
{
    protected $signature = 'system:guard
        {--target= : Check specific target URL only}
        {--recover : Recover all open incidents}
        {--status : Show current status summary}
        {--report : Generate incident report}
        {--hours=24 : Report period in hours}
        {--full : Run full monitoring cycle}';

    protected $description = 'System Guard — Monitor, detect, recover, and verify system health';

    protected SystemGuard $systemGuard;

    public function __construct(SystemGuard $systemGuard)
    {
        parent::__construct();
        $this->systemGuard = $systemGuard;
    }

    public function handle(): int
    {
        if (!config('system-guard.enabled', true)) {
            $this->warn('System Guard is disabled.');

            return self::SUCCESS;
        }

        if ($this->option('status')) {
            return $this->showStatus();
        }

        if ($this->option('report')) {
            return $this->showReport();
        }

        if ($this->option('recover')) {
            return $this->recoverAll();
        }

        if ($this->option('target')) {
            return $this->checkTarget($this->option('target'));
        }

        return $this->runFullCycle();
    }

    protected function runFullCycle(): int
    {
        $this->info('System Guard — Full Monitoring Cycle');
        $this->newLine();

        $results = $this->systemGuard->runFullCycle();

        $this->table(
            ['Metric', 'Value'],
            [
                ['Targets Checked', $results['checked']],
                ['Healthy', $results['healthy']],
                ['Unhealthy', $results['unhealthy']],
                ['Recovered', $results['recovered']],
                ['Failed', $results['failed']],
            ]
        );

        if (!empty($results['incidents'])) {
            $this->warn('Active Incidents:');
            $this->newLine();

            $incidentRows = array_map(fn ($inc) => [
                $inc['incident_id'] ?? 'N/A',
                $inc['target'],
                $inc['error'] ?? 'Unknown',
                $inc['status'],
            ], $results['incidents']);

            $this->table(
                ['Incident ID', 'Target', 'Error', 'Status'],
                $incidentRows
            );
        } else {
            $this->info('No active incidents.');
        }

        return self::SUCCESS;
    }

    protected function checkTarget(string $targetUrl): int
    {
        $this->info("System Guard — Checking: {$targetUrl}");
        $this->newLine();

        $result = $this->systemGuard->checkTarget($targetUrl);

        if ($result === null) {
            $this->warn("No monitor config found for: {$targetUrl}");

            return self::FAILURE;
        }

        $this->table(
            ['Property', 'Value'],
            [
                ['Target', $result['target']],
                ['Healthy', $result['healthy'] ? 'YES' : 'NO'],
                ['Status', $result['status']],
                ['Error Type', $result['error_type'] ?? 'N/A'],
                ['Incident ID', $result['incident_id'] ?? 'None'],
                ['Recovered', $result['recovered'] ? 'YES' : 'NO'],
            ]
        );

        if ($result['verification']) {
            $this->info('Verification Result:');
            $this->table(
                ['Check', 'Result'],
                [
                    ['DNS', $result['verification']['dns_ok'] ? 'OK' : 'FAILED'],
                    ['Connection', $result['verification']['connection_ok'] ? 'OK' : 'FAILED'],
                    ['HTTP', $result['verification']['http_ok'] ? 'OK' : 'FAILED'],
                    ['Response Time', ($result['verification']['response_time_ms'] ?? 'N/A') . ' ms'],
                    ['Overall', $result['verification']['passed'] ? 'ONLINE' : 'GANGGUAN'],
                ]
            );
        }

        return $result['healthy'] ? self::SUCCESS : self::FAILURE;
    }

    protected function recoverAll(): int
    {
        $this->info('System Guard — Recovering All Open Incidents');
        $this->newLine();

        $results = $this->systemGuard->recoverAllOpen();

        if (empty($results)) {
            $this->info('No open incidents to recover.');

            return self::SUCCESS;
        }

        $rows = array_map(fn ($r) => [
            $r['incident']->incident_id,
            $r['incident']->target,
            $r['incident']->error_type,
            $r['incident']->status,
            $r['recovered'] ? 'YES' : 'NO',
            $r['incident']->retry_count,
        ], $results);

        $this->table(
            ['Incident ID', 'Target', 'Error', 'Status', 'Recovered', 'Retries'],
            $rows
        );

        return self::SUCCESS;
    }

    protected function showStatus(): int
    {
        $this->info('System Guard — Status Summary');
        $this->newLine();

        $summary = $this->systemGuard->getStatusSummary();

        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Targets', $summary['total']],
                ['Online', $summary['online']],
                ['Gangguan', $summary['gangguan']],
                ['Waspada', $summary['waspada']],
            ]
        );

        if (!empty($summary['targets'])) {
            $rows = array_map(fn ($t) => [
                $t['name'],
                $t['target'],
                $t['status'],
                $t['has_open_incident'] ? 'YES' : 'NO',
                $t['last_check'] ?? 'Never',
            ], $summary['targets']);

            $this->table(
                ['Name', 'Target', 'Status', 'Open Incident', 'Last Check'],
                $rows
            );
        }

        return self::SUCCESS;
    }

    protected function showReport(): int
    {
        $hours = (int) $this->option('hours');

        $this->info("System Guard — Incident Report (Last {$hours}h)");
        $this->newLine();

        $report = $this->systemGuard->getIncidentReport($hours);

        $this->table(
            ['Metric', 'Value'],
            [
                ['Period', $report['period_hours'] . ' hours'],
                ['Total Incidents', $report['total_incidents']],
                ['Resolved', $report['resolved']],
                ['Open', $report['open']],
                ['Recovering', $report['recovering']],
                ['Escalated', $report['escalated']],
                ['Failed', $report['failed']],
                ['Recovery Rate', $report['recovery_success_rate'] . '%'],
                ['Avg Recovery Time', $report['avg_recovery_time'] . 's'],
            ]
        );

        if (!empty($report['incidents'])) {
            $rows = array_map(fn ($i) => [
                $i['incident_id'],
                $i['target'],
                $i['error_type'],
                $i['severity'],
                $i['status'],
                $i['detected_at'] ?? 'N/A',
                ($i['duration_seconds'] ?? 'N/A') . 's',
            ], $report['incidents']);

            $this->table(
                ['ID', 'Target', 'Error', 'Severity', 'Status', 'Detected', 'Duration'],
                $rows
            );
        }

        return self::SUCCESS;
    }
}
