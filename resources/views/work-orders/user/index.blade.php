@extends('layouts.app')

@section('title', 'Work Order')

@section('page_title', 'Work Order')

@section(
    'page_subtitle',
    'Daftar seluruh Work Order'
)

@push('styles')

<style>

    * {
        box-sizing: border-box;
    }

    .wo-page {
        padding: 20px 0 30px;
    }


    /* =====================================================
       HEADER
    ====================================================== */

    .wo-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
        margin-bottom: 22px;
    }

    .wo-header-left h1 {
        margin: 0 0 6px;
        font-size: 28px;
        color: var(--pds-ink);
    }

    .wo-header-left p {
        margin: 0;
        color: var(--pds-muted);
        font-size: 14px;
    }

    .wo-header-actions {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;

        min-height: 40px;

        padding: 10px 16px;

        border: none;
        border-radius: 8px;

        cursor: pointer;

        font-size: 13px;
        font-weight: 600;

        text-decoration: none;

        transition: .15s ease;
    }

    .btn-add {
        background: #2563eb;
        color: #ffffff;
    }

    .btn-add:hover {
        background: #1d4ed8;
    }

    .btn-print {
        background: #ecfdf5;
        color: #047857;
        border: 1px solid #a7f3d0;
    }

    .btn-print:hover {
        background: #d1fae5;
    }


    /* =====================================================
       ALERT
    ====================================================== */

    .alert {
        padding: 12px 15px;
        margin-bottom: 20px;

        border-radius: 8px;

        font-size: 13px;
    }

    .alert-success {
        background: #ecfdf5;
        border: 1px solid #a7f3d0;
        color: #047857;
    }

    .alert-error {
        background: #fef2f2;
        border: 1px solid #fecaca;
        color: #b91c1c;
    }


    /* =====================================================
       FILTER
    ====================================================== */

    .filter-card {
        margin-bottom: 18px;
        padding: 18px;

        background: var(--pds-card);

        border-radius: 12px;

        box-shadow:
            0 3px 12px rgba(0,0,0,.06);
    }

    .filter-grid {
        display: grid;

        grid-template-columns:
            minmax(220px, 1.5fr)
            repeat(4, minmax(150px, 1fr));

        gap: 10px;

        align-items: end;
    }

    .filter-group {
        display: flex;
        flex-direction: column;

        min-width: 0;
    }

    .filter-label {
        margin-bottom: 6px;

        color: var(--pds-ink-2);

        font-size: 11px;
        font-weight: 700;
    }

    .filter-input,
    .filter-select {
        width: 100%;
        height: 40px;

        padding: 0 11px;

        border: 1px solid var(--pds-line-2);
        border-radius: 8px;

        background: var(--pds-card);
        color: var(--pds-ink);

        font-size: 12px;
    }

    .filter-input:focus,
    .filter-select:focus {
        outline: none;

        border-color: #2563eb;

        box-shadow:
            0 0 0 3px
            rgba(37,99,235,.08);
    }

    .filter-bottom {
        display: flex;

        align-items: center;
        justify-content: space-between;

        gap: 12px;

        margin-top: 12px;
    }

    .filter-result {
        color: var(--pds-muted);
        font-size: 11px;
    }

    .filter-reset {
        height: 36px;

        padding: 0 14px;

        border: 1px solid var(--pds-line-2);
        border-radius: 8px;

        background: var(--pds-soft-2);
        color: var(--pds-ink-2);

        cursor: pointer;

        font-size: 12px;
        font-weight: 600;

        white-space: nowrap;
    }

    .filter-reset:hover {
        background: var(--pds-line);
    }


    /* =====================================================
       CARD
    ====================================================== */

    .card {
        background: var(--pds-card);

        border-radius: 12px;

        box-shadow:
            0 3px 12px rgba(0,0,0,.06);

        overflow: hidden;
    }

    .card-header {
        padding: 18px 20px;

        border-bottom:
            1px solid var(--pds-line);
    }

    .card-title {
        font-size: 17px;
        font-weight: 700;

        color: var(--pds-ink);
    }

    .card-subtitle {
        margin-top: 5px;

        color: var(--pds-muted);

        font-size: 12px;
    }


    /* =====================================================
       TABLE
    ====================================================== */

    .table-wrap {
        width: 100%;

        overflow-x: auto;
    }

    .wo-table {
        width: 100%;

        min-width: 1700px;

        border-collapse: collapse;

        table-layout: fixed;
    }

    .wo-table th,
    .wo-table td {
        padding: 11px 10px;

        border-bottom:
            1px solid var(--pds-line);

        text-align: center;

        font-size: 12px;

        vertical-align: middle;

        word-break: break-word;
    }

    .wo-table th {
        background: var(--pds-soft);

        color: var(--pds-ink-2);

        font-weight: 700;

        white-space: nowrap;
    }

    .wo-table tbody tr:hover {
        background: var(--pds-soft);
    }

    .wo-table tbody tr.filter-hidden {
        display: none;
    }

    .wo-table th:nth-child(1),
    .wo-table td:nth-child(1) {
        width: 48px;
    }

    .wo-table th:nth-child(2),
    .wo-table td:nth-child(2) {
        width: 125px;
    }

    .wo-table th:nth-child(3),
    .wo-table td:nth-child(3) {
        width: 90px;
    }

    .wo-table th:nth-child(4),
    .wo-table td:nth-child(4) {
        width: 75px;
    }

    .wo-table th:nth-child(5),
    .wo-table td:nth-child(5) {
        width: 115px;
    }

    .wo-table th:nth-child(6),
    .wo-table td:nth-child(6) {
        width: 190px;
    }

    .wo-table th:nth-child(7),
    .wo-table td:nth-child(7) {
        width: 145px;
    }

    .wo-table th:nth-child(8),
    .wo-table td:nth-child(8) {
        width: 120px;
    }

    .wo-table th:nth-child(9),
    .wo-table td:nth-child(9) {
        width: 125px;
    }

    .wo-table th:nth-child(10),
    .wo-table td:nth-child(10) {
        width: 180px;
    }

    .wo-table th:nth-child(11),
    .wo-table td:nth-child(11) {
        width: 100px;
    }

    .wo-table th:nth-child(12),
    .wo-table td:nth-child(12) {
        width: 105px;
    }

    .wo-table th:nth-child(13),
    .wo-table td:nth-child(13) {
        width: 145px;
    }

    .wo-number {
        display: inline-block;

        font-weight: 700;

        color: var(--pds-ink);

        white-space: nowrap;
    }

    .creator-name {
        display: block;

        margin-top: 4px;

        color: var(--pds-muted);

        font-size: 10px;
    }

    .row-number {
        color: var(--pds-muted);

        font-weight: 700;
    }


    /* =====================================================
       STATUS
    ====================================================== */

    .status {
        display: inline-flex;

        align-items: center;
        justify-content: center;

        min-width: 85px;

        padding: 5px 9px;

        border-radius: 20px;

        font-size: 10px;
        font-weight: 700;

        white-space: nowrap;
    }

    .status-blue {
        background: #dbeafe;

        color: #1d4ed8;
    }

    .status-gray {
        background: var(--pds-line);

        color: var(--pds-muted);
    }

    .status-red {
        background: #fee2e2;

        color: #b91c1c;
    }


    /* =====================================================
       PRIORITY
    ====================================================== */

    .priority {
        display: inline-flex;

        align-items: center;
        justify-content: center;

        min-width: 72px;

        padding: 5px 9px;

        border-radius: 20px;

        font-size: 10px;
        font-weight: 700;

        white-space: nowrap;
    }

    .priority-normal {
        background: #dcfce7;

        color: #166534;
    }

    .priority-urgent {
        background: #fef3c7;

        color: #92400e;
    }

    .priority-emergency {
        background: #fee2e2;

        color: #b91c1c;
    }


    /* =====================================================
       AKSI
    ====================================================== */

    .actions {
        display: flex;

        align-items: center;
        justify-content: center;

        gap: 6px;
    }

    .action-button {
        width: 34px;
        height: 34px;

        display: inline-flex;

        align-items: center;
        justify-content: center;

        padding: 0;

        border:
            1px solid transparent;

        border-radius: 8px;

        background: transparent;

        text-decoration: none;

        cursor: pointer;

        font-size: 16px;

        line-height: 1;

        transition:
            background .15s ease,
            color .15s ease,
            border-color .15s ease,
            transform .15s ease;
    }

    .action-button:hover {
        transform:
            translateY(-1px);
    }

    .action-view {
        background: var(--pds-soft-2);

        color: var(--pds-ink-2);

        border-color: var(--pds-line);
    }

    .action-view:hover {
        background: var(--pds-line);

        color: var(--pds-ink);
    }

    .action-edit {
        background: #e0f2fe;

        color: #0369a1;

        border-color: #bae6fd;
    }

    .action-edit:hover {
        background: #bae6fd;

        color: #075985;
    }

    .action-delete {
        background: #fef2f2;

        color: #dc2626;

        border-color: #fecaca;
    }

    .action-delete:hover {
        background: #fee2e2;

        color: #991b1b;
    }


    /* =====================================================
       DELETE MODAL
    ====================================================== */

    .delete-modal {
        position: fixed;

        inset: 0;

        z-index: 999999;

        display: none;

        align-items: center;
        justify-content: center;

        padding: 20px;

        background:
            rgba(15,23,42,.55);

        backdrop-filter:
            blur(5px);
    }

    .delete-modal.active {
        display: flex;
    }

    .delete-modal-box {
        width: 420px;

        max-width:
            calc(100vw - 30px);

        background: var(--pds-card);

        border-radius: 16px;

        box-shadow:
            0 25px 70px
            rgba(15,23,42,.25);

        overflow: hidden;

        animation:
            deleteModalIn
            .18s ease;
    }

    @keyframes deleteModalIn {

        from {

            opacity: 0;

            transform:
                translateY(8px)
                scale(.98);
        }

        to {

            opacity: 1;

            transform:
                translateY(0)
                scale(1);
        }
    }

    .delete-modal-content {
        padding:
            28px 25px 20px;

        text-align: center;
    }

    .delete-modal-icon {
        width: 60px;
        height: 60px;

        margin:
            0 auto 15px;

        display: flex;

        align-items: center;
        justify-content: center;

        border-radius: 50%;

        background: #fef2f2;

        color: #dc2626;

        font-size: 28px;
    }

    .delete-modal-title {
        margin: 0;

        color: var(--pds-ink);

        font-size: 18px;

        font-weight: 800;
    }

    .delete-modal-message {
        margin-top: 10px;

        color: var(--pds-muted);

        font-size: 13px;

        line-height: 1.6;
    }

    .delete-modal-message strong {
        color: var(--pds-ink);
    }

    .delete-modal-actions {
        display: flex;

        gap: 10px;

        padding:
            16px 20px 20px;

        border-top:
            1px solid var(--pds-line);
    }

    .delete-modal-actions form {
        flex: 1;

        margin: 0;
    }

    .delete-modal-actions button {
        width: 100%;

        min-height: 42px;

        border-radius: 9px;

        cursor: pointer;

        font-size: 12px;

        font-weight: 700;
    }

    .delete-modal-cancel {
        flex: 1;

        border:
            1px solid var(--pds-line-2);

        background: var(--pds-card);

        color: var(--pds-ink-2);
    }

    .delete-modal-cancel:hover {
        background: var(--pds-soft-2);
    }

    .delete-modal-confirm {
        border:
            1px solid #dc2626;

        background:
            linear-gradient(
                135deg,
                #ef4444,
                #dc2626
            );

        color: #ffffff;
    }

    .delete-modal-confirm:hover {
        background:
            linear-gradient(
                135deg,
                #dc2626,
                #b91c1c
            );
    }


    /* =====================================================
       EMPTY
    ====================================================== */

    .empty {
        text-align: center;

        padding:
            45px 20px !important;

        color: var(--pds-muted);
    }


    /* =====================================================
       PRINT
    ====================================================== */

    .print-only {
        display: none;
    }

    @media print {

        @page {
            size: A4 landscape;
            margin: 8mm;
        }

        html,
        body {
            margin: 0 !important;
            padding: 0 !important;

            background: #ffffff !important;
        }

        .sidebar,
        .topbar,
        .ai-assistant-button,
        .ai-assistant-panel,
        .crud-toast-container,
        .wo-header,
        .filter-card,
        .card-header,
        .no-print,
        nav,
        aside,
        footer {
            display: none !important;
        }

        .main {
            width: 100% !important;

            margin-left: 0 !important;
        }

        .content {
            padding: 0 !important;
        }

        .wo-page {
            width: 100% !important;

            padding: 0 !important;

            margin: 0 !important;
        }

        .print-only {
            display: block !important;

            margin-bottom: 10px !important;
        }

        .print-title {
            margin: 0;

            font-size: 15pt;

            font-weight: 700;

            text-align: center;
        }

        .print-subtitle {
            margin-top: 3px;

            font-size: 8pt;

            text-align: center;

            color: #4b5563;
        }

        .card {
            width: 100% !important;

            box-shadow: none !important;

            border-radius: 0 !important;

            overflow: visible !important;
        }

        .table-wrap {
            width: 100% !important;

            overflow: visible !important;
        }

        .wo-table {
            width: 100% !important;

            min-width: 0 !important;

            table-layout: fixed !important;

            border-collapse:
                collapse !important;
        }

        .wo-table th:last-child,
        .wo-table td:last-child {
            display: none !important;
        }

        .wo-table th,
        .wo-table td {
            border:
                1px solid #9ca3af !important;

            padding:
                4px 5px !important;

            font-size:
                7pt !important;

            line-height:
                1.2 !important;

            text-align:
                center !important;

            vertical-align:
                middle !important;

            word-break:
                break-word !important;
        }

        .wo-table th {
            background:
                #f3f4f6 !important;

            color:
                #111827 !important;

            font-weight:
                700 !important;
        }

        .wo-table tbody tr:hover {
            background:
                #ffffff !important;
        }

        .wo-number {
            font-weight:
                700 !important;
        }

        .creator-name {
            font-size:
                6.5pt !important;

            color:
                #374151 !important;
        }

        .status,
        .priority {
            min-width:
                0 !important;

            padding:
                2px 4px !important;

            border:
                none !important;

            background:
                transparent !important;

            color:
                #111827 !important;

            font-size:
                7pt !important;
        }
    }


    /* =====================================================
       RESPONSIVE
    ====================================================== */

    @media (max-width: 1100px) {

        .filter-grid {
            grid-template-columns:
                repeat(
                    2,
                    minmax(180px, 1fr)
                );
        }

        .filter-bottom {
            align-items:
                flex-start;
        }
    }

    @media (max-width: 700px) {

        .wo-page {
            padding: 12px;
        }

        .wo-header {
            align-items:
                flex-start;

            flex-direction:
                column;
        }

        .wo-header-actions {
            width: 100%;

            flex-direction:
                column;
        }

        .wo-header-actions .btn {
            width: 100%;
        }

        .filter-grid {
            grid-template-columns:
                1fr;
        }

        .filter-bottom {
            align-items:
                stretch;

            flex-direction:
                column;
        }

        .filter-reset {
            width:
                fit-content;
        }

        .actions {
            gap:
                4px;
        }

        .action-button {
            width:
                32px;

            height:
                32px;
        }

        .delete-modal-box {
            width:
                100%;

            max-width:
                380px;
        }
    }

</style>

@endpush


@section('content')

<div class="wo-page">


    {{-- =====================================================
         HEADER
    ====================================================== --}}

    <div class="wo-header no-print">

        <div class="wo-header-left">

            <h1>
                Work Order
            </h1>

            <p>
                Menampilkan seluruh Work Order.
                Tombol aksi tersedia pada setiap Work Order.
            </p>

        </div>


        <div class="wo-header-actions">

            <a
                href="{{ route('work-orders.create') }}"
                class="btn btn-add"
            >
                + Tambah WO
            </a>


            <button
                type="button"
                class="btn btn-print"
                onclick="printWorkOrderTable()"
            >
                <x-icon name="print"></x-icon> Print Tabel
            </button>

        </div>

    </div>


    {{-- =====================================================
         HEADER PRINT
    ====================================================== --}}

    <div class="print-only">

        <div class="print-title">
            DAFTAR WORK ORDER
        </div>

        <div class="print-subtitle">
            Seluruh Work Order
        </div>

    </div>


    {{-- =====================================================
         SUCCESS
    ====================================================== --}}

    @if(session('success'))

        <div class="alert alert-success no-print">
            {{ session('success') }}
        </div>

    @endif


    {{-- =====================================================
         ERROR
    ====================================================== --}}

    @if($errors->any())

        <div class="alert alert-error no-print">

            <strong>
                Data belum dapat disimpan:
            </strong>

            <ul style="margin:7px 0 0 18px;">

                @foreach($errors->all() as $error)

                    <li>
                        {{ $error }}
                    </li>

                @endforeach

            </ul>

        </div>

    @endif


    {{-- =====================================================
         FILTER
    ====================================================== --}}

    <div class="filter-card no-print">

        <div class="filter-grid">

            {{-- NOMOR WO --}}

            <div class="filter-group">

                <label
                    for="filterNoWo"
                    class="filter-label"
                >
                    Nomor WO
                </label>

                <input
                    type="text"
                    id="filterNoWo"
                    class="filter-input"
                    placeholder="Cari nomor WO..."
                    autocomplete="off"
                >

            </div>


            {{-- BULAN --}}

            <div class="filter-group">

                <label
                    for="filterBulan"
                    class="filter-label"
                >
                    Bulan
                </label>

                <select
                    id="filterBulan"
                    class="filter-select"
                >

                    <option value="">
                        Semua Bulan
                    </option>

                    <option value="1">Januari</option>
                    <option value="2">Februari</option>
                    <option value="3">Maret</option>
                    <option value="4">April</option>
                    <option value="5">Mei</option>
                    <option value="6">Juni</option>
                    <option value="7">Juli</option>
                    <option value="8">Agustus</option>
                    <option value="9">September</option>
                    <option value="10">Oktober</option>
                    <option value="11">November</option>
                    <option value="12">Desember</option>

                </select>

            </div>


            {{-- TAHUN --}}

            <div class="filter-group">

                <label
                    for="filterTahun"
                    class="filter-label"
                >
                    Tahun
                </label>

                <select
                    id="filterTahun"
                    class="filter-select"
                >

                    <option value="">
                        Semua Tahun
                    </option>

                    @for(
                        $tahun = 2026;
                        $tahun <= 2030;
                        $tahun++
                    )

                        <option value="{{ $tahun }}">
                            {{ $tahun }}
                        </option>

                    @endfor

                </select>

            </div>


            {{-- STATUS --}}

            <div class="filter-group">

                <label
                    for="filterStatus"
                    class="filter-label"
                >
                    Status
                </label>

                <select
                    id="filterStatus"
                    class="filter-select"
                >

                    <option value="">
                        Semua Status
                    </option>

                    <option value="OPEN">
                        OPEN
                    </option>

                    <option value="DITERIMA">
                        DITERIMA
                    </option>

                    <option value="DITOLAK">
                        DITOLAK
                    </option>

                    <option value="SCHEDULED">
                        SCHEDULED
                    </option>

                    <option value="IN PROGRESS">
                        IN PROGRESS
                    </option>

                    <option value="PENDING">
                        PENDING
                    </option>

                    <option value="SERVICE LUAR">
                        SERVICE LUAR
                    </option>

                    <option value="CLOSE">
                        CLOSE
                    </option>

                </select>

            </div>


            {{-- PRIORITAS --}}

            <div class="filter-group">

                <label
                    for="filterPriority"
                    class="filter-label"
                >
                    Prioritas
                </label>

                <select
                    id="filterPriority"
                    class="filter-select"
                >

                    <option value="">
                        Semua Prioritas
                    </option>

                    <option value="EMERGENCY">
                        EMERGENCY
                    </option>

                    <option value="URGENT">
                        URGENT
                    </option>

                    <option value="NORMAL">
                        NORMAL
                    </option>

                </select>

            </div>

        </div>


        <div class="filter-bottom">

            <div
                id="filterResult"
                class="filter-result"
            >
                Menampilkan semua Work Order.
            </div>


            <button
                type="button"
                id="resetFilter"
                class="filter-reset"
            >
                Reset Filter
            </button>

        </div>

    </div>


    {{-- =====================================================
         TABLE
    ====================================================== --}}

    <div class="card">

        <div class="card-header no-print">

            <div class="card-title">
                Daftar Seluruh Work Order
            </div>

            <div class="card-subtitle">
                Semua Work Order ditampilkan.
            </div>

        </div>


        <div class="table-wrap">

            <table class="wo-table">

                <thead>

                    <tr>

                        <th>
                            No
                        </th>

                        <th>
                            No. WO
                        </th>

                        <th>
                            Tanggal
                        </th>

                        <th>
                            Jam
                        </th>

                        <th>
                            Departemen
                        </th>

                        <th>
                            Kategori
                        </th>

                        <th>
                            Ditujukan
                        </th>

                        <th>
                            Area / Line
                        </th>

                        <th>
                            Mesin
                        </th>

                        <th>
                            Job
                        </th>

                        <th>
                            Prioritas
                        </th>

                        <th>
                            Status
                        </th>

                        <th class="no-print">
                            Aksi
                        </th>

                    </tr>

                </thead>


                <tbody id="workOrderTableBody">

                @forelse(
                    $workOrders
                    as $index
                    => $workOrder
                )

                    @php

                        $status =
                            strtoupper(
                                trim(
                                    $workOrder->status ?? ''
                                )
                            );

                        $priority =
                            strtoupper(
                                trim(
                                    $workOrder->priority ?? ''
                                )
                            );

                        $bulanFilter =
                            $workOrder->tanggal_kerusakan
                                ? \Carbon\Carbon::parse(
                                    $workOrder->tanggal_kerusakan
                                )->format('n')
                                : '';

                        $tahunFilter =
                            $workOrder->tanggal_kerusakan
                                ? \Carbon\Carbon::parse(
                                    $workOrder->tanggal_kerusakan
                                )->format('Y')
                                : '';

                        $statusForFilter =
                            $status === 'IN PROSES'
                                ? 'IN PROGRESS'
                                : (
                                    in_array(
                                        $status,
                                        [
                                            'CLOSE',
                                            'SELESAI'
                                        ],
                                        true
                                    )
                                        ? 'CLOSE'
                                        : $status
                                );

                    @endphp


                    <tr
                        class="work-order-row"

                        data-no-wo="{{ $workOrder->no_wo ?? '' }}"

                        data-status="{{ $statusForFilter }}"

                        data-priority="{{ $priority }}"

                        data-month="{{ $bulanFilter }}"

                        data-year="{{ $tahunFilter }}"
                    >


                        {{-- NO --}}

                        <td class="row-number">
                            {{ $index + 1 }}
                        </td>


                        {{-- NO WO --}}

                        <td>

                            <span class="wo-number">
                                {{ $workOrder->no_wo ?: '-' }}
                            </span>

                            @if(
                                !empty(
                                    $workOrder->dibuat_oleh
                                )
                            )

                                <span class="creator-name">
                                    {{ $workOrder->dibuat_oleh }}
                                </span>

                            @endif

                        </td>


                        {{-- TANGGAL --}}

                        <td>

                            @if(
                                $workOrder->tanggal_kerusakan
                            )

                                {{
                                    \Carbon\Carbon::parse(
                                        $workOrder->tanggal_kerusakan
                                    )->format('d-m-Y')
                                }}

                            @else

                                -

                            @endif

                        </td>


                        {{-- JAM --}}

                        <td>
                            {{ $workOrder->jam_kerusakan ?: '-' }}
                        </td>


                        {{-- DEPARTEMEN --}}

                        <td>
                            {{ $workOrder->departemen ?: '-' }}
                        </td>


                        {{-- KATEGORI --}}

                        <td>
                            {{ $workOrder->kategori ?: '-' }}
                        </td>


                        {{-- DITUJUKAN --}}

                        <td>
                            {{ $workOrder->tujuan ?: '-' }}
                        </td>


                        {{-- AREA --}}

                        <td>
                            {{ $workOrder->area ?: '-' }}
                        </td>


                        {{-- MESIN --}}

                        <td>
                            {{ $workOrder->mesin ?: '-' }}
                        </td>


                        {{-- JOB --}}

                        <td>
                            {{ $workOrder->job ?: '-' }}
                        </td>


                        {{-- PRIORITAS --}}

                        <td>

                            @if(
                                $priority === 'EMERGENCY'
                            )

                                <span
                                    class="priority priority-emergency"
                                >
                                    EMERGENCY
                                </span>

                            @elseif(
                                $priority === 'URGENT'
                            )

                                <span
                                    class="priority priority-urgent"
                                >
                                    URGENT
                                </span>

                            @elseif(
                                $priority === 'NORMAL'
                            )

                                <span
                                    class="priority priority-normal"
                                >
                                    NORMAL
                                </span>

                            @else

                                -

                            @endif

                        </td>


                        {{-- STATUS --}}

                        <td>

                            @if(
                                in_array(
                                    $statusForFilter,
                                    [
                                        'OPEN',
                                        'DITERIMA',
                                        'SCHEDULED',
                                        'IN PROGRESS'
                                    ],
                                    true
                                )
                            )

                                <span
                                    class="status status-blue"
                                >
                                    {{ $statusForFilter }}
                                </span>

                            @elseif(
                                in_array(
                                    $statusForFilter,
                                    [
                                        'DITOLAK',
                                        'PENDING',
                                        'SERVICE LUAR'
                                    ],
                                    true
                                )
                            )

                                <span
                                    class="status status-gray"
                                >
                                    {{ $statusForFilter }}
                                </span>

                            @elseif(
                                $statusForFilter === 'CLOSE'
                            )

                                <span
                                    class="status status-red"
                                >
                                    CLOSE
                                </span>

                            @else

                                <span
                                    class="status status-gray"
                                >
                                    {{
                                        $workOrder->status
                                            ?: '-'
                                    }}
                                </span>

                            @endif

                        </td>


                        {{-- =================================================
                             AKSI
                        ================================================== --}}

                        <td class="no-print">

                            @php
                                $viewerRole = \App\Support\DepartmentAccess::normalizeRole(
                                    auth()->user()->role ?? ''
                                );

                                $viewerIsAdmin =
                                    $viewerRole === \App\Support\DepartmentAccess::ADMINISTRATOR;

                                $viewerIsMaintenanceStaff =
                                    \App\Support\DepartmentAccess::isMaintenanceStaff(auth()->user());

                                $_canEdit = \App\Support\DepartmentAccess::canEditWorkOrder(auth()->user(), $workOrder);
                                $_canDelete = \App\Support\DepartmentAccess::canDeleteWorkOrder(auth()->user(), $workOrder);

                                $viewerOwnsWo =
                                    strcasecmp(
                                        trim(
                                            (string) (
                                                $workOrder->dibuat_oleh ?? ''
                                            )
                                        ),
                                        trim(
                                            (string) (
                                                auth()->user()->name ?? ''
                                            )
                                        )
                                    ) === 0;
                            @endphp

                            <div class="actions">

                                {{-- LIHAT --}}

                                <a
                                    href="{{ route(
                                        'work-orders.show',
                                        $workOrder->id
                                    ) }}"
                                    class="action-button action-view"
                                    title="Lihat Work Order"
                                    aria-label="Lihat Work Order"
                                >
                                    <x-icon name="eye"></x-icon>
                                </a>

                                {{-- EDIT / TINDAK LANJUT SESUAI ROLE --}}

                                @if ($_canEdit)
                                    @if ($viewerIsAdmin)
                                        <a
                                            href="{{ route(
                                                'work-orders.admin.edit',
                                                $workOrder->id
                                            ) }}"
                                            class="action-button action-edit"
                                            title="Edit Work Order"
                                            aria-label="Edit Work Order"
                                        >
                                            <x-icon name="edit"></x-icon>
                                        </a>
                                    @elseif ($viewerIsMaintenanceStaff)
                                        <a
                                            href="{{ route(
                                                'work-orders.maintenance.edit',
                                                $workOrder->id
                                            ) }}"
                                            class="action-button action-edit"
                                            title="Tindak Lanjut Work Order"
                                            aria-label="Tindak Lanjut Work Order"
                                        >
                                            <x-icon name="edit"></x-icon>
                                        </a>
                                    @else
                                        <a
                                            href="{{ route(
                                                'work-orders.edit',
                                                $workOrder->id
                                            ) }}"
                                            class="action-button action-edit"
                                            title="Edit Permintaan Work Order"
                                            aria-label="Edit Permintaan Work Order"
                                        >
                                            <x-icon name="edit"></x-icon>
                                        </a>
                                    @endif
                                @endif


                                {{-- HAPUS --}}
                                @if($_canDelete)
                                    @if($viewerIsAdmin || $viewerIsMaintenanceStaff)
                                        @php
                                            $_deleteRoute = $viewerIsAdmin
                                                ? route('work-orders.admin.destroy', $workOrder->id)
                                                : route('work-orders.maintenance.destroy', $workOrder->id);
                                        @endphp
                                        <form
                                            action="{{ $_deleteRoute }}"
                                            method="POST"
                                            data-confirm="Hapus Work Order {{ $workOrder->no_wo }}? Tindakan ini tidak dapat dibatalkan."
                                            style="display:inline;"
                                        >
                                            @csrf
                                            @method('DELETE')
                                            <button
                                                type="submit"
                                                class="action-button action-delete"
                                                title="Hapus Work Order"
                                                aria-label="Hapus Work Order"
                                            >
                                                <x-icon name="trash"></x-icon>
                                            </button>
                                        </form>
                                    @endif
                                @endif

                            </div>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td
                            colspan="13"
                            class="empty"
                        >
                            Belum ada Work Order.
                        </td>

                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>

    </div>


    {{-- =====================================================
         DELETE MODAL
    ====================================================== --}}

    <div
        id="deleteModal"
        class="delete-modal"
        aria-hidden="true"
    >

        <div
            class="delete-modal-box"
            role="dialog"
            aria-modal="true"
            aria-labelledby="deleteModalTitle"
        >

            <div class="delete-modal-content">

                <div
                    class="delete-modal-icon"
                    aria-hidden="true"
                >
                    <x-icon name="trash"></x-icon>
                </div>


                <h3
                    id="deleteModalTitle"
                    class="delete-modal-title"
                >
                    Apakah Anda ingin menghapus?
                </h3>


                <div class="delete-modal-message">

                    Work Order

                    <strong
                        id="deleteWorkOrderNo"
                    >
                        -
                    </strong>

                    akan dihapus langsung dari database.

                    <br>

                    Tindakan ini tidak dapat dibatalkan.

                </div>

            </div>


            <div class="delete-modal-actions">

                {{-- BATAL --}}

                <button
                    type="button"
                    id="deleteCancelButton"
                    class="delete-modal-cancel"
                >
                    Batal
                </button>


                {{-- HAPUS --}}

                <form
                    id="deleteWorkOrderForm"
                    method="POST"
                >

                    @csrf

                    @method('DELETE')

                    <button
                        type="submit"
                        class="delete-modal-confirm"
                    >
                        Hapus
                    </button>

                </form>

            </div>

        </div>

    </div>

</div>


<script>

/*
|--------------------------------------------------------------------------
| FILTER WORK ORDER
|--------------------------------------------------------------------------
*/

document.addEventListener(
    'DOMContentLoaded',
    function ()
    {

        const noWoInput =
            document.getElementById(
                'filterNoWo'
            );

        const bulanSelect =
            document.getElementById(
                'filterBulan'
            );

        const tahunSelect =
            document.getElementById(
                'filterTahun'
            );

        const statusSelect =
            document.getElementById(
                'filterStatus'
            );

        const prioritySelect =
            document.getElementById(
                'filterPriority'
            );

        const resetButton =
            document.getElementById(
                'resetFilter'
            );

        const resultText =
            document.getElementById(
                'filterResult'
            );

        const rows =
            Array.from(
                document.querySelectorAll(
                    '.work-order-row'
                )
            );


        function applyFilter()
        {

            const noWo =
                (
                    noWoInput?.value ||
                    ''
                )
                .toLowerCase()
                .trim();


            const bulan =
                bulanSelect?.value ||
                '';


            const tahun =
                tahunSelect?.value ||
                '';


            const status =
                statusSelect?.value ||
                '';


            const priority =
                prioritySelect?.value ||
                '';


            let visible =
                0;


            rows.forEach(
                function(row)
                {

                    const rowNoWo =
                        (
                            row.dataset.noWo ||
                            ''
                        )
                        .toLowerCase();


                    const rowMonth =
                        row.dataset.month ||
                        '';


                    const rowYear =
                        row.dataset.year ||
                        '';


                    const rowStatus =
                        row.dataset.status ||
                        '';


                    const rowPriority =
                        row.dataset.priority ||
                        '';


                    const matchNoWo =
                        noWo === ''
                        ||
                        rowNoWo.includes(
                            noWo
                        );


                    const matchMonth =
                        bulan === ''
                        ||
                        rowMonth === bulan;


                    const matchYear =
                        tahun === ''
                        ||
                        rowYear === tahun;


                    const matchStatus =
                        status === ''
                        ||
                        rowStatus === status;


                    const matchPriority =
                        priority === ''
                        ||
                        rowPriority === priority;


                    const match =
                        matchNoWo
                        &&
                        matchMonth
                        &&
                        matchYear
                        &&
                        matchStatus
                        &&
                        matchPriority;


                    if (match)
                    {

                        row.classList.remove(
                            'filter-hidden'
                        );

                        visible++;

                    }
                    else
                    {

                        row.classList.add(
                            'filter-hidden'
                        );

                    }

                }
            );


            if (!resultText)
            {
                return;
            }


            if (
                noWo === ''
                &&
                bulan === ''
                &&
                tahun === ''
                &&
                status === ''
                &&
                priority === ''
            )
            {

                resultText.textContent =
                    'Menampilkan semua Work Order. Total: ' +
                    rows.length +
                    ' Work Order.';

            }
            else
            {

                resultText.textContent =
                    'Menampilkan ' +
                    visible +
                    ' dari ' +
                    rows.length +
                    ' Work Order.';

            }

        }


        if (noWoInput)
        {

            noWoInput.addEventListener(
                'input',
                applyFilter
            );

        }


        if (bulanSelect)
        {

            bulanSelect.addEventListener(
                'change',
                applyFilter
            );

        }


        if (tahunSelect)
        {

            tahunSelect.addEventListener(
                'change',
                applyFilter
            );

        }


        if (statusSelect)
        {

            statusSelect.addEventListener(
                'change',
                applyFilter
            );

        }


        if (prioritySelect)
        {

            prioritySelect.addEventListener(
                'change',
                applyFilter
            );

        }


        if (resetButton)
        {

            resetButton.addEventListener(
                'click',
                function()
                {

                    if (noWoInput)
                    {
                        noWoInput.value = '';
                    }

                    if (bulanSelect)
                    {
                        bulanSelect.value = '';
                    }

                    if (tahunSelect)
                    {
                        tahunSelect.value = '';
                    }

                    if (statusSelect)
                    {
                        statusSelect.value = '';
                    }

                    if (prioritySelect)
                    {
                        prioritySelect.value = '';
                    }

                    applyFilter();

                }
            );

        }


        applyFilter();


        /*
        |--------------------------------------------------------------------------
        | DELETE MODAL
        |--------------------------------------------------------------------------
        */

        const deleteModal =
            document.getElementById(
                'deleteModal'
            );

        const deleteWorkOrderNo =
            document.getElementById(
                'deleteWorkOrderNo'
            );

        const deleteWorkOrderForm =
            document.getElementById(
                'deleteWorkOrderForm'
            );

        const deleteCancelButton =
            document.getElementById(
                'deleteCancelButton'
            );


        /*
        |--------------------------------------------------------------------------
        | OPEN DELETE MODAL
        |--------------------------------------------------------------------------
        */

        document.addEventListener(
            'click',
            function(event)
            {

                const deleteButton =
                    event.target.closest(
                        '.js-delete-work-order'
                    );


                if (!deleteButton)
                {
                    return;
                }


                event.preventDefault();

                event.stopPropagation();


                const workOrderId =
                    deleteButton.getAttribute(
                        'data-id'
                    );


                const workOrderNo =
                    deleteButton.getAttribute(
                        'data-no-wo'
                    );


                if (
                    !deleteModal ||
                    !deleteWorkOrderNo ||
                    !deleteWorkOrderForm
                )
                {

                    console.error(
                        'Elemen modal hapus tidak ditemukan.'
                    );

                    return;

                }


                deleteWorkOrderNo.textContent =
                    workOrderNo || '-';


                deleteWorkOrderForm.action =
                    "{{ url('/work-orders') }}/" +
                    workOrderId;


                deleteModal.classList.add(
                    'active'
                );


                deleteModal.setAttribute(
                    'aria-hidden',
                    'false'
                );


                document.body.style.overflow =
                    'hidden';

            }
        );


        /*
        |--------------------------------------------------------------------------
        | CLOSE DELETE MODAL
        |--------------------------------------------------------------------------
        */

        function closeDeleteModal()
        {

            if (!deleteModal)
            {
                return;
            }


            deleteModal.classList.remove(
                'active'
            );


            deleteModal.setAttribute(
                'aria-hidden',
                'true'
            );


            document.body.style.overflow =
                '';

        }


        /*
        |--------------------------------------------------------------------------
        | BATAL
        |--------------------------------------------------------------------------
        */

        if (deleteCancelButton)
        {

            deleteCancelButton.addEventListener(
                'click',
                function(event)
                {

                    event.preventDefault();

                    closeDeleteModal();

                }
            );

        }


        /*
        |--------------------------------------------------------------------------
        | KLIK BACKDROP
        |--------------------------------------------------------------------------
        */

        if (deleteModal)
        {

            deleteModal.addEventListener(
                'click',
                function(event)
                {

                    if (
                        event.target ===
                        deleteModal
                    )
                    {

                        closeDeleteModal();

                    }

                }
            );

        }


        /*
        |--------------------------------------------------------------------------
        | ESC
        |--------------------------------------------------------------------------
        */

        document.addEventListener(
            'keydown',
            function(event)
            {

                if (
                    event.key === 'Escape'
                )
                {

                    closeDeleteModal();

                }

            }
        );

    }
);


/*
|--------------------------------------------------------------------------
| PRINT
|--------------------------------------------------------------------------
*/

function printWorkOrderTable()
{
    window.print();
}


/*
|--------------------------------------------------------------------------
| AFTER PRINT
|--------------------------------------------------------------------------
*/

window.addEventListener(
    'afterprint',
    function()
    {

        document.body.style.overflow =
            '';

    }
);

</script>

@endsection