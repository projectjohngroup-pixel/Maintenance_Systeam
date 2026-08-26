@extends('layouts.app')

@section('title', 'Stok Barang')

@section('page_title', 'Inventory')

@section(
    'page_subtitle',
    'Pengelolaan persediaan dan aktivitas inventory'
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
}


/* =====================================================
   FILTER
===================================================== */

.inventory-filter-card {
    padding: 15px 18px;
    margin-bottom: 20px;
}

.inventory-filter {
    display: flex;
    align-items: center;
    gap: 10px;
    width: 100%;
    flex-wrap: nowrap;
}

.inventory-search {
    flex: 1;
    min-width: 280px;
    height: 42px;
    padding: 0 13px;
    border: 1px solid var(--pds-line-2);
    border-radius: 8px;
    background: var(--pds-card);
    font-size: 13px;
}

.inventory-filter-select {
    width: 165px;
    height: 42px;
    padding: 0 12px;
    border: 1px solid var(--pds-line-2);
    border-radius: 8px;
    background: var(--pds-card);
    cursor: pointer;
    font-size: 13px;
}

.inventory-search:focus,
.inventory-filter-select:focus,
.inventory-form-control:focus {
    outline: none;
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37,99,235,.08);
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
    min-width: 1150px;
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

.inventory-empty {
    text-align: center;
    padding: 40px;
    color: var(--pds-muted-2);
}


/* =====================================================
   KONDISI
===================================================== */

.stock-pill {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 6px 10px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 700;
    white-space: nowrap;
}

.stock-ready {
    background: #ecfdf5;
    color: #047857;
}

.stock-low {
    background: #fffbeb;
    color: #b45309;
}


.pill-dot {
    display: inline-block;
    width: 8px;
    height: 8px;
    border-radius: 999px;
    flex: none;
}

.dot-ok    { background: #10b981; }
.dot-low   { background: #f59e0b; }
.dot-empty { background: #ef4444; }

.label-with-dot {
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.stock-empty {
    background: #fef2f2;
    color: #b91c1c;
}

.status-active {
    color: #047857;
    font-weight: 700;
}

.status-inactive {
    color: #b91c1c;
    font-weight: 700;
}


/* =====================================================
   ACTION
===================================================== */

.inventory-actions-cell {
    display: flex;
    align-items: center;
    gap: 6px;
}

.inventory-icon {
    width: 34px;
    height: 34px;
    border: none;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 16px;
}

.inventory-icon-view {
    background: #eff6ff;
    color: #2563eb;
}

.inventory-icon-edit {
    background: #fffbeb;
    color: #d97706;
}

.inventory-icon-delete {
    background: #fef2f2;
    color: #dc2626;
}


/* =====================================================
   MODAL
===================================================== */

.inventory-modal-overlay {
    display: none;
    position: fixed;
    inset: 0;
    z-index: 9999;
    background: rgba(15,23,42,.58);
    backdrop-filter: blur(5px);
    -webkit-backdrop-filter: blur(5px);
    align-items: center;
    justify-content: center;
    padding: 20px;
}

.inventory-modal-overlay.active {
    display: flex;
}

.inventory-modal {
    width: 100%;
    max-width: 580px;
    max-height: calc(100vh - 40px);
    overflow-y: auto;
    background: var(--pds-card);
    border-radius: 16px;
    padding: 25px;
    box-shadow: 0 25px 70px rgba(0,0,0,.25);
}

.inventory-modal-small {
    max-width: 400px;
    text-align: center;
}

.inventory-modal-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 10px;
    margin-bottom: 20px;
}

.inventory-modal-header h2 {
    font-size: 20px;
    margin-bottom: 5px;
}

.inventory-modal-header p {
    color: var(--pds-muted);
    font-size: 13px;
}

.inventory-modal-close {
    width: 32px;
    height: 32px;
    border: none;
    border-radius: 8px;
    background: var(--pds-soft-2);
    color: var(--pds-ink-2);
    cursor: pointer;
    font-size: 18px;
}


/* =====================================================
   FORM
===================================================== */

.inventory-form-group {
    margin-bottom: 15px;
}

.inventory-form-group label {
    display: block;
    margin-bottom: 7px;
    font-size: 13px;
    font-weight: 700;
}

.inventory-form-control {
    width: 100%;
    padding: 10px 11px;
    border: 1px solid var(--pds-line-2);
    border-radius: 8px;
    background: var(--pds-card);
    font-size: 14px;
}

.inventory-unit-row {
    display: flex;
    align-items: center;
    gap: 8px;
}

.inventory-unit-row select {
    flex: 1;
}

.inventory-unit-add {
    width: 42px;
    height: 42px;
    border: none;
    border-radius: 8px;
    background: #2563eb;
    color: #ffffff;
    cursor: pointer;
    font-size: 20px;
    font-weight: 700;
}

.inventory-unit-error {
    display: none;
    margin-top: 7px;
    color: #b91c1c;
    font-size: 12px;
}

.inventory-modal-actions {
    display: flex;
    justify-content: flex-end;
    gap: 8px;
    margin-top: 20px;
}


/* =====================================================
   DETAIL
===================================================== */

.inventory-detail-grid {
    display: grid;
    grid-template-columns: 170px 1fr;
    border: 1px solid var(--pds-line);
    border-radius: 10px;
    overflow: hidden;
}

.inventory-detail-label,
.inventory-detail-value {
    padding: 12px;
    border-bottom: 1px solid var(--pds-line);
    font-size: 13px;
}

.inventory-detail-label {
    background: var(--pds-soft);
    font-weight: 700;
    color: var(--pds-ink-2);
}


/* =====================================================
   MESSAGE
===================================================== */

.inventory-message {
    padding: 12px 14px;
    border-radius: 8px;
    margin-bottom: 18px;
    font-size: 13px;
}

.inventory-message-success {
    background: #ecfdf5;
    border: 1px solid #a7f3d0;
    color: #047857;
}

.inventory-message-error {
    background: #fef2f2;
    border: 1px solid #fecaca;
    color: #b91c1c;
}


/* =====================================================
   DELETE
===================================================== */

.inventory-delete-icon {
    width: 58px;
    height: 58px;
    margin: 0 auto 15px;
    border-radius: 50%;
    background: #fee2e2;
    color: #dc2626;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 25px;
    font-weight: 700;
}


/* =====================================================
   PDF MODAL
===================================================== */

.inventory-pdf-info {
    padding: 12px 14px;
    background: var(--pds-soft);
    border: 1px solid var(--pds-line);
    border-radius: 8px;
    color: var(--pds-muted);
    font-size: 12px;
    line-height: 1.5;
}

.inventory-print-title {
    display: none;
}

.inventory-print-date {
    margin-top: 4px !important;
}


/* =====================================================
   AI ASSISTANT
===================================================== */

.pachira-ai-button {
    position: fixed;
    right: 24px;
    bottom: 20px;

    z-index: 99999;

    width: 52px;
    height: 52px;

    display: flex;
    align-items: center;
    justify-content: center;

    border: none;
    border-radius: 50%;

    background: #fbbf24;
    color: #ffffff;

    font-size: 23px;

    cursor: pointer;

    box-shadow:
        0 7px 20px rgba(0,0,0,.22);

    transition: .2s ease;
}

.pachira-ai-button:hover {
    transform: scale(1.06);
}

.pachira-ai-button:active {
    transform: scale(.97);
}

.pachira-ai-panel {
    position: fixed;

    right: 24px;
    bottom: 84px;

    z-index: 99998;

    width: 380px;
    max-width: calc(100vw - 30px);
    height: 520px;
    max-height: calc(100vh - 120px);

    display: none;
    flex-direction: column;

    overflow: hidden;

    background: var(--pds-card);

    border: 1px solid var(--pds-line);
    border-radius: 16px;

    box-shadow:
        0 20px 60px rgba(0,0,0,.20);
}

.pachira-ai-panel.active {
    display: flex;
}

.pachira-ai-header {
    display: flex;
    align-items: center;
    justify-content: space-between;

    padding: 15px 17px;

    background: #111827;
    color: #ffffff;
}

.pachira-ai-header-left {
    display: flex;
    align-items: center;
    gap: 10px;
}

.pachira-ai-icon {
    width: 34px;
    height: 34px;

    display: flex;
    align-items: center;
    justify-content: center;

    border-radius: 50%;

    background: #fbbf24;

    font-size: 17px;
}

.pachira-ai-title {
    font-size: 13px;
    font-weight: 700;
}

.pachira-ai-status {
    margin-top: 2px;

    font-size: 10px;

    color: var(--pds-muted-2);
}

.pachira-ai-close {
    width: 30px;
    height: 30px;

    display: flex;
    align-items: center;
    justify-content: center;

    border: none;
    border-radius: 7px;

    background: transparent;

    color: #d1d5db;

    font-size: 18px;

    cursor: pointer;
}

.pachira-ai-close:hover {
    background: rgba(255,255,255,.08);
    color: #ffffff;
}

.pachira-ai-body {
    flex: 1;

    padding: 17px;

    overflow-y: auto;

    background: var(--pds-soft);
}

.pachira-ai-message {
    display: flex;
    margin-bottom: 12px;
}

.pachira-ai-message.ai {
    justify-content: flex-start;
}

.pachira-ai-message.user {
    justify-content: flex-end;
}

.pachira-ai-bubble {
    max-width: 85%;

    padding: 10px 12px;

    border-radius: 12px;

    font-size: 12px;

    line-height: 1.5;
}

.pachira-ai-message.ai .pachira-ai-bubble {
    background: var(--pds-card);

    border: 1px solid var(--pds-line);

    color: var(--pds-ink-2);

    border-top-left-radius: 5px;
}

.pachira-ai-message.user .pachira-ai-bubble {
    background: #2563eb;

    color: #ffffff;

    border-top-right-radius: 5px;
}

.pachira-ai-footer {
    padding: 12px;

    background: var(--pds-card);

    border-top: 1px solid var(--pds-line);
}

.pachira-ai-form {
    display: flex;
    align-items: center;
    gap: 8px;
}

.pachira-ai-input {
    flex: 1;

    height: 40px;

    padding: 0 12px;

    border: 1px solid var(--pds-line-2);

    border-radius: 9px;

    outline: none;

    font-size: 12px;

    background: var(--pds-card);
}

.pachira-ai-input:focus {
    border-color: #2563eb;

    box-shadow:
        0 0 0 2px rgba(37,99,235,.08);
}

.pachira-ai-send {
    width: 40px;
    height: 40px;

    border: none;
    border-radius: 9px;

    background: #2563eb;

    color: #ffffff;

    cursor: pointer;

    font-size: 15px;
}

.pachira-ai-send:hover {
    background: #1d4ed8;
}


/* =====================================================
   RESPONSIVE
===================================================== */

@media (max-width: 1000px) {

    .inventory-summary {
        grid-template-columns: repeat(2,1fr);
    }

    .inventory-filter {
        flex-wrap: wrap;
    }

    .inventory-search {
        min-width: 100%;
        flex-basis: 100%;
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

    .inventory-filter-select {
        width: 100%;
    }

    .inventory-table-header {
        flex-direction: column;
        align-items: flex-start;
    }

    .inventory-detail-grid {
        grid-template-columns: 1fr;
    }

    .pachira-ai-button {
        right: 15px;
        bottom: 15px;

        width: 48px;
        height: 48px;
    }

    .pachira-ai-panel {
        right: 15px;
        bottom: 73px;

        width: calc(100vw - 30px);
        height: 500px;
    }

}


/* =====================================================
   PRINT / PDF
===================================================== */

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
    .inventory-export,
    .inventory-actions-cell,
    .inventory-modal-overlay,
    .pagination,
    .sidebar,
    .topbar,
    .message,
    .inventory-message,
    .pachira-ai-button,
    .pachira-ai-panel {
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
        display: block !important;
        margin: 0 0 5px !important;
        padding: 0 !important;
        font-size: 20px !important;
        font-weight: 700 !important;
        color: #000000 !important;
        text-align: center !important;
    }

    .inventory-print-title p {
        display: block !important;
        margin: 2px 0 !important;
        padding: 0 !important;
        font-size: 10px !important;
        color: #555555 !important;
        text-align: center !important;
    }

    .inventory-print-date {
        margin-top: 4px !important;
    }

    .inventory-table-wrapper {
        overflow: visible !important;
    }

    .inventory-table {
        width: 100% !important;
        min-width: 0 !important;
        border-collapse: collapse !important;
        table-layout: auto !important;
    }

    .inventory-table th,
    .inventory-table td {
        padding: 7px 6px !important;
        border: 1px solid #000000 !important;
        font-size: 9px !important;
        color: #000000 !important;
        text-align: center !important;
        vertical-align: middle !important;
    }

    .inventory-table th {
        background: #f2f2f2 !important;
        font-weight: 700 !important;
        text-align: center !important;
    }

    .inventory-table th:last-child,
    .inventory-table td:last-child {
        display: none !important;
    }

    .inventory-table td,
    .barang-kode,
    .status-active,
    .status-inactive {
        text-align: center !important;
    }

    .stock-pill {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 4px !important;
        background: transparent !important;
        padding: 0 !important;
        border: none !important;
        color: #000000 !important;
        font-size: 9px !important;
        font-weight: 400 !important;
    }

    .stock-ready,
    .stock-low,
    .stock-empty {
        background: transparent !important;
        color: #000000 !important;
    }

    .inventory-empty {
        display: table-cell !important;
        text-align: center !important;
        color: #000000 !important;
    }

}

body.inventory-print-landscape .inventory-table th,
body.inventory-print-landscape .inventory-table td {
    font-size: 8px !important;
    padding: 5px !important;
}

</style>

@endpush


@section('content')


<!-- =====================================================
     MENU MODUL INVENTORY
===================================================== -->

<div class="inventory-module-nav">


    {{-- STOK BARANG --}}

    <a
        href="{{ route('barang.index') }}"
        class="inventory-module-link active"
    >
        <x-icon name="box"></x-icon> Stok Barang
    </a>


    {{-- BARANG MASUK --}}

    <a
        href="{{ route('barang-masuk.index') }}"
        class="inventory-module-link"
    >
        <x-icon name="download"></x-icon> Barang Masuk
    </a>


    {{-- BARANG KELUAR --}}

    <a
        href="{{ route('barang-keluar.index') }}"
        class="inventory-module-link"
    >
        <x-icon name="upload"></x-icon> Barang Keluar
    </a>


    {{-- PURCHASE REQUEST --}}

    <a
        href="{{ url('/purchase-requests') }}"
        class="inventory-module-link"
    >
        <x-icon name="clipboard"></x-icon> Purchase Request
    </a>


    {{-- LAPORAN HARIAN --}}

    <a
        href="{{ route('laporan-harian.index') }}"
        class="inventory-module-link"
    >
        <x-icon name="clipboard"></x-icon> Laporan Harian
    </a>


    {{-- RATA-RATA PEMAKAIAN --}}

    <a
        href="{{ route('rata-rata-pemakaian.index') }}"
        class="inventory-module-link"
    >
        <x-icon name="chart"></x-icon> Rata-rata Pemakaian
    </a>


    {{-- RESTOCK --}}

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
            Stok Barang
        </h1>

        <p>
            Master barang / sparepart dan kondisi stok.
        </p>

    </div>


    <div class="inventory-actions">

        <a
            href="{{ route('dashboard') }}"
            class="btn btn-gray"
        >
            <x-icon name="back"></x-icon> Kembali
        </a>


        <a
            href="{{ route('barang.restock') }}"
            class="btn btn-gray"
        >
            <x-icon name="refresh"></x-icon> Re-stock
        </a>


        <button
            type="button"
            class="btn btn-primary"
            onclick="openInventoryModal('addModal')"
        >
            + Tambah Barang
        </button>

    </div>


</div>


<!-- =====================================================
     JUDUL PRINT / PDF
===================================================== -->

<div class="inventory-print-title">

    <h1>
        DAFTAR STOK BARANG
    </h1>

    <p>
        Maintenance Management System
    </p>

    <p class="inventory-print-date">
        Dicetak:
        {{ now()->format('d/m/Y H:i') }}
    </p>

</div>


<!-- =====================================================
     MESSAGE
===================================================== -->

@if(session('success'))

    <div class="
        inventory-message
        inventory-message-success
    ">
        {{ session('success') }}
    </div>

@endif


@if(isset($errors) && $errors->any())

    <div class="
        inventory-message
        inventory-message-error
    ">
        {{ $errors->first() }}
    </div>

@endif


<!-- =====================================================
     SUMMARY
===================================================== -->

<div class="inventory-summary">


    <div class="inventory-summary-card">

        <div class="inventory-summary-label">
            Total Barang
        </div>

        <div class="inventory-summary-value">
            {{ $totalBarang }}
        </div>

    </div>


    <div class="inventory-summary-card">

        <div class="inventory-summary-label">
            <span class="label-with-dot"><span class="pill-dot dot-ok"></span>Tersedia</span>
        </div>

        <div class="inventory-summary-value">
            {{ $totalTersedia }}
        </div>

    </div>


    <div class="inventory-summary-card">

        <div class="inventory-summary-label">
            <span class="label-with-dot"><span class="pill-dot dot-low"></span>Menipis</span>
        </div>

        <div class="inventory-summary-value">
            {{ $totalMenipis }}
        </div>

    </div>


    <div class="inventory-summary-card">

        <div class="inventory-summary-label">
            <span class="label-with-dot"><span class="pill-dot dot-empty"></span>Habis</span>
        </div>

        <div class="inventory-summary-value">
            {{ $totalHabis }}
        </div>

    </div>


</div>


<!-- =====================================================
     SEARCH / FILTER
===================================================== -->

<div class="card inventory-filter-card">


    <form
        id="filterForm"
        action="{{ route('barang.index') }}"
        method="GET"
        class="inventory-filter"
    >


        <input
            type="text"
            name="search"
            class="inventory-search"
            value="{{ request('search') }}"
            placeholder="Cari kode barang atau nama / spesifikasi..."
        >


        <select
            name="kondisi"
            class="inventory-filter-select"
            onchange="submitInventoryFilter()"
        >

            <option value="">
                Semua Kondisi
            </option>


            <option
                value="TERSEDIA"
                @selected(request('kondisi') === 'TERSEDIA')
            >
                Tersedia
            </option>


            <option
                value="MENIPIS"
                @selected(request('kondisi') === 'MENIPIS')
            >
                Menipis
            </option>


            <option
                value="HABIS"
                @selected(request('kondisi') === 'HABIS')
            >
                Habis
            </option>

        </select>


        <select
            name="status"
            class="inventory-filter-select"
            onchange="submitInventoryFilter()"
        >

            <option value="">
                Semua Status
            </option>


            <option
                value="AKTIF"
                @selected(request('status') === 'AKTIF')
            >
                Aktif
            </option>


            <option
                value="TIDAK AKTIF"
                @selected(request('status') === 'TIDAK AKTIF')
            >
                Tidak Aktif
            </option>

        </select>


        @if(
            request()->filled('search') ||
            request()->filled('kondisi') ||
            request()->filled('status')
        )

            <a
                href="{{ route('barang.index') }}"
                class="btn btn-gray"
            >
                Reset
            </a>

        @endif


    </form>


</div>


<!-- =====================================================
     TABLE
===================================================== -->

<div class="card">


    <div class="inventory-table-header">


        <div class="inventory-table-title">
            Daftar Stok Barang
        </div>


        <div class="inventory-export">

            <button
                type="button"
                class="inventory-export-btn"
                onclick="exportInventoryExcel()"
            >
                <x-icon name="chart"></x-icon> Excel
            </button>


            <button
                type="button"
                class="inventory-export-btn"
                onclick="openPdfModal()"
            >
                <x-icon name="file"></x-icon> PDF
            </button>


            <button
                type="button"
                class="inventory-export-btn"
                onclick="printInventory()"
            >
                <x-icon name="print"></x-icon> Print
            </button>

        </div>


    </div>


    <div class="inventory-table-wrapper">


        <table
            id="inventoryTable"
            class="inventory-table"
        >


            <thead>

                <tr>

                    <th>No</th>

                    <th>Kode Barang</th>

                    <th>Nama Barang / Spesifikasi</th>

                    <th>Satuan</th>

                    <th>Stok</th>

                    <th>Minimum</th>

                    <th>Lokasi Penyimpanan</th>

                    <th>Kondisi</th>

                    <th>Status</th>

                    <th>Aksi</th>

                </tr>

            </thead>


            <tbody>


                @forelse(
                    $barangs
                    as $index
                    => $barang
                )

                    @php
                        $kondisi = $barang->kondisi_stok;
                    @endphp


                    <tr>


                        <td>
                            {{
                                $barangs->firstItem()
                                + $index
                            }}
                        </td>


                        <td class="barang-kode">
                            {{ $barang->kode_barang }}
                        </td>


                        <td>
                            {{ $barang->nama_spesifikasi }}
                        </td>


                        <td>
                            {{ $barang->satuan->nama ?? '-' }}
                        </td>


                        <td>
                            {{ $barang->stok }}
                        </td>


                        <td>
                            {{ $barang->stok_minimum }}
                        </td>


                        <td>
                            {{
                                $barang->lokasi_penyimpanan
                                ?? '-'
                            }}
                        </td>


                        <td>

                            @if($kondisi === 'TERSEDIA')

                                <span class="
                                    stock-pill
                                    stock-ready
                                ">
                                    <span class="pill-dot dot-ok"></span>Tersedia
                                </span>

                            @elseif($kondisi === 'MENIPIS')

                                <span class="
                                    stock-pill
                                    stock-low
                                ">
                                    <span class="pill-dot dot-low"></span>Menipis
                                </span>

                            @else

                                <span class="
                                    stock-pill
                                    stock-empty
                                ">
                                    <span class="pill-dot dot-empty"></span>Habis
                                </span>

                            @endif

                        </td>


                        <td>

                            @if($barang->status === 'AKTIF')

                                <span class="status-active">
                                    Aktif
                                </span>

                            @else

                                <span class="status-inactive">
                                    Tidak Aktif
                                </span>

                            @endif

                        </td>


                        <td>

                            <div class="inventory-actions-cell">

                                <button
                                    type="button"
                                    class="
                                        inventory-icon
                                        inventory-icon-view
                                    "
                                    title="Lihat"
                                    onclick="
                                        openInventoryView(
                                            @js($barang->kode_barang),
                                            @js($barang->nama_spesifikasi),
                                            @js($barang->satuan->nama ?? '-'),
                                            {{ $barang->stok }},
                                            {{ $barang->stok_minimum }},
                                            @js($barang->lokasi_penyimpanan ?? '-'),
                                            @js($kondisi),
                                            @js($barang->status)
                                        )
                                    "
                                >
                                    <x-icon name="eye"></x-icon>
                                </button>


                                @php
                                    $_canEditBarang = \App\Support\DepartmentAccess::canEditBarang(auth()->user(), $barang);
                                    $_canDeleteBarang = \App\Support\DepartmentAccess::canDeleteBarang(auth()->user(), $barang);
                                @endphp

                                @if($_canEditBarang)
                                <button
                                    type="button"
                                    class="
                                        inventory-icon
                                        inventory-icon-edit
                                    "
                                    title="Edit"
                                    onclick="
                                        openInventoryEdit(
                                            {{ $barang->id }},
                                            @js($barang->kode_barang),
                                            @js($barang->nama_spesifikasi),
                                            {{ $barang->satuan_id }},
                                            {{ $barang->stok }},
                                            {{ $barang->stok_minimum }},
                                            @js($barang->lokasi_penyimpanan ?? ''),
                                            @js($barang->status)
                                        )
                                    "
                                >
                                    <x-icon name="edit"></x-icon>
                                </button>
                                @endif


                                @if($_canDeleteBarang)
                                <button
                                    type="button"
                                    class="
                                        inventory-icon
                                        inventory-icon-delete
                                    "
                                    title="Hapus"
                                    onclick="
                                        openInventoryDelete(
                                            {{ $barang->id }},
                                            @js($barang->nama_spesifikasi)
                                        )
                                    "
                                >
                                    <x-icon name="trash"></x-icon>
                                </button>
                                @endif


                            </div>

                        </td>


                    </tr>


                @empty

                    <tr>

                        <td
                            colspan="10"
                            class="inventory-empty"
                        >
                            Belum ada data barang.
                        </td>

                    </tr>

                @endforelse


            </tbody>


        </table>


    </div>


    <div style="margin-top:18px;">

        {{ $barangs->links() }}

    </div>


</div>


<!-- =====================================================
     MODAL TAMBAH BARANG
===================================================== -->

<div
    id="addModal"
    class="inventory-modal-overlay"
    onclick="
        closeInventoryOverlay(
            event,
            'addModal'
        )
    "
>


    <div class="inventory-modal">


        <div class="inventory-modal-header">


            <div>

                <h2>
                    Tambah Barang
                </h2>

                <p>
                    Tambahkan master barang dan stok awal.
                </p>

            </div>


            <button
                type="button"
                class="inventory-modal-close"
                onclick="
                    closeInventoryModal(
                        'addModal'
                    )
                "
            >
                ×
            </button>


        </div>


        <form
            action="{{ route('barang.store') }}"
            method="POST"
        >

            @csrf


            <div class="inventory-form-group">

                <label>
                    Kode Barang
                </label>

                <input
                    type="text"
                    class="inventory-form-control"
                    id="add_kode_barang"
                    name="kode_barang"
                    placeholder="Contoh: BRG-0001"
                    required
                >

            </div>


            <div class="inventory-form-group">

                <label>
                    Nama Barang / Spesifikasi
                </label>

                <input
                    type="text"
                    class="inventory-form-control"
                    id="add_nama_spesifikasi"
                    name="nama_spesifikasi"
                    placeholder='Contoh: Cutting Disk - WD 4"'
                    required
                >

            </div>


            <div class="inventory-form-group">

                <label>
                    Satuan
                </label>


                <div class="inventory-unit-row">


                    <select
                        class="inventory-form-control"
                        id="add_satuan_id"
                        name="satuan_id"
                        required
                    >

                        <option value="">
                            -- Pilih Satuan --
                        </option>


                        @foreach(
                            $satuans
                            as $satuan
                        )

                            <option
                                value="{{ $satuan->id }}"
                            >
                                {{ $satuan->nama }}
                            </option>

                        @endforeach


                    </select>


                    <button
                        type="button"
                        class="inventory-unit-add"
                        title="Tambah Satuan"
                        onclick="
                            openInventoryModal(
                                'unitModal'
                            )
                        "
                    >
                        +
                    </button>


                </div>


            </div>


            <div class="inventory-form-group">

                <label>
                    Stok Awal
                </label>

                <input
                    type="number"
                    class="inventory-form-control"
                    id="add_stok"
                    name="stok"
                    min="0"
                    step="1"
                    value="0"
                    required
                >

            </div>


            <div class="inventory-form-group">

                <label>
                    Stok Minimum
                </label>

                <input
                    type="number"
                    class="inventory-form-control"
                    id="add_stok_minimum"
                    name="stok_minimum"
                    min="0"
                    step="1"
                    value="0"
                    required
                >

            </div>


            <div class="inventory-form-group">

                <label>
                    Lokasi Penyimpanan
                </label>

                <input
                    type="text"
                    class="inventory-form-control"
                    id="add_lokasi"
                    name="lokasi_penyimpanan"
                    placeholder="Contoh: Rak A-01"
                >

            </div>


            <div class="inventory-form-group">

                <label>
                    Status
                </label>


                <select
                    class="inventory-form-control"
                    name="status"
                    required
                >

                    <option value="AKTIF">
                        Aktif
                    </option>

                    <option value="TIDAK AKTIF">
                        Tidak Aktif
                    </option>

                </select>


            </div>


            <div class="inventory-modal-actions">


                <button
                    type="button"
                    class="btn btn-gray"
                    onclick="
                        closeInventoryModal(
                            'addModal'
                        )
                    "
                >
                    Batal
                </button>


                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    Simpan Barang
                </button>


            </div>


        </form>


    </div>

</div>


<!-- =====================================================
     MODAL TAMBAH SATUAN
===================================================== -->

<div
    id="unitModal"
    class="inventory-modal-overlay"
    onclick="
        closeInventoryOverlay(
            event,
            'unitModal'
        )
    "
>


    <div
        class="inventory-modal"
        style="max-width:420px;"
    >


        <div class="inventory-modal-header">


            <div>

                <h2>
                    Tambah Satuan
                </h2>

                <p>
                    Satuan baru langsung tersedia di pilihan.
                </p>

            </div>


            <button
                type="button"
                class="inventory-modal-close"
                onclick="
                    closeInventoryModal(
                        'unitModal'
                    )
                "
            >
                ×
            </button>


        </div>


        <div class="inventory-form-group">


            <label>
                Nama Satuan
            </label>


            <input
                type="text"
                class="inventory-form-control"
                id="unit_nama"
                placeholder="Contoh: PCS / KG / SET"
                autocomplete="off"
            >


            <div
                id="unitError"
                class="inventory-unit-error"
            ></div>


        </div>


        <div class="inventory-modal-actions">


            <button
                type="button"
                class="btn btn-gray"
                onclick="
                    closeInventoryModal(
                        'unitModal'
                    )
                "
            >
                Batal
            </button>


            <button
                type="button"
                id="saveUnitButton"
                class="btn btn-primary"
                onclick="saveInventoryUnit()"
            >
                Simpan Satuan
            </button>


        </div>


    </div>

</div>


<!-- =====================================================
     MODAL LIHAT
===================================================== -->

<div
    id="viewModal"
    class="inventory-modal-overlay"
    onclick="
        closeInventoryOverlay(
            event,
            'viewModal'
        )
    "
>


    <div class="inventory-modal">


        <div class="inventory-modal-header">


            <div>

                <h2>
                    Detail Barang
                </h2>

                <p>
                    Informasi lengkap barang.
                </p>

            </div>


            <button
                type="button"
                class="inventory-modal-close"
                onclick="
                    closeInventoryModal(
                        'viewModal'
                    )
                "
            >
                ×
            </button>


        </div>


        <div class="inventory-detail-grid">


            <div class="inventory-detail-label">
                Kode Barang
            </div>

            <div
                id="viewKode"
                class="inventory-detail-value"
            >
                -
            </div>


            <div class="inventory-detail-label">
                Nama / Spesifikasi
            </div>

            <div
                id="viewNama"
                class="inventory-detail-value"
            >
                -
            </div>


            <div class="inventory-detail-label">
                Satuan
            </div>

            <div
                id="viewSatuan"
                class="inventory-detail-value"
            >
                -
            </div>


            <div class="inventory-detail-label">
                Stok
            </div>

            <div
                id="viewStok"
                class="inventory-detail-value"
            >
                -
            </div>


            <div class="inventory-detail-label">
                Stok Minimum
            </div>

            <div
                id="viewMinimum"
                class="inventory-detail-value"
            >
                -
            </div>


            <div class="inventory-detail-label">
                Lokasi
            </div>

            <div
                id="viewLokasi"
                class="inventory-detail-value"
            >
                -
            </div>


            <div class="inventory-detail-label">
                Kondisi Stok
            </div>

            <div
                id="viewKondisi"
                class="inventory-detail-value"
            >
                -
            </div>


            <div class="inventory-detail-label">
                Status
            </div>

            <div
                id="viewStatus"
                class="inventory-detail-value"
            >
                -
            </div>


        </div>


        <div class="inventory-modal-actions">


            <button
                type="button"
                class="btn btn-gray"
                onclick="
                    closeInventoryModal(
                        'viewModal'
                    )
                "
            >
                Tutup
            </button>


        </div>


    </div>

</div>


<!-- =====================================================
     MODAL EDIT
===================================================== -->

<div
    id="editModal"
    class="inventory-modal-overlay"
    onclick="
        closeInventoryOverlay(
            event,
            'editModal'
        )
    "
>


    <div class="inventory-modal">


        <div class="inventory-modal-header">


            <div>

                <h2>
                    Edit Barang
                </h2>

                <p>
                    Ubah data master barang.
                </p>

            </div>


            <button
                type="button"
                class="inventory-modal-close"
                onclick="
                    closeInventoryModal(
                        'editModal'
                    )
                "
            >
                ×
            </button>


        </div>


        <form
            id="editForm"
            method="POST"
        >

            @csrf

            @method('PATCH')


            <div class="inventory-form-group">

                <label>
                    Kode Barang
                </label>

                <input
                    type="text"
                    class="inventory-form-control"
                    id="edit_kode_barang"
                    name="kode_barang"
                    required
                >

            </div>


            <div class="inventory-form-group">

                <label>
                    Nama Barang / Spesifikasi
                </label>

                <input
                    type="text"
                    class="inventory-form-control"
                    id="edit_nama_spesifikasi"
                    name="nama_spesifikasi"
                    required
                >

            </div>


            <div class="inventory-form-group">

                <label>
                    Satuan
                </label>


                <select
                    class="inventory-form-control"
                    id="edit_satuan_id"
                    name="satuan_id"
                    required
                >

                    @foreach(
                        $satuans
                        as $satuan
                    )

                        <option
                            value="{{ $satuan->id }}"
                        >
                            {{ $satuan->nama }}
                        </option>

                    @endforeach

                </select>

            </div>


            <div class="inventory-form-group">

                <label>
                    Stok
                </label>

                <input
                    type="number"
                    class="inventory-form-control"
                    id="edit_stok"
                    name="stok"
                    min="0"
                    step="1"
                    required
                >

            </div>


            <div class="inventory-form-group">

                <label>
                    Stok Minimum
                </label>

                <input
                    type="number"
                    class="inventory-form-control"
                    id="edit_stok_minimum"
                    name="stok_minimum"
                    min="0"
                    step="1"
                    required
                >

            </div>


            <div class="inventory-form-group">

                <label>
                    Lokasi Penyimpanan
                </label>

                <input
                    type="text"
                    class="inventory-form-control"
                    id="edit_lokasi"
                    name="lokasi_penyimpanan"
                >

            </div>


            <div class="inventory-form-group">

                <label>
                    Status
                </label>


                <select
                    class="inventory-form-control"
                    id="edit_status"
                    name="status"
                    required
                >

                    <option value="AKTIF">
                        Aktif
                    </option>

                    <option value="TIDAK AKTIF">
                        Tidak Aktif
                    </option>

                </select>

            </div>


            <div class="inventory-modal-actions">


                <button
                    type="button"
                    class="btn btn-gray"
                    onclick="
                        closeInventoryModal(
                            'editModal'
                        )
                    "
                >
                    Batal
                </button>


                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    Simpan Perubahan
                </button>


            </div>


        </form>


    </div>

</div>


<!-- =====================================================
     MODAL HAPUS
===================================================== -->

<div
    id="deleteModal"
    class="inventory-modal-overlay"
    onclick="
        closeInventoryOverlay(
            event,
            'deleteModal'
        )
    "
>


    <div
        class="
            inventory-modal
            inventory-modal-small
        "
    >


        <div class="inventory-delete-icon">
            !
        </div>


        <h2>
            Hapus Barang?
        </h2>


        <p
            style="
                margin-top:8px;
                margin-bottom:20px;
                color:var(--pds-muted);
                font-size:13px;
            "
        >

            Apakah Anda yakin ingin menghapus

            <strong id="deleteNama"></strong>?

        </p>


        <form
            id="deleteForm"
            method="POST"
        >

            @csrf

            @method('DELETE')


            <div class="inventory-modal-actions">


                <button
                    type="button"
                    class="btn btn-gray"
                    onclick="
                        closeInventoryModal(
                            'deleteModal'
                        )
                    "
                >
                    Batal
                </button>


                <button
                    type="submit"
                    class="btn btn-danger"
                >
                    Ya, Hapus
                </button>


            </div>


        </form>


    </div>

</div>


<!-- =====================================================
     MODAL PDF
===================================================== -->

<div
    id="pdfModal"
    class="inventory-modal-overlay"
    onclick="
        closeInventoryOverlay(
            event,
            'pdfModal'
        )
    "
>


    <div
        class="inventory-modal"
        style="max-width:470px;"
    >


        <div class="inventory-modal-header">


            <div>

                <h2>
                    Cetak Laporan Stok
                </h2>

                <p>
                    Pilih format laporan.
                </p>

            </div>


            <button
                type="button"
                class="inventory-modal-close"
                onclick="
                    closeInventoryModal(
                        'pdfModal'
                    )
                "
            >
                ×
            </button>


        </div>


        <div class="inventory-form-group">

            <label>
                Orientasi
            </label>


            <select
                id="pdfOrientation"
                class="inventory-form-control"
            >

                <option value="portrait">
                    Potret
                </option>

                <option value="landscape">
                    Landscape
                </option>

            </select>

        </div>


        <div class="inventory-form-group">

            <label>
                Data
            </label>


            <select
                id="pdfDetail"
                class="inventory-form-control"
            >

                <option value="ringkas">
                    Ringkas
                </option>

                <option
                    value="lengkap"
                    selected
                >
                    Lengkap
                </option>

            </select>

        </div>


        <div class="inventory-pdf-info">

            PDF dan Print hanya menampilkan:

            <strong style="color:var(--pds-ink-2);">
                Daftar Stok Barang
            </strong>

            beserta tabel datanya.

        </div>


        <div class="inventory-modal-actions">


            <button
                type="button"
                class="btn btn-gray"
                onclick="
                    closeInventoryModal(
                        'pdfModal'
                    )
                "
            >
                Batal
            </button>


            <button
                type="button"
                class="btn btn-primary"
                onclick="generateInventoryPdf()"
            >
                Buat PDF
            </button>


        </div>


    </div>

</div>


<!-- =====================================================
     PACHIRA AI ASSISTANT
===================================================== -->

<button
    type="button"
    id="pachiraAiButton"
    class="pachira-ai-button"
    aria-label="Pachira AI Assistant"
    title="Pachira AI Assistant"
>
    <x-icon name="robot"></x-icon>
</button>


<div
    id="pachiraAiPanel"
    class="pachira-ai-panel"
>


    <div class="pachira-ai-header">


        <div class="pachira-ai-header-left">


            <div class="pachira-ai-icon">
                <x-icon name="robot"></x-icon>
            </div>


            <div>

                <div class="pachira-ai-title">
                    Pachira AI Assistant
                </div>

                <div class="pachira-ai-status">
                    Ada yang bisa saya bantu?
                </div>

            </div>


        </div>


        <button
            type="button"
            id="pachiraAiClose"
            class="pachira-ai-close"
            aria-label="Tutup AI Assistant"
        >
            ×
        </button>


    </div>


    <div
        id="pachiraAiBody"
        class="pachira-ai-body"
    >


        <div class="pachira-ai-message ai">

            <div class="pachira-ai-bubble">

                Ada yang bisa saya bantu?

            </div>

        </div>


    </div>


    <div class="pachira-ai-footer">


        <form
            id="pachiraAiForm"
            class="pachira-ai-form"
        >


            <input
                type="text"
                id="pachiraAiInput"
                class="pachira-ai-input"
                placeholder="Ketik pertanyaan..."
                autocomplete="off"
            >


            <button
                type="submit"
                class="pachira-ai-send"
                aria-label="Kirim pertanyaan"
            >
                <x-icon name="arrow"></x-icon>
            </button>


        </form>


    </div>


</div>


@endsection


@push('scripts')

<script>

/* =====================================================
   FILTER
===================================================== */

function submitInventoryFilter()
{
    const form =
        document.getElementById(
            'filterForm'
        );

    if (form) {
        form.submit();
    }
}


const inventorySearch =
    document.querySelector(
        '.inventory-search'
    );


if (inventorySearch) {

    inventorySearch.addEventListener(
        'keydown',
        function(event)
        {

            if (
                event.key === 'Enter'
            ) {

                event.preventDefault();

                submitInventoryFilter();

            }

        }
    );

}


/* =====================================================
   MODAL
===================================================== */

function openInventoryModal(id)
{

    const modal =
        document.getElementById(id);

    if (modal) {
        modal.classList.add('active');
    }

}


function closeInventoryModal(id)
{

    const modal =
        document.getElementById(id);

    if (modal) {
        modal.classList.remove('active');
    }

}


function closeInventoryOverlay(
    event,
    id
)
{

    const modal =
        document.getElementById(id);

    if (
        modal &&
        event.target === modal
    ) {

        closeInventoryModal(id);

    }

}


/* =====================================================
   LIHAT
===================================================== */

function openInventoryView(
    kode,
    nama,
    satuan,
    stok,
    minimum,
    lokasi,
    kondisi,
    status
)
{

    document.getElementById(
        'viewKode'
    ).textContent = kode;

    document.getElementById(
        'viewNama'
    ).textContent = nama;

    document.getElementById(
        'viewSatuan'
    ).textContent = satuan;

    document.getElementById(
        'viewStok'
    ).textContent = stok;

    document.getElementById(
        'viewMinimum'
    ).textContent = minimum;

    document.getElementById(
        'viewLokasi'
    ).textContent = lokasi;


    let kondisiText =
        'Habis';


    if (
        kondisi === 'TERSEDIA'
    ) {

        kondisiText =
            'Tersedia';

    }
    else if (
        kondisi === 'MENIPIS'
    ) {

        kondisiText =
            'Menipis';

    }


    document.getElementById(
        'viewKondisi'
    ).textContent =
        kondisiText;


    document.getElementById(
        'viewStatus'
    ).textContent =
        status === 'AKTIF'
            ? 'Aktif'
            : 'Tidak Aktif';


    openInventoryModal(
        'viewModal'
    );

}


/* =====================================================
   EDIT
===================================================== */

function openInventoryEdit(
    id,
    kode,
    nama,
    satuanId,
    stok,
    minimum,
    lokasi,
    status
)
{

    const form =
        document.getElementById(
            'editForm'
        );

    if (!form) {
        return;
    }


    form.action =
        '/barang/' + id;


    document.getElementById(
        'edit_kode_barang'
    ).value = kode;

    document.getElementById(
        'edit_nama_spesifikasi'
    ).value = nama;

    document.getElementById(
        'edit_satuan_id'
    ).value = satuanId;

    document.getElementById(
        'edit_stok'
    ).value = stok;

    document.getElementById(
        'edit_stok_minimum'
    ).value = minimum;

    document.getElementById(
        'edit_lokasi'
    ).value = lokasi;

    document.getElementById(
        'edit_status'
    ).value = status;


    openInventoryModal(
        'editModal'
    );

}


/* =====================================================
   HAPUS
===================================================== */

function openInventoryDelete(
    id,
    nama
)
{

    const form =
        document.getElementById(
            'deleteForm'
        );

    if (!form) {
        return;
    }


    form.action =
        '/barang/' + id;


    document.getElementById(
        'deleteNama'
    ).textContent =
        nama;


    openInventoryModal(
        'deleteModal'
    );

}


/* =====================================================
   TAMBAH SATUAN
===================================================== */

async function saveInventoryUnit()
{

    const input =
        document.getElementById(
            'unit_nama'
        );

    const errorBox =
        document.getElementById(
            'unitError'
        );

    const button =
        document.getElementById(
            'saveUnitButton'
        );


    if (
        !input ||
        !errorBox ||
        !button
    ) {
        return;
    }


    const nama =
        input.value.trim();


    errorBox.style.display =
        'none';

    errorBox.textContent =
        '';


    if (!nama) {

        errorBox.textContent =
            'Nama satuan wajib diisi.';

        errorBox.style.display =
            'block';

        input.focus();

        return;

    }


    button.disabled =
        true;

    button.textContent =
        'Menyimpan...';


    try {

        const response =
            await fetch(
                "{{ route('satuan.store') }}",
                {
                    method: 'POST',

                    headers: {

                        'Content-Type':
                            'application/json',

                        'Accept':
                            'application/json',

                        'X-CSRF-TOKEN':
                            "{{ csrf_token() }}"

                    },

                    body:
                        JSON.stringify({

                            nama:
                                nama,

                            status:
                                '1'

                        })
                }
            );


        const data =
            await response.json();


        if (
            response.status === 422
        ) {

            const firstError =
                data.errors
                    ? Object.values(
                        data.errors
                    )[0][0]
                    : 'Data tidak valid.';


            errorBox.textContent =
                firstError;

            errorBox.style.display =
                'block';

            return;

        }


        if (
            !response.ok ||
            !data.success
        ) {

            errorBox.textContent =
                data.message ||
                'Satuan gagal disimpan.';

            errorBox.style.display =
                'block';

            return;

        }


        const select =
            document.getElementById(
                'add_satuan_id'
            );


        if (select) {

            const option =
                document.createElement(
                    'option'
                );

            option.value =
                data.data.id;

            option.textContent =
                data.data.nama;

            select.appendChild(
                option
            );

            select.value =
                data.data.id;

        }


        input.value =
            '';

        errorBox.textContent =
            '';

        errorBox.style.display =
            'none';


        closeInventoryModal(
            'unitModal'
        );


        const stokInput =
            document.getElementById(
                'add_stok'
            );


        if (stokInput) {
            stokInput.focus();
        }


    }
    catch (error) {

        console.error(error);

        errorBox.textContent =
            'Terjadi kesalahan saat menyimpan satuan.';

        errorBox.style.display =
            'block';

    }
    finally {

        button.disabled =
            false;

        button.textContent =
            'Simpan Satuan';

    }

}


/* =====================================================
   ENTER TAMBAH SATUAN
===================================================== */

const unitInput =
    document.getElementById(
        'unit_nama'
    );


if (unitInput) {

    unitInput.addEventListener(
        'keydown',
        function(event)
        {

            if (
                event.key === 'Enter'
            ) {

                event.preventDefault();

                saveInventoryUnit();

            }

        }
    );

}


/* =====================================================
   EXCEL
===================================================== */

function exportInventoryExcel()
{

    const table =
        document.getElementById(
            'inventoryTable'
        );


    if (!table) {
        return;
    }


    const rows =
        Array.from(
            table.querySelectorAll('tr')
        );


    let csv =
        'No,Kode Barang,Nama Barang / Spesifikasi,Satuan,Stok,Minimum,Lokasi,Kondisi,Status\n';


    rows.slice(1).forEach(
        function(row)
        {

            const cells =
                Array.from(
                    row.querySelectorAll(
                        'th,td'
                    )
                );


            if (
                cells.length < 9
            ) {
                return;
            }


            const values =
                cells
                    .slice(0, 9)
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
                '\ufeff' + csv
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
        'stok-barang.csv';


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
   PDF
===================================================== */

function openPdfModal()
{

    openInventoryModal(
        'pdfModal'
    );

}


function generateInventoryPdf()
{

    const orientation =
        document.getElementById(
            'pdfOrientation'
        ).value;


    document.body.classList.remove(
        'inventory-print-landscape'
    );


    if (
        orientation === 'landscape'
    ) {

        document.body.classList.add(
            'inventory-print-landscape'
        );

    }


    closeInventoryModal(
        'pdfModal'
    );


    setTimeout(
        function()
        {

            window.print();

        },
        100
    );

}


/* =====================================================
   PRINT
===================================================== */

function printInventory()
{

    document.body.classList.remove(
        'inventory-print-landscape'
    );


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

        document.body.classList.remove(
            'inventory-print-landscape'
        );

    }
);


/* =====================================================
   ESC
===================================================== */

document.addEventListener(
    'keydown',
    function(event)
    {

        if (
            event.key === 'Escape'
        ) {

            document
                .querySelectorAll(
                    '.inventory-modal-overlay.active'
                )
                .forEach(
                    function(modal)
                    {

                        modal.classList.remove(
                            'active'
                        );

                    }
                );

        }

    }
);


/* =====================================================
   PACHIRA AI ASSISTANT
===================================================== */

document.addEventListener(
    'DOMContentLoaded',
    function()
    {

        const aiButton =
            document.getElementById(
                'pachiraAiButton'
            );

        const aiPanel =
            document.getElementById(
                'pachiraAiPanel'
            );

        const aiClose =
            document.getElementById(
                'pachiraAiClose'
            );

        const aiForm =
            document.getElementById(
                'pachiraAiForm'
            );

        const aiInput =
            document.getElementById(
                'pachiraAiInput'
            );

        const aiBody =
            document.getElementById(
                'pachiraAiBody'
            );


        if (
            !aiButton ||
            !aiPanel ||
            !aiClose ||
            !aiForm ||
            !aiInput ||
            !aiBody
        ) {
            return;
        }


        /* ================================================
           BUKA AI
        ================================================= */

        aiButton.addEventListener(
            'click',
            function()
            {

                aiPanel.classList.add(
                    'active'
                );

                setTimeout(
                    function()
                    {
                        aiInput.focus();
                    },
                    100
                );

            }
        );


        /* ================================================
           TUTUP AI
        ================================================= */

        aiClose.addEventListener(
            'click',
            function()
            {

                aiPanel.classList.remove(
                    'active'
                );

            }
        );


        /* ================================================
           KIRIM
        ================================================= */

        aiForm.addEventListener(
            'submit',
            function(event)
            {

                event.preventDefault();


                const question =
                    aiInput.value.trim();


                if (!question) {
                    return;
                }


                /* USER */

                const userMessage =
                    document.createElement(
                        'div'
                    );

                userMessage.className =
                    'pachira-ai-message user';

                userMessage.innerHTML = `
                    <div class="pachira-ai-bubble">
                        ${escapePachiraAiHtml(question)}
                    </div>
                `;

                aiBody.appendChild(
                    userMessage
                );


                aiInput.value =
                    '';


                /* JAWABAN SEMENTARA */

                const aiMessage =
                    document.createElement(
                        'div'
                    );

                aiMessage.className =
                    'pachira-ai-message ai';

                aiMessage.innerHTML = `
                    <div class="pachira-ai-bubble">
                        Ada yang bisa saya bantu?
                        <br><br>
                        AI sedang disiapkan untuk membaca data Pachira seperti:
                        <br><br>
                        • Work Order<br>
                        • Stok barang<br>
                        • Mesin<br>
                        • Area<br>
                        • KW<br>
                        • Sparepart<br>
                        • Rata-rata pemakaian
                    </div>
                `;

                aiBody.appendChild(
                    aiMessage
                );


                aiBody.scrollTop =
                    aiBody.scrollHeight;

            }
        );


        /* ================================================
           ESCAPE
        ================================================= */

        function escapePachiraAiHtml(
            text
        )
        {

            const div =
                document.createElement(
                    'div'
                );

            div.textContent =
                text;

            return div.innerHTML;

        }

    }
);

</script>

@endpush