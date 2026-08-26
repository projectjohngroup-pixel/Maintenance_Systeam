@extends('layouts.app')

@section('title', 'Rata-rata Pemakaian')

@section('page_title', 'Inventory')

@section(
    'page_subtitle',
    'Analisis rata-rata pemakaian barang berdasarkan periode'
)

@push('styles')

<style>

/* =====================================================
   MENU MODUL INVENTORY
===================================================== */

.inventory-module-nav {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 20px;
    padding: 8px;
    background: var(--pds-card);
    border: 1px solid var(--pds-line);
    border-radius: 12px;
    box-shadow: 0 3px 12px rgba(0,0,0,.04);
    overflow-x: auto;
}

.inventory-module-link {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 38px;
    padding: 0 14px;
    border-radius: 8px;
    color: var(--pds-ink-2);
    font-size: 12px;
    font-weight: 600;
    white-space: nowrap;
    text-decoration: none;
    cursor: pointer;
    transition: .15s ease;
}

.inventory-module-link:hover {
    background: var(--pds-soft-2);
    color: var(--pds-ink);
}

.inventory-module-link.active {
    background: #2563eb;
    color: #ffffff;
}


/* =====================================================
   HEADER
===================================================== */

.inventory-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 15px;
    margin-bottom: 20px;
}

.inventory-title h1 {
    font-size: 24px;
    margin-bottom: 5px;
}

.inventory-title p {
    color: var(--pds-muted);
    font-size: 13px;
}

.inventory-actions {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}


/* =====================================================
   SUMMARY
===================================================== */

.inventory-summary {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 15px;
    margin-bottom: 20px;
}

.inventory-summary-card {
    background: var(--pds-card);
    border: 1px solid var(--pds-line);
    border-radius: 14px;
    padding: 18px;
    box-shadow: 0 3px 12px rgba(0,0,0,.05);
}

.inventory-summary-label {
    color: var(--pds-muted);
    font-size: 12px;
    margin-bottom: 8px;
}

.inventory-summary-value {
    font-size: 25px;
    font-weight: 700;
    color: var(--pds-ink);
}

.inventory-summary-sub {
    margin-top: 5px;
    color: var(--pds-muted-2);
    font-size: 11px;
}


/* =====================================================
   FILTER
===================================================== */

.inventory-filter-card {
    padding: 15px 18px;
    margin-bottom: 20px;
}

.inventory-filter-title {
    font-size: 14px;
    font-weight: 700;
    margin-bottom: 12px;
    color: var(--pds-ink);
}

.inventory-filter {
    display: flex;
    align-items: end;
    gap: 10px;
    width: 100%;
    flex-wrap: wrap;
}

.inventory-filter-group {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.inventory-filter-group label {
    font-size: 12px;
    font-weight: 700;
    color: var(--pds-ink-2);
}

.inventory-form-control {
    height: 42px;
    padding: 0 12px;
    border: 1px solid var(--pds-line-2);
    border-radius: 8px;
    background: var(--pds-card);
    font-size: 13px;
}

.inventory-form-control:focus {
    outline: none;
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37,99,235,.08);
}

.inventory-filter-action {
    display: flex;
    gap: 8px;
}


/* =====================================================
   TABLE TOOLBAR
===================================================== */

.inventory-table-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    margin-bottom: 15px;
}

.inventory-table-title {
    font-size: 15px;
    font-weight: 700;
    color: var(--pds-ink);
}

.inventory-table-subtitle {
    margin-top: 3px;
    font-size: 12px;
    color: var(--pds-muted-2);
}

.inventory-export {
    display: flex;
    align-items: center;
    gap: 7px;
    flex-wrap: wrap;
}

.inventory-export-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    min-height: 34px;
    padding: 0 11px;
    border: 1px solid var(--pds-line-2);
    border-radius: 8px;
    background: var(--pds-card);
    color: var(--pds-ink-2);
    cursor: pointer;
    font-size: 12px;
    font-weight: 600;
}

.inventory-export-btn:hover {
    background: var(--pds-soft);
    border-color: var(--pds-muted);
}


/* =====================================================
   TABLE
===================================================== */

.inventory-table-wrapper {
    overflow-x: auto;
}

.inventory-table {
    width: 100%;
    min-width: 900px;
    border-collapse: collapse;
}

.inventory-table th,
.inventory-table td {
    padding: 12px;
    border-bottom: 1px solid var(--pds-line);
    text-align: left;
    vertical-align: middle;
    font-size: 13px;
}

.inventory-table th {
    background: var(--pds-soft);
    color: var(--pds-ink-2);
    font-weight: 700;
    white-space: nowrap;
}

.inventory-table tbody tr:hover {
    background: var(--pds-soft);
}

.barang-kode {
    font-weight: 700;
    color: var(--pds-ink);
    white-space: nowrap;
}

.text-number {
    text-align: right !important;
    font-weight: 600;
}

.text-center {
    text-align: center !important;
}

.average-pill {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 75px;
    padding: 6px 10px;
    border-radius: 999px;
    background: #eff6ff;
    color: #2563eb;
    font-size: 11px;
    font-weight: 700;
}

.inventory-empty {
    text-align: center;
    padding: 40px;
    color: var(--pds-muted-2);
}


/* =====================================================
   PRINT
===================================================== */

.inventory-print-title {
    display: none;
}

@media print {

    @page {
        margin: 12mm;
    }

    body {
        background: #ffffff !important;
        color: #000000 !important;
    }

    .inventory-module-nav,
    .inventory-header,
    .inventory-filter-card,
    .inventory-summary,
    .inventory-table-header,
    .sidebar,
    .topbar {
        display: none !important;
    }

    .main {
        width: 100% !important;
        margin-left: 0 !important;
    }

    .content {
        padding: 0 !important;
    }

    .card {
        border: none !important;
        box-shadow: none !important;
    }

    .inventory-print-title {
        display: block !important;
        text-align: center !important;
        margin-bottom: 18px !important;
    }

    .inventory-print-title h1 {
        margin: 0 0 5px !important;
        font-size: 20px !important;
        color: #000000 !important;
    }

    .inventory-print-title p {
        margin: 2px 0 !important;
        font-size: 10px !important;
        color: #555555 !important;
    }

    .inventory-table {
        width: 100% !important;
        min-width: 0 !important;
    }

    .inventory-table th,
    .inventory-table td {
        padding: 7px 6px !important;
        border: 1px solid #000000 !important;
        font-size: 9px !important;
        color: #000000 !important;
    }

    .average-pill {
        background: transparent !important;
        color: #000000 !important;
        padding: 0 !important;
    }

}

@media (max-width: 1000px) {

    .inventory-summary {
        grid-template-columns: repeat(2, 1fr);
    }

}

@media (max-width: 700px) {

    .inventory-header {
        flex-direction: column;
        align-items: flex-start;
    }

    .inventory-summary {
        grid-template-columns: 1fr;
    }

    .inventory-filter {
        align-items: stretch;
    }

    .inventory-filter-group {
        width: 100%;
    }

}

</style>

@endpush


@section('content')

<!-- =====================================================
     MENU MODUL INVENTORY
===================================================== -->

<div class="inventory-module-nav">

    <a
        href="{{ route('barang.index') }}"
        class="inventory-module-link"
    >
        <x-icon name="box"></x-icon> Stok Barang
    </a>

    <a
        href="{{ route('barang-masuk.index') }}"
        class="inventory-module-link"
    >
        <x-icon name="download"></x-icon> Barang Masuk
    </a>

    <a
        href="{{ route('barang-keluar.index') }}"
        class="inventory-module-link"
    >
        <x-icon name="upload"></x-icon> Barang Keluar
    </a>

    <a
        href="{{ url('/purchase-requests') }}"
        class="inventory-module-link"
    >
        <x-icon name="clipboard"></x-icon> Purchase Request
    </a>

    <a
        href="{{ route('laporan-harian.index') }}"
        class="inventory-module-link"
    >
        <x-icon name="clipboard"></x-icon> Laporan Harian
    </a>

    <a
        href="{{ route('rata-rata-pemakaian.index') }}"
        class="inventory-module-link active"
    >
        <x-icon name="chart"></x-icon> Rata-rata Pemakaian
    </a>

    <a
        href="{{ route('barang.restock') }}"
        class="inventory-module-link"
    >
        <x-icon name="refresh"></x-icon> Restock
    </a>

</div>


<!-- =====================================================
     HEADER
===================================================== -->

<div class="inventory-header">

    <div class="inventory-title">

        <h1>
            Rata-rata Pemakaian
        </h1>

        <p>
            Analisis pemakaian barang berdasarkan periode transaksi barang keluar.
        </p>

    </div>

    <div class="inventory-actions">

        <a
            href="{{ route('barang.index') }}"
            class="btn btn-gray"
        >
            <x-icon name="back"></x-icon> Kembali
        </a>

    </div>

</div>


<!-- =====================================================
     PRINT TITLE
===================================================== -->

<div class="inventory-print-title">

    <h1>
        RATA-RATA PEMAKAIAN BARANG
    </h1>

    <p>
        Maintenance Management System
    </p>

    <p>
        Periode:
        {{ request('dari') ?: '-' }}
        s/d
        {{ request('sampai') ?: '-' }}
    </p>

</div>


<!-- =====================================================
     SUMMARY
===================================================== -->

<div class="inventory-summary">

    <div class="inventory-summary-card">

        <div class="inventory-summary-label">
            Total Barang Digunakan
        </div>

        <div class="inventory-summary-value">
            {{ $totalBarang ?? 0 }}
        </div>

        <div class="inventory-summary-sub">
            Barang memiliki transaksi keluar
        </div>

    </div>


    <div class="inventory-summary-card">

        <div class="inventory-summary-label">
            Total Pemakaian
        </div>

        <div class="inventory-summary-value">
            {{ $totalPemakaian ?? 0 }}
        </div>

        <div class="inventory-summary-sub">
            Akumulasi barang keluar
        </div>

    </div>


    <div class="inventory-summary-card">

        <div class="inventory-summary-label">
            Rata-rata Pemakaian
        </div>

        <div class="inventory-summary-value">
            {{ $rataRataPemakaian ?? 0 }}
        </div>

        <div class="inventory-summary-sub">
            Berdasarkan periode pencarian
        </div>

    </div>


    <div class="inventory-summary-card">

        <div class="inventory-summary-label">
            Periode Analisis
        </div>

        <div class="inventory-summary-value"
             style="font-size:17px;"
        >
            {{ request('dari') ?: '-' }}
        </div>

        <div class="inventory-summary-sub">
            s/d {{ request('sampai') ?: '-' }}
        </div>

    </div>

</div>


<!-- =====================================================
     FILTER
===================================================== -->

<div class="card inventory-filter-card">

    <div class="inventory-filter-title">
        Cari Periode Pemakaian
    </div>

    <form
        method="GET"
        action="{{ route('rata-rata-pemakaian.index') }}"
        class="inventory-filter"
    >

        <div class="inventory-filter-group">

            <label>
                Dari Tanggal
            </label>

            <input
                type="date"
                name="dari"
                class="inventory-form-control"
                value="{{ request('dari') }}"
            >

        </div>


        <div class="inventory-filter-group">

            <label>
                Sampai Tanggal
            </label>

            <input
                type="date"
                name="sampai"
                class="inventory-form-control"
                value="{{ request('sampai') }}"
            >

        </div>


        <div class="inventory-filter-action">

            <button
                type="submit"
                class="btn btn-primary"
            >
                <x-icon name="search"></x-icon> Cari
            </button>

            <a
                href="{{ route('rata-rata-pemakaian.index') }}"
                class="btn btn-gray"
            >
                Reset
            </a>

        </div>

    </form>

</div>


<!-- =====================================================
     TABLE
===================================================== -->

<div class="card">

    <div class="inventory-table-header">

        <div>

            <div class="inventory-table-title">
                Pemakaian Barang
            </div>

            <div class="inventory-table-subtitle">
                Rekap berdasarkan transaksi barang keluar
            </div>

        </div>


        <div class="inventory-export">

            <button
                type="button"
                class="inventory-export-btn"
                onclick="printRataRata()"
            >
                <x-icon name="print"></x-icon> Print
            </button>

        </div>

    </div>


    <div class="inventory-table-wrapper">

        <table
            id="rataRataTable"
            class="inventory-table"
        >

            <thead>

                <tr>

                    <th>No</th>

                    <th>Kode Barang</th>

                    <th>Nama Barang / Spesifikasi</th>

                    <th>Satuan</th>

                    <th class="text-number">
                        Total Keluar
                    </th>

                    <th class="text-number">
                        Rata-rata Pemakaian
                    </th>

                </tr>

            </thead>


            <tbody>

                @forelse(($data ?? []) as $item)

                    <tr>

                        <td>
                            {{ $loop->iteration }}
                        </td>

                        <td class="barang-kode">
                            {{ $item['kode_barang'] ?? '-' }}
                        </td>

                        <td>
                            {{ $item['barang'] ?? '-' }}
                        </td>

                        <td>
                            {{ $item['satuan'] ?? '-' }}
                        </td>

                        <td class="text-number">
                            {{ $item['total'] ?? 0 }}
                        </td>

                        <td class="text-number">

                            <span class="average-pill">
                                {{ $item['rata_rata'] ?? 0 }}
                            </span>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td
                            colspan="6"
                            class="inventory-empty"
                        >
                            Belum ada data pemakaian pada periode yang dipilih.
                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</div>

@endsection


@push('scripts')

<script>

function printRataRata()
{
    window.print();
}

</script>

@endpush