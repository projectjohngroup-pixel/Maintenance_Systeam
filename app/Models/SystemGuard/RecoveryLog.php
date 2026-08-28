<?php

namespace App\Models\SystemGuard;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecoveryLog extends Model
{
    protected $table = 'recovery_logs';

    protected $fillable = [
        'incident_id',
        'monitor_config_id',
        'action',
        'action_type',
        'status',
        'result_message',
        'verification_passed',
        'verification_details',
        'attempt_number',
        'started_at',
        'completed_at',
        'duration_ms',
        'metadata',
    ];

    protected $casts = [
        'verification_passed' => 'boolean',
        'started_at'          => 'datetime',
        'completed_at'        => 'datetime',
        'duration_ms'         => 'integer',
        'attempt_number'      => 'integer',
        'metadata'            => 'array',
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

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    public function scopeSuccessful($query)
    {
        return $query->where('verification_passed', true);
    }

    public function scopeFailed($query)
    {
        return $query->where('verification_passed', false);
    }

    public function scopeForIncident($query, string $incidentId)
    {
        return $query->where('incident_id', $incidentId);
    }

    /*
    |--------------------------------------------------------------------------
    | METHODS
    |--------------------------------------------------------------------------
    */

    public function markCompleted(bool $passed, ?string $details = null): void
    {
        $this->update([
            'status'               => $passed ? 'SUCCESS' : 'FAILED',
            'verification_passed'  => $passed,
            'verification_details' => $details,
            'completed_at'         => now(),
            'duration_ms'          => $this->started_at
                ? (int) $this->started_at->diffInMilliseconds(now())
                : null,
        ]);
    }

    public function markSkipped(string $reason): void
    {
        $this->update([
            'status'         => 'SKIPPED',
            'result_message' => $reason,
            'completed_at'   => now(),
        ]);
    }

    public function durationLabel(): string
    {
        if ($this->duration_ms === null) {
            return 'N/A';
        }

        if ($this->duration_ms < 1000) {
            return $this->duration_ms . ' ms';
        }

        return round($this->duration_ms / 1000, 2) . ' s';
    }
}
