<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Inventory\Barang;
use App\Models\WorkOrder\WorkOrder;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ApiDashboardController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | GET /api/dashboard
    |--------------------------------------------------------------------------
    |
    | Ringkasan dashboard sesuai role user yang terautentikasi.
    | Semua angka dihitung dari database aktual (tanpa hardcode).
    |
    */

    public function index(Request $request)
    {
        $user = $request->user();

        $role = \App\Support\DepartmentAccess::normalizeRole($user->role ?? '');

        $year = now()->year;

        $start = Carbon::create($year, 1, 1)->startOfYear();
        $end = Carbon::create($year, 12, 31)->endOfYear();

        $payload = [

            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'role' => $user->role,
                'bagian' => $user->bagian,
            ],

            'year' => $year,

            'server_time' => now()->toIso8601String(),
        ];

        /*
        | PRODUKSI: ringkasan work order milik sendiri.
        */

        if ($role === \App\Support\DepartmentAccess::PRODUKSI) {

            $base = WorkOrder::query()
                ->whereRaw('LOWER(TRIM(dibuat_oleh)) = ?', [
                    strtolower(trim((string) $user->name)),
                ]);

            $payload['my'] = [
                'total' => (clone $base)->count(),
                'open' => (clone $base)->whereRaw(
                    "LOWER(TRIM(status)) NOT IN ('close','done','selesai')"
                )->count(),
                'selesai' => (clone $base)->whereRaw(
                    "LOWER(TRIM(status)) IN ('close','done','selesai')"
                )->count(),
            ];
        }

        /*
        | MAINTENANCE: beban kerja seluruh WO tahun berjalan.
        */

        if ($role === \App\Support\DepartmentAccess::MAINTENANCE) {

            $values = WorkOrder::query()
                ->whereBetween('tanggal_kerusakan', [$start, $end])
                ->pluck('status')
                ->map(fn ($s) => strtolower(trim((string) $s)));

            $payload['workload'] = [
                'total' => $values->count(),
                'open' => $values->filter(fn ($s) => $s === 'open')->count(),
                'in_progress' => $values->filter(
                    fn ($s) => str_contains($s, 'progress') || str_contains($s, 'proses')
                )->count(),
                'pending' => $values->filter(fn ($s) => $s === 'pending')->count(),
                'close' => $values->filter(fn ($s) => $s === 'close')->count(),
                'low_stock_items' => Barang::query()
                    ->whereNotNull('stok_minimum')
                    ->whereColumn('stok', '<=', 'stok_minimum')
                    ->count(),
            ];
        }

        /*
        | MANAGER / ADMINISTRATOR: KPI monitoring global.
        */

        if (in_array($role, [
            \App\Support\DepartmentAccess::MANAGER,
            \App\Support\DepartmentAccess::ADMINISTRATOR,
            \App\Support\DepartmentAccess::DIREKTUR,
        ], true)) {

            $rows = WorkOrder::query()
                ->whereBetween('tanggal_kerusakan', [$start, $end])
                ->get([
                    'status',
                    'priority',
                    'tanggal_kerusakan',
                    'jam_kerusakan',
                    'tanggal_selesai_perbaikan',
                    'jam_selesai_perbaikan',
                ]);

            $slaHours = ['EMERGENCY' => 8, 'URGENT' => 24, 'NORMAL' => 72];

            $closeWo = 0;
            $overdueWo = 0;
            $pendingWo = 0;
            $openWo = 0;
            $closedTotal = 0;
            $closedOnTime = 0;

            foreach ($rows as $row) {

                $status = strtolower(trim((string) $row->status));

                match (true) {
                    $status === 'open' => $openWo++,
                    $status === 'pending' => $pendingWo++,
                    $status === 'close' => $closeWo++,
                    default => null,
                };

                $prio = strtoupper(trim(
                    (string) ($row->priority ?: 'NORMAL')
                ));

                $sla = $slaHours[$prio] ?? $slaHours['NORMAL'];

                try {
                    $reportedAt = !empty($row->tanggal_kerusakan)
                        ? Carbon::parse($row->tanggal_kerusakan)
                        : null;
                    if ($reportedAt && !empty($row->jam_kerusakan)) {
                        $reportedAt->setTimeFromTimeString($row->jam_kerusakan);
                    } elseif ($reportedAt) {
                        $reportedAt->startOfDay();
                    }
                } catch (\Throwable $e) {
                    $reportedAt = null;
                }

                if ($status !== 'close' && $reportedAt && now()->greaterThan($reportedAt->copy()->addHours($sla))) {
                    $overdueWo++;
                }

                if ($status === 'close') {

                    $closedTotal++;

                    try {
                        $finishedAt = !empty($row->tanggal_selesai_perbaikan)
                            ? Carbon::parse($row->tanggal_selesai_perbaikan)
                            : null;
                        if ($finishedAt && !empty($row->jam_selesai_perbaikan)) {
                            $finishedAt->setTimeFromTimeString($row->jam_selesai_perbaikan);
                        } elseif ($finishedAt) {
                            $finishedAt->endOfDay();
                        }
                    } catch (\Throwable $e) {
                        $finishedAt = null;
                    }

                    if (
                        $reportedAt
                        && $finishedAt
                        && $finishedAt->lessThanOrEqualTo($reportedAt->copy()->addHours($sla))
                    ) {
                        $closedOnTime++;
                    }
                }
            }

            $payload['kpi'] = [
                'total_wo' => $rows->count(),
                'open_wo' => $openWo,
                'pending_wo' => $pendingWo,
                'close_wo' => $closeWo,
                'overdue_wo' => $overdueWo,
                'completion_rate' => $rows->count() > 0
                    ? (int) round($closeWo / $rows->count() * 100)
                    : 0,
                'on_time_rate' => $closedTotal > 0
                    ? (int) round($closedOnTime / $closedTotal * 100)
                    : 0,
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $payload,
        ]);
    }
}
