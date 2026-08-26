<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_requests', function (Blueprint $table) {
            $table->id();

            $table->string('no_pr')->unique();
            $table->date('tanggal_pr');
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('keperluan');
            $table->enum('prioritas', ['NORMAL', 'URGENT'])
                ->default('NORMAL');

            $table->enum('status', [
                'DRAFT',
                'DIAJUKAN',
                'DISETUJUI',
                'DITOLAK',
                'SELESAI'
            ])->default('DRAFT');

            $table->text('catatan')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_requests');
    }
};