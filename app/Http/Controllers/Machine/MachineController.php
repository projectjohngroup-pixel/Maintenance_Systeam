<?php

namespace App\Http\Controllers\Machine;

use App\Http\Controllers\Controller;
use App\Models\Machine\Area;
use App\Models\Machine\Machine;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MachineController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | DAFTAR MESIN
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | SEMUA AREA UNTUK DROPDOWN
        |--------------------------------------------------------------------------
        */

        $allAreas = Area::orderBy('nama_area')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | QUERY MESIN
        |--------------------------------------------------------------------------
        */

        $query = Machine::with('area');


        /*
        |--------------------------------------------------------------------------
        | FILTER PENCARIAN (KODE / NAMA)
        |--------------------------------------------------------------------------
        */

        if ($request->filled('q')) {

            $q = trim($request->q);

            $query->where(function ($sub) use ($q) {

                $sub->where(
                    'kode_mesin',
                    'like',
                    '%' . $q . '%'
                )->orWhere(
                    'nama_mesin',
                    'like',
                    '%' . $q . '%'
                );

            });
        }


        /*
        |--------------------------------------------------------------------------
        | FILTER AREA
        |--------------------------------------------------------------------------
        */

        if ($request->filled('area_id')) {

            $query->where(
                'area_id',
                $request->area_id
            );
        }


        /*
        |--------------------------------------------------------------------------
        | FILTER STATUS
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
        | DATA MESIN
        |--------------------------------------------------------------------------
        | Blade memakai $mesins->firstItem() dan $mesins->links()
        | sehingga wajib paginate().
        */

        $mesins = $query
            ->orderBy('nama_mesin')
            ->orderBy('kode_mesin')
            ->paginate(15)
            ->withQueryString();


        /*
        |--------------------------------------------------------------------------
        | RINGKASAN
        |--------------------------------------------------------------------------
        */

        $totalMesin = Machine::count();

        $totalAktif = Machine::where(
            'status',
            'Aktif'
        )->count();

        $totalTidakAktif = Machine::where(
            'status',
            'Tidak Aktif'
        )->count();

        $totalKw = Machine::sum('kw');


        /*
        |--------------------------------------------------------------------------
        | KIRIM KE VIEW
        |--------------------------------------------------------------------------
        */

        return view(
            'master.mesin.index',
            compact(
                'mesins',
                'allAreas',
                'totalMesin',
                'totalAktif',
                'totalTidakAktif',
                'totalKw'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | FORM TAMBAH
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        return redirect()->route('machines.index');
    }


    /*
    |--------------------------------------------------------------------------
    | SIMPAN MESIN
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $validated = $request->validate([

            'kode_mesin' => [
                'required',
                'string',
                'max:100',
                'unique:mesins,kode_mesin',
            ],

            'nama_mesin' => [
                'required',
                'string',
                'max:255',
            ],

            /*
            | Area tidak wajib diisi.
            */

            'area_id' => [
                'nullable',
                'exists:areas,id',
            ],

            'spesifikasi' => [
                'nullable',
                'string',
            ],

            'kw' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'status' => [
                'required',
                Rule::in([
                    'Aktif',
                    'Tidak Aktif',
                ]),
            ],

        ]);


        Machine::create(
            $validated
        );


        return redirect()
            ->route('machines.index')
            ->with(
                'success',
                'Mesin berhasil ditambahkan.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | DETAIL MESIN
    |--------------------------------------------------------------------------
    */

    public function show(Machine $machine)
    {
        return redirect()->route('machines.index');
    }


    /*
    |--------------------------------------------------------------------------
    | FORM EDIT
    |--------------------------------------------------------------------------
    */

    public function edit(Machine $machine)
    {
        return redirect()->route('machines.index');
    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE MESIN
    |--------------------------------------------------------------------------
    */

    public function update(
        Request $request,
        Machine $machine
    ) {
        $validated = $request->validate([

            'kode_mesin' => [
                'required',
                'string',
                'max:100',

                Rule::unique(
                    'mesins',
                    'kode_mesin'
                )->ignore(
                    $machine->id
                ),
            ],

            'nama_mesin' => [
                'required',
                'string',
                'max:255',
            ],

            'area_id' => [
                'nullable',
                'exists:areas,id',
            ],

            'spesifikasi' => [
                'nullable',
                'string',
            ],

            'kw' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'status' => [
                'required',
                Rule::in([
                    'Aktif',
                    'Tidak Aktif',
                ]),
            ],

        ]);


        $machine->update(
            $validated
        );


        return redirect()
            ->route('machines.index')
            ->with(
                'success',
                'Data mesin berhasil diperbarui.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | HAPUS MESIN
    |--------------------------------------------------------------------------
    */

    public function destroy(Machine $machine)
    {
        $machine
            ->spareparts()
            ->delete();


        $machine->delete();


        return redirect()
            ->route('machines.index')
            ->with(
                'success',
                'Mesin berhasil dihapus.'
            );
    }
}