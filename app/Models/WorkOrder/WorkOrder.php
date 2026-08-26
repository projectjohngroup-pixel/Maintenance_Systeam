<?php

namespace App\Models\WorkOrder;

use App\Support\DepartmentAccess;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkOrder extends Model
{
    use HasFactory;

    protected $table = 'work_orders';


    /*
    |--------------------------------------------------------------------------
    | FILLABLE
    |--------------------------------------------------------------------------
    */

    protected $fillable = [

        // =====================================================
        // DATA DASAR WORK ORDER
        // =====================================================

        'no_wo',

        'tanggal_kerusakan',

        'jam_kerusakan',

        'departemen',

        'tujuan',

        'assigned_department',

        'priority',

        'kategori',

        'area',

        'mesin',

        'job',

        'deskripsi',

        'status',


        // =====================================================
        // DOKUMENTASI USER
        // =====================================================

        'foto',

        'foto_kerusakan',

        // Tetap dipertahankan karena ada di sistem lama
        'wo',


        // =====================================================
        // PEMBUAT WO
        // =====================================================

        'dibuat_oleh',


        // =====================================================
        // MAINTENANCE
        // =====================================================

        'laporan_diterima',

        'perencanaan_pekerjaan',

        'jadwal_perbaikan',

        'tanggal_mulai_perbaikan',

        'jam_mulai_perbaikan',

        'tanggal_selesai_perbaikan',

        'jam_selesai_perbaikan',

        'teknisi',

        'foto_perbaikan',

        'keterangan',
    ];


    /*
    |--------------------------------------------------------------------------
    | CASTS
    |--------------------------------------------------------------------------
    */

    protected $casts = [

        'tanggal_kerusakan' =>
            'date',

        'jadwal_perbaikan' =>
            'datetime',

        'tanggal_mulai_perbaikan' =>
            'date',

        'tanggal_selesai_perbaikan' =>
            'date',

        /*
        | Kolom JSON foto kerusakan.
        */

        'foto_kerusakan' => 'array',
    ];


    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    public function statusHistories()
    {
        return $this->hasMany(
            WorkOrderStatusHistory::class,
            'work_order_id'
        );
    }

    public function notifications()
    {
        return $this->hasMany(
            \App\Models\Notification::class,
            'work_order_id'
        );
    }

    protected static function booted(): void
    {
        static::saving(function (WorkOrder $workOrder) {
            $data = DepartmentAccess::resolveAssignedDepartment([
                'tujuan' => $workOrder->tujuan,
                'assigned_department' => $workOrder->assigned_department,
            ]);

            $workOrder->assigned_department =
                $data['assigned_department'] ?? $workOrder->assigned_department;

            $workOrder->tujuan =
                $data['tujuan'] ?? $workOrder->tujuan;
        });
    }

    public function scopeVisibleTo(Builder $query, $user, ?string $department = null): Builder
    {
        return DepartmentAccess::applyWorkOrderScope($query, $user, $department);
    }

    public function assignedDepartment(): string
    {
        return DepartmentAccess::workOrderDepartment($this);
    }
}