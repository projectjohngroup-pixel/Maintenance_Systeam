<?php

namespace App\Models\Inventory;

use App\Models\Machine\MachineSparepart;
use App\Support\DepartmentAccess;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Barang extends Model
{
    protected $table = 'barangs';

    protected $fillable = [
        'kode_barang',
        'nama_spesifikasi',
        'satuan_id',
        'stok',
        'stok_minimum',
        'lokasi_penyimpanan',
        'status',
        'department',
    ];

    protected $casts = [
        'stok' => 'integer',
        'stok_minimum' => 'integer',
    ];

    public function satuan(): BelongsTo
    {
        return $this->belongsTo(
            Satuan::class,
            'satuan_id'
        );
    }

    public function machineSpareparts(): HasMany
    {
        return $this->hasMany(
            MachineSparepart::class,
            'barang_id'
        );
    }

    public function scopeVisibleTo(Builder $query, $user, ?string $department = null): Builder
    {
        return DepartmentAccess::applyBarangScope($query, $user, $department);
    }

    public function getKondisiStokAttribute(): string
    {
        if ($this->stok <= 0) {
            return 'HABIS';
        }

        if ($this->stok <= $this->stok_minimum) {
            return 'MENIPIS';
        }

        return 'TERSEDIA';
    }
}