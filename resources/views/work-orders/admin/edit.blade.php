
@extends('layouts.app')

@section('title', 'Tindak Lanjut Work Order')

@section('page_title', 'Work Order Maintenance')

@section('page_subtitle', 'Tindak lanjut dan pengerjaan Work Order')

@push('styles')
<style>
    * {
        box-sizing: border-box;
    }

    .mtc-page {
        padding: 10px 0 30px;
    }

    .mtc-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 20px;
        margin-bottom: 22px;
    }

    .mtc-header h2 {
        margin: 0 0 5px;
        font-size: 24px;
        color: var(--pds-ink);
    }

    .mtc-header p {
        margin: 0;
        color: var(--pds-muted);
        font-size: 13px;
    }

    .mtc-card {
        background: var(--pds-card);
        border: 1px solid var(--pds-line);
        border-radius: 14px;
        box-shadow: 0 3px 12px rgba(0, 0, 0, .05);
        overflow: hidden;
        margin-bottom: 18px;
    }

    .mtc-card-header {
        padding: 18px 22px;
        border-bottom: 1px solid var(--pds-line);
    }

    .mtc-card-header h3 {
        margin: 0 0 5px;
        font-size: 16px;
        color: var(--pds-ink);
    }

    .mtc-card-header p {
        margin: 0;
        font-size: 12px;
        color: var(--pds-muted);
    }

    .mtc-body {
        padding: 22px;
    }

    .mtc-detail-grid,
    .mtc-form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 18px 20px;
    }

    .mtc-field,
    .mtc-form-group {
        display: flex;
        flex-direction: column;
    }

    .full {
        grid-column: 1 / -1;
    }

    .mtc-label {
        margin-bottom: 6px;
        font-size: 11px;
        font-weight: 700;
        color: var(--pds-muted);
        text-transform: uppercase;
        letter-spacing: .03em;
    }

    .mtc-value {
        min-height: 42px;
        padding: 11px 12px;
        border: 1px solid var(--pds-line);
        border-radius: 9px;
        background: var(--pds-soft);
        color: var(--pds-ink);
        font-size: 13px;
        line-height: 1.5;
        word-break: break-word;
    }

    .mtc-text-value {
        min-height: 110px;
        white-space: pre-wrap;
    }

    .mtc-label-form {
        margin-bottom: 7px;
        color: var(--pds-ink-2);
        font-size: 12px;
        font-weight: 700;
    }

    .required {
        color: #dc2626;
    }

    .mtc-input,
    .mtc-select,
    .mtc-textarea {
        width: 100%;
        border: 1px solid var(--pds-line-2);
        border-radius: 9px;
        background: var(--pds-card);
        color: var(--pds-ink);
        font-size: 13px;
    }

    .mtc-input,
    .mtc-select {
        height: 42px;
        padding: 0 12px;
    }

    .mtc-textarea {
        min-height: 110px;
        padding: 11px 12px;
        resize: vertical;
    }

    .mtc-input:focus,
    .mtc-select:focus,
    .mtc-textarea:focus {
        outline: none;
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, .08);
    }

    .mtc-help {
        margin-top: 5px;
        color: var(--pds-muted-2);
        font-size: 11px;
        line-height: 1.5;
    }

    .mtc-error {
        margin-bottom: 18px;
        padding: 13px 15px;
        border-radius: 9px;
        background: #fef2f2;
        border: 1px solid #fecaca;
        color: #991b1b;
        font-size: 13px;
    }

    .mtc-error ul {
        margin: 7px 0 0 18px;
        padding: 0;
    }

    .mtc-notice {
        margin-bottom: 20px;
        padding: 13px 15px;
        border-radius: 9px;
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        color: #1e40af;
        font-size: 12px;
        line-height: 1.6;
    }

    /* =====================================================
       STATUS
    ====================================================== */

    .mtc-status {
        display: inline-flex;
        width: fit-content;
        min-width: 90px;
        justify-content: center;
        padding: 6px 11px;
        border-radius: 20px;
        font-size: 10px;
        font-weight: 700;
    }

    .status-blue {
        background: #dbeafe;
        color: #1d4ed8;
    }

    .status-gray {
        background: var(--pds-line);
        color: var(--pds-muted);
    }

    .status-close {
        background: #fee2e2;
        color: #b91c1c;
    }

    /* =====================================================
       SPAREPART AREA
    ====================================================== */

    .sparepart-selector {
        border: 1px solid var(--pds-line);
        border-radius: 11px;
        background: var(--pds-soft);
        overflow: hidden;
    }

    .sparepart-selector-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
        padding: 15px;
        background: var(--pds-card);
        border-bottom: 1px solid var(--pds-line);
        cursor: pointer;
    }

    .sparepart-selector-head:hover {
        background: var(--pds-soft);
    }

    .sparepart-head-title {
        font-size: 13px;
        font-weight: 700;
        color: var(--pds-ink);
    }

    .sparepart-head-subtitle {
        margin-top: 4px;
        font-size: 11px;
        color: var(--pds-muted-2);
        line-height: 1.5;
    }

    .sparepart-head-arrow {
        font-size: 16px;
        color: var(--pds-muted);
        transition: transform .2s ease;
    }

    .sparepart-selector.open .sparepart-head-arrow {
        transform: rotate(180deg);
    }

    .sparepart-machine-panel {
        display: none;
        padding: 15px;
        border-bottom: 1px solid var(--pds-line);
        background: var(--pds-soft);
    }

    .sparepart-selector.open .sparepart-machine-panel {
        display: block;
    }

    .sparepart-section-title {
        margin-bottom: 10px;
        color: var(--pds-ink-2);
        font-size: 12px;
        font-weight: 700;
    }

    .sparepart-table-wrap {
        width: 100%;
        overflow-x: auto;
    }

    .sparepart-table {
        width: 100%;
        min-width: 760px;
        border-collapse: collapse;
        background: var(--pds-card);
    }

    .sparepart-table th,
    .sparepart-table td {
        padding: 10px 11px;
        border: 1px solid var(--pds-line);
        font-size: 11px;
        text-align: left;
        vertical-align: middle;
    }

    .sparepart-table th {
        background: var(--pds-soft);
        color: var(--pds-ink-2);
        font-weight: 700;
        white-space: nowrap;
    }

    .sparepart-name {
        color: var(--pds-ink);
        font-weight: 600;
    }

    .sparepart-code {
        margin-top: 3px;
        color: var(--pds-muted-2);
        font-size: 9px;
    }

    .sparepart-stock {
        color: var(--pds-ink);
        font-weight: 700;
    }

    .sparepart-stock-empty {
        color: #dc2626;
    }

    .sparepart-qty {
        width: 80px;
        height: 34px;
        padding: 0 8px;
        border: 1px solid var(--pds-line-2);
        border-radius: 7px;
        font-size: 11px;
    }

    .sparepart-btn {
        min-width: 68px;
        height: 34px;
        border: 1px solid #bfdbfe;
        border-radius: 7px;
        background: #eff6ff;
        color: #1d4ed8;
        cursor: pointer;
        font-size: 10px;
        font-weight: 700;
    }

    .sparepart-btn:hover {
        background: #dbeafe;
    }

    .sparepart-btn-danger {
        border-color: #fecaca;
        background: #fef2f2;
        color: #dc2626;
    }

    .sparepart-empty {
        padding: 22px;
        text-align: center;
        border: 1px dashed var(--pds-line-2);
        border-radius: 8px;
        background: var(--pds-card);
        color: var(--pds-muted-2);
        font-size: 12px;
    }

    .btn-add-sparepart {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 38px;
        padding: 8px 13px;
        border: none;
        border-radius: 8px;
        background: #2563eb;
        color: #ffffff;
        cursor: pointer;
        font-size: 11px;
        font-weight: 700;
        white-space: nowrap;
    }

    .btn-add-sparepart:hover {
        background: #1d4ed8;
    }

    /* =====================================================
       TABEL STOK
    ====================================================== */

    .stock-panel {
        display: none;
        padding: 15px;
        background: var(--pds-soft);
        border-top: 1px solid var(--pds-line);
    }

    .stock-panel.open {
        display: block;
    }

    .stock-panel-head {
        margin-bottom: 12px;
    }

    .stock-panel-title {
        font-size: 13px;
        font-weight: 700;
        color: var(--pds-ink);
    }

    .stock-panel-subtitle {
        margin-top: 4px;
        color: var(--pds-muted);
        font-size: 11px;
    }

    .stock-search {
        width: 100%;
        height: 40px;
        margin-bottom: 10px;
        padding: 0 11px;
        border: 1px solid var(--pds-line-2);
        border-radius: 8px;
        background: var(--pds-card);
        font-size: 12px;
    }

    .stock-table-wrap {
        width: 100%;
        max-height: 350px;
        overflow: auto;
        border: 1px solid var(--pds-line);
        border-radius: 8px;
        background: var(--pds-card);
    }

    .stock-table {
        width: 100%;
        min-width: 800px;
        border-collapse: collapse;
    }

    .stock-table th,
    .stock-table td {
        padding: 9px 10px;
        border-bottom: 1px solid var(--pds-line);
        font-size: 11px;
        text-align: left;
        vertical-align: middle;
        white-space: nowrap;
    }

    .stock-table th {
        position: sticky;
        top: 0;
        z-index: 2;
        background: var(--pds-soft);
        color: var(--pds-ink-2);
    }

    .stock-table tbody tr:hover {
        background: var(--pds-soft);
    }

    .stock-empty-row {
        padding: 20px;
        text-align: center;
        color: var(--pds-muted-2);
    }

    /* =====================================================
       SPAREPART TERPILIH
    ====================================================== */

    .used-sparepart-box {
        margin-top: 16px;
    }

    .used-sparepart-title {
        margin-bottom: 10px;
        color: var(--pds-ink-2);
        font-size: 12px;
        font-weight: 700;
    }

    .used-sparepart-empty {
        padding: 20px;
        text-align: center;
        color: var(--pds-muted-2);
        border: 1px dashed var(--pds-line-2);
        border-radius: 8px;
        background: var(--pds-card);
        font-size: 12px;
    }

    /* =====================================================
       FOTO
    ====================================================== */

    .mtc-upload {
        padding: 12px;
        border: 1px dashed var(--pds-line-2);
        border-radius: 9px;
        background: var(--pds-soft);
    }

    .mtc-photo {
        margin-top: 10px;
    }

    .mtc-photo img {
        width: 130px;
        height: 100px;
        object-fit: cover;
        border: 1px solid var(--pds-line);
        border-radius: 8px;
    }

    /* =====================================================
       FOOTER
    ====================================================== */

    .mtc-footer {
        display: flex;
        justify-content: flex-end;
        gap: 8px;
        padding: 18px 22px;
        border-top: 1px solid var(--pds-line);
        background: var(--pds-soft);
    }

    .mtc-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 42px;
        padding: 10px 18px;
        border: none;
        border-radius: 8px;
        text-decoration: none;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
    }

    .mtc-btn-gray {
        background: var(--pds-line);
        color: var(--pds-ink-2);
    }

    .mtc-btn-primary {
        background: #2563eb;
        color: #ffffff;
    }

    .mtc-btn-primary:hover {
        background: #1d4ed8;
    }

    @media (max-width: 800px) {

        .mtc-header {
            flex-direction: column;
        }

        .mtc-detail-grid,
        .mtc-form-grid {
            grid-template-columns: 1fr;
        }

        .full {
            grid-column: auto;
        }

        .sparepart-selector-head {
            align-items: flex-start;
        }
    }

    @media (max-width: 600px) {

        .sparepart-selector-head {
            flex-direction: column;
        }

        .btn-add-sparepart {
            width: 100%;
        }

        .mtc-footer {
            flex-direction: column-reverse;
        }

        .mtc-btn {
            width: 100%;
        }
    }

</style>
@endpush


@section('content')

@php

    /*
    |--------------------------------------------------------------------------
    | STATUS
    |--------------------------------------------------------------------------
    */

    $maintenanceStatus = strtoupper(
        trim(
            $workOrder->status_maintenance
            ?? $workOrder->status
            ?? 'OPEN'
        )
    );

    /*
    |--------------------------------------------------------------------------
    | SPAREPART TERKAIT MESIN
    |--------------------------------------------------------------------------
    |
    | Harus dikirim dari Controller berdasarkan mesin Work Order.
    |
    */

    $machineSpareparts =
        $machineSpareparts ?? collect();

    /*
    |--------------------------------------------------------------------------
    | SEMUA STOK SPAREPART
    |--------------------------------------------------------------------------
    |
    | Harus berasal dari tabel Stok Barang / Inventory.
    |
    */

    $stockSpareparts =
        $spareparts ?? collect();

@endphp


<div class="mtc-page">

    {{-- =====================================================
         HEADER
    ====================================================== --}}

    <div class="mtc-header">

        <div>

            <h2>
                Tindak Lanjut Work Order
            </h2>

            <p>
                Maintenance memproses Work Order tanpa mengubah data permintaan User.
            </p>

        </div>

        <a
            href="{{ route('work-orders.admin.index') }}"
            class="mtc-btn mtc-btn-gray"
        >
            Kembali
        </a>

    </div>


    {{-- =====================================================
         ERROR
    ====================================================== --}}

    @if($errors->any())

        <div class="mtc-error">

            <strong>
                Data belum dapat disimpan:
            </strong>

            <ul>

                @foreach($errors->all() as $error)

                    <li>
                        {{ $error }}
                    </li>

                @endforeach

            </ul>

        </div>

    @endif


    <div class="mtc-notice">

        <strong>Detail Work Order</strong> adalah data asli dari User.
        Maintenance tidak dapat mengubahnya.
        Bagian yang dapat diubah hanya
        <strong>Tindak Lanjut Maintenance</strong>.

    </div>


    {{-- =====================================================
         DETAIL WORK ORDER USER
    ====================================================== --}}

    <div class="mtc-card">

        <div class="mtc-card-header">

            <h3>
                Detail Work Order
            </h3>

            <p>
                Data permintaan User
            </p>

        </div>


        <div class="mtc-body">

            <div class="mtc-detail-grid">

                <div class="mtc-field">

                    <div class="mtc-label">
                        No. WO
                    </div>

                    <div class="mtc-value">
                        {{ $workOrder->no_wo ?: '-' }}
                    </div>

                </div>


                <div class="mtc-field">

                    <div class="mtc-label">
                        Dibuat Oleh
                    </div>

                    <div class="mtc-value">
                        {{ $workOrder->dibuat_oleh ?: '-' }}
                    </div>

                </div>


                <div class="mtc-field">

                    <div class="mtc-label">
                        Tanggal Kerusakan
                    </div>

                    <div class="mtc-value">

                        {{
                            $workOrder->tanggal_kerusakan
                                ? \Carbon\Carbon::parse(
                                    $workOrder->tanggal_kerusakan
                                )->format('d-m-Y')
                                : '-'
                        }}

                    </div>

                </div>


                <div class="mtc-field">

                    <div class="mtc-label">
                        Jam Kerusakan
                    </div>

                    <div class="mtc-value">
                        {{ $workOrder->jam_kerusakan ?: '-' }}
                    </div>

                </div>


                <div class="mtc-field">

                    <div class="mtc-label">
                        Departemen
                    </div>

                    <div class="mtc-value">
                        {{ $workOrder->departemen ?: '-' }}
                    </div>

                </div>


                <div class="mtc-field">

                    <div class="mtc-label">
                        Kategori
                    </div>

                    <div class="mtc-value">
                        {{ $workOrder->kategori ?: '-' }}
                    </div>

                </div>


                <div class="mtc-field">

                    <div class="mtc-label">
                        Ditujukan
                    </div>

                    <div class="mtc-value">
                        {{ $workOrder->tujuan ?: '-' }}
                    </div>

                </div>


                <div class="mtc-field">

                    <div class="mtc-label">
                        Area / Line
                    </div>

                    <div class="mtc-value">
                        {{ $workOrder->area ?: '-' }}
                    </div>

                </div>


                <div class="mtc-field">

                    <div class="mtc-label">
                        Mesin
                    </div>

                    <div class="mtc-value">
                        {{ $workOrder->mesin ?: '-' }}
                    </div>

                </div>


                <div class="mtc-field">

                    <div class="mtc-label">
                        Prioritas
                    </div>

                    <div class="mtc-value">
                        {{ strtoupper($workOrder->priority ?? '-') }}
                    </div>

                </div>


                <div class="mtc-field full">

                    <div class="mtc-label">
                        Job
                    </div>

                    <div class="mtc-value">
                        {{ $workOrder->job ?: '-' }}
                    </div>

                </div>


                <div class="mtc-field full">

                    <div class="mtc-label">
                        Deskripsi Kerusakan / Permintaan
                    </div>

                    <div class="mtc-value mtc-text-value">
                        {{ $workOrder->deskripsi ?: '-' }}
                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- =====================================================
         TINDAK LANJUT MAINTENANCE
    ====================================================== --}}

    <div class="mtc-card">

        <div class="mtc-card-header">

            <h3>
                Tindak Lanjut Maintenance
            </h3>

            <p>
                Status, solusi, pengerjaan dan sparepart
            </p>

        </div>


        <form
            id="maintenanceForm"
            action="{{ route(
                'work-orders.admin.update',
                $workOrder->id
            ) }}"
            method="POST"
            enctype="multipart/form-data"
        >

            @csrf
            @method('PUT')

            @if($errors->any())
                <div style="padding:12px 18px;margin:0 0 16px;background:#fef2f2;border:1px solid #fca5a5;border-radius:8px;color:#991b1b;font-size:14px;">
                    <strong style="display:block;margin-bottom:6px;">Data belum dapat disimpan.</strong>
                    <ul style="margin:0;padding-left:18px;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif


            <div class="mtc-body">

                <div class="mtc-form-grid">

                    {{-- STATUS --}}

                    <div class="mtc-form-group">

                        <label
                            for="status"
                            class="mtc-label-form"
                        >
                            Status Maintenance
                            <span class="required">*</span>
                        </label>

                        <select
                            id="status"
                            name="status"
                            class="mtc-select"
                            required
                        >

                            <option
                                value="OPEN"
                                @selected(
                                    $maintenanceStatus === 'OPEN'
                                )
                            >
                                OPEN
                            </option>

                            <option
                                value="DITERIMA"
                                @selected(
                                    $maintenanceStatus === 'DITERIMA'
                                )
                            >
                                DITERIMA
                            </option>

                            <option
                                value="DITOLAK"
                                @selected(
                                    $maintenanceStatus === 'DITOLAK'
                                )
                            >
                                DITOLAK
                            </option>

                            <option
                                value="SCHEDULED"
                                @selected(
                                    $maintenanceStatus === 'SCHEDULED'
                                )
                            >
                                SCHEDULED
                            </option>

                            <option
                                value="IN PROGRESS"
                                @selected(
                                    $maintenanceStatus === 'IN PROGRESS'
                                    || $maintenanceStatus === 'IN PROSES'
                                )
                            >
                                IN PROGRESS
                            </option>

                            <option
                                value="PENDING"
                                @selected(
                                    $maintenanceStatus === 'PENDING'
                                )
                            >
                                PENDING
                            </option>

                            <option
                                value="SERVICE LUAR"
                                @selected(
                                    $maintenanceStatus === 'SERVICE LUAR'
                                )
                            >
                                SERVICE LUAR
                            </option>

                            <option
                                value="CLOSE"
                                @selected(
                                    $maintenanceStatus === 'CLOSE'
                                    || $maintenanceStatus === 'SELESAI'
                                )
                            >
                                CLOSE
                            </option>

                        </select>

                    </div>


                    {{-- LAPORAN DITERIMA --}}

                    <div class="mtc-form-group">

                        <label
                            for="laporan_diterima"
                            class="mtc-label-form"
                        >
                            Laporan Diterima
                        </label>

                        <input
                            type="text"
                            id="laporan_diterima"
                            name="laporan_diterima"
                            class="mtc-input"
                            value="{{ old(
                                'laporan_diterima',
                                $workOrder->laporan_diterima
                            ) }}"
                            placeholder="Contoh: Laporan diterima Maintenance"
                        >

                    </div>


                    {{-- SOLUSI --}}

                    <div class="mtc-form-group full">

                        <label
                            for="solusi_perbaikan"
                            class="mtc-label-form"
                        >
                            Solusi / Tindakan Perbaikan
                        </label>

                        <textarea
                            id="solusi_perbaikan"
                            name="solusi_perbaikan"
                            class="mtc-textarea"
                            placeholder="Jelaskan solusi dan tindakan yang dilakukan untuk menyelesaikan kerusakan..."
                        >{{ old(
                            'solusi_perbaikan',
                            $workOrder->solusi_perbaikan
                            ?? $workOrder->perencanaan_pekerjaan
                        ) }}</textarea>

                        <div class="mtc-help">
                            Contoh: penggantian bearing, alignment ulang,
                            setting sensor, penggantian kabel, dan sebagainya.
                        </div>

                    </div>


                    {{-- JADWAL --}}

                    <div class="mtc-form-group">

                        <label
                            for="jadwal_perbaikan"
                            class="mtc-label-form"
                        >
                            Jadwal Perbaikan
                        </label>

                        <input
                            type="datetime-local"
                            id="jadwal_perbaikan"
                            name="jadwal_perbaikan"
                            class="mtc-input"
                            value="{{ old(
                                'jadwal_perbaikan',
                                $workOrder->jadwal_perbaikan
                                    ? \Carbon\Carbon::parse(
                                        $workOrder->jadwal_perbaikan
                                    )->format('Y-m-d\TH:i')
                                    : ''
                            ) }}"
                        >

                    </div>


                    {{-- TEKNISI --}}

                    <div class="mtc-form-group">

                        <label
                            for="teknisi"
                            class="mtc-label-form"
                        >
                            Teknisi
                        </label>

                        <input
                            type="text"
                            id="teknisi"
                            name="teknisi"
                            class="mtc-input"
                            value="{{ old(
                                'teknisi',
                                $workOrder->teknisi
                            ) }}"
                            placeholder="Nama teknisi"
                        >

                    </div>


                    {{-- MULAI --}}

                    <div class="mtc-form-group">

                        <label
                            for="tanggal_mulai_perbaikan"
                            class="mtc-label-form"
                        >
                            Tanggal Mulai
                        </label>

                        <input
                            type="date"
                            id="tanggal_mulai_perbaikan"
                            name="tanggal_mulai_perbaikan"
                            class="mtc-input"
                            value="{{ old(
                                'tanggal_mulai_perbaikan',
                                $workOrder->tanggal_mulai_perbaikan
                                    ? \Carbon\Carbon::parse(
                                        $workOrder->tanggal_mulai_perbaikan
                                    )->format('Y-m-d')
                                    : ''
                            ) }}"
                        >

                    </div>


                    <div class="mtc-form-group">

                        <label
                            for="jam_mulai_perbaikan"
                            class="mtc-label-form"
                        >
                            Jam Mulai
                        </label>

                        <input
                            type="time"
                            id="jam_mulai_perbaikan"
                            name="jam_mulai_perbaikan"
                            class="mtc-input"
                            value="{{ old(
                                'jam_mulai_perbaikan',
                                $workOrder->jam_mulai_perbaikan
                            ) }}"
                        >

                    </div>


                    {{-- SELESAI --}}

                    <div class="mtc-form-group">

                        <label
                            for="tanggal_selesai_perbaikan"
                            class="mtc-label-form"
                        >
                            Tanggal Selesai
                        </label>

                        <input
                            type="date"
                            id="tanggal_selesai_perbaikan"
                            name="tanggal_selesai_perbaikan"
                            class="mtc-input"
                            value="{{ old(
                                'tanggal_selesai_perbaikan',
                                $workOrder->tanggal_selesai_perbaikan
                                    ? \Carbon\Carbon::parse(
                                        $workOrder->tanggal_selesai_perbaikan
                                    )->format('Y-m-d')
                                    : ''
                            ) }}"
                        >

                    </div>


                    <div class="mtc-form-group">

                        <label
                            for="jam_selesai_perbaikan"
                            class="mtc-label-form"
                        >
                            Jam Selesai
                        </label>

                        <input
                            type="time"
                            id="jam_selesai_perbaikan"
                            name="jam_selesai_perbaikan"
                            class="mtc-input"
                            value="{{ old(
                                'jam_selesai_perbaikan',
                                $workOrder->jam_selesai_perbaikan
                            ) }}"
                        >

                    </div>


                    {{-- KETERANGAN --}}

                    <div class="mtc-form-group full">

                        <label
                            for="keterangan"
                            class="mtc-label-form"
                        >
                            Keterangan / Alasan
                        </label>

                        <textarea
                            id="keterangan"
                            name="keterangan"
                            class="mtc-textarea"
                            placeholder="Wajib diisi jika DITOLAK, PENDING, atau SERVICE LUAR."
                        >{{ old(
                            'keterangan',
                            $workOrder->keterangan
                        ) }}</textarea>

                    </div>

                </div>

            </div>


            {{-- =====================================================
                 SPAREPART
            ====================================================== --}}

            <div class="mtc-body">

                <div class="mtc-form-group">

                    <label class="mtc-label-form">
                        Sparepart
                    </label>


                    <div
                        id="sparepartSelector"
                        class="sparepart-selector"
                    >


                        {{-- =================================================
                             HEADER / KLIK SPAREPART
                        ================================================== --}}

                        <div
                            class="sparepart-selector-head"
                            onclick="toggleMachineSpareparts()"
                        >

                            <div>

                                <div class="sparepart-head-title">

                                    @if($workOrder->mesin)

                                        Sparepart Mesin:
                                        {{ $workOrder->mesin }}

                                    @else

                                        Sparepart Work Order

                                    @endif

                                </div>


                                <div class="sparepart-head-subtitle">

                                    @if($workOrder->mesin)

                                        Klik untuk melihat sparepart
                                        yang sudah ditentukan untuk mesin ini.

                                    @else

                                        Mesin tidak dipilih.
                                        Sparepart dapat dipilih dari Stok Barang.

                                    @endif

                                </div>

                            </div>


                            <div
                                id="sparepartArrow"
                                class="sparepart-head-arrow"
                            >
                                ▼
                            </div>

                        </div>


                        {{-- =================================================
                             PILIHAN OTOMATIS SPAREPART MESIN
                        ================================================== --}}

                        <div class="sparepart-machine-panel">

                            <div class="sparepart-section-title">

                                Sparepart yang Sudah Ditentukan untuk Mesin

                            </div>


                            @if($machineSpareparts->count() > 0)

                                <div class="sparepart-table-wrap">

                                    <table class="sparepart-table">

                                        <thead>

                                            <tr>

                                                <th>
                                                    Kode
                                                </th>

                                                <th>
                                                    Nama Barang
                                                </th>

                                                <th>
                                                    Satuan
                                                </th>

                                                <th>
                                                    Stok
                                                </th>

                                                <th>
                                                    Qty
                                                </th>

                                                <th>
                                                    Aksi
                                                </th>

                                            </tr>

                                        </thead>


                                        <tbody>

                                            @foreach(
                                                $machineSpareparts
                                                as $sparepart
                                            )

                                                @php

                                                    $spId =
                                                        $sparepart->id;

                                                    $spCode =
                                                        $sparepart->kode_barang
                                                        ?? $sparepart->kode_sparepart
                                                        ?? '-';

                                                    $spName =
                                                        $sparepart->nama_barang
                                                        ?? $sparepart->nama_sparepart
                                                        ?? $sparepart->nama
                                                        ?? '-';

                                                    $spUnit =
                                                        $sparepart->satuan
                                                        ?? '-';

                                                    $spStock =
                                                        $sparepart->stok
                                                        ?? $sparepart->stock
                                                        ?? 0;

                                                @endphp


                                                <tr>

                                                    <td>
                                                        {{ $spCode }}
                                                    </td>

                                                    <td>

                                                        <div
                                                            class="sparepart-name"
                                                        >
                                                            {{ $spName }}
                                                        </div>

                                                    </td>

                                                    <td>
                                                        {{ $spUnit }}
                                                    </td>

                                                    <td>

                                                        <span class="
                                                            sparepart-stock
                                                            {{
                                                                (float) $spStock <= 0
                                                                    ? 'sparepart-stock-empty'
                                                                    : ''
                                                            }}
                                                        ">
                                                            {{ $spStock }}
                                                        </span>

                                                    </td>

                                                    <td>

                                                        <input
                                                            type="number"
                                                            min="0"
                                                            step="0.01"
                                                            class="sparepart-qty"
                                                            value="1"
                                                            data-stock-id="{{ $spId }}"
                                                        >

                                                    </td>

                                                    <td>

                                                        <button
                                                            type="button"
                                                            class="sparepart-btn"
                                                            onclick="
                                                                pilihSparepartMesin(
                                                                    @js($spId),
                                                                    @js($spCode),
                                                                    @js($spName),
                                                                    @js($spUnit),
                                                                    @js($spStock),
                                                                    this
                                                                )
                                                            "
                                                        >
                                                            Pilih
                                                        </button>

                                                    </td>

                                                </tr>

                                            @endforeach

                                        </tbody>

                                    </table>

                                </div>

                            @else

                                <div class="sparepart-empty">

                                    @if($workOrder->mesin)

                                        Belum ada sparepart yang
                                        ditentukan untuk mesin
                                        <strong>
                                            {{ $workOrder->mesin }}
                                        </strong>.

                                        <br>
                                        Gunakan
                                        <strong>
                                            + Tambahkan Sparepart
                                        </strong>
                                        untuk mencari dari Stok Barang.

                                    @else

                                        Mesin tidak ada pada WO ini.

                                        <br>
                                        Gunakan
                                        <strong>
                                            + Tambahkan Sparepart
                                        </strong>.

                                    @endif

                                </div>

                            @endif


                            {{-- =================================================
                                 SPAREPART YANG SUDAH DIPILIH
                            ================================================== --}}

                            <div class="used-sparepart-box">

                                <div class="used-sparepart-title">
                                    Sparepart yang Digunakan
                                </div>


                                <div
                                    id="usedSparepartContainer"
                                >

                                    <div
                                        id="usedSparepartEmpty"
                                        class="used-sparepart-empty"
                                    >
                                        Belum ada sparepart yang dipilih.
                                    </div>

                                </div>

                            </div>


                            {{-- =================================================
                                 TOMBOL TAMBAH
                            ================================================== --}}

                            <div style="margin-top:12px;">

                                <button
                                    type="button"
                                    class="btn-add-sparepart"
                                    onclick="openStockPanel()"
                                >
                                    + Tambahkan Sparepart
                                </button>

                            </div>

                        </div>


                        {{-- =================================================
                             TABEL SELURUH STOK
                        ================================================== --}}

                        <div
                            id="stockPanel"
                            class="stock-panel"
                        >

                            <div class="stock-panel-head">

                                <div class="stock-panel-title">
                                    Pilih dari Stok Barang
                                </div>

                                <div class="stock-panel-subtitle">
                                    Berikut seluruh sparepart yang tersedia
                                    pada tabel Stok Barang.
                                </div>

                            </div>


                            <input
                                type="text"
                                id="stockSearch"
                                class="stock-search"
                                placeholder="Cari kode, nama barang, atau satuan..."
                                oninput="filterStock()"
                            >


                            <div class="stock-table-wrap">

                                <table class="stock-table">

                                    <thead>

                                        <tr>

                                            <th>
                                                Kode
                                            </th>

                                            <th>
                                                Nama Barang
                                            </th>

                                            <th>
                                                Satuan
                                            </th>

                                            <th>
                                                Stok
                                            </th>

                                            <th>
                                                Qty
                                            </th>

                                            <th>
                                                Pilih
                                            </th>

                                        </tr>

                                    </thead>


                                    <tbody id="stockRows">

                                        @forelse(
                                            $stockSpareparts
                                            as $sparepart
                                        )

                                            @php

                                                $spId =
                                                    $sparepart->id;

                                                $spCode =
                                                    $sparepart->kode_barang
                                                    ?? $sparepart->kode_sparepart
                                                    ?? '-';

                                                $spName =
                                                    $sparepart->nama_barang
                                                    ?? $sparepart->nama_sparepart
                                                    ?? $sparepart->nama
                                                    ?? '-';

                                                $spUnit =
                                                    $sparepart->satuan
                                                    ?? '-';

                                                $spStock =
                                                    $sparepart->stok
                                                    ?? $sparepart->stock
                                                    ?? 0;

                                            @endphp


                                            <tr
                                                class="stock-row"
                                                data-search="{{
                                                    strtolower(
                                                        $spCode . ' ' .
                                                        $spName . ' ' .
                                                        $spUnit
                                                    )
                                                }}"
                                            >

                                                <td>
                                                    {{ $spCode }}
                                                </td>

                                                <td>

                                                    <div class="sparepart-name">
                                                        {{ $spName }}
                                                    </div>

                                                </td>

                                                <td>
                                                    {{ $spUnit }}
                                                </td>

                                                <td>

                                                    <span class="
                                                        sparepart-stock
                                                        {{
                                                            (float) $spStock <= 0
                                                                ? 'sparepart-stock-empty'
                                                                : ''
                                                        }}
                                                    ">
                                                        {{ $spStock }}
                                                    </span>

                                                </td>

                                                <td>

                                                    <input
                                                        type="number"
                                                        min="0"
                                                        step="0.01"
                                                        value="1"
                                                        class="sparepart-qty"
                                                        id="stockQty{{ $spId }}"
                                                    >

                                                </td>

                                                <td>

                                                    <button
                                                        type="button"
                                                        class="sparepart-btn"
                                                        onclick="
                                                            pilihSparepartStok(
                                                                @js($spId),
                                                                @js($spCode),
                                                                @js($spName),
                                                                @js($spUnit),
                                                                @js($spStock)
                                                            )
                                                        "
                                                    >
                                                        Pilih
                                                    </button>

                                                </td>

                                            </tr>

                                        @empty

                                            <tr>

                                                <td
                                                    colspan="6"
                                                    class="stock-empty-row"
                                                >
                                                    Tidak ada sparepart
                                                    pada Stok Barang.

                                                </td>

                                            </tr>

                                        @endforelse

                                    </tbody>

                                </table>

                            </div>

                        </div>

                    </div>


                    <div class="mtc-help">

                        Alurnya:
                        pilih sparepart yang sudah terkait dengan mesin.
                        Jika tidak ada, klik
                        <strong>+ Tambahkan Sparepart</strong>,
                        lalu pilih dari seluruh Stok Barang.

                    </div>

                </div>

            </div>


            {{-- =====================================================
                 FOTO PERBAIKAN
            ====================================================== --}}

            <div class="mtc-body">

                <div class="mtc-form-group">

                    <label
                        for="foto_perbaikan"
                        class="mtc-label-form"
                    >
                        Foto Perbaikan
                    </label>


                    <div class="mtc-upload">

                        <input
                            type="file"
                            id="foto_perbaikan"
                            name="foto_perbaikan"
                            class="mtc-input"
                            accept="image/*"
                        >

                        <div class="mtc-help">
                            Opsional. Maksimal 5 MB.
                        </div>


                        @if($workOrder->foto_perbaikan)

                            <div class="mtc-photo">

                                <img
                                    src="{{ asset(
                                        'storage/' .
                                        $workOrder->foto_perbaikan
                                    ) }}"
                                    alt="Foto Perbaikan"
                                >

                            </div>

                        @endif

                    </div>

                </div>

            </div>


            {{-- =====================================================
                 FOOTER
            ====================================================== --}}

            <div class="mtc-footer">

                <a
                    href="{{ route('work-orders.admin.index') }}"
                    class="mtc-btn mtc-btn-gray"
                >
                    Batal
                </a>

                <button
                    type="submit"
                    class="mtc-btn mtc-btn-primary"
                    id="saveMaintenance"
                >
                    Simpan Tindak Lanjut
                </button>

            </div>

        </form>

    </div>

</div>


<script>

    /*
    |--------------------------------------------------------------------------
    | STATUS
    |--------------------------------------------------------------------------
    */

    const statusSelect =
        document.getElementById('status');

    const keterangan =
        document.getElementById('keterangan');

    function updateKeteranganRequirement()
    {
        if (
            !statusSelect ||
            !keterangan
        ) {
            return;
        }

        const requiredStatus = [
            'DITOLAK',
            'PENDING',
            'SERVICE LUAR'
        ].includes(
            statusSelect.value
        );

        keterangan.required =
            requiredStatus;

        if (requiredStatus) {

            keterangan.placeholder =
                'Wajib diisi karena status ' +
                statusSelect.value +
                '.';

        } else {

            keterangan.placeholder =
                'Wajib diisi jika DITOLAK, PENDING, atau SERVICE LUAR.';

        }
    }


    if (statusSelect) {

        statusSelect.addEventListener(
            'change',
            updateKeteranganRequirement
        );

        updateKeteranganRequirement();

    }


    /*
    |--------------------------------------------------------------------------
    | KLIK KOLOM SPAREPART MESIN
    |--------------------------------------------------------------------------
    */

    function toggleMachineSpareparts()
    {
        const selector =
            document.getElementById(
                'sparepartSelector'
            );

        if (!selector) {
            return;
        }

        selector.classList.toggle(
            'open'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | BUKA TABEL SEMUA STOK
    |--------------------------------------------------------------------------
    */

    function openStockPanel()
    {
        const panel =
            document.getElementById(
                'stockPanel'
            );

        if (!panel) {
            return;
        }

        panel.classList.add(
            'open'
        );

        const search =
            document.getElementById(
                'stockSearch'
            );

        if (search) {
            search.focus();
        }

        panel.scrollIntoView({
            behavior: 'smooth',
            block: 'nearest'
        });
    }


    /*
    |--------------------------------------------------------------------------
    | FILTER STOK
    |--------------------------------------------------------------------------
    */

    function filterStock()
    {
        const search =
            document.getElementById(
                'stockSearch'
            );

        const keyword =
            (
                search?.value ||
                ''
            )
                .toLowerCase()
                .trim();

        const rows =
            document.querySelectorAll(
                '.stock-row'
            );

        rows.forEach(
            function(row)
            {

                const searchText =
                    row.dataset.search ||
                    '';

                row.style.display =
                    searchText.includes(
                        keyword
                    )
                        ? ''
                        : 'none';

            }
        );
    }


    /*
    |--------------------------------------------------------------------------
    | PILIH SPAREPART MESIN
    |--------------------------------------------------------------------------
    */

    function pilihSparepartMesin(
        id,
        code,
        name,
        unit,
        stock,
        button
    )
    {
        const qtyInput =
            button
                .closest('tr')
                ?.querySelector(
                    '.sparepart-qty'
                );

        const qty =
            qtyInput &&
            parseFloat(
                qtyInput.value
            ) > 0
                ? qtyInput.value
                : 1;

        addUsedSparepart(
            id,
            code,
            name,
            unit,
            stock,
            qty
        );
    }


    /*
    |--------------------------------------------------------------------------
    | PILIH DARI STOK
    |--------------------------------------------------------------------------
    */

    function pilihSparepartStok(
        id,
        code,
        name,
        unit,
        stock
    )
    {
        const qtyInput =
            document.getElementById(
                'stockQty' + id
            );

        const qty =
            qtyInput &&
            parseFloat(
                qtyInput.value
            ) > 0
                ? qtyInput.value
                : 1;

        addUsedSparepart(
            id,
            code,
            name,
            unit,
            stock,
            qty
        );


        /*
        |--------------------------------------------------------------------------
        | Tutup tabel stok setelah pilih
        |--------------------------------------------------------------------------
        */

        const stockPanel =
            document.getElementById(
                'stockPanel'
            );

        if (stockPanel) {
            stockPanel.classList.remove(
                'open'
            );
        }
    }


    /*
    |--------------------------------------------------------------------------
    | MASUKKAN KE TABEL SPAREPART YANG DIGUNAKAN
    |--------------------------------------------------------------------------
    */

    function addUsedSparepart(
        id,
        code,
        name,
        unit,
        stock,
        qty
    )
    {
        const container =
            document.getElementById(
                'usedSparepartContainer'
            );

        const empty =
            document.getElementById(
                'usedSparepartEmpty'
            );

        if (!container) {
            return;
        }


        /*
        |--------------------------------------------------------------------------
        | Jika sudah ada, hanya update Qty
        |--------------------------------------------------------------------------
        */

        const existing =
            container.querySelector(
                '[data-sparepart-id="' +
                id +
                '"]'
            );

        if (existing) {

            const existingQty =
                existing.querySelector(
                    '.selected-qty'
                );

            if (existingQty) {

                existingQty.value =
                    qty;

                existingQty.focus();

                existingQty.select();

            }

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | Hapus pesan kosong
        |--------------------------------------------------------------------------
        */

        if (empty) {
            empty.remove();
        }


        /*
        |--------------------------------------------------------------------------
        | Buat tabel jika belum ada
        |--------------------------------------------------------------------------
        */

        let table =
            document.getElementById(
                'usedSparepartTable'
            );

        let tbody =
            document.getElementById(
                'usedSparepartTbody'
            );


        if (!table) {

            table =
                document.createElement(
                    'table'
                );

            table.id =
                'usedSparepartTable';

            table.className =
                'sparepart-table';


            table.innerHTML =
                `
                <thead>

                    <tr>

                        <th>
                            Kode
                        </th>

                        <th>
                            Nama Barang
                        </th>

                        <th>
                            Satuan
                        </th>

                        <th>
                            Stok
                        </th>

                        <th>
                            Qty Digunakan
                        </th>

                        <th>
                            Aksi
                        </th>

                    </tr>

                </thead>

                <tbody
                    id="usedSparepartTbody"
                >
                </tbody>
                `;


            const wrap =
                document.createElement(
                    'div'
                );

            wrap.className =
                'sparepart-table-wrap';

            wrap.appendChild(
                table
            );

            container.appendChild(
                wrap
            );


            tbody =
                document.getElementById(
                    'usedSparepartTbody'
                );

        }


        if (!tbody) {
            return;
        }


        /*
        |--------------------------------------------------------------------------
        | Baris baru
        |--------------------------------------------------------------------------
        */

        const row =
            document.createElement(
                'tr'
            );

        row.dataset.sparepartId =
            id;


        row.innerHTML =
            `
            <td>
                ${escapeHtml(code)}
            </td>

            <td>

                <div class="sparepart-name">
                    ${escapeHtml(name)}
                </div>

            </td>

            <td>
                ${escapeHtml(unit)}
            </td>

            <td>

                <span class="
                    sparepart-stock
                    ${
                        parseFloat(stock) <= 0
                            ? 'sparepart-stock-empty'
                            : ''
                    }
                ">
                    ${escapeHtml(stock)}
                </span>

            </td>

            <td>

                <input
                    type="number"
                    min="0"
                    step="0.01"
                    class="sparepart-qty selected-qty"
                    name="sparepart_qty[${id}]"
                    value="${escapeHtml(qty)}"
                >

            </td>

            <td>

                <button
                    type="button"
                    class="
                        sparepart-btn
                        sparepart-btn-danger
                    "
                    onclick="removeUsedSparepart(this)"
                >
                    Hapus
                </button>

            </td>
            `;


        tbody.appendChild(
            row
        );

    }


    /*
    |--------------------------------------------------------------------------
    | HAPUS DARI TABEL DIGUNAKAN
    |--------------------------------------------------------------------------
    */

    function removeUsedSparepart(
        button
    )
    {
        const row =
            button.closest('tr');

        if (!row) {
            return;
        }

        row.remove();


        const tbody =
            document.getElementById(
                'usedSparepartTbody'
            );


        if (
            tbody &&
            tbody.children.length === 0
        ) {

            const table =
                document.getElementById(
                    'usedSparepartTable'
                );

            if (table) {
                table.closest(
                    '.sparepart-table-wrap'
                )?.remove();
            }


            const container =
                document.getElementById(
                    'usedSparepartContainer'
                );


            if (container) {

                container.innerHTML =
                    `
                    <div
                        id="usedSparepartEmpty"
                        class="used-sparepart-empty"
                    >
                        Belum ada sparepart yang dipilih.
                    </div>
                    `;

            }

        }

    }


    /*
    |--------------------------------------------------------------------------
    | ESCAPE HTML
    |--------------------------------------------------------------------------
    */

    function escapeHtml(
        value
    )
    {
        return String(
            value ?? ''
        )
            .replace(
                /&/g,
                '&amp;'
            )
            .replace(
                /</g,
                '&lt;'
            )
            .replace(
                />/g,
                '&gt;'
            )
            .replace(
                /"/g,
                '&quot;'
            )
            .replace(
                /'/g,
                '&#039;'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | DOUBLE SUBMIT
    |--------------------------------------------------------------------------
    */

    const maintenanceForm =
        document.getElementById(
            'maintenanceForm'
        );

    const saveMaintenance =
        document.getElementById(
            'saveMaintenance'
        );


    if (
        maintenanceForm &&
        saveMaintenance
    ) {

        maintenanceForm.addEventListener(
            'submit',
            function()
            {

                saveMaintenance.disabled =
                    true;

                saveMaintenance.textContent =
                    'Menyimpan...';

            }
        );

    }

</script>

@endsection