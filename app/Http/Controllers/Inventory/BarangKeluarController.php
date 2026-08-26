<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Inventory\Barang;
use App\Models\Inventory\BarangKeluar;
use App\Models\Machine\Area;
use App\Models\Machine\Machine;
use App\Models\WorkOrder\WorkOrder;
use App\Support\DepartmentAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class BarangKeluarController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $department = DepartmentAccess::requestedDepartmentFromRequest($request, $user);

        $bucket = $department
            ? DepartmentAccess::toBucket($department)
            : ($request->get('bucket', 'all'));

        $query = BarangKeluar::with([
            'barang',
            'area',
            'machine',
        ])
            ->whereHas('barang', function ($q) use ($user, $department) {
                DepartmentAccess::applyBarangScope($q, $user, $department);
            })
            ->latest('tanggal_keluar')
            ->latest('id');

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('no_transaksi', 'like', "%{$search}%")
                    ->orWhere('no_wo', 'like', "%{$search}%")
                    ->orWhereHas('barang', function ($barangQuery) use ($search) {
                        $barangQuery
                            ->where('kode_barang', 'like', "%{$search}%")
                            ->orWhere(
                                'nama_spesifikasi',
                                'like',
                                "%{$search}%"
                            );
                    });
            });
        }

        if ($request->filled('tanggal_dari')) {
            $query->whereDate(
                'tanggal_keluar',
                '>=',
                $request->tanggal_dari
            );
        }

        if ($request->filled('tanggal_sampai')) {
            $query->whereDate(
                'tanggal_keluar',
                '<=',
                $request->tanggal_sampai
            );
        }

        if ($request->filled('dipakai_oleh')) {
            $query->where(
                'dipakai_oleh',
                $request->dipakai_oleh
            );
        }

        $jumlahTransaksi = (clone $query)->count();

        $totalKeluar = (clone $query)->sum('qty');

        $query = BarangKeluar::query()
            ->whereMonth(
                'tanggal_keluar',
                now()->month
            )
            ->whereYear(
                'tanggal_keluar',
                now()->year
            )
            ->whereHas('barang', function ($q) use ($user, $department) {
                DepartmentAccess::applyBarangScope($q, $user, $department);
            });

        $transaksiBulanIni = $query->count();

        $barangKeluars = $query
            ->paginate(20)
            ->withQueryString();

        return view(
            'inventory.barang-keluar.index',
            compact(
                'barangKeluars',
                'jumlahTransaksi',
                'totalKeluar',
                'transaksiBulanIni',
                'bucket'
            )
        );
    }

    public function create(Request $request)
    {
        $user = auth()->user();
        $department = DepartmentAccess::requestedDepartmentFromRequest($request, $user);

        $bucket = $department
            ? DepartmentAccess::toBucket($department)
            : ($request->get('bucket', 'all'));

        $areas = Area::query()
            ->orderBy('nama_area')
            ->get();

        $barangs = Barang::with('satuan')
            ->visibleTo($user, $department)
            ->where('status', 'AKTIF')
            ->orderBy('nama_spesifikasi')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | WORK ORDER
        |--------------------------------------------------------------------------
        |
        | Mengambil data Work Order untuk pilihan No WO
        | pada form Barang Keluar.
        |
        */

        $workOrders = WorkOrder::query()
            ->orderByDesc('created_at')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | NOMOR TRANSAKSI BERIKUTNYA
        |--------------------------------------------------------------------------
        */

        $nextNoTransaksi = $this->generateNoTransaksi();

        return view(
            'inventory.barang-keluar.create',
            compact(
                'areas',
                'barangs',
                'workOrders',
                'bucket',
                'nextNoTransaksi'
            )
        );
    }

    public function machinesByArea($areaId)
    {
        $machines = Machine::query()
            ->where('area_id', $areaId)
            ->where('status', 'Aktif')
            ->orderBy('nama_mesin')
            ->get([
                'id',
                'kode_mesin',
                'nama_mesin',
            ])
            ->map(fn ($machine) => [
                'id' => $machine->id,
                'code' => $machine->kode_mesin,
                'name' => $machine->nama_mesin,
            ]);

        return response()->json($machines);
    }

    public function barang($id)
    {
        $barang = Barang::with('satuan')
            ->findOrFail($id);

        DepartmentAccess::assertCanAccessBarang(auth()->user(), $barang);

        return response()->json([
            'id' => $barang->id,
            'kode_barang' => $barang->kode_barang,
            'nama_spesifikasi' => $barang->nama_spesifikasi,
            'stok' => (int) $barang->stok,
            'satuan' => $barang->satuan?->nama ?? '-',
            'stok_minimum' => (int) $barang->stok_minimum,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([

            'bucket' => [
                'nullable',
                Rule::in([
                    'me_prev',
                    'prev',
                ]),
            ],

            'tanggal_keluar' => [
                'required',
                'date',
            ],

            'area_id' => [
                'nullable',
                'integer',
                'exists:areas,id',
            ],

            'machine_id' => [
                'nullable',
                'integer',
                'exists:mesins,id',
            ],

            'barang_id' => [
                'required',
                'integer',
                'exists:barangs,id',
            ],

            'qty' => [
                'required',
                'integer',
                'min:1',
            ],

            'dipakai_oleh' => [
                'required',
                Rule::in([
                    'ME_PREV',
                    'PREV',
                    'SIPIL',
                ]),
            ],

            'no_wo' => [
                'nullable',
                'string',
                'max:100',
            ],

            'keterangan' => [
                'nullable',
                'string',
            ],
        ]);

        $bucket = $validated['bucket'] ?? 'me_prev';

        $barangKeluar = DB::transaction(function () use ($validated) {

            $barang = Barang::query()
                ->lockForUpdate()
                ->findOrFail(
                    $validated['barang_id']
                );

            DepartmentAccess::assertCanAccessBarang(auth()->user(), $barang);

            $stokAwal = (int) $barang->stok;

            $qty = (int) $validated['qty'];

            if ($qty > $stokAwal) {
                abort(
                    422,
                    "Stok {$barang->nama_spesifikasi} tidak mencukupi. " .
                    "Stok tersedia {$stokAwal} {$barang->satuan?->nama}."
                );
            }

            $areaId = $validated['area_id'] ?? null;

            $machineId = $validated['machine_id'] ?? null;

            if (!$areaId) {

                $machineId = null;

            } elseif ($machineId) {

                $machineExists = Machine::query()
                    ->where('id', $machineId)
                    ->where('area_id', $areaId)
                    ->exists();

                if (!$machineExists) {
                    abort(
                        422,
                        'Mesin yang dipilih tidak berada pada Area / Line tersebut.'
                    );
                }
            }

            $sisaStok = $stokAwal - $qty;

            /*
            |--------------------------------------------------------------------------
            | NOMOR BK
            |--------------------------------------------------------------------------
            */

            $noTransaksi = $this->generateNoTransaksi();

            /*
            |--------------------------------------------------------------------------
            | SIMPAN
            |--------------------------------------------------------------------------
            */

            $transaksi = BarangKeluar::create([

                'no_transaksi' => $noTransaksi,

                'tanggal_keluar' =>
                    $validated['tanggal_keluar'],

                'area_id' =>
                    $areaId,

                'machine_id' =>
                    $machineId,

                'barang_id' =>
                    $barang->id,

                'qty' =>
                    $qty,

                'satuan' =>
                    $barang->satuan?->nama ?? '-',

                'stok_awal' =>
                    $stokAwal,

                'sisa_stok' =>
                    $sisaStok,

                'dipakai_oleh' =>
                    $validated['dipakai_oleh'],

                'no_wo' =>
                    $validated['no_wo'] ?? null,

                'keterangan' =>
                    $validated['keterangan'] ?? null,

                'status' =>
                    'RECEIVED',

                'user_id' =>
                    auth()->id(),
            ]);

            $barang->update([
                'stok' => $sisaStok,
            ]);

            return $transaksi;
        });

        return redirect()
            ->route(
                'barang-keluar.index',
                [
                    'bucket' => $bucket,
                ]
            )
            ->with(
                'success',
                "Barang Keluar {$barangKeluar->no_transaksi} berhasil disimpan."
            );
    }

    public function show(BarangKeluar $barangKeluar)
    {
        $barangKeluar->load([
            'barang.satuan',
            'area',
            'machine',
        ]);

        return view(
            'inventory.barang-keluar.show',
            compact('barangKeluar')
        );
    }

    public function cancel(
        Request $request,
        BarangKeluar $barangKeluar
    ) {
        $bucket = $request->get(
            'bucket',
            'me_prev'
        );

        if (!in_array($bucket, ['me_prev', 'prev'], true)) {
            $bucket = 'me_prev';
        }

        DB::transaction(function () use ($barangKeluar) {

            if ($barangKeluar->status === 'CANCELLED') {
                return;
            }

            $barang = Barang::query()
                ->lockForUpdate()
                ->findOrFail(
                    $barangKeluar->barang_id
                );

            $barang->increment(
                'stok',
                $barangKeluar->qty
            );

            $barangKeluar->update([
                'status' => 'CANCELLED',
            ]);
        });

        return redirect()
            ->route(
                'barang-keluar.index',
                [
                    'bucket' => $bucket,
                ]
            )
            ->with(
                'success',
                "Transaksi {$barangKeluar->no_transaksi} dibatalkan dan stok dikembalikan."
            );
    }

    /*
    |--------------------------------------------------------------------------
    | GENERATE NOMOR TRANSAKSI
    |--------------------------------------------------------------------------
    |
    | BK-001
    | BK-002
    | BK-003
    |
    */

    private function generateNoTransaksi(): string
    {
        $last = BarangKeluar::query()
            ->where('no_transaksi', 'like', 'BK-%')
            ->orderByDesc('id')
            ->value('no_transaksi');

        if (!$last) {
            return 'BK-001';
        }

        $lastNumber = (int) str_replace(
            'BK-',
            '',
            $last
        );

        return 'BK-' . str_pad(
            $lastNumber + 1,
            3,
            '0',
            STR_PAD_LEFT
        );
    }
}