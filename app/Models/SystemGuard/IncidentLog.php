<?php

namespace App\Models\SystemGuard;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class IncidentLog extends Model
{
    protected $table = 'incident_logs';

    protected $fillable = [
        'incident_id',
        'monitor_config_id',
        'target',
        'error_category',
        'error_type',
        'error_message',
        'severity',
        'status',
        'detected_at',
        'recovered_at',
        'duration_seconds',
        'retry_count',
        'recovery_summary',
    ];

    protected $casts = [
        'detected_at'       => 'datetime',
        'recovered_at'      => 'datetime',
        'duration_seconds'  => 'integer',
        'retry_count'       => 'integer',
        'recovery_summary'  => 'array',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    public function monitorConfig(): BelongsTo
    {
        return $this->belongsTo(MonitorConfig::class, 'monitor_config_id');
    }

    public function recoveryLogs(): HasMany
    {
        return $this->hasMany(RecoveryLog::class, 'incident_id', 'incident_id');
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    public function scopeOpen($query)
    {
        return $query->whereIn('status', ['OPEN', 'RECOVERING', 'ESCALATED']);
    }

    public function scopeResolved($query)
    {
        return $query->where('status', 'RESOLVED');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'FAILED');
    }

    public function scopeRecent($query, int $hours = 24)
    {
        return $query->where('detected_at', '>=', now()->subHours($hours));
    }

    /*
    |--------------------------------------------------------------------------
    | METHODS
    |--------------------------------------------------------------------------
    */

    public function isOpen(): bool
    {
        return in_array($this->status, ['OPEN', 'RECOVERING', 'ESCALATED']);
    }

    public function markRecovering(): void
    {
        $this->update(['status' => 'RECOVERING']);
    }

    public function markResolved(array $recoverySummary = []): void
    {
        $this->update([
            'status'          => 'RESOLVED',
            'recovered_at'    => now(),
            'duration_seconds' => $this->detected_at
                ? (int) $this->detected_at->diffInSeconds(now())
                : null,
            'recovery_summary' => $recoverySummary,
        ]);
    }

    public function markFailed(): void
    {
        $this->update(['status' => 'FAILED']);
    }

    public function markEscalated(): void
    {
        $this->update(['status' => 'ESCALATED']);
    }

    public function incrementRetry(): void
    {
        $this->increment('retry_count');
    }

    public static function generateIncidentId(): string
    {
        $date = now()->format('Y');
        $sequence = self::whereYear('detected_at', $date)->count() + 1;

        return sprintf('INC-%s-%04d', $date, $sequence);
    }
}
