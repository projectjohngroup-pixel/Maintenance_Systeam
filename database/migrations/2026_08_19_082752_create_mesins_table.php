<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('mesins')) {
            return;
        }

        Schema::create('mesins', function (Blueprint $table) {

            $table->id();

            $table->string('kode_mesin')->unique();

            $table->string('nama_mesin');

            $table->foreignId('area_id')
                ->nullable()
                ->constrained('areas')
                ->restrictOnDelete()
                ->cascadeOnUpdate();

            $table->text('spesifikasi')->nullable();

            $table->decimal('kw', 12, 2)
                ->nullable();

            $table->string('status')
                ->default('Aktif');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mesins');
    }
};