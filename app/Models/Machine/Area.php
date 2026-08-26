<?php

namespace App\Models\Machine;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Area extends Model
{
    protected $table = 'areas';

    protected $fillable = [
        'nama_area',
        'keterangan',
    ];

    public function machines(): HasMany
    {
        return $this->hasMany(
            Machine::class,
            'area_id'
        );
    }
}