<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('work_orders', function (Blueprint $table) {

            // =====================================================
            // DATA WAKTU KERUSAKAN
            // =====================================================

            $table->time('jam_kerusakan')
                ->nullable()
                ->after('tanggal_kerusakan');


            // =====================================================
            // PENERIMAAN & PERENCANAAN MAINTENANCE
            // =====================================================

            $table->text('laporan_diterima')
                ->nullable()
                ->after('status');

            $table->text('perencanaan_pekerjaan')
                ->nullable()
                ->after('laporan_diterima');

            $table->dateTime('jadwal_perbaikan')
                ->nullable()
                ->after('perencanaan_pekerjaan');


            // =====================================================
            // WAKTU PELAKSANAAN PERBAIKAN
            // =====================================================

            $table->date('tanggal_mulai_perbaikan')
                ->nullable()
                ->after('jadwal_perbaikan');

            $table->time('jam_mulai_perbaikan')
                ->nullable()
                ->after('tanggal_mulai_perbaikan');

            $table->date('tanggal_selesai_perbaikan')
                ->nullable()
                ->after('jam_mulai_perbaikan');

            $table->time('jam_selesai_perbaikan')
                ->nullable()
                ->after('tanggal_selesai_perbaikan');


            // =====================================================
            // TEKNISI
            // =====================================================

            $table->string('teknisi')
                ->nullable()
                ->after('jam_selesai_perbaikan');


            // =====================================================
            // FOTO HASIL PERBAIKAN
            // =====================================================

            $table->string('foto_perbaikan')
                ->nullable()
                ->after('foto');


            // =====================================================
            // PRIORITAS
            // =====================================================
            //
            // Field priority SUDAH ADA.
            // Tidak dibuat lagi.
            //


            // =====================================================
            // KETERANGAN
            // =====================================================
            //
            // Field keterangan SUDAH ADA.
            // Tidak dibuat lagi.
            //
        });
    }

    public function down(): void
    {
        Schema::table('work_orders', function (Blueprint $table) {

            $columns = [
                'jam_kerusakan',
                'laporan_diterima',
                'perencanaan_pekerjaan',
                'jadwal_perbaikan',
                'tanggal_mulai_perbaikan',
                'jam_mulai_perbaikan',
                'tanggal_selesai_perbaikan',
                'jam_selesai_perbaikan',
                'teknisi',
                'foto_perbaikan',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('work_orders', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};