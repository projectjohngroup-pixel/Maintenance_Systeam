<?php

namespace App\Http\Controllers\WorkOrder;

use App\Http\Controllers\Controller;

use App\Models\Inventory\Barang;
use App\Models\Machine\Machine;
use App\Models\Notification;
use App\Models\User;
use App\Models\WorkOrder\WorkOrder;
use App\Models\WorkOrder\WorkOrderStatusHistory;

use App\Support\DepartmentAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class MaintenanceWorkOrderController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | INDEX MAINTENANCE
    |--------------------------------------------------------------------------
    |
    | Maintenance bertindak sebagai Customer Service Maintenance.
    |
    | SEMUA WORK ORDER ditampilkan di daftar.
    |
    | Filter:
    | - No. WO
    | - Departemen
    | - Kategori
    | - Prioritas
    | - Status
    | - Bulan
    | - Tahun
    |
    */

    public function index(Request $request)
    {
        return $this->renderIndex($request, null);
    }

    public function indexMekanik(Request $request)
    {
        DepartmentAccess::assertCanAccessDepartment(
            auth()->user(),
            DepartmentAccess::MEKANIK_MAINT
        );

        return $this->renderIndex(
            $request,
            DepartmentAccess::MEKANIK_MAINT
        );
    }

    public function indexPrev(Request $request)
    {
        DepartmentAccess::assertCanAccessDepartment(
            auth()->user(),
            DepartmentAccess::PREV_MAINT
        );

        return $this->renderIndex(
            $request,
            DepartmentAccess::PREV_MAINT
        );
    }

    private function renderIndex(Request $request, ?string $departmentScope)
    {
        $user = auth()->user();

        if (!DepartmentAccess::isMaintenanceStaff($user) && !DepartmentAccess::isAdmin($user)) {
            abort(403, 'Anda tidak memiliki akses ke Work Order Maintenance.');
        }

        /*
        |--------------------------------------------------------------------------
        | FILTER
        |--------------------------------------------------------------------------
        */

        $searchNoWo = trim(
            $request->input('no_wo', '')
        );

        $departemen = trim(
            $request->input('departemen', '')
        );

        $kategori = trim(
            $request->input('kategori', '')
        );

        $prioritas = trim(
            $request->input('prioritas', '')
        );

        $status = trim(
            $request->input('status', '')
        );

        $bulan = trim(
            $request->input('bulan', '')
        );

        $tahun = trim(
            $request->input('tahun', '')
        );


        /*
        |--------------------------------------------------------------------------
        | QUERY WORK ORDER SESUAI ROLE / DEPARTMENT
        |--------------------------------------------------------------------------
        */

        $query = WorkOrder::query()->visibleTo($user, $departmentScope);


        /*
        |--------------------------------------------------------------------------
        | NO. WO
        |--------------------------------------------------------------------------
        */

        if ($searchNoWo !== '') {

            $query->where(
                'no_wo',
                'like',
                '%' . $searchNoWo . '%'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | DEPARTEMEN
        |--------------------------------------------------------------------------
        */

        if ($departemen !== '') {

            $query->where(
                'departemen',
                $departemen
            );
        }


        /*
        |--------------------------------------------------------------------------
        | KATEGORI
        |--------------------------------------------------------------------------
        */

        if ($kategori !== '') {

            $query->where(
                'kategori',
                $kategori
            );
        }


        /*
        |--------------------------------------------------------------------------
        | PRIORITAS
        |--------------------------------------------------------------------------
        */

        if ($prioritas !== '') {

            $query->where(
                'priority',
                $prioritas
            );
        }


        /*
        |--------------------------------------------------------------------------
        | STATUS
        |--------------------------------------------------------------------------
        */

        if ($status !== '') {

            $query->where(
                'status',
                $status
            );
        }


        /*
        |--------------------------------------------------------------------------
        | BULAN
        |--------------------------------------------------------------------------
        */

        if (
            $bulan !== ''
            &&
            is_numeric($bulan)
        ) {

            $query->whereMonth(
                'created_at',
                (int) $bulan
            );
        }


        /*
        |--------------------------------------------------------------------------
        | TAHUN
        |--------------------------------------------------------------------------
        */

        if (
            $tahun !== ''
            &&
            is_numeric($tahun)
        ) {

            $query->whereYear(
                'created_at',
                (int) $tahun
            );
        }


        /*
        |--------------------------------------------------------------------------
        | DATA WORK ORDER
        |--------------------------------------------------------------------------
        */

        $workOrders =
            $query
                ->orderByDesc(
                    'created_at'
                )
                ->get();


        /*
        |--------------------------------------------------------------------------
        | AMBIL WO UNREAD
        |--------------------------------------------------------------------------
        |
        | Badge dihitung berdasarkan WO unik.
        |
        */

        $unreadWorkOrderIds =
            Notification::query()
                ->where(
                    'user_id',
                    $user->id
                )
                ->where(
                    'status',
                    'UNREAD'
                )
                ->whereNotNull(
                    'work_order_id'
                )
                ->pluck(
                    'work_order_id'
                )
                ->filter()
                ->unique()
                ->values();


        /*
        |--------------------------------------------------------------------------
        | PASANG STATUS NOTIFIKASI
        |--------------------------------------------------------------------------
        */

        $workOrders =
            $workOrders->map(
                function (
                    WorkOrder $workOrder
                ) use (
                    $unreadWorkOrderIds
                ) {

                    $workOrder->notification_status =
                        $unreadWorkOrderIds->contains(
                            $workOrder->id
                        )
                            ? 'UNREAD'
                            : 'READ';

                    return $workOrder;
                }
            );


        /*
        |--------------------------------------------------------------------------
        | BADGE JUMLAH NOTIFIKASI
        |--------------------------------------------------------------------------
        */

        $unreadCount =
            $unreadWorkOrderIds->count();


        $baseOptions = WorkOrder::query()->visibleTo($user, $departmentScope);

        /*
        |--------------------------------------------------------------------------
        | DROPDOWN DEPARTEMEN
        |--------------------------------------------------------------------------
        */

        $departemenOptions =
            (clone $baseOptions)
                ->whereNotNull(
                    'departemen'
                )
                ->where(
                    'departemen',
                    '!=',
                    ''
                )
                ->distinct()
                ->orderBy(
                    'departemen'
                )
                ->pluck(
                    'departemen'
                );


        /*
        |--------------------------------------------------------------------------
        | DROPDOWN KATEGORI
        |--------------------------------------------------------------------------
        */

        $kategoriOptions =
            (clone $baseOptions)
                ->whereNotNull(
                    'kategori'
                )
                ->where(
                    'kategori',
                    '!=',
                    ''
                )
                ->distinct()
                ->orderBy(
                    'kategori'
                )
                ->pluck(
                    'kategori'
                );


        /*
        |--------------------------------------------------------------------------
        | DROPDOWN PRIORITAS
        |--------------------------------------------------------------------------
        */

        $prioritasOptions =
            (clone $baseOptions)
                ->whereNotNull(
                    'priority'
                )
                ->where(
                    'priority',
                    '!=',
                    ''
                )
                ->distinct()
                ->orderBy(
                    'priority'
                )
                ->pluck(
                    'priority'
                );


        /*
        |--------------------------------------------------------------------------
        | DROPDOWN STATUS
        |--------------------------------------------------------------------------
        */

        $statusOptions =
            (clone $baseOptions)
                ->whereNotNull(
                    'status'
                )
                ->where(
                    'status',
                    '!=',
                    ''
                )
                ->distinct()
                ->orderBy(
                    'status'
                )
                ->pluck(
                    'status'
                );


        /*
        |--------------------------------------------------------------------------
        | TAHUN
        |--------------------------------------------------------------------------
        |
        | 2026 - 2030
        |
        */

        $tahunOptions =
            collect(
                range(
                    2026,
                    2030
                )
            );

        $pendingCount = $workOrders->filter(function (WorkOrder $workOrder) {
            return in_array(
                strtoupper(trim((string) $workOrder->status)),
                ['OPEN', 'DITERIMA'],
                true
            );
        })->count();

        $newWoCount = $workOrders->filter(function (WorkOrder $workOrder) {
            return ($workOrder->notification_status ?? '') === 'UNREAD';
        })->count();


        /*
        |--------------------------------------------------------------------------
        | VIEW
        |--------------------------------------------------------------------------
        */

        return view(
            'work-orders.maintenance.index',
            compact(
                'workOrders',
                'unreadCount',
                'searchNoWo',
                'departemen',
                'kategori',
                'prioritas',
                'status',
                'bulan',
                'tahun',
                'departemenOptions',
                'kategoriOptions',
                'prioritasOptions',
                'statusOptions',
                'tahunOptions',
                'departmentScope',
                'pendingCount',
                'newWoCount'
            )
        );
    }



    /*
    |--------------------------------------------------------------------------
    | REPORT / LAPORAN WORK ORDER
    |--------------------------------------------------------------------------
    |
    | Halaman khusus laporan Maintenance.
    |
    | Filter:
    | - No. WO
    | - Departemen
    | - Kategori
    | - Status
    | - Bulan
    | - Tahun
    |
    */

    public function report(Request $request)
    {
        $user = auth()->user();

        if (!DepartmentAccess::isMaintenanceStaff($user) && !DepartmentAccess::isAdmin($user)) {
            abort(403, 'Anda tidak memiliki akses ke laporan Work Order.');
        }

        /*
        |--------------------------------------------------------------------------
        | FILTER
        |--------------------------------------------------------------------------
        */

        $searchNoWo = trim(
            $request->input('no_wo', '')
        );

        $departemen = trim(
            $request->input('departemen', '')
        );

        $kategori = trim(
            $request->input('kategori', '')
        );

        $status = trim(
            $request->input('status', '')
        );

        $bulan = trim(
            $request->input('bulan', '')
        );

        $tahun = trim(
            $request->input('tahun', '')
        );


        /*
        |--------------------------------------------------------------------------
        | QUERY
        |--------------------------------------------------------------------------
        */

        $query = WorkOrder::query()->visibleTo($user);


        /*
        |--------------------------------------------------------------------------
        | NO. WO
        |--------------------------------------------------------------------------
        */

        if ($searchNoWo !== '') {

            $query->where(
                'no_wo',
                'like',
                '%' . $searchNoWo . '%'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | DEPARTEMEN
        |--------------------------------------------------------------------------
        */

        if ($departemen !== '') {

            $query->where(
                'departemen',
                $departemen
            );
        }


        /*
        |--------------------------------------------------------------------------
        | KATEGORI
        |--------------------------------------------------------------------------
        */

        if ($kategori !== '') {

            $query->where(
                'kategori',
                $kategori
            );
        }


        /*
        |--------------------------------------------------------------------------
        | STATUS
        |--------------------------------------------------------------------------
        */

        if ($status !== '') {

            $query->where(
                'status',
                $status
            );
        }


        /*
        |--------------------------------------------------------------------------
        | BULAN
        |--------------------------------------------------------------------------
        */

        if (
            $bulan !== ''
            &&
            is_numeric($bulan)
        ) {

            $query->whereMonth(
                'created_at',
                (int) $bulan
            );
        }


        /*
        |--------------------------------------------------------------------------
        | TAHUN
        |--------------------------------------------------------------------------
        */

        if (
            $tahun !== ''
            &&
            is_numeric($tahun)
        ) {

            $query->whereYear(
                'created_at',
                (int) $tahun
            );
        }


        /*
        |--------------------------------------------------------------------------
        | DATA LAPORAN
        |--------------------------------------------------------------------------
        */

        $workOrders =
            $query
                ->orderByDesc(
                    'created_at'
                )
                ->get();


        /*
        |--------------------------------------------------------------------------
        | DROPDOWN DEPARTEMEN
        |--------------------------------------------------------------------------
        */

        $departemenOptions =
            WorkOrder::query()
                ->visibleTo($user)
                ->whereNotNull(
                    'departemen'
                )
                ->where(
                    'departemen',
                    '!=',
                    ''
                )
                ->distinct()
                ->orderBy(
                    'departemen'
                )
                ->pluck(
                    'departemen'
                );


        /*
        |--------------------------------------------------------------------------
        | DROPDOWN KATEGORI
        |--------------------------------------------------------------------------
        */

        $kategoriOptions =
            WorkOrder::query()
                ->visibleTo($user)
                ->whereNotNull(
                    'kategori'
                )
                ->where(
                    'kategori',
                    '!=',
                    ''
                )
                ->distinct()
                ->orderBy(
                    'kategori'
                )
                ->pluck(
                    'kategori'
                );


        /*
        |--------------------------------------------------------------------------
        | DROPDOWN STATUS
        |--------------------------------------------------------------------------
        */

        $statusOptions =
            WorkOrder::query()
                ->visibleTo($user)
                ->whereNotNull(
                    'status'
                )
                ->where(
                    'status',
                    '!=',
                    ''
                )
                ->distinct()
                ->orderBy(
                    'status'
                )
                ->pluck(
                    'status'
                );


        /*
        |--------------------------------------------------------------------------
        | TAHUN
        |--------------------------------------------------------------------------
        */

        $tahunOptions =
            collect(
                range(
                    2026,
                    2030
                )
            );


        /*
        |--------------------------------------------------------------------------
        | VIEW
        |--------------------------------------------------------------------------
        */

        return view(
            'work-orders.maintenance.report',
            compact(
                'workOrders',
                'searchNoWo',
                'departemen',
                'kategori',
                'status',
                'bulan',
                'tahun',
                'departemenOptions',
                'kategoriOptions',
                'statusOptions',
                'tahunOptions'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | SHOW
    |--------------------------------------------------------------------------
    |
    | Melihat seluruh detail Work Order.
    |
    */

    public function show($id)
    {
        $workOrder =
            WorkOrder::findOrFail(
                $id
            );


        DepartmentAccess::assertCanAccessWorkOrder(
            auth()->user(),
            $workOrder
        );

        if (
            !DepartmentAccess::isMaintenanceStaff(auth()->user())
            && !DepartmentAccess::isAdmin(auth()->user())
        ) {
            abort(403, 'Anda tidak memiliki akses ke Work Order Maintenance.');
        }


        $this->markNotificationsRead(
            $workOrder->id
        );


        return view(
            'work-orders.maintenance.show',
            compact(
                'workOrder'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | EDIT / TINDAK LANJUT
    |--------------------------------------------------------------------------
    */

    public function edit($id)
    {
        $workOrder =
            WorkOrder::findOrFail(
                $id
            );


        DepartmentAccess::assertCanAccessWorkOrder(
            auth()->user(),
            $workOrder
        );

        if (
            !DepartmentAccess::isMaintenanceStaff(auth()->user())
            && !DepartmentAccess::isAdmin(auth()->user())
        ) {
            abort(403, 'Anda tidak memiliki akses ke Work Order Maintenance.');
        }


        /*
        |--------------------------------------------------------------------------
        | WO DIANGGAP SUDAH DIBACA
        |--------------------------------------------------------------------------
        */

        $this->markNotificationsRead(
            $workOrder->id
        );


        /*
        |--------------------------------------------------------------------------
        | STOK BARANG
        |--------------------------------------------------------------------------
        */

        $spareparts =
            $this->getStockBarang($workOrder);


        /*
        |--------------------------------------------------------------------------
        | SPAREPART SESUAI MESIN
        |--------------------------------------------------------------------------
        */

        $machineSpareparts =
            $this->getMachineSpareparts(
                $workOrder
            );


        /*
        |--------------------------------------------------------------------------
        | DATA MESIN
        |--------------------------------------------------------------------------
        */

        $machines =
            Machine::query()
                ->orderBy(
                    'nama_mesin'
                )
                ->get([
                    'id',
                    'kode_mesin',
                    'nama_mesin',
                    'area_id',
                ]);


        /*
        |--------------------------------------------------------------------------
        | SPAREPART TERSIMPAN
        |--------------------------------------------------------------------------
        */

        $usedSpareparts =
            $this->getSavedSpareparts(
                $workOrder
            );


        return view(
            'work-orders.maintenance.edit',
            compact(
                'workOrder',
                'spareparts',
                'machineSpareparts',
                'machines',
                'usedSpareparts'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE TINDAK LANJUT MAINTENANCE
    |--------------------------------------------------------------------------
    */

    public function update(
        Request $request,
        $id
    ) {

        $workOrder =
            WorkOrder::findOrFail(
                $id
            );


        DepartmentAccess::assertCanAccessWorkOrder(
            auth()->user(),
            $workOrder
        );

        if (
            !DepartmentAccess::isMaintenanceStaff(auth()->user())
            && !DepartmentAccess::isAdmin(auth()->user())
        ) {
            abort(403, 'Anda tidak memiliki akses ke Work Order Maintenance.');
        }


        /*
        |--------------------------------------------------------------------------
        | VALIDASI
        |--------------------------------------------------------------------------
        */

        $validated =
            $request->validate(
                [

                    'status' => [
                        'required',
                        'in:OPEN,DITERIMA,DITOLAK,SCHEDULED,IN PROGRESS,PENDING,SERVICE LUAR,CLOSE',
                    ],

                    'laporan_diterima' => [
                        'nullable',
                        'string',
                    ],

                    'solusi_perbaikan' => [
                        'nullable',
                        'string',
                    ],

                    'perencanaan_pekerjaan' => [
                        'nullable',
                        'string',
                    ],

                    'jadwal_perbaikan' => [
                        'nullable',
                        'date',
                    ],

                    'tanggal_mulai_perbaikan' => [
                        'nullable',
                        'date',
                    ],

                    'jam_mulai_perbaikan' => [
                        'nullable',
                        'date_format:H:i',
                    ],

                    'tanggal_selesai_perbaikan' => [
                        'nullable',
                        'date',
                    ],

                    'jam_selesai_perbaikan' => [
                        'nullable',
                        'date_format:H:i',
                    ],

                    'teknisi' => [
                        'nullable',
                        'string',
                        'max:255',
                    ],

                    'foto_perbaikan' => [
                        'nullable',
                        'image',
                        'max:5120',
                    ],

                    'keterangan' => [
                        'nullable',
                        'string',
                    ],

                    'sparepart_qty' => [
                        'nullable',
                        'array',
                    ],

                    'sparepart_qty.*' => [
                        'nullable',
                        'numeric',
                        'min:1',
                    ],

                ]
            );


        /*
        |--------------------------------------------------------------------------
        | DITOLAK WAJIB ALASAN
        |--------------------------------------------------------------------------
        */

        if (
            $validated['status'] === 'DITOLAK'
            &&
            empty(
                trim(
                    $validated['keterangan']
                    ?? ''
                )
            )
        ) {

            return back()
                ->withErrors([
                    'keterangan' =>
                        'Alasan wajib diisi ketika WO berstatus DITOLAK.',
                ])
                ->withInput();
        }


        /*
        |--------------------------------------------------------------------------
        | PENDING WAJIB KETERANGAN
        |--------------------------------------------------------------------------
        */

        if (
            $validated['status'] === 'PENDING'
            &&
            empty(
                trim(
                    $validated['keterangan']
                    ?? ''
                )
            )
        ) {

            return back()
                ->withErrors([
                    'keterangan' =>
                        'Keterangan/alasan wajib diisi ketika WO berstatus PENDING.',
                ])
                ->withInput();
        }


        /*
        |--------------------------------------------------------------------------
        | SERVICE LUAR WAJIB KETERANGAN
        |--------------------------------------------------------------------------
        */

        if (
            $validated['status'] === 'SERVICE LUAR'
            &&
            empty(
                trim(
                    $validated['keterangan']
                    ?? ''
                )
            )
        ) {

            return back()
                ->withErrors([
                    'keterangan' =>
                        'Keterangan wajib diisi ketika WO menggunakan SERVICE LUAR.',
                ])
                ->withInput();
        }


        /*
        |--------------------------------------------------------------------------
        | CLOSE WAJIB TANGGAL + JAM SELESAI
        |--------------------------------------------------------------------------
        */

        if (
            $validated['status'] === 'CLOSE'
        ) {

            if (
                empty(
                    $validated[
                        'tanggal_selesai_perbaikan'
                    ]
                    ?? null
                )
                ||
                empty(
                    $validated[
                        'jam_selesai_perbaikan'
                    ]
                    ?? null
                )
            ) {

                return back()
                    ->withErrors([
                        'tanggal_selesai_perbaikan' =>
                            'Tanggal dan jam selesai wajib diisi ketika WO ditutup.',
                    ])
                    ->withInput();
            }
        }


        /*
        |--------------------------------------------------------------------------
        | STATUS LAMA / BARU
        |--------------------------------------------------------------------------
        */

        $oldStatus =
            strtoupper(
                trim(
                    $workOrder->status
                    ?? ''
                )
            );


        $newStatus =
            strtoupper(
                trim(
                    $validated['status']
                )
            );


        /*
        |--------------------------------------------------------------------------
        | SOLUSI
        |--------------------------------------------------------------------------
        */

        $solusi =
            $validated[
                'solusi_perbaikan'
            ]
            ??
            $validated[
                'perencanaan_pekerjaan'
            ]
            ??
            $workOrder->perencanaan_pekerjaan;


        /*
        |--------------------------------------------------------------------------
        | DATA MAINTENANCE
        |--------------------------------------------------------------------------
        */

        $data = [

            'status' =>
                $newStatus,

            'laporan_diterima' =>
                $validated[
                    'laporan_diterima'
                ]
                ??
                $workOrder->laporan_diterima,

            'perencanaan_pekerjaan' =>
                $solusi,

            'jadwal_perbaikan' =>
                $validated[
                    'jadwal_perbaikan'
                ]
                ??
                $workOrder->jadwal_perbaikan,

            'tanggal_mulai_perbaikan' =>
                $validated[
                    'tanggal_mulai_perbaikan'
                ]
                ??
                $workOrder->tanggal_mulai_perbaikan,

            'jam_mulai_perbaikan' =>
                $validated[
                    'jam_mulai_perbaikan'
                ]
                ??
                $workOrder->jam_mulai_perbaikan,

            'tanggal_selesai_perbaikan' =>
                $validated[
                    'tanggal_selesai_perbaikan'
                ]
                ??
                $workOrder->tanggal_selesai_perbaikan,

            'jam_selesai_perbaikan' =>
                $validated[
                    'jam_selesai_perbaikan'
                ]
                ??
                $workOrder->jam_selesai_perbaikan,

            'teknisi' =>
                $validated[
                    'teknisi'
                ]
                ??
                $workOrder->teknisi,

            'keterangan' =>
                $validated[
                    'keterangan'
                ]
                ??
                $workOrder->keterangan,

        ];


        /*
        |--------------------------------------------------------------------------
        | FOTO PERBAIKAN
        |--------------------------------------------------------------------------
        */

        if (
            $request->hasFile(
                'foto_perbaikan'
            )
        ) {

            $data[
                'foto_perbaikan'
            ] =
                $request
                    ->file(
                        'foto_perbaikan'
                    )
                    ->store(
                        'work-orders/foto/perbaikan',
                        'public'
                    );
        }


        /*
        |--------------------------------------------------------------------------
        | SPAREPART
        |--------------------------------------------------------------------------
        */

        $sparepartPayload =
            $this->buildSparepartPayload(
                $request
            );


        $sparepartColumn =
            $this->findWorkOrderSparepartColumn();


        if (
            $sparepartColumn
            &&
            !is_null(
                $sparepartPayload
            )
        ) {

            $data[
                $sparepartColumn
            ] =
                json_encode(
                    $sparepartPayload,
                    JSON_UNESCAPED_UNICODE
                );
        }


        /*
        |--------------------------------------------------------------------------
        | SIMPAN
        |--------------------------------------------------------------------------
        */

        DB::beginTransaction();

        try {

            $workOrder->update(
                $data
            );


            if (
                $oldStatus !== $newStatus
            ) {

                WorkOrderStatusHistory::recordTransition(

                    $workOrder,

                    $newStatus,

                    null,

                    $validated['keterangan']
                        ?? null
                );
            }

            DB::commit();

        } catch (\Throwable $e) {

            DB::rollBack();

            if (
                isset($data['foto_perbaikan']) &&
                $data['foto_perbaikan'] &&
                Storage::disk('public')->exists($data['foto_perbaikan'])
            ) {

                Storage::disk('public')->delete($data['foto_perbaikan']);
            }

            return back()
                ->withInput()
                ->withErrors([
                    'status' =>
                        'Gagal memperbarui Work Order: ' .
                        $e->getMessage(),
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | USER PEMBUAT WO
        |--------------------------------------------------------------------------
        */

        $creatorId =
            $this->getWorkOrderCreatorId(
                $workOrder
            );


        /*
        |--------------------------------------------------------------------------
        | DEADLINE
        |--------------------------------------------------------------------------
        */

        $deadline =
            $this->getDeadline(
                $workOrder
            );


        /*
        |--------------------------------------------------------------------------
        | NOTIFIKASI KE USER
        |--------------------------------------------------------------------------
        */

        if (
            $oldStatus !== $newStatus
        ) {

            $title =
                'Update Work Order';


            $message =
                'WO ' .
                $workOrder->no_wo .
                ' sekarang berstatus ' .
                $workOrder->status .
                '.';


            switch (
                $newStatus
            ) {

                case 'DITERIMA':

                    $title =
                        'WO Diterima';

                    $message =
                        'WO ' .
                        $workOrder->no_wo .
                        ' telah diterima oleh Maintenance.';

                    break;


                case 'DITOLAK':

                    $title =
                        'WO Ditolak';

                    $message =
                        'WO ' .
                        $workOrder->no_wo .
                        ' ditolak oleh Maintenance. Keterangan: ' .
                        (
                            $workOrder->keterangan
                            ?: '-'
                        );

                    break;


                case 'SCHEDULED':

                    $title =
                        'WO Dijadwalkan';

                    $message =
                        'WO ' .
                        $workOrder->no_wo .
                        ' sudah dijadwalkan oleh Maintenance.';

                    break;


                case 'IN PROGRESS':

                    $title =
                        'WO Sedang Dikerjakan';

                    $message =
                        'WO ' .
                        $workOrder->no_wo .
                        ' sedang dikerjakan oleh Maintenance.';

                    break;


                case 'PENDING':

                    $title =
                        'WO Pending';

                    $message =
                        'WO ' .
                        $workOrder->no_wo .
                        ' berstatus PENDING. Keterangan: ' .
                        (
                            $workOrder->keterangan
                            ?: '-'
                        );

                    break;


                case 'SERVICE LUAR':

                    $title =
                        'WO Service Luar';

                    $message =
                        'WO ' .
                        $workOrder->no_wo .
                        ' menggunakan SERVICE LUAR. Keterangan: ' .
                        (
                            $workOrder->keterangan
                            ?: '-'
                        );

                    break;


                case 'CLOSE':

                    $title =
                        'WO Selesai';

                    $message =
                        'WO ' .
                        $workOrder->no_wo .
                        ' telah selesai dikerjakan oleh Maintenance.';

                    break;
            }


            $this->notifyUser(
                $creatorId,
                $workOrder->id,
                'WO_STATUS_CHANGED',
                $title,
                $message,
                $deadline
            );
        }


        /*
        |--------------------------------------------------------------------------
        | PROGRESS BERUBAH TAPI STATUS SAMA
        |--------------------------------------------------------------------------
        */

        elseif (
            $this->hasMaintenanceUpdate(
                $request
            )
            ||
            !empty(
                $sparepartPayload
            )
        ) {

            $this->notifyUser(
                $creatorId,
                $workOrder->id,
                'WO_PROGRESS_UPDATED',
                'Perkembangan Work Order Diperbarui',
                'Perkembangan WO ' .
                    $workOrder->no_wo .
                    ' telah diperbarui oleh Maintenance.',
                $deadline
            );
        }


        /*
        |--------------------------------------------------------------------------
        | CEK KETERLAMBATAN
        |--------------------------------------------------------------------------
        */

        if (
            strtoupper(
                trim(
                    $workOrder->status
                    ?? ''
                )
            )
            ===
            'CLOSE'
        ) {

            $completedAt =
                $this->getCompletedAt(
                    $workOrder
                );


            if (
                $completedAt
                &&
                $completedAt->greaterThan(
                    $deadline
                )
            ) {

                $reason =
                    trim(
                        $workOrder->keterangan
                        ?? ''
                    );


                $this->notifyUser(
                    $creatorId,
                    $workOrder->id,
                    'WO_DELAY',
                    'WO Terlambat',
                    'WO ' .
                        $workOrder->no_wo .
                        ' selesai melewati batas waktu. ' .
                        (
                            $reason
                            ? 'Alasan: ' . $reason
                            : 'Alasan keterlambatan belum diisi.'
                        ),
                    $deadline
                );


                $this->notifyMaintenanceTeamDelay(
                    $workOrder,
                    $deadline,
                    $reason
                );
            }
        }


        /*
        |--------------------------------------------------------------------------
        | KEMBALI
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route(
                'work-orders.maintenance'
            )
            ->with(
                'success',
                'Work Order berhasil diperbarui.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | DELETE WORK ORDER
    |--------------------------------------------------------------------------
    |
    | Hanya boleh menghapus WO milik departemen sendiri.
    | Admin dan Maintenance CS boleh menghapus semua.
    |
    */

    public function destroy($id)
    {
        $workOrder = WorkOrder::findOrFail($id);

        DepartmentAccess::assertCanDeleteWorkOrder(
            auth()->user(),
            $workOrder
        );

        $creatorId = $this->getWorkOrderCreatorId($workOrder);
        $noWo = $workOrder->no_wo;

        Notification::where('work_order_id', $workOrder->id)->delete();

        WorkOrderStatusHistory::where('work_order_id', $workOrder->id)->delete();

        $fotoKerusakan = $workOrder->foto_kerusakan;

        if (is_array($fotoKerusakan)) {
            foreach ($fotoKerusakan as $path) {
                if ($path && Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                }
            }
        }

        if ($workOrder->foto && Storage::disk('public')->exists($workOrder->foto)) {
            Storage::disk('public')->delete($workOrder->foto);
        }

        if ($workOrder->foto_perbaikan && Storage::disk('public')->exists($workOrder->foto_perbaikan)) {
            Storage::disk('public')->delete($workOrder->foto_perbaikan);
        }

        $workOrder->delete();

        $this->notifyUser(
            $creatorId,
            null,
            'WO_DELETED',
            'Work Order Dihapus',
            'WO ' . $noWo . ' telah dihapus.'
        );

        return redirect()
            ->route('work-orders.maintenance')
            ->with('success', 'Work Order berhasil dihapus.');
    }


    /*
    |--------------------------------------------------------------------------
    | STOCK BARANG
    |--------------------------------------------------------------------------
    */

    private function getStockBarang(?WorkOrder $workOrder = null)
    {
        $query = Barang::with('satuan');

        if ($workOrder) {
            $query->where(
                'department',
                DepartmentAccess::workOrderDepartment($workOrder)
            );
        } else {
            DepartmentAccess::applyBarangScope($query, auth()->user());
        }

        $barang = $query->get();


        return $barang
            ->map(
                function (
                    $item
                ) {

                    $namaSatuan =
                        '-';


                    if (
                        $item->satuan
                    ) {

                        $namaSatuan =
                            $item
                                ->satuan
                                ->nama
                            ??
                            $item
                                ->satuan
                                ->nama_satuan
                            ??
                            $item
                                ->satuan
                                ->satuan
                            ??
                            '-';
                    }


                    return (object) [

                        'id' =>
                            $item->id,

                        'kode_barang' =>
                            $item->kode_barang,

                        'nama_barang' =>
                            $item->nama_spesifikasi,

                        'satuan' =>
                            $namaSatuan,

                        'stok' =>
                            $item->stok,

                    ];
                }
            )
            ->sortBy(
                function (
                    $item
                ) {

                    return strtolower(
                        trim(
                            $item->nama_barang
                        )
                    );
                }
            )
            ->values();
    }


    /*
    |--------------------------------------------------------------------------
    | SPAREPART SESUAI MESIN
    |--------------------------------------------------------------------------
    */

    private function getMachineSpareparts(
        WorkOrder $workOrder
    ) {

        if (
            empty(
                trim(
                    $workOrder->mesin
                    ?? ''
                )
            )
        ) {

            return collect();
        }


        if (
            !Schema::hasTable(
                'machine_spareparts'
            )
        ) {

            return collect();
        }


        $machine =
            Machine::where(
                'nama_mesin',
                $workOrder->mesin
            )
            ->first();


        if (
            !$machine
        ) {

            return collect();
        }


        $columns =
            Schema::getColumnListing(
                'machine_spareparts'
            );


        $machineColumn =
            $this->findExistingColumn(
                $columns,
                [
                    'machine_id',
                    'mesin_id',
                    'id_mesin',
                ]
            );


        $barangColumn =
            $this->findExistingColumn(
                $columns,
                [
                    'barang_id',
                    'sparepart_id',
                    'id_barang',
                    'id_sparepart',
                ]
            );


        if (
            !$machineColumn
            ||
            !$barangColumn
        ) {

            return collect();
        }


        $barangIds =
            DB::table(
                'machine_spareparts'
            )
            ->where(
                $machineColumn,
                $machine->id
            )
            ->pluck(
                $barangColumn
            )
            ->filter()
            ->unique()
            ->values();


        if (
            $barangIds->isEmpty()
        ) {

            return collect();
        }


        return $this->getStockBarang()
            ->whereIn(
                'id',
                $barangIds
            )
            ->values();
    }


    /*
    |--------------------------------------------------------------------------
    | SPAREPART TERSIMPAN
    |--------------------------------------------------------------------------
    */

    private function getSavedSpareparts(
        WorkOrder $workOrder
    ) {

        $column =
            $this->findWorkOrderSparepartColumn();


        if (
            !$column
        ) {

            return [];
        }


        $value =
            $workOrder->{$column};


        if (
            empty(
                $value
            )
        ) {

            return [];
        }


        if (
            is_array(
                $value
            )
        ) {

            return $value;
        }


        if (
            is_string(
                $value
            )
        ) {

            $decoded =
                json_decode(
                    $value,
                    true
                );


            return is_array(
                $decoded
            )
                ? $decoded
                : [];
        }


        return [];
    }


    /*
    |--------------------------------------------------------------------------
    | CARI KOLOM SPAREPART WORK ORDER
    |--------------------------------------------------------------------------
    */

    private function findWorkOrderSparepartColumn(): ?string
    {
        $table =
            (new WorkOrder)->getTable();


        if (
            !Schema::hasTable(
                $table
            )
        ) {

            return null;
        }


        $columns =
            Schema::getColumnListing(
                $table
            );


        return $this->findExistingColumn(
            $columns,
            [
                'spareparts',
                'sparepart',
                'sparepart_data',
                'used_spareparts',
                'sparepart_used',
            ]
        );
    }


    /*
    |--------------------------------------------------------------------------
    | BANGUN DATA SPAREPART
    |--------------------------------------------------------------------------
    */

    private function buildSparepartPayload(
        Request $request
    ): ?array {

        $qtyData =
            $request->input(
                'sparepart_qty',
                []
            );


        if (
            !is_array(
                $qtyData
            )
            ||
            empty(
                $qtyData
            )
        ) {

            return [];
        }


        $stock =
            $this->getStockBarang()
                ->keyBy(
                    'id'
                );


        $workOrderId =
            $request->route(
                'work_order'
            );


        if (
            !$workOrderId
        ) {

            return [];
        }


        $currentWorkOrder =
            WorkOrder::find(
                $workOrderId
            );


        if (
            !$currentWorkOrder
        ) {

            return [];
        }


        $machine =
            collect(
                $this->getMachineSpareparts(
                    $currentWorkOrder
                )
            )
            ->keyBy(
                'id'
            );


        $payload = [];


        foreach (
            $qtyData as $barangId => $qty
        ) {

            if (
                !is_numeric(
                    $qty
                )
            ) {

                continue;
            }


            $qty =
                (float) $qty;


            if (
                $qty <= 0
            ) {

                continue;
            }


            $barang =
                $stock->get(
                    (int) $barangId
                );


            if (
                !$barang
            ) {

                continue;
            }


            $stok =
                (float) (
                    $barang->stok
                    ?? 0
                );


            if (
                $stok <= 0
            ) {

                continue;
            }


            if (
                $qty > $stok
            ) {

                $qty =
                    $stok;
            }


            $payload[] = [

                'barang_id' =>
                    (int) $barang->id,

                'kode_barang' =>
                    $barang->kode_barang,

                'nama_barang' =>
                    $barang->nama_barang,

                'satuan' =>
                    $barang->satuan,

                'stok_saat_dipilih' =>
                    $stok,

                'qty' =>
                    $qty,

                'dari_sparepart_mesin' =>
                    $machine->has(
                        (int) $barangId
                    ),

            ];
        }


        return $payload;
    }


    /*
    |--------------------------------------------------------------------------
    | CARI KOLOM
    |--------------------------------------------------------------------------
    */

    private function findExistingColumn(
        array $columns,
        array $candidates
    ): ?string {

        foreach (
            $candidates
            as $candidate
        ) {

            foreach (
                $columns
                as $column
            ) {

                if (
                    strtolower(
                        trim(
                            $column
                        )
                    )
                    ===
                    strtolower(
                        trim(
                            $candidate
                        )
                    )
                ) {

                    return $column;
                }
            }
        }


        return null;
    }


    /*
    |--------------------------------------------------------------------------
    | CEK AKSES MAINTENANCE
    |--------------------------------------------------------------------------
    |
    | Role Maintenance dapat mewakili:
    |
    | PREV-MAINT
    | MEKANIK/MAINTENANCE
    |
    | Tidak menggunakan bagian sebagai pembatas akses.
    |
    */

    private function ensureCanAccess(
        WorkOrder $workOrder
    ): void {

        $user = auth()->user();

        if (
            !DepartmentAccess::isMaintenanceStaff($user)
            && !DepartmentAccess::isAdmin($user)
        ) {
            abort(
                403,
                'Anda tidak memiliki akses ke Work Order Maintenance.'
            );
        }

        DepartmentAccess::assertCanAccessWorkOrder($user, $workOrder);
    }


    /*
    |--------------------------------------------------------------------------
    | MARK NOTIFICATION READ
    |--------------------------------------------------------------------------
    */

    private function markNotificationsRead(
        int $workOrderId
    ): void {

        Notification::query()
            ->where(
                'user_id',
                auth()->id()
            )
            ->where(
                'work_order_id',
                $workOrderId
            )
            ->where(
                'status',
                'UNREAD'
            )
            ->update([
                'status' => 'READ',
            ]);
    }


    /*
    |--------------------------------------------------------------------------
    | NOTIFIKASI USER
    |--------------------------------------------------------------------------
    */

    private function notifyUser(
        ?int $userId,
        ?int $workOrderId,
        string $type,
        string $title,
        string $message,
        ?Carbon $deadline = null
    ): void {

        if (
            !$userId
        ) {

            return;
        }


        Notification::create([

            'user_id' =>
                $userId,

            'work_order_id' =>
                $workOrderId,

            'type' =>
                $type,

            'title' =>
                $title,

            'message' =>
                $message,

            'status' =>
                'UNREAD',

            'deadline_at' =>
                $deadline,

        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | CARI USER PEMBUAT WO
    |--------------------------------------------------------------------------
    */

    private function getWorkOrderCreatorId(
        WorkOrder $workOrder
    ): ?int {

        if (
            !$workOrder->dibuat_oleh
        ) {

            return null;
        }


        return User::where(
            'name',
            $workOrder->dibuat_oleh
        )
            ->value(
                'id'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | DEADLINE
    |--------------------------------------------------------------------------
    */

    private function getDeadline(
        WorkOrder $workOrder
    ): Carbon {

        $createdAt =
            $workOrder->created_at
            ? Carbon::parse(
                $workOrder->created_at
            )
            : now();


        return match (
            strtoupper(
                trim(
                    $workOrder->priority
                    ?? ''
                )
            )
        ) {

            'EMERGENCY' =>
                $createdAt
                    ->copy()
                    ->addDay(),

            'URGENT' =>
                $createdAt
                    ->copy()
                    ->addDays(7),

            default =>
                $createdAt
                    ->copy()
                    ->addDays(14),

        };
    }


    /*
    |--------------------------------------------------------------------------
    | COMPLETED AT
    |--------------------------------------------------------------------------
    */

    private function getCompletedAt(
        WorkOrder $workOrder
    ): ?Carbon {

        if (
            !$workOrder->tanggal_selesai_perbaikan
        ) {

            return null;
        }


        $date =
            $workOrder->tanggal_selesai_perbaikan;


        $time =
            $workOrder->jam_selesai_perbaikan
            ?? '00:00';


        try {

            return Carbon::parse(
                $date .
                ' ' .
                $time
            );

        } catch (
            \Throwable $e
        ) {

            return null;
        }
    }


    /*
    |--------------------------------------------------------------------------
    | CEK PERUBAHAN MAINTENANCE
    |--------------------------------------------------------------------------
    */

    private function hasMaintenanceUpdate(
        Request $request
    ): bool {

        return
            $request->filled(
                'laporan_diterima'
            )
            ||
            $request->filled(
                'solusi_perbaikan'
            )
            ||
            $request->filled(
                'perencanaan_pekerjaan'
            )
            ||
            $request->filled(
                'jadwal_perbaikan'
            )
            ||
            $request->filled(
                'tanggal_mulai_perbaikan'
            )
            ||
            $request->filled(
                'jam_mulai_perbaikan'
            )
            ||
            $request->filled(
                'tanggal_selesai_perbaikan'
            )
            ||
            $request->filled(
                'jam_selesai_perbaikan'
            )
            ||
            $request->filled(
                'teknisi'
            )
            ||
            $request->filled(
                'keterangan'
            )
            ||
            $request->hasFile(
                'foto_perbaikan'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | NOTIF KETERLAMBATAN
    |--------------------------------------------------------------------------
    */

    private function notifyMaintenanceTeamDelay(
        WorkOrder $workOrder,
        Carbon $deadline,
        string $reason = ''
    ): void {

        $message =
            'WO ' .
            $workOrder->no_wo .
            ' telah melewati batas waktu. ' .
            (
                $reason
                ? 'Alasan: ' . $reason
                : 'Alasan keterlambatan belum diisi.'
            );


        $this->notifyMaintenanceTeam(
            $workOrder,
            $deadline,
            'WO_DELAY',
            'Peringatan Keterlambatan WO',
            $message
        );
    }


    /*
    |--------------------------------------------------------------------------
    | NOTIFIKASI TEAM MAINTENANCE
    |--------------------------------------------------------------------------
    |
    | Notifikasi WO baru diberikan ke Maintenance aktif
    | yang sesuai dengan tujuan WO.
    |
    */

    private function notifyMaintenanceTeam(
        WorkOrder $workOrder,
        ?Carbon $deadline = null,
        string $type = 'WO_CREATED',
        string $title = 'Work Order Baru',
        ?string $customMessage = null
    ): void {

        DepartmentAccess::notifyWorkOrderTeam(
            $workOrder,
            $deadline,
            $type,
            $title,
            $customMessage
        );
    }
}
