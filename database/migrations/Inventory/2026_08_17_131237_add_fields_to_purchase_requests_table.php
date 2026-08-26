<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (
            !Schema::hasTable('purchase_requests') ||
            Schema::hasColumn('purchase_requests', 'no_pr')
        ) {
            return;
        }

        Schema::table('purchase_requests', function (Blueprint $table) {
            $table->string('no_pr')->unique()->after('id');
            $table->date('tanggal_pr')->after('no_pr');

            $table->foreignId('user_id')
                ->nullable()
                ->after('tanggal_pr')
                ->constrained('users')
                ->nullOnDelete();

            $table->string('keperluan')->after('user_id');

            $table->enum('prioritas', ['NORMAL', 'URGENT'])
                ->default('NORMAL')
                ->after('keperluan');

            $table->enum('status', [
                'DRAFT',
                'DIAJUKAN',
                'DISETUJUI',
                'DITOLAK',
                'SELESAI'
            ])->default('DRAFT')->after('prioritas');

            $table->text('catatan')
                ->nullable()
                ->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('purchase_requests', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn([
                'no_pr',
                'tanggal_pr',
                'user_id',
                'keperluan',
                'prioritas',
                'status',
                'catatan',
            ]);
        });
    }
};
