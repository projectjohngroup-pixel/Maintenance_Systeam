<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('work_order_status_histories')) {
            return;
        }


        Schema::create(
            'work_order_status_histories',
            function (Blueprint $table) {

                $table->id();

                $table->unsignedBigInteger('work_order_id');

                $table->string('status', 50);

                /*
                | Alasan pending / penolakan.
                */

                $table->string('alasan', 100)
                    ->nullable();

                /*
                | Keterangan tambahan.
                */

                $table->text('keterangan')
                    ->nullable();

                $table->dateTime('started_at');

                $table->dateTime('ended_at')
                    ->nullable();

                $table->unsignedBigInteger('created_by')
                    ->nullable();

                $table->timestamps();


                $table->index(
                    [
                        'work_order_id',
                        'status',
                    ]
                );

                $table->index('started_at');
            }
        );
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'work_order_status_histories'
        );
    }
};
