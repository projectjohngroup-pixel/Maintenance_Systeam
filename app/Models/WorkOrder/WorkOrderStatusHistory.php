<?php

namespace App\Models\WorkOrder;

use Illuminate\Database\Eloquent\Model;

class WorkOrderStatusHistory extends Model
{
    protected $table =
        'work_order_status_histories';


    protected $fillable = [

        'work_order_id',

        'status',

        'alasan',

        'keterangan',

        'started_at',

        'ended_at',

        'created_by',
    ];


    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
    ];


    /*
    |--------------------------------------------------------------------------
    | RELASI
    |--------------------------------------------------------------------------
    */

    public function workOrder()
    {
        return $this->belongsTo(
            WorkOrder::class,
            'work_order_id'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | RECORD STATUS BARU
    |--------------------------------------------------------------------------
    |
    | Menutup histori aktif (ended_at masih null)
    | lalu membuka histori baru untuk status terpasang.
    |
    */

    public static function recordTransition(
        WorkOrder $workOrder,
        string $newStatus,
        ?string $alasan = null,
        ?string $keterangan = null
    ): void {

        $now = now();

        $active = self::where('work_order_id', $workOrder->id)
            ->whereNull('ended_at')
            ->orderByDesc('id')
            ->first();


        if ($active) {

            if (
                strtoupper(trim($active->status)) ===
                strtoupper(trim($newStatus))
            ) {
                return;
            }

            $active->update([
                'ended_at' => $now,
            ]);
        }


        self::create([
            'work_order_id' => $workOrder->id,

            'status' => $newStatus,

            'alasan' => $alasan,

            'keterangan' => $keterangan,

            'started_at' => $now,

            'created_by' => auth()->id(),
        ]);
    }
}
