<?php

namespace App\Http\Controllers\WorkOrder;

use App\Http\Controllers\Controller;

use App\Models\Inventory\Barang;
use App\Models\Machine\Area;
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

class AdminWorkOrderController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | INDEX ADMIN
    |--------------------------------------------------------------------------
    |
    | Administrator dapat melihat SELURUH Work Order.
    |
    */

    public function index(Request $request)
    {
        /*
        |----------------------------------------------------------------------
        | FILTER INPUT
        |----------------------------------------------------------------------
        */

        $searchNoWo = trim(
            (string) $request->input('no_wo', '')
        );

        $departemen = trim(
            (string) $request->input('departemen', '')
        );

        $kategori = trim(
            (string) $request->input('kategori', '')
        );

        $statusFilter = trim(
            (string) $request->input('status', '')
        );

        $bulan = trim(
            (string) $request->input('bulan', '')
        );

        $tahun = trim(
            (string) $request->input('tahun', '')
        );


        /*
        |----------------------------------------------------------------------
        | QUERY SELURUH WORK ORDER + FILTER
        |----------------------------------------------------------------------
        */

        $query = WorkOrder::query();

        if ($searchNoWo !== '') {
            $query->where('no_wo', 'like', '%' . $searchNoWo . '%');
        }

        if ($departemen !== '') {
            $query->whereRaw(
                'LOWER(TRIM(departemen)) = ?',
                [strtolower($departemen)]
            );
        }

        if ($kategori !== '') {
            $query->whereRaw(
                'LOWER(TRIM(kategori)) = ?',
                [strtolower($kategori)]
            );
        }

        if ($statusFilter !== '') {
            $query->whereRaw(
                'LOWER(TRIM(status)) = ?',
                [strtolower($statusFilter)]
            );
        }

        if ($bulan !== '') {
            $query->whereMonth(
                'tanggal_kerusakan',
                max(1, min(12, (int) $bulan))
            );
        }

        if ($tahun !== '') {
            $query->whereYear(
                'tanggal_kerusakan',
                (int) $tahun
            );
        }

        $workOrders =
            $query
                ->orderByDesc('created_at')
                ->get();


        /*
        |----------------------------------------------------------------------
        | DROPDOWN OPTIONS
        |----------------------------------------------------------------------
        */

        $departemenOptions =
            WorkOrder::query()
                ->whereNotNull('departemen')
                ->where('departemen', '<>', '')
                ->distinct()
                ->orderBy('departemen')
                ->pluck('departemen');

        $kategoriOptions =
            WorkOrder::query()
                ->whereNotNull('kategori')
                ->where('kategori', '<>', '')
                ->distinct()
                ->orderBy('kategori')
                ->pluck('kategori');

        $statusOptions =
            WorkOrder::query()
                ->whereNotNull('status')
                ->where('status', '<>', '')
                ->distinct()
                ->orderBy('status')
                ->pluck('status');

        $tahunOptions =
            collect(range(2026, 2030));


        /*
        |----------------------------------------------------------------------
        | MASTER (UNTUK KOMPONEN LAIN BILA DIPERLUKAN)
        |----------------------------------------------------------------------
        */

        $areas =
            Area::orderBy(
                'nama_area'
            )
            ->get([
                'id',
                'nama_area',
            ]);

        $machines =
            Machine::orderBy(
                'nama_mesin'
            )
            ->get([
                'id',
                'kode_mesin',
                'nama_mesin',
                'area_id',
            ]);

        return view(
            'work-orders.admin.index',
            compact(
                'workOrders',
                'areas',
                'machines',
                'searchNoWo',
                'departemen',
                'kategori',
                'statusFilter',
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
    | Administrator dapat melihat seluruh WO.
    |
    */

    public function show($id)
    {
        $workOrder =
            WorkOrder::findOrFail(
                $id
            );

        return view(
            'work-orders.admin.show',
            compact(
                'workOrder'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
    |
    | Administrator boleh membuat WO bila diperlukan.
    |
    */

    public function create()
    {
        $nextNoWo =
            $this->generateNextWoNumber();

        $areas =
            Area::orderBy(
                'nama_area'
            )
            ->get([
                'id',
                'nama_area',
            ]);

        $machines =
            Machine::orderBy(
                'nama_mesin'
            )
            ->get([
                'id',
                'kode_mesin',
                'nama_mesin',
                'area_id',
            ]);

        return view(
            'work-orders.admin.create',
            compact(
                'nextNoWo',
                'areas',
                'machines'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */

    public function store(
        Request $request
    ) {
        $validated =
            $request->validate(
                [

                    'no_wo' => [
                        'required',
                        'string',
                        'max:255',
                        'unique:work_orders,no_wo',
                    ],

                    'tanggal_kerusakan' => [
                        'required',
                        'date',
                    ],

                    'jam_kerusakan' => [
                        'nullable',
                        'date_format:H:i',
                    ],

                    'kategori' => [
                        'required',
                        'in:PERMINTAAN PERBAIKAN / KERUSAKAN,PEMBUATAN BARU / MODIFIKASI',
                    ],

                    'tujuan' => [
                        'required',
                        'in:PREV-MAINT,MEKANIK/MAINTENANCE',
                    ],

                    'area' => [
                        'nullable',
                        'string',
                        'max:255',
                    ],

                    'mesin' => [
                        'nullable',
                        'string',
                        'max:255',
                    ],

                    'job' => [
                        'required',
                        'string',
                        'max:255',
                    ],

                    'deskripsi' => [
                        'required',
                        'string',
                    ],

                    'priority' => [
                        'required',
                        'in:NORMAL,URGENT,EMERGENCY',
                    ],

                    'dibuat_oleh' => [
                        'nullable',
                        'string',
                        'max:255',
                    ],

                    'foto' => [
                        'nullable',
                        'array',
                    ],

                    'foto.*' => [
                        'image',
                        'max:5120',
                    ],

                ]
            );


        /*
        |--------------------------------------------------------------------------
        | VALIDASI AREA
        |--------------------------------------------------------------------------
        */

        $area = null;

        if (
            !empty(
                $validated['area']
            )
        ) {

            $area =
                Area::where(
                    'nama_area',
                    $validated['area']
                )
                ->first();

            if (!$area) {

                return back()
                    ->withErrors([
                        'area' =>
                            'Area yang dipilih tidak ditemukan di Master Area.',
                    ])
                    ->withInput();
            }
        }


        /*
        |--------------------------------------------------------------------------
        | VALIDASI MESIN
        |--------------------------------------------------------------------------
        */

        if (
            !empty(
                $validated['mesin']
            )
        ) {

            if (!$area) {

                return back()
                    ->withErrors([
                        'mesin' =>
                            'Area harus dipilih jika Mesin diisi.',
                    ])
                    ->withInput();
            }


            $machine =
                Machine::where(
                    'nama_mesin',
                    $validated['mesin']
                )
                ->where(
                    'area_id',
                    $area->id
                )
                ->first();


            if (!$machine) {

                return back()
                    ->withErrors([
                        'mesin' =>
                            'Mesin yang dipilih tidak berada pada Area tersebut.',
                    ])
                    ->withInput();
            }
        }

        $currentUser =
            auth()->user();


        $fotoKerusakan = [];

        if (
            $request->hasFile(
                'foto'
            )
        ) {

            foreach (
                $request->file('foto')
                as $file
            ) {

                $fotoKerusakan[] =
                    $file->store(
                        'work-orders/foto/kerusakan',
                        'public'
                    );
            }
        }


        DB::beginTransaction();

        try {

            $data = [

                'no_wo' =>
                    $validated['no_wo'],

                'tanggal_kerusakan' =>
                    $validated['tanggal_kerusakan'],

                'jam_kerusakan' =>
                    $validated['jam_kerusakan']
                    ?? null,

                'departemen' =>
                    $currentUser->bagian
                    ?? null,

                'kategori' =>
                    $validated['kategori'],

                'tujuan' =>
                    $validated['tujuan'],

                'assigned_department' =>
                    DepartmentAccess::fromTujuan($validated['tujuan']),

                'area' =>
                    $validated['area']
                    ?? null,

                'mesin' =>
                    $validated['mesin']
                    ?? null,

                'job' =>
                    $validated['job'],

                'deskripsi' =>
                    $validated['deskripsi'],

                'priority' =>
                    $validated['priority'],

                'status' =>
                    'OPEN',

                'dibuat_oleh' =>
                    $validated['dibuat_oleh']
                    ??
                    $currentUser->name,

                'laporan_diterima' =>
                    null,

                'perencanaan_pekerjaan' =>
                    null,

                'jadwal_perbaikan' =>
                    null,

                'tanggal_mulai_perbaikan' =>
                    null,

                'jam_mulai_perbaikan' =>
                    null,

                'tanggal_selesai_perbaikan' =>
                    null,

                'jam_selesai_perbaikan' =>
                    null,

                'teknisi' =>
                    null,

                'foto_perbaikan' =>
                    null,

                'keterangan' =>
                    null,

                'foto_kerusakan' =>
                    null,
            ];


            if (!empty($fotoKerusakan)) {

                $data['foto_kerusakan'] =
                    $fotoKerusakan;

                $data['foto'] =
                    $fotoKerusakan[0]
                    ?? null;
            }


            $workOrder =
                WorkOrder::create(
                    $data
                );


            WorkOrderStatusHistory::recordTransition(

                $workOrder,

                'OPEN',

                null,

                'Work Order dibuat.'
            );


            $deadline =
                $this->getDeadline(
                    $workOrder
                );


            $this->notifyMaintenanceTeam(
                $workOrder,
                $deadline,
                'WO_CREATED',
                'Work Order Baru',
                'WO ' .
                    $workOrder->no_wo .
                    ' dibuat oleh Administrator.'
            );


            DB::commit();

        } catch (\Throwable $e) {

            DB::rollBack();

            if (
                !empty($fotoKerusakan)
            ) {

                foreach ($fotoKerusakan as $path) {

                    if ($path && \Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {

                        \Illuminate\Support\Facades\Storage::disk('public')->delete($path);
                    }
                }
            }

            $errorCode = ((string) $e->getCode() === '23000' || str_contains($e->getMessage(), 'Duplicate') || str_contains($e->getMessage(), 'UNIQUE'))
                ? 'Nomor WO sudah digunakan. Silakan gunakan nomor lain.'
                : 'Data gagal disimpan karena koneksi/database bermasalah.';

            return back()
                ->withInput()
                ->withErrors([
                    'status' => $errorCode,
                ]);
        }


        return redirect()
            ->route(
                'work-orders.admin.index'
            )
            ->with(
                'success',
                'Work Order berhasil dibuat.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | EDIT
    |--------------------------------------------------------------------------
    |
    | Administrator full access.
    |
    */

    public function edit($id)
    {
        $workOrder =
            WorkOrder::findOrFail(
                $id
            );

        DepartmentAccess::assertCanEditWorkOrder(
            auth()->user(),
            $workOrder
        );

        $areas =
            Area::orderBy(
                'nama_area'
            )
            ->get([
                'id',
                'nama_area',
            ]);

        $machines =
            Machine::orderBy(
                'nama_mesin'
            )
            ->get([
                'id',
                'kode_mesin',
                'nama_mesin',
                'area_id',
            ]);

        $spareparts =
            $this->getStockBarang();

        $machineSpareparts =
            $this->getMachineSpareparts(
                $workOrder
            );

        return view(
            'work-orders.admin.edit',
            compact(
                'workOrder',
                'areas',
                'machines',
                'spareparts',
                'machineSpareparts'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    |
    | Administrator full access.
    | Field permintaan + field Maintenance dapat dikelola.
    |
    */

    public function update(
        Request $request,
        $id
    ) {
        $workOrder =
            WorkOrder::findOrFail(
                $id
            );

        DepartmentAccess::assertCanEditWorkOrder(
            auth()->user(),
            $workOrder
        );


        $validated =
            $request->validate(
                [

                    /*
                    | PERMINTAAN
                    */

                    'no_wo' => [
                        'required',
                        'string',
                        'max:255',
                        'unique:work_orders,no_wo,' .
                            $workOrder->id,
                    ],

                    'tanggal_kerusakan' => [
                        'required',
                        'date',
                    ],

                    'jam_kerusakan' => [
                        'nullable',
                        'date_format:H:i',
                    ],

                    'kategori' => [
                        'required',
                        'in:PERMINTAAN PERBAIKAN / KERUSAKAN,PEMBUATAN BARU / MODIFIKASI',
                    ],

                    'tujuan' => [
                        'required',
                        'in:PREV-MAINT,MEKANIK/MAINTENANCE',
                    ],

                    'area' => [
                        'nullable',
                        'string',
                        'max:255',
                    ],

                    'mesin' => [
                        'nullable',
                        'string',
                        'max:255',
                    ],

                    'job' => [
                        'required',
                        'string',
                        'max:255',
                    ],

                    'deskripsi' => [
                        'required',
                        'string',
                    ],

                    'priority' => [
                        'required',
                        'in:NORMAL,URGENT,EMERGENCY',
                    ],

                    /*
                    | STATUS
                    */

                    'status' => [
                        'required',
                        'in:OPEN,DITERIMA,DITOLAK,SCHEDULED,IN PROGRESS,PENDING,SERVICE LUAR,CLOSE',
                    ],

                    /*
                    | MAINTENANCE
                    */

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

                    'foto' => [
                        'nullable',
                        'array',
                    ],

                    'foto.*' => [
                        'image',
                        'max:5120',
                    ],

                    'keterangan' => [
                        'nullable',
                        'string',
                    ],

                ]
            );


        /*
        |--------------------------------------------------------------------------
        | VALIDASI AREA
        |--------------------------------------------------------------------------
        */

        $area = null;

        if (
            !empty(
                $validated['area']
            )
        ) {

            $area =
                Area::where(
                    'nama_area',
                    $validated['area']
                )
                ->first();


            if (!$area) {

                return back()
                    ->withErrors([
                        'area' =>
                            'Area yang dipilih tidak ditemukan di Master Area.',
                    ])
                    ->withInput();
            }
        }


        /*
        |--------------------------------------------------------------------------
        | VALIDASI MESIN
        |--------------------------------------------------------------------------
        */

        if (
            !empty(
                $validated['mesin']
            )
        ) {

            if (!$area) {

                return back()
                    ->withErrors([
                        'mesin' =>
                            'Area harus dipilih jika Mesin diisi.',
                    ])
                    ->withInput();
            }


            $machine =
                Machine::where(
                    'nama_mesin',
                    $validated['mesin']
                )
                ->where(
                    'area_id',
                    $area->id
                )
                ->first();


            if (!$machine) {

                return back()
                    ->withErrors([
                        'mesin' =>
                            'Mesin yang dipilih tidak berada pada Area tersebut.',
                    ])
                    ->withInput();
            }
        }


        /*
        |--------------------------------------------------------------------------
        | STATUS KHUSUS
        |--------------------------------------------------------------------------
        */

        if (
            $validated['status'] ===
            'DITOLAK'
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


        if (
            $validated['status'] ===
            'PENDING'
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
                        'Keterangan wajib diisi ketika WO berstatus PENDING.',
                ])
                ->withInput();
        }


        if (
            $validated['status'] ===
            'SERVICE LUAR'
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


        if (
            $validated['status'] ===
            'CLOSE'
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
        | DATA
        |--------------------------------------------------------------------------
        */

        $data = [

            'no_wo' =>
                $validated['no_wo'],

            'tanggal_kerusakan' =>
                $validated['tanggal_kerusakan'],

            'jam_kerusakan' =>
                $validated['jam_kerusakan']
                ?? null,

            'departemen' =>
                $workOrder->departemen,

            'kategori' =>
                $validated['kategori'],

            'tujuan' =>
                $validated['tujuan'],

            'assigned_department' =>
                DepartmentAccess::fromTujuan($validated['tujuan']),

            'area' =>
                $validated['area']
                ?? null,

            'mesin' =>
                $validated['mesin']
                ?? null,

            'job' =>
                $validated['job'],

            'deskripsi' =>
                $validated['deskripsi'],

            'priority' =>
                $validated['priority'],

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
        | FOTO KERUSAKAN
        |--------------------------------------------------------------------------
        */

        if (
            $request->hasFile(
                'foto'
            )
        ) {

            $fotoKerusakan = [];

            foreach (
                $request->file('foto')
                as $file
            ) {

                $fotoKerusakan[] =
                    $file->store(
                        'work-orders/foto/kerusakan',
                        'public'
                    );
            }


            $data['foto_kerusakan'] =
                $fotoKerusakan;

            $data['foto'] =
                $fotoKerusakan[0]
                ?? null;
        }


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
        | UPDATE
        |--------------------------------------------------------------------------
        */

        $workOrder->update(
            $data
        );


        /*
        |--------------------------------------------------------------------------
        | HISTORI STATUS
        |--------------------------------------------------------------------------
        */

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


        $deadline =
            $this->getDeadline(
                $workOrder
            );


        /*
        |--------------------------------------------------------------------------
        | CREATOR
        |--------------------------------------------------------------------------
        */

        $creatorId =
            $this->getWorkOrderCreatorId(
                $workOrder
            );


        /*
        |--------------------------------------------------------------------------
        | NOTIF USER
        |--------------------------------------------------------------------------
        */

        if (
            $oldStatus !==
            $newStatus
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
                        ' ditolak. Keterangan: ' .
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
                        ' telah dijadwalkan.';

                    break;

                case 'IN PROGRESS':

                    $title =
                        'WO Sedang Dikerjakan';

                    $message =
                        'WO ' .
                        $workOrder->no_wo .
                        ' sedang dikerjakan.';

                    break;

                case 'PENDING':

                    $title =
                        'WO Pending';

                    $message =
                        'WO ' .
                        $workOrder->no_wo .
                        ' berstatus PENDING.';

                    break;

                case 'SERVICE LUAR':

                    $title =
                        'WO Service Luar';

                    $message =
                        'WO ' .
                        $workOrder->no_wo .
                        ' menggunakan SERVICE LUAR.';

                    break;

                case 'CLOSE':

                    $title =
                        'WO Selesai';

                    $message =
                        'WO ' .
                        $workOrder->no_wo .
                        ' telah selesai dikerjakan.';

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

        } else {

            $this->notifyUser(
                $creatorId,
                $workOrder->id,
                'WO_ADMIN_UPDATED',
                'Work Order Diperbarui',
                'WO ' .
                    $workOrder->no_wo .
                    ' telah diperbarui oleh Administrator.',
                $deadline
            );
        }


        return redirect()
            ->route(
                'work-orders.admin.index'
            )
            ->with(
                'success',
                'Work Order berhasil diperbarui.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | DELETE
    |--------------------------------------------------------------------------
    */

    public function destroy(
        $id
    ) {

        $workOrder =
            WorkOrder::findOrFail(
                $id
            );

        DepartmentAccess::assertCanDeleteWorkOrder(
            auth()->user(),
            $workOrder
        );


        $creatorId =
            $this->getWorkOrderCreatorId(
                $workOrder
            );


        $noWo =
            $workOrder->no_wo;


        Notification::where(
            'work_order_id',
            $workOrder->id
        )->delete();


        WorkOrderStatusHistory::where(
            'work_order_id',
            $workOrder->id
        )->delete();


        $fotoKerusakan =
            $workOrder->foto_kerusakan;

        if (is_array($fotoKerusakan)) {

            foreach ($fotoKerusakan as $path) {

                if (
                    $path &&
                    Storage::disk('public')->exists($path)
                ) {

                    Storage::disk('public')->delete($path);
                }
            }
        }

        if (
            $workOrder->foto &&
            Storage::disk('public')->exists($workOrder->foto)
        ) {

            Storage::disk('public')->delete($workOrder->foto);
        }

        if (
            $workOrder->foto_perbaikan &&
            Storage::disk('public')->exists($workOrder->foto_perbaikan)
        ) {

            Storage::disk('public')->delete($workOrder->foto_perbaikan);
        }


        $workOrder->delete();


        /*
        |--------------------------------------------------------------------------
        | NOTIF USER
        |--------------------------------------------------------------------------
        */

        $this->notifyUser(
            $creatorId,
            null,
            'WO_DELETED',
            'Work Order Dihapus',
            'WO ' .
                $noWo .
                ' telah dihapus oleh Administrator.'
        );


        return redirect()
            ->route(
                'work-orders.admin.index'
            )
            ->with(
                'success',
                'Work Order berhasil dihapus.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | LAPORAN
    |--------------------------------------------------------------------------
    |
    | Admin full akses laporan.
    |
    */

    public function report(
        Request $request
    ) {

        $search =
            trim(
                $request->input(
                    'search',
                    ''
                )
            );

        $tujuan =
            $request->input(
                'tujuan'
            );

        $status =
            $request->input(
                'status'
            );

        $tanggalMulai =
            $request->input(
                'tanggal_mulai'
            );

        $tanggalSelesai =
            $request->input(
                'tanggal_selesai'
            );

        $bulan =
            $request->input(
                'bulan'
            );

        $tahun =
            $request->input(
                'tahun'
            );


        $query =
            WorkOrder::query();


        /*
        |--------------------------------------------------------------------------
        | SEARCH
        |--------------------------------------------------------------------------
        */

        if (
            $search !== ''
        ) {

            $query->where(
                function (
                    $q
                ) use (
                    $search
                ) {

                    $like =
                        '%' .
                        $search .
                        '%';


                    $q->where(
                        'no_wo',
                        'like',
                        $like
                    )
                    ->orWhere(
                        'dibuat_oleh',
                        'like',
                        $like
                    )
                    ->orWhere(
                        'departemen',
                        'like',
                        $like
                    )
                    ->orWhere(
                        'kategori',
                        'like',
                        $like
                    )
                    ->orWhere(
                        'tujuan',
                        'like',
                        $like
                    )
                    ->orWhere(
                        'area',
                        'like',
                        $like
                    )
                    ->orWhere(
                        'mesin',
                        'like',
                        $like
                    )
                    ->orWhere(
                        'job',
                        'like',
                        $like
                    )
                    ->orWhere(
                        'deskripsi',
                        'like',
                        $like
                    )
                    ->orWhere(
                        'status',
                        'like',
                        $like
                    )
                    ->orWhere(
                        'laporan_diterima',
                        'like',
                        $like
                    )
                    ->orWhere(
                        'perencanaan_pekerjaan',
                        'like',
                        $like
                    )
                    ->orWhere(
                        'teknisi',
                        'like',
                        $like
                    )
                    ->orWhere(
                        'keterangan',
                        'like',
                        $like
                    );
                }
            );
        }


        /*
        |--------------------------------------------------------------------------
        | TUJUAN
        |--------------------------------------------------------------------------
        */

        if (
            $tujuan !== null &&
            $tujuan !== ''
        ) {

            $query->where(
                'tujuan',
                $tujuan
            );
        }


        /*
        |--------------------------------------------------------------------------
        | STATUS
        |--------------------------------------------------------------------------
        */

        if (
            $status !== null &&
            $status !== ''
        ) {

            $query->where(
                'status',
                $status
            );
        }


        /*
        |--------------------------------------------------------------------------
        | TANGGAL MULAI
        |--------------------------------------------------------------------------
        */

        if (
            $tanggalMulai
        ) {

            $query->whereDate(
                'tanggal_kerusakan',
                '>=',
                $tanggalMulai
            );
        }


        /*
        |--------------------------------------------------------------------------
        | TANGGAL SELESAI
        |--------------------------------------------------------------------------
        */

        if (
            $tanggalSelesai
        ) {

            $query->whereDate(
                'tanggal_kerusakan',
                '<=',
                $tanggalSelesai
            );
        }


        /*
        |--------------------------------------------------------------------------
        | BULAN
        |--------------------------------------------------------------------------
        */

        if (
            $bulan !== null &&
            $bulan !== ''
        ) {

            $query->whereMonth(
                'tanggal_kerusakan',
                $bulan
            );
        }


        /*
        |--------------------------------------------------------------------------
        | TAHUN
        |--------------------------------------------------------------------------
        */

        if (
            $tahun !== null &&
            $tahun !== ''
        ) {

            $query->whereYear(
                'tanggal_kerusakan',
                $tahun
            );
        }


        $workOrders =
            $query
                ->orderByDesc(
                    'tanggal_kerusakan'
                )
                ->orderByDesc(
                    'created_at'
                )
                ->get();


        /*
        |--------------------------------------------------------------------------
        | TAHUN LIST
        |--------------------------------------------------------------------------
        */

        $tahunList =
            WorkOrder::query()
                ->selectRaw(
                    'YEAR(tanggal_kerusakan) as tahun'
                )
                ->whereNotNull(
                    'tanggal_kerusakan'
                )
                ->distinct()
                ->orderByDesc(
                    'tahun'
                )
                ->pluck(
                    'tahun'
                );


        return view(
            'work-orders.admin.report',
            compact(
                'workOrders',
                'tahunList',
                'search',
                'tujuan',
                'status',
                'tanggalMulai',
                'tanggalSelesai',
                'bulan',
                'tahun'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | STOCK BARANG
    |--------------------------------------------------------------------------
    |
    | Mengikuti struktur tabel barangs:
    |
    | kode_barang
    | nama_spesifikasi
    | satuan_id
    | stok
    |
    */

    private function getStockBarang()
    {
        $barang =
            Barang::with(
                'satuan'
            )
            ->get();


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


        if (!$machine) {

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
            !$machineColumn ||
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
    | CARI USER PEMBUAT
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
    | NOTIF USER
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

        if (!$userId) {
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
    | NOTIF MAINTENANCE
    |--------------------------------------------------------------------------
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
    | GENERATE NEXT WO NUMBER
    |--------------------------------------------------------------------------
    |
    | Format: WO-YYYY-NNNN
    |
    */

    private function generateNextWoNumber(): string
    {
        $allNos = WorkOrder::query()
            ->pluck('no_wo')
            ->filter(fn ($v) => $v !== '' && ctype_digit((string) $v))
            ->map(fn ($v) => (int) $v);

        $maxExisting = $allNos->max() ?? 0;
        $nextNumber = $maxExisting + 1;

        $padLen = max(3, strlen((string) $maxExisting));

        return str_pad(
            (string) $nextNumber,
            $padLen,
            '0',
            STR_PAD_LEFT
        );
    }
}