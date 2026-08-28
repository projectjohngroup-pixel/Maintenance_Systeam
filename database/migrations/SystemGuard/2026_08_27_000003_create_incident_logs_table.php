<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('incident_logs')) {
            return;
        }

        Schema::create('incident_logs', function (Blueprint $table) {
            $table->id();

            $table->string('incident_id', 50)
                ->unique();

            $table->unsignedBigInteger('monitor_config_id');

            $table->string('target', 500);

            $table->string('error_category', 50);

            $table->string('error_type', 50);

            $table->text('error_message');

            $table->string('severity', 20);

            $table->string('status', 50);

            $table->dateTime('detected_at');

            $table->dateTime('recovered_at')
                ->nullable();

            $table->integer('duration_seconds')
                ->nullable();

            $table->integer('retry_count')
                ->default(0);

            $table->json('recovery_summary')
                ->nullable();

            $table->timestamps();

            $table->index('monitor_config_id');
            $table->index('status');
            $table->index('severity');
            $table->index('detected_at');
            $table->index('incident_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incident_logs');
    }
};
