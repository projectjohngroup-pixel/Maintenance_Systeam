<?php

namespace App\Http\Controllers\AiAssistant;

use App\Http\Controllers\Controller;
use App\Models\Inventory\Barang;
use App\Models\Inventory\BarangKeluar;
use App\Models\Inventory\BarangMasuk;
use App\Models\Machine\Area;
use App\Models\Machine\Machine;
use App\Models\Machine\MachineSparepart;
use App\Models\PurchaseRequest\PurchaseRequest;
use App\Models\User;
use App\Models\WorkOrder\WorkOrder;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class AiAssistantController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | ASK
    |--------------------------------------------------------------------------
    */

    public function ask(Request $request)
    {
        $validated = $request->validate([
            'question' => [
                'required',
                'string',
                'max:2000',
            ],
        ]);

        $question = trim(
            $validated['question']
        );

        try {
            $answer = $this->processQuestion(
                $question
            );

            return response()->json([
                'success' => true,
                'answer' => $answer,
            ]);
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'success' => false,
                'answer' =>
                    'Maaf, terjadi kesalahan saat membaca data Pachira.',
            ], 500);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | ROUTER UTAMA
    |--------------------------------------------------------------------------
    */

    private function processQuestion(
        string $question
    ): string {
        $q = $this->normalize(
            $question
        );

        /*
        |--------------------------------------------------------------------------
        | SAPAAN
        |--------------------------------------------------------------------------
        */

        if (
            $this->isGreeting($q)
        ) {
            return
                "Ada yang bisa saya bantu?\n\n"
                . "Silakan tanyakan data Maintenance & Inventory Pachira.";
        }

        /*
        |--------------------------------------------------------------------------
        | WO HARI INI
        |--------------------------------------------------------------------------
        */

        if (
            $this->containsAny(
                $q,
                [
                    'wo hari ini',
                    'work order hari ini',
                    'workorder hari ini',
                    'ada wo hari ini',
                    'berapa wo hari ini',
                    'jumlah wo hari ini',
                    'wo tanggal hari ini',
                    'total wo hari ini',
                ]
            )
        ) {
            return $this->answerWorkOrderToday();
        }

        /*
        |--------------------------------------------------------------------------
        | STATUS WO
        |--------------------------------------------------------------------------
        */

        $status = $this->detectWorkOrderStatus(
            $q
        );

        if ($status !== null) {
            return $this->answerWorkOrderByStatus(
                $status,
                $question
            );
        }

        /*
        |--------------------------------------------------------------------------
        | PRIORITAS WO
        |--------------------------------------------------------------------------
        */

        $priority = $this->detectPriority(
            $q
        );

        if ($priority !== null) {
            return $this->answerWorkOrderByPriority(
                $priority,
                $question
            );
        }

        /*
        |--------------------------------------------------------------------------
        | BARANG PALING BANYAK KELUAR
        |--------------------------------------------------------------------------
        */

        if (
            $this->containsAny(
                $q,
                [
                    'barang paling banyak keluar',
                    'barang terbanyak keluar',
                    'paling banyak keluar',
                    'terbanyak dipakai',
                    'barang paling banyak dipakai',
                    'pemakaian terbanyak',
                    'barang terbanyak dipakai',
                ]
            )
        ) {
            return $this->answerTopBarangKeluar();
        }

        /*
        |--------------------------------------------------------------------------
        | RATA-RATA PEMAKAIAN
        |--------------------------------------------------------------------------
        */

        if (
            $this->containsAny(
                $q,
                [
                    'rata rata pemakaian',
                    'rata-rata pemakaian',
                    'rata pemakaian',
                    'pemakaian rata rata',
                    'pemakaian bulan ini',
                    'rata rata keluar',
                    'rata-rata keluar',
                    'rata keluar',
                ]
            )
        ) {
            return $this->answerAverageUsage();
        }

        /*
        |--------------------------------------------------------------------------
        | BARANG MASUK
        |--------------------------------------------------------------------------
        */

        if (
            $this->containsAny(
                $q,
                [
                    'barang masuk',
                    'barang yang masuk',
                    'penerimaan barang',
                    'barang datang',
                    'berapa barang masuk',
                ]
            )
        ) {
            return $this->answerBarangMasuk();
        }

        /*
        |--------------------------------------------------------------------------
        | BARANG KELUAR
        |--------------------------------------------------------------------------
        */

        if (
            $this->containsAny(
                $q,
                [
                    'barang keluar',
                    'barang yang keluar',
                    'pemakaian barang',
                    'berapa barang keluar',
                    'barang digunakan',
                ]
            )
        ) {
            return $this->answerBarangKeluar();
        }

        /*
        |--------------------------------------------------------------------------
        | RESTOCK / MENIPIS
        |--------------------------------------------------------------------------
        */

        if (
            $this->containsAny(
                $q,
                [
                    'restock',
                    're-stock',
                    'stok menipis',
                    'barang menipis',
                    'stok hampir habis',
                    'stok minimum',
                    'barang hampir habis',
                    'menipis dong',
                ]
            )
        ) {
            return $this->answerRestock();
        }

        /*
        |--------------------------------------------------------------------------
        | BARANG HABIS
        |--------------------------------------------------------------------------
        */

        if (
            $this->containsAny(
                $q,
                [
                    'stok habis',
                    'barang habis',
                    'yang habis',
                    'habis dong',
                    'stok kosong',
                    'barang kosong',
                ]
            )
        ) {
            return $this->answerBarangHabis();
        }

        /*
        |--------------------------------------------------------------------------
        | TOTAL KW / DAYA
        |--------------------------------------------------------------------------
        */

        if (
            $this->containsAny(
                $q,
                [
                    'total kw',
                    'jumlah kw',
                    'berapa kw',
                    'total daya',
                    'daya mesin',
                ]
            )
        ) {
            return $this->answerTotalKw(
                $question
            );
        }

        /*
        |--------------------------------------------------------------------------
        | STOK / DAFTAR BARANG
        |--------------------------------------------------------------------------
        */

        if (
            $this->containsAny(
                $q,
                [
                    'stok',
                    'sisa stok',
                    'persediaan',
                    'inventory',
                    'stok barang',
                    'barang apa saja',
                    'nama barang',
                    'daftar barang',
                    'barang apa',
                    'apa saja barang',
                    'stok semua',
                ]
            )
        ) {
            return $this->answerStockList(
                $question
            );
        }

        /*
        |--------------------------------------------------------------------------
        | SPAREPART
        |--------------------------------------------------------------------------
        */

        if (
            $this->containsAny(
                $q,
                [
                    'sparepart',
                    'spare part',
                    'spare-part',
                    'part mesin',
                    'sparepart mesin',
                ]
            )
        ) {
            return $this->answerSparepart(
                $question
            );
        }

        /*
        |--------------------------------------------------------------------------
        | MESIN
        |--------------------------------------------------------------------------
        */

        if (
            $this->containsAny(
                $q,
                [
                    'mesin',
                    'machine',
                    'mesin apa saja',
                    'daftar mesin',
                    'nama mesin',
                    'data mesin',
                ]
            )
        ) {
            return $this->answerMachine(
                $question
            );
        }

        /*
        |--------------------------------------------------------------------------
        | AREA
        |--------------------------------------------------------------------------
        */

        if (
            $this->containsAny(
                $q,
                [
                    'area',
                    'lokasi',
                    'area apa saja',
                    'daftar area',
                    'nama area',
                ]
            )
        ) {
            return $this->answerArea(
                $question
            );
        }

        /*
        |--------------------------------------------------------------------------
        | PURCHASE REQUEST
        |--------------------------------------------------------------------------
        */

        if (
            $this->containsAny(
                $q,
                [
                    'purchase request',
                    'purchase',
                    'permintaan pembelian',
                    'permintaan barang',
                    'pr bulan ini',
                ]
            )
        ) {
            return $this->answerPurchaseRequest();
        }

        /*
        |--------------------------------------------------------------------------
        | DASHBOARD
        |--------------------------------------------------------------------------
        */

        if (
            $this->containsAny(
                $q,
                [
                    'dashboard',
                    'kpi',
                    'ringkasan',
                    'summary',
                    'rekap',
                ]
            )
        ) {
            return $this->answerDashboard();
        }

        /*
        |--------------------------------------------------------------------------
        | USER
        |--------------------------------------------------------------------------
        */

        if (
            $this->containsAny(
                $q,
                [
                    'user',
                    'pengguna',
                    'daftar user',
                    'jumlah user',
                ]
            )
        ) {
            return $this->answerUsers();
        }

        /*
        |--------------------------------------------------------------------------
        | NOMOR / DETAIL WO
        |--------------------------------------------------------------------------
        */

        if (
            $this->looksLikeWorkOrderQuestion(
                $q
            )
        ) {
            return $this->answerWorkOrder(
                $question
            );
        }

        /*
        |--------------------------------------------------------------------------
        | GLOBAL SEARCH
        |--------------------------------------------------------------------------
        */

        return $this->globalSearch(
            $question
        );
    }

    /*
    |--------------------------------------------------------------------------
    | NORMALIZE
    |--------------------------------------------------------------------------
    */

    private function normalize(
        string $text
    ): string {
        $text = Str::lower(
            trim($text)
        );

        $text = preg_replace(
            '/[^\pL\pN\s\-\/\.\'"]+/u',
            ' ',
            $text
        );

        $text = preg_replace(
            '/\s+/',
            ' ',
            $text
        );

        return trim(
            $text
        );
    }

    /*
    |--------------------------------------------------------------------------
    | GREETING
    |--------------------------------------------------------------------------
    */

    private function isGreeting(
        string $q
    ): bool {
        return $this->containsAny(
            $q,
            [
                'hallo',
                'halo',
                'hai',
                'hi',
                'hello',
                'pagi',
                'siang',
                'sore',
                'malam',
                'test',
                'tes',
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | STRING HELPER
    |--------------------------------------------------------------------------
    */

    private function containsAny(
        string $text,
        array $needles
    ): bool {
        foreach ($needles as $needle) {
            if (
                Str::contains(
                    $text,
                    $needle
                )
            ) {
                return true;
            }
        }

        return false;
    }

    /*
    |--------------------------------------------------------------------------
    | DETECT STATUS WO
    |--------------------------------------------------------------------------
    */

    private function detectWorkOrderStatus(
        string $q
    ): ?string {
        if (
            $this->containsAny(
                $q,
                [
                    'in proses',
                    'proses',
                    'sedang proses',
                    'sedang dikerjakan',
                    'dikerjakan',
                    'on progress',
                    'in progress',
                    'progress',
                ]
            )
        ) {
            return 'IN PROSES';
        }

        if (
            $this->containsAny(
                $q,
                [
                    'open',
                    'wo open',
                    'yang open',
                    'masih open',
                    'terbuka',
                ]
            )
        ) {
            return 'OPEN';
        }

        if (
            $this->containsAny(
                $q,
                [
                    'close',
                    'closed',
                    'selesai',
                    'sudah selesai',
                    'wo close',
                ]
            )
        ) {
            return 'CLOSE';
        }

        return null;
    }

    /*
    |--------------------------------------------------------------------------
    | DETECT PRIORITY
    |--------------------------------------------------------------------------
    */

    private function detectPriority(
        string $q
    ): ?string {
        $map = [
            [
                'value' => 'EMERGENCY',
                'words' => [
                    'emergency',
                    'darurat',
                    'sangat darurat',
                    'critical emergency',
                    'prioritas emergency',
                ],
            ],
            [
                'value' => 'URGENT',
                'words' => [
                    'urgent',
                    'mendesak',
                    'sangat mendesak',
                    'prioritas urgent',
                ],
            ],
            [
                'value' => 'HIGH',
                'words' => [
                    'high priority',
                    'prioritas tinggi',
                    'tinggi',
                    'high',
                ],
            ],
            [
                'value' => 'MEDIUM',
                'words' => [
                    'medium priority',
                    'prioritas sedang',
                    'medium',
                ],
            ],
            [
                'value' => 'LOW',
                'words' => [
                    'low priority',
                    'prioritas rendah',
                    'rendah',
                    'low',
                ],
            ],
            [
                'value' => 'NORMAL',
                'words' => [
                    'normal',
                    'prioritas normal',
                ],
            ],
        ];

        foreach ($map as $item) {
            if (
                $this->containsAny(
                    $q,
                    $item['words']
                )
            ) {
                return $item['value'];
            }
        }

        return null;
    }

    /*
    |--------------------------------------------------------------------------
    | LOOKS LIKE WORK ORDER
    |--------------------------------------------------------------------------
    */

    private function looksLikeWorkOrderQuestion(
        string $q
    ): bool {
        if (
            $this->containsAny(
                $q,
                [
                    'work order',
                    'workorder',
                    'wo ',
                    'wo-',
                    'no wo',
                    'nomor wo',
                    'detail wo',
                    'kerusakan',
                    'perbaikan',
                    'repair',
                ]
            )
        ) {
            return true;
        }

        return (bool) preg_match(
            '/(^|\s)(wo[\s\-\/]?[a-z0-9\-\/]+)(\s|$)/i',
            $q
        );
    }

    /*
    |--------------------------------------------------------------------------
    | STOCK LIST
    |--------------------------------------------------------------------------
    */

    private function answerStockList(
        string $question
    ): string {
        $cleanQuestion =
            $this->removeCommonWords(
                $this->normalize(
                    $question
                )
            );

        $specific =
            $cleanQuestion !== ''
            && !in_array(
                $cleanQuestion,
                [
                    'stok',
                    'barang',
                    'stok barang',
                    'nama barang',
                    'daftar barang',
                    'apa saja',
                ],
                true
            );

        $barang =
            $specific
                ? $this->findBarangCollection(
                    $question
                )
                : collect();

        if (
            !$specific
            || $barang->isEmpty()
        ) {
            $barang =
                Barang::with(
                    'satuan'
                )
                ->orderBy(
                    'nama_spesifikasi'
                )
                ->get();
        }

        if (
            $barang->isEmpty()
        ) {
            return
                "STOK BARANG\n\n"
                . "Belum ada data barang.";
        }

        $lines = [
            'STOK BARANG',
            '',
            'Total jenis barang : '
            . $barang->count(),
            '',
            '| No | Nama Barang | Satuan | Qty | Minimum | Status |',
            '|---:|---|---|---:|---:|---|',
        ];

        foreach (
            $barang as $index => $item
        ) {
            $lines[] =
                "| "
                . ($index + 1)
                . " | "
                . ($item->nama_spesifikasi ?? '-')
                . " | "
                . (optional(
                    $item->satuan
                )->nama ?? '-')
                . " | "
                . $this->formatNumber(
                    $item->stok
                )
                . " | "
                . $this->formatNumber(
                    $item->stok_minimum
                )
                . " | "
                . $this->stockStatus(
                    $item
                )
                . " |";
        }

        return implode(
            "\n",
            $lines
        );
    }

    /*
    |--------------------------------------------------------------------------
    | FIND BARANG COLLECTION
    |--------------------------------------------------------------------------
    */

    private function findBarangCollection(
        string $question
    ): Collection {
        $q =
            $this->normalize(
                $question
            );

        $barang =
            Barang::with(
                'satuan'
            )
            ->orderBy(
                'nama_spesifikasi'
            )
            ->get();

        $clean =
            $this->removeCommonWords(
                $q
            );

        if (
            $clean === ''
        ) {
            return collect();
        }

        $words =
            collect(
                preg_split(
                    '/\s+/',
                    $clean
                )
            )
            ->filter(
                fn ($word) =>
                    mb_strlen(
                        trim($word)
                    ) >= 2
            )
            ->values();

        if (
            $words->isEmpty()
        ) {
            return collect();
        }

        return $barang
            ->map(
                function ($item) use ($words) {

                    $haystack =
                        $this->normalize(
                            implode(
                                ' ',
                                [
                                    $item->kode_barang ?? '',
                                    $item->nama_spesifikasi ?? '',
                                    $item->lokasi_penyimpanan ?? '',
                                    $item->kondisi_stok ?? '',
                                    $item->status ?? '',
                                ]
                            )
                        );

                    $score = 0;

                    foreach (
                        $words as $word
                    ) {
                        if (
                            Str::contains(
                                $haystack,
                                $word
                            )
                        ) {
                            $score++;
                        }
                    }

                    return [
                        'item' => $item,
                        'score' => $score,
                    ];
                }
            )
            ->filter(
                fn ($row) =>
                    $row['score'] > 0
            )
            ->sortByDesc(
                'score'
            )
            ->pluck(
                'item'
            )
            ->values();
    }

    /*
    |--------------------------------------------------------------------------
    | STOCK STATUS
    |--------------------------------------------------------------------------
    */

    private function stockStatus(
        $item
    ): string {
        $stok =
            (float) (
                $item->stok ?? 0
            );

        $minimum =
            (float) (
                $item->stok_minimum ?? 0
            );

        if (
            $stok <= 0
        ) {
            return 'Habis';
        }

        if (
            $stok <= $minimum
        ) {
            return 'Menipis';
        }

        return 'Tersedia';
    }

    /*
    |--------------------------------------------------------------------------
    | BARANG HABIS
    |--------------------------------------------------------------------------
    */

    private function answerBarangHabis(): string
    {
        $barang =
            Barang::with(
                'satuan'
            )
            ->where(
                'stok',
                '<=',
                0
            )
            ->orderBy(
                'nama_spesifikasi'
            )
            ->get();

        if (
            $barang->isEmpty()
        ) {
            return
                "BARANG HABIS\n\n"
                . "Tidak ada barang yang stoknya habis.";
        }

        $lines = [
            'BARANG HABIS',
            '',
            'Total : '
            . $barang->count()
            . ' barang',
            '',
            '| No | Nama Barang | Satuan | Qty | Minimum | Status |',
            '|---:|---|---|---:|---:|---|',
        ];

        foreach (
            $barang as $index => $item
        ) {
            $lines[] =
                "| "
                . ($index + 1)
                . " | "
                . ($item->nama_spesifikasi ?? '-')
                . " | "
                . (optional(
                    $item->satuan
                )->nama ?? '-')
                . " | "
                . $this->formatNumber(
                    $item->stok
                )
                . " | "
                . $this->formatNumber(
                    $item->stok_minimum
                )
                . " | Habis |";
        }

        return implode(
            "\n",
            $lines
        );
    }

    /*
    |--------------------------------------------------------------------------
    | RESTOCK
    |--------------------------------------------------------------------------
    */

    private function answerRestock(): string
    {
        $barang =
            Barang::with(
                'satuan'
            )
            ->where(
                function ($q) {

                    $q->where(
                        'stok',
                        '<=',
                        0
                    )
                    ->orWhereColumn(
                        'stok',
                        '<=',
                        'stok_minimum'
                    );
                }
            )
            ->orderBy(
                'stok'
            )
            ->orderBy(
                'nama_spesifikasi'
            )
            ->get();

        if (
            $barang->isEmpty()
        ) {
            return
                "RESTOCK\n\n"
                . "Tidak ada barang yang perlu direstock.";
        }

        $lines = [
            'BARANG MENIPIS / PERLU RESTOCK',
            '',
            'Total : '
            . $barang->count()
            . ' barang',
            '',
            '| No | Nama Barang | Satuan | Qty | Minimum | Status |',
            '|---:|---|---|---:|---:|---|',
        ];

        foreach (
            $barang as $index => $item
        ) {
            $lines[] =
                "| "
                . ($index + 1)
                . " | "
                . ($item->nama_spesifikasi ?? '-')
                . " | "
                . (optional(
                    $item->satuan
                )->nama ?? '-')
                . " | "
                . $this->formatNumber(
                    $item->stok
                )
                . " | "
                . $this->formatNumber(
                    $item->stok_minimum
                )
                . " | "
                . $this->stockStatus(
                    $item
                )
                . " |";
        }

        return implode(
            "\n",
            $lines
        );
    }

    /*
    |--------------------------------------------------------------------------
    | BARANG KELUAR
    |--------------------------------------------------------------------------
    */

    private function answerBarangKeluar(): string
    {
        $awal =
            Carbon::now()
                ->startOfMonth();

        $akhir =
            Carbon::now()
                ->endOfMonth();

        $data =
            BarangKeluar::query()
                ->whereBetween(
                    'tanggal_keluar',
                    [
                        $awal,
                        $akhir,
                    ]
                )
                ->orderByDesc(
                    'tanggal_keluar'
                )
                ->get();

        if (
            $data->isEmpty()
        ) {
            return
                "BARANG KELUAR BULAN INI\n\n"
                . "Belum ada transaksi.";
        }

        $qty =
            $data->sum(
                fn ($row) =>
                    (float) (
                        $row->qty ?? 0
                    )
            );

        $lines = [
            'BARANG KELUAR BULAN INI',
            '',
            'Total transaksi : '
            . $data->count(),
            'Total Qty       : '
            . $this->formatNumber(
                $qty
            ),
            '',
            '| No | Tanggal | Barang | Qty | Satuan | Keterangan |',
            '|---:|---|---|---:|---|---|',
        ];

        foreach (
            $data as $index => $row
        ) {
            $lines[] =
                "| "
                . ($index + 1)
                . " | "
                . (
                    $row->tanggal_keluar
                    ? Carbon::parse(
                        $row->tanggal_keluar
                    )->format('Y-m-d')
                    : '-'
                )
                . " | "
                . (
                    $row->nama_barang
                    ?? $row->barang
                    ?? $row->nama_spesifikasi
                    ?? '-'
                )
                . " | "
                . $this->formatNumber(
                    $row->qty
                )
                . " | "
                . (
                    $row->satuan
                    ?? '-'
                )
                . " | "
                . (
                    $row->keterangan
                    ?? '-'
                )
                . " |";
        }

        return implode(
            "\n",
            $lines
        );
    }

    /*
    |--------------------------------------------------------------------------
    | BARANG MASUK
    |--------------------------------------------------------------------------
    */

    private function answerBarangMasuk(): string
    {
        $awal =
            Carbon::now()
                ->startOfMonth();

        $akhir =
            Carbon::now()
                ->endOfMonth();

        $data =
            BarangMasuk::query()
                ->whereBetween(
                    'tanggal_masuk',
                    [
                        $awal,
                        $akhir,
                    ]
                )
                ->orderByDesc(
                    'tanggal_masuk'
                )
                ->get();

        if (
            $data->isEmpty()
        ) {
            return
                "BARANG MASUK BULAN INI\n\n"
                . "Belum ada transaksi.";
        }

        $qty =
            $data->sum(
                fn ($row) =>
                    (float) (
                        $row->qty ?? 0
                    )
            );

        $lines = [
            'BARANG MASUK BULAN INI',
            '',
            'Total transaksi : '
            . $data->count(),
            'Total Qty       : '
            . $this->formatNumber(
                $qty
            ),
            '',
            '| No | Tanggal | Barang | Qty | Satuan | Keterangan |',
            '|---:|---|---|---:|---|---|',
        ];

        foreach (
            $data as $index => $row
        ) {
            $lines[] =
                "| "
                . ($index + 1)
                . " | "
                . (
                    $row->tanggal_masuk
                    ? Carbon::parse(
                        $row->tanggal_masuk
                    )->format('Y-m-d')
                    : '-'
                )
                . " | "
                . (
                    $row->nama_barang
                    ?? $row->barang
                    ?? $row->nama_spesifikasi
                    ?? '-'
                )
                . " | "
                . $this->formatNumber(
                    $row->qty
                )
                . " | "
                . (
                    $row->satuan
                    ?? '-'
                )
                . " | "
                . (
                    $row->keterangan
                    ?? '-'
                )
                . " |";
        }

        return implode(
            "\n",
            $lines
        );
    }

    /*
    |--------------------------------------------------------------------------
    | TOP BARANG KELUAR
    |--------------------------------------------------------------------------
    */

    private function answerTopBarangKeluar(): string
    {
        $awal =
            Carbon::now()
                ->startOfMonth();

        $akhir =
            Carbon::now()
                ->endOfMonth();

        $data =
            BarangKeluar::query()
                ->whereBetween(
                    'tanggal_keluar',
                    [
                        $awal,
                        $akhir,
                    ]
                )
                ->get();

        if (
            $data->isEmpty()
        ) {
            return
                "PEMAKAIAN BARANG\n\n"
                . "Belum ada data barang keluar bulan ini.";
        }

        $groups =
            $data->groupBy(
                function ($row) {

                    return Str::lower(
                        trim(
                            $row->nama_barang
                            ?? $row->barang
                            ?? $row->nama_spesifikasi
                            ?? '-'
                        )
                    );
                }
            )
            ->map(
                function ($rows) {

                    $first =
                        $rows->first();

                    return [
                        'name' =>
                            $first->nama_barang
                            ?? $first->barang
                            ?? $first->nama_spesifikasi
                            ?? '-',

                        'satuan' =>
                            $first->satuan
                            ?? '-',

                        'qty' =>
                            $rows->sum(
                                fn ($row) =>
                                    (float) (
                                        $row->qty ?? 0
                                    )
                            ),
                    ];
                }
            )
            ->sortByDesc(
                'qty'
            )
            ->values();

        $lines = [
            'BARANG PALING BANYAK KELUAR BULAN INI',
            '',
            '| No | Nama Barang | Satuan | Total Keluar |',
            '|---:|---|---|---:|',
        ];

        foreach (
            $groups as $index => $item
        ) {
            $lines[] =
                "| "
                . ($index + 1)
                . " | "
                . $item['name']
                . " | "
                . $item['satuan']
                . " | "
                . $this->formatNumber(
                    $item['qty']
                )
                . " |";
        }

        return implode(
            "\n",
            $lines
        );
    }

    /*
    |--------------------------------------------------------------------------
    | RATA-RATA
    |--------------------------------------------------------------------------
    */

    private function answerAverageUsage(): string
    {
        $awal =
            Carbon::now()
                ->startOfMonth();

        $akhir =
            Carbon::now()
                ->endOfMonth();

        $total =
            (float) BarangKeluar::query()
                ->whereBetween(
                    'tanggal_keluar',
                    [
                        $awal,
                        $akhir,
                    ]
                )
                ->sum(
                    'qty'
                );

        $hari =
            max(
                1,
                Carbon::now()->day
            );

        $rata =
            $total / $hari;

        return
            "RATA-RATA PEMAKAIAN BULAN INI\n\n"
            . "| Keterangan | Nilai |\n"
            . "|---|---:|\n"
            . "| Total Pemakaian | "
            . $this->formatNumber(
                $total
            )
            . " |\n"
            . "| Hari Berjalan | "
            . $hari
            . " |\n"
            . "| Rata-rata / Hari | "
            . $this->formatNumber(
                $rata
            )
            . " |";
    }

    /*
    |--------------------------------------------------------------------------
    | MACHINE
    |--------------------------------------------------------------------------
    */

    private function answerMachine(
        string $question
    ): string {
        $clean =
            $this->removeCommonWords(
                $this->normalize(
                    $question
                )
            );

        $machines =
            Machine::with(
                'area'
            )
            ->orderBy(
                'nama_mesin'
            )
            ->get();

        if (
            $clean !== ''
        ) {
            $words =
                collect(
                    preg_split(
                        '/\s+/',
                        $clean
                    )
                )
                ->filter(
                    fn ($word) =>
                        mb_strlen(
                            trim($word)
                        ) >= 2
                );

            $machines =
                $machines
                ->filter(
                    function ($machine) use ($words) {

                        $areaName =
                            $this->getAttributeSafe(
                                $machine->area,
                                [
                                    'nama_area',
                                    'name',
                                    'area',
                                    'nama',
                                    'nama_lokasi',
                                    'lokasi',
                                ]
                            );

                        $haystack =
                            $this->normalize(
                                implode(
                                    ' ',
                                    [
                                        $machine->nama_mesin ?? '',
                                        $machine->kode_mesin ?? '',
                                        $machine->spesifikasi ?? '',
                                        $areaName ?? '',
                                    ]
                                )
                            );

                        foreach (
                            $words as $word
                        ) {
                            if (
                                Str::contains(
                                    $haystack,
                                    $word
                                )
                            ) {
                                return true;
                            }
                        }

                        return false;
                    }
                )
                ->values();
        }

        if (
            $machines->isEmpty()
        ) {
            return
                "MESIN\n\n"
                . "Mesin yang dicari belum ditemukan.";
        }

        $lines = [
            'DATA MESIN',
            '',
            '| No | Nama Mesin | Area | KW | Spesifikasi |',
            '|---:|---|---|---:|---|',
        ];

        foreach (
            $machines as $index => $machine
        ) {
            $areaName =
                $this->getAttributeSafe(
                    $machine->area,
                    [
                        'nama_area',
                        'name',
                        'area',
                        'nama',
                        'nama_lokasi',
                        'lokasi',
                    ]
                );

            $lines[] =
                "| "
                . ($index + 1)
                . " | "
                . ($machine->nama_mesin ?? '-')
                . " | "
                . ($areaName ?? '-')
                . " | "
                . $this->formatNumber(
                    $machine->kw
                )
                . " | "
                . ($machine->spesifikasi ?? '-')
                . " |";
        }

        return implode(
            "\n",
            $lines
        );
    }

    /*
    |--------------------------------------------------------------------------
    | SPAREPART
    |--------------------------------------------------------------------------
    */

    private function answerSparepart(
        string $question
    ): string {
        $clean =
            $this->removeCommonWords(
                $this->normalize(
                    $question
                )
            );

        $items =
            MachineSparepart::with([
                'machine.area',
                'barang',
            ])
            ->get();

        if (
            $clean !== ''
        ) {
            $words =
                collect(
                    preg_split(
                        '/\s+/',
                        $clean
                    )
                )
                ->filter(
                    fn ($word) =>
                        mb_strlen(
                            trim($word)
                        ) >= 2
                );

            $items =
                $items
                ->filter(
                    function ($item) use ($words) {

                        $machine =
                            $item->machine;

                        $barang =
                            $item->barang;

                        $areaName =
                            $this->getAttributeSafe(
                                $machine?->area,
                                [
                                    'nama_area',
                                    'name',
                                    'area',
                                    'nama',
                                    'nama_lokasi',
                                    'lokasi',
                                ]
                            );

                        $haystack =
                            $this->normalize(
                                implode(
                                    ' ',
                                    [
                                        $machine?->nama_mesin ?? '',
                                        $machine?->kode_mesin ?? '',
                                        $barang?->nama_spesifikasi ?? '',
                                        $barang?->kode_barang ?? '',
                                        $areaName ?? '',
                                    ]
                                )
                            );

                        foreach (
                            $words as $word
                        ) {
                            if (
                                Str::contains(
                                    $haystack,
                                    $word
                                )
                            ) {
                                return true;
                            }
                        }

                        return false;
                    }
                )
                ->values();
        }

        if (
            $items->isEmpty()
        ) {
            return
                "DATA SPAREPART\n\n"
                . "Sparepart yang dimaksud belum ditemukan.";
        }

        $lines = [
            'DATA SPAREPART',
            '',
            '| No | Mesin | Area | Sparepart / Barang | Kode | Qty |',
            '|---:|---|---|---|---|---:|',
        ];

        foreach (
            $items as $index => $item
        ) {
            $machine =
                $item->machine;

            $barang =
                $item->barang;

            $areaName =
                $this->getAttributeSafe(
                    $machine?->area,
                    [
                        'nama_area',
                        'name',
                        'area',
                        'nama',
                        'nama_lokasi',
                        'lokasi',
                    ]
                );

            $lines[] =
                "| "
                . ($index + 1)
                . " | "
                . ($machine?->nama_mesin ?? '-')
                . " | "
                . ($areaName ?? '-')
                . " | "
                . ($barang?->nama_spesifikasi ?? '-')
                . " | "
                . ($barang?->kode_barang ?? '-')
                . " | "
                . $this->formatNumber(
                    $item->qty
                )
                . " |";
        }

        return implode(
            "\n",
            $lines
        );
    }

    /*
    |--------------------------------------------------------------------------
    | AREA NAME COLUMN
    |--------------------------------------------------------------------------
    */

    private function areaNameColumn(): string
    {
        $columns =
            $this->modelColumns(
                Area::class
            );

        return
            $this->findExistingColumn(
                $columns,
                [
                    'nama_area',
                    'name',
                    'area',
                    'nama',
                    'nama_lokasi',
                    'lokasi',
                ]
            )
            ?? $columns[0]
            ?? 'id';
    }

    /*
    |--------------------------------------------------------------------------
    | AREA CODE COLUMN
    |--------------------------------------------------------------------------
    */

    private function areaCodeColumn(): ?string
    {
        return
            $this->findExistingColumn(
                $this->modelColumns(
                    Area::class
                ),
                [
                    'code',
                    'kode_area',
                    'kode',
                ]
            );
    }

    /*
    |--------------------------------------------------------------------------
    | AREA STATUS COLUMN
    |--------------------------------------------------------------------------
    */

    private function areaStatusColumn(): ?string
    {
        return
            $this->findExistingColumn(
                $this->modelColumns(
                    Area::class
                ),
                [
                    'status',
                    'status_area',
                    'aktif',
                ]
            );
    }

    /*
    |--------------------------------------------------------------------------
    | AREA
    |--------------------------------------------------------------------------
    */

    private function answerArea(
        string $question
    ): string {
        $q =
            $this->removeCommonWords(
                $this->normalize(
                    $question
                )
            );

        $areaNameColumn =
            $this->areaNameColumn();

        $areas =
            Area::with(
                'machines'
            )
            ->orderBy(
                $areaNameColumn
            )
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Kalau user menyebut nama area,
        | cari berdasarkan nama area, kode, status.
        |--------------------------------------------------------------------------
        */

        if (
            $q !== ''
            && $this->hasRealAreaKeyword(
                $q
            )
        ) {
            $words =
                collect(
                    preg_split(
                        '/\s+/',
                        trim($q)
                    )
                )
                ->filter(
                    fn ($word) =>
                        mb_strlen(
                            trim($word)
                        ) >= 2
                )
                ->values();

            $areas =
                $areas
                ->filter(
                    function ($area) use ($words) {

                        $name =
                            $this->getAttributeSafe(
                                $area,
                                [
                                    'nama_area',
                                    'name',
                                    'area',
                                    'nama',
                                    'nama_lokasi',
                                    'lokasi',
                                ]
                            );

                        $code =
                            $this->getAttributeSafe(
                                $area,
                                [
                                    'code',
                                    'kode_area',
                                    'kode',
                                ]
                            );

                        $status =
                            $this->getAttributeSafe(
                                $area,
                                [
                                    'status',
                                    'status_area',
                                    'aktif',
                                ]
                            );

                        $haystack =
                            $this->normalize(
                                implode(
                                    ' ',
                                    [
                                        $name ?? '',
                                        $code ?? '',
                                        $status ?? '',
                                    ]
                                )
                            );

                        foreach (
                            $words as $word
                        ) {
                            if (
                                Str::contains(
                                    $haystack,
                                    $word
                                )
                            ) {
                                return true;
                            }
                        }

                        return false;
                    }
                )
                ->values();
        }

        if (
            $areas->isEmpty()
        ) {
            return
                "AREA\n\n"
                . "Area yang dicari belum ditemukan.";
        }

        $lines = [
            'DATA AREA',
            '',
            '| No | Nama Area | Kode | Status | Jumlah Mesin | Total KW |',
            '|---:|---|---|---|---:|---:|',
        ];

        foreach (
            $areas as $index => $area
        ) {
            $name =
                $this->getAttributeSafe(
                    $area,
                    [
                        'nama_area',
                        'name',
                        'area',
                        'nama',
                        'nama_lokasi',
                        'lokasi',
                    ]
                ) ?? '-';

            $code =
                $this->getAttributeSafe(
                    $area,
                    [
                        'code',
                        'kode_area',
                        'kode',
                    ]
                ) ?? '-';

            $status =
                $this->getAttributeSafe(
                    $area,
                    [
                        'status',
                        'status_area',
                        'aktif',
                    ]
                ) ?? '-';

            $totalKw =
                $area->machines->sum(
                    fn ($machine) =>
                        (float) (
                            $machine->kw ?? 0
                        )
                );

            $lines[] =
                "| "
                . ($index + 1)
                . " | "
                . $name
                . " | "
                . $code
                . " | "
                . $status
                . " | "
                . $area->machines->count()
                . " | "
                . $this->formatNumber(
                    $totalKw
                )
                . " KW |";
        }

        return implode(
            "\n",
            $lines
        );
    }

    /*
    |--------------------------------------------------------------------------
    | REAL AREA KEYWORD
    |--------------------------------------------------------------------------
    */

    private function hasRealAreaKeyword(
        string $q
    ): bool {
        $q = trim($q);

        return $q !== ''
            && !in_array(
                $q,
                [
                    'area',
                    'lokasi',
                    'area apa saja',
                    'daftar area',
                    'nama area',
                ],
                true
            );
    }

    /*
    |--------------------------------------------------------------------------
    | TOTAL KW
    |--------------------------------------------------------------------------
    */

    private function answerTotalKw(
        string $question
    ): string {
        $areas =
            Area::with(
                'machines'
            )
            ->orderBy(
                $this->areaNameColumn()
            )
            ->get();

        $q =
            $this->removeCommonWords(
                $this->normalize(
                    $question
                )
            );

        $selectedArea =
            $areas->first(
                function ($area) use ($q) {

                    $name =
                        $this->normalize(
                            $this->getAttributeSafe(
                                $area,
                                [
                                    'nama_area',
                                    'name',
                                    'area',
                                    'nama',
                                    'nama_lokasi',
                                    'lokasi',
                                ]
                            ) ?? ''
                        );

                    $code =
                        $this->normalize(
                            $this->getAttributeSafe(
                                $area,
                                [
                                    'code',
                                    'kode_area',
                                    'kode',
                                ]
                            ) ?? ''
                        );

                    if ($name !== '') {
                        if (
                            Str::contains(
                                $q,
                                $name
                            )
                        ) {
                            return true;
                        }

                        $nameWords =
                            collect(
                                preg_split(
                                    '/\s+/',
                                    $name
                                )
                            )
                            ->filter(
                                fn ($word) =>
                                    mb_strlen(
                                        trim($word)
                                    ) >= 3
                            );

                        foreach (
                            $nameWords as $word
                        ) {
                            if (
                                Str::contains(
                                    $q,
                                    $word
                                )
                            ) {
                                return true;
                            }
                        }
                    }

                    return
                        $code !== ''
                        &&
                        Str::contains(
                            $q,
                            $code
                        );
                }
            );

        if (
            $selectedArea
        ) {
            $name =
                $this->getAttributeSafe(
                    $selectedArea,
                    [
                        'nama_area',
                        'name',
                        'area',
                        'nama',
                        'nama_lokasi',
                        'lokasi',
                    ]
                ) ?? '-';

            $total =
                $selectedArea->machines->sum(
                    fn ($machine) =>
                        (float) (
                            $machine->kw ?? 0
                        )
                );

            $lines = [
                'TOTAL KW AREA',
                '',
                '| Keterangan | Nilai |',
                '|---|---:|',
                '| Area | '
                . $name
                . ' |',
                '| Jumlah Mesin | '
                . $selectedArea->machines->count()
                . ' |',
                '| Total KW | '
                . $this->formatNumber(
                    $total
                )
                . ' KW |',
                '',
                'RINCIAN MESIN',
                '',
                '| No | Mesin | KW |',
                '|---:|---|---:|',
            ];

            foreach (
                $selectedArea->machines
                as $index => $machine
            ) {
                $lines[] =
                    "| "
                    . ($index + 1)
                    . " | "
                    . ($machine->nama_mesin ?? '-')
                    . " | "
                    . $this->formatNumber(
                        $machine->kw
                    )
                    . " KW |";
            }

            return implode(
                "\n",
                $lines
            );
        }

        $lines = [
            'TOTAL KW SEMUA AREA',
            '',
            '| No | Area | Jumlah Mesin | Total KW |',
            '|---:|---|---:|---:|',
        ];

        $grandTotal = 0;

        foreach (
            $areas as $index => $area
        ) {
            $name =
                $this->getAttributeSafe(
                    $area,
                    [
                        'nama_area',
                        'name',
                        'area',
                        'nama',
                        'nama_lokasi',
                        'lokasi',
                    ]
                ) ?? '-';

            $total =
                $area->machines->sum(
                    fn ($machine) =>
                        (float) (
                            $machine->kw ?? 0
                        )
                );

            $grandTotal +=
                $total;

            $lines[] =
                "| "
                . ($index + 1)
                . " | "
                . $name
                . " | "
                . $area->machines->count()
                . " | "
                . $this->formatNumber(
                    $total
                )
                . " KW |";
        }

        $lines[] = '';

        $lines[] =
            '| TOTAL | | | '
            . $this->formatNumber(
                $grandTotal
            )
            . ' KW |';

        return implode(
            "\n",
            $lines
        );
    }

    /*
    |--------------------------------------------------------------------------
    | WO HARI INI
    |--------------------------------------------------------------------------
    */

    private function answerWorkOrderToday(): string
    {
        $today =
            Carbon::today();

        $dateColumn =
            $this->workOrderDateColumn();

        $data =
            WorkOrder::query()
                ->whereDate(
                    $dateColumn,
                    $today
                )
                ->orderByDesc(
                    $dateColumn
                )
                ->get();

        return $this->formatWorkOrderTable(
            $data,
            'WORK ORDER HARI INI'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | WO STATUS
    |--------------------------------------------------------------------------
    */

    private function answerWorkOrderByStatus(
        string $status,
        string $question = ''
    ): string {
        $columns =
            $this->workOrderColumns();

        $statusColumn =
            $this->findExistingColumn(
                $columns,
                [
                    'status',
                    'status_wo',
                    'wo_status',
                    'state',
                ]
            );

        if (
            $statusColumn === null
        ) {
            return
                "WORK ORDER {$status}\n\n"
                . "Kolom status belum dapat ditemukan pada Work Order.";
        }

        $variants =
            $this->statusVariants(
                $status
            );

        $query =
            WorkOrder::query();

        $query->where(
            function ($q) use (
                $statusColumn,
                $variants
            ) {
                foreach (
                    $variants as $index => $variant
                ) {
                    if (
                        $index === 0
                    ) {
                        $q->where(
                            $statusColumn,
                            'like',
                            '%' . $variant . '%'
                        );
                    } else {
                        $q->orWhere(
                            $statusColumn,
                            'like',
                            '%' . $variant . '%'
                        );
                    }
                }
            }
        );

        $normalizedQuestion =
            $this->normalize(
                $question
            );

        if (
            $this->containsAny(
                $normalizedQuestion,
                [
                    'hari ini',
                    'today',
                ]
            )
        ) {
            $query->whereDate(
                $this->workOrderDateColumn(),
                Carbon::today()
            );
        }

        $data =
            $query
                ->orderByDesc(
                    $this->workOrderDateColumn()
                )
                ->get();

        return $this->formatWorkOrderTable(
            $data,
            'WORK ORDER ' . $status
        );
    }

    /*
    |--------------------------------------------------------------------------
    | WO PRIORITY
    |--------------------------------------------------------------------------
    */

    private function answerWorkOrderByPriority(
        string $priority,
        string $question = ''
    ): string {
        $columns =
            $this->workOrderColumns();

        $priorityColumn =
            $this->findExistingColumn(
                $columns,
                [
                    'prioritas',
                    'priority',
                    'prioritas_wo',
                    'wo_prioritas',
                    'level_prioritas',
                    'priority_level',
                ]
            );

        if (
            $priorityColumn !== null
        ) {
            $query =
                WorkOrder::query()
                    ->where(
                        $priorityColumn,
                        'like',
                        '%' . $priority . '%'
                    );

            $normalizedQuestion =
                $this->normalize(
                    $question
                );

            if (
                $this->containsAny(
                    $normalizedQuestion,
                    [
                        'hari ini',
                        'today',
                    ]
                )
            ) {
                $query->whereDate(
                    $this->workOrderDateColumn(),
                    Carbon::today()
                );
            }

            $data =
                $query
                    ->orderByDesc(
                        $this->workOrderDateColumn()
                    )
                    ->get();

            return $this->formatWorkOrderTable(
                $data,
                'WORK ORDER ' . $priority
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Fallback seluruh kolom teks
        |--------------------------------------------------------------------------
        */

        $textColumns =
            $this->findTextColumns(
                WorkOrder::class
            );

        if (
            empty($textColumns)
        ) {
            return
                "WORK ORDER {$priority}\n\n"
                . "Data Work Order belum dapat dibaca.";
        }

        $query =
            WorkOrder::query()
                ->where(
                    function ($q) use (
                        $textColumns,
                        $priority
                    ) {
                        foreach (
                            $textColumns as $index => $column
                        ) {
                            if (
                                $index === 0
                            ) {
                                $q->where(
                                    $column,
                                    'like',
                                    '%' . $priority . '%'
                                );
                            } else {
                                $q->orWhere(
                                    $column,
                                    'like',
                                    '%' . $priority . '%'
                                );
                            }
                        }
                    }
                );

        $normalizedQuestion =
            $this->normalize(
                $question
            );

        if (
            $this->containsAny(
                $normalizedQuestion,
                [
                    'hari ini',
                    'today',
                ]
            )
        ) {
            $query->whereDate(
                $this->workOrderDateColumn(),
                Carbon::today()
            );
        }

        $data =
            $query
                ->orderByDesc(
                    $this->workOrderDateColumn()
                )
                ->get();

        return $this->formatWorkOrderTable(
            $data,
            'WORK ORDER ' . $priority
        );
    }

    /*
    |--------------------------------------------------------------------------
    | WO DETAIL
    |--------------------------------------------------------------------------
    */

    private function answerWorkOrder(
        string $question
    ): string {
        $q =
            $this->normalize(
                $question
            );

        $candidate =
            $this->extractWoCandidate(
                $question
            );

        if (
            $candidate !== null
        ) {
            $noWoColumn =
                $this->findExistingColumn(
                    $this->workOrderColumns(),
                    [
                        'no_wo',
                        'nomor_wo',
                        'wo_no',
                        'no_work_order',
                        'work_order_no',
                    ]
                );

            if (
                $noWoColumn
            ) {
                $wo =
                    WorkOrder::where(
                        $noWoColumn,
                        $candidate
                    )->first();

                if (
                    !$wo
                ) {
                    $wo =
                        WorkOrder::where(
                            $noWoColumn,
                            'like',
                            '%' . $candidate . '%'
                        )->first();
                }

                if (
                    $wo
                ) {
                    return $this->formatSingleWorkOrder(
                        $wo
                    );
                }
            }
        }

        $keyword =
            $this->removeCommonWords(
                $q
            );

        if (
            $keyword !== ''
        ) {
            $data =
                $this->searchWorkOrderKeyword(
                    $keyword
                );

            if (
                $data->isNotEmpty()
            ) {
                return $this->formatWorkOrderTable(
                    $data,
                    'HASIL WORK ORDER: '
                    . Str::upper(
                        $keyword
                    )
                );
            }
        }

        return $this->formatWorkOrderTable(
            WorkOrder::query()
                ->orderByDesc(
                    $this->workOrderDateColumn()
                )
                ->limit(100)
                ->get(),
            'DAFTAR WORK ORDER'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | EXTRACT WO
    |--------------------------------------------------------------------------
    */

    private function extractWoCandidate(
        string $question
    ): ?string {
        $question =
            trim(
                $question
            );

        if (
            preg_match(
                '/\b(?:wo[\s\-\/]?)?([A-Z0-9]+(?:[\-\/][A-Z0-9]+)*)\b/i',
                $question,
                $matches
            )
        ) {
            $candidate =
                trim(
                    $matches[1]
                );

            $ignore = [
                'hari',
                'ini',
                'ada',
                'berapa',
                'work',
                'order',
                'yang',
                'open',
                'close',
                'proses',
                'emergency',
                'urgent',
                'in',
                'wo',
                'total',
            ];

            if (
                !in_array(
                    Str::lower(
                        $candidate
                    ),
                    $ignore,
                    true
                )
            ) {
                return $candidate;
            }
        }

        return null;
    }

    /*
    |--------------------------------------------------------------------------
    | GLOBAL SEARCH
    |--------------------------------------------------------------------------
    */

    private function globalSearch(
        string $question
    ): string {
        $keyword =
            $this->removeCommonWords(
                $this->normalize(
                    $question
                )
            );

        if (
            $keyword === ''
        ) {
            return
                "Silakan masukkan nama barang, mesin, area, sparepart, atau informasi Work Order.";
        }

        $sections = [];

        /*
        |--------------------------------------------------------------------------
        | BARANG
        |--------------------------------------------------------------------------
        */

        $barang =
            $this->findBarangCollection(
                $question
            );

        if (
            $barang->isNotEmpty()
        ) {
            $lines = [
                'BARANG',
                '',
                '| No | Nama Barang | Satuan | Qty | Minimum | Status |',
                '|---:|---|---|---:|---:|---|',
            ];

            foreach (
                $barang as $index => $item
            ) {
                $lines[] =
                    "| "
                    . ($index + 1)
                    . " | "
                    . ($item->nama_spesifikasi ?? '-')
                    . " | "
                    . (optional(
                        $item->satuan
                    )->nama ?? '-')
                    . " | "
                    . $this->formatNumber(
                        $item->stok
                    )
                    . " | "
                    . $this->formatNumber(
                        $item->stok_minimum
                    )
                    . " | "
                    . $this->stockStatus(
                        $item
                    )
                    . " |";
            }

            $sections[] =
                implode(
                    "\n",
                    $lines
                );
        }

        /*
        |--------------------------------------------------------------------------
        | MESIN
        |--------------------------------------------------------------------------
        */

        $machines =
            $this->searchMachines(
                $keyword
            );

        if (
            $machines->isNotEmpty()
        ) {
            $lines = [
                'MESIN',
                '',
                '| No | Nama Mesin | Area | KW | Spesifikasi |',
                '|---:|---|---|---:|---|',
            ];

            foreach (
                $machines as $index => $machine
            ) {
                $areaName =
                    $this->getAttributeSafe(
                        $machine->area,
                        [
                            'nama_area',
                            'name',
                            'area',
                            'nama',
                            'nama_lokasi',
                            'lokasi',
                        ]
                    );

                $lines[] =
                    "| "
                    . ($index + 1)
                    . " | "
                    . ($machine->nama_mesin ?? '-')
                    . " | "
                    . ($areaName ?? '-')
                    . " | "
                    . $this->formatNumber(
                        $machine->kw
                    )
                    . " | "
                    . ($machine->spesifikasi ?? '-')
                    . " |";
            }

            $sections[] =
                implode(
                    "\n",
                    $lines
                );
        }

        /*
        |--------------------------------------------------------------------------
        | AREA
        |--------------------------------------------------------------------------
        */

        $areas =
            $this->searchAreas(
                $keyword
            );

        if (
            $areas->isNotEmpty()
        ) {
            $lines = [
                'AREA',
                '',
                '| No | Nama Area | Kode | Status | Mesin | Total KW |',
                '|---:|---|---|---|---:|---:|',
            ];

            foreach (
                $areas as $index => $area
            ) {
                $name =
                    $this->getAttributeSafe(
                        $area,
                        [
                            'nama_area',
                            'name',
                            'area',
                            'nama',
                            'nama_lokasi',
                            'lokasi',
                        ]
                    ) ?? '-';

                $code =
                    $this->getAttributeSafe(
                        $area,
                        [
                            'code',
                            'kode_area',
                            'kode',
                        ]
                    ) ?? '-';

                $status =
                    $this->getAttributeSafe(
                        $area,
                        [
                            'status',
                            'status_area',
                            'aktif',
                        ]
                    ) ?? '-';

                $totalKw =
                    $area->machines->sum(
                        fn ($machine) =>
                            (float) (
                                $machine->kw ?? 0
                            )
                    );

                $lines[] =
                    "| "
                    . ($index + 1)
                    . " | "
                    . $name
                    . " | "
                    . $code
                    . " | "
                    . $status
                    . " | "
                    . $area->machines->count()
                    . " | "
                    . $this->formatNumber(
                        $totalKw
                    )
                    . " KW |";
            }

            $sections[] =
                implode(
                    "\n",
                    $lines
                );
        }

        /*
        |--------------------------------------------------------------------------
        | SPAREPART
        |--------------------------------------------------------------------------
        */

        $spareparts =
            $this->searchSpareparts(
                $keyword
            );

        if (
            $spareparts->isNotEmpty()
        ) {
            $lines = [
                'SPAREPART',
                '',
                '| No | Mesin | Area | Barang | Kode | Qty |',
                '|---:|---|---|---|---|---:|',
            ];

            foreach (
                $spareparts as $index => $item
            ) {
                $machine =
                    $item->machine;

                $barang =
                    $item->barang;

                $areaName =
                    $this->getAttributeSafe(
                        $machine?->area,
                        [
                            'nama_area',
                            'name',
                            'area',
                            'nama',
                            'nama_lokasi',
                            'lokasi',
                        ]
                    );

                $lines[] =
                    "| "
                    . ($index + 1)
                    . " | "
                    . ($machine?->nama_mesin ?? '-')
                    . " | "
                    . ($areaName ?? '-')
                    . " | "
                    . ($barang?->nama_spesifikasi ?? '-')
                    . " | "
                    . ($barang?->kode_barang ?? '-')
                    . " | "
                    . $this->formatNumber(
                        $item->qty
                    )
                    . " |";
            }

            $sections[] =
                implode(
                    "\n",
                    $lines
                );
        }

        /*
        |--------------------------------------------------------------------------
        | WORK ORDER
        |--------------------------------------------------------------------------
        */

        $workOrders =
            $this->searchWorkOrderKeyword(
                $keyword
            );

        if (
            $workOrders->isNotEmpty()
        ) {
            $sections[] =
                $this->formatWorkOrderTable(
                    $workOrders,
                    'WORK ORDER'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Jika tidak ada hasil
        |--------------------------------------------------------------------------
        */

        if (
            empty($sections)
        ) {
            return
                "Data tidak ditemukan untuk: "
                . $question;
        }

        return implode(
            "\n\n",
            [
                "HASIL PENCARIAN: "
                . Str::upper(
                    $question
                ),

                implode(
                    "\n\n",
                    $sections
                ),
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | SEARCH MACHINES
    |--------------------------------------------------------------------------
    */

    private function searchMachines(
        string $keyword
    ): Collection {
        $words =
            collect(
                preg_split(
                    '/\s+/',
                    trim(
                        $keyword
                    )
                )
            )
            ->filter(
                fn ($word) =>
                    mb_strlen(
                        trim($word)
                    ) >= 2
            );

        if (
            $words->isEmpty()
        ) {
            return collect();
        }

        return Machine::with(
            'area'
        )
        ->get()
        ->filter(
            function ($machine) use ($words) {

                $areaName =
                    $this->getAttributeSafe(
                        $machine->area,
                        [
                            'nama_area',
                            'name',
                            'area',
                            'nama',
                            'nama_lokasi',
                            'lokasi',
                        ]
                    );

                $haystack =
                    $this->normalize(
                        implode(
                            ' ',
                            [
                                $machine->nama_mesin ?? '',
                                $machine->kode_mesin ?? '',
                                $machine->spesifikasi ?? '',
                                $areaName ?? '',
                            ]
                        )
                    );

                foreach (
                    $words as $word
                ) {
                    if (
                        Str::contains(
                            $haystack,
                            $word
                        )
                    ) {
                        return true;
                    }
                }

                return false;
            }
        )
        ->values();
    }

    /*
    |--------------------------------------------------------------------------
    | SEARCH AREAS
    |--------------------------------------------------------------------------
    */

    private function searchAreas(
        string $keyword
    ): Collection {
        $areas =
            Area::with(
                'machines'
            )
            ->get();

        $words =
            collect(
                preg_split(
                    '/\s+/',
                    trim(
                        $this->normalize(
                            $keyword
                        )
                    )
                )
            )
            ->filter(
                fn ($word) =>
                    mb_strlen(
                        trim($word)
                    ) >= 2
            )
            ->values();

        if (
            $words->isEmpty()
        ) {
            return collect();
        }

        return $areas
            ->filter(
                function ($area) use ($words) {

                    $name =
                        $this->getAttributeSafe(
                            $area,
                            [
                                'nama_area',
                                'name',
                                'area',
                                'nama',
                                'nama_lokasi',
                                'lokasi',
                            ]
                        );

                    $code =
                        $this->getAttributeSafe(
                            $area,
                            [
                                'code',
                                'kode_area',
                                'kode',
                            ]
                        );

                    $status =
                        $this->getAttributeSafe(
                            $area,
                            [
                                'status',
                                'status_area',
                                'aktif',
                            ]
                        );

                    $haystack =
                        $this->normalize(
                            implode(
                                ' ',
                                [
                                    $name ?? '',
                                    $code ?? '',
                                    $status ?? '',
                                ]
                            )
                        );

                    foreach (
                        $words as $word
                    ) {
                        if (
                            Str::contains(
                                $haystack,
                                $word
                            )
                        ) {
                            return true;
                        }
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Cari area melalui mesin yang berada di area tersebut
                    |--------------------------------------------------------------------------
                    */

                    foreach (
                        $area->machines as $machine
                    ) {
                        $machineHaystack =
                            $this->normalize(
                                implode(
                                    ' ',
                                    [
                                        $machine->nama_mesin ?? '',
                                        $machine->kode_mesin ?? '',
                                        $machine->spesifikasi ?? '',
                                    ]
                                )
                            );

                        foreach (
                            $words as $word
                        ) {
                            if (
                                Str::contains(
                                    $machineHaystack,
                                    $word
                                )
                            ) {
                                return true;
                            }
                        }
                    }

                    return false;
                }
            )
            ->sortBy(
                function ($area) {

                    return $this->normalize(
                        $this->getAttributeSafe(
                            $area,
                            [
                                'nama_area',
                                'name',
                                'area',
                                'nama',
                                'nama_lokasi',
                                'lokasi',
                            ]
                        ) ?? ''
                    );
                }
            )
            ->values();
    }

    /*
    |--------------------------------------------------------------------------
    | SEARCH SPAREPART
    |--------------------------------------------------------------------------
    */

    private function searchSpareparts(
        string $keyword
    ): Collection {
        $words =
            collect(
                preg_split(
                    '/\s+/',
                    trim(
                        $this->normalize(
                            $keyword
                        )
                    )
                )
            )
            ->filter(
                fn ($word) =>
                    mb_strlen(
                        trim($word)
                    ) >= 2
            )
            ->values();

        if (
            $words->isEmpty()
        ) {
            return collect();
        }

        return MachineSparepart::with([
            'machine.area',
            'barang',
        ])
        ->get()
        ->filter(
            function ($item) use ($words) {

                $machine =
                    $item->machine;

                $barang =
                    $item->barang;

                $areaName =
                    $this->getAttributeSafe(
                        $machine?->area,
                        [
                            'nama_area',
                            'name',
                            'area',
                            'nama',
                            'nama_lokasi',
                            'lokasi',
                        ]
                    );

                $haystack =
                    $this->normalize(
                        implode(
                            ' ',
                            [
                                $machine?->nama_mesin ?? '',
                                $machine?->kode_mesin ?? '',
                                $barang?->nama_spesifikasi ?? '',
                                $barang?->kode_barang ?? '',
                                $areaName ?? '',
                            ]
                        )
                    );

                foreach (
                    $words as $word
                ) {
                    if (
                        Str::contains(
                            $haystack,
                            $word
                        )
                    ) {
                        return true;
                    }
                }

                return false;
            }
        )
        ->values();
    }

    /*
    |--------------------------------------------------------------------------
    | SEARCH WO
    |--------------------------------------------------------------------------
    */

    private function searchWorkOrderKeyword(
        string $keyword
    ): Collection {
        $columns =
            $this->findTextColumns(
                WorkOrder::class
            );

        if (
            empty($columns)
        ) {
            return collect();
        }

        $words =
            collect(
                preg_split(
                    '/\s+/',
                    trim(
                        $keyword
                    )
                )
            )
            ->filter(
                fn ($word) =>
                    mb_strlen(
                        trim($word)
                    ) >= 2
            )
            ->values();

        if (
            $words->isEmpty()
        ) {
            return collect();
        }

        $query =
            WorkOrder::query();

        $query->where(
            function ($q) use (
                $columns,
                $words
            ) {
                foreach (
                    $words as $word
                ) {
                    $q->where(
                        function ($sub) use (
                            $columns,
                            $word
                        ) {
                            foreach (
                                $columns as $index => $column
                            ) {
                                if (
                                    $index === 0
                                ) {
                                    $sub->where(
                                        $column,
                                        'like',
                                        '%' . $word . '%'
                                    );
                                } else {
                                    $sub->orWhere(
                                        $column,
                                        'like',
                                        '%' . $word . '%'
                                    );
                                }
                            }
                        }
                    );
                }
            }
        );

        return $query
            ->orderByDesc(
                $this->workOrderDateColumn()
            )
            ->limit(100)
            ->get();
    }

    /*
    |--------------------------------------------------------------------------
    | FORMAT WO TABLE
    |--------------------------------------------------------------------------
    */

    private function formatWorkOrderTable(
        Collection $data,
        string $title
    ): string {
        if (
            $data->isEmpty()
        ) {
            return
                $title
                . "\n\n"
                . "Total : 0 WO";
        }

        $lines = [
            $title,
            '',
            'Total : '
            . $data->count()
            . ' WO',
            '',
            '| No | No WO | Tanggal | Mesin | Area | Kategori | Prioritas | Status |',
            '|---:|---|---|---|---|---|---|---|',
        ];

        foreach (
            $data as $index => $wo
        ) {
            $lines[] =
                "| "
                . ($index + 1)
                . " | "
                . (
                    $this->getAttributeSafe(
                        $wo,
                        [
                            'no_wo',
                            'nomor_wo',
                            'wo_no',
                            'no_work_order',
                            'work_order_no',
                        ]
                    ) ?? '-'
                )
                . " | "
                . $this->formatWorkOrderDate(
                    $this->getAttributeSafe(
                        $wo,
                        [
                            'tanggal_kerusakan',
                            'tanggal',
                            'tanggal_wo',
                            'created_at',
                        ]
                    )
                )
                . " | "
                . (
                    $this->getAttributeSafe(
                        $wo,
                        [
                            'mesin',
                            'nama_mesin',
                            'machine',
                        ]
                    ) ?? '-'
                )
                . " | "
                . (
                    $this->getAttributeSafe(
                        $wo,
                        [
                            'area',
                            'nama_area',
                            'lokasi',
                        ]
                    ) ?? '-'
                )
                . " | "
                . (
                    $this->getAttributeSafe(
                        $wo,
                        [
                            'kategori',
                            'category',
                        ]
                    ) ?? '-'
                )
                . " | "
                . (
                    $this->getAttributeSafe(
                        $wo,
                        [
                            'prioritas',
                            'priority',
                            'prioritas_wo',
                            'wo_prioritas',
                            'level_prioritas',
                            'priority_level',
                        ]
                    ) ?? '-'
                )
                . " | "
                . (
                    $this->getAttributeSafe(
                        $wo,
                        [
                            'status',
                            'status_wo',
                            'wo_status',
                            'state',
                        ]
                    ) ?? '-'
                )
                . " |";
        }

        return implode(
            "\n",
            $lines
        );
    }

    /*
    |--------------------------------------------------------------------------
    | FORMAT SINGLE WO
    |--------------------------------------------------------------------------
    */

    private function formatSingleWorkOrder(
        Model $wo
    ): string {
        $lines = [
            'DETAIL WORK ORDER',
            '',
            '| Keterangan | Data |',
            '|---|---|',
        ];

        $fields = [
            'No WO' => [
                'no_wo',
                'nomor_wo',
                'wo_no',
                'no_work_order',
                'work_order_no',
            ],
            'Tanggal' => [
                'tanggal_kerusakan',
                'tanggal',
                'tanggal_wo',
                'created_at',
            ],
            'Mesin' => [
                'mesin',
                'nama_mesin',
                'machine',
            ],
            'Area' => [
                'area',
                'nama_area',
                'lokasi',
            ],
            'Kategori' => [
                'kategori',
                'category',
            ],
            'Prioritas' => [
                'prioritas',
                'priority',
                'prioritas_wo',
                'wo_prioritas',
                'level_prioritas',
                'priority_level',
            ],
            'Status' => [
                'status',
                'status_wo',
                'wo_status',
                'state',
            ],
            'Kepada' => [
                'kepada',
                'ditujukan',
                'assigned_to',
            ],
            'Job' => [
                'job',
                'pekerjaan',
            ],
            'Deskripsi' => [
                'deskripsi',
                'description',
                'keterangan',
            ],
        ];

        foreach (
            $fields as $label => $columns
        ) {
            $value =
                $this->getAttributeSafe(
                    $wo,
                    $columns
                );

            if (
                $label === 'Tanggal'
            ) {
                $value =
                    $this->formatWorkOrderDate(
                        $value
                    );
            }

            $lines[] =
                "| "
                . $label
                . " | "
                . (
                    $value !== null
                    && $value !== ''
                        ? $value
                        : '-'
                )
                . " |";
        }

        return implode(
            "\n",
            $lines
        );
    }

    /*
    |--------------------------------------------------------------------------
    | DASHBOARD
    |--------------------------------------------------------------------------
    */

    private function answerDashboard(): string
    {
        $barang =
            Barang::count();

        $mesin =
            Machine::count();

        $area =
            Area::count();

        $wo =
            WorkOrder::count();

        $pr =
            PurchaseRequest::count();

        return
            "RINGKASAN PACHIRA\n\n"
            . "| Data | Jumlah |\n"
            . "|---|---:|\n"
            . "| Barang | {$barang} |\n"
            . "| Mesin | {$mesin} |\n"
            . "| Area | {$area} |\n"
            . "| Work Order | {$wo} |\n"
            . "| Purchase Request | {$pr} |";
    }

    /*
    |--------------------------------------------------------------------------
    | PURCHASE REQUEST
    |--------------------------------------------------------------------------
    */

    private function answerPurchaseRequest(): string
    {
        $columns =
            $this->modelColumns(
                PurchaseRequest::class
            );

        $dateColumn =
            $this->findExistingColumn(
                $columns,
                [
                    'tanggal_pr',
                    'tanggal',
                    'tanggal_pengajuan',
                    'created_at',
                ]
            );

        $query =
            PurchaseRequest::query();

        if (
            $dateColumn
        ) {
            $query->whereBetween(
                $dateColumn,
                [
                    Carbon::now()
                        ->startOfMonth(),

                    Carbon::now()
                        ->endOfMonth(),
                ]
            );
        }

        $total =
            $query->count();

        return
            "PURCHASE REQUEST BULAN INI\n\n"
            . "| Keterangan | Jumlah |\n"
            . "|---|---:|\n"
            . "| Total PR | {$total} |";
    }

    /*
    |--------------------------------------------------------------------------
    | USERS
    |--------------------------------------------------------------------------
    */

    private function answerUsers(): string
    {
        return
            "DATA USER\n\n"
            . "| Keterangan | Jumlah |\n"
            . "|---|---:|\n"
            . "| Total User | "
            . User::count()
            . " |";
    }

    /*
    |--------------------------------------------------------------------------
    | REMOVE COMMON WORDS
    |--------------------------------------------------------------------------
    */

    private function removeCommonWords(
        string $text
    ): string {
        $words = [
            'berapa',
            'jumlah',
            'total',
            'data',
            'tampilkan',
            'tampilkan semua',
            'tampilkan daftar',
            'daftar',
            'apa',
            'apa saja',
            'saja',
            'ada',
            'adakah',
            'tolong',
            'mohon',
            'dong',
            'donk',
            'yang',
            'ini',
            'itu',
            'tersebut',
            'untuk',
            'dari',
            'pada',
            'dalam',
            'bulan ini',
            'hari ini',
            'hari',
            'sekarang',
            'berapa banyak',
            'bisa',
            'kasih',
            'beri',
            'berikan',
            'menampilkan',
            'minta',
            'mohon tampilkan',
            'nama',
            'please',
        ];

        foreach (
            $words as $word
        ) {
            $text =
                preg_replace(
                    '/\b'
                    . preg_quote(
                        $word,
                        '/'
                    )
                    . '\b/i',
                    ' ',
                    $text
                );
        }

        $text =
            preg_replace(
                '/\s+/',
                ' ',
                $text
            );

        return trim(
            $text
        );
    }

    /*
    |--------------------------------------------------------------------------
    | WORK ORDER COLUMNS
    |--------------------------------------------------------------------------
    */

    private function workOrderColumns(): array
    {
        return $this->modelColumns(
            WorkOrder::class
        );
    }

    /*
    |--------------------------------------------------------------------------
    | MODEL COLUMNS
    |--------------------------------------------------------------------------
    */

    private function modelColumns(
        string $modelClass
    ): array {
        try {
            /** @var Model $model */
            $model =
                new $modelClass;

            return Schema::getColumnListing(
                $model->getTable()
            );
        } catch (
            \Throwable $e
        ) {
            report($e);

            return [];
        }
    }

    /*
    |--------------------------------------------------------------------------
    | FIND EXISTING COLUMN
    |--------------------------------------------------------------------------
    */

    private function findExistingColumn(
        array $columns,
        array $candidates
    ): ?string {
        foreach (
            $candidates as $candidate
        ) {
            if (
                in_array(
                    $candidate,
                    $columns,
                    true
                )
            ) {
                return $candidate;
            }
        }

        return null;
    }

    /*
    |--------------------------------------------------------------------------
    | TEXT COLUMNS
    |--------------------------------------------------------------------------
    */

    private function findTextColumns(
        string $modelClass
    ): array {
        try {
            /** @var Model $model */
            $model =
                new $modelClass;

            $table =
                $model->getTable();

            $schema =
                Schema::getConnection();

            $driver =
                $schema->getDriverName();

            if (
                $driver === 'mysql'
            ) {
                try {
                    $rows =
                        $schema
                            ->getDoctrineSchemaManager()
                            ->listTableColumns(
                                $table
                            );

                    $result = [];

                    foreach (
                        $rows as $name => $column
                    ) {
                        $type =
                            strtolower(
                                (string) $column->getType()
                            );

                        if (
                            Str::contains(
                                $type,
                                [
                                    'string',
                                    'text',
                                    'varchar',
                                ]
                            )
                        ) {
                            $result[] =
                                $name;
                        }
                    }

                    if (
                        !empty($result)
                    ) {
                        return $result;
                    }
                } catch (
                    \Throwable $e
                ) {
                    /*
                    | Fallback di bawah.
                    */
                }
            }

            $columns =
                Schema::getColumnListing(
                    $table
                );

            $skip = [
                'id',
                'created_at',
                'updated_at',
                'deleted_at',
            ];

            return array_values(
                array_filter(
                    $columns,
                    fn ($column) =>
                        !in_array(
                            $column,
                            $skip,
                            true
                        )
                )
            );
        } catch (
            \Throwable $e
        ) {
            report($e);

            return [];
        }
    }

    /*
    |--------------------------------------------------------------------------
    | WORK ORDER DATE COLUMN
    |--------------------------------------------------------------------------
    */

    private function workOrderDateColumn(): string
    {
        $columns =
            $this->workOrderColumns();

        return
            $this->findExistingColumn(
                $columns,
                [
                    'tanggal_kerusakan',
                    'tanggal',
                    'tanggal_wo',
                    'created_at',
                ]
            )
            ?? 'created_at';
    }

    /*
    |--------------------------------------------------------------------------
    | STATUS VARIANTS
    |--------------------------------------------------------------------------
    */

    private function statusVariants(
        string $status
    ): array {
        return match (
            Str::upper(
                $status
            )
        ) {
            'OPEN' => [
                'OPEN',
                'Open',
                'open',
            ],

            'CLOSE' => [
                'CLOSE',
                'CLOSED',
                'Close',
                'Closed',
                'SELESAI',
                'Selesai',
            ],

            'IN PROSES' => [
                'IN PROSES',
                'IN PROGRESS',
                'PROSES',
                'PROCESS',
                'ON PROGRESS',
                'SEDANG PROSES',
            ],

            default => [
                $status,
            ],
        };
    }

    /*
    |--------------------------------------------------------------------------
    | SAFE ATTRIBUTE
    |--------------------------------------------------------------------------
    */

    private function getAttributeSafe(
        ?Model $model,
        array $columns
    ): mixed {
        if (
            !$model
        ) {
            return null;
        }

        foreach (
            $columns as $column
        ) {
            if (
                array_key_exists(
                    $column,
                    $model->getAttributes()
                )
            ) {
                return $model->{$column};
            }
        }

        return null;
    }

    /*
    |--------------------------------------------------------------------------
    | FORMAT WO DATE
    |--------------------------------------------------------------------------
    */

    private function formatWorkOrderDate(
        mixed $value
    ): string {
        if (
            empty($value)
        ) {
            return '-';
        }

        try {
            return Carbon::parse(
                $value
            )->format(
                'Y-m-d H:i'
            );
        } catch (
            \Throwable $e
        ) {
            return (string) $value;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | FORMAT NUMBER
    |--------------------------------------------------------------------------
    */

    private function formatNumber(
        mixed $value
    ): string {
        return pdsNumber(
            (float) $value,
            ',',
            '.'
        );
    }
}