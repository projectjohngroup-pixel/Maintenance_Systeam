<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('work_orders', function (Blueprint $table) {

            $table->id();

            // =========================
            // IDENTITAS WO
            // =========================

            $table->string('no_wo')->unique();

            $table->date('tanggal_kerusakan');


            // =========================
            // PENGAJU WO
            // =========================

            $table->string('departemen')->nullable();


            // =========================
            // TUJUAN WO
            // =========================

            $table->string('tujuan')->nullable();


            // =========================
            // PRIORITAS
            // =========================

            $table->string('priority')->nullable();


            // =========================
            // AREA & MESIN
            // =========================

            $table->string('area')->nullable();

            $table->string('mesin')->nullable();


            // =========================
            // KATEGORI
            // =========================

            $table->string('kategori')->nullable();


            // =========================
            // PEKERJAAN
            // =========================

            $table->string('job')->nullable();

            $table->text('deskripsi')->nullable();


            // =========================
            // FILE
            // =========================

            $table->string('foto')->nullable();

            $table->string('wo')->nullable();


            // =========================
            // STATUS
            // =========================

            $table->string('status')->default('OPEN');


            // =========================
            // KETERANGAN
            // =========================

            $table->string('keterangan')->nullable();


            // =========================
            // PEMBUAT WO
            // =========================

            $table->string('dibuat_oleh')->nullable();


            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_orders');
    }
};