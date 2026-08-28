<?php

namespace App\Models\SystemGuard;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MonitorResult extends Model
{
    protected $table = 'monitor_results';

    protected $fillable = [
        'monitor_config_id',
        'status',
        'category',
        'dns_resolved',
        'connection_successful',
        'http_successful',
        'http_status_code',
        'response_time_ms',
        'timeout_ms',
        'error_message',
        'error_type',
        'severity',
        'raw_response',
    ];

    protected $casts = [
        'dns_resolved'         => 'boolean',
        'connection_successful' => 'boolean',
        'http_successful'      => 'boolean',
        'http_status_code'     => 'integer',
        'response_time_ms'     => 'integer',
        'timeout_ms'           => 'integer',
        'raw_response'         => 'array',
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

    public function scopeHealthy($query)
    {
        return $query->where('status', 'ONLINE');
    }

    public function scopeUnhealthy($query)
    {
        return $query->where('status', '!=', 'ONLINE');
    }

    public function scopeRecent($query, int $minutes = 30)
    {
        return $query->where('created_at', '>=', now()->subMinutes($minutes));
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    public function isHealthy(): bool
    {
        return $this->status === 'ONLINE';
    }

    public function isDnsFailure(): bool
    {
        return $this->error_type === 'DNS' || $this->error_type === 'DNS_NXDOMAIN';
    }

    public function isHttpFailure(): bool
    {
        return $this->error_type === 'HTTP_4XX'
            || $this->error_type === 'HTTP_5XX';
    }

    public function isTimeout(): bool
    {
        return $this->error_type === 'TIMEOUT';
    }

    public function isConnectionFailure(): bool
    {
        return $this->error_type === 'CONNECTION_REFUSED'
            || $this->error_type === 'CONNECTION_RESET';
    }

    public function responseTimeLabel(): string
    {
        if ($this->response_time_ms === null) {
            return 'N/A';
        }

        if ($this->response_time_ms < 1000) {
            return $this->response_time_ms . ' ms';
        }

        return round($this->response_time_ms / 1000, 2) . ' s';
    }
}
