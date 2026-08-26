<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Inventory\Barang;
use App\Models\Inventory\BarangMasuk;
use App\Support\DepartmentAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BarangMasukController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $department = DepartmentAccess::requestedDepartmentFromRequest($request, $user);

        $query = BarangMasuk::with([
            'barang.satuan',
            'satuan',
            'receivedBy',
        ])
            ->whereHas('barang', function ($q) use ($user, $department) {
                DepartmentAccess::applyBarangScope($q, $user, $department);
            })
            ->orderByDesc('tanggal_masuk')
            ->orderByDesc('id');

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where(
                    'no_transaksi',
                    'like',
                    "%{$search}%"
                )->orWhereHas(
                    'barang',
                    function ($barangQuery) use ($search) {
                        $barangQuery
                            ->where(
                                'kode_barang',
                                'like',
                                "%{$search}%"
                            )
                            ->orWhere(
                                'nama_spesifikasi',
                                'like',
                                "%{$search}%"
                            );
                    }
                );
            });
        }

        if ($request->filled('tanggal_dari')) {
            $query->whereDate(
                'tanggal_masuk',
                '>=',
                $request->tanggal_dari
            );
        }

        if ($request->filled('tanggal_sampai')) {
            $query->whereDate(
                'tanggal_masuk',
                '<=',
                $request->tanggal_sampai
            );
        }

        $totalMasuk = (clone $query)
            ->reorder()
            ->where('status', 'RECEIVED')
            ->sum('qty');

        $jumlahTransaksi = (clone $query)
            ->reorder()
            ->count();

        $barangMasuks = $query
            ->paginate(15)
            ->withQueryString();

        $barangs = Barang::with('satuan')
            ->visibleTo($user, $department)
            ->orderBy('nama_spesifikasi')
            ->get();

        $nextNoTransaksi = sprintf(
            '%03d',
            (int) BarangMasuk::max('id') + 1
        );

        return view(
            'inventory.barang-masuk.index',
            compact(
                'barangMasuks',
                'totalMasuk',
                'jumlahTransaksi',
                'barangs',
                'nextNoTransaksi'
            )
        );
    }

    public function create()
    {
        return redirect()->route('barang-masuk.index');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal_masuk' => [
                'required',
                'date',
            ],

            'barang_id' => [
                'required',
                'integer',
                'exists:barangs,id',
            ],

            'qty' => [
                'required',
                'numeric',
                'gt:0',
            ],

            'supplier' => [
                'nullable',
                'string',
                'max:255',
            ],

            'no_faktur' => [
                'nullable',
                'string',
                'max:255',
            ],

            'harga' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'keterangan' => [
                'nullable',
                'string',
            ],
        ]);

        DB::transaction(function () use ($validated) {

            /*
            |--------------------------------------------------------------------------
            | KUNCI BARANG
            |--------------------------------------------------------------------------
            |
            | lockForUpdate mencegah dua transaksi bersamaan
            | membaca stok lama yang sama (stok bertambah ganda).
            |
            */

            $barang = Barang::query()
                ->lockForUpdate()
                ->findOrFail($validated['barang_id']);

            DepartmentAccess::assertCanAccessBarang(auth()->user(), $barang);

            /*
            |--------------------------------------------------------------------------
            | STOK LAMA + QTY MASUK = STOK BARU
            |--------------------------------------------------------------------------
            */

            $stokLama = (float) $barang->stok;

            $qtyMasuk = (float) $validated['qty'];

            $stokBaru = $stokLama + $qtyMasuk;

            BarangMasuk::create([
                'no_transaksi' => $this->generateNoTransaksi(),

                'tanggal_masuk' =>
                    $validated['tanggal_masuk'],

                'barang_id' => $barang->id,

                'qty' => $validated['qty'],

                // Satuan diambil dari master barang,
                // sehingga satuan_id dijamin konsisten.
                'satuan_id' => $barang->satuan_id,

                'supplier' =>
                    $validated['supplier'] ?? null,

                'no_faktur' =>
                    $validated['no_faktur'] ?? null,

                'harga' =>
                    $validated['harga'] ?? null,

                'keterangan' =>
                    $validated['keterangan'] ?? null,

                'status' => 'RECEIVED',

                'received_by' => auth()->id(),
            ]);

            $barang->update([
                'stok' => $stokBaru,
            ]);
        });

        return redirect()
            ->route('barang-masuk.index')
            ->with(
                'success',
                'Barang masuk berhasil disimpan dan stok diperbarui.'
            );
    }

    public function show(BarangMasuk $barangMasuk)
    {
        $barangMasuk->load([
            'barang.satuan',
            'satuan',
            'receivedBy',
        ]);

        return view(
            'inventory.barang-masuk.show',
            compact('barangMasuk')
        );
    }

    public function edit(BarangMasuk $barangMasuk)
    {
        DepartmentAccess::assertCanAccessBarang(auth()->user(), $barangMasuk->barang);

        $barangMasuk->load([
            'barang.satuan',
            'satuan',
        ]);

        return view(
            'inventory.barang-masuk.edit',
            compact('barangMasuk')
        );
    }

    public function update(
        Request $request,
        BarangMasuk $barangMasuk
    ) {
        DepartmentAccess::assertCanAccessBarang(auth()->user(), $barangMasuk->barang);

        $validated = $request->validate([
            'tanggal_masuk' => [
                'required',
                'date',
            ],

            'qty' => [
                'required',
                'numeric',
                'gt:0',
            ],

            'supplier' => [
                'nullable',
                'string',
                'max:255',
            ],

            'no_faktur' => [
                'nullable',
                'string',
                'max:255',
            ],

            'harga' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'keterangan' => [
                'nullable',
                'string',
            ],
        ]);

        DB::transaction(function () use ($validated, $barangMasuk) {

            if ($barangMasuk->status === 'CANCELLED') {
                abort(
                    422,
                    'Transaksi berstatus CANCELLED tidak dapat diubah.'
                );
            }

            $barang = Barang::query()
                ->lockForUpdate()
                ->findOrFail($barangMasuk->barang_id);

            /*
            |--------------------------------------------------------------------------
            | KOREKSI STOK SAAT QTY DIUBAH
            |--------------------------------------------------------------------------
            |
            | Stok saat ini sudah memuat qty lama.
            | Stok baru = stok sekarang - qty lama + qty baru.
            |
            */

            $qtyLama = (float) $barangMasuk->qty;

            $qtyBaru = (float) $validated['qty'];

            $stokBaru = (float) $barang->stok - $qtyLama + $qtyBaru;

            if ($stokBaru < 0) {
                abort(
                    422,
                    'Perubahan menyebabkan stok menjadi negatif. ' .
                    'Stok tersedia saat ini ' . $barang->stok .
                    ' (sudah termasuk qty lama ' . $qtyLama . ').'
                );
            }

            $barangMasuk->update([
                'tanggal_masuk' =>
                    $validated['tanggal_masuk'],

                'qty' => $validated['qty'],

                'supplier' =>
                    $validated['supplier'] ?? null,

                'no_faktur' =>
                    $validated['no_faktur'] ?? null,

                'harga' =>
                    $validated['harga'] ?? null,

                'keterangan' =>
                    $validated['keterangan']
                    ?? $barangMasuk->keterangan,
            ]);

            $barang->update([
                'stok' => $stokBaru,
            ]);
        });

        return redirect()
            ->route('barang-masuk.index')
            ->with(
                'success',
                'Barang masuk berhasil diperbarui dan stok disesuaikan.'
            );
    }

    public function destroy(BarangMasuk $barangMasuk)
    {
        DepartmentAccess::assertCanAccessBarang(auth()->user(), $barangMasuk->barang);

        DB::transaction(function () use ($barangMasuk) {

            /*
            |--------------------------------------------------------------------------
            | KEMBALIKAN STOK JIKA TRANSAKSI MASIH RECEIVED
            |--------------------------------------------------------------------------
            */

            if ($barangMasuk->status === 'RECEIVED') {

                $barang = Barang::query()
                    ->lockForUpdate()
                    ->find($barangMasuk->barang_id);

                if ($barang) {

                    $stokBaru =
                        (float) $barang->stok
                        - (float) $barangMasuk->qty;

                    if ($stokBaru < 0) {
                        abort(
                            422,
                            'Stok tidak mencukupi untuk membatalkan transaksi ini.'
                        );
                    }

                    $barang->update([
                        'stok' => $stokBaru,
                    ]);
                }
            }

            $barangMasuk->delete();
        });

        return redirect()
            ->route('barang-masuk.index')
            ->with(
                'success',
                'Barang masuk berhasil dihapus dan stok dikembalikan.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | GENERATE NOMOR TRANSAKSI
    |--------------------------------------------------------------------------
    |
    | BM-001, BM-002, dst.
    | Nomor mengikuti id terakhir agar unik
    | meski transaksi lama dihapus.
    |
    */

    private function generateNoTransaksi(): string
    {
        $lastId = (int) BarangMasuk::max('id');

        return sprintf('BM-%03d', $lastId + 1);
    }
}
