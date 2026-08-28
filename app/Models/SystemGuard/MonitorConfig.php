<?php

namespace App\Models\SystemGuard;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MonitorConfig extends Model
{
    protected $table = 'monitor_configs';

    protected $fillable = [
        'name',
        'target_url',
        'target_domain',
        'type',
        'is_active',
        'check_interval_seconds',
        'timeout_seconds',
        'expected_status_code',
        'response_time_threshold_ms',
        'max_retries',
        'retry_delay_seconds',
        'recovery_actions',
        'auto_recovery_enabled',
        'description',
    ];

    protected $casts = [
        'is_active'                  => 'boolean',
        'auto_recovery_enabled'      => 'boolean',
        'check_interval_seconds'     => 'integer',
        'timeout_seconds'            => 'integer',
        'expected_status_code'       => 'integer',
        'response_time_threshold_ms' => 'integer',
        'max_retries'                => 'integer',
        'retry_delay_seconds'        => 'integer',
        'recovery_actions'           => 'array',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    public function results(): HasMany
    {
        return $this->hasMany(MonitorResult::class, 'monitor_config_id');
    }

    public function incidents(): HasMany
    {
        return $this->hasMany(IncidentLog::class, 'monitor_config_id');
    }

    public function recoveryLogs(): HasMany
    {
        return $this->hasMany(RecoveryLog::class, 'monitor_config_id');
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    public function getDomainAttribute(): ?string
    {
        if ($this->target_domain) {
            return $this->target_domain;
        }

        $parsed = parse_url($this->target_url);

        return $parsed['host'] ?? null;
    }

    public function getLatestResultAttribute(): ?MonitorResult
    {
        return $this->results()
            ->latest()
            ->first();
    }

    public function getLatestIncidentAttribute(): ?IncidentLog
    {
        return $this->incidents()
            ->latest()
            ->first();
    }

    public function hasOpenIncident(): bool
    {
        return $this->incidents()
            ->whereIn('status', ['OPEN', 'RECOVERING', 'ESCALATED'])
            ->exists();
    }

    public function activeIncident(): ?IncidentLog
    {
        return $this->incidents()
            ->whereIn('status', ['OPEN', 'RECOVERING', 'ESCALATED'])
            ->latest()
            ->first();
    }

    public function allowedRecoveryActions(): array
    {
        $configured = $this->recovery_actions ?? [];
        $whitelist = config('system-guard.recovery_whitelist', []);

        return array_filter(
            $configured,
            fn (string $action) => isset($whitelist[$action])
        );
    }
}
