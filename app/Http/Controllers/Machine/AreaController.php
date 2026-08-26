<?php

namespace App\Http\Controllers\Machine;

use App\Http\Controllers\Controller;
use App\Models\Machine\Area;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AreaController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | DAFTAR AREA / LINE
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        /*
        | Semua Area
        */

        $allAreas = Area::orderBy('nama_area')
            ->get();


        /*
        | Query Area
        */

        $query = Area::withCount('machines')
            ->withSum('machines', 'kw');


        /*
        | Filter Pencarian
        */

        if ($request->filled('q')) {

            $q = trim($request->q);

            $query->where(function ($sub) use ($q) {

                $sub->where(
                    'nama_area',
                    'like',
                    '%' . $q . '%'
                )->orWhere(
                    'keterangan',
                    'like',
                    '%' . $q . '%'
                );

            });
        }


        /*
        | Filter Area
        */

        if ($request->filled('area_id')) {

            $query->where(
                'id',
                $request->area_id
            );
        }


        /*
        | Data Area
        */

        $areas = $query
            ->orderBy('nama_area')
            ->get();


        /*
        | Total Mesin
        */

        $totalMachines = $areas->sum(
            'machines_count'
        );


        /*
        | Total KW
        */

        $totalKw = $areas->sum(
            fn ($area) =>
                (float) (
                    $area->machines_sum_kw ?? 0
                )
        );


        return view(
            'master.area.index',
            compact(
                'areas',
                'allAreas',
                'totalMachines',
                'totalKw'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | FORM TAMBAH AREA
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        return redirect()->route('areas.index');
    }


    /*
    |--------------------------------------------------------------------------
    | SIMPAN AREA
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $validated = $request->validate([

            'nama_area' => [
                'required',
                'string',
                'max:255',
                'unique:areas,nama_area',
            ],

            'keterangan' => [
                'nullable',
                'string',
            ],

        ]);


        Area::create(
            $validated
        );


        return redirect()
            ->route('areas.index')
            ->with(
                'success',
                'Area / Line berhasil ditambahkan.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | DETAIL AREA
    |--------------------------------------------------------------------------
    */

    public function show(Area $area)
    {
        return redirect()->route('areas.index');
    }


    /*
    |--------------------------------------------------------------------------
    | FORM EDIT AREA
    |--------------------------------------------------------------------------
    */

    public function edit(Area $area)
    {
        return redirect()->route('areas.index');
    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE AREA
    |--------------------------------------------------------------------------
    */

    public function update(
        Request $request,
        Area $area
    ) {
        $validated = $request->validate([

            'nama_area' => [
                'required',
                'string',
                'max:255',

                Rule::unique(
                    'areas',
                    'nama_area'
                )->ignore(
                    $area->id
                ),
            ],

            'keterangan' => [
                'nullable',
                'string',
            ],

        ]);


        $area->update(
            $validated
        );


        return redirect()
            ->route(
                'areas.index'
            )
            ->with(
                'success',
                'Area / Line berhasil diperbarui.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | HAPUS AREA
    |--------------------------------------------------------------------------
    */

    public function destroy(Area $area)
    {
        /*
        | Jangan hapus jika masih digunakan mesin
        */

        if (
            $area
                ->machines()
                ->exists()
        ) {

            return back()
                ->withErrors([
                    'area' =>
                        'Area / Line tidak dapat dihapus karena masih memiliki mesin.',
                ]);
        }


        $area->delete();


        return redirect()
            ->route(
                'areas.index'
            )
            ->with(
                'success',
                'Area / Line berhasil dihapus.'
            );
    }
}