<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Inventory\Barang;
use App\Models\Inventory\Satuan;
use App\Support\DepartmentAccess;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BarangController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | DAFTAR BARANG
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $user = auth()->user();

        $department = DepartmentAccess::requestedDepartmentFromRequest(
            $request,
            $user
        );

        /*
        |--------------------------------------------------------------------------
        | DATA SATUAN UNTUK DROPDOWN
        |--------------------------------------------------------------------------
        */

        $satuans = Satuan::where('status', true)
            ->orderBy('nama')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | QUERY BARANG
        |--------------------------------------------------------------------------
        */

        $query = Barang::with('satuan')->visibleTo($user, $department);


        /*
        |--------------------------------------------------------------------------
        | PENCARIAN KODE / NAMA / SPESIFIKASI
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {

            $search = trim(
                $request->search
            );

            $query->where(function ($q) use ($search) {

                $q->where(
                    'kode_barang',
                    'like',
                    "%{$search}%"
                );

                $q->orWhere(
                    'nama_spesifikasi',
                    'like',
                    "%{$search}%"
                );

            });
        }


        /*
        |--------------------------------------------------------------------------
        | FILTER KONDISI STOK
        |--------------------------------------------------------------------------
        */

        if ($request->filled('kondisi')) {

            switch ($request->kondisi) {

                case 'TERSEDIA':

                    $query->whereColumn(
                        'stok',
                        '>',
                        'stok_minimum'
                    );

                    break;


                case 'MENIPIS':

                    $query
                        ->where(
                            'stok',
                            '>',
                            0
                        )
                        ->whereColumn(
                            'stok',
                            '<=',
                            'stok_minimum'
                        );

                    break;


                case 'HABIS':

                    $query->where(
                        'stok',
                        0
                    );

                    break;

            }
        }


        /*
        |--------------------------------------------------------------------------
        | FILTER STATUS MASTER
        |--------------------------------------------------------------------------
        */

        if ($request->filled('status')) {

            $query->where(
                'status',
                $request->status
            );
        }


        /*
        |--------------------------------------------------------------------------
        | DATA BARANG
        |--------------------------------------------------------------------------
        */

        $barangs = $query
            ->orderBy('nama_spesifikasi')
            ->orderBy('kode_barang')
            ->paginate(10)
            ->withQueryString();


        /*
        |--------------------------------------------------------------------------
        | RINGKASAN
        |--------------------------------------------------------------------------
        */

        $summaryQuery = Barang::query()->visibleTo($user, $department);

        $totalBarang = (clone $summaryQuery)->count();

        $totalTersedia = (clone $summaryQuery)->whereColumn(
            'stok',
            '>',
            'stok_minimum'
        )->count();

        $totalMenipis = (clone $summaryQuery)->where(
                'stok',
                '>',
                0
            )
            ->whereColumn(
                'stok',
                '<=',
                'stok_minimum'
            )
            ->count();

        $totalHabis = (clone $summaryQuery)->where(
            'stok',
            0
        )->count();


        /*
        |--------------------------------------------------------------------------
        | KIRIM KE VIEW
        |--------------------------------------------------------------------------
        */

        return view(
            'inventory.stok-barang.index',
            compact(
                'barangs',
                'satuans',
                'totalBarang',
                'totalTersedia',
                'totalMenipis',
                'totalHabis',
                'department'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | SIMPAN BARANG
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $validated = $request->validate([

            'kode_barang' => [
                'required',
                'string',
                'max:50',
                'unique:barangs,kode_barang',
            ],

            'nama_spesifikasi' => [
                'required',
                'string',
                'max:255',
            ],

            'satuan_id' => [
                'required',
                'exists:satuans,id',
            ],

            'stok' => [
                'required',
                'integer',
                'min:0',
            ],

            'stok_minimum' => [
                'required',
                'integer',
                'min:0',
            ],

            'lokasi_penyimpanan' => [
                'nullable',
                'string',
                'max:100',
            ],

            'status' => [
                'required',
                Rule::in([
                    'AKTIF',
                    'TIDAK AKTIF',
                ]),
            ],

            'department' => [
                'nullable',
                Rule::in(DepartmentAccess::departments()),
            ],

        ]);

        $user = auth()->user();
        $scoped = DepartmentAccess::scopedDepartment($user);

        if ($scoped) {
            $validated['department'] = $scoped;
        } else {
            $validated['department'] = $validated['department']
                ?? DepartmentAccess::MEKANIK_MAINT;
        }

        DepartmentAccess::assertCanAccessDepartment(
            $user,
            $validated['department']
        );


        Barang::create(
            $validated
        );


        return redirect()
            ->route(
                'barang.index'
            )
            ->with(
                'success',
                'Barang berhasil ditambahkan.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE BARANG
    |--------------------------------------------------------------------------
    */

    public function update(
        Request $request,
        Barang $barang
    ) {
        $validated = $request->validate([

            'kode_barang' => [
                'required',
                'string',
                'max:50',

                Rule::unique(
                    'barangs',
                    'kode_barang'
                )->ignore(
                    $barang->id
                ),
            ],

            'nama_spesifikasi' => [
                'required',
                'string',
                'max:255',
            ],

            'satuan_id' => [
                'required',
                'exists:satuans,id',
            ],

            'stok' => [
                'required',
                'integer',
                'min:0',
            ],

            'stok_minimum' => [
                'required',
                'integer',
                'min:0',
            ],

            'lokasi_penyimpanan' => [
                'nullable',
                'string',
                'max:100',
            ],

            'status' => [
                'required',
                Rule::in([
                    'AKTIF',
                    'TIDAK AKTIF',
                ]),
            ],

            'department' => [
                'nullable',
                Rule::in(DepartmentAccess::departments()),
            ],

        ]);

        DepartmentAccess::assertCanAccessBarang(auth()->user(), $barang);

        $user = auth()->user();
        $scoped = DepartmentAccess::scopedDepartment($user);

        if ($scoped) {
            $validated['department'] = $scoped;
        } else {
            $validated['department'] = $validated['department']
                ?? $barang->department
                ?? DepartmentAccess::MEKANIK_MAINT;
        }

        DepartmentAccess::assertCanAccessDepartment(
            $user,
            $validated['department']
        );


        $barang->update(
            $validated
        );


        return redirect()
            ->route(
                'barang.index'
            )
            ->with(
                'success',
                'Barang berhasil diperbarui.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | HAPUS BARANG
    |--------------------------------------------------------------------------
    */

    public function destroy(
        Barang $barang
    ) {
        DepartmentAccess::assertCanAccessBarang(auth()->user(), $barang);

        $barang->delete();


        return redirect()
            ->route(
                'barang.index'
            )
            ->with(
                'success',
                'Barang berhasil dihapus.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | DATA RE-STOCK
    |--------------------------------------------------------------------------
    */

    public function restock()
    {
        $user = auth()->user();

        $barangs = Barang::with('satuan')
            ->visibleTo($user)
            ->where(function ($query) {

                $query
                    ->where('stok', 0)
                    ->orWhereColumn(
                        'stok',
                        '<=',
                        'stok_minimum'
                    );

            })
            ->orderBy('stok')
            ->orderBy('nama_spesifikasi')
            ->get();


        return view(
            'inventory.stok-barang.restock',
            compact('barangs')
        );
    }
}