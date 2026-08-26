@extends('layouts.app')

@section('title', 'Work Order Maintenance')

@section('page_title', 'Work Order Maintenance')

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

    /*
    |--------------------------------------------------------------------------
    | FILTER
    |--------------------------------------------------------------------------
    */

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

    /*
    |--------------------------------------------------------------------------
    | INFO
    |--------------------------------------------------------------------------
    */

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

    /*
    |--------------------------------------------------------------------------
    | TABLE
    |--------------------------------------------------------------------------
    */

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
        color: var(--pds-ink);
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

    .mt-report-action {
        min-width: 90px;
        text-align: center;
        white-space: nowrap;
    }

    .mt-report-detail {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        border-radius: 8px;
        background: var(--pds-soft-2);
        color: var(--pds-ink-2);
        text-decoration: none;
        font-size: 17px;
    }

    .mt-report-detail:hover {
        background: var(--pds-line);
    }

    .mt-icon-actions {
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .mt-report-edit {
        background: #eff6ff;
        color: #1d4ed8;
    }

    .mt-report-edit:hover {
        background: #dbeafe;
        color: #1e40af;
    }

    .mt-icon-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 34px;
        height: 34px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        transition: background .15s, color .15s;
        background: #f3f4f6;
        color: #6b7280;
    }

    .mt-icon-btn:hover {
        background: #e5e7eb;
        color: #111827;
    }

    .mt-icon-btn svg {
        width: 16px;
        height: 16px;
    }

    .mt-icon-btn.mt-icon-del {
        background: #fef2f2;
        color: #dc2626;
    }

    .mt-icon-btn.mt-icon-del:hover {
        background: #fee2e2;
        color: #b91c1c;
    }

    @media (max-width: 640px) {

        .mt-report-detail {
            width: 32px;
            height: 32px;
            font-size: 15px;
        }

    }

    .mt-report-empty {
        padding: 45px 20px !important;
        text-align: center !important;
        color: var(--pds-muted-2) !important;
    }

    /*
    |--------------------------------------------------------------------------
    | PRINT
    |--------------------------------------------------------------------------
    */

    .mt-report-print-header {
        display: none;
    }

    @media (max-width: 1400px) {

        .mt-report-filter-grid {
            grid-template-columns:
                repeat(4, minmax(140px, 1fr));
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

    {{-- =====================================================
         HEADER
    ====================================================== --}}

    <div class="mt-report-header">

        <div>

            <h1>
                Laporan Work Order Maintenance
            </h1>

            <p>
                Seluruh Work Order yang tersimpan untuk kebutuhan
                monitoring dan laporan Maintenance.
            </p>

        </div>


        <div class="mt-report-header-right">

            <a
                href="{{ route('work-orders.maintenance') }}"
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


    {{-- =====================================================
         PRINT HEADER
    ====================================================== --}}

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

        {{-- =================================================
             HEADER
        ================================================== --}}

        <div class="mt-report-card-header">

            <div class="mt-report-title">
                Laporan Work Order
            </div>

            <div class="mt-report-subtitle">
                Data diambil langsung dari Work Order yang tersimpan.
            </div>

        </div>


        {{-- =================================================
             FILTER
        ================================================== --}}

        <div class="mt-report-filter">

            <div class="mt-report-filter-title">
                Pencarian / Filter Work Order
            </div>


            <form
                method="GET"
                action="{{ route('work-orders.maintenance.report') }}"
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
                            value="{{ $searchNoWo }}"
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

                            @foreach(
                                $departemenOptions
                                as $option
                            )

                                <option
                                    value="{{ $option }}"
                                    {{ $departemen === $option ? 'selected' : '' }}
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

                            @foreach(
                                $kategoriOptions
                                as $option
                            )

                                <option
                                    value="{{ $option }}"
                                    {{ $kategori === $option ? 'selected' : '' }}
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

                            @foreach(
                                $statusOptions
                                as $option
                            )

                                <option
                                    value="{{ $option }}"
                                    {{ $status === $option ? 'selected' : '' }}
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

                            @for(
                                $i = 1;
                                $i <= 12;
                                $i++
                            )

                                <option
                                    value="{{ $i }}"
                                    {{ (string) $bulan === (string) $i ? 'selected' : '' }}
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

                            @foreach(
                                $tahunOptions
                                as $option
                            )

                                <option
                                    value="{{ $option }}"
                                    {{ (string) $tahun === (string) $option ? 'selected' : '' }}
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
                                type="submit"
                                class="mt-report-filter-btn mt-report-filter-search"
                            >
                                Cari
                            </button>

                            <a
                                href="{{ route('work-orders.maintenance.report') }}"
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


        {{-- =================================================
             INFO
        ================================================== --}}

        <div class="mt-report-info">

            <strong>Laporan Work Order:</strong>

            data pada halaman ini merupakan data Work Order yang sama
            dengan Daftar Work Order Maintenance. Filter dapat digunakan
            untuk membuat laporan berdasarkan No. WO, Departemen,
            Kategori, Status, Bulan, dan Tahun.

        </div>


        {{-- =================================================
             TABLE
        ================================================== --}}

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

                    @forelse(
                        $workOrders as $index => $workOrder
                    )

                        @php

                            /*
                            |--------------------------------------------------------------------------
                            | STATUS
                            |--------------------------------------------------------------------------
                            */

                            $reportStatus =
                                strtoupper(
                                    trim(
                                        (string) (
                                            $workOrder->status
                                            ?? 'OPEN'
                                        )
                                    )
                                );


                            /*
                            |--------------------------------------------------------------------------
                            | PRIORITAS
                            |--------------------------------------------------------------------------
                            */

                            $reportPriority =
                                strtoupper(
                                    trim(
                                        (string) (
                                            $workOrder->priority
                                            ?? ''
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


                            foreach (
                                $sparepartColumns as $column
                            ) {

                                if (
                                    isset(
                                        $workOrder->{$column}
                                    )
                                    &&
                                    !empty(
                                        $workOrder->{$column}
                                    )
                                ) {

                                    if (
                                        is_array(
                                            $workOrder->{$column}
                                        )
                                    ) {

                                        $spareparts =
                                            $workOrder->{$column};

                                    } else {

                                        $decoded =
                                            json_decode(
                                                $workOrder->{$column},
                                                true
                                            );

                                        if (
                                            is_array(
                                                $decoded
                                            )
                                        ) {

                                            $spareparts =
                                                $decoded;
                                        }

                                    }

                                    break;
                                }

                            }


                            /*
                            |--------------------------------------------------------------------------
                            | TANGGAL KERUSAKAN
                            |--------------------------------------------------------------------------
                            */

                            $tanggalKerusakan =
                                '-';

                            if (
                                !empty(
                                    $workOrder->tanggal_kerusakan
                                    ?? null
                                )
                            ) {

                                try {

                                    $tanggalKerusakan =
                                        \Carbon\Carbon::parse(
                                            $workOrder->tanggal_kerusakan
                                        )->format('d-m-Y');

                                } catch (
                                    \Throwable $e
                                ) {

                                    $tanggalKerusakan =
                                        $workOrder->tanggal_kerusakan;

                                }

                            }


                            /*
                            |--------------------------------------------------------------------------
                            | JADWAL
                            |--------------------------------------------------------------------------
                            */

                            $jadwalPerbaikan =
                                '-';

                            if (
                                !empty(
                                    $workOrder->jadwal_perbaikan
                                    ?? null
                                )
                            ) {

                                try {

                                    $jadwalPerbaikan =
                                        \Carbon\Carbon::parse(
                                            $workOrder->jadwal_perbaikan
                                        )->format('d-m-Y');

                                } catch (
                                    \Throwable $e
                                ) {

                                    $jadwalPerbaikan =
                                        $workOrder->jadwal_perbaikan;

                                }

                            }


                            /*
                            |--------------------------------------------------------------------------
                            | MULAI
                            |--------------------------------------------------------------------------
                            */

                            $tanggalMulai =
                                '-';

                            if (
                                !empty(
                                    $workOrder->tanggal_mulai_perbaikan
                                    ?? null
                                )
                            ) {

                                try {

                                    $tanggalMulai =
                                        \Carbon\Carbon::parse(
                                            $workOrder->tanggal_mulai_perbaikan
                                        )->format('d-m-Y');

                                } catch (
                                    \Throwable $e
                                ) {

                                    $tanggalMulai =
                                        $workOrder->tanggal_mulai_perbaikan;

                                }

                            }


                            /*
                            |--------------------------------------------------------------------------
                            | SELESAI
                            |--------------------------------------------------------------------------
                            */

                            $tanggalSelesai =
                                '-';

                            if (
                                !empty(
                                    $workOrder->tanggal_selesai_perbaikan
                                    ?? null
                                )
                            ) {

                                try {

                                    $tanggalSelesai =
                                        \Carbon\Carbon::parse(
                                            $workOrder->tanggal_selesai_perbaikan
                                        )->format('d-m-Y');

                                } catch (
                                    \Throwable $e
                                ) {

                                    $tanggalSelesai =
                                        $workOrder->tanggal_selesai_perbaikan;

                                }

                            }

                        @endphp


                        <tr
                            data-work-order-id="{{ $workOrder->id }}"
                        >

                            {{-- NO --}}

                            <td class="mt-report-no">
                                {{ $index + 1 }}
                            </td>


                            {{-- NO WO --}}

                            <td class="mt-report-wo">
                                {{ $workOrder->no_wo ?: '-' }}
                            </td>


                            {{-- TANGGAL KERUSAKAN --}}

                            <td class="mt-report-date">
                                {{ $tanggalKerusakan }}
                            </td>


                            {{-- JAM --}}

                            <td class="mt-report-time">
                                {{ $workOrder->jam_kerusakan ?: '-' }}
                            </td>


                            {{-- DIBUAT OLEH --}}

                            <td class="mt-report-user">
                                {{ $workOrder->dibuat_oleh ?: '-' }}
                            </td>


                            {{-- DEPARTEMEN --}}

                            <td class="mt-report-wide">
                                {{ $workOrder->departemen ?: '-' }}
                            </td>


                            {{-- KATEGORI --}}

                            <td class="mt-report-wide">
                                {{ $workOrder->kategori ?: '-' }}
                            </td>


                            {{-- DITUJUKAN --}}

                            <td class="mt-report-wide">
                                {{ $workOrder->tujuan ?: '-' }}
                            </td>


                            {{-- AREA --}}

                            <td class="mt-report-wide">
                                {{ $workOrder->area ?: '-' }}
                            </td>


                            {{-- MESIN --}}

                            <td class="mt-report-wide">
                                {{ $workOrder->mesin ?: '-' }}
                            </td>


                            {{-- JOB --}}

                            <td class="mt-report-wide">
                                {{ $workOrder->job ?: '-' }}
                            </td>


                            {{-- DESKRIPSI --}}

                            <td class="mt-report-description">
                                {{ $workOrder->deskripsi ?: '-' }}
                            </td>


                            {{-- PRIORITAS --}}

                            <td class="mt-report-wide">

                                @if(
                                    $reportPriority === 'EMERGENCY'
                                )

                                    <span
                                        class="mt-report-priority mt-report-priority-emergency"
                                    >
                                        EMERGENCY
                                    </span>

                                @elseif(
                                    $reportPriority === 'URGENT'
                                )

                                    <span
                                        class="mt-report-priority mt-report-priority-urgent"
                                    >
                                        URGENT
                                    </span>

                                @elseif(
                                    $reportPriority === 'NORMAL'
                                )

                                    <span
                                        class="mt-report-priority mt-report-priority-normal"
                                    >
                                        NORMAL
                                    </span>

                                @elseif(
                                    $reportPriority !== ''
                                )

                                    <span
                                        class="mt-report-priority mt-report-priority-normal"
                                    >
                                        {{ $reportPriority }}
                                    </span>

                                @else

                                    -

                                @endif

                            </td>


                            {{-- STATUS --}}

                            <td class="mt-report-wide">

                                @if(
                                    $reportStatus === 'OPEN'
                                )

                                    <span
                                        class="mt-report-status mt-report-status-open"
                                    >
                                        OPEN
                                    </span>

                                @elseif(
                                    $reportStatus === 'DITERIMA'
                                )

                                    <span
                                        class="mt-report-status mt-report-status-diterima"
                                    >
                                        DITERIMA
                                    </span>

                                @elseif(
                                    $reportStatus === 'SCHEDULED'
                                )

                                    <span
                                        class="mt-report-status mt-report-status-scheduled"
                                    >
                                        SCHEDULED
                                    </span>

                                @elseif(
                                    $reportStatus === 'IN PROGRESS'
                                    ||
                                    $reportStatus === 'IN PROSES'
                                )

                                    <span
                                        class="mt-report-status mt-report-status-progress"
                                    >
                                        IN PROGRESS
                                    </span>

                                @elseif(
                                    $reportStatus === 'PENDING'
                                )

                                    <span
                                        class="mt-report-status mt-report-status-pending"
                                    >
                                        PENDING
                                    </span>

                                @elseif(
                                    $reportStatus === 'DITOLAK'
                                )

                                    <span
                                        class="mt-report-status mt-report-status-ditolak"
                                    >
                                        DITOLAK
                                    </span>

                                @elseif(
                                    $reportStatus === 'SERVICE LUAR'
                                )

                                    <span
                                        class="mt-report-status mt-report-status-service"
                                    >
                                        SERVICE LUAR
                                    </span>

                                @elseif(
                                    $reportStatus === 'CLOSE'
                                    ||
                                    $reportStatus === 'SELESAI'
                                )

                                    <span
                                        class="mt-report-status mt-report-status-close"
                                    >
                                        CLOSE
                                    </span>

                                @else

                                    <span
                                        class="mt-report-status mt-report-status-pending"
                                    >
                                        {{ $workOrder->status ?: '-' }}
                                    </span>

                                @endif

                            </td>


                            {{-- LAPORAN DITERIMA --}}

                            <td class="mt-report-description">
                                {{ $workOrder->laporan_diterima ?: '-' }}
                            </td>


                            {{-- PERENCANAAN / SOLUSI --}}

                            <td class="mt-report-description">
                                {{ $workOrder->perencanaan_pekerjaan ?: '-' }}
                            </td>


                            {{-- JADWAL --}}

                            <td class="mt-report-date">
                                {{ $jadwalPerbaikan }}
                            </td>


                            {{-- TANGGAL MULAI --}}

                            <td class="mt-report-date">
                                {{ $tanggalMulai }}
                            </td>


                            {{-- JAM MULAI --}}

                            <td class="mt-report-time">
                                {{ $workOrder->jam_mulai_perbaikan ?: '-' }}
                            </td>


                            {{-- TANGGAL SELESAI --}}

                            <td class="mt-report-date">
                                {{ $tanggalSelesai }}
                            </td>


                            {{-- JAM SELESAI --}}

                            <td class="mt-report-time">
                                {{ $workOrder->jam_selesai_perbaikan ?: '-' }}
                            </td>


                            {{-- TEKNISI --}}

                            <td class="mt-report-wide">
                                {{ $workOrder->teknisi ?: '-' }}
                            </td>


                            {{-- KETERANGAN --}}

                            <td class="mt-report-description">
                                {{ $workOrder->keterangan ?: '-' }}
                            </td>


                            {{-- SPAREPART --}}

                            <td class="mt-report-sparepart">

                                @if(
                                    count($spareparts) > 0
                                )

                                    @foreach(
                                        $spareparts as $sparepart
                                    )

                                        <div class="mt-report-sparepart-item">

                                            {{
                                                $sparepart['nama_barang']
                                                ??
                                                '-'
                                            }}

                                            ×

                                            {{
                                                $sparepart['qty']
                                                ??
                                                0
                                            }}

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

                                    <a
                                        href="{{ route(
                                            'work-orders.maintenance.show',
                                            $workOrder->id
                                        ) }}"
                                        class="mt-report-detail"
                                        title="Lihat Detail"
                                        aria-label="Lihat Detail Work Order"
                                    >
                                        <x-icon name="eye"></x-icon>
                                    </a>

                                    {{-- TINDAK LANJUT / EDIT --}}

                                    @if($_canEdit)
                                    <a
                                        href="{{ route(
                                            'work-orders.maintenance.edit',
                                            $workOrder->id
                                        ) }}"
                                        class="mt-report-detail mt-report-edit"
                                        title="Tindak Lanjut / Edit Work Order"
                                        aria-label="Tindak Lanjut Work Order"
                                    >
                                        <x-icon name="edit"></x-icon>
                                    </a>
                                    @endif

                                    {{-- DELETE --}}

                                    @if($_canDelete)
                                    <form
                                        action="{{ route(
                                            'work-orders.maintenance.destroy',
                                            $workOrder->id
                                        ) }}"
                                        method="POST"
                                        onsubmit="return confirm('Hapus Work Order {{ $workOrder->no_wo }}? Tindakan ini tidak dapat dibatalkan.');"
                                        style="display:inline"
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
                                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
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
                                Belum ada Work Order
                                dalam database.
                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>


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
                    table.cloneNode(
                        true
                    );


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
                | HILANGKAN STYLE YANG TIDAK PERLU
                |--------------------------------------------------------------------------
                */

                clonedTable
                    .querySelectorAll(
                        '.mt-report-detail'
                    )
                    .forEach(
                        function (element) {

                            element.remove();

                        }
                    );


                /*
                |--------------------------------------------------------------------------
                | WINDOW PRINT
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
        table-layout: fixed;
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
        white-space: nowrap;
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

    .mt-report-no {
        width: 3%;
    }

    .mt-report-wo {
        width: 5%;
    }

    .mt-report-date {
        width: 4%;
    }

    .mt-report-time {
        width: 3%;
    }

    .mt-report-user {
        width: 5%;
    }

    .mt-report-wide {
        width: 5%;
    }

    .mt-report-description,
    .mt-report-sparepart {
        width: 6%;
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

@endsection