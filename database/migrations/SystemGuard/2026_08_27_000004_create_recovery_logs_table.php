<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('recovery_logs')) {
            return;
        }

        Schema::create('recovery_logs', function (Blueprint $table) {
            $table->id();

            $table->string('incident_id', 50);

            $table->unsignedBigInteger('monitor_config_id');

            $table->string('action', 100);

            $table->string('action_type', 50);

            $table->string('status', 50);

            $table->text('result_message')
                ->nullable();

            $table->boolean('verification_passed')
                ->nullable();

            $table->text('verification_details')
                ->nullable();

            $table->integer('attempt_number')
                ->default(1);

            $table->dateTime('started_at');

            $table->dateTime('completed_at')
                ->nullable();

            $table->integer('duration_ms')
                ->nullable();

            $table->json('metadata')
                ->nullable();

            $table->timestamps();

            $table->index('incident_id');
            $table->index('monitor_config_id');
            $table->index('status');
            $table->index('started_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recovery_logs');
    }
};
