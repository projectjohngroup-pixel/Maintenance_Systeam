@extends('layouts.app')

@section('title', 'Barang Keluar')

@section('page_title', 'Inventory')

@section(
    'page_subtitle',
    'Pencatatan dan pengeluaran barang keluar'
)

@push('styles')

<style>

/* =====================================================
   INVENTORY
===================================================== */

.sidebar {
    display: none !important;
}

.main {
    width: 100% !important;
    margin-left: 0 !important;
}


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
    color: #ffffff !important;
}

.inventory-module-link.disabled {
    color: var(--pds-muted-2);
    cursor: not-allowed;
    background: var(--pds-soft);
}

.inventory-module-link.disabled:hover {
    background: var(--pds-soft);
    color: var(--pds-muted-2);
}


/* =====================================================
   PAGE
===================================================== */

.bk-page {
    padding: 4px 0 30px;
}


/* =====================================================
   HEADER
===================================================== */

.bk-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 20px;
    margin-bottom: 22px;
}

.bk-title h2 {
    margin: 0 0 5px;
    font-size: 24px;
    font-weight: 700;
    color: var(--pds-ink);
}

.bk-title p {
    margin: 0;
    color: var(--pds-muted);
    font-size: 13px;
}

.bk-header-actions {
    display: flex;
    gap: 8px;
    align-items: center;
    flex-wrap: wrap;
}


/* =====================================================
   SUMMARY
===================================================== */

.bk-summary {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
    margin-bottom: 20px;
}

.bk-summary-card {
    background: var(--pds-card);
    border: 1px solid var(--pds-line);
    border-radius: 12px;
    padding: 18px 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,.04);
}

.bk-summary-label {
    color: var(--pds-muted);
    font-size: 12px;
    margin-bottom: 7px;
}

.bk-summary-value {
    color: var(--pds-ink);
    font-size: 25px;
    font-weight: 700;
}


/* =====================================================
   FILTER
===================================================== */

.bk-filter {
    background: var(--pds-card);
    border: 1px solid var(--pds-line);
    border-radius: 12px;
    padding: 18px;
    margin-bottom: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,.04);
}

.bk-filter-grid {
    display: grid;
    grid-template-columns: 2fr 1fr 1fr 1fr auto;
    gap: 12px;
    align-items: end;
}

.bk-field label {
    display: block;
    margin-bottom: 6px;
    font-size: 12px;
    font-weight: 600;
    color: var(--pds-ink-2);
}

.bk-field input,
.bk-field select {
    width: 100%;
    height: 40px;
    padding: 0 12px;
    border: 1px solid var(--pds-line-2);
    border-radius: 8px;
    background: var(--pds-card);
    color: var(--pds-ink);
    font-size: 13px;
}

.bk-field input:focus,
.bk-field select:focus {
    outline: none;
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37,99,235,.08);
}

.bk-filter-actions {
    display: flex;
    gap: 8px;
}


/* =====================================================
   TABLE CARD
===================================================== */

.bk-table-card {
    background: var(--pds-card);
    border: 1px solid var(--pds-line);
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,.04);
}


/* =====================================================
   TABLE TOP
===================================================== */

.bk-table-top {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    padding: 17px 18px;
    border-bottom: 1px solid var(--pds-line);
}

.bk-table-left {
    display: flex;
    align-items: center;
    gap: 14px;
    flex-wrap: wrap;
}

.bk-table-title {
    font-size: 15px;
    font-weight: 700;
    color: var(--pds-ink);
}

.bk-table-info {
    font-size: 12px;
    color: var(--pds-muted);
}

.bk-export {
    display: flex;
    align-items: center;
    gap: 7px;
    flex-wrap: wrap;
}

.bk-export-btn {
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

.bk-export-btn:hover {
    background: var(--pds-soft);
    border-color: var(--pds-muted);
}


/* =====================================================
   TABLE
===================================================== */

.bk-table-wrap {
    overflow-x: auto;
}

.bk-table {
    width: 100%;
    min-width: 1450px;
    border-collapse: collapse;
}

.bk-table th {
    padding: 12px 14px;
    background: var(--pds-soft);
    border-bottom: 1px solid var(--pds-line);
    color: var(--pds-ink-2);
    font-size: 12px;
    font-weight: 700;
    text-align: left;
    white-space: nowrap;
}

.bk-table td {
    padding: 13px 14px;
    border-bottom: 1px solid #eef2f7;
    color: var(--pds-ink-2);
    font-size: 13px;
    vertical-align: middle;
}

.bk-table tbody tr:hover {
    background: #fafcff;
}

.bk-number {
    width: 50px;
    color: var(--pds-muted);
}

.bk-transaction {
    font-weight: 700;
    color: var(--pds-ink);
    white-space: nowrap;
}

.bk-date {
    white-space: nowrap;
    color: var(--pds-ink-2);
}

.bk-qty {
    font-weight: 700;
    color: var(--pds-ink);
    white-space: nowrap;
}


/* =====================================================
   ACTION
===================================================== */

.bk-actions {
    display: flex;
    gap: 6px;
}

.bk-action {
    min-width: 34px;
    height: 32px;
    padding: 0 9px;
    border-radius: 7px;
    border: 1px solid var(--pds-line-2);
    background: var(--pds-card);
    font-size: 11px;
    font-weight: 600;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
}

.bk-action-view {
    color: #2563eb;
    border-color: #bfdbfe;
    background: #eff6ff;
}

.bk-action-view:hover {
    background: #dbeafe;
}


/* =====================================================
   EMPTY
===================================================== */

.bk-empty {
    padding: 55px 20px !important;
    text-align: center;
    color: var(--pds-muted-2) !important;
}


/* =====================================================
   PAGINATION
===================================================== */

.bk-pagination {
    padding: 15px 18px;
    border-top: 1px solid var(--pds-line);
}


/* =====================================================
   PRINT TITLE
===================================================== */

.bk-print-title {
    display: none;
}


/* =====================================================
   RESPONSIVE
===================================================== */

@media (max-width: 1100px) {

    .bk-summary {
        grid-template-columns: repeat(2, 1fr);
    }

    .bk-filter-grid {
        grid-template-columns: 1fr 1fr;
    }

    .bk-filter-actions {
        grid-column: 1 / -1;
    }

}

@media (max-width: 700px) {

    .bk-header {
        flex-direction: column;
        align-items: flex-start;
    }

    .bk-summary {
        grid-template-columns: 1fr;
    }

    .bk-filter-grid {
        grid-template-columns: 1fr;
    }

    .bk-filter-actions {
        grid-column: auto;
    }

    .bk-table-top {
        flex-direction: column;
        align-items: flex-start;
    }

}


/* =====================================================
   PRINT / PDF
===================================================== */

@media print {

    @page {
        size: landscape;
        margin: 10mm;
    }

    html,
    body {
        background: #ffffff !important;
        color: #000000 !important;
    }

    .sidebar,
    .topbar,
    .inventory-module-nav,
    .bk-header,
    .bk-summary,
    .bk-filter,
    .bk-export,
    .bk-actions,
    .bk-pagination,
    .message,
    .alert {
        display: none !important;
    }

    .main {
        width: 100% !important;
        margin-left: 0 !important;
    }

    .content {
        padding: 0 !important;
    }

    .bk-page {
        padding: 0 !important;
    }

    .bk-print-title {
        display: block !important;
        text-align: center !important;
        margin-bottom: 15px !important;
    }

    .bk-print-title h1 {
        margin: 0 0 5px !important;
        font-size: 18px !important;
        color: #000000 !important;
    }

    .bk-print-title p {
        margin: 2px 0 !important;
        font-size: 10px !important;
        color: #333333 !important;
    }

    .bk-table-card {
        border: none !important;
        box-shadow: none !important;
        overflow: visible !important;
    }

    .bk-table-wrap {
        overflow: visible !important;
    }

    .bk-table {
        width: 100% !important;
        min-width: 0 !important;
        border-collapse: collapse !important;
        table-layout: auto !important;
    }

    .bk-table th,
    .bk-table td {
        border: 1px solid #000000 !important;
        padding: 5px 6px !important;
        font-size: 7.5px !important;
        color: #000000 !important;
        text-align: center !important;
        vertical-align: middle !important;
    }

    .bk-table th {
        background: #f2f2f2 !important;
        font-weight: 700 !important;
    }

}


/* =====================================================
   EXCEL EXPORT PRINT-COPY SAFETY
===================================================== */

.bk-export-hidden {
    display: none !important;
}

</style>

@endpush


@section('content')

<div class="bk-page">


    <!-- =================================================
         MENU INVENTORY
    ================================================== -->

    <div class="inventory-module-nav">

        <a
            href="{{ route('barang.index') }}"
            class="
                inventory-module-link
                {{ request()->routeIs('barang.index') ? 'active' : '' }}
            "
        >
            <x-icon name="box"></x-icon> Stok Barang
        </a>


        <a
            href="{{ route('barang-masuk.index') }}"
            class="
                inventory-module-link
                {{ request()->routeIs('barang-masuk.*') ? 'active' : '' }}
            "
        >
            <x-icon name="download"></x-icon> Barang Masuk
        </a>


        <a
            href="{{ route('barang-keluar.index') }}"
            class="
                inventory-module-link
                {{ request()->routeIs('barang-keluar.*') ? 'active' : '' }}
            "
        >
            <x-icon name="upload"></x-icon> Barang Keluar
        </a>


        <a
            href="#"
            class="inventory-module-link disabled"
            onclick="return false;"
        >
            <x-icon name="clipboard"></x-icon> Purchase Request
        </a>


        <a
            href="#"
            class="inventory-module-link disabled"
            onclick="return false;"
        >
            <x-icon name="clipboard"></x-icon> Laporan Harian
        </a>


        <a
            href="#"
            class="inventory-module-link disabled"
            onclick="return false;"
        >
            <x-icon name="chart"></x-icon> Rata-rata Pemakaian
        </a>


        <a
            href="{{ route('barang.restock') }}"
            class="
                inventory-module-link
                {{ request()->routeIs('barang.restock') ? 'active' : '' }}
            "
        >
            <x-icon name="refresh"></x-icon> Restock
        </a>

    </div>


    <!-- =================================================
         HEADER
    ================================================== -->

    <div class="bk-header">

        <div class="bk-title">

            <h2>
                Barang Keluar
            </h2>

            <p>
                Kelola pencatatan barang yang digunakan atau dikeluarkan dari stok.
            </p>

        </div>


        <div class="bk-header-actions">

            <a
                href="{{ route('inventory.index') }}"
                class="btn btn-gray"
            >
                <x-icon name="back"></x-icon> Inventory
            </a>


            <a
                href="{{ route('barang-keluar.create') }}"
                class="btn btn-primary"
            >
                + Barang Keluar
            </a>

        </div>

    </div>


    <!-- =================================================
         PRINT TITLE
    ================================================== -->

    <div class="bk-print-title">

        <h1>
            DAFTAR BARANG KELUAR
        </h1>

        <p>
            Maintenance Management System
        </p>

        <p>
            Dicetak:
            {{ now()->format('d/m/Y H:i') }}
        </p>

    </div>


    <!-- =================================================
         SUMMARY
    ================================================== -->

    <div class="bk-summary">

        <div class="bk-summary-card">

            <div class="bk-summary-label">
                Jumlah Transaksi
            </div>

            <div class="bk-summary-value">
                {{ $jumlahTransaksi }}
            </div>

        </div>


        <div class="bk-summary-card">

            <div class="bk-summary-label">
                Total Qty Keluar
            </div>

            <div class="bk-summary-value">

                {{
                    number_format(
                        $totalKeluar,
                        2,
                        ',',
                        '.'
                    )
                }}

            </div>

        </div>


        <div class="bk-summary-card">

            <div class="bk-summary-label">
                Transaksi Bulan Ini
            </div>

            <div class="bk-summary-value">
                {{ $transaksiBulanIni }}
            </div>

        </div>

    </div>


    <!-- =================================================
         FILTER
    ================================================== -->

    <div class="bk-filter">

        <form
            method="GET"
            action="{{ route('barang-keluar.index') }}"
        >

            <div class="bk-filter-grid">


                <div class="bk-field">

                    <label>
                        Search
                    </label>

                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Cari no transaksi, barang atau No WO..."
                    >

                </div>


                <div class="bk-field">

                    <label>
                        Tanggal Dari
                    </label>

                    <input
                        type="date"
                        name="tanggal_dari"
                        value="{{ request('tanggal_dari') }}"
                    >

                </div>


                <div class="bk-field">

                    <label>
                        Tanggal Sampai
                    </label>

                    <input
                        type="date"
                        name="tanggal_sampai"
                        value="{{ request('tanggal_sampai') }}"
                    >

                </div>


                <div class="bk-field">

                    <label>
                        Dipakai Oleh
                    </label>

                    <select name="dipakai_oleh">

                        <option value="">
                            Semua
                        </option>

                        <option
                            value="ME_PREV"
                            @selected(
                                request('dipakai_oleh') === 'ME_PREV'
                            )
                        >
                            ME & PREV MAINT
                        </option>

                        <option
                            value="PREV"
                            @selected(
                                request('dipakai_oleh') === 'PREV'
                            )
                        >
                            PREV MAINT
                        </option>

                        <option
                            value="SIPIL"
                            @selected(
                                request('dipakai_oleh') === 'SIPIL'
                            )
                        >
                            SIPIL
                        </option>

                    </select>

                </div>


                <div class="bk-filter-actions">

                    <button
                        type="submit"
                        class="btn btn-primary"
                    >
                        Cari
                    </button>


                    <a
                        href="{{ route('barang-keluar.index') }}"
                        class="btn btn-gray"
                    >
                        Reset
                    </a>

                </div>


            </div>

        </form>

    </div>


    <!-- =================================================
         TABLE
    ================================================== -->

    <div class="bk-table-card">


        <div class="bk-table-top">


            <div class="bk-table-left">

                <div class="bk-table-title">
                    Daftar Barang Keluar
                </div>

                <div class="bk-table-info">

                    {{
                        $barangKeluars->count()
                    }}

                    data pada halaman ini

                </div>

            </div>


            <!-- =================================================
                 EXPORT
            ================================================== -->

            <div class="bk-export">

                <button
                    type="button"
                    class="bk-export-btn"
                    onclick="exportBarangKeluarExcel()"
                >
                    <x-icon name="chart"></x-icon> Excel
                </button>


                <button
                    type="button"
                    class="bk-export-btn"
                    onclick="printBarangKeluar('pdf')"
                >
                    <x-icon name="file"></x-icon> PDF
                </button>


                <button
                    type="button"
                    class="bk-export-btn"
                    onclick="printBarangKeluar('print')"
                >
                    <x-icon name="print"></x-icon> Print
                </button>

            </div>


        </div>


        <div class="bk-table-wrap">


            <table
                id="barangKeluarTable"
                class="bk-table"
            >


                <thead>

                    <tr>

                        <th>
                            No
                        </th>

                        <th>
                            No Transaksi
                        </th>

                        <th>
                            Tanggal
                        </th>

                        <th>
                            Kode Barang
                        </th>

                        <th>
                            Nama Barang / Spesifikasi
                        </th>

                        <th>
                            Qty
                        </th>

                        <th>
                            Satuan
                        </th>

                        <th>
                            Stok Awal
                        </th>

                        <th>
                            Sisa Stok
                        </th>

                        <th>
                            Area / Line
                        </th>

                        <th>
                            Mesin
                        </th>

                        <th>
                            Dipakai Oleh
                        </th>

                        <th>
                            No WO
                        </th>

                        <th>
                            Keterangan
                        </th>

                        <th>
                            Aksi
                        </th>

                    </tr>

                </thead>


                <tbody>


                @forelse(
                    $barangKeluars
                    as $index
                    => $barangKeluar
                )


                    <tr>


                        <!-- NO -->

                        <td class="bk-number">

                            {{
                                $barangKeluars->firstItem()
                                + $index
                            }}

                        </td>


                        <!-- NO TRANSAKSI -->

                        <td class="bk-transaction">

                            {{ $barangKeluar->no_transaksi }}

                        </td>


                        <!-- TANGGAL -->

                        <td class="bk-date">

                            {{
                                $barangKeluar->tanggal_keluar
                                ?->format('d/m/Y')
                            }}

                        </td>


                        <!-- KODE BARANG -->

                        <td class="bk-transaction">

                            {{
                                $barangKeluar->barang?->kode_barang
                                ?? '-'
                            }}

                        </td>


                        <!-- NAMA BARANG -->

                        <td>

                            {{
                                $barangKeluar->barang?->nama_spesifikasi
                                ?? '-'
                            }}

                        </td>


                        <!-- QTY -->

                        <td class="bk-qty">

                            {{
                                number_format(
                                    $barangKeluar->qty,
                                    2,
                                    ',',
                                    '.'
                                )
                            }}

                        </td>


                        <!-- SATUAN -->

                        <td>

                            {{
                                $barangKeluar->satuan
                                ?? '-'
                            }}

                        </td>


                        <!-- STOK AWAL -->

                        <td class="bk-qty">

                            {{
                                number_format(
                                    $barangKeluar->stok_awal,
                                    2,
                                    ',',
                                    '.'
                                )
                            }}

                        </td>


                        <!-- SISA STOK -->

                        <td class="bk-qty">

                            {{
                                number_format(
                                    $barangKeluar->sisa_stok,
                                    2,
                                    ',',
                                    '.'
                                )
                            }}

                        </td>


                        <!-- AREA / LINE -->

                        <td>

                            @if($barangKeluar->area)

                                {{
                                    $barangKeluar->area->nama_area
                                }}

                            @else

                                -

                            @endif

                        </td>


                        <!-- MESIN -->

                        <td>

                            @if($barangKeluar->machine)

                                {{
                                    $barangKeluar->machine->kode_mesin
                                }}

                                -

                                {{
                                    $barangKeluar->machine->nama_mesin
                                }}

                            @else

                                -

                            @endif

                        </td>


                        <!-- DIPAKAI OLEH -->

                        <td>

                            @switch($barangKeluar->dipakai_oleh)

                                @case('ME_PREV')

                                    ME & PREV MAINT

                                    @break

                                @case('PREV')

                                    PREV MAINT

                                    @break

                                @case('SIPIL')

                                    SIPIL

                                    @break

                                @default

                                    -

                            @endswitch

                        </td>


                        <!-- NO WO -->

                        <td>

                            {{
                                $barangKeluar->no_wo
                                ?: '-'
                            }}

                        </td>


                        <!-- KETERANGAN -->

                        <td>

                            {{
                                $barangKeluar->keterangan
                                ?: '-'
                            }}

                        </td>


                        <!-- AKSI -->

                        <td>

                            <div class="bk-actions">

                                <a
                                    href="{{
                                        route(
                                            'barang-keluar.show',
                                            $barangKeluar
                                        )
                                    }}"
                                    class="act-btn act-view"
                                    title="Lihat Detail Transaksi"
                                    aria-label="Lihat Detail Transaksi"
                                >
                                    <x-icon name="eye" />
                                </a>

                            </div>

                        </td>


                    </tr>


                @empty


                    <tr>

                        <td
                            colspan="15"
                            class="bk-empty"
                        >

                            Belum ada data barang keluar.

                        </td>

                    </tr>


                @endforelse


                </tbody>


            </table>


        </div>


        <!-- =================================================
             PAGINATION
        ================================================== -->

        @if(
            method_exists(
                $barangKeluars,
                'hasPages'
            )
            &&
            $barangKeluars->hasPages()
        )

            <div class="bk-pagination">

                {{
                    $barangKeluars->links()
                }}

            </div>

        @endif


    </div>


</div>

@endsection


@push('scripts')

<script>

/* =====================================================
   EXCEL / CSV
===================================================== */

function exportBarangKeluarExcel()
{

    const table =
        document.getElementById(
            'barangKeluarTable'
        );

    if (!table) {
        return;
    }


    const rows =
        Array.from(
            table.querySelectorAll('tr')
        );


    /*
     * Status sudah DIHAPUS.
     * Kolom export sekarang hanya sampai Keterangan.
     */

    let csv =
        'No,No Transaksi,Tanggal,Kode Barang,Nama Barang / Spesifikasi,Qty,Satuan,Stok Awal,Sisa Stok,Area / Line,Mesin,Dipakai Oleh,No WO,Keterangan\n';


    rows.forEach(
        function(row, rowIndex)
        {

            const cells =
                Array.from(
                    row.querySelectorAll(
                        'th,td'
                    )
                );


            /*
             * Lewati header.
             */

            if (
                rowIndex === 0
            ) {
                return;
            }


            /*
             * Minimal 15 kolom.
             */

            if (
                cells.length < 15
            ) {
                return;
            }


            /*
             * Ambil 14 kolom data.
             *
             * Kolom ke-15 adalah Aksi,
             * sehingga tidak ikut export.
             */

            const values =
                cells
                    .slice(
                        0,
                        14
                    )
                    .map(
                        function(cell)
                        {

                            return '"' +
                                cell.innerText
                                    .trim()
                                    .replace(
                                        /"/g,
                                        '""'
                                    ) +
                                '"';

                        }
                    );


            csv +=
                values.join(',') +
                '\n';

        }
    );


    const blob =
        new Blob(
            [
                '\ufeff' +
                csv
            ],
            {
                type:
                    'text/csv;charset=utf-8;'
            }
        );


    const url =
        URL.createObjectURL(
            blob
        );


    const link =
        document.createElement(
            'a'
        );


    link.href =
        url;


    link.download =
        'barang-keluar-' +
        new Date()
            .toISOString()
            .slice(
                0,
                10
            ) +
        '.csv';


    document.body.appendChild(
        link
    );


    link.click();


    link.remove();


    URL.revokeObjectURL(
        url
    );

}


/* =====================================================
   PRINT / PDF
===================================================== */

function printBarangKeluar(mode)
{

    /*
     * Browser akan membuka dialog print.
     *
     * Untuk PDF:
     * pilih "Save as PDF".
     */

    document.title =
        'Laporan Barang Keluar';


    setTimeout(
        function()
        {

            window.print();

        },
        100
    );

}


/* =====================================================
   AFTER PRINT
===================================================== */

window.addEventListener(
    'afterprint',
    function()
    {

        document.title =
            'Barang Keluar';

    }
);

</script>

@endpush