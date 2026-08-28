<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('downtime_logs')) {
            return;
        }

        Schema::create('downtime_logs', function (Blueprint $table) {
            $table->id();

            $table->string('component', 50)
                ->comment('internet | tunnel | origin');

            $table->string('status', 50)
                ->comment('DEGRADED | OFFLINE');

            $table->string('error_type', 50)
                ->nullable();

            $table->text('error_message')
                ->nullable();

            $table->dateTime('started_at');

            $table->dateTime('ended_at')
                ->nullable();

            $table->integer('duration_seconds')
                ->nullable();

            $table->text('resolution_summary')
                ->nullable();

            $table->timestamps();

            $table->index('component');
            $table->index('status');
            $table->index('started_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('downtime_logs');
    }
};
