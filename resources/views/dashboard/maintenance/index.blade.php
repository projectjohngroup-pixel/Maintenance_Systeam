@extends('layouts.app')

@section('title', 'Dashboard Maintenance')

@section('content')

<style>
    .dashboard-page {
        width: 100%;
    }

    .dashboard-header {
        margin-bottom: 24px;
    }

    .dashboard-header h2 {
        margin: 0 0 6px;
        font-size: 22px;
        font-weight: 700;
        color: #111827;
    }

    .dashboard-header p {
        margin: 0;
        font-size: 13px;
        color: #6b7280;
    }

    /* =========================================================
       KPI
    ========================================================== */

    .dashboard-section {
        margin-bottom: 24px;
    }

    .section-title {
        margin-bottom: 14px;
    }

    .section-title h3 {
        margin: 0 0 4px;
        font-size: 17px;
        font-weight: 700;
        color: #111827;
    }

    .section-title p {
        margin: 0;
        font-size: 12px;
        color: #6b7280;
    }

    .kpi-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 16px;
    }

    .kpi-card {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 3px 12px rgba(0, 0, 0, .04);
    }

    .kpi-label {
        font-size: 11px;
        font-weight: 700;
        color: #6b7280;
        margin-bottom: 10px;
        text-transform: uppercase;
    }

    .kpi-value {
        font-size: 28px;
        line-height: 1;
        font-weight: 800;
        color: #111827;
    }

    .kpi-note {
        margin-top: 8px;
        font-size: 11px;
        color: #9ca3af;
    }

    /* =========================================================
       CHART GRID
    ========================================================== */

    .chart-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 20px;
    }

    .chart-grid-3 {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 20px;
    }

    .dashboard-card {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 3px 12px rgba(0, 0, 0, .04);
        min-width: 0;
    }

    .card-title {
        margin-bottom: 4px;
        font-size: 15px;
        font-weight: 700;
        color: #111827;
    }

    .card-subtitle {
        margin-bottom: 16px;
        font-size: 12px;
        color: #6b7280;
    }

    .chart-box {
        width: 100%;
        height: 300px;
        position: relative;
    }

    .chart-box-small {
        width: 100%;
        height: 270px;
        position: relative;
    }

    .empty-chart {
        height: 100%;
        min-height: 220px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px dashed #d1d5db;
        border-radius: 10px;
        background: #fafafa;
        color: #9ca3af;
        font-size: 13px;
    }

    /* =========================================================
       ANALYTIC LIST
    ========================================================== */

    .analytic-list {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .analytic-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 15px;
        padding: 11px 12px;
        background: #f8fafc;
        border-radius: 9px;
    }

    .analytic-name {
        font-size: 12px;
        color: #374151;
        font-weight: 600;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .analytic-value {
        font-size: 13px;
        color: #111827;
        font-weight: 800;
        white-space: nowrap;
    }

    .analytic-empty {
        padding: 25px 10px;
        text-align: center;
        color: #9ca3af;
        font-size: 12px;
    }

    /* =========================================================
       INVENTORY KPI
    ========================================================== */

    .inventory-kpi-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 16px;
    }

    /* =========================================================
       RESPONSIVE
    ========================================================== */

    @media (max-width: 1200px) {
        .kpi-grid,
        .inventory-kpi-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .chart-grid-3 {
            grid-template-columns: 1fr 1fr;
        }
    }

    @media (max-width: 850px) {
        .chart-grid,
        .chart-grid-3 {
            grid-template-columns: 1fr;
        }

        .chart-grid-3 {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 600px) {
        .kpi-grid,
        .inventory-kpi-grid {
            grid-template-columns: 1fr;
        }

        .dashboard-card,
        .kpi-card {
            padding: 16px;
        }

        .chart-box {
            height: 250px;
        }

        .chart-box-small {
            height: 230px;
        }
    }
</style>


<div class="dashboard-page">

    {{-- =====================================================
         HEADER
    ====================================================== --}}

    <div class="dashboard-header">

        <h2>
            @if(($dashboardDepartment ?? '') === 'MEKANIK_MAINT')
                Dashboard Mekanik & Maint
            @elseif(($dashboardDepartment ?? '') === 'PREV_MAINT')
                Dashboard Prev-Maint
            @else
                Dashboard Maintenance
            @endif
        </h2>

        <p>
            @if(($dashboardDepartment ?? '') === 'MEKANIK_MAINT')
                Monitoring Work Order dan Inventory Mekanik & Maint.
            @elseif(($dashboardDepartment ?? '') === 'PREV_MAINT')
                Monitoring Work Order dan Inventory Prev-Maint.
            @else
                Monitoring seluruh Work Order, Mekanik & Maint dan Prev-Maint.
            @endif
        </p>

    </div>


    {{-- =====================================================
         FILTER
    ====================================================== --}}

    <form method="GET" action="{{ route('dashboard') }}" class="dashboard-filter" style="display:flex; gap:10px; align-items:center; margin-bottom:20px; flex-wrap:wrap;">
        <div style="display:flex; align-items:center; gap:6px;">
            <label for="filterTahun" style="font-size:13px; font-weight:600; color:#374151;">Tahun</label>
            <select name="tahun" id="filterTahun" style="padding:6px 10px; border:1px solid #d1d5db; border-radius:8px; font-size:13px;">
                @foreach($tahunList as $t)
                    <option value="{{ $t }}" {{ ($filterTahun ?? now()->year) == $t ? 'selected' : '' }}>{{ $t }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" style="padding:6px 14px; background:#2563eb; color:#fff; border:none; border-radius:8px; font-size:13px; font-weight:600; cursor:pointer;">Terapkan</button>
        <a href="{{ route('dashboard') }}" style="padding:6px 14px; background:#e5e7eb; color:#374151; border:none; border-radius:8px; font-size:13px; text-decoration:none;">Reset</a>
    </form>


    {{-- =====================================================
         WORK ORDER KPI
    ====================================================== --}}

    <div class="dashboard-section">

        <div class="section-title">

            <h3>
                Work Order
            </h3>

            <p>
                @if(($dashboardDepartment ?? '') === 'MEKANIK_MAINT')
                    Ringkasan status Work Order Mekanik & Maint.
                @elseif(($dashboardDepartment ?? '') === 'PREV_MAINT')
                    Ringkasan status Work Order Prev-Maint.
                @else
                    Ringkasan status seluruh Work Order.
                @endif
            </p>

        </div>


        <div class="kpi-grid">

            <div class="kpi-card">

                <div class="kpi-label">
                    Total WO
                </div>

                <div class="kpi-value">
                    {{ $totalWorkOrders ?? 0 }}
                </div>

                <div class="kpi-note">
                    @if(($dashboardDepartment ?? '') === 'MEKANIK_MAINT')
                        WO Mekanik & Maint
                    @elseif(($dashboardDepartment ?? '') === 'PREV_MAINT')
                        WO Prev-Maint
                    @else
                        Seluruh Work Order
                    @endif
                </div>

            </div>


            <div class="kpi-card">

                <div class="kpi-label">
                    Open
                </div>

                <div class="kpi-value">
                    {{ $openWorkOrders ?? 0 }}
                </div>

                <div class="kpi-note">
                    Belum ditangani
                </div>

            </div>


            <div class="kpi-card">

                <div class="kpi-label">
                    In Proses
                </div>

                <div class="kpi-value">
                    {{ $inProgressWorkOrders ?? 0 }}
                </div>

                <div class="kpi-note">
                    Sedang dikerjakan
                </div>

            </div>


            <div class="kpi-card">

                <div class="kpi-label">
                    Selesai
                </div>

                <div class="kpi-value">
                    {{ $completedWorkOrders ?? 0 }}
                </div>

                <div class="kpi-note">
                    Work Order selesai
                </div>

            </div>

        </div>

    </div>


    {{-- =====================================================
         PRIORITY KPI
    ====================================================== --}}

    <div class="dashboard-section">

        <div class="section-title">

            <h3>
                Prioritas Work Order
            </h3>

            <p>
                Distribusi Work Order berdasarkan tingkat prioritas.
            </p>

        </div>


        <div class="kpi-grid">

            <div class="kpi-card">

                <div class="kpi-label">
                    Rendah
                </div>

                <div class="kpi-value">
                    {{ $lowPriorityWorkOrders ?? 0 }}
                </div>

            </div>


            <div class="kpi-card">

                <div class="kpi-label">
                    Sedang
                </div>

                <div class="kpi-value">
                    {{ $mediumPriorityWorkOrders ?? 0 }}
                </div>

            </div>


            <div class="kpi-card">

                <div class="kpi-label">
                    Tinggi
                </div>

                <div class="kpi-value">
                    {{ $highPriorityWorkOrders ?? 0 }}
                </div>

            </div>


            <div class="kpi-card">

                <div class="kpi-label">
                    Critical
                </div>

                <div class="kpi-value">
                    {{ $criticalPriorityWorkOrders ?? 0 }}
                </div>

            </div>

        </div>

    </div>


    {{-- =====================================================
         DEPARTMENT BREAKDOWN (MAINTENANCE ONLY)
    ====================================================== --}}

    @if(($dashboardDepartment ?? null) === null)
    <div class="dashboard-section">

        <div class="section-title">

            <h3>
                Per Department
            </h3>

            <p>
                Jumlah Work Order per departemen.
            </p>

        </div>


        <div class="kpi-grid">

            <div class="kpi-card">

                <div class="kpi-label">
                    Mekanik & Maint
                </div>

                <div class="kpi-value">
                    {{ $totalWoMekanik ?? 0 }}
                </div>

                <div class="kpi-note">
                    WO untuk Mekanik & Maint
                </div>

            </div>


            <div class="kpi-card">

                <div class="kpi-label">
                    Prev-Maint
                </div>

                <div class="kpi-value">
                    {{ $totalWoPrev ?? 0 }}
                </div>

                <div class="kpi-note">
                    WO untuk Prev-Maint
                </div>

            </div>


            <div class="kpi-card">

                <div class="kpi-label">
                    Pending Follow-Up
                </div>

                <div class="kpi-value">
                    {{ $pendingFollowUp ?? 0 }}
                </div>

                <div class="kpi-note">
                    Perlu tindak lanjut
                </div>

            </div>


            <div class="kpi-card">

                <div class="kpi-label">
                    WO Baru
                </div>

                <div class="kpi-value">
                    {{ $newWoCount ?? 0 }}
                </div>

                <div class="kpi-note">
                    Belum dibaca
                </div>

            </div>

        </div>

    </div>
    @endif


    {{-- =====================================================
         GRAFIK WO
    ====================================================== --}}

    <div class="dashboard-section">

        <div class="section-title">

            <h3>
                Analisis Work Order
            </h3>

            <p>
                Grafik untuk melihat perkembangan dan pola kerusakan.
            </p>

        </div>


        <div class="chart-grid">

            {{-- WO PER BULAN --}}

            <div class="dashboard-card">

                <div class="card-title">
                    Work Order per Bulan {{ $filterTahun ?? now()->year }}
                </div>

                <div class="card-subtitle">
                    Jumlah WO berdasarkan bulan.
                </div>

                <div class="chart-box">

                    <canvas id="woMonthlyChart"></canvas>

                    <div
                        id="woMonthlyEmpty"
                        class="empty-chart"
                        style="display:none;"
                    >
                        Belum ada data Work Order.
                    </div>

                </div>

            </div>


            {{-- STATUS WO --}}

            <div class="dashboard-card">

                <div class="card-title">
                    Status Work Order
                </div>

                <div class="card-subtitle">
                    Perbandingan Open, In Proses dan Selesai.
                </div>

                <div class="chart-box">

                    <canvas id="woStatusChart"></canvas>

                    <div
                        id="woStatusEmpty"
                        class="empty-chart"
                        style="display:none;"
                    >
                        Belum ada data status Work Order.
                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- =====================================================
         ANALISIS KERUSAKAN
    ====================================================== --}}

    <div class="dashboard-section">

        <div class="section-title">

            <h3>
                Analisis Kerusakan
            </h3>

            <p>
                Menampilkan kerusakan, mesin dan area yang paling banyak menghasilkan WO.
            </p>

        </div>


        <div class="chart-grid-3">

            {{-- KERUSAKAN --}}

            <div class="dashboard-card">

                <div class="card-title">
                    Kerusakan Terbanyak
                </div>

                <div class="card-subtitle">
                    Kerusakan yang paling sering dilaporkan.
                </div>

                <div class="analytic-list">

                    @forelse(($topDamages ?? []) as $item)

                        @php
                            $name =
                                data_get($item, 'label')
                                ?? data_get($item, 'nama')
                                ?? data_get($item, 'deskripsi')
                                ?? data_get($item, 'name')
                                ?? '-';

                            $value =
                                data_get($item, 'total')
                                ?? data_get($item, 'jumlah')
                                ?? data_get($item, 'count')
                                ?? 0;
                        @endphp

                        <div class="analytic-row">

                            <span class="analytic-name">
                                {{ $name }}
                            </span>

                            <span class="analytic-value">
                                {{ $value }} WO
                            </span>

                        </div>

                    @empty

                        <div class="analytic-empty">
                            Belum ada data kerusakan.
                        </div>

                    @endforelse

                </div>

            </div>


            {{-- MESIN --}}

            <div class="dashboard-card">

                <div class="card-title">
                    Mesin dengan WO Terbanyak
                </div>

                <div class="card-subtitle">
                    Mesin yang paling banyak mengalami gangguan.
                </div>

                <div class="analytic-list">

                    @forelse(($topMachines ?? []) as $item)

                        @php
                            $name =
                                data_get($item, 'label')
                                ?? data_get($item, 'nama_mesin')
                                ?? data_get($item, 'mesin')
                                ?? data_get($item, 'name')
                                ?? '-';

                            $value =
                                data_get($item, 'total')
                                ?? data_get($item, 'jumlah')
                                ?? data_get($item, 'count')
                                ?? 0;
                        @endphp

                        <div class="analytic-row">

                            <span class="analytic-name">
                                {{ $name }}
                            </span>

                            <span class="analytic-value">
                                {{ $value }} WO
                            </span>

                        </div>

                    @empty

                        <div class="analytic-empty">
                            Belum ada data mesin.
                        </div>

                    @endforelse

                </div>

            </div>


            {{-- AREA --}}

            <div class="dashboard-card">

                <div class="card-title">
                    Area dengan WO Terbanyak
                </div>

                <div class="card-subtitle">
                    Area yang paling banyak menghasilkan Work Order.
                </div>

                <div class="analytic-list">

                    @forelse(($topAreas ?? []) as $item)

                        @php
                            $name =
                                data_get($item, 'label')
                                ?? data_get($item, 'nama_area')
                                ?? data_get($item, 'area')
                                ?? data_get($item, 'name')
                                ?? '-';

                            $value =
                                data_get($item, 'total')
                                ?? data_get($item, 'jumlah')
                                ?? data_get($item, 'count')
                                ?? 0;
                        @endphp

                        <div class="analytic-row">

                            <span class="analytic-name">
                                {{ $name }}
                            </span>

                            <span class="analytic-value">
                                {{ $value }} WO
                            </span>

                        </div>

                    @empty

                        <div class="analytic-empty">
                            Belum ada data area.
                        </div>

                    @endforelse

                </div>

            </div>

        </div>

    </div>


    {{-- =====================================================
         KATEGORI KERUSAKAN
    ====================================================== --}}

    <div class="dashboard-section">

        <div class="dashboard-card">

            <div class="card-title">
                Kategori Work Order Terbanyak
            </div>

            <div class="card-subtitle">
                Distribusi WO berdasarkan kategori.
            </div>

            <div class="chart-box-small">

                <canvas id="woCategoryChart"></canvas>

                <div
                    id="woCategoryEmpty"
                    class="empty-chart"
                    style="display:none;"
                >
                    Belum ada data kategori.
                </div>

            </div>

        </div>

    </div>


    {{-- =====================================================
         INVENTORY KPI
    ====================================================== --}}

    <div class="dashboard-section">

        <div class="section-title">

            <h3>
                Inventory
            </h3>

            <p>
                Ringkasan stok dan pergerakan barang.
            </p>

        </div>


        <div class="inventory-kpi-grid">

            <div class="kpi-card">

                <div class="kpi-label">
                    Total Stok
                </div>

                <div class="kpi-value">
                    {{ $totalStock ?? 0 }}
                </div>

                <div class="kpi-note">
                    Total kuantitas stok
                </div>

            </div>


            <div class="kpi-card">

                <div class="kpi-label">
                    Jenis Barang
                </div>

                <div class="kpi-value">
                    {{ $totalItems ?? 0 }}
                </div>

                <div class="kpi-note">
                    Total item
                </div>

            </div>


            <div class="kpi-card">

                <div class="kpi-label">
                    Sparepart
                </div>

                <div class="kpi-value">
                    {{ $totalSpareparts ?? 0 }}
                </div>

                <div class="kpi-note">
                    Total sparepart
                </div>

            </div>


            <div class="kpi-card">

                <div class="kpi-label">
                    Stok Menipis
                </div>

                <div class="kpi-value">
                    {{ $lowStockItems ?? 0 }}
                </div>

                <div class="kpi-note">
                    Perlu diperhatikan
                </div>

            </div>

        </div>

    </div>


    {{-- =====================================================
         PERGERAKAN INVENTORY
    ====================================================== --}}

    <div class="dashboard-section">

        <div class="chart-grid">

            {{-- BARANG MASUK/KELUAR --}}

            <div class="dashboard-card">

                <div class="card-title">
                    Barang Masuk vs Barang Keluar
                </div>

                <div class="card-subtitle">
                    Perbandingan pergerakan inventory.
                </div>

                <div class="chart-box">

                    <canvas id="inventoryMovementChart"></canvas>

                    <div
                        id="inventoryMovementEmpty"
                        class="empty-chart"
                        style="display:none;"
                    >
                        Belum ada data inventory.
                    </div>

                </div>

            </div>


            {{-- PEMAKAIAN --}}

            <div class="dashboard-card">

                <div class="card-title">
                    Rata-rata Pemakaian
                </div>

                <div class="card-subtitle">
                    Ringkasan pemakaian barang dari data transaksi keluar.
                </div>

                <div class="analytic-list">

                    <div class="analytic-row">

                        <span class="analytic-name">
                            Rata-rata Keluar
                        </span>

                        <span class="analytic-value">
                            {{ $averageUsage ?? 0 }}
                        </span>

                    </div>


                    <div class="analytic-row">

                        <span class="analytic-name">
                            Keluar Terbanyak
                        </span>

                        <span class="analytic-value">
                            {{ $highestUsage ?? 0 }}
                        </span>

                    </div>


                    <div class="analytic-row">

                        <span class="analytic-name">
                            Barang Paling Banyak Keluar
                        </span>

                        <span class="analytic-value">
                            {{ $mostUsedItem ?? '-' }}
                        </span>

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- =====================================================
         BARANG KELUAR TERBANYAK
    ====================================================== --}}

    <div class="dashboard-section">

        <div class="dashboard-card">

            <div class="card-title">
                Barang / Sparepart Paling Banyak Keluar
            </div>

            <div class="card-subtitle">
                Menampilkan item dengan jumlah pemakaian terbesar.
            </div>

            <div class="analytic-list">

                @forelse(($topUsedItems ?? []) as $item)

                    @php
                        $name =
                            data_get($item, 'label')
                            ?? data_get($item, 'nama_barang')
                            ?? data_get($item, 'barang')
                            ?? data_get($item, 'nama')
                            ?? data_get($item, 'name')
                            ?? '-';

                        $value =
                            data_get($item, 'total')
                            ?? data_get($item, 'jumlah')
                            ?? data_get($item, 'qty')
                            ?? data_get($item, 'count')
                            ?? 0;

                        $satuan =
                            data_get($item, 'satuan')
                            ?? '';
                    @endphp

                    <div class="analytic-row">

                        <span class="analytic-name">
                            {{ $name }}
                        </span>

                        <span class="analytic-value">
                            {{ $value }} {{ $satuan }}
                        </span>

                    </div>

                @empty

                    <div class="analytic-empty">
                        Belum ada data barang keluar.
                    </div>

                @endforelse

            </div>

        </div>

    </div>


    {{-- =====================================================
         CHART DATA
    ====================================================== --}}

    <script src="{{ asset('assets/vendor/chart.umd.min.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            /*
             * Semua data grafik berasal dari controller.
             * Tidak ada data dummy.
             */

            const monthlyLabels = @json($monthlyLabels ?? []);
            const monthlyValues = @json($monthlyWorkOrders ?? []);

            const statusLabels = @json($statusLabels ?? []);
            const statusValues = @json($statusValues ?? []);

            const categoryLabels = @json($categoryLabels ?? []);
            const categoryValues = @json($categoryValues ?? []);

            const inventoryLabels = @json($inventoryLabels ?? []);
            const inventoryInValues = @json($inventoryInValues ?? []);
            const inventoryOutValues = @json($inventoryOutValues ?? []);


            /* =================================================
               HELPER
            ================================================== */

            function hasChartData(labels, values) {

                return (
                    Array.isArray(labels) &&
                    labels.length > 0 &&
                    Array.isArray(values) &&
                    values.length > 0
                );

            }


            function showEmpty(canvasId, emptyId) {

                const canvas = document.getElementById(canvasId);
                const empty = document.getElementById(emptyId);

                if (canvas) {
                    canvas.style.display = 'none';
                }

                if (empty) {
                    empty.style.display = 'flex';
                }

            }


            /* =================================================
               WO PER BULAN
            ================================================== */

            if (
                hasChartData(
                    monthlyLabels,
                    monthlyValues
                )
            ) {

                const canvas =
                    document.getElementById(
                        'woMonthlyChart'
                    );

                if (canvas) {

                    new Chart(
                        canvas,
                        {
                            type: 'line',

                            data: {
                                labels: monthlyLabels,

                                datasets: [{
                                    label: 'Work Order',

                                    data: monthlyValues,

                                    borderWidth: 2,

                                    tension: .35,

                                    fill: false,

                                    pointRadius: 4
                                }]
                            },

                            options: {

                                responsive: true,

                                maintainAspectRatio: false,

                                plugins: {
                                    legend: {
                                        display: false
                                    }
                                },

                                scales: {

                                    y: {
                                        beginAtZero: true,

                                        ticks: {
                                            precision: 0
                                        }
                                    }

                                }
                            }
                        }
                    );

                }

            } else {

                showEmpty(
                    'woMonthlyChart',
                    'woMonthlyEmpty'
                );

            }


            /* =================================================
               STATUS WO
            ================================================== */

            if (
                hasChartData(
                    statusLabels,
                    statusValues
                )
            ) {

                const canvas =
                    document.getElementById(
                        'woStatusChart'
                    );

                if (canvas) {

                    new Chart(
                        canvas,
                        {
                            type: 'doughnut',

                            data: {
                                labels: statusLabels,

                                datasets: [{
                                    data: statusValues,

                                    borderWidth: 1
                                }]
                            },

                            options: {

                                responsive: true,

                                maintainAspectRatio: false,

                                plugins: {
                                    legend: {
                                        position: 'bottom'
                                    }
                                }
                            }
                        }
                    );

                }

            } else {

                showEmpty(
                    'woStatusChart',
                    'woStatusEmpty'
                );

            }


            /* =================================================
               KATEGORI WO
            ================================================== */

            if (
                hasChartData(
                    categoryLabels,
                    categoryValues
                )
            ) {

                const canvas =
                    document.getElementById(
                        'woCategoryChart'
                    );

                if (canvas) {

                    new Chart(
                        canvas,
                        {
                            type: 'bar',

                            data: {
                                labels: categoryLabels,

                                datasets: [{
                                    label: 'Jumlah WO',

                                    data: categoryValues,

                                    borderWidth: 1
                                }]
                            },

                            options: {

                                responsive: true,

                                maintainAspectRatio: false,

                                plugins: {
                                    legend: {
                                        display: false
                                    }
                                },

                                scales: {

                                    y: {
                                        beginAtZero: true,

                                        ticks: {
                                            precision: 0
                                        }
                                    }

                                }
                            }
                        }
                    );

                }

            } else {

                showEmpty(
                    'woCategoryChart',
                    'woCategoryEmpty'
                );

            }


            /* =================================================
               INVENTORY
            ================================================== */

            if (
                hasChartData(
                    inventoryLabels,
                    inventoryInValues
                ) &&
                Array.isArray(inventoryOutValues) &&
                inventoryOutValues.length > 0
            ) {

                const canvas =
                    document.getElementById(
                        'inventoryMovementChart'
                    );

                if (canvas) {

                    new Chart(
                        canvas,
                        {
                            type: 'bar',

                            data: {

                                labels: inventoryLabels,

                                datasets: [

                                    {
                                        label: 'Barang Masuk',

                                        data: inventoryInValues,

                                        borderWidth: 1
                                    },

                                    {
                                        label: 'Barang Keluar',

                                        data: inventoryOutValues,

                                        borderWidth: 1
                                    }

                                ]

                            },

                            options: {

                                responsive: true,

                                maintainAspectRatio: false,

                                plugins: {
                                    legend: {
                                        position: 'bottom'
                                    }
                                },

                                scales: {

                                    y: {
                                        beginAtZero: true,

                                        ticks: {
                                            precision: 0
                                        }
                                    }

                                }
                            }
                        }
                    );

                }

            } else {

                showEmpty(
                    'inventoryMovementChart',
                    'inventoryMovementEmpty'
                );

            }

        });
    </script>

</div>

@endsection