<?php

namespace App\Models\Machine;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Machine extends Model
{
    protected $table = 'mesins';

    protected $fillable = [
        'kode_mesin',
        'nama_mesin',
        'area_id',
        'spesifikasi',
        'kw',
        'status',
    ];

    protected $casts = [
        'kw' => 'decimal:2',
    ];

    public function area(): BelongsTo
    {
        return $this->belongsTo(
            Area::class,
            'area_id'
        );
    }

    public function spareparts(): HasMany
    {
        return $this->hasMany(
            MachineSparepart::class,
            'machine_id'
        );
    }
}