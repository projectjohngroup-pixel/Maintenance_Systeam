<?php

namespace App\Models\Inventory;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BarangMasuk extends Model
{
    protected $table = 'barang_masuks';

    protected $fillable = [
        'no_transaksi',
        'tanggal_masuk',
        'barang_id',
        'qty',
        'satuan_id',
        'supplier',
        'no_faktur',
        'harga',
        'keterangan',
        'status',
        'received_by',
    ];

    protected $casts = [
        'tanggal_masuk' => 'date',
        'qty' => 'decimal:2',
        'harga' => 'decimal:2',
    ];

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class, 'barang_id');
    }

    public function satuan(): BelongsTo
    {
        return $this->belongsTo(Satuan::class, 'satuan_id');
    }

    public function receivedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }
}
