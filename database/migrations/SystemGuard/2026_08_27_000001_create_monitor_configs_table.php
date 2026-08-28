<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('monitor_configs')) {
            return;
        }

        Schema::create('monitor_configs', function (Blueprint $table) {
            $table->id();

            $table->string('name', 100);

            $table->string('target_url', 500);

            $table->string('target_domain', 255)
                ->nullable();

            $table->string('type', 50)
                ->default('http');

            $table->boolean('is_active')
                ->default(true);

            $table->integer('check_interval_seconds')
                ->default(900);

            $table->integer('timeout_seconds')
                ->default(10);

            $table->integer('expected_status_code')
                ->default(200);

            $table->integer('response_time_threshold_ms')
                ->default(5000);

            $table->integer('max_retries')
                ->default(3);

            $table->integer('retry_delay_seconds')
                ->default(30);

            $table->json('recovery_actions')
                ->nullable();

            $table->boolean('auto_recovery_enabled')
                ->default(false);

            $table->text('description')
                ->nullable();

            $table->timestamps();

            $table->index('is_active');
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monitor_configs');
    }
};
