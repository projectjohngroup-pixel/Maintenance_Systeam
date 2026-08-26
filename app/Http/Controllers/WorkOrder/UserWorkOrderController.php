<?php

namespace App\Http\Controllers\WorkOrder;

use App\Http\Controllers\Controller;
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
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class UserWorkOrderController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    |
    | Menampilkan SELURUH Work Order.
    |
    */

    public function index()
    {
        $workOrders = WorkOrder::query()
            ->orderByDesc('created_at')
            ->get();

        return view(
            'work-orders.user.index',
            compact('workOrders')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
    |
    | Hanya USER yang dapat membuat WO.
    |
    */

    public function create()
    {
        $this->ensureUserCanCreate();

        $nextNoWo =
            $this->generateNextWoNumber();

        $areas = Area::query()
            ->orderBy('nama_area')
            ->get([
                'id',
                'nama_area',
            ]);

        $machines = Machine::query()
            ->orderBy('nama_mesin')
            ->get([
                'id',
                'kode_mesin',
                'nama_mesin',
                'area_id',
            ]);

        return view(
            'work-orders.user.create',
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

    public function store(Request $request)
    {
        $this->ensureUserCanCreate();

        $validated = $this->validateUserWorkOrder(
            $request
        );

        $this->validateAreaAndMachine(
            $validated['area'] ?? null,
            $validated['mesin'] ?? null
        );

        $user = auth()->user();


        $fotoKerusakan =
            $this->storeDamagePhotos(
                $request
            );


        DB::beginTransaction();

        try {

            $data = [

                'no_wo' =>
                    $validated['no_wo'],

                'tanggal_kerusakan' =>
                    $validated['tanggal_kerusakan'],

                'jam_kerusakan' =>
                    $validated['jam_kerusakan'] ?? null,

                'departemen' =>
                    $user->bagian ?? null,

                'tujuan' =>
                    $validated['tujuan'],

                'assigned_department' =>
                    DepartmentAccess::fromTujuan($validated['tujuan']),

                'priority' =>
                    $validated['priority'],

                'kategori' =>
                    $validated['kategori'],

                'area' =>
                    $validated['area'] ?? null,

                'mesin' =>
                    $validated['mesin'] ?? null,

                'job' =>
                    $validated['job'],

                'deskripsi' =>
                    $validated['deskripsi'],

                'status' =>
                    'OPEN',

                'dibuat_oleh' =>
                    $user->name ?? null,

                'keterangan' =>
                    null,

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

                'foto_kerusakan' =>
                    null,
            ];


            if (!empty($fotoKerusakan)) {

                $data['foto_kerusakan'] =
                    $fotoKerusakan;

                $data['foto'] =
                    $fotoKerusakan[0];
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
                'WO Baru',
                'WO ' .
                $workOrder->no_wo .
                ' baru masuk untuk ' .
                DepartmentAccess::label(
                    DepartmentAccess::fromTujuan($workOrder->tujuan)
                ) .
                '.'
            );


            $this->notifyUser(
                $user->id,
                $workOrder->id,
                'WO_CREATED',
                'Work Order Berhasil Dikirim',
                'WO ' .
                $workOrder->no_wo .
                ' berhasil dikirim dan menunggu tindak lanjut Maintenance.',
                $deadline
            );


            DB::commit();

        } catch (\Throwable $e) {

            DB::rollBack();

            if (
                $fotoKerusakan &&
                is_array($fotoKerusakan)
            ) {

                foreach ($fotoKerusakan as $path) {

                    if ($path && Storage::disk('public')->exists($path)) {

                        Storage::disk('public')->delete($path);
                    }
                }
            }

            $errorCode = ((string) $e->getCode() === '23000' || str_contains($e->getMessage(), 'Duplicate') || str_contains($e->getMessage(), 'UNIQUE'))
                ? 'Data gagal disimpan. Nomor WO sudah digunakan.'
                : 'Data gagal disimpan karena koneksi/database bermasalah.';

            return back()
                ->withInput()
                ->withErrors([
                    'status' => $errorCode,
                ]);
        }


        return redirect()
            ->route(
                'work-orders.index'
            )
            ->with(
                'success',
                'Work Order berhasil disimpan.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | SHOW
    |--------------------------------------------------------------------------
    |
    | Detail tetap hanya untuk WO milik User.
    |
    */

    public function show($id)
    {
        $workOrder =
            WorkOrder::findOrFail(
                $id
            );

        $this->ensureOwner(
            $workOrder,
            'Anda tidak memiliki akses ke Work Order ini.'
        );

        return view(
            'work-orders.user.show',
            compact('workOrder')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | EDIT
    |--------------------------------------------------------------------------
    */

    public function edit($id)
    {
        $workOrder =
            WorkOrder::findOrFail(
                $id
            );

        $this->ensureOwner(
            $workOrder,
            'Anda hanya boleh mengubah Work Order milik sendiri.'
        );

        $areas =
            Area::query()
                ->orderBy('nama_area')
                ->get([
                    'id',
                    'nama_area',
                ]);

        $machines =
            Machine::query()
                ->orderBy('nama_mesin')
                ->get([
                    'id',
                    'kode_mesin',
                    'nama_mesin',
                    'area_id',
                ]);

        return view(
            'work-orders.user.edit',
            compact(
                'workOrder',
                'areas',
                'machines'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE
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

        $this->ensureOwner(
            $workOrder,
            'Anda hanya boleh mengubah Work Order milik sendiri.'
        );

        $validated =
            $this->validateUserWorkOrder(
                $request,
                $workOrder->id
            );

        $this->validateAreaAndMachine(
            $validated['area'] ?? null,
            $validated['mesin'] ?? null
        );

        $fotoKerusakan =
            $this->storeDamagePhotos(
                $request
            );

        DB::beginTransaction();

        try {

            $workOrder->update([

                'no_wo' =>
                    $validated['no_wo'],

                'tanggal_kerusakan' =>
                    $validated['tanggal_kerusakan'],

                'jam_kerusakan' =>
                    $validated['jam_kerusakan'] ?? null,

                'kategori' =>
                    $validated['kategori'],

                'tujuan' =>
                    $validated['tujuan'],

                'assigned_department' =>
                    DepartmentAccess::fromTujuan($validated['tujuan']),

                'area' =>
                    $validated['area'] ?? null,

                'mesin' =>
                    $validated['mesin'] ?? null,

                'job' =>
                    $validated['job'],

                'deskripsi' =>
                    $validated['deskripsi'],

                'priority' =>
                    $validated['priority'],
            ]);


            if (!empty($fotoKerusakan)) {

                $workOrder->update([

                    'foto_kerusakan' =>
                        $fotoKerusakan,

                    'foto' =>
                        $fotoKerusakan[0],

                ]);
            }

            $workOrder->refresh();

            $deadline =
                $this->getDeadline(
                    $workOrder
                );

            $this->notifyMaintenanceTeam(
                $workOrder,
                $deadline,
                'WO_UPDATED',
                'Work Order Diperbarui',
                'WO ' .
                $workOrder->no_wo .
                ' telah diperbarui oleh User.'
            );

            $this->notifyUser(
                auth()->id(),
                $workOrder->id,
                'WO_UPDATED',
                'Work Order Diperbarui',
                'Permintaan WO ' .
                $workOrder->no_wo .
                ' berhasil diperbarui.',
                $deadline
            );

            DB::commit();

        } catch (\Throwable $e) {

            DB::rollBack();

            if (
                $fotoKerusakan &&
                is_array($fotoKerusakan)
            ) {

                foreach ($fotoKerusakan as $path) {

                    if ($path && Storage::disk('public')->exists($path)) {

                        Storage::disk('public')->delete($path);
                    }
                }
            }

            return back()
                ->withInput()
                ->withErrors([
                    'status' =>
                        'Gagal memperbarui Work Order: ' .
                        $e->getMessage(),
                ]);
        }


        return redirect()
            ->route(
                'work-orders.index'
            )
            ->with(
                'success',
                'Permintaan Work Order berhasil diperbarui.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | DESTROY
    |--------------------------------------------------------------------------
    |
    | WO langsung dihapus dari database.
    |
    | Tidak ada pengecekan owner.
    |
    */

    public function destroy($id)
    {
        $workOrder =
            WorkOrder::findOrFail(
                $id
            );


        /*
        |--------------------------------------------------------------------------
        | GUARD HAPUS
        |--------------------------------------------------------------------------
        |
        | Administrator boleh menghapus semua WO.
        | Role lain hanya boleh menghapus WO milik sendiri.
        |
        */

        $role =
            \App\Support\DepartmentAccess::normalizeRole(
                auth()->user()->role ?? ''
            );

        if (
            !in_array(
                $role,
                [
                    \App\Support\DepartmentAccess::ADMINISTRATOR,
                ],
                true
            )
        ) {

            $this->ensureOwner(
                $workOrder,
                'Anda hanya boleh menghapus Work Order milik sendiri.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | HAPUS NOTIFIKASI TERKAIT
        |--------------------------------------------------------------------------
        */

        Notification::where(
            'work_order_id',
            $workOrder->id
        )->delete();


        /*
        |--------------------------------------------------------------------------
        | HAPUS STATUS HISTORY
        |--------------------------------------------------------------------------
        */

        WorkOrderStatusHistory::where(
            'work_order_id',
            $workOrder->id
        )->delete();


        /*
        |--------------------------------------------------------------------------
        | HAPUS FOTO DARI STORAGE
        |--------------------------------------------------------------------------
        */

        $fotoKerusakan =
            $workOrder->foto_kerusakan;

        if (
            is_array($fotoKerusakan)
        ) {

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


        /*
        |--------------------------------------------------------------------------
        | HAPUS WO
        |--------------------------------------------------------------------------
        */

        $workOrder->delete();


        return redirect()
            ->route(
                'work-orders.index'
            )
            ->with(
                'success',
                'Work Order berhasil dihapus.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | CEK ROLE USER
    |--------------------------------------------------------------------------
    */

    private function ensureUserCanCreate(): void
    {
        $role =
            \App\Support\DepartmentAccess::normalizeRole(
                auth()->user()->role ?? null
            );

        if ($role !== \App\Support\DepartmentAccess::PRODUKSI) {

            abort(
                403,
                'Hanya Produksi yang dapat membuat permintaan Work Order.'
            );
        }
    }


    /*
    |--------------------------------------------------------------------------
    | CEK PEMILIK
    |--------------------------------------------------------------------------
    */

    private function ensureOwner(
        WorkOrder $workOrder,
        string $message
    ): void {

        $userName =
            trim(
                (string) (
                    auth()->user()->name ?? ''
                )
            );

        $creatorName =
            trim(
                (string) (
                    $workOrder->dibuat_oleh ?? ''
                )
            );

        if (
            $creatorName === '' ||
            $userName === '' ||
            strcasecmp(
                $creatorName,
                $userName
            ) !== 0
        ) {

            abort(
                403,
                $message
            );
        }
    }


    /*
    |--------------------------------------------------------------------------
    | VALIDASI
    |--------------------------------------------------------------------------
    */

    private function validateUserWorkOrder(
        Request $request,
        ?int $workOrderId = null
    ): array {

        $uniqueNoWo =
            'unique:work_orders,no_wo';

        if ($workOrderId !== null) {

            $uniqueNoWo .=
                ',' . $workOrderId;
        }

        return $request->validate(
            [

                'no_wo' => [
                    'required',
                    'string',
                    'max:255',
                    $uniqueNoWo,
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

                'foto' => [
                    'nullable',
                    'array',
                ],

                'foto.*' => [
                    'image',
                    'max:5120',
                ],

            ],
            [

                'no_wo.required' =>
                    'No. WO wajib diisi.',

                'no_wo.unique' =>
                    'Nomor WO sudah digunakan. Silakan gunakan nomor lain.',

                'tanggal_kerusakan.required' =>
                    'Tanggal Kerusakan wajib diisi.',

                'tanggal_kerusakan.date' =>
                    'Tanggal Kerusakan tidak valid.',

                'jam_kerusakan.date_format' =>
                    'Format Jam Kerusakan harus HH:MM.',

                'kategori.required' =>
                    'Kategori wajib dipilih.',

                'tujuan.required' =>
                    'Ditujukan wajib dipilih.',

                'job.required' =>
                    'Job wajib diisi.',

                'deskripsi.required' =>
                    'Deskripsi wajib diisi.',

                'priority.required' =>
                    'Prioritas wajib dipilih.',

                'foto.*.image' =>
                    'File foto harus berupa gambar.',

                'foto.*.max' =>
                    'Ukuran setiap foto maksimal 5 MB.',
            ]
        );
    }


    /*
    |--------------------------------------------------------------------------
    | VALIDASI AREA + MESIN
    |--------------------------------------------------------------------------
    */

    private function validateAreaAndMachine(
        ?string $areaName,
        ?string $machineName
    ): array {

        $area = null;
        $machine = null;

        $areaName =
            trim(
                (string) $areaName
            );

        $machineName =
            trim(
                (string) $machineName
            );


        if ($areaName !== '') {

            $area =
                Area::whereRaw(
                    'LOWER(TRIM(nama_area)) = ?',
                    [
                        strtolower(
                            $areaName
                        ),
                    ]
                )
                ->first();

            if (!$area) {

                throw ValidationException::withMessages([
                    'area' =>
                        'Area yang dipilih tidak ditemukan di Master Area.',
                ]);
            }
        }


        if ($machineName !== '') {

            if (!$area) {

                throw ValidationException::withMessages([
                    'mesin' =>
                        'Area harus dipilih jika Mesin diisi.',
                ]);
            }


            $machine =
                Machine::whereRaw(
                    'LOWER(TRIM(nama_mesin)) = ?',
                    [
                        strtolower(
                            $machineName
                        ),
                    ]
                )
                ->where(
                    'area_id',
                    $area->id
                )
                ->first();


            if (!$machine) {

                throw ValidationException::withMessages([
                    'mesin' =>
                        'Mesin yang dipilih tidak berada pada Area tersebut.',
                ]);
            }
        }


        return [
            $area,
            $machine,
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | SIMPAN FOTO
    |--------------------------------------------------------------------------
    */

    private function storeDamagePhotos(
        Request $request
    ): array {

        if (!$request->hasFile('foto')) {

            return [];
        }

        $paths = [];

        foreach (
            (array) $request->file('foto')
            as $file
        ) {

            if (
                $file &&
                $file->isValid()
            ) {

                $paths[] =
                    $file->store(
                        'work-orders/foto/kerusakan',
                        'public'
                    );
            }
        }

        return $paths;
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
            $this->normalize(
                $workOrder->priority ?? null
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
    | GENERATE NEXT WO NUMBER
    |--------------------------------------------------------------------------
    |
    | Format: WO-YYYY-NNNN
    |
    */


    /*
    |--------------------------------------------------------------------------
    | CHECK NO WO AVAILABILITY
    |--------------------------------------------------------------------------
    */

    public function checkNoWo(Request $request)
    {
        $noWo = trim($request->input('no_wo', ''));

        if ($noWo === '') {
            return response()->json([
                'available' => false,
                'message'   => 'No. WO tidak boleh kosong.',
            ]);
        }

        $exists = WorkOrder::query()
            ->where('no_wo', $noWo)
            ->exists();

        return response()->json([
            'available' => !$exists,
            'message'   => $exists
                ? 'Nomor WO ' . $noWo . ' sudah digunakan.'
                : 'Nomor WO ' . $noWo . ' tersedia.',
        ]);
    }


    private function generateNextWoNumber(): string
    {
        $allNos = WorkOrder::query()
            ->lockForUpdate()
            ->pluck('no_wo')
            ->filter(fn ($v) => $v !== '' && ctype_digit((string) $v))
            ->map(fn ($v) => (int) $v);

        $maxExisting = $allNos->max() ?? 0;
        $nextNumber = $maxExisting + 1;

        $padLen = max(3, strlen((string) $maxExisting));

        do {
            $candidate = str_pad(
                (string) $nextNumber,
                $padLen,
                '0',
                STR_PAD_LEFT
            );

            $exists = WorkOrder::query()
                ->where('no_wo', $candidate)
                ->exists();

            $nextNumber++;
        } while ($exists);

        return $candidate;
    }


    /*
    |--------------------------------------------------------------------------
    | NORMALIZE
    |--------------------------------------------------------------------------
    */

    private function normalize(
        ?string $value
    ): string {

        return strtoupper(
            trim(
                (string) $value
            )
        );
    }
}