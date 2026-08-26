<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('barang_masuks')) {
            return;
        }

        Schema::create('barang_masuks', function (Blueprint $table) {
            $table->id();

            $table->string('no_transaksi', 50)->unique();
            $table->date('tanggal_masuk');

            $table->foreignId('barang_id')
                ->constrained('barangs')
                ->restrictOnDelete();

            $table->decimal('qty', 12, 2);

            $table->foreignId('satuan_id')
                ->constrained('satuans')
                ->restrictOnDelete();

            $table->string('supplier')->nullable();
            $table->string('no_faktur')->nullable();
            $table->decimal('harga', 15, 2)->nullable();

            $table->text('keterangan')->nullable();

            $table->enum('status', [
                'RECEIVED',
                'CANCELLED'
            ])->default('RECEIVED');

            $table->foreignId('received_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('barang_masuks');
    }
};