@extends('layouts.app')

@section('title', 'Laporan Work Order Maintenance')

@section('page_title', 'Laporan Work Order Maintenance')

@section(
    'page_subtitle',
    'Laporan seluruh Work Order yang ditangani Maintenance'
)

@push('styles')
<style>
    .mt-report-page {
        padding: 20px 0 30px;
    }

    .mt-report-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 20px;
        margin-bottom: 20px;
    }

    .mt-report-header h1 {
        margin: 0 0 5px;
        font-size: 26px;
        color: var(--pds-ink);
    }

    .mt-report-header p {
        margin: 0;
        color: var(--pds-muted);
        font-size: 13px;
        line-height: 1.5;
    }

    .mt-report-header-right {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .mt-report-btn {
        height: 36px;
        padding: 0 13px;
        border: none;
        border-radius: 7px;
        font-size: 11px;
        font-weight: 700;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        white-space: nowrap;
    }

    .mt-report-btn-back {
        background: var(--pds-soft-2);
        color: var(--pds-ink-2);
    }

    .mt-report-btn-back:hover {
        background: var(--pds-line);
    }

    .mt-report-btn-pdf {
        background: #dc2626;
        color: #ffffff;
    }

    .mt-report-btn-pdf:hover {
        background: #b91c1c;
    }

    .mt-report-card {
        background: var(--pds-card);
        border: 1px solid var(--pds-line);
        border-radius: 12px;
        box-shadow: 0 3px 12px rgba(0, 0, 0, .05);
        overflow: hidden;
    }

    .mt-report-card-header {
        padding: 17px 20px;
        border-bottom: 1px solid var(--pds-line);
    }

    .mt-report-title {
        font-size: 16px;
        font-weight: 700;
        color: var(--pds-ink);
    }

    .mt-report-subtitle {
        margin-top: 4px;
        color: var(--pds-muted);
        font-size: 12px;
        line-height: 1.5;
    }

    /* ================= FILTER ================= */

    .mt-report-filter {
        padding: 15px 20px;
        border-bottom: 1px solid var(--pds-line);
        background: var(--pds-card);
    }

    .mt-report-filter-title {
        margin-bottom: 10px;
        font-size: 13px;
        font-weight: 700;
        color: var(--pds-ink);
    }

    .mt-report-filter-grid {
        display: grid;
        grid-template-columns:
            minmax(180px, 1.35fr)
            minmax(140px, 1fr)
            minmax(180px, 1.15fr)
            minmax(130px, .9fr)
            minmax(120px, .8fr)
            minmax(120px, .8fr)
            auto;
        gap: 9px;
        align-items: end;
    }

    .mt-report-filter-item {
        min-width: 0;
    }

    .mt-report-filter-label {
        display: block;
        margin-bottom: 5px;
        color: var(--pds-muted);
        font-size: 10px;
        font-weight: 700;
    }

    .mt-report-filter-input,
    .mt-report-filter-select {
        width: 100%;
        height: 37px;
        padding: 0 9px;
        border: 1px solid var(--pds-line-2);
        border-radius: 7px;
        background: var(--pds-card);
        color: var(--pds-ink);
        font-size: 11px;
        outline: none;
        box-sizing: border-box;
    }

    .mt-report-filter-input:focus,
    .mt-report-filter-select:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 2px rgba(37, 99, 235, .08);
    }

    .mt-report-filter-buttons {
        display: flex;
        gap: 6px;
    }

    .mt-report-filter-btn {
        height: 37px;
        padding: 0 11px;
        border: none;
        border-radius: 7px;
        font-size: 11px;
        font-weight: 700;
        cursor: pointer;
        white-space: nowrap;
    }

    .mt-report-filter-search {
        background: #2563eb;
        color: #ffffff;
    }

    .mt-report-filter-search:hover {
        background: #1d4ed8;
    }

    .mt-report-filter-reset {
        background: var(--pds-soft-2);
        color: var(--pds-ink-2);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .mt-report-filter-reset:hover {
        background: var(--pds-line);
    }

    .mt-report-result {
        margin-top: 8px;
        color: var(--pds-muted);
        font-size: 10px;
    }

    /* ================= INFO ================= */

    .mt-report-info {
        margin: 18px 20px;
        padding: 12px 14px;
        border: 1px solid #bfdbfe;
        border-radius: 8px;
        background: #eff6ff;
        color: #1e40af;
        font-size: 12px;
        line-height: 1.6;
    }

    /* ================= TABLE ================= */

    .mt-report-table-wrap {
        width: 100%;
        overflow-x: auto;
    }

    .mt-report-table {
        width: 100%;
        min-width: 2800px;
        border-collapse: collapse;
    }

    .mt-report-table th,
    .mt-report-table td {
        padding: 11px 12px;
        border-bottom: 1px solid var(--pds-line);
        font-size: 12px;
        vertical-align: middle;
    }

    .mt-report-table th {
        background: var(--pds-soft);
        color: var(--pds-ink-2);
        font-weight: 700;
        text-align: center;
        white-space: nowrap;
    }

    .mt-report-table td {
        color: var(--pds-ink-2);
    }

    .mt-report-table tbody tr:hover {
        background: var(--pds-soft);
    }

    .mt-report-no {
        width: 55px;
        min-width: 55px;
        text-align: center !important;
        font-weight: 700;
        color: var(--pds-muted) !important;
    }

    .mt-report-wo {
        min-width: 130px;
        color: var(--pds-ink) !important;
        font-weight: 700;
        white-space: nowrap;
    }

    .mt-report-date {
        min-width: 120px;
        white-space: nowrap;
        text-align: center;
    }

    .mt-report-time {
        min-width: 95px;
        white-space: nowrap;
        text-align: center;
    }

    .mt-report-user {
        min-width: 135px;
        font-weight: 600;
        color: var(--pds-ink) !important;
    }

    .mt-report-wide {
        min-width: 170px;
    }

    .mt-report-description {
        min-width: 220px;
        max-width: 320px;
        line-height: 1.5;
    }

    /* ================= STATUS ================= */

    .mt-report-status,
    .mt-report-priority {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 80px;
        padding: 5px 9px;
        border-radius: 999px;
        font-size: 10px;
        font-weight: 700;
        white-space: nowrap;
    }

    .mt-report-status-open {
        background: #dbeafe;
        color: #1d4ed8;
    }

    .mt-report-status-diterima {
        background: #e0f2fe;
        color: #0369a1;
    }

    .mt-report-status-scheduled {
        background: #dbeafe;
        color: #1d4ed8;
    }

    .mt-report-status-progress {
        background: #fef3c7;
        color: #92400e;
    }

    .mt-report-status-pending {
        background: var(--pds-soft-2);
        color: var(--pds-muted);
    }

    .mt-report-status-ditolak {
        background: #fee2e2;
        color: #b91c1c;
    }

    .mt-report-status-service {
        background: #ede9fe;
        color: #6d28d9;
    }

    .mt-report-status-close {
        background: #dcfce7;
        color: #166534;
    }

    .mt-report-priority-normal {
        background: #dcfce7;
        color: #166534;
    }

    .mt-report-priority-urgent {
        background: #fef3c7;
        color: #92400e;
    }

    .mt-report-priority-emergency {
        background: #fee2e2;
        color: #b91c1c;
    }

    /* ================= SPAREPART ================= */

    .mt-report-sparepart {
        min-width: 200px;
        line-height: 1.5;
    }

    .mt-report-sparepart-item {
        margin-bottom: 4px;
    }

    .mt-report-sparepart-item:last-child {
        margin-bottom: 0;
    }

    /* ================= ACTION ================= */

    .mt-report-action {
        min-width: 140px;
        text-align: center;
        white-space: nowrap;
        vertical-align: middle;
    }

    .mt-icon-actions {
        display: inline-flex;
        gap: 7px;
        align-items: center;
        justify-content: center;
    }

    .mt-icon-actions form {
        display: inline-flex;
        margin: 0;
        padding: 0;
        line-height: 0;
    }

    .mt-icon-btn {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1.4px solid var(--pds-line);
        background: var(--pds-card);
        cursor: pointer;
        color: #2563eb;
        text-decoration: none;
        transition: all .13s ease;
        padding: 0;
        flex-shrink: 0;
    }

    .mt-icon-btn:hover {
        background: rgba(37, 99, 235, .08);
        border-color: #2563eb;
        transform: translateY(-1px);
    }

    .mt-icon-btn svg {
        width: 18px;
        height: 18px;
        display: block;
        stroke-width: 2.2;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .mt-icon-btn.mt-icon-eye {
        color: #0891b2;
    }

    .mt-icon-btn.mt-icon-eye:hover {
        background: rgba(8, 145, 178, .09);
        border-color: #0891b2;
    }

    .mt-icon-btn.mt-icon-edit {
        color: #d97706;
    }

    .mt-icon-btn.mt-icon-edit:hover {
        background: rgba(217, 119, 6, .10);
        border-color: #d97706;
    }

    .mt-icon-btn.mt-icon-del {
        color: #dc2626;
    }

    .mt-icon-btn.mt-icon-del:hover {
        background: rgba(220, 38, 38, .09);
        border-color: #dc2626;
    }

    .mt-report-empty {
        padding: 45px 20px !important;
        text-align: center !important;
        color: var(--pds-muted-2) !important;
    }

    /* ================= OVERLAY FORM WO (FULLSCREEN) ================= */

    .wo-overlay {
        position: fixed;
        inset: 0;
        z-index: 90;
        background: rgba(15, 23, 42, .62);
        backdrop-filter: blur(4px);
        display: none;
        padding: 0;
        overflow: hidden;
    }

    .wo-overlay.open {
        display: block;
    }

    /* Saat form terbuka, halaman di belakang dikunci agar
       panel benar-benar memenuhi layar tanpa scrollbar */
    body:has(.wo-overlay.open) {
        overflow: hidden;
    }

    .wo-overlay-panel {
        position: relative;
        width: 100%;
        max-width: none;
        height: 100vh;
        height: 100dvh;
        margin: 0;
        background: var(--pds-card);
        border-radius: 0;
        box-shadow: none;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .wo-overlay-close {
        position: absolute;
        top: 12px;
        right: 16px;
        z-index: 5;
        width: 38px;
        height: 38px;
        border-radius: 50%;
        border: none;
        background: #ef4444;
        color: #ffffff;
        font-size: 16px;
        cursor: pointer;
        box-shadow: 0 4px 12px rgba(239, 68, 68, .45);
    }

    .wo-overlay-close:hover {
        background: #dc2626;
    }

    /* Panel memenuhi layar: judul & tombol tetap terlihat,
       area isi form yang discroll */
    .wo-overlay-panel .wo-create-page {
        display: flex;
        flex-direction: column;
        width: 100%;
        max-width: none;
        min-height: 0;
        padding: 0;
        margin: 0;
    }

    .wo-overlay-panel .wo-create-header {
        flex-shrink: 0;
        margin: 0;
        padding: 16px 20px 10px;
    }

    .wo-overlay-panel .wo-create-card {
        width: 100%;
        max-width: none;
        flex: 1 1 auto;
        min-height: 0;
        height: auto;
        border-radius: 0;
        box-shadow: none;
        border-top: 1px solid var(--pds-line);
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .wo-overlay-panel .wo-create-card-header,
    .wo-overlay-panel .wo-create-footer {
        flex-shrink: 0;
    }

    /* <form> tanpa kelas di antara card & body harus ikut flex
       agar area isi bisa menyusut dan footer menempel di bawah */
    .wo-overlay-panel .wo-create-card > form {
        display: flex;
        flex-direction: column;
        flex: 1 1 auto;
        min-height: 0;
    }

    .wo-overlay-panel .wo-create-body {
        flex: 1 1 auto;
        min-height: 0;
        max-height: none;
        overflow-y: auto;
    }

    /* ================= PRINT ================= */

    .mt-report-print-header {
        display: none;
    }

    @media (max-width: 1400px) {
        .mt-report-filter-grid {
            grid-template-columns: repeat(4, minmax(140px, 1fr));
        }
    }

    @media (max-width: 700px) {
        .mt-report-page {
            padding: 12px;
        }

        .mt-report-header {
            flex-direction: column;
        }

        .mt-report-filter-grid {
            grid-template-columns: 1fr;
        }

        .mt-report-filter-buttons {
            width: 100%;
        }

        .mt-report-filter-btn {
            flex: 1;
        }
    }

    @media print {
        @page {
            size: landscape;
            margin: 7mm;
        }

        body {
            background: #ffffff !important;
        }

        body * {
            visibility: hidden !important;
        }

        .mt-report-page,
        .mt-report-page * {
            visibility: visible !important;
        }

        .mt-report-page {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            padding: 0 !important;
        }

        .mt-report-header,
        .mt-report-filter,
        .mt-report-info,
        .mt-report-action {
            display: none !important;
        }

        .mt-report-card {
            border: none !important;
            box-shadow: none !important;
        }

        .mt-report-card-header {
            display: block !important;
            border: none !important;
            padding: 0 0 10px !important;
        }

        .mt-report-print-header {
            display: block;
            text-align: center;
            margin-bottom: 8px;
        }

        .mt-report-print-header h1 {
            margin: 0;
            font-size: 18px;
        }

        .mt-report-print-header p {
            margin: 3px 0 0;
            font-size: 9px;
        }

        .mt-report-table-wrap {
            overflow: visible !important;
        }

        .mt-report-table {
            width: 100% !important;
            min-width: 0 !important;
            font-size: 7px !important;
        }

        .mt-report-table th,
        .mt-report-table td {
            border: 1px solid #999 !important;
            padding: 3px 4px !important;
            color: #111827 !important;
            background: #ffffff !important;
            font-size: 7px !important;
        }

        .mt-report-table th {
            background: #eeeeee !important;
            position: static !important;
        }

        .mt-report-status,
        .mt-report-priority {
            display: inline !important;
            min-width: 0 !important;
            padding: 0 !important;
            background: transparent !important;
            color: #111827 !important;
            border-radius: 0 !important;
        }
    }
</style>
@endpush


@section('content')

<div class="mt-report-page">

    {{-- ================= HEADER ================= --}}

    <div class="mt-report-header">
        <div>
            <h1>Laporan Work Order Maintenance</h1>

            <p>
                Seluruh Work Order yang tersimpan untuk kebutuhan
                monitoring dan laporan Maintenance.
            </p>
        </div>

        <div class="mt-report-header-right">

            <a
                href="{{ route('work-orders.admin.index') }}"
                class="mt-report-btn mt-report-btn-back"
            >
                Kembali
            </a>

            <button
                type="button"
                id="btnMaintenanceReportPdf"
                class="mt-report-btn mt-report-btn-pdf"
            >
                PDF
            </button>

        </div>
    </div>


    {{-- ================= PRINT HEADER ================= --}}

    <div class="mt-report-print-header">

        <h1>
            LAPORAN WORK ORDER MAINTENANCE
        </h1>

        <p>
            Dicetak:
            {{ now()->format('d-m-Y H:i') }}
        </p>

    </div>


    <div class="mt-report-card">

        {{-- ================= CARD HEADER ================= --}}

        <div class="mt-report-card-header">

            <div class="mt-report-title">
                Laporan Work Order
            </div>

            <div class="mt-report-subtitle">
                Data diambil langsung dari Work Order yang tersimpan.
            </div>

        </div>


        {{-- ================= FILTER ================= --}}

        <div class="mt-report-filter">

            <div class="mt-report-filter-title">
                Pencarian / Filter Work Order
            </div>

            <form
                method="GET"
                action="{{ route('work-orders.admin.report') }}"
            >

                <div class="mt-report-filter-grid">

                    {{-- NO WO --}}
                    <div class="mt-report-filter-item">

                        <label
                            class="mt-report-filter-label"
                            for="reportNoWo"
                        >
                            No. WO
                        </label>

                        <input
                            type="text"
                            id="reportNoWo"
                            name="no_wo"
                            class="mt-report-filter-input"
                            value="{{ $searchNoWo ?? '' }}"
                            placeholder="Cari No. WO..."
                            autocomplete="off"
                        >

                    </div>


                    {{-- DEPARTEMEN --}}
                    <div class="mt-report-filter-item">

                        <label
                            class="mt-report-filter-label"
                            for="reportDepartemen"
                        >
                            Departemen
                        </label>

                        <select
                            id="reportDepartemen"
                            name="departemen"
                            class="mt-report-filter-select"
                        >

                            <option value="">
                                Semua Departemen
                            </option>

                            @foreach($departemenOptions ?? [] as $option)

                                <option
                                    value="{{ $option }}"
                                    {{ ($departemen ?? '') == $option ? 'selected' : '' }}
                                >
                                    {{ $option }}
                                </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- KATEGORI --}}
                    <div class="mt-report-filter-item">

                        <label
                            class="mt-report-filter-label"
                            for="reportKategori"
                        >
                            Kategori
                        </label>

                        <select
                            id="reportKategori"
                            name="kategori"
                            class="mt-report-filter-select"
                        >

                            <option value="">
                                Semua Kategori
                            </option>

                            @foreach($kategoriOptions ?? [] as $option)

                                <option
                                    value="{{ $option }}"
                                    {{ ($kategori ?? '') == $option ? 'selected' : '' }}
                                >
                                    {{ $option }}
                                </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- STATUS --}}
                    <div class="mt-report-filter-item">

                        <label
                            class="mt-report-filter-label"
                            for="reportStatus"
                        >
                            Status
                        </label>

                        <select
                            id="reportStatus"
                            name="status"
                            class="mt-report-filter-select"
                        >

                            <option value="">
                                Semua Status
                            </option>

                            @foreach($statusOptions ?? [] as $option)

                                <option
                                    value="{{ $option }}"
                                    {{ ($statusFilter ?? '') == $option ? 'selected' : '' }}
                                >
                                    {{ $option }}
                                </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- BULAN --}}
                    <div class="mt-report-filter-item">

                        <label
                            class="mt-report-filter-label"
                            for="reportBulan"
                        >
                            Bulan
                        </label>

                        <select
                            id="reportBulan"
                            name="bulan"
                            class="mt-report-filter-select"
                        >

                            <option value="">
                                Semua Bulan
                            </option>

                            @for($i = 1; $i <= 12; $i++)

                                <option
                                    value="{{ $i }}"
                                    {{ (string) ($bulan ?? '') === (string) $i ? 'selected' : '' }}
                                >
                                    {{ \Carbon\Carbon::create()->month($i)->locale('id')->translatedFormat('F') }}
                                </option>

                            @endfor

                        </select>

                    </div>


                    {{-- TAHUN --}}
                    <div class="mt-report-filter-item">

                        <label
                            class="mt-report-filter-label"
                            for="reportTahun"
                        >
                            Tahun
                        </label>

                        <select
                            id="reportTahun"
                            name="tahun"
                            class="mt-report-filter-select"
                        >

                            <option value="">
                                Semua Tahun
                            </option>

                            @foreach($tahunOptions ?? [] as $option)

                                <option
                                    value="{{ $option }}"
                                    {{ (string) ($tahun ?? '') === (string) $option ? 'selected' : '' }}
                                >
                                    {{ $option }}
                                </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- BUTTON --}}
                    <div class="mt-report-filter-item">

                        <label class="mt-report-filter-label">
                            &nbsp;
                        </label>

                        <div class="mt-report-filter-buttons">

                            <button
                                type="button"
                                id="btnOpenWoForm"
                                class="mt-report-filter-btn mt-report-filter-search"
                            >
                                + Buat WO
                            </button>

                            <button
                                type="submit"
                                class="mt-report-filter-btn mt-report-filter-search"
                            >
                                Cari
                            </button>

                            <a
                                href="{{ route('work-orders.admin.report') }}"
                                class="mt-report-filter-btn mt-report-filter-reset"
                            >
                                Reset
                            </a>

                        </div>

                    </div>

                </div>

            </form>


            <div
                class="mt-report-result"
                id="mtReportResult"
            >
                Menampilkan

                <strong>
                    {{ count($workOrders ?? []) }}
                </strong>

                Work Order.
            </div>

        </div>


        {{-- ================= INFO ================= --}}

        <div class="mt-report-info">

            <strong>Laporan Work Order:</strong>

            data pada halaman ini merupakan data Work Order yang sama
            dengan Daftar Work Order Maintenance. Filter dapat digunakan
            untuk membuat laporan berdasarkan No. WO, Departemen,
            Kategori, Status, Bulan, dan Tahun.

        </div>


        {{-- ================= TABLE ================= --}}

        <div class="mt-report-table-wrap">

            <table
                class="mt-report-table"
                id="maintenanceReportTable"
            >

                <thead>

                    <tr>

                        <th class="mt-report-no">
                            No
                        </th>

                        <th class="mt-report-wo">
                            No. WO
                        </th>

                        <th class="mt-report-date">
                            Tanggal Kerusakan
                        </th>

                        <th class="mt-report-time">
                            Jam Kerusakan
                        </th>

                        <th class="mt-report-user">
                            Dibuat Oleh
                        </th>

                        <th class="mt-report-wide">
                            Departemen
                        </th>

                        <th class="mt-report-wide">
                            Kategori
                        </th>

                        <th class="mt-report-wide">
                            Ditujukan
                        </th>

                        <th class="mt-report-wide">
                            Area / Line
                        </th>

                        <th class="mt-report-wide">
                            Mesin
                        </th>

                        <th class="mt-report-wide">
                            Job
                        </th>

                        <th class="mt-report-description">
                            Deskripsi
                        </th>

                        <th class="mt-report-wide">
                            Prioritas
                        </th>

                        <th class="mt-report-wide">
                            Status
                        </th>

                        <th class="mt-report-description">
                            Laporan Diterima
                        </th>

                        <th class="mt-report-description">
                            Perencanaan / Solusi
                        </th>

                        <th class="mt-report-date">
                            Jadwal
                        </th>

                        <th class="mt-report-date">
                            Tanggal Mulai
                        </th>

                        <th class="mt-report-time">
                            Jam Mulai
                        </th>

                        <th class="mt-report-date">
                            Tanggal Selesai
                        </th>

                        <th class="mt-report-time">
                            Jam Selesai
                        </th>

                        <th class="mt-report-wide">
                            Teknisi
                        </th>

                        <th class="mt-report-description">
                            Keterangan
                        </th>

                        <th class="mt-report-sparepart">
                            Sparepart
                        </th>

                        <th class="mt-report-action">
                            Aksi
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @forelse($workOrders ?? [] as $index => $workOrder)

                        @php

                            /*
                            |--------------------------------------------------------------------------
                            | STATUS
                            |--------------------------------------------------------------------------
                            */

                            $reportStatus = strtoupper(
                                trim(
                                    (string) (
                                        $workOrder->status ?? 'OPEN'
                                    )
                                )
                            );


                            /*
                            |--------------------------------------------------------------------------
                            | PRIORITAS
                            |--------------------------------------------------------------------------
                            */

                            $reportPriority = strtoupper(
                                trim(
                                    (string) (
                                        $workOrder->priority ?? ''
                                    )
                                )
                            );


                            /*
                            |--------------------------------------------------------------------------
                            | SPAREPART
                            |--------------------------------------------------------------------------
                            */

                            $spareparts = [];

                            $sparepartColumns = [
                                'spareparts',
                                'sparepart',
                                'sparepart_data',
                                'used_spareparts',
                                'sparepart_used',
                            ];

                            foreach ($sparepartColumns as $column) {

                                $value = $workOrder->{$column} ?? null;

                                if (!empty($value)) {

                                    if (is_array($value)) {

                                        $spareparts = $value;

                                    } else {

                                        $decoded = json_decode(
                                            $value,
                                            true
                                        );

                                        if (is_array($decoded)) {
                                            $spareparts = $decoded;
                                        }
                                    }

                                    if (!empty($spareparts)) {
                                        break;
                                    }
                                }
                            }


                            /*
                            |--------------------------------------------------------------------------
                            | TANGGAL KERUSAKAN
                            |--------------------------------------------------------------------------
                            */

                            $tanggalKerusakan = '-';

                            if (!empty($workOrder->tanggal_kerusakan)) {

                                try {

                                    $tanggalKerusakan =
                                        \Carbon\Carbon::parse(
                                            $workOrder->tanggal_kerusakan
                                        )->format('d-m-Y');

                                } catch (\Throwable $e) {

                                    $tanggalKerusakan =
                                        $workOrder->tanggal_kerusakan;
                                }
                            }


                            /*
                            |--------------------------------------------------------------------------
                            | JADWAL
                            |--------------------------------------------------------------------------
                            */

                            $jadwalPerbaikan = '-';

                            if (!empty($workOrder->jadwal_perbaikan)) {

                                try {

                                    $jadwalPerbaikan =
                                        \Carbon\Carbon::parse(
                                            $workOrder->jadwal_perbaikan
                                        )->format('d-m-Y');

                                } catch (\Throwable $e) {

                                    $jadwalPerbaikan =
                                        $workOrder->jadwal_perbaikan;
                                }
                            }


                            /*
                            |--------------------------------------------------------------------------
                            | TANGGAL MULAI
                            |--------------------------------------------------------------------------
                            */

                            $tanggalMulai = '-';

                            if (!empty($workOrder->tanggal_mulai_perbaikan)) {

                                try {

                                    $tanggalMulai =
                                        \Carbon\Carbon::parse(
                                            $workOrder->tanggal_mulai_perbaikan
                                        )->format('d-m-Y');

                                } catch (\Throwable $e) {

                                    $tanggalMulai =
                                        $workOrder->tanggal_mulai_perbaikan;
                                }
                            }


                            /*
                            |--------------------------------------------------------------------------
                            | TANGGAL SELESAI
                            |--------------------------------------------------------------------------
                            */

                            $tanggalSelesai = '-';

                            if (!empty($workOrder->tanggal_selesai_perbaikan)) {

                                try {

                                    $tanggalSelesai =
                                        \Carbon\Carbon::parse(
                                            $workOrder->tanggal_selesai_perbaikan
                                        )->format('d-m-Y');

                                } catch (\Throwable $e) {

                                    $tanggalSelesai =
                                        $workOrder->tanggal_selesai_perbaikan;
                                }
                            }

                        @endphp


                        <tr data-work-order-id="{{ $workOrder->id }}">

                            <td class="mt-report-no">
                                {{ $index + 1 }}
                            </td>

                            <td class="mt-report-wo">
                                {{ $workOrder->no_wo ?: '-' }}
                            </td>

                            <td class="mt-report-date">
                                {{ $tanggalKerusakan }}
                            </td>

                            <td class="mt-report-time">
                                {{ $workOrder->jam_kerusakan ?: '-' }}
                            </td>

                            <td class="mt-report-user">
                                {{ $workOrder->dibuat_oleh ?: '-' }}
                            </td>

                            <td class="mt-report-wide">
                                {{ $workOrder->departemen ?: '-' }}
                            </td>

                            <td class="mt-report-wide">
                                {{ $workOrder->kategori ?: '-' }}
                            </td>

                            <td class="mt-report-wide">
                                {{ $workOrder->tujuan ?: '-' }}
                            </td>

                            <td class="mt-report-wide">
                                {{ $workOrder->area ?: '-' }}
                            </td>

                            <td class="mt-report-wide">
                                {{ $workOrder->mesin ?: '-' }}
                            </td>

                            <td class="mt-report-wide">
                                {{ $workOrder->job ?: '-' }}
                            </td>

                            <td class="mt-report-description">
                                {{ $workOrder->deskripsi ?: '-' }}
                            </td>


                            {{-- PRIORITAS --}}

                            <td class="mt-report-wide">

                                @if($reportPriority === 'EMERGENCY')

                                    <span class="mt-report-priority mt-report-priority-emergency">
                                        EMERGENCY
                                    </span>

                                @elseif($reportPriority === 'URGENT')

                                    <span class="mt-report-priority mt-report-priority-urgent">
                                        URGENT
                                    </span>

                                @elseif($reportPriority === 'NORMAL')

                                    <span class="mt-report-priority mt-report-priority-normal">
                                        NORMAL
                                    </span>

                                @elseif($reportPriority !== '')

                                    <span class="mt-report-priority mt-report-priority-normal">
                                        {{ $reportPriority }}
                                    </span>

                                @else

                                    -

                                @endif

                            </td>


                            {{-- STATUS --}}

                            <td class="mt-report-wide">

                                @if($reportStatus === 'OPEN')

                                    <span class="mt-report-status mt-report-status-open">
                                        OPEN
                                    </span>

                                @elseif($reportStatus === 'DITERIMA')

                                    <span class="mt-report-status mt-report-status-diterima">
                                        DITERIMA
                                    </span>

                                @elseif($reportStatus === 'SCHEDULED')

                                    <span class="mt-report-status mt-report-status-scheduled">
                                        SCHEDULED
                                    </span>

                                @elseif(
                                    $reportStatus === 'IN PROGRESS' ||
                                    $reportStatus === 'IN PROSES'
                                )

                                    <span class="mt-report-status mt-report-status-progress">
                                        IN PROGRESS
                                    </span>

                                @elseif($reportStatus === 'PENDING')

                                    <span class="mt-report-status mt-report-status-pending">
                                        PENDING
                                    </span>

                                @elseif($reportStatus === 'DITOLAK')

                                    <span class="mt-report-status mt-report-status-ditolak">
                                        DITOLAK
                                    </span>

                                @elseif($reportStatus === 'SERVICE LUAR')

                                    <span class="mt-report-status mt-report-status-service">
                                        SERVICE LUAR
                                    </span>

                                @elseif(
                                    $reportStatus === 'CLOSE' ||
                                    $reportStatus === 'SELESAI'
                                )

                                    <span class="mt-report-status mt-report-status-close">
                                        CLOSE
                                    </span>

                                @else

                                    <span class="mt-report-status mt-report-status-pending">
                                        {{ $workOrder->status ?: '-' }}
                                    </span>

                                @endif

                            </td>


                            <td class="mt-report-description">
                                {{ $workOrder->laporan_diterima ?: '-' }}
                            </td>

                            <td class="mt-report-description">
                                {{ $workOrder->perencanaan_pekerjaan ?: '-' }}
                            </td>

                            <td class="mt-report-date">
                                {{ $jadwalPerbaikan }}
                            </td>

                            <td class="mt-report-date">
                                {{ $tanggalMulai }}
                            </td>

                            <td class="mt-report-time">
                                {{ $workOrder->jam_mulai_perbaikan ?: '-' }}
                            </td>

                            <td class="mt-report-date">
                                {{ $tanggalSelesai }}
                            </td>

                            <td class="mt-report-time">
                                {{ $workOrder->jam_selesai_perbaikan ?: '-' }}
                            </td>

                            <td class="mt-report-wide">
                                {{ $workOrder->teknisi ?: '-' }}
                            </td>

                            <td class="mt-report-description">
                                {{ $workOrder->keterangan ?: '-' }}
                            </td>


                            {{-- SPAREPART --}}

                            <td class="mt-report-sparepart">

                                @if(count($spareparts) > 0)

                                    @foreach($spareparts as $sparepart)

                                        <div class="mt-report-sparepart-item">

                                            {{ $sparepart['nama_barang'] ?? '-' }}

                                            ×

                                            {{ $sparepart['qty'] ?? 0 }}

                                        </div>

                                    @endforeach

                                @else

                                    -

                                @endif

                            </td>


                            {{-- AKSI --}}

                            <td class="mt-report-action">

                                <div class="mt-icon-actions">

                                    @php
                                        $_authUser = auth()->user();
                                        $_canEdit = \App\Support\DepartmentAccess::canEditWorkOrder($_authUser, $workOrder);
                                        $_canDelete = \App\Support\DepartmentAccess::canDeleteWorkOrder($_authUser, $workOrder);
                                    @endphp

                                    {{-- LIHAT --}}

                                    <a
                                        href="{{ route(
                                            'work-orders.admin.show',
                                            $workOrder->id
                                        ) }}"
                                        class="mt-icon-btn mt-icon-eye"
                                        title="Lihat Semua Detail"
                                    >

                                        <svg
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                        >
                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                            <circle cx="12" cy="12" r="3"/>
                                        </svg>

                                    </a>


                                    {{-- EDIT --}}

                                    @if($_canEdit)
                                    <a
                                        href="{{ route(
                                            'work-orders.admin.edit',
                                            $workOrder->id
                                        ) }}"
                                        class="mt-icon-btn mt-icon-edit"
                                        title="Edit Work Order"
                                    >

                                        <svg
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                        >
                                            <path d="M17 3a2.83 2.83 0 0 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"/>
                                        </svg>

                                    </a>
                                    @endif


                                    {{-- DELETE --}}

                                    @if($_canDelete)
                                    <form
                                        action="{{ route(
                                            'work-orders.admin.destroy',
                                            $workOrder->id
                                        ) }}"
                                        method="POST"
                                        data-confirm="Hapus Work Order {{ $workOrder->no_wo }}? Tindakan ini tidak dapat dibatalkan."
                                    >

                                        @csrf

                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="mt-icon-btn mt-icon-del"
                                            title="Hapus Work Order"
                                        >

                                            <svg
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                stroke="currentColor"
                                            >
                                                <polyline points="3 6 5 6 21 6"/>
                                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/>
                                                <path d="M9 6V4a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2"/>
                                                <line x1="10" y1="11" x2="10" y2="17"/>
                                                <line x1="14" y1="11" x2="14" y2="17"/>
                                            </svg>

                                        </button>

                                    </form>
                                    @endif

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="25"
                                class="mt-report-empty"
                            >
                                Belum ada Work Order dalam database.
                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>


{{-- ================= OVERLAY FORM BUAT WO ================= --}}

<div
    class="wo-overlay"
    id="woOverlay"
>

    <div class="wo-overlay-panel">

        <button
            type="button"
            id="btnCloseWoForm"
            class="wo-overlay-close"
            title="Tutup Form"
        >
            &#10005;
        </button>

        @include('work-orders.admin._form')

    </div>

</div>


@push('scripts')
<script>
(function () {

    'use strict';

    const woOverlay = document.getElementById('woOverlay');
    const btnOpenWo = document.getElementById('btnOpenWoForm');
    const btnCloseWo = document.getElementById('btnCloseWoForm');


    function openWoOverlay() {

        if (!woOverlay) {
            return;
        }

        woOverlay.classList.add('open');

        document.body.style.overflow = 'hidden';
    }


    function closeWoOverlay() {

        if (!woOverlay) {
            return;
        }

        woOverlay.classList.remove('open');

        document.body.style.overflow = '';
    }


    if (btnOpenWo) {

        btnOpenWo.addEventListener(
            'click',
            openWoOverlay
        );

    }


    if (btnCloseWo) {

        btnCloseWo.addEventListener(
            'click',
            closeWoOverlay
        );

    }


    if (woOverlay) {

        woOverlay.addEventListener(
            'click',
            function (event) {

                if (event.target === woOverlay) {

                    closeWoOverlay();

                }

            }
        );

    }


    document.addEventListener(
        'keydown',
        function (event) {

            if (event.key === 'Escape') {

                closeWoOverlay();

            }

        }
    );


    @if($errors->any())

        window.addEventListener(
            'DOMContentLoaded',
            openWoOverlay
        );

    @endif

})();
</script>


<script>
document.addEventListener(
    'DOMContentLoaded',
    function () {

        const pdfButton =
            document.getElementById(
                'btnMaintenanceReportPdf'
            );


        if (!pdfButton) {
            return;
        }


        pdfButton.addEventListener(
            'click',
            function () {

                const table =
                    document.getElementById(
                        'maintenanceReportTable'
                    );


                if (!table) {

                    alert(
                        'Tabel laporan tidak ditemukan.'
                    );

                    return;
                }


                const clonedTable =
                    table.cloneNode(true);


                /*
                |--------------------------------------------------------------------------
                | HAPUS KOLOM AKSI
                |--------------------------------------------------------------------------
                */

                clonedTable
                    .querySelectorAll(
                        '.mt-report-action'
                    )
                    .forEach(
                        function (element) {

                            element.remove();

                        }
                    );


                /*
                |--------------------------------------------------------------------------
                | BUAT WINDOW PRINT
                |--------------------------------------------------------------------------
                */

                const printWindow =
                    window.open(
                        '',
                        '_blank',
                        'width=1600,height=900'
                    );


                if (!printWindow) {

                    alert(
                        'Popup diblokir browser. Izinkan popup untuk membuat PDF.'
                    );

                    return;
                }


                printWindow.document.open();


                printWindow.document.write(`
<!DOCTYPE html>

<html lang="id">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">

<title>
    Laporan Work Order Maintenance
</title>

<style>

    @page {
        size: landscape;
        margin: 7mm;
    }

    * {
        box-sizing: border-box;
    }

    body {
        margin: 0;
        padding: 0;
        background: var(--pds-card);
        color: var(--pds-ink);
        font-family:
            Arial,
            Helvetica,
            sans-serif;
    }

    .report-head {
        margin-bottom: 8px;
        text-align: center;
    }

    .report-head h1 {
        margin: 0;
        font-size: 18px;
        font-weight: 700;
    }

    .report-head p {
        margin: 4px 0 0;
        font-size: 8px;
        color: var(--pds-ink-2);
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    th,
    td {
        border: 1px solid #888;
        padding: 3px 4px;
        font-size: 6.5px;
        line-height: 1.35;
        color: var(--pds-ink);
        vertical-align: middle;
        word-break: break-word;
    }

    th {
        background: #eeeeee;
        font-weight: 700;
        text-align: center;
    }

    td {
        background: var(--pds-card);
    }

    .mt-report-status,
    .mt-report-priority {
        display: inline;
        min-width: 0;
        padding: 0;
        border-radius: 0;
        background: transparent !important;
        color: var(--pds-ink) !important;
        font-size: 6.5px;
        font-weight: 700;
    }

</style>

</head>

<body>

    <div class="report-head">

        <h1>
            LAPORAN WORK ORDER MAINTENANCE
        </h1>

        <p>
            Dicetak:
            ${new Date().toLocaleString('id-ID')}
        </p>

    </div>

    ${clonedTable.outerHTML}

</body>

</html>
                `);


                printWindow.document.close();


                setTimeout(
                    function () {

                        printWindow.focus();

                        printWindow.print();

                    },
                    500
                );

            }
        );

    }
);
</script>
@endpush

@endsection