<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('system_guard_states')) {
            return;
        }

        Schema::create('system_guard_states', function (Blueprint $table) {
            $table->id();

            $table->string('component', 50)
                ->unique()
                ->comment('internet | tunnel | origin | daemon | system');

            $table->string('status', 50)
                ->default('UNKNOWN')
                ->comment('ONLINE | DEGRADED | OFFLINE | RUNNING | STOPPED | UNKNOWN');

            $table->string('message', 500)
                ->nullable();

            $table->string('error_type', 50)
                ->nullable();

            $table->string('last_value', 255)
                ->nullable();

            $table->text('detail')
                ->nullable();

            $table->dateTime('state_changed_at')
                ->nullable();

            $table->dateTime('last_checked_at')
                ->nullable();

            $table->decimal('downtime_seconds', 12, 1)
                ->default(0);

            $table->integer('consecutive_failures')
                ->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_guard_states');
    }
};
