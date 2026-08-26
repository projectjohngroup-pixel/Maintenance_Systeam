<?php

namespace App\Models\Machine;

use App\Models\Inventory\Barang;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MachineSparepart extends Model
{
    protected $table = 'machine_spareparts';

    protected $fillable = [
        'machine_id',
        'barang_id',
        'qty',
        'keterangan',
    ];

    protected $casts = [
        'qty' => 'decimal:2',
    ];

    public function machine(): BelongsTo
    {
        return $this->belongsTo(
            Machine::class,
            'machine_id'
        );
    }

    public function barang(): BelongsTo
    {
        return $this->belongsTo(
            Barang::class,
            'barang_id'
        );
    }
}