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
        Schema::create('machine_spareparts', function (Blueprint $table) {

            $table->id();

            $table->foreignId('machine_id')
                ->constrained('mesins')
                ->cascadeOnDelete();

            $table->foreignId('barang_id')
                ->constrained('barangs')
                ->cascadeOnDelete();

            $table->decimal('qty', 12, 2)
                ->default(1);

            $table->text('keterangan')
                ->nullable();

            $table->timestamps();


            /*
            |--------------------------------------------------------------------------
            | SATU SPAREPART HANYA SATU KALI
            | PADA MESIN YANG SAMA
            |--------------------------------------------------------------------------
            */

            $table->unique([
                'machine_id',
                'barang_id',
            ]);

        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(
            'machine_spareparts'
        );
    }
};