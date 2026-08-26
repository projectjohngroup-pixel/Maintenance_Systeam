<?php

use App\Support\DepartmentAccess;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (
            Schema::hasTable('work_orders')
            && !Schema::hasColumn('work_orders', 'assigned_department')
        ) {
            Schema::table('work_orders', function (Blueprint $table) {
                $table->string('assigned_department', 50)
                    ->nullable()
                    ->after('tujuan');
            });
        }

        if (
            Schema::hasTable('work_orders')
            && Schema::hasColumn('work_orders', 'assigned_department')
        ) {
            $orders = DB::table('work_orders')->select('id', 'tujuan', 'assigned_department')->get();

            foreach ($orders as $order) {
                $department = DepartmentAccess::fromTujuan($order->tujuan ?? '');

                if (($order->assigned_department ?? '') !== $department) {
                    DB::table('work_orders')
                        ->where('id', $order->id)
                        ->update(['assigned_department' => $department]);
                }
            }

            Schema::table('work_orders', function (Blueprint $table) {
                if (!$this->hasIndex('work_orders', 'work_orders_assigned_department_index')) {
                    $table->index('assigned_department', 'work_orders_assigned_department_index');
                }
            });
        }

        if (
            Schema::hasTable('work_orders')
            && Schema::hasColumn('work_orders', 'no_wo')
            && !$this->hasIndex('work_orders', 'work_orders_no_wo_unique')
        ) {
            $duplicates = DB::table('work_orders')
                ->select('no_wo', DB::raw('COUNT(*) as total'))
                ->whereNotNull('no_wo')
                ->where('no_wo', '<>', '')
                ->groupBy('no_wo')
                ->having('total', '>', 1)
                ->count();

            if ($duplicates === 0) {
                Schema::table('work_orders', function (Blueprint $table) {
                    $table->unique('no_wo', 'work_orders_no_wo_unique');
                });
            }
        }

        if (
            Schema::hasTable('barangs')
            && !Schema::hasColumn('barangs', 'department')
        ) {
            Schema::table('barangs', function (Blueprint $table) {
                $table->string('department', 50)
                    ->nullable()
                    ->after('status');
            });
        }

        if (
            Schema::hasTable('barangs')
            && Schema::hasColumn('barangs', 'department')
        ) {
            DB::table('barangs')
                ->whereNull('department')
                ->orWhere('department', '')
                ->update(['department' => DepartmentAccess::MEKANIK_MAINT]);

            Schema::table('barangs', function (Blueprint $table) {
                if (!$this->hasIndex('barangs', 'barangs_department_index')) {
                    $table->index('department', 'barangs_department_index');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('work_orders') && Schema::hasColumn('work_orders', 'assigned_department')) {
            Schema::table('work_orders', function (Blueprint $table) {
                if ($this->hasIndex('work_orders', 'work_orders_assigned_department_index')) {
                    $table->dropIndex('work_orders_assigned_department_index');
                }

                $table->dropColumn('assigned_department');
            });
        }

        if (Schema::hasTable('barangs') && Schema::hasColumn('barangs', 'department')) {
            Schema::table('barangs', function (Blueprint $table) {
                if ($this->hasIndex('barangs', 'barangs_department_index')) {
                    $table->dropIndex('barangs_department_index');
                }

                $table->dropColumn('department');
            });
        }
    }

    private function hasIndex(string $table, string $index): bool
    {
        try {
            return Schema::hasIndex($table, $index);
        } catch (\Throwable $e) {
            return false;
        }
    }
};
