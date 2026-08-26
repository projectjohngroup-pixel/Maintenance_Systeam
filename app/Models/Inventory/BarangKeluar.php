<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BarangKeluar extends Model
{
    protected $table = 'barang_keluars';

    protected $fillable = [
        'no_transaksi',
        'tanggal_keluar',
        'area_id',
        'machine_id',
        'barang_id',
        'qty',
        'satuan',
        'stok_awal',
        'sisa_stok',
        'dipakai_oleh',
        'no_wo',
        'keterangan',
        'status',
        'user_id',
    ];

    protected $casts = [
        'tanggal_keluar' => 'date',
        'qty' => 'integer',
        'stok_awal' => 'integer',
        'sisa_stok' => 'integer',
    ];

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class, 'barang_id');
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Machine\Area::class, 'area_id');
    }

    public function machine(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Machine\Machine::class, 'machine_id');
    }
}