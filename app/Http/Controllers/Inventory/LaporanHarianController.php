<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Inventory\BarangMasuk;
use App\Models\Inventory\BarangKeluar;
use App\Support\DepartmentAccess;
use Illuminate\Http\Request;

class LaporanHarianController extends Controller
{
    public function index(Request $request)
    {
        $tanggalDari = $request->filled('tanggal_dari')
            ? $request->tanggal_dari
            : now()->format('Y-m-d');

        $tanggalSampai = $request->filled('tanggal_sampai')
            ? $request->tanggal_sampai
            : now()->format('Y-m-d');


        $barangMasuks = BarangMasuk::query()
            ->whereHas('barang', function ($q) {
                DepartmentAccess::applyBarangScope($q, auth()->user());
            })
            ->whereBetween(
                'tanggal_masuk',
                [
                    $tanggalDari,
                    $tanggalSampai,
                ]
            )
            ->orderBy('tanggal_masuk')
            ->orderBy('id')
            ->get();


        $barangKeluars = BarangKeluar::with([
            'barang.satuan',
            'machine',
            'area',
        ])
            ->whereHas('barang', function ($q) {
                DepartmentAccess::applyBarangScope($q, auth()->user());
            })
            ->whereBetween(
                'tanggal_keluar',
                [
                    $tanggalDari,
                    $tanggalSampai,
                ]
            )
            ->orderBy('tanggal_keluar')
            ->orderBy('id')
            ->get();


        return view(
            'inventory.laporan-harian.index',
            compact(
                'barangMasuks',
                'barangKeluars',
                'tanggalDari',
                'tanggalSampai'
            )
        );
    }
}