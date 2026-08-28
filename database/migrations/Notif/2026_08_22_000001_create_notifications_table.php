<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('notifications')) { return; }

        Schema::create('notifications', function (Blueprint $table) {

            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('work_order_id')
                ->nullable()
                ->constrained('work_orders')
                ->cascadeOnDelete();

            $table->string('type', 50);

            $table->string('title', 150);

            $table->text('message');

            $table->enum('status', [
                'UNREAD',
                'READ',
            ])->default('UNREAD');

            $table->timestamp('read_at')
                ->nullable();

            $table->timestamp('deadline_at')
                ->nullable();

            $table->timestamp('completed_at')
                ->nullable();

            $table->text('delay_reason')
                ->nullable();

            $table->timestamps();

            $table->index([
                'user_id',
                'status',
            ]);

            $table->index('work_order_id');

            $table->index('type');

            $table->index('deadline_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};