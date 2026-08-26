<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Finalisasi struktur barang_masuks.
     * Semua kolom final sudah dibuat oleh migration sebelumnya,
     * jadi migration ini hanya memastikan tidak ada yang tertinggal.
     */
    public function up(): void
    {
        if (!Schema::hasTable('barang_masuks')) {
            return;
        }

        if (
            Schema::hasColumn('barang_masuks', 'no_transaksi') &&
            Schema::hasColumn('barang_masuks', 'status') &&
            Schema::hasColumn('barang_masuks', 'received_by')
        ) {
            return;
        }
    }

    public function down(): void
    {
        //
    }
};
