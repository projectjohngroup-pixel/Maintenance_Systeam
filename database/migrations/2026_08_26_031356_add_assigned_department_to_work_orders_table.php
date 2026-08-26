<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('work_orders', function (Blueprint $table) {
            $table->string('assigned_department')->nullable()->after('tujuan');
        });

        DB::table('work_orders')
            ->whereNull('assigned_department')
            ->orWhere('assigned_department', '')
            ->update([
                'assigned_department' => DB::raw("CASE
                    WHEN LOWER(TRIM(tujuan)) LIKE '%prev%' THEN 'PREV_MAINT'
                    WHEN LOWER(TRIM(tujuan)) LIKE '%mekanik%' THEN 'MEKANIK_MAINT'
                    WHEN LOWER(TRIM(departemen)) = 'produksi' THEN 'MEKANIK_MAINT'
                    ELSE 'MEKANIK_MAINT'
                END"),
            ]);
    }

    public function down(): void
    {
        Schema::table('work_orders', function (Blueprint $table) {
            $table->dropColumn('assigned_department');
        });
    }
};
