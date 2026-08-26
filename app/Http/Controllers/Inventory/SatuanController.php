<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Inventory\Satuan;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SatuanController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | DAFTAR SATUAN
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        return redirect()->route('barang.index');
    }


    /*
    |--------------------------------------------------------------------------
    | SIMPAN SATUAN
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => [
                'required',
                'string',
                'max:50',
                'unique:satuans,nama',
            ],

            'status' => [
                'required',
                Rule::in([
                    '0',
                    '1',
                ]),
            ],
        ]);


        $satuan = Satuan::create([
            'nama' => trim(
                $validated['nama']
            ),

            'status' => (
                (int) $validated['status']
            ) === 1,
        ]);


        /*
        |--------------------------------------------------------------------------
        | AJAX
        |--------------------------------------------------------------------------
        */

        if ($request->expectsJson()) {

            return response()->json([
                'success' => true,

                'message' =>
                    'Satuan berhasil ditambahkan.',

                'data' => [
                    'id' => $satuan->id,
                    'nama' => $satuan->nama,
                ],
            ]);
        }


        return redirect()
            ->back()
            ->with(
                'success',
                'Satuan berhasil ditambahkan.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE SATUAN
    |--------------------------------------------------------------------------
    */

    public function update(
        Request $request,
        Satuan $satuan
    ) {
        $validated = $request->validate([
            'nama' => [
                'required',
                'string',
                'max:50',

                Rule::unique(
                    'satuans',
                    'nama'
                )->ignore(
                    $satuan->id
                ),
            ],

            'status' => [
                'required',
                Rule::in([
                    '0',
                    '1',
                ]),
            ],
        ]);


        $satuan->update([
            'nama' => trim(
                $validated['nama']
            ),

            'status' => (
                (int) $validated['status']
            ) === 1,
        ]);


        return redirect()
            ->back()
            ->with(
                'success',
                'Satuan berhasil diperbarui.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | HAPUS SATUAN
    |--------------------------------------------------------------------------
    */

    public function destroy(
        Satuan $satuan
    ) {
        if (
            $satuan
                ->barangs()
                ->exists()
        ) {

            return redirect()
                ->back()
                ->withErrors([
                    'satuan' =>
                        'Satuan tidak dapat dihapus karena masih digunakan oleh barang.',
                ]);
        }


        $satuan->delete();


        return redirect()
            ->back()
            ->with(
                'success',
                'Satuan berhasil dihapus.'
            );
    }
}