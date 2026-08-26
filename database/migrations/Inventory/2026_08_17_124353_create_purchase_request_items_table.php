<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_request_items', function (Blueprint $table) {

            $table->id();

            $table->foreignId('purchase_request_id')
                ->constrained('purchase_requests')
                ->cascadeOnDelete();

            $table->foreignId('barang_id')
                ->nullable()
                ->constrained('barangs')
                ->nullOnDelete();

            $table->string('nama_barang', 255);

            $table->decimal('jumlah', 12, 2)
                ->default(1);

            $table->string('satuan', 50)
                ->nullable();

            $table->foreignId('machine_id')
                ->nullable()
                ->constrained('mesins')
                ->nullOnDelete();

            $table->text('keterangan')
                ->nullable();

            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_request_items');
    }
};