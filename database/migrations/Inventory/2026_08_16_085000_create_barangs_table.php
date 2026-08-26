<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('barangs', function (Blueprint $table) {

            $table->id();

            $table->string(
                'kode_barang',
                100
            )->unique();

            $table->string(
                'nama_spesifikasi'
            );

            $table->foreignId(
                'satuan_id'
            )
            ->nullable()
            ->constrained(
                'satuans'
            )
            ->nullOnDelete();

            $table->integer(
                'stok'
            )->default(0);

            $table->integer(
                'stok_minimum'
            )->default(0);

            $table->string(
                'lokasi_penyimpanan'
            )->nullable();

            $table->string(
                'status'
            )->nullable();

            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'barangs'
        );
    }
};