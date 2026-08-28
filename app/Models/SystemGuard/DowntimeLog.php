<?php

namespace App\Models\SystemGuard;

use Illuminate\Database\Eloquent\Model;

class DowntimeLog extends Model
{
    protected $table = 'downtime_logs';

    protected $fillable = [
        'component',
        'status',
        'error_type',
        'error_message',
        'started_at',
        'ended_at',
        'duration_seconds',
        'resolution_summary',
    ];

    protected $casts = [
        'started_at'       => 'datetime',
        'ended_at'         => 'datetime',
        'duration_seconds' => 'integer',
    ];

    protected $dates = [
        'started_at',
        'ended_at',
    ];

    public function isOpen(): bool
    {
        return $this->ended_at === null;
    }

    public function close(string $resolutionSummary = null): void
    {
        $started = $this->started_at;

        $this->update([
            'ended_at'           => now(),
            'duration_seconds'   => $started
                ? (int) $started->diffInSeconds(now())
                : null,
            'resolution_summary' => $resolutionSummary,
        ]);
    }

    public static function openFor(string $component): ?self
    {
        return static::where('component', $component)
            ->whereNull('ended_at')
            ->latest('started_at')
            ->first();
    }

    public static function totalDowntimeSeconds(?string $component = null, ?int $hours = null): int
    {
        $query = static::query();
        $total = 0;

        $logs = $query->get();

        foreach ($logs as $log) {
            if ($log->isOpen()) {
                $total += (int) $log->started_at->diffInSeconds(now());
            } else {
                $total += (int) ($log->duration_seconds ?? 0);
            }
        }

        return $total;
    }
}
