<?php

namespace App\Models\SystemGuard;

use Illuminate\Database\Eloquent\Model;

class SystemGuardState extends Model
{
    protected $table = 'system_guard_states';

    protected $fillable = [
        'component',
        'status',
        'message',
        'error_type',
        'last_value',
        'detail',
        'state_changed_at',
        'last_checked_at',
        'downtime_seconds',
        'consecutive_failures',
    ];

    protected $casts = [
        'state_changed_at'     => 'datetime',
        'last_checked_at'      => 'datetime',
        'downtime_seconds'     => 'float',
        'consecutive_failures' => 'integer',
        'detail'               => 'array',
    ];

    protected $dates = [
        'state_changed_at',
        'last_checked_at',
    ];

    /**
     * Ambil state untuk komponen tertentu (buat jika belum ada).
     */
    public static function forComponent(string $component): self
    {
        return static::firstOrCreate(
            ['component' => $component],
            ['status' => 'UNKNOWN']
        );
    }

    /*
    |--------------------------------------------------------------------------
    | STATUS PREFIXES
    |--------------------------------------------------------------------------
    */

    public function isOnline(): bool
    {
        return $this->status === 'ONLINE' || $this->status === 'RUNNING';
    }

    public function isHealthy(): bool
    {
        return $this->isOnline();
    }

    public function isDegraded(): bool
    {
        return $this->status === 'DEGRADED';
    }

    public function isOffline(): bool
    {
        return $this->status === 'OFFLINE';
    }
}
