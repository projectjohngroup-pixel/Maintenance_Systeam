<?php

namespace App\Http\Controllers\Machine;

use App\Http\Controllers\Controller;
use App\Models\Inventory\Barang;
use App\Models\Machine\Machine;
use App\Models\Machine\MachineSparepart;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MachineSparepartController extends Controller
{
    public function index(Request $request)
    {
        $query = MachineSparepart::with([
            'machine',
            'barang.satuan',
        ])->latest('id');

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->whereHas('machine', function ($machine) use ($search) {
                    $machine->where('kode_mesin', 'like', "%{$search}%")
                        ->orWhere('nama_mesin', 'like', "%{$search}%");
                })->orWhereHas('barang', function ($barang) use ($search) {
                    $barang->where('kode_barang', 'like', "%{$search}%")
                        ->orWhere('nama_spesifikasi', 'like', "%{$search}%");
                });
            });
        }

        $machineSpareparts = $query
            ->paginate(15)
            ->withQueryString();

        $machines = Machine::orderBy('nama_mesin')->get();

        $barangs = Barang::with('satuan')
            ->orderBy('nama_spesifikasi')
            ->get();

        return view(
            'master.mesin-sparepart.index',
            compact(
                'machineSpareparts',
                'machines',
                'barangs'
            )
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'machine_id' => [
                'required',
                'exists:mesins,id',
                Rule::unique('machine_spareparts')
                    ->where(function ($query) use ($request) {
                        return $query->where(
                            'barang_id',
                            $request->barang_id
                        );
                    }),
            ],

            'barang_id' => [
                'required',
                'exists:barangs,id',
            ],

            'qty' => [
                'required',
                'numeric',
                'gt:0',
            ],

            'keterangan' => [
                'nullable',
                'string',
            ],
        ], [
            'machine_id.unique' =>
                'Sparepart tersebut sudah terhubung dengan mesin ini.',
        ]);

        MachineSparepart::create([
            'machine_id' =>
                $validated['machine_id'],

            'barang_id' =>
                $validated['barang_id'],

            'qty' =>
                $validated['qty'],

            'keterangan' =>
                $validated['keterangan'] ?? null,
        ]);

        return redirect()
            ->route('machine-spareparts.index')
            ->with(
                'success',
                'Relasi mesin dan sparepart berhasil ditambahkan.'
            );
    }

    public function update(
        Request $request,
        MachineSparepart $machineSparepart
    ) {
        $validated = $request->validate([
            'machine_id' => [
                'required',
                'exists:mesins,id',
            ],

            'barang_id' => [
                'required',
                'exists:barangs,id',
            ],

            'qty' => [
                'required',
                'numeric',
                'gt:0',
            ],

            'keterangan' => [
                'nullable',
                'string',
            ],
        ]);

        $duplicate = MachineSparepart::where(
            'machine_id',
            $validated['machine_id']
        )
            ->where(
                'barang_id',
                $validated['barang_id']
            )
            ->where(
                'id',
                '!=',
                $machineSparepart->id
            )
            ->exists();

        if ($duplicate) {
            return back()
                ->withErrors([
                    'machine_id' =>
                        'Sparepart tersebut sudah terhubung dengan mesin ini.',
                ])
                ->withInput();
        }

        $machineSparepart->update([
            'machine_id' =>
                $validated['machine_id'],

            'barang_id' =>
                $validated['barang_id'],

            'qty' =>
                $validated['qty'],

            'keterangan' =>
                $validated['keterangan'] ?? null,
        ]);

        return redirect()
            ->route('machine-spareparts.index')
            ->with(
                'success',
                'Relasi mesin dan sparepart berhasil diperbarui.'
            );
    }

    public function destroy(
        MachineSparepart $machineSparepart
    ) {
        $machineSparepart->delete();

        return redirect()
            ->route('machine-spareparts.index')
            ->with(
                'success',
                'Relasi mesin dan sparepart berhasil dihapus.'
            );
    }
}