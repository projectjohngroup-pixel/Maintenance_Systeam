<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('satuans')) { return; }

        Schema::create('satuans', function (Blueprint $table) {
            $table->id();

            $table->string('nama', 50)->unique();

            $table->boolean('status')
                ->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('satuans');
    }
};