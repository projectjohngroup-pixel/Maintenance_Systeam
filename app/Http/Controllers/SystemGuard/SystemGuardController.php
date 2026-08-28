<?php

namespace App\Http\Controllers\SystemGuard;

use App\Http\Controllers\Controller;
use App\Models\SystemGuard\MonitorConfig;
use App\Models\SystemGuard\MonitorResult;
use App\Models\SystemGuard\IncidentLog;
use App\Models\SystemGuard\RecoveryLog;
use App\Models\SystemGuard\SystemGuardState;
use App\Models\SystemGuard\DowntimeLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SystemGuardController extends Controller
{
    public function dashboard()
    {
        $data = $this->getDashboardData();

        return view('system-guard.dashboard', $data);
    }


    public function apiStatus()
    {
        return response()->json($this->getDashboardData());
    }


    public function check()
    {
        $guard = app(\App\Services\SystemGuard\SystemGuard::class);

        $result = $guard->runFullCycle();

        return redirect()
            ->route('system-guard.dashboard')
            ->with('success', "Monitoring selesai: {$result['checked']} target diperiksa, {$result['healthy']} sehat, {$result['unhealthy']} bermasalah.");
    }


    public function apiPoll()
    {
        return response()->json([
            'timestamp' => now()->toIso8601String(),
            'summary' => $this->getSummaryCards(),
            'targets' => $this->getTargetList(),
            'liveFeed' => $this->getLiveIncidentFeed(10),
            'recovery' => $this->getRecoveryStatus(),
            'components' => $this->getComponentsStatus(),
            'daemon' => $this->getDaemonStatus(),
            'downtime' => $this->getDowntimeSummary(),
        ]);
    }


    public function incidentDetail($incidentId)
    {
        $incident = IncidentLog::with('monitorConfig')
            ->where('incident_id', $incidentId)
            ->firstOrFail();

        $recoveryLogs = RecoveryLog::where('incident_id', $incidentId)
            ->orderBy('started_at')
            ->get();

        return view('system-guard.incident-detail', [
            'incident' => $incident,
            'recoveryLogs' => $recoveryLogs,
        ]);
    }


    private function getDashboardData(): array
    {
        $period = request()->input('period', '24h');

        $header = $this->getHeaderData();
        $summary = $this->getSummaryCards();
        $health = $this->getSystemHealth();
        $rt = $this->getResponseTimeTrend($period);
        $it = $this->getIncidentTrend($period);
        $ec = $this->getErrorCategories($period);
        $feed = $this->getLiveIncidentFeed(15);
        $recovery = $this->getRecoveryStatus();
        $targets = $this->getTargetList();
        $actions = $this->getQuickActions();
        $components = $this->getComponentsStatus();
        $daemon = $this->getDaemonStatus();
        $downtime = $this->getDowntimeSummary();

        return array_merge($header, $summary, $health, [
            'rtLabels' => $rt['labels'],
            'rtValues' => $rt['values'],
            'rtCurrent' => $rt['current'],
            'rtAverage' => $rt['average'],
            'rtMinimum' => $rt['minimum'],
            'rtMaximum' => $rt['maximum'],
            'itLabels' => $it['labels'],
            'itSeries' => $it['series'],
            'ecLabels' => $ec['labels'],
            'ecValues' => $ec['values'],
            'ecColors' => $ec['colors'],
            'liveFeed' => $feed,
            'recentRecoveries' => $recovery['recentRecoveries'],
            'totalAttempts' => $recovery['totalAttempts'],
            'successful' => $recovery['successful'],
            'failed' => $recovery['failed'],
            'skipped' => $recovery['skipped'],
            'successRate' => $recovery['successRate'],
            'activeRecoveries' => $recovery['activeRecoveries'],
            'activeIncidentsFromRecovery' => $recovery['activeIncidents'],
            'targets' => $targets,
            'quickActions' => $actions,
            'period' => $period,
            'components' => $components,
            'daemon' => $daemon,
            'downtime' => $downtime,
        ]);
    }


    private function getHeaderData(): array
    {
        $totalTargets = MonitorConfig::count();

        $hasIncidents = IncidentLog::open()->exists();

        $hasGangguan = MonitorResult::query()
            ->where('severity', 'critical')
            ->where('created_at', '>=', now()->subHour())
            ->exists();

        $status = 'CONNECTED';
        $statusColor = 'green';

        if ($hasGangguan) {
            $status = 'DEGRADED';
            $statusColor = 'yellow';
        }

        if ($totalTargets === 0) {
            $status = 'DISCONNECTED';
            $statusColor = 'red';
        }

        $latestResult = MonitorResult::query()
            ->orderByDesc('created_at')
            ->first();

        $lastUpdate = $latestResult
            ? $latestResult->created_at->format('H:i:s')
            : '—';

        $latency = $latestResult && $latestResult->response_time_ms
            ? $latestResult->response_time_ms . ' ms'
            : '—';

        return [
            'headerStatus' => $status,
            'headerStatusColor' => $statusColor,
            'lastUpdate' => $lastUpdate,
            'latency' => $latency,
            'totalTargets' => $totalTargets,
        ];
    }


    private function getSummaryCards(): array
    {
        $totalTargets = MonitorConfig::count();
        $activeTargets = MonitorConfig::active()->count();

        $latestPerTarget = $this->getLatestResultsPerTarget();

        $online = 0;
        $perhatian = 0;
        $gangguan = 0;

        foreach ($latestPerTarget as $result) {
            if ($result->severity === 'normal') {
                $online++;
            } elseif (in_array($result->severity, ['medium', 'warning'])) {
                $perhatian++;
            } else {
                $gangguan++;
            }
        }

        $activeIncidents = IncidentLog::open()->count();

        $recoveryCount = IncidentLog::query()
            ->where('status', 'RECOVERING')
            ->count();

        $totalResults = $latestPerTarget->count();

        $availability = $totalResults > 0
            ? round($online / $totalResults * 100, 1)
            : 0;

        $avgResponseTime = MonitorResult::query()
            ->whereNotNull('response_time_ms')
            ->where('created_at', '>=', now()->subHour())
            ->avg('response_time_ms');

        return [
            'totalTargets' => $totalTargets,
            'activeTargets' => $activeTargets,
            'online' => $online,
            'perhatian' => $perhatian,
            'gangguan' => $gangguan,
            'activeIncidents' => $activeIncidents,
            'recoveryCount' => $recoveryCount,
            'availability' => $availability,
            'avgResponseTime' => $avgResponseTime
                ? round($avgResponseTime) . ' ms'
                : '—',
        ];
    }


    private function getSystemHealth(): array
    {
        $latestPerTarget = $this->getLatestResultsPerTarget();

        $total = $latestPerTarget->count();

        if ($total === 0) {
            return [
                'healthPercent' => 0,
                'healthy' => 0,
                'attention' => 0,
                'critical' => 0,
                'total' => 0,
                'label' => 'Belum ada data',
            ];
        }

        $healthy = $latestPerTarget
            ->where('severity', 'normal')
            ->count();

        $attention = $latestPerTarget
            ->filter(fn ($r) => in_array($r->severity, ['medium', 'warning']))
            ->count();

        $critical = $total - $healthy - $attention;

        $healthPercent = (int) round($healthy / $total * 100);

        return [
            'healthPercent' => $healthPercent,
            'healthy' => $healthy,
            'attention' => $attention,
            'critical' => $critical,
            'total' => $total,
            'label' => $healthPercent >= 80 ? 'Sehat' : ($healthPercent >= 50 ? 'Perhatian' : 'Kritis'),
        ];
    }


    private function getResponseTimeTrend(string $period = '24h'): array
    {
        $hours = $this->periodToHours($period);

        $cutoff = now()->subHours($hours);

        $results = MonitorResult::query()
            ->whereNotNull('response_time_ms')
            ->where('created_at', '>=', $cutoff)
            ->orderBy('created_at')
            ->get(['created_at', 'response_time_ms']);

        if ($results->isEmpty()) {
            return [
                'labels' => [],
                'values' => [],
                'current' => '—',
                'average' => '—',
                'minimum' => '—',
                'maximum' => '—',
            ];
        }

        $bucketMinutes = $this->getBucketMinutes($hours);

        $buckets = [];

        foreach ($results as $r) {
            $key = $r->created_at->format('Y-m-d H:') .
                str_pad(
                    (int) floor($r->created_at->minute / $bucketMinutes) * $bucketMinutes,
                    2,
                    '0',
                    STR_PAD_LEFT
                );

            if (!isset($buckets[$key])) {
                $buckets[$key] = ['sum' => 0, 'count' => 0];
            }

            $buckets[$key]['sum'] += $r->response_time_ms;
            $buckets[$key]['count']++;
        }

        ksort($buckets);

        $labels = [];
        $values = [];

        foreach ($buckets as $key => $bucket) {
            $labels[] = Carbon::parse($key)->format(
                $hours <= 48 ? 'H:i' : 'd M'
            );
            $values[] = (int) round($bucket['sum'] / $bucket['count']);
        }

        return [
            'labels' => $labels,
            'values' => $values,
            'current' => $results->last()->response_time_ms . ' ms',
            'average' => round($results->avg('response_time_ms')) . ' ms',
            'minimum' => $results->min('response_time_ms') . ' ms',
            'maximum' => $results->max('response_time_ms') . ' ms',
        ];
    }


    private function getIncidentTrend(string $period = '24h'): array
    {
        $hours = $this->periodToHours($period);

        $incidents = IncidentLog::query()
            ->where('detected_at', '>=', now()->subHours($hours))
            ->orderBy('detected_at')
            ->get(['detected_at', 'severity']);

        if ($incidents->isEmpty()) {
            return ['labels' => [], 'series' => []];
        }

        $severities = ['normal' => [], 'perhatian' => [], 'gangguan' => [], 'serius' => []];

        $days = max(1, (int) ceil($hours / 24));

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $label = $date->format('d M');
            $dayStart = $date->copy()->startOfDay();
            $dayEnd = $date->copy()->endOfDay();

            $dayIncidents = $incidents->filter(
                fn ($inc) => $inc->detected_at->between($dayStart, $dayEnd)
            );

            foreach ($severities as $sev => &$counts) {
                $counts[] = $dayIncidents
                    ->filter(fn ($inc) => $this->mapSeverity($inc->severity) === $sev)
                    ->count();
            }
            unset($counts);

            $labels[] = $label;
        }

        return [
            'labels' => $labels ?? [],
            'series' => [
                ['name' => 'Normal', 'data' => $severities['normal']],
                ['name' => 'Perhatian', 'data' => $severities['perhatian']],
                ['name' => 'Gangguan', 'data' => $severities['gangguan']],
                ['name' => 'Serius', 'data' => $severities['serius']],
            ],
        ];
    }


    private function getErrorCategories(string $period = '24h'): array
    {
        $hours = $this->periodToHours($period);

        $categories = IncidentLog::query()
            ->where('detected_at', '>=', now()->subHours($hours))
            ->selectRaw('error_category as category, COUNT(*) as total')
            ->groupBy('error_category')
            ->orderByDesc('total')
            ->pluck('total', 'category');

        if ($categories->isEmpty()) {
            return ['labels' => [], 'values' => [], 'colors' => []];
        }

        $palette = [
            '#ef4444', '#f97316', '#eab308', '#22c55e',
            '#06b6d4', '#3b82f6', '#8b5cf6', '#ec4899',
            '#64748b', '#14b8a6', '#a855f7', '#f43f5e',
        ];

        return [
            'labels' => $categories->keys()->values(),
            'values' => $categories->values(),
            'colors' => $categories->keys()->values()->map(
                fn ($_, $i) => $palette[$i % count($palette)]
            )->values(),
        ];
    }


    private function getLiveIncidentFeed(int $limit = 15): array
    {
        return IncidentLog::with('monitorConfig')
            ->orderByDesc('detected_at')
            ->limit($limit)
            ->get()
            ->map(fn ($inc) => [
                'incident_id' => $inc->incident_id,
                'target' => $inc->monitorConfig?->name ?? $inc->target,
                'error_type' => $inc->error_type,
                'error_message' => $inc->error_message,
                'severity' => $inc->severity,
                'status' => $inc->status,
                'detected_at' => $inc->detected_at->format('H:i:s'),
                'detected_date' => $inc->detected_at->format('d M Y'),
            ])
            ->toArray();
    }


    private function getRecoveryStatus(): array
    {
        $recoveryStats = RecoveryLog::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $totalAttempts = $recoveryStats->sum();

        $successful = $recoveryStats->get('completed', 0);

        $successRate = $totalAttempts > 0
            ? round($successful / $totalAttempts * 100)
            : 0;

        $activeRecoveries = RecoveryLog::query()
            ->where('status', 'running')
            ->count();

        $activeIncidents = IncidentLog::query()
            ->where('status', 'RECOVERING')
            ->count();

        $recentRecoveries = RecoveryLog::with('monitorConfig')
            ->orderByDesc('started_at')
            ->limit(5)
            ->get()
            ->map(fn ($log) => [
                'action' => $log->action,
                'status' => $log->status,
                'target' => $log->monitorConfig?->name ?? '—',
                'message' => $log->result_message,
                'duration' => $log->durationLabel(),
                'started_at' => $log->started_at->format('H:i:s'),
            ])
            ->toArray();

        return [
            'totalAttempts' => $totalAttempts,
            'successful' => $successful,
            'failed' => $recoveryStats->get('failed', 0),
            'skipped' => $recoveryStats->get('skipped', 0),
            'successRate' => $successRate,
            'activeRecoveries' => $activeRecoveries,
            'activeIncidents' => $activeIncidents,
            'recentRecoveries' => $recentRecoveries,
            'byStatus' => $recoveryStats->toArray(),
        ];
    }


    private function getTargetList(): array
    {
        return MonitorConfig::query()
            ->get()
            ->map(function ($config) {
                $result = $config->latest_result;

                $status = 'OFFLINE';
                $statusColor = 'red';

                if ($result) {
                    if ($result->severity === 'normal') {
                        $status = 'ONLINE';
                        $statusColor = 'green';
                    } elseif (in_array($result->severity, ['medium', 'warning'])) {
                        $status = 'PERHATIAN';
                        $statusColor = 'yellow';
                    } else {
                        $status = 'GANGGUAN';
                        $statusColor = 'red';
                    }
                }

                $hasOpenIncident = $config->hasOpenIncident();

                return [
                    'id' => $config->id,
                    'name' => $config->name,
                    'target_url' => $config->target_url,
                    'type' => $config->type,
                    'is_active' => $config->is_active,
                    'status' => $status,
                    'statusColor' => $statusColor,
                    'response_time' => $result && $result->response_time_ms
                        ? $result->response_time_ms . ' ms'
                        : '—',
                    'last_check' => $result
                        ? $result->created_at->diffForHumans()
                        : 'Belum dicek',
                    'http_status' => $result?->http_status_code ?? '—',
                    'error_type' => $result?->error_type ?? null,
                    'has_incident' => $hasOpenIncident,
                ];
            })
            ->toArray();
    }


    private function getComponentsStatus(): array
    {
        $definitions = [
            'internet' => ['label' => 'Internet', 'desc' => 'Koneksi internet lokal'],
            'tunnel'   => ['label' => 'Cloudflare Tunnel', 'desc' => 'Proses cloudflared & hostname'],
            'origin'   => ['label' => 'Maintenance System', 'desc' => 'Server origin (HTTP)'],
        ];

        $result = [];

        foreach ($definitions as $key => $def) {
            $state = SystemGuardState::forComponent($key);

            $status = $state->status;
            $color = match ($status) {
                'ONLINE', 'RUNNING' => 'green',
                'DEGRADED'          => 'yellow',
                'OFFLINE'           => 'red',
                default             => 'gray',
            };

            $result[$key] = [
                'component'  => $key,
                'label'      => $def['label'],
                'desc'       => $def['desc'],
                'status'     => $status,
                'color'      => $color,
                'message'    => $state->message ?? 'Belum dicek',
                'error_type' => $state->error_type,
                'last_check' => $state->last_checked_at?->diffForHumans() ?? 'Belum dicek',
            ];
        }

        return $result;
    }


    private function getDaemonStatus(): array
    {
        $state = SystemGuardState::forComponent('daemon');

        $maxAge = (int) config('system-guard.daemon.max_heartbeat_age', 180);
        $running = $state->status === 'RUNNING'
            && $state->last_checked_at
            && $state->last_checked_at->diffInSeconds(now()) <= $maxAge;

        return [
            'status'  => $running ? 'RUNNING' : 'STOPPED',
            'color'   => $running ? 'green' : 'red',
            'message' => $running
                ? 'Daemon aktif (24/7)'
                : 'Daemon tidak terdeteksi — jalankan system:guard:install',
            'last_seen' => $state->last_checked_at?->diffForHumans() ?? 'Belum pernah',
            'consecutive_failures' => $state->consecutive_failures,
            'recovery_state' => SystemGuardState::forComponent('recovery')->status,
        ];
    }


    private function getDowntimeSummary(): array
    {
        $period = request()->input('period', '24h');
        $hours = $this->periodToHours($period);

        $cutoff = now()->subHours($hours);

        $summary = [];

        foreach (['internet', 'tunnel', 'origin'] as $component) {
            $total = 0;
            $events = 0;

            DowntimeLog::where('component', $component)
                ->where('started_at', '>=', $cutoff)
                ->get()
                ->each(function ($log) use (&$total, &$events) {
                    $events++;
                    $total += $log->isOpen()
                        ? (int) $log->started_at->diffInSeconds(now())
                        : (int) ($log->duration_seconds ?? 0);
                });

            $summary[$component] = [
                'total_seconds' => $total,
                'label'         => $this->formatDuration($total),
                'events'        => $events,
            ];
        }

        return $summary;
    }


    private function formatDuration(int $seconds): string
    {
        if ($seconds < 60) {
            return $seconds . ' dtk';
        }

        if ($seconds < 3600) {
            return round($seconds / 60, 1) . ' mnt';
        }

        return round($seconds / 3600, 2) . ' jam';
    }


    private function getQuickActions(): array
    {
        return [
            [
                'label' => 'System Check',
                'description' => 'Jalankan monitoring cycle',
                'route' => 'system-guard.check',
                'icon' => 'refresh',
            ],
            [
                'label' => 'View Incidents',
                'description' => 'Lihat semua insiden',
                'route' => 'system-guard.dashboard',
                'icon' => 'alert',
            ],
            [
                'label' => 'Laporan',
                'description' => 'Laporan monitoring',
                'route' => null,
                'icon' => 'report',
            ],
        ];
    }


    private function getLatestResultsPerTarget()
    {
        $subQuery = MonitorResult::query()
            ->selectRaw('monitor_config_id, MAX(id) as max_id')
            ->groupBy('monitor_config_id');

        return MonitorResult::query()
            ->join(
                DB::raw('(' . $subQuery->toSql() . ') as latest'),
                function ($join) use ($subQuery) {
                    $join->on('monitor_results.id', '=', 'latest.max_id')
                        ->addBinding($subQuery->getBindings(), 'join');
                }
            )
            ->get();
    }


    private function periodToHours(string $period): int
    {
        return match ($period) {
            '24h' => 24,
            '7d' => 168,
            '30d' => 720,
            '90d' => 2160,
            default => 24,
        };
    }


    private function getBucketMinutes(int $hours): int
    {
        if ($hours <= 24) {
            return 60;
        }

        if ($hours <= 168) {
            return 360;
        }

        return 1440;
    }


    private function mapSeverity(string $severity): string
    {
        return match (strtolower($severity)) {
            'critical', 'high', 'gangguan', 'serius' => 'serius',
            'medium', 'warning', 'perhatian' => 'perhatian',
            'normal', 'low', 'ok' => 'normal',
            default => 'normal',
        };
    }
}
