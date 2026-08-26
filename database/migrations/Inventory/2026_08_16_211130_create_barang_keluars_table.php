<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('barang_keluars', function (Blueprint $table) {
            $table->id();

            // =========================
            // IDENTITAS TRANSAKSI
            // =========================
            $table->string('no_transaksi', 50)->unique();
            $table->date('tanggal_keluar');

            // =========================
            // LOKASI PEMAKAIAN
            // Boleh kosong
            // =========================
            $table->foreignId('area_id')
                ->nullable()
                ->constrained('areas')
                ->nullOnDelete();

            $table->foreignId('machine_id')
                ->nullable()
                ->constrained('mesins')
                ->nullOnDelete();

            // =========================
            // BARANG
            // =========================
            $table->foreignId('barang_id')
                ->constrained('barangs')
                ->restrictOnDelete();

            // =========================
            // STOK
            // Snapshot saat transaksi
            // =========================
            $table->unsignedInteger('qty');
            $table->string('satuan', 50);

            $table->unsignedInteger('stok_awal');
            $table->unsignedInteger('sisa_stok');

            // =========================
            // DIPAKAI OLEH
            // =========================
            $table->enum('dipakai_oleh', [
                'ME_PREV',
                'PREV',
                'SIPIL',
            ]);

            // =========================
            // WORK ORDER
            // Tidak wajib
            // Tidak FK karena WO berada
            // pada database berbeda
            // =========================
            $table->string('no_wo', 100)->nullable();

            // =========================
            // KETERANGAN
            // =========================
            $table->text('keterangan')->nullable();

    
            // =========================
            // USER PEMBUAT TRANSAKSI
            // Sementara tanpa FK
            // =========================
            // =========================
            // STATUS TRANSAKSI
            // =========================
            $table->enum('status', [
                'RECEIVED',
                'CANCELLED',
            ])->default('RECEIVED');

            $table->unsignedBigInteger('user_id')->nullable();

            $table->timestamps();

            // =========================
            // INDEX
            // =========================
            $table->index(['barang_id', 'tanggal_keluar']);
            $table->index(['dipakai_oleh', 'tanggal_keluar']);
            $table->index(['area_id', 'tanggal_keluar']);
            $table->index(['machine_id', 'tanggal_keluar']);
            $table->index('no_wo');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barang_keluars');
    }
};