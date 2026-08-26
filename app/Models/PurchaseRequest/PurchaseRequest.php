<?php

namespace App\Models\PurchaseRequest;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseRequest extends Model
{
    protected $table = 'purchase_requests';

    protected $fillable = [
        'no_pr',
        'tanggal_pr',
        'user_id',
        'keperluan',
        'prioritas',
        'status',
        'catatan',
    ];

    protected $casts = [
        'tanggal_pr' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(
            PurchaseRequestItem::class,
            'purchase_request_id'
        );
    }
}