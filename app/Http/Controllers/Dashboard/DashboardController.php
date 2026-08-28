<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;

use App\Models\Inventory\Barang;
use App\Models\Inventory\BarangKeluar;
use App\Models\Inventory\BarangMasuk;
use App\Models\Machine\MachineSparepart;
use App\Models\Settings\SystemSetting;
use App\Models\WorkOrder\WorkOrder;

use App\Support\DepartmentAccess;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        /*
        |--------------------------------------------------------------------------
        | SETTINGS SISTEM
        |--------------------------------------------------------------------------
        */

        $settings = SystemSetting::pluck('value', 'key');


        /*
        |--------------------------------------------------------------------------
        | ROLE (CASE-INSENSITIVE)
        |--------------------------------------------------------------------------
        |
        | Normalisasi sama seperti middleware UserAccess agar
        | routing dashboard tidak terpengaruh huruf besar/kecil.
        |
        */

        $role = DepartmentAccess::normalizeRole($user->role ?? '');


        /*
        |--------------------------------------------------------------------------
        | MANAGER - LANGSUNG DASHBOARD MANAGER
        |--------------------------------------------------------------------------
        */

        if ($role === DepartmentAccess::MANAGER) {

            return redirect()
                ->route('dashboard.manager');

        }


        /*
        |--------------------------------------------------------------------------
        | DIREKTUR
        |--------------------------------------------------------------------------
        */

        if ($role === DepartmentAccess::DIREKTUR) {

            return redirect()
                ->route('dashboard.manager');

        }


        /*
        |--------------------------------------------------------------------------
        | PRODUKSI
        |--------------------------------------------------------------------------
        */

        if ($role === DepartmentAccess::PRODUKSI) {

            return view(
                'dashboard.user.index',
                array_merge(
                    [
                        'settings' => $settings,
                        'role' => $user->role,
                    ],
                    $this->getUserStats($user)
                )
            );
        }


        /*
        |--------------------------------------------------------------------------
        | MAINTENANCE
        |--------------------------------------------------------------------------
        */

        if (
            in_array(
                $role,
                [
                    DepartmentAccess::MAINTENANCE,
                    DepartmentAccess::MEKANIK_MAINT,
                    DepartmentAccess::PREV_MAINT,
                ],
                true
            )
        ) {

            return view(
                'dashboard.maintenance.index',
                array_merge(
                    [
                        'settings' => $settings,
                        'dashboardDepartment' => DepartmentAccess::scopedDepartment($user),
                    ],
                    $this->getMaintenanceStats($user, request())
                )
            );
        }


        /*
        |--------------------------------------------------------------------------
        | ADMINISTRATOR - PUSAT MONITORING
        |--------------------------------------------------------------------------
        */

        return view(
            'dashboard.admin.index',
            array_merge(
                ['settings' => $settings],
                $this->getAdminStats(request())
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | STATISTIK ADMINISTRATOR
    |--------------------------------------------------------------------------
    |
    | Semua angka dihitung dari database aktual.
    | Filter: tahun, bulan, departemen, status, prioritas.
    |
    */

    private function getAdminStats($request): array
    {
        /*
        |----------------------------------------------------------------------
        | FILTER
        |----------------------------------------------------------------------
        */

        $tahun = (int) $request->input('tahun', now()->year);

        if ($tahun < 2000 || $tahun > 2100) {
            $tahun = now()->year;
        }

        $bulan = $request->input('bulan');

        $bulan = ($bulan !== null && $bulan !== '')
            ? max(1, min(12, (int) $bulan))
            : null;

        $departemen =
            trim((string) $request->input('departemen', ''));

        $statusFilter =
            trim((string) $request->input('status', ''));

        $prioritasFilter =
            trim((string) $request->input('prioritas', ''));


        $startDate = \Carbon\Carbon::create($tahun, 1, 1)->startOfYear();

        $endDate = \Carbon\Carbon::create($tahun, 12, 31)->endOfYear();


        /*
        |----------------------------------------------------------------------
        | QUERY WORK ORDER TERFILTER
        |----------------------------------------------------------------------
        */

        $woQuery = WorkOrder::query()
            ->whereBetween('tanggal_kerusakan', [$startDate, $endDate]);

        if ($bulan) {

            $woQuery->whereMonth('tanggal_kerusakan', $bulan);
        }

        if ($departemen !== '') {

            $woQuery->whereRaw(
                'LOWER(TRIM(departemen)) = ?',
                [strtolower($departemen)]
            );
        }

        if ($statusFilter !== '') {

            $woQuery->whereRaw(
                'LOWER(TRIM(status)) = ?',
                [strtolower($statusFilter)]
            );
        }

        if ($prioritasFilter !== '') {

            $woQuery->whereRaw(
                'LOWER(TRIM(priority)) = ?',
                [strtolower($prioritasFilter)]
            );
        }


        $woRows = $woQuery->get();


        /*
        |----------------------------------------------------------------------
        | KPI STATUS WO
        |----------------------------------------------------------------------
        */

        $statusKeys = [

            'OPEN',
            'DITERIMA',
            'SCHEDULED',
            'IN PROGRESS',
            'PENDING',
            'SERVICE LUAR',
            'DITOLAK',
            'CLOSE',
        ];

        $statusCounts = [];

        foreach ($statusKeys as $key) {

            $statusCounts[$key] = $woRows->filter(
                fn ($w) => $this->matchStatus($w->status, $key)
            )->count();
        }

        $totalWo = $woRows->count();


        /*
        |----------------------------------------------------------------------
        | OVERDUE & ON-TIME
        |----------------------------------------------------------------------
        |
        | Overdue: belum CLOSE dan melewati batas waktu
        | berdasarkan prioritas.
        |
        */

        $slaHours = [

            'EMERGENCY' => 8,
            'URGENT' => 24,
            'NORMAL' => 72,
        ];

        $now = now();

        $overdueWo = 0;

        $closedOnTime = 0;

        $closedTotal = 0;

        foreach ($woRows as $w) {

            $closed =
                $this->matchStatus($w->status, 'CLOSE');

            $prioritasValue = strtoupper(
                trim(
                    (string) (
                        $w->priority
                        ?: 'NORMAL'
                    )
                )
            );

            $sla =
                $slaHours[$prioritasValue]
                ?? $slaHours['NORMAL'];

            $reportedAt = $this->woReportedAt($w);

            $finishedAt = $this->woFinishedAt($w);

            if (!$closed && $reportedAt) {

                if (
                    $now->greaterThan(
                        $reportedAt->copy()->addHours($sla)
                    )
                ) {
                    $overdueWo++;
                }
            }

            if ($closed) {

                $closedTotal++;

                if (
                    $reportedAt
                    && $finishedAt
                    && $finishedAt->lessThanOrEqualTo(
                        $reportedAt->copy()->addHours($sla)
                    )
                ) {
                    $closedOnTime++;
                }
            }
        }


        /*
        |----------------------------------------------------------------------
        | KPI WAKTU (LEAD TIME / WORK TIME / PENDING TIME)
        |----------------------------------------------------------------------
        |
        | Prioritas memakai histori status bila tersedia,
        | jika kosong fallback ke tanggal pada work_orders.
        |
        */

        $leadTimes = [];

        $workTimes = [];

        $totalPendingSeconds = 0;

        $delayReasonTotals = [];

        $historiesByWo = [];

        $historyIds = $woRows->pluck('id')->all();

        if (!empty($historyIds)) {

            $historiesByWo = \App\Models\WorkOrder\WorkOrderStatusHistory::query()
                ->whereIn('work_order_id', $historyIds)
                ->orderBy('started_at')
                ->get()
                ->groupBy('work_order_id');
        }

        foreach ($woRows as $w) {

            $reportedAt = $this->woReportedAt($w);

            $finishedAt = $this->woFinishedAt($w);

            $startWorkAt = $this->woStartWorkAt($w);

            if (
                $reportedAt
                && $finishedAt
            ) {
                $leadTimes[] =
                    $finishedAt->diffInMinutes($reportedAt);
            }

            if (
                $startWorkAt
                && $finishedAt
            ) {
                $workTimes[] =
                    $finishedAt->diffInMinutes($startWorkAt);
            }


            /*
            | Pending time dari histori.
            */

            $pendingSecondsWo = 0;

            $rows =
                $historiesByWo[$w->id]
                ?? collect();

            foreach ($rows as $h) {

                if (
                    strtoupper(trim($h->status))
                    !== 'PENDING'
                ) {
                    continue;
                }

                $start =
                    $h->started_at
                    ? \Carbon\Carbon::parse($h->started_at)
                    : null;

                $end =
                    $h->ended_at
                    ? \Carbon\Carbon::parse($h->ended_at)
                    : ($this->matchStatus($w->status, 'CLOSE')
                        ? ($finishedAt ?? $now)
                        : $now);

                if ($start) {

                    $pendingSecondsWo +=
                        $start->diffInSeconds($end);


                    /*
                    | Rekap delay reason.
                    */

                    $reasonKey = strtoupper(
                        trim(
                            (string) ($h->alasan ?: 'LAINNYA')
                        )
                    );

                    $delayReasonTotals[$reasonKey] =
                        ($delayReasonTotals[$reasonKey] ?? 0)
                        + $start->diffInMinutes($end);
                }
            }


            /*
            | Fallback pending tanpa histori:
            | tidak ada data, jangan mengarang.
            */

            if ($pendingSecondsWo > 0) {
                // sudah ditambahkan via delay totals
            }
        }


        $avgLeadMinutes =
            count($leadTimes) > 0
                ? (int) round(array_sum($leadTimes) / count($leadTimes))
                : 0;

        $avgWorkMinutes =
            count($workTimes) > 0
                ? (int) round(array_sum($workTimes) / count($workTimes))
                : 0;


        /*
        |----------------------------------------------------------------------
        | KPI USER
        |----------------------------------------------------------------------
        */

        $onlineThreshold = now()->subMinutes(
            \App\Http\Middleware\UpdateLastActivity::ONLINE_THRESHOLD_MINUTES
        );

        $userStats = [

            'total' => \App\Models\User::count(),

            'aktif' => \App\Models\User::whereRaw(
                'LOWER(TRIM(status)) = ?',
                ['aktif']
            )->count(),

            'nonaktif' => \App\Models\User::whereRaw(
                'LOWER(TRIM(status)) <> ?',
                ['aktif']
            )->orWhereNull('status')->count(),

            'online' => \App\Models\User::whereNotNull('last_activity_at')
                ->where('last_activity_at', '>=', $onlineThreshold)
                ->count(),

            'offline' => \App\Models\User::query()
                ->where(function ($q) use ($onlineThreshold) {

                    $q->whereNull('last_activity_at')
                        ->orWhere('last_activity_at', '<', $onlineThreshold);
                })
                ->count(),

            'loginHariIni' => \App\Models\User::whereDate(
                'last_login_at',
                today()
            )->count(),
        ];


        /*
        |----------------------------------------------------------------------
        | KPI INVENTORY
        |----------------------------------------------------------------------
        */

        $totalStock = (int) Barang::sum('stok');

        $totalItems = Barang::count();

        $lowStockItems = Barang::query()
            ->whereNotNull('stok_minimum')
            ->whereColumn('stok', '<=', 'stok_minimum')
            ->count();

        $barangMasukQty = (int) BarangMasuk::query()
            ->whereBetween('tanggal_masuk', [$startDate, $endDate])
            ->where('status', '<>', 'CANCELLED')
            ->sum('qty');

        $barangMasukCount = BarangMasuk::query()
            ->whereBetween('tanggal_masuk', [$startDate, $endDate])
            ->where('status', '<>', 'CANCELLED')
            ->count();

        $barangKeluarQty = (int) BarangKeluar::query()
            ->whereBetween('tanggal_keluar', [$startDate, $endDate])
            ->where('status', '<>', 'CANCELLED')
            ->sum('qty');

        $barangKeluarCount = BarangKeluar::query()
            ->whereBetween('tanggal_keluar', [$startDate, $endDate])
            ->where('status', '<>', 'CANCELLED')
            ->count();


        /*
        |----------------------------------------------------------------------
        | KPI MESIN & KW PER LINE
        |----------------------------------------------------------------------
        */

        $totalMesin = \App\Models\Machine\Machine::count();

        $mesinAktif = \App\Models\Machine\Machine::query()
            ->whereRaw(
                "LOWER(TRIM(status)) = ?",
                ['aktif']
            )
            ->count();

        $mesinTidakAktif =
            max(0, $totalMesin - $mesinAktif);

        $totalKwAll = (float) \App\Models\Machine\Machine::sum('kw');

        $kwPerArea = \App\Models\Machine\Area::query()
            ->withCount('machines')
            ->withSum('machines', 'kw')
            ->orderByDesc('machines_sum_kw')
            ->get([
                'id',
                'nama_area',
                'keterangan',
            ]);


        /*
        |----------------------------------------------------------------------
        | KINERJA PER DEPARTEMEN
        |----------------------------------------------------------------------
        |
        | Rata-rata kerusakan (lapor -> selesai) dan rata-rata
        | perbaikan (mulai kerja -> selesai) per departemen,
        | dihitung dari baris WO terfilter.
        |
        */

        $deptPerfMap = [];

        foreach ($woRows as $w) {

            $deptKey = strtoupper(
                trim((string) ($w->departemen ?: 'TANPA DEPARTEMEN'))
            );

            if (!isset($deptPerfMap[$deptKey])) {

                $deptPerfMap[$deptKey] = [
                    'departemen' => $deptKey,
                    'total'      => 0,
                    'selesai'    => 0,
                    'lead'       => [],
                    'work'       => [],
                ];
            }

            $bucket =& $deptPerfMap[$deptKey];

            $bucket['total']++;

            $closed = $this->matchStatus($w->status, 'CLOSE');

            if ($closed) {
                $bucket['selesai']++;
            }

            $reportedAt = $this->woReportedAt($w);

            $finishedAt = $this->woFinishedAt($w);

            $startWorkAt = $this->woStartWorkAt($w);

            if ($reportedAt && $finishedAt) {
                $bucket['lead'][] =
                    $finishedAt->diffInMinutes($reportedAt);
            }

            if ($startWorkAt && $finishedAt) {
                $bucket['work'][] =
                    $finishedAt->diffInMinutes($startWorkAt);
            }
        }

        $deptPerformance = collect($deptPerfMap)
            ->map(function ($row) {

                $row['avgLeadMinutes'] =
                    count($row['lead']) > 0
                        ? (int) round(array_sum($row['lead']) / count($row['lead']))
                        : null;

                $row['avgWorkMinutes'] =
                    count($row['work']) > 0
                        ? (int) round(array_sum($row['work']) / count($row['work']))
                        : null;

                unset($row['lead'], $row['work']);

                return $row;
            })
            ->sortByDesc('total')
            ->take(10)
            ->values();


        /*
        |----------------------------------------------------------------------
        | GRAFIK
        |----------------------------------------------------------------------
        */

        $monthNames = [

            'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun',
            'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des',
        ];


        /*
        | Trend 12 bulan untuk tahun terpilih
        | (tanpa filter bulan agar tren terlihat).
        */

        $trendQuery = WorkOrder::query()
            ->whereBetween('tanggal_kerusakan', [$startDate, $endDate]);

        if ($bulan) {

            $trendQuery->whereMonth('tanggal_kerusakan', $bulan);
        }

        if ($departemen !== '') {

            $trendQuery->whereRaw(
                'LOWER(TRIM(departemen)) = ?',
                [strtolower($departemen)]
            );
        }

        $trendRows = $trendQuery->get(['tanggal_kerusakan', 'tujuan']);

        $trendValues = array_fill(0, 12, 0);

        foreach ($trendRows as $row) {

            try {
                $m = \Carbon\Carbon::parse($row->tanggal_kerusakan)->month;
            } catch (\Throwable $e) {
                continue;
            }

            $trendValues[$m - 1]++;
        }


        /*
        | Permintaan pekerjaan (kolom tujuan:
        | PREV-MAINT / MEKANIK-MAINTENANCE).
        | Mengikuti semantik trend: tahun penuh,
        | tanpa filter bulan.
        */

        $tujuanKeys = $trendRows
            ->map(
                fn ($row) => strtoupper(
                    trim((string) ($row->tujuan ?: 'LAINNYA'))
                )
            )
            ->unique()
            ->sort()
            ->values();

        $tujuanMonthly = $tujuanKeys->map(
            function ($key) use ($trendRows) {

                $values = array_fill(0, 12, 0);

                foreach ($trendRows as $row) {

                    $rowKey = strtoupper(
                        trim((string) ($row->tujuan ?: 'LAINNYA'))
                    );

                    if ($rowKey !== $key) {
                        continue;
                    }

                    try {
                        $m = \Carbon\Carbon::parse(
                            $row->tanggal_kerusakan
                        )->month;
                    } catch (\Throwable $e) {
                        continue;
                    }

                    $values[$m - 1]++;
                }

                return $values;
            }
        );

        $tujuanTotals = $tujuanMonthly->map(
            fn ($values) => array_sum($values)
        );


        /*
        | Status chart (semua status urut).
        */

        $statusChartLabels = [];

        $statusChartValues = [];

        foreach ($statusKeys as $key) {

            if ($statusCounts[$key] > 0) {

                $statusChartLabels[] = $key;
                $statusChartValues[] = $statusCounts[$key];
            }
        }


        /*
        | Prioritas chart.
        */

        $prioritasKeys = ['NORMAL', 'URGENT', 'EMERGENCY'];

        $prioritasValues = [];

        foreach ($prioritasKeys as $pk) {

            $prioritasValues[] = $woRows->filter(
                fn ($w) => strtoupper(
                    trim(
                        (string) (
                            $w->priority
                            ?: 'NORMAL'
                        )
                    )
                ) === $pk
            )->count();
        }


        /*
        | Top mesin & area paling sering rusak
        | (dari seluruh WO terfilter, max 8).
        */

        $topMesinGroups = $woRows
            ->filter(
                fn ($w) => trim((string) ($w->mesin ?? '')) !== ''
            )
            ->groupBy(
                fn ($w) => strtoupper(trim((string) $w->mesin))
            )
            ->map(fn ($group) => $group->count())
            ->sortByDesc(fn ($count) => $count)
            ->slice(0, 8);

        $topMesinLabels = $topMesinGroups->keys()->values();

        $topMesinValues = $topMesinGroups->values();

        $topAreaGroups = $woRows
            ->filter(
                fn ($w) => trim((string) ($w->area ?? '')) !== ''
            )
            ->groupBy(
                fn ($w) => strtoupper(trim((string) $w->area))
            )
            ->map(fn ($group) => $group->count())
            ->sortByDesc(fn ($count) => $count)
            ->slice(0, 8);

        $topAreaLabels = $topAreaGroups->keys()->values();

        $topAreaValues = $topAreaGroups->values();


        /*
        | Departemen chart (dari database).
        */

        $departemenGroups = WorkOrder::query()
            ->whereBetween('tanggal_kerusakan', [$startDate, $endDate]);

        if ($bulan) {

            $departemenGroups->whereMonth('tanggal_kerusakan', $bulan);
        }

        if ($departemen !== '') {

            $departemenGroups->whereRaw(
                'LOWER(TRIM(departemen)) = ?',
                [strtolower($departemen)]
            );
        }

        $departemenGroups = $departemenGroups
            ->whereNotNull('departemen')
            ->where('departemen', '<>', '')
            ->selectRaw('departemen as label, COUNT(*) as total')
            ->groupBy('departemen')
            ->orderByDesc('total')
            ->limit(8)
            ->get();


        /*
        | Departemen list untuk filter dropdown.
        */

        $departemenList = WorkOrder::query()
            ->whereNotNull('departemen')
            ->where('departemen', '<>', '')
            ->distinct()
            ->orderBy('departemen')
            ->pluck('departemen');


        /*
        | Inventory masuk / keluar per bulan
        | untuk tahun terpilih.
        */

        $inventoryInValues = array_fill(0, 12, 0);

        $masukQuery = BarangMasuk::query()
            ->whereBetween('tanggal_masuk', [$startDate, $endDate])
            ->where('status', '<>', 'CANCELLED');

        if ($bulan) {

            $masukQuery->whereMonth('tanggal_masuk', $bulan);
        }

        $masukRows = $masukQuery->get(['tanggal_masuk', 'qty']);

        foreach ($masukRows as $row) {

            if (empty($row->tanggal_masuk)) {
                continue;
            }

            try {
                $date = \Carbon\Carbon::parse($row->tanggal_masuk);
            } catch (\Throwable $e) {
                continue;
            }

            $inventoryInValues[$date->month - 1] += (int) $row->qty;
        }

        $inventoryOutValues = array_fill(0, 12, 0);

        $keluarQuery = BarangKeluar::query()
            ->whereBetween('tanggal_keluar', [$startDate, $endDate])
            ->where('status', '<>', 'CANCELLED');

        if ($bulan) {

            $keluarQuery->whereMonth('tanggal_keluar', $bulan);
        }

        $keluarRows = $keluarQuery->get(['tanggal_keluar', 'qty']);

        foreach ($keluarRows as $row) {

            if (empty($row->tanggal_keluar)) {
                continue;
            }

            try {
                $date = \Carbon\Carbon::parse($row->tanggal_keluar);
            } catch (\Throwable $e) {
                continue;
            }

            $inventoryOutValues[$date->month - 1] += (int) $row->qty;
        }


        /*
        | Tahun list untuk filter (2026 - 2045).
        */

        $tahunList = range(
            2026,
            max(2045, now()->year)
        );


        /*
        | Monitoring user (tabel).
        */

        $monitoringUsers = \App\Models\User::query()
            ->orderByDesc('last_activity_at')
            ->limit(50)
            ->get([
                'id',
                'name',
                'role',
                'bagian',
                'status',
                'foto',
                'last_login_at',
                'last_activity_at',
            ]);


        /*
        | Completion & on-time rate.
        */

        $completionRate =
            $totalWo > 0
                ? (int) round($statusCounts['CLOSE'] / $totalWo * 100)
                : 0;

        $onTimeRate =
            $closedTotal > 0
                ? (int) round($closedOnTime / $closedTotal * 100)
                : 0;


        return [

            /* FILTER */

            'filterTahun' => $tahun,

            'filterBulan' => $bulan,

            'filterDepartemen' => $departemen,

            'filterStatus' => $statusFilter,

            'filterPrioritas' => $prioritasFilter,

            'tahunList' => $tahunList,

            'departemenList' => $departemenList,

            'statusKeys' => $statusKeys,

            'prioritasKeys' => $prioritasKeys,

            /* KPI WO */

            'totalWo' => $totalWo,

            'statusCounts' => $statusCounts,

            'overdueWo' => $overdueWo,

            'closedOnTime' => $closedOnTime,

            'closedTotal' => $closedTotal,

            /* RATES */

            'completionRate' => $completionRate,

            'onTimeRate' => $onTimeRate,

            'avgLeadMinutes' => $avgLeadMinutes,

            'avgWorkMinutes' => $avgWorkMinutes,

            /* KPI USER */

            'userStats' => $userStats,

            'monitoringUsers' => $monitoringUsers,

            /* GRAFIK */

            'monthNames' => $monthNames,

            'trendValues' => $trendValues,

            'statusChartLabels' => $statusChartLabels,

            'statusChartValues' => $statusChartValues,

            'prioritasValues' => $prioritasValues,

            'topMesinLabels' => $topMesinLabels,

            'topMesinValues' => $topMesinValues,

            'topAreaLabels' => $topAreaLabels,

            'topAreaValues' => $topAreaValues,

            'tujuanKeys' => $tujuanKeys,

            'tujuanMonthly' => $tujuanMonthly,

            'tujuanTotals' => $tujuanTotals,

            'departemenChartLabels' =>
                $departemenGroups->pluck('label'),

            'departemenChartValues' =>
                $departemenGroups->pluck('total'),

            'delayReasonTotals' => $delayReasonTotals,

            /* KPI INVENTORY */

            'totalStock' => $totalStock,

            'totalItems' => $totalItems,

            'lowStockItems' => $lowStockItems,

            'barangMasukQty' => $barangMasukQty,

            'barangMasukCount' => $barangMasukCount,

            'barangKeluarQty' => $barangKeluarQty,

            'barangKeluarCount' => $barangKeluarCount,

            /* KPI MESIN & LINE */

            'totalMesin' => $totalMesin,

            'mesinAktif' => $mesinAktif,

            'mesinTidakAktif' => $mesinTidakAktif,

            'totalKwAll' => $totalKwAll,

            'kwPerArea' => $kwPerArea,

            /* KINERJA DEPARTEMEN */

            'deptPerformance' => $deptPerformance,

            /* GRAFIK INVENTORY */

            'inventoryInValues' => $inventoryInValues,

            'inventoryOutValues' => $inventoryOutValues,
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | STATISTIK DASHBOARD USER
    |--------------------------------------------------------------------------
    */

    private function getUserStats($user): array
    {
        $userName = trim((string) $user->name);

        $base = WorkOrder::query()
            ->whereRaw(
                'LOWER(TRIM(dibuat_oleh)) = ?',
                [strtolower($userName)]
            );

        $total = (clone $base)->count();

        $open = (clone $base)->whereRaw(
            "LOWER(TRIM(status)) NOT IN ('close','done','selesai')"
        )->count();

        $selesai = (clone $base)->whereRaw(
            "LOWER(TRIM(status)) IN ('close','done','selesai')"
        )->count();

        $recent = (clone $base)
            ->orderByDesc('created_at')
            ->limit(8)
            ->get([
                'id',
                'no_wo',
                'kategori',
                'job',
                'status',
                'priority',
                'tanggal_kerusakan',
                'created_at',
            ]);

        return [

            'myTotal' => $total,

            'myOpen' => $open,

            'mySelesai' => $selesai,

            'myRecent' => $recent,
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | HELPER STATUS
    |--------------------------------------------------------------------------
    */

    private function matchStatus(?string $value, string $key): bool
    {
        return strtolower(trim((string) $value))
            === strtolower(trim($key));
    }


    private function woReportedAt($w): ?\Carbon\Carbon
    {
        if (!empty($w->tanggal_kerusakan)) {

            try {

                $date = \Carbon\Carbon::parse($w->tanggal_kerusakan);

                if (!empty($w->jam_kerusakan)) {
                    $date->setTimeFromTimeString($w->jam_kerusakan);
                } else {
                    $date->startOfDay();
                }

                return $date;

            } catch (\Throwable $e) {
                return null;
            }
        }

        return null;
    }


    private function woStartWorkAt($w): ?\Carbon\Carbon
    {

        if (!empty($w->tanggal_mulai_perbaikan)) {

            try {

                $date = \Carbon\Carbon::parse(
                    $w->tanggal_mulai_perbaikan
                );

                if (!empty($w->jam_mulai_perbaikan)) {
                    $date->setTimeFromTimeString(
                        $w->jam_mulai_perbaikan
                    );
                } else {
                    $date->startOfDay();
                }

                return $date;

            } catch (\Throwable $e) {
                return null;
            }
        }

        return null;
    }


    private function woFinishedAt($w): ?\Carbon\Carbon
    {

        if (!empty($w->tanggal_selesai_perbaikan)) {

            try {

                $date = \Carbon\Carbon::parse(
                    $w->tanggal_selesai_perbaikan
                );

                if (!empty($w->jam_selesai_perbaikan)) {
                    $date->setTimeFromTimeString(
                        $w->jam_selesai_perbaikan
                    );
                } else {
                    $date->endOfDay();
                }

                return $date;

            } catch (\Throwable $e) {
                return null;
            }
        }

        return null;
    }


    /*
    |--------------------------------------------------------------------------
    | STATISTIK DASHBOARD MAINTENANCE
    |--------------------------------------------------------------------------
    |
    | Mengikuti pola ManagerDashboardController:
    | data aktual dari tabel work_orders, barangs,
    | barang_masuks dan barang_keluars.
    |
    | Scope waktu: tahun berjalan (Januari - Desember).
    |
    */

    private function getMaintenanceStats($user, $request): array
    {
        $year = (int) $request->input('tahun', now()->year);

        if ($year < 2000 || $year > 2100) {
            $year = now()->year;
        }

        $startDate = \Carbon\Carbon::create($year, 1, 1)->startOfYear();

        $endDate = \Carbon\Carbon::create($year, 12, 31)->endOfYear();

        $baseQuery = WorkOrder::query()
            ->visibleTo($user)
            ->whereBetween('tanggal_kerusakan', [$startDate, $endDate]);


        /*
        |----------------------------------------------------------------------
        | STATUS WORK ORDER
        |----------------------------------------------------------------------
        */

        $statusValues = (clone $baseQuery)->pluck('status');

        $totalWorkOrders = $statusValues->count();

        $openWorkOrders = $this->countIn($statusValues, ['open']);

        $inProgressWorkOrders = $this->countIn($statusValues, [
            'in progress',
            'inprogress',
            'in-progres',
            'in progres',
            'in proses',
            'progress',
        ]);

        $completedWorkOrders = $this->countIn($statusValues, [
            'close',
            'done',
            'selesai',
            'complete',
            'completed',
        ]);


        /*
        |----------------------------------------------------------------------
        | PRIORITAS WORK ORDER
        |----------------------------------------------------------------------
        |
        | Nilai prioritas pada sistem:
        | NORMAL, URGENT, EMERGENCY.
        |
        */

        $priorityValues = (clone $baseQuery)->pluck('priority');

        $lowPriorityWorkOrders = $this->countIn($priorityValues, ['normal']);

        $highPriorityWorkOrders = $this->countIn($priorityValues, ['urgent']);

        $criticalPriorityWorkOrders = $this->countIn($priorityValues, ['emergency']);

        $mediumPriorityWorkOrders = $priorityValues
            ->filter(fn ($p) => trim((string) $p) !== '')
            ->count()
            - $lowPriorityWorkOrders
            - $highPriorityWorkOrders
            - $criticalPriorityWorkOrders;

        if ($mediumPriorityWorkOrders < 0) {
            $mediumPriorityWorkOrders = 0;
        }


        /*
        |----------------------------------------------------------------------
        | TOP KERUSAKAN / MESIN / AREA
        |----------------------------------------------------------------------
        */

        $topDamages = (clone $baseQuery)
            ->whereNotNull('job')
            ->where('job', '<>', '')
            ->selectRaw('job as nama, COUNT(*) as jumlah')
            ->groupBy('job')
            ->orderByDesc('jumlah')
            ->limit(5)
            ->get();

        $topMachines = (clone $baseQuery)
            ->whereNotNull('mesin')
            ->where('mesin', '<>', '')
            ->selectRaw('mesin as nama_mesin, COUNT(*) as jumlah')
            ->groupBy('mesin')
            ->orderByDesc('jumlah')
            ->limit(5)
            ->get();

        $topAreas = (clone $baseQuery)
            ->whereNotNull('area')
            ->where('area', '<>', '')
            ->selectRaw('area as nama_area, COUNT(*) as jumlah')
            ->groupBy('area')
            ->orderByDesc('jumlah')
            ->limit(5)
            ->get();


        /*
        |----------------------------------------------------------------------
        | INVENTORY
        |----------------------------------------------------------------------
        */

        $totalStockQuery = Barang::query()->visibleTo($user);
        $totalStock = (int) (clone $totalStockQuery)->sum('stok');

        $totalItems = (clone $totalStockQuery)->count();

        $totalSpareparts = MachineSparepart::count();

        $lowStockItems = (clone $totalStockQuery)
            ->whereNotNull('stok_minimum')
            ->whereColumn('stok', '<=', 'stok_minimum')
            ->count();


        /*
        |----------------------------------------------------------------------
        | PEMAKAIAN BARANG (BARANG KELUAR)
        |----------------------------------------------------------------------
        */

        $usageRows = BarangKeluar::query()
            ->where('status', '<>', 'CANCELLED')
            ->whereHas('barang', function ($q) use ($user) {
                DepartmentAccess::applyBarangScope($q, $user);
            })
            ->selectRaw('barang_id, SUM(qty) as total_qty')
            ->groupBy('barang_id')
            ->orderByDesc('total_qty')
            ->with('barang')
            ->limit(5)
            ->get();

        $topUsedItems = $usageRows->map(fn ($row) => [
            'nama_barang' => $row->barang?->nama_spesifikasi ?? '-',
            'qty' => (int) $row->total_qty,
        ]);

        $mostUsedItem = $topUsedItems->first()['nama_barang'] ?? '-';

        $highestUsage = (int) ($topUsedItems->first()['qty'] ?? 0);

        $averageUsage = (int) round(
            (float) BarangKeluar::query()
                ->where('status', '<>', 'CANCELLED')
                ->whereHas('barang', function ($q) use ($user) {
                    DepartmentAccess::applyBarangScope($q, $user);
                })
                ->avg('qty')
        );


        /*
        |----------------------------------------------------------------------
        | GRAFIK BULANAN
        |----------------------------------------------------------------------
        */

        $monthNames = [
            'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun',
            'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des',
        ];

        $monthlyWorkOrders = array_fill(0, 12, 0);

        $rows = (clone $baseQuery)->get(['tanggal_kerusakan']);

        foreach ($rows as $row) {

            if (empty($row->tanggal_kerusakan)) {
                continue;
            }

            try {
                $date = \Carbon\Carbon::parse($row->tanggal_kerusakan);
            } catch (\Throwable $e) {
                continue;
            }

            $monthlyWorkOrders[$date->month - 1]++;
        }


        /*
        |----------------------------------------------------------------------
        | GRAFIK STATUS & KATEGORI
        |----------------------------------------------------------------------
        */

        $statusGroups = (clone $baseQuery)
            ->whereNotNull('status')
            ->where('status', '<>', '')
            ->selectRaw('status as label, COUNT(*) as total')
            ->groupBy('status')
            ->orderByDesc('total')
            ->get();

        $categoryGroups = (clone $baseQuery)
            ->whereNotNull('kategori')
            ->where('kategori', '<>', '')
            ->selectRaw('kategori as label, COUNT(*) as total')
            ->groupBy('kategori')
            ->orderByDesc('total')
            ->get();


        /*
        |----------------------------------------------------------------------
        | GRAFIK INVENTORY MASUK / KELUAR PER BULAN
        |----------------------------------------------------------------------
        */

        $inventoryInValues = array_fill(0, 12, 0);

        $masukRows = BarangMasuk::query()
            ->whereBetween('tanggal_masuk', [$startDate, $endDate])
            ->where('status', '<>', 'CANCELLED')
            ->get(['tanggal_masuk', 'qty']);

        foreach ($masukRows as $row) {

            if (empty($row->tanggal_masuk)) {
                continue;
            }

            try {
                $date = \Carbon\Carbon::parse($row->tanggal_masuk);
            } catch (\Throwable $e) {
                continue;
            }

            $inventoryInValues[$date->month - 1] += (int) $row->qty;
        }

        $inventoryOutValues = array_fill(0, 12, 0);

        $keluarRows = BarangKeluar::query()
            ->whereBetween('tanggal_keluar', [$startDate, $endDate])
            ->where('status', '<>', 'CANCELLED')
            ->get(['tanggal_keluar', 'qty']);

        foreach ($keluarRows as $row) {

            if (empty($row->tanggal_keluar)) {
                continue;
            }

            try {
                $date = \Carbon\Carbon::parse($row->tanggal_keluar);
            } catch (\Throwable $e) {
                continue;
            }

            $inventoryOutValues[$date->month - 1] += (int) $row->qty;
        }

        $totalWoMekanik = 0;

        if (DepartmentAccess::canAccessDepartment($user, DepartmentAccess::MEKANIK_MAINT)) {
            $totalWoMekanik = WorkOrder::query()
                ->visibleTo($user, DepartmentAccess::MEKANIK_MAINT)
                ->whereBetween('tanggal_kerusakan', [$startDate, $endDate])
                ->count();
        }

        $totalWoPrev = 0;

        if (DepartmentAccess::canAccessDepartment($user, DepartmentAccess::PREV_MAINT)) {
            $totalWoPrev = WorkOrder::query()
                ->visibleTo($user, DepartmentAccess::PREV_MAINT)
                ->whereBetween('tanggal_kerusakan', [$startDate, $endDate])
                ->count();
        }

        $pendingFollowUp = $this->countIn($statusValues, [
            'open',
            'diterima',
        ]);

        $newWoCount = \App\Models\Notification::query()
            ->where('user_id', $user->id)
            ->where('status', 'UNREAD')
            ->where('type', 'WO_CREATED')
            ->whereNotNull('work_order_id')
            ->count();


        $tahunList = range(
            2026,
            max(2045, now()->year)
        );

        return [

            // FILTER

            'filterTahun' => $year,

            'tahunList' => $tahunList,

            // STATISTIK WORK ORDER

            'totalWorkOrders' => $totalWorkOrders,

            'openWorkOrders' => $openWorkOrders,

            'inProgressWorkOrders' => $inProgressWorkOrders,

            'completedWorkOrders' => $completedWorkOrders,

            'totalWoMekanik' => $totalWoMekanik,

            'totalWoPrev' => $totalWoPrev,

            'pendingFollowUp' => $pendingFollowUp,

            'newWoCount' => $newWoCount,

            // PRIORITAS

            'lowPriorityWorkOrders' => $lowPriorityWorkOrders,

            'mediumPriorityWorkOrders' => $mediumPriorityWorkOrders,

            'highPriorityWorkOrders' => $highPriorityWorkOrders,

            'criticalPriorityWorkOrders' => $criticalPriorityWorkOrders,

            // TOP LIST

            'topDamages' => $topDamages,

            'topMachines' => $topMachines,

            'topAreas' => $topAreas,

            // INVENTORY

            'totalStock' => $totalStock,

            'totalItems' => $totalItems,

            'totalSpareparts' => $totalSpareparts,

            'lowStockItems' => $lowStockItems,

            // PEMAKAIAN

            'averageUsage' => $averageUsage,

            'highestUsage' => $highestUsage,

            'mostUsedItem' => $mostUsedItem,

            'topUsedItems' => $topUsedItems,

            // GRAFIK

            'monthlyLabels' => $monthNames,

            'monthlyWorkOrders' => $monthlyWorkOrders,

            'statusLabels' => $statusGroups->pluck('label'),

            'statusValues' => $statusGroups->pluck('total'),

            'categoryLabels' => $categoryGroups->pluck('label'),

            'categoryValues' => $categoryGroups->pluck('total'),

            'inventoryLabels' => $monthNames,

            'inventoryInValues' => $inventoryInValues,

            'inventoryOutValues' => $inventoryOutValues,
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | HITUNG NILAI DALAM KOLEKSI (CASE-INSENSITIVE)
    |--------------------------------------------------------------------------
    */

    private function countIn($values, array $needles): int
    {
        return $values
            ->filter(function ($value) use ($needles) {

                $value = strtolower(trim((string) $value));

                return in_array($value, $needles, true);
            })
            ->count();
    }
}
