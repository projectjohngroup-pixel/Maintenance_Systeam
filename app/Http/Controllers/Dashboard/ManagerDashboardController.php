<?php
namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Inventory\Barang;
use App\Models\Inventory\BarangKeluar;
use App\Models\Inventory\BarangMasuk;
use App\Models\WorkOrder\WorkOrder;
use App\Models\WorkOrder\WorkOrderStatusHistory;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ManagerDashboardController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | KONSTANTA STATUS & PRIORITAS
    |--------------------------------------------------------------------------
    */

    private const IN_PROGRESS_LIST = [
        'in progress',
        'inprogress',
        'in-progres',
        'in progres',
        'in proses',
        'progress',
        'proses',
        'on progress',
        'onprocess',
        'on process',
    ];

    private const DONE_LIST = [
        'close',
        'done',
        'selesai',
        'complete',
        'completed',
        'finished',
    ];

    private const SLA_HOURS = [
        'EMERGENCY' => 8,
        'URGENT'    => 24,
        'NORMAL'    => 72,
    ];

    /*
    |--------------------------------------------------------------------------
    | HALAMAN DASHBOARD MANAGER
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        return view('dashboard.manager.index');
    }


    /*
    |--------------------------------------------------------------------------
    | DATA ANALITIK (JSON) UNTUK SELURUH DASHBOARD
    |--------------------------------------------------------------------------
    |
    | Semua angka dihitung dari database aktual.
    | Filter periode: day / week / month / year.
    |
    */

    public function data(Request $request)
    {

        $request->validate([
            'period' => [
                'nullable',
                'string',
                'in:day,week,month,year',
            ],
        ]);

        $window = $this->resolvePeriod(
            (string) $request->input('period', 'month')
        );


        /*
        |----------------------------------------------------------------------
        | WORK ORDER - MASUK & SELESAI
        |----------------------------------------------------------------------
        */

        $woInWindow =
            $this->woCreatedQuery($window)->get();

        $woFinishedWindow =
            $this->woFinishedQuery($window)->get();

        $prevWoIn =
            $this->woCreatedQuery($window['prev'])->count();

        $prevWoFinished =
            $this->woFinishedQuery($window['prev'])->count();


        $woMasuk   = $woInWindow->count();
        $woSelesai = $woFinishedWindow->count();


        /*
        |----------------------------------------------------------------------
        | STATUS SNAPSHOT (WO MASUK PADA PERIODE)
        |----------------------------------------------------------------------
        */

        $statusCounts = [

            'open' =>
                $this->countStatus($woInWindow, ['open']),

            'progress' =>
                $this->countStatus($woInWindow, self::IN_PROGRESS_LIST),

            'hold' =>
                $this->countStatus($woInWindow, ['pending', 'hold']),

            'selesai' =>
                $this->countStatus($woInWindow, self::DONE_LIST),

        ];


        /*
        |----------------------------------------------------------------------
        | PRIORITAS
        |----------------------------------------------------------------------
        */

        $priorityCounts = [
            'emergency' =>
                $this->countPriority($woInWindow, ['emergency']),
            'urgent' =>
                $this->countPriority($woInWindow, ['urgent']),
            'normal' =>
                $this->countPriority($woInWindow, ['normal']),
        ];

        $prevPriority = $this->priorityCounts(
            $this->woCreatedQuery($window['prev'])->get()
        );


        /*
        |----------------------------------------------------------------------
        | OVERDUE / DELAY
        |----------------------------------------------------------------------
        */

        $now = now();

        $openWoModels =
            $this->woCreatedQuery($window)
                ->whereRaw("LOWER(TRIM(status)) NOT IN ('" . implode("','", self::DONE_LIST) . "')")
                ->get();

        $overdueRows =
            $this->filterOverdue($openWoModels, $now);

        $overdueCount =
            $overdueRows->count();

        $belumSelesai =
            $openWoModels->count();

        $longestOpen =
            $this->longestOpenRow($openWoModels, $now);


        /*
        |----------------------------------------------------------------------
        | WAKTU PENYELESAIAN / PENGERJAAN / RESPONSE
        |----------------------------------------------------------------------
        */

        $completionStats =
            $this->timeStats($woFinishedWindow);

        $responseStats =
            $this->responseStats($woInWindow);


        /*
        |----------------------------------------------------------------------
        | TREND PER BUCKET
        |----------------------------------------------------------------------
        */

        $masukTrend =
            $this->trendByDate(
                $window,
                'tanggal_kerusakan'
            );

        $selesaiTrend =
            $this->trendByDate(
                $window,
                'tanggal_selesai_perbaikan'
            );

        $statusTrend =
            $this->statusTrend($window);

        $priorityTrend =
            $this->priorityTrend($window);

        $kerusakanTrend =
            $masukTrend['values'];

        $inventoryTrend =
            $this->inventoryTrends($window);


        /*
        |----------------------------------------------------------------------
        | RANKING DEPARTMENT / MESIN / AREA / KATEGORI
        |----------------------------------------------------------------------
        */

        $departmentRanking =
            $this->ranking($window, 'departemen', 10);

        $mesinRanking =
            $this->ranking($window, 'mesin', 10);

        $areaRanking =
            $this->ranking($window, 'area', 10);

        $kategoriRanking =
            $this->ranking($window, 'kategori', 10);


        /*
        |----------------------------------------------------------------------
        | DOWNTIME (ESTIMASI DARI DURASI WO)
        |----------------------------------------------------------------------
        */

        $downtime =
            $this->downtimeStats($window);


        /*
        |----------------------------------------------------------------------
        | INVENTORY
        |----------------------------------------------------------------------
        */

        $stok =
            $this->stokSnapshot();

        $topKeluar =
            $this->topBarangKeluar($window, 10);

        $barangKritis =
            $this->barangKritis(10);

        $prevMasuk =
            BarangMasuk::query()
                ->whereBetween('tanggal_masuk', [
                    $window['prev']['start'],
                    $window['prev']['end'],
                ])
                ->sum('qty');

        $prevKeluar =
            BarangKeluar::query()
                ->whereBetween('tanggal_keluar', [
                    $window['prev']['start'],
                    $window['prev']['end'],
                ])
                ->sum('qty');


        /*
        |----------------------------------------------------------------------
        | ALERT PANEL
        |----------------------------------------------------------------------
        */

        $alerts =
            $this->buildAlerts(
                $priorityCounts,
                $prevPriority,
                $overdueCount,
                $belumSelesai,
                $woSelesai,
                $stok,
            );


        return response()->json([

            'meta' => [
                'period'      => $window['key'],
                'label'       => $window['label'],
                'start'       => $window['start']->toDateString(),
                'end'         => $window['end']->toDateString(),
                'labels'      => $window['labels'],
                'generatedAt' => now()->format('d M Y H:i'),
            ],

            'kpi' => [
                'woMasuk' => [
                    'total' => $woMasuk,
                    'delta' => $this->deltaPercent($prevWoIn, $woMasuk),
                ],
                'woSelesai' => [
                    'total' => $woSelesai,
                    'delta' => $this->deltaPercent($prevWoFinished, $woSelesai),
                ],
                'woOpen' => [
                    'total' => $statusCounts['open'],
                ],
                'woProgress' => [
                    'total' => $statusCounts['progress'],
                ],
                'woHold' => [
                    'total' => $statusCounts['hold'],
                ],
                'woOverdue' => [
                    'total' => $overdueCount,
                ],
                'emergency' => [
                    'total' => $priorityCounts['emergency'],
                    'delta' => $this->deltaPercent($prevPriority['emergency'], $priorityCounts['emergency']),
                ],
                'urgent' => [
                    'total' => $priorityCounts['urgent'],
                    'delta' => $this->deltaPercent($prevPriority['urgent'], $priorityCounts['urgent']),
                ],
                'avgCompletionHours' =>
                    $completionStats['avgCompletionHours'],
                'avgWorkHours' =>
                    $completionStats['avgWorkHours'],
                'avgResponseHours' =>
                    $responseStats['avgResponseHours'],
                'stokAman' =>
                    $stok['aman'],
                'stokMenipis' =>
                    $stok['menipis'],
                'stokHabis' =>
                    $stok['habis'],
                'barangMasukQty' => [
                    'total' => (int) $inventoryTrend['masukTotal'],
                    'delta' => $this->deltaPercent((int) $prevMasuk, (int) $inventoryTrend['masukTotal']),
                ],
                'barangKeluarQty' => [
                    'total' => (int) $inventoryTrend['keluarTotal'],
                    'delta' => $this->deltaPercent((int) $prevKeluar, (int) $inventoryTrend['keluarTotal']),
                ],
                'downtimeHours' =>
                    $downtime['totalHours'],
            ],

            'trends' => [
                'wo' => [
                    'labels'  => $window['labels'],
                    'masuk'   => $masukTrend['values'],
                    'selesai' => $selesaiTrend['values'],
                ],
                'status' => $statusTrend,
                'priority' => $priorityTrend,
                'kerusakanMesin' => [
                    'labels' => $window['labels'],
                    'values' => $kerusakanTrend,
                ],
                'inventory' => [
                    'labels' => $window['labels'],
                    'masuk'  => $inventoryTrend['masukSeries'],
                    'keluar' => $inventoryTrend['keluarSeries'],
                ],
            ],

            'statusCounts' => $statusCounts,

            'priorities' => [
                'counts' => $priorityCounts,
                'rising' => [
                    'emergency' =>
                        $priorityCounts['emergency']
                        > $prevPriority['emergency'],
                    'urgent' =>
                        $priorityCounts['urgent']
                        > $prevPriority['urgent'],
                ],
            ],

            'rankings' => [
                'department' => $departmentRanking,
                'mesin'      => $mesinRanking,
                'area'       => $areaRanking,
                'kategori'   => $kategoriRanking,
            ],

            'completion' => [
                'masuk'     => $woMasuk,
                'selesai'   => $woSelesai,
                'open'      => $statusCounts['open'],
                'progress'  => $statusCounts['progress'],
                'hold'      => $statusCounts['hold'],
                'overdue'   => $overdueCount,
                'rate'      => $woMasuk > 0
                    ? (int) round($woSelesai / $woMasuk * 100)
                    : ($woSelesai > 0 ? 100 : 0),
                'avgCompletionHours' =>
                    $completionStats['avgCompletionHours'],
                'avgWorkHours' =>
                    $completionStats['avgWorkHours'],
                'avgResponseHours' =>
                    $responseStats['avgResponseHours'],
            ],

            'delay' => [
                'terlambat'     => $overdueCount,
                'belumSelesai'  => $belumSelesai,
                'terlama'       => $longestOpen,
                'byArea' =>
                    $this->delayRanking($overdueRows, 'area'),
                'byMesin' =>
                    $this->delayRanking($overdueRows, 'mesin'),
                'byDepartment' =>
                    $this->delayRanking($overdueRows, 'departemen'),
            ],

            'machines' => [
                'topMesin' => $mesinRanking,
                'topArea'  => $areaRanking,
                'topKategori' => $kategoriRanking,
                'downtime' => $downtime,
            ],

            'inventory' => [
                'stok' => $stok,
                'masukTotal' => (int) $inventoryTrend['masukTotal'],
                'keluarTotal' => (int) $inventoryTrend['keluarTotal'],
                'topKeluar' => $topKeluar,
                'kritis' => $barangKritis,
            ],

            'alerts' => $alerts,

            'window' => [
                'start' => $window['start']->toDateTimeString(),
                'end'   => $window['end']->toDateTimeString(),
            ],

        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | DETAIL MONITORING (LIHAT DETAIL) - READ ONLY
    |--------------------------------------------------------------------------
    |
    | Endpoint JSON khusus MANAGER untuk melihat data di balik
    | KPI / grafik Dashboard Manager. Tanpa akses CRUD.
    |
    | Mendukung:
    | - year + month (mode lama)
    | - start + end ISO date (mengikuti filter periode dashboard)
    | - field + value untuk memfilter daftar WO
    |
    */

    public function detail(Request $request)
    {

        /*
        |----------------------------------------------------------------------
        | VALIDASI METRIC
        |----------------------------------------------------------------------
        */

        $request->validate([
            'metric' => [
                'required',
                'string',
                'max:40',
            ],

            'month' => [
                'nullable',
                'integer',
                'min:1',
                'max:12',
            ],

            'field' => [
                'nullable',
                'string',
                'in:departemen,mesin,area,kategori,status,priority',
            ],

            'value' => [
                'nullable',
                'string',
                'max:120',
            ],
        ]);


        /*
        |----------------------------------------------------------------------
        | WINDOW (PERIODE)
        |----------------------------------------------------------------------
        */

        $metric = strtolower(
            trim(
                (string) $request->input('metric')
            )
        );


        if (
            $request->filled('start')
            && $request->filled('end')
        ) {

            try {

                $start = Carbon::parse(
                    $request->input('start')
                )->startOfDay();

                $end = Carbon::parse(
                    $request->input('end')
                )->endOfDay();

            } catch (\Throwable $e) {

                return response()->json(
                    ['message' => 'Rentang tanggal tidak valid.'],
                    422
                );

            }

            if (
                $start->greaterThan($end)
                || $start->diffInDays($end) > 400
            ) {

                return response()->json(
                    ['message' => 'Rentang tanggal terlalu lebar.'],
                    422
                );

            }

            $year = $start->year;

        } else {

            $year = (int) $request->get(
                'year',
                now()->year
            );

            if ($year < 2026 || $year > 2070) {
                $year = now()->year;
            }

            $start = Carbon::create(
                $year,
                1,
                1
            )->startOfYear();

            $end = Carbon::create(
                $year,
                12,
                31
            )->endOfYear();

        }

        $subtitle =
            $start->format('d M Y')
            . ' - '
            . $end->format('d M Y');


        /*
        |----------------------------------------------------------------------
        | QUERY DASAR
        |----------------------------------------------------------------------
        */

        $baseQuery = WorkOrder::query()
            ->whereBetween(
                'tanggal_kerusakan',
                [$start, $end]
            );


        /*
        |----------------------------------------------------------------------
        | FILTER FIELD/VALUE (DRILLDOWN GRAFIK)
        |----------------------------------------------------------------------
        */

        if (
            $request->filled('field')
            && $request->filled('value')
            && $metric === 'wo-list'
        ) {

            $field =
                (string) $request->input('field');

            $value =
                trim(
                    (string) $request->input('value')
                );

            if ($field === 'status') {

                $needles = match (strtolower($value)) {

                    'open' => ['open'],

                    'in proses', 'progress', 'in progress' =>
                        self::IN_PROGRESS_LIST,

                    'hold' => ['pending', 'hold'],

                    default => self::DONE_LIST,

                };

                $baseQuery->where(function ($q) use ($needles) {

                    foreach ($needles as $needle) {

                        $q->orWhereRaw(
                            'LOWER(TRIM(status)) = ?',
                            [$needle]
                        );

                    }

                });

            } elseif ($field === 'priority') {

                $baseQuery->whereRaw(
                    'LOWER(TRIM(priority)) = ?',
                    [strtolower($value)]
                );

            } else {

                $baseQuery->where($field, $value);

            }

            return response()->json(
                $this->woTablePayload(
                    $this->fetchWoRows(clone $baseQuery),
                    'Daftar Work Order',
                    $subtitle . ' - '
                        . ucfirst($field) . ': ' . $value
                )
            );

        }


        /*
        |----------------------------------------------------------------------
        | STATUS & PRIORITAS
        |----------------------------------------------------------------------
        */

        $statusMap = [

            'total' => null,

            'in-progress' => self::IN_PROGRESS_LIST,

            'done' => self::DONE_LIST,

            'open' => ['open'],

            'pending' => ['pending'],

            'hold' => ['pending', 'hold'],

            'close' => ['close'],

        ];

        $priorityMap = [

            'emergency' => ['emergency'],

            'urgent' => ['urgent'],

            'normal' => ['normal'],

        ];


        $rows = collect();


        if (
            array_key_exists($metric, $statusMap)
            || isset($priorityMap[$metric])
        ) {

            $query = clone $baseQuery;

            if (
                array_key_exists($metric, $statusMap)
                && is_array($statusMap[$metric])
            ) {

                $needles = $statusMap[$metric];

                $query->where(function ($q) use ($needles) {

                    foreach ($needles as $needle) {

                        $q->orWhereRaw(
                            'LOWER(TRIM(status)) = ?',
                            [$needle]
                        );

                    }

                });

            } elseif (isset($priorityMap[$metric])) {

                $needles = $priorityMap[$metric];

                $query->where(function ($q) use ($needles) {

                    foreach ($needles as $needle) {

                        $q->orWhereRaw(
                            'LOWER(TRIM(priority)) = ?',
                            [$needle]
                        );

                    }

                });

            }

            $rows = $this->fetchWoRows($query);

        } elseif ($metric === 'overdue') {

            $candidates =
                (clone $baseQuery)
                    ->whereRaw(
                        "LOWER(TRIM(status)) NOT IN ('"
                        . implode("','", self::DONE_LIST)
                        . "')"
                    )
                    ->get();

            $rows =
                $this->filterOverdue($candidates, now())
                    ->values()
                    ->map(fn ($w) => $this->formatWoRow($w));

        } elseif (
            $metric === 'completion'
            || $metric === 'on-time'
        ) {

            /*
            | CLOSE saja; on-time difilter dengan SLA.
            */

            $query = (clone $baseQuery)
                ->whereRaw(
                    "LOWER(TRIM(status)) IN ('"
                    . implode("','", self::DONE_LIST)
                    . "')"
                );

            $all = $this->fetchWoRows($query, raw: true);

            if ($metric === 'completion') {

                $rows = $all;

            } else {

                $rows = $all->filter(
                    function ($row) {

                        $reportedAt =
                            $row['_reported_at'] ?? null;

                        $finishedAt =
                            $row['_finished_at'] ?? null;

                        if (!$reportedAt || !$finishedAt) {
                            return false;
                        }

                        $prio = strtoupper(
                            trim(
                                (string) ($row['priority_raw'] ?: 'NORMAL')
                            )
                        );

                        $sla =
                            self::SLA_HOURS[$prio]
                            ?? self::SLA_HOURS['NORMAL'];

                        return $finishedAt->lessThanOrEqualTo(
                            $reportedAt->copy()->addHours($sla)
                        );

                    }
                )->values();

            }

        } elseif ($metric === 'wo-longest') {

            /*
            | Work order belum selesai dengan waktu
            | berjalan paling lama.
            */

            $now = now();

            $rows =
                $this->filterOverdue(
                    (clone $baseQuery)
                        ->whereRaw(
                            "LOWER(TRIM(status)) NOT IN ('"
                            . implode("','", self::DONE_LIST)
                            . "')"
                        )
                        ->orderBy('tanggal_kerusakan')
                        ->limit(50)
                        ->get(),
                    $now
                )
                ->sortByDesc(
                    fn ($w) =>
                        optional($this->reportedAt($w))
                            ->diffInHours($now) ?? 0
                )
                ->values()
                ->take(50)
                ->map(fn ($w) => $this->formatWoRow($w, running: true));

        } elseif ($metric === 'delay-reasons') {

            return response()->json(
                $this->delayReasonsPayload(
                    $baseQuery,
                    'Rekap Alasan Pending Work Order',
                    $year
                )
            );

        } elseif (
            in_array($metric, [
                'top-machines',
                'top-areas',
                'top-categories',
                'top-departments',
            ], true)
        ) {

            $column = match ($metric) {

                'top-machines' => 'mesin',

                'top-areas' => 'area',

                'top-departments' => 'departemen',

                default => 'kategori',

            };

            $items =
                $this->ranking(
                    [
                        'start' => $start,
                        'end'   => $end,
                    ],
                    $column,
                    20
                );

            return response()->json([

                'title' => match ($metric) {

                    'top-machines' =>
                        'Top Mesin Bermasalah',

                    'top-areas' =>
                        'Top Area Kerusakan',

                    'top-departments' =>
                        'Top Department Pengaju',

                    default =>
                        'Top Kategori Kerusakan',

                },

                'subtitle' =>
                    $subtitle . ' - 20 teratas',

                'type' => 'ranking',

                'columns' => [
                    'label' => 'Nama',
                    'total' => 'Jumlah WO',
                    'percent' => 'Kontribusi',
                ],

                'rows' => $items,

                'total' =>
                    (int) $items->sum('total'),

            ]);

        } elseif (
            in_array($metric, [
                'downtime-by-mesin',
                'downtime-by-area',
            ], true)
        ) {

            $column = $metric === 'downtime-by-mesin'
                ? 'mesin'
                : 'area';

            $stats =
                $this->downtimeStats([
                    'start' => $start,
                    'end'   => $end,
                ]);

            $items = $column === 'mesin'
                ? $stats['byMesin']
                : $stats['byArea'];

            return response()->json([

                'title' =>
                    $metric === 'downtime-by-mesin'
                        ? 'Downtime per Mesin (Estimasi)'
                        : 'Downtime per Area (Estimasi)',

                'subtitle' =>
                    $subtitle
                    . ' - estimasi durasi kerusakan sampai selesai',

                'type' => 'ranking',

                'columns' => [
                    'label' => 'Nama',
                    'hours' => 'Total Jam',
                ],

                'rows' => $items,

                'total' =>
                    count($items),

            ]);

        } elseif ($metric === 'inventory-top-keluar') {

            $items =
                $this->topBarangKeluar(
                    ['start' => $start, 'end' => $end],
                    20
                );

            return response()->json([

                'title' =>
                    'Barang Paling Banyak Keluar',

                'subtitle' =>
                    $subtitle . ' - 20 teratas',

                'type' => 'ranking',

                'columns' => [
                    'label' => 'Barang',
                    'total' => 'Qty Keluar',
                ],

                'rows' => $items,

                'total' =>
                    (int) collect($items)->sum('total'),

            ]);

        } elseif ($metric === 'inventory-kritis') {

            $items = $this->barangKritis(30);

            return response()->json([

                'title' =>
                    'Barang Kritis (Stok Rendah / Habis)',

                'subtitle' =>
                    'Snapshot stok saat ini',

                'type' => 'table',

                'columns' => [
                    'kode' => 'Kode',
                    'label' => 'Barang',
                    'stok' => 'Stok',
                    'minimum' => 'Stok Minimum',
                    'kondisi' => 'Kondisi',
                ],

                'rows' => $items,

                'total' =>
                    count($items),

            ]);

        } elseif ($metric === 'inventory-stok') {

            $items = Barang::query()
                ->orderBy('stok')
                ->limit(200)
                ->get()
                ->map(fn ($b) => [
                    'kode' =>
                        $b->kode_barang ?: '-',
                    'label' =>
                        $b->nama_spesifikasi ?: '-',
                    'stok' =>
                        (int) $b->stok,
                    'minimum' =>
                        (int) $b->stok_minimum,
                    'kondisi' =>
                        $b->kondisi_stok,
                ]);

            return response()->json([

                'title' =>
                    'Daftar Stok Sparepart',

                'subtitle' =>
                    'Snapshot stok saat ini - diurutkan dari stok terkecil',

                'type' => 'table',

                'columns' => [
                    'kode' => 'Kode',
                    'label' => 'Barang',
                    'stok' => 'Stok',
                    'minimum' => 'Stok Minimum',
                    'kondisi' => 'Kondisi',
                ],

                'rows' => $items,

                'total' =>
                    $items->count(),

            ]);

        } elseif (
            in_array($metric, [
                'delay-by-area',
                'delay-by-mesin',
                'delay-by-department',
            ], true)
        ) {

            $column = match ($metric) {

                'delay-by-area' => 'area',

                'delay-by-mesin' => 'mesin',

                default => 'departemen',

            };

            $candidates =
                (clone $baseQuery)
                    ->whereRaw(
                        "LOWER(TRIM(status)) NOT IN ('"
                        . implode("','", self::DONE_LIST)
                        . "')"
                    )
                    ->get();

            $overdueRows =
                $this->filterOverdue($candidates, now());

            $grouped =
                $overdueRows
                    ->groupBy(
                        fn ($w) =>
                            trim((string) ($w->{$column} ?: 'LAINNYA'))
                    )
                    ->map(fn ($g, $label) => [
                        'label' =>
                            $label,
                        'total' =>
                            $g->count(),
                    ])
                    ->sortByDesc('total')
                    ->take(20)
                    ->values();

            return response()->json([

                'title' =>
                    'Delay per '
                    . match ($metric) {
                        'delay-by-area' => 'Area',
                        'delay-by-mesin' => 'Mesin',
                        default => 'Department',
                    },

                'subtitle' =>
                    $subtitle
                    . ' - WO melewati batas SLA prioritas',

                'type' => 'ranking',

                'columns' => [
                    'label' => 'Nama',
                    'total' => 'WO Terlambat',
                ],

                'rows' => $grouped,

                'total' =>
                    $overdueRows->count(),

            ]);

        } elseif (
            str_starts_with($metric, 'chart-')
        ) {

            /*
            | Detail grafik: seluruh WO pada window.
            */

            $rows = $this->fetchWoRows(clone $baseQuery);

        } else {

            return response()->json(
                [
                    'message' =>
                        'Metric tidak dikenali.',
                ],
                422
            );

        }


        /*
        |----------------------------------------------------------------------
        | NORMALISASI BARIS UNTUK JSON
        |----------------------------------------------------------------------
        */

        $rows = $rows->map(
            function ($row) {

                unset(
                    $row['_reported_at'],
                    $row['_finished_at'],
                    $row['priority_raw'],
                );

                return $row;

            }
        )->values();


        return response()->json(
            $this->woTablePayload(
                $rows,
                $this->detailTitle($metric),
                $subtitle
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | RESOLVE PERIODE (DAY / WEEK / MONTH / YEAR)
    |--------------------------------------------------------------------------
    */

    private function resolvePeriod(string $key): array
    {

        $now = now();

        switch ($key) {

            case 'day':

                $start = $now->copy()->startOfDay();
                $end   = $now->copy()->endOfDay();
                $prevStart = $start->copy()->subDay();
                $prevEnd   = $end->copy()->subDay();

                $labels = [];

                for ($h = 0; $h <= 23; $h++) {
                    $labels[] =
                        str_pad((string) $h, 2, '0', STR_PAD_LEFT)
                        . ':00';
                }

                $label =
                    'Hari Ini - '
                    . $now->format('d M Y');

                break;

            case 'week':

                $start = $now->copy()->startOfWeek();
                $end   = $now->copy()->endOfWeek();
                $prevStart = $start->copy()->subWeek();
                $prevEnd   = $end->copy()->subWeek();

                $names = [
                    'Sen', 'Sel', 'Rab',
                    'Kam', 'Jum', 'Sab', 'Min',
                ];

                $labels = [];

                for ($d = 0; $d < 7; $d++) {

                    $day = $start->copy()->addDays($d);

                    $labels[] =
                        $names[$d]
                        . ' '
                        . $day->format('d');

                }

                $label =
                    'Minggu Ini - '
                    . $start->format('d M')
                    . ' s/d '
                    . $end->format('d M Y');

                break;

            case 'year':

                $start = $now->copy()->startOfYear();
                $end   = $now->copy()->endOfYear();
                $prevStart = $start->copy()->subYear();
                $prevEnd   = $end->copy()->subYear();

                $labels = [
                    'Jan', 'Feb', 'Mar', 'Apr',
                    'Mei', 'Jun', 'Jul', 'Agu',
                    'Sep', 'Okt', 'Nov', 'Des',
                ];

                $label =
                    'Tahun ' . $now->year;

                break;

            default:

                $key = 'month';

                $start = $now->copy()->startOfMonth();
                $end   = $now->copy()->endOfMonth();
                $prevStart = $start->copy()->subMonth();
                $prevEnd   = $end->copy()->subMonth();

                $labels = [];

                for (
                    $d = 1;
                    $d <= $end->day;
                    $d++
                ) {
                    $labels[] = (string) $d;
                }

                $label =
                    'Bulan Ini - '
                    . $now->translatedFormat('F Y');

                break;

        }


        return [
            'key'       => $key,
            'label'     => $label,
            'start'     => $start,
            'end'       => $end,
            'prev'      => [
                'start' => $prevStart,
                'end'   => $prevEnd,
            ],
            'labels'    => $labels,
        ];

    }


    /*
    |--------------------------------------------------------------------------
    | QUERY HELPER
    |--------------------------------------------------------------------------
    */

    private function woCreatedQuery(array $window)
    {
        return WorkOrder::query()
            ->whereBetween('tanggal_kerusakan', [
                $window['start'],
                $window['end'],
            ]);
    }

    private function woFinishedQuery(array $window)
    {
        return WorkOrder::query()
            ->whereIn(
                \DB::raw('LOWER(TRIM(COALESCE(NULLIF(status, ""), "-")))'),
                self::DONE_LIST
            )
            ->whereNotNull('tanggal_selesai_perbaikan')
            ->whereBetween('tanggal_selesai_perbaikan', [
                $window['start'],
                $window['end'],
            ]);
    }


    private function countStatus($models, array $needles): int
    {
        return $models->filter(
            fn ($w) =>
                in_array(
                    strtolower(trim((string) $w->status)),
                    $needles,
                    true
                )
        )->count();
    }

    /*
    |--------------------------------------------------------------------------
    | PERBANDINGAN PERIODE SEBELUMNYA (PERSEN)
    |--------------------------------------------------------------------------
    */

    private function deltaPercent($previous, $current): ?int
    {

        if (
            !is_numeric($previous)
            || !is_numeric($current)
        ) {
            return null;
        }

        $previous = (float) $previous;

        $current = (float) $current;

        if ($previous <= 0) {

            return $current > 0
                ? 100
                : 0;

        }

        return (int) round(
            ($current - $previous) / $previous * 100
        );

    }

    private function countPriority($models, array $needles): int
    {
        return $models->filter(
            fn ($w) =>
                in_array(
                    strtolower(trim((string) ($w->priority ?: 'normal'))),
                    $needles,
                    true
                )
        )->count();
    }

    private function priorityCounts($models): array
    {
        return [
            'emergency' =>
                $this->countPriority($models, ['emergency']),
            'urgent' =>
                $this->countPriority($models, ['urgent']),
            'normal' =>
                $this->countPriority($models, ['normal']),
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | TANGGAL & WAKTU
    |--------------------------------------------------------------------------
    */

    private function reportedAt($w): ?Carbon
    {
        try {

            if (empty($w->tanggal_kerusakan)) {
                return null;
            }

            $at = Carbon::parse($w->tanggal_kerusakan);

            if (!empty($w->jam_kerusakan)) {
                $at->setTimeFromTimeString($w->jam_kerusakan);
            } else {
                $at->startOfDay();
            }

            return $at;

        } catch (\Throwable $e) {
            return null;
        }
    }

    private function finishedAt($w): ?Carbon
    {
        try {

            if (empty($w->tanggal_selesai_perbaikan)) {
                return null;
            }

            $at = Carbon::parse($w->tanggal_selesai_perbaikan);

            if (!empty($w->jam_selesai_perbaikan)) {
                $at->setTimeFromTimeString($w->jam_selesai_perbaikan);
            } else {
                $at->endOfDay();
            }

            return $at;

        } catch (\Throwable $e) {
            return null;
        }
    }

    private function startedRepairAt($w): ?Carbon
    {
        try {

            if (empty($w->tanggal_mulai_perbaikan)) {
                return null;
            }

            $at = Carbon::parse($w->tanggal_mulai_perbaikan);

            if (!empty($w->jam_mulai_perbaikan)) {
                $at->setTimeFromTimeString($w->jam_mulai_perbaikan);
            } else {
                $at->startOfDay();
            }

            return $at;

        } catch (\Throwable $e) {
            return null;
        }
    }

    private function slaHoursFor($w): int
    {
        $prio = strtoupper(
            trim((string) ($w->priority ?: 'NORMAL'))
        );

        return self::SLA_HOURS[$prio]
            ?? self::SLA_HOURS['NORMAL'];
    }

    private function filterOverdue($models, Carbon $now)
    {
        return $models->filter(
            function ($w) use ($now) {

                $reportedAt =
                    $this->reportedAt($w);

                if (!$reportedAt) {
                    return false;
                }

                return $now->greaterThan(
                    $reportedAt->copy()->addHours(
                        $this->slaHoursFor($w)
                    )
                );

            }
        )->values();
    }

    private function longestOpenRow($models, Carbon $now)
    {

        $longest = null;

        $maxHours = 0;

        foreach ($models as $w) {

            $reportedAt =
                $this->reportedAt($w);

            if (!$reportedAt) {
                continue;
            }

            $hours =
                $reportedAt->diffInMinutes($now) / 60;

            if ($hours > $maxHours) {

                $maxHours = $hours;

                $longest = [
                    'no_wo' =>
                        $w->no_wo ?: ('WO-' . $w->id),
                    'job' =>
                        $w->job ?: '-',
                    'mesin' =>
                        $w->mesin ?: '-',
                    'area' =>
                        $w->area ?: '-',
                    'departemen' =>
                        $w->departemen ?: '-',
                    'priority' =>
                        strtoupper(trim((string) ($w->priority ?: '-'))),
                    'status' =>
                        strtoupper(trim((string) ($w->status ?: '-'))),
                    'tanggal' =>
                        optional($reportedAt)->format('d M Y H:i'),
                    'hours' =>
                        round($hours, 1),
                ];

            }

        }

        return $longest;

    }

    private function timeStats($finishedModels): array
    {

        $completionHours = [];

        $workHours = [];

        foreach ($finishedModels as $w) {

            $reportedAt = $this->reportedAt($w);

            $finishedAt = $this->finishedAt($w);

            if ($reportedAt && $finishedAt) {

                $hours =
                    $reportedAt->diffInMinutes($finishedAt) / 60;

                if ($hours >= 0 && $hours < 24 * 90) {
                    $completionHours[] = $hours;
                }

            }

            $startedAt = $this->startedRepairAt($w);

            if ($startedAt && $finishedAt) {

                $hours =
                    $startedAt->diffInMinutes($finishedAt) / 60;

                if ($hours >= 0 && $hours < 24 * 90) {
                    $workHours[] = $hours;
                }

            }

        }

        return [
            'avgCompletionHours' =>
                count($completionHours) > 0
                    ? round(array_sum($completionHours) / count($completionHours), 1)
                    : null,
            'avgWorkHours' =>
                count($workHours) > 0
                    ? round(array_sum($workHours) / count($workHours), 1)
                    : null,
        ];

    }

    private function responseStats($models): array
    {

        $ids = $models->pluck('id');

        if ($ids->isEmpty()) {
            return ['avgResponseHours' => null];
        }

        $firstResponses =
            WorkOrderStatusHistory::query()
                ->whereIn('work_order_id', $ids)
                ->whereRaw("LOWER(TRIM(status)) <> 'open'")
                ->orderBy('started_at')
                ->get(['work_order_id', 'started_at'])
                ->groupBy('work_order_id');

        $hours = [];

        foreach ($models as $w) {

            $histories =
                $firstResponses->get($w->id);

            if (!$histories || $histories->isEmpty()) {
                continue;
            }

            $first =
                optional($histories->first())->started_at;

            if (!$first) {
                continue;
            }

            $reportedAt =
                $this->reportedAt($w);

            if (!$reportedAt) {
                continue;
            }

            try {

                $respondedAt =
                    Carbon::parse($first);

                $h =
                    $reportedAt->diffInMinutes($respondedAt) / 60;

                if ($h >= 0 && $h < 24 * 30) {
                    $hours[] = $h;
                }

            } catch (\Throwable $e) {
                continue;
            }

        }

        return [
            'avgResponseHours' =>
                count($hours) > 0
                    ? round(array_sum($hours) / count($hours), 1)
                    : null,
        ];

    }


    /*
    |--------------------------------------------------------------------------
    | TREND
    |--------------------------------------------------------------------------
    */

    private function bucketIndex(Carbon $date, array $window): ?int
    {

        $key = $window['key'];

        if ($key === 'day') {

            return $date->hour;

        }

        if ($key === 'week' || $key === 'month') {

            $index =
                $window['start']->startOfDay()
                    ->diffInDays($date->copy()->startOfDay());

            $count = count($window['labels']);

            if ($index < 0 || $index >= $count) {
                return null;
            }

            return (int) $index;

        }

        return $date->month - 1;

    }

    private function trendByDate(array $window, string $column): array
    {

        $values = array_fill(
            0,
            count($window['labels']),
            0
        );

        $rows = WorkOrder::query()
            ->whereBetween($column, [
                $window['start'],
                $window['end'],
            ])
            ->get([$column]);

        foreach ($rows as $row) {

            if (empty($row->{$column})) {
                continue;
            }

            try {

                $date = Carbon::parse($row->{$column});

            } catch (\Throwable $e) {
                continue;
            }

            $index =
                $this->bucketIndex($date, $window);

            if ($index !== null) {
                $values[$index]++;
            }

        }

        return [
            'labels' => $window['labels'],
            'values' => $values,
        ];

    }

    private function statusGroupOf(?string $status): string
    {

        $s = strtolower(trim((string) $status));

        if (in_array($s, self::DONE_LIST, true)) {
            return 'selesai';
        }

        if (in_array($s, self::IN_PROGRESS_LIST, true)) {
            return 'progress';
        }

        if ($s === 'pending' || $s === 'hold') {
            return 'hold';
        }

        return 'open';

    }

    private function statusTrend(array $window): array
    {

        $labels = $window['labels'];

        $series = [
            'open'     => array_fill(0, count($labels), 0),
            'progress' => array_fill(0, count($labels), 0),
            'hold'     => array_fill(0, count($labels), 0),
            'selesai'  => array_fill(0, count($labels), 0),
        ];


        $rows =
            $this->woCreatedQuery($window)
                ->get([
                    'tanggal_kerusakan',
                    'status',
                ]);


        foreach ($rows as $w) {

            if (empty($w->tanggal_kerusakan)) {
                continue;
            }

            try {

                $date =
                    Carbon::parse($w->tanggal_kerusakan);

            } catch (\Throwable $e) {
                continue;
            }

            $index =
                $this->bucketIndex($date, $window);

            if ($index === null) {
                continue;
            }

            $group =
                $this->statusGroupOf($w->status);

            $series[$group][$index]++;

        }


        return [
            'labels' => $labels,
            'series' => $series,
        ];

    }

    private function priorityTrend(array $window): array
    {

        $labels = $window['labels'];

        $series = [
            'EMERGENCY' => array_fill(0, count($labels), 0),
            'URGENT'    => array_fill(0, count($labels), 0),
            'NORMAL'    => array_fill(0, count($labels), 0),
        ];


        $rows =
            $this->woCreatedQuery($window)
                ->get([
                    'tanggal_kerusakan',
                    'priority',
                ]);


        foreach ($rows as $w) {

            if (empty($w->tanggal_kerusakan)) {
                continue;
            }

            try {

                $date =
                    Carbon::parse($w->tanggal_kerusakan);

            } catch (\Throwable $e) {
                continue;
            }

            $index =
                $this->bucketIndex($date, $window);

            if ($index === null) {
                continue;
            }

            $prio = strtoupper(
                trim((string) ($w->priority ?: 'NORMAL'))
            );

            if (!isset($series[$prio])) {
                $prio = 'NORMAL';
            }

            $series[$prio][$index]++;

        }


        return [
            'labels' => $labels,
            'series' => $series,
        ];

    }

    private function inventoryTrends(array $window): array
    {


        $masukSeries =
            array_fill(0, count($window['labels']), 0);

        $keluarSeries =
            array_fill(0, count($window['labels']), 0);


        $masukRows =
            BarangMasuk::query()
                ->whereBetween('tanggal_masuk', [
                    $window['start'],
                    $window['end'],
                ])
                ->get(['tanggal_masuk', 'qty']);

        $keluarRows =
            BarangKeluar::query()
                ->whereBetween('tanggal_keluar', [
                    $window['start'],
                    $window['end'],
                ])
                ->get(['tanggal_keluar', 'qty']);

        $masukTotal = 0;

        foreach ($masukRows as $row) {

            if (empty($row->tanggal_masuk)) {
                continue;
            }

            try {

                $date =
                    Carbon::parse($row->tanggal_masuk);

            } catch (\Throwable $e) {
                continue;
            }

            $index =
                $this->bucketIndex($date, $window);

            $qty = (float) $row->qty;

            $masukTotal += $qty;

            if ($index !== null) {
                $masukSeries[$index] += $qty;
            }

        }

        $keluarTotal = 0;

        foreach ($keluarRows as $row) {

            if (empty($row->tanggal_keluar)) {
                continue;
            }

            try {

                $date =
                    Carbon::parse($row->tanggal_keluar);

            } catch (\Throwable $e) {
                continue;
            }

            $index =
                $this->bucketIndex($date, $window);

            $qty = (float) $row->qty;

            $keluarTotal += $qty;

            if ($index !== null) {
                $keluarSeries[$index] += $qty;
            }

        }


        $round = fn (array $a) =>
            array_map(
                fn ($v) => round((float) $v, 2),
                $a
            );


        return [
            'masukSeries' =>
                $round($masukSeries),
            'keluarSeries' =>
                $round($keluarSeries),
            'masukTotal' =>
                round($masukTotal, 2),
            'keluarTotal' =>
                round($keluarTotal, 2),
        ];

    }


    /*
    |--------------------------------------------------------------------------
    | RANKING
    |--------------------------------------------------------------------------
    */

    private function ranking(array $window, string $column, int $limit)
    {

        $items = WorkOrder::query()
            ->whereBetween('tanggal_kerusakan', [
                $window['start'],
                $window['end'],
            ])
            ->whereNotNull($column)
            ->where($column, '<>', '')
            ->selectRaw(
                "{$column} as label, COUNT(*) as total"
            )
            ->groupBy($column)
            ->orderByDesc('total')
            ->limit($limit)
            ->get();

        $grandTotal =
            (int) $items->sum('total');

        return $items->map(function ($item) use ($grandTotal) {

            $item->total =
                (int) $item->total;

            $item->percent =
                $grandTotal > 0
                    ? round($item->total / $grandTotal * 100, 1)
                    : 0;

            return $item;

        });

    }

    private function delayRanking($overdueRows, string $column): array
    {

        return $overdueRows
            ->groupBy(
                fn ($w) =>
                    trim((string) ($w->{$column} ?: 'LAINNYA'))
            )
            ->map(fn ($g, $label) => [
                'label' =>
                    $label,
                'total' =>
                    $g->count(),
            ])
            ->sortByDesc('total')
            ->take(8)
            ->values()
            ->toArray();

    }


    /*
    |--------------------------------------------------------------------------
    | DOWNTIME (ESTIMASI DARI DURASI WO)
    |--------------------------------------------------------------------------
    */

    private function downtimeStats(array $window): array
    {

        $rows =
            WorkOrder::query()
                ->whereBetween('tanggal_kerusakan', [
                    $window['start'],
                    $window['end'],
                ])
                ->get();

        $totalMinutes = 0;

        $counted = 0;

        $byMesin = [];

        $byArea = [];


        foreach ($rows as $w) {

            $reportedAt =
                $this->reportedAt($w);

            $finishedAt =
                $this->finishedAt($w);

            if (!$reportedAt || !$finishedAt) {
                continue;
            }

            $minutes =
                $reportedAt->diffInMinutes($finishedAt);

            if ($minutes <= 0 || $minutes > 60 * 24 * 180) {
                continue;
            }

            $totalMinutes += $minutes;

            $counted++;

            $mesinKey =
                trim((string) ($w->mesin ?: 'TANPA MESIN'));

            $areaKey =
                trim((string) ($w->area ?: 'TANPA AREA'));

            $byMesin[$mesinKey] =
                ($byMesin[$mesinKey] ?? 0) + $minutes;

            $byArea[$areaKey] =
                ($byArea[$areaKey] ?? 0) + $minutes;

        }

        $toRanking =
            fn (array $map) =>
                collect($map)
                    ->map(fn ($minutes, $label) => [
                        'label' =>
                            $label,
                        'hours' =>
                            round($minutes / 60, 1),
                    ])
                    ->sortByDesc('hours')
                    ->take(10)
                    ->values()
                    ->toArray();


        return [
            'totalHours' =>
                round($totalMinutes / 60, 1),
            'avgHours' =>
                $counted > 0
                    ? round($totalMinutes / 60 / $counted, 1)
                    : null,
            'byMesin' =>
                $toRanking($byMesin),
            'byArea' =>
                $toRanking($byArea),
        ];

    }


    /*
    |--------------------------------------------------------------------------
    | INVENTORY
    |--------------------------------------------------------------------------
    */

    private function stokSnapshot(): array
    {

        $total =
            (int) Barang::count();

        $habis =
            (int) Barang::where('stok', '<=', 0)->count();

        $menipis =
            (int) Barang::where('stok', '>', 0)
                ->whereColumn('stok', '<=', 'stok_minimum')
                ->count();

        $rataRata =
            round(
                (float) Barang::avg('stok'),
                1
            );

        return [
            'total'   => $total,
            'aman'    => max(0, $total - $habis - $menipis),
            'menipis' => $menipis,
            'habis'   => $habis,
            'rataRata' => $rataRata,
        ];

    }

    private function topBarangKeluar(array $window, int $limit): array
    {

        return BarangKeluar::query()
            ->whereBetween('tanggal_keluar', [
                $window['start'],
                $window['end'],
            ])
            ->selectRaw(
                'barang_id, SUM(qty) as total'
            )
            ->groupBy('barang_id')
            ->orderByDesc('total')
            ->limit($limit)
            ->get()
            ->map(function ($row) {

                $nama =
                    \DB::table('barangs')
                        ->where('id', $row->barang_id)
                        ->value('nama_spesifikasi');

                return [
                    'label' =>
                        $nama ?: ('Barang #' . $row->barang_id),
                    'total' =>
                        round((float) $row->total, 2),
                ];

            })
            ->toArray();

    }

    private function barangKritis(int $limit): array
    {

        return Barang::query()
            ->orderByRaw('CASE WHEN stok <= 0 THEN 0 WHEN stok <= stok_minimum THEN 1 ELSE 2 END')
            ->orderByRaw('CASE WHEN stok_minimum > 0 THEN stok / stok_minimum ELSE stok END')
            ->orderBy('stok')
            ->limit($limit)
            ->get()
            ->filter(
                fn ($b) =>
                    $b->stok <= 0
                    || $b->stok <= max(1, (int) $b->stok_minimum)
            )
            ->values()
            ->map(fn ($b) => [
                'kode' =>
                    $b->kode_barang ?: '-',
                'label' =>
                    $b->nama_spesifikasi ?: '-',
                'stok' =>
                    (int) $b->stok,
                'minimum' =>
                    (int) $b->stok_minimum,
                'kondisi' =>
                    $b->kondisi_stok,
            ])
            ->toArray();

    }


    /*
    |--------------------------------------------------------------------------
    | ALERTS
    |--------------------------------------------------------------------------
    */

    private function buildAlerts(
        array $priorityCounts,
        array $prevPriority,
        int $overdueCount,
        int $belumSelesai,
        int $woSelesai,
        array $stok
    ): array {

        $alerts = [];


        if (
            $priorityCounts['emergency']
            > $prevPriority['emergency']
        ) {

            $alerts[] = [
                'level' => 'danger',
                'text'  =>
                    'WO Emergency meningkat menjadi '
                    . $priorityCounts['emergency']
                    . ' (periode sebelumnya: '
                    . $prevPriority['emergency'] . ').',
            ];

        }


        if (
            $priorityCounts['urgent']
            > $prevPriority['urgent']
        ) {

            $alerts[] = [
                'level' => 'warning',
                'text'  =>
                    'WO Urgent meningkat menjadi '
                    . $priorityCounts['urgent']
                    . ' (periode sebelumnya: '
                    . $prevPriority['urgent'] . ').',
            ];

        }


        if ($overdueCount > 0) {

            $alerts[] = [
                'level' => 'danger',
                'text'  =>
                    $overdueCount
                    . ' WO melewati batas SLA dan belum selesai.',
            ];

        }


        if (
            $stok['habis'] > 0
            || $stok['menipis'] > 0
        ) {

            $alerts[] = [
                'level' =>
                    $stok['habis'] > 0 ? 'danger' : 'warning',
                'text'  =>
                    'Inventory: '
                    . $stok['habis'] . ' barang habis, '
                    . $stok['menipis'] . ' barang menipis.',
            ];

        }


        if (
            $woSelesai < $belumSelesai
            && ($woSelesai + $belumSelesai) > 0
        ) {

            $alerts[] = [
                'level' => 'warning',
                'text'  =>
                    'Backlog WO menumpuk: '
                    . $belumSelesai
                    . ' belum selesai vs '
                    . $woSelesai
                    . ' selesai pada periode ini.',
            ];

        }


        if (empty($alerts)) {

            $alerts[] = [
                'level' => 'info',
                'text'  =>
                    'Semua kondisi monitoring dalam batas aman.',
            ];

        }

        return $alerts;

    }


    /*
    |--------------------------------------------------------------------------
    | FORMAT BARIS WO
    |--------------------------------------------------------------------------
    */

    private function formatWoRow($w, bool $running = false): array
    {

        $reportedAt =
            $this->reportedAt($w);

        $finishedAt =
            $this->finishedAt($w);

        $selesai =
            $finishedAt
                ? $finishedAt->format('d M Y H:i')
                : '-';

        $durasi = '-';

        if ($running && $reportedAt) {

            $durasi =
                round($reportedAt->diffInMinutes(now()) / 60, 1)
                . ' jam (berjalan)';

        } elseif ($reportedAt && $finishedAt) {

            $durasi =
                round(
                    $reportedAt->diffInMinutes($finishedAt) / 60,
                    1
                )
                . ' jam';

        }


        return [

            '_reported_at' =>
                $reportedAt,

            '_finished_at' =>
                $finishedAt,

            'priority_raw' =>
                $w->priority,

            'no_wo' =>
                $w->no_wo ?: ('WO-' . $w->id),

            'tanggal_kerusakan' =>
                $reportedAt
                    ? $reportedAt->format('d M Y H:i')
                    : '-',

            'job' =>
                $w->job ?: '-',

            'mesin' =>
                $w->mesin ?: '-',

            'area' =>
                $w->area ?: '-',

            'departemen' =>
                $w->departemen ?: '-',

            'dibuat_oleh' =>
                $w->dibuat_oleh ?: '-',

            'priority' =>
                strtoupper(
                    trim((string) ($w->priority ?: '-'))
                ),

            'status' =>
                strtoupper(
                    trim((string) ($w->status ?: '-'))
                ),

            'tanggal_selesai_perbaikan' =>
                $selesai,

            'durasi' =>
                $durasi,

        ];

    }


    /*
    |--------------------------------------------------------------------------
    | AMBIL BARIS WO TERFORMAT
    |--------------------------------------------------------------------------
    */

    private function fetchWoRows(
        $query,
        bool $raw = false
    ) {

        $models = $query
            ->orderByDesc('tanggal_kerusakan')
            ->limit(5000)
            ->get();


        $rows =
            $models->map(
                fn ($w) =>
                    $this->formatWoRow($w)
            );


        return $raw
            ? $rows
            : $rows->map(function ($row) {

                unset(
                    $row['_reported_at'],
                    $row['_finished_at'],
                    $row['priority_raw'],
                );

                return $row;

            });

    }

    private function woTablePayload($rows, string $title, string $subtitle): array
    {

        return [

            'title' =>
                $title,

            'subtitle' =>
                $subtitle,

            'type' => 'table',

            'columns' => [

                'no_wo' => 'No. WO',

                'tanggal_kerusakan' => 'Tgl Kerusakan',

                'job' => 'Pekerjaan',

                'mesin' => 'Mesin',

                'area' => 'Area',

                'departemen' => 'Departemen',

                'dibuat_oleh' => 'Pelapor',

                'priority' => 'Prioritas',

                'status' => 'Status',

                'tanggal_selesai_perbaikan' => 'Tgl Selesai',

                'durasi' => 'Durasi',

            ],

            'rows' =>
                $rows instanceof \Illuminate\Support\Collection
                    ? $rows->take(200)->values()
                    : collect($rows)->take(200)->values(),

            'total' =>
                $rows instanceof \Illuminate\Support\Collection
                    ? $rows->count()
                    : count($rows),

        ];

    }

    private function detailTitle(string $metric): string
    {

        $titleMap = [

            'total' =>
                'Total Work Order',

            'in-progress' =>
                'Work Order In Progress',

            'done' =>
                'Work Order Done / Selesai',

            'emergency' =>
                'Prioritas Emergency',

            'urgent' =>
                'Prioritas Urgent',

            'normal' =>
                'Prioritas Normal',

            'open' =>
                'Status OPEN',

            'pending' =>
                'Status PENDING / HOLD',

            'hold' =>
                'Status PENDING / HOLD',

            'close' =>
                'Status CLOSE',

            'overdue' =>
                'Work Order Overdue (Melewati SLA)',

            'completion' =>
                'Work Order CLOSE (Completion Rate)',

            'on-time' =>
                'Close On-Time (Dalam SLA Prioritas)',

            'chart-wo-monthly' =>
                'Detail Work Order pada Periode',

            'chart-status' =>
                'Detail Status Work Order',

            'chart-priority' =>
                'Detail Prioritas Work Order',

            'chart-department' =>
                'Detail Department Pengaju',

            'chart-kategori' =>
                'Detail Kategori Kerusakan',

            'chart-trend' =>
                'Detail Trend Work Order',

        ];

        return $titleMap[$metric] ?? ucfirst($metric);

    }


    /*
    |--------------------------------------------------------------------------
    | PAYLOAD ALASAN PENDING
    |--------------------------------------------------------------------------
    */

    private function delayReasonsPayload(
        $baseQuery,
        string $title,
        int $year
    ) {

        $totals = [];

        $woIds = (clone $baseQuery)->pluck('id');

        if ($woIds->isNotEmpty()) {

            $histories =
                WorkOrderStatusHistory::query()
                    ->whereIn('work_order_id', $woIds)
                    ->whereRaw("LOWER(TRIM(status)) = 'pending'")
                    ->get([
                        'work_order_id',
                        'alasan',
                        'started_at',
                        'ended_at',
                    ]);

            foreach ($histories as $history) {

                if (empty($history->started_at)) {
                    continue;
                }

                try {

                    $start = Carbon::parse($history->started_at);

                    $end = !empty($history->ended_at)
                        ? Carbon::parse($history->ended_at)
                        : now();

                    $minutes = $start->diffInMinutes($end);

                    if ($minutes <= 0) {
                        continue;
                    }

                    $reasonKey = strtoupper(
                        trim(
                            (string) ($history->alasan ?: 'LAINNYA')
                        )
                    );

                    $totals[$reasonKey] =
                        ($totals[$reasonKey] ?? 0) + $minutes;

                } catch (\Throwable $e) {
                    continue;
                }

            }

            arsort($totals);

        }


        return [

            'title' =>
                $title,

            'subtitle' =>
                "Tahun {$year} - total menit pending per alasan",

            'type' => 'ranking',

            'columns' => [
                'label' => 'Alasan Pending',
                'total' => 'Total Menit',
            ],

            'rows' => collect($totals)
                ->map(
                    fn ($minutes, $reason) => [
                        'label' => $reason,
                        'total' => (int) round($minutes),
                    ]
                )
                ->values(),

            'total' =>
                count($totals),

        ];

    }
}
