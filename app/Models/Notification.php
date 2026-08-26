<?php

namespace App\Models;

use App\Models\WorkOrder\WorkOrder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    protected $table = 'notifications';

    protected $fillable = [
        'user_id',
        'work_order_id',
        'type',
        'title',
        'message',
        'status',
        'read_at',
        'deadline_at',
        'completed_at',
        'delay_reason',
    ];

    protected $casts = [
        'read_at' => 'datetime',
        'deadline_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function workOrder(): BelongsTo
    {
        return $this->belongsTo(
            WorkOrder::class,
            'work_order_id'
        );
    }
}