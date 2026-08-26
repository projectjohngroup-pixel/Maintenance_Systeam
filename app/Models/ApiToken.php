<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApiToken extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'token',
        'last_used_at',
        'expires_at',
    ];

    protected $casts = [
        'last_used_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /*
    |--------------------------------------------------------------------------
    | RESOLVE USER DARI BEARER TOKEN
    |--------------------------------------------------------------------------
    */

    public static function resolveUser(?string $plainToken): ?User
    {
        if (!$plainToken) {
            return null;
        }

        $record = static::query()
            ->where('token', hash('sha256', $plainToken))
            ->first();

        if (!$record) {
            return null;
        }

        if ($record->expires_at && $record->expires_at->isPast()) {
            return null;
        }

        $record->forceFill(['last_used_at' => now()])->save();

        return $record->user;
    }
}
