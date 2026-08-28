<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('monitor_results')) {
            return;
        }

        Schema::create('monitor_results', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('monitor_config_id');

            $table->string('status', 50);

            $table->string('category', 50);

            $table->boolean('dns_resolved')
                ->default(true);

            $table->boolean('connection_successful')
                ->default(true);

            $table->boolean('http_successful')
                ->default(true);

            $table->integer('http_status_code')
                ->nullable();

            $table->integer('response_time_ms')
                ->nullable();

            $table->integer('timeout_ms')
                ->nullable();

            $table->text('error_message')
                ->nullable();

            $table->string('error_type', 50)
                ->nullable();

            $table->string('severity', 20)
                ->default('normal');

            $table->json('raw_response')
                ->nullable();

            $table->timestamps();

            $table->index('monitor_config_id');
            $table->index('status');
            $table->index('severity');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monitor_results');
    }
};
