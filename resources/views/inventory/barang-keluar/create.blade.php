@extends('layouts.app')

@section('title', 'Tambah Barang Keluar')

@section('page_title', 'Barang Keluar')

@section(
    'page_subtitle',
    'Tambah transaksi pengeluaran barang / sparepart'
)

@push('styles')
<style>
    /* =====================================================
       HALAMAN
    ===================================================== */

    .bm-form-page {
        padding: 4px 0 30px;
    }

    .bm-form-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
        margin-bottom: 22px;
    }

    .bm-form-title h2 {
        margin: 0 0 5px;
        font-size: 24px;
        font-weight: 700;
        color: var(--pds-ink);
    }

    .bm-form-title p {
        margin: 0;
        color: var(--pds-muted);
        font-size: 13px;
    }


    /* =====================================================
       FORM MODAL UTAMA
    ===================================================== */

    .bm-main-modal-overlay {
        position: fixed;
        inset: 0;
        z-index: 9998;

        background: rgba(15, 23, 42, .58);

        backdrop-filter: blur(4px);
        -webkit-backdrop-filter: blur(4px);

        display: flex;
        align-items: center;
        justify-content: center;

        padding: 24px;

        box-sizing: border-box;
    }

    .bm-main-modal {
        width: 100%;
        max-width: 900px;
        max-height: 92vh;

        background: var(--pds-card);

        border-radius: 18px;

        box-shadow:
            0 25px 70px rgba(0,0,0,.25);

        overflow: hidden;

        display: flex;
        flex-direction: column;
    }

    .bm-main-modal-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;

        gap: 20px;

        padding: 22px 30px 18px;

        border-bottom: 1px solid var(--pds-line);

        flex-shrink: 0;
    }

    .bm-main-modal-header h2 {
        margin: 0 0 6px;

        font-size: 24px;
        font-weight: 700;

        color: var(--pds-ink);
    }

    .bm-main-modal-header p {
        margin: 0;

        font-size: 13px;

        color: var(--pds-muted);
    }

    .bm-main-close {
        width: 38px;
        height: 38px;

        border: 0;
        border-radius: 10px;

        background: var(--pds-soft-2);
        color: var(--pds-ink-2);

        font-size: 22px;
        line-height: 1;

        cursor: pointer;

        display: flex;
        align-items: center;
        justify-content: center;

        flex-shrink: 0;
    }

    .bm-main-close:hover {
        background: var(--pds-line);
        color: var(--pds-ink);
    }

    .bm-main-modal-body {
        padding: 22px 30px 30px;

        overflow-y: auto;

        flex: 1;
        min-height: 0;
    }


    /* =====================================================
       FORM CARD
    ===================================================== */

    .bm-form-card {
        background: var(--pds-card);
        border: none;
        border-radius: 0;
        box-shadow: none;
        overflow: visible;
    }

    .bm-form-card-header {
        display: none;
    }

    .bm-form-body {
        padding: 0;
    }

    .bm-form-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 18px;
    }

    .bm-field-full {
        grid-column: 1 / -1;
    }

    .bm-field label {
        display: block;
        margin-bottom: 7px;

        color: var(--pds-ink-2);

        font-size: 12px;
        font-weight: 700;
    }

    .bm-required {
        color: #dc2626;
    }

    .bm-input,
    .bm-select,
    .bm-textarea {
        width: 100%;

        border: 1px solid var(--pds-line-2);
        border-radius: 9px;

        background: var(--pds-card);
        color: var(--pds-ink);

        font-size: 13px;

        transition: .15s;

        box-sizing: border-box;
    }

    .bm-input,
    .bm-select {
        height: 42px;
        padding: 0 12px;
    }

    .bm-textarea {
        min-height: 110px;
        padding: 11px 12px;
        resize: vertical;
    }

    .bm-input:focus,
    .bm-select:focus,
    .bm-textarea:focus {
        outline: none;

        border-color: #2563eb;

        box-shadow:
            0 0 0 3px rgba(37,99,235,.08);
    }

    .bm-readonly {
        background: var(--pds-soft);
        color: var(--pds-muted);
    }

    .bm-help {
        margin-top: 5px;

        font-size: 11px;

        color: var(--pds-muted-2);
    }

    .bm-error {
        margin-top: 5px;

        font-size: 11px;

        color: #dc2626;
    }


    /* =====================================================
       FOOTER FORM
    ===================================================== */

    .bm-form-footer {
        display: flex;
        justify-content: flex-end;

        gap: 8px;

        padding: 18px 30px;

        border-top: 1px solid var(--pds-line);

        background: var(--pds-soft);

        flex-shrink: 0;
    }


    /* =====================================================
       ALERT
    ===================================================== */

    .bm-alert {
        margin-bottom: 18px;

        padding: 13px 15px;

        border-radius: 9px;

        font-size: 13px;
    }

    .bm-alert-error {
        background: #fef2f2;

        color: #991b1b;

        border: 1px solid #fecaca;
    }

    .bm-alert-error ul {
        margin: 7px 0 0 18px;
        padding: 0;
    }


    /* =====================================================
       SEARCH INPUT
    ===================================================== */

    .bm-input-search {
        display: flex;
        gap: 8px;
    }

    .bm-input-search .bm-input {
        flex: 1;
    }

    .bm-search-btn {
        width: 46px;
        height: 42px;

        flex-shrink: 0;

        border: 1px solid var(--pds-line-2);

        border-radius: 9px;

        background: var(--pds-soft);

        color: var(--pds-ink-2);

        cursor: pointer;

        font-size: 18px;
    }

    .bm-search-btn:hover {
        background: #eff6ff;

        border-color: #93c5fd;
    }


    /* =====================================================
       STOK SUMMARY
    ===================================================== */

    .bm-stock-summary {
        display: grid;

        grid-template-columns: repeat(3, 1fr);

        gap: 12px;
    }

    .bm-stock-box {
        border: 1px solid var(--pds-line);

        border-radius: 9px;

        background: var(--pds-soft);

        padding: 12px 14px;
    }

    .bm-stock-box-label {
        margin-bottom: 4px;

        font-size: 11px;

        color: var(--pds-muted);
    }

    .bm-stock-box-value {
        font-size: 18px;

        font-weight: 700;

        color: var(--pds-ink);
    }

    .bm-stock-box-value.success {
        color: #16a34a;
    }

    .bm-stock-box-value.danger {
        color: #dc2626;
    }


    /* =====================================================
       MODAL PILIH BARANG / WO
    ===================================================== */

    .bm-modal-overlay {
        display: none;

        position: fixed;

        inset: 0;

        z-index: 10000;

        background: rgba(15,23,42,.58);

        backdrop-filter: blur(4px);

        -webkit-backdrop-filter: blur(4px);

        align-items: center;

        justify-content: center;

        padding: 20px;

        box-sizing: border-box;
    }

    .bm-modal-overlay.active {
        display: flex;
    }

    .bm-modal {
        width: 100%;

        max-width: 950px;

        max-height: 85vh;

        overflow: hidden;

        background: var(--pds-card);

        border-radius: 14px;

        box-shadow:
            0 25px 70px rgba(0,0,0,.25);

        display: flex;

        flex-direction: column;
    }

    .bm-modal-header {
        display: flex;

        justify-content: space-between;

        align-items: flex-start;

        gap: 15px;

        padding: 18px 20px;

        border-bottom: 1px solid var(--pds-line);
    }

    .bm-modal-title h3 {
        margin: 0 0 4px;

        font-size: 18px;

        color: var(--pds-ink);
    }

    .bm-modal-title p {
        margin: 0;

        color: var(--pds-muted);

        font-size: 12px;
    }

    .bm-modal-close {
        width: 34px;

        height: 34px;

        border: none;

        border-radius: 8px;

        background: var(--pds-soft-2);

        color: var(--pds-ink-2);

        cursor: pointer;

        font-size: 20px;
    }

    .bm-modal-close:hover {
        background: var(--pds-line);
    }

    .bm-modal-search {
        padding: 15px 20px;

        border-bottom: 1px solid var(--pds-line);
    }

    .bm-stock-table-wrap {
        max-height: 55vh;

        overflow: auto;
    }

    .bm-stock-table {
        width: 100%;

        border-collapse: collapse;

        min-width: 750px;
    }

    .bm-stock-table th,
    .bm-stock-table td {
        padding: 12px 14px;

        border-bottom: 1px solid var(--pds-line);

        text-align: left;

        vertical-align: middle;

        font-size: 13px;
    }

    .bm-stock-table th {
        position: sticky;

        top: 0;

        z-index: 2;

        background: var(--pds-soft);

        color: var(--pds-ink-2);

        font-weight: 700;

        white-space: nowrap;
    }

    .bm-stock-table tbody tr:hover {
        background: var(--pds-soft);
    }

    .bm-stock-code {
        font-weight: 700;

        color: var(--pds-ink);

        white-space: nowrap;
    }

    .bm-stock-qty {
        font-weight: 700;

        color: var(--pds-ink);

        white-space: nowrap;
    }

    .bm-stock-empty {
        padding: 40px 20px !important;

        text-align: center !important;

        color: var(--pds-muted-2);
    }

    .bm-pilih-btn {
        min-height: 34px;

        padding: 7px 12px;

        border: none;

        border-radius: 7px;

        background: #2563eb;

        color: #ffffff;

        font-size: 12px;

        font-weight: 600;

        cursor: pointer;
    }

    .bm-pilih-btn:hover {
        background: #1d4ed8;
    }


    /* =====================================================
       RESPONSIVE
    ===================================================== */

    @media (max-width: 700px) {

        .bm-main-modal-overlay {
            padding: 10px;
        }

        .bm-main-modal {
            max-height: 96vh;

            border-radius: 14px;
        }

        .bm-main-modal-header {
            padding: 18px 20px 15px;
        }

        .bm-main-modal-header h2 {
            font-size: 20px;
        }

        .bm-main-modal-body {
            padding: 18px 20px 22px;
        }

        .bm-form-grid {
            grid-template-columns: 1fr;
        }

        .bm-field-full {
            grid-column: auto;
        }

        .bm-form-footer {
            padding: 15px 20px;
        }

        .bm-stock-summary {
            grid-template-columns: 1fr;
        }

        .bm-modal {
            max-height: 92vh;
        }
    }

    @media (max-width: 480px) {

        .bm-main-modal-overlay {
            padding: 7px;
        }

        .bm-main-modal {
            max-height: 97vh;

            border-radius: 12px;
        }

        .bm-main-modal-header {
            gap: 12px;

            padding: 16px;
        }

        .bm-main-modal-header h2 {
            font-size: 18px;
        }

        .bm-main-modal-header p {
            font-size: 12px;
        }

        .bm-main-close {
            width: 34px;
            height: 34px;

            font-size: 20px;
        }

        .bm-main-modal-body {
            padding: 16px;
        }

        .bm-form-footer {
            padding: 12px 16px;
        }

        .bm-modal-overlay {
            padding: 8px;
        }
    }
</style>
@endpush


@section('content')

{{-- =====================================================
     HALAMAN BELAKANG
===================================================== --}}

<div class="bm-form-page">

    <div class="bm-form-header">

        <div class="bm-form-title">

            <h2>
                Tambah Barang Keluar
            </h2>

            <p>
                Isi data pengeluaran barang dengan lengkap.
            </p>

        </div>

        <a
            href="{{ route('barang-keluar.index') }}"
            class="btn btn-gray"
        >
            Kembali
        </a>

    </div>

</div>


{{-- =====================================================
     MODAL FORM UTAMA
===================================================== --}}

<div class="bm-main-modal-overlay">

    <div class="bm-main-modal">

        {{-- HEADER FORM --}}

        <div class="bm-main-modal-header">

            <div>

                <h2>
                    Tambah Barang Keluar
                </h2>

                <p>
                    Tambah transaksi pengeluaran barang / sparepart
                </p>

            </div>

            <button
                type="button"
                class="bm-main-close"
                onclick="window.location.href='{{ route('barang-keluar.index') }}'"
            >
                ×
            </button>

        </div>


        {{-- BODY --}}

        <div class="bm-main-modal-body">


            {{-- ERROR --}}

            @if ($errors->any())

                <div class="bm-alert bm-alert-error">

                    <strong>
                        Data belum dapat disimpan.
                    </strong>

                    <ul>

                        @foreach ($errors->all() as $error)

                            <li>
                                {{ $error }}
                            </li>

                        @endforeach

                    </ul>

                </div>

            @endif


            {{-- FORM --}}

            <div class="bm-form-card">

                <form
                    action="{{ route('barang-keluar.store') }}"
                    method="POST"
                    id="barangKeluarForm"
                >

                    @csrf

                    <div class="bm-form-body">

                        <div class="bm-form-grid">


                            {{-- NO TRANSAKSI --}}

                            <div class="bm-field">

                                <label>
                                    No Transaksi
                                </label>

                                <input
                                    type="text"
                                    name="no_transaksi"
                                    class="bm-input bm-readonly"
                                    value="{{ old('no_transaksi', 'BK-001') }}"
                                    readonly
                                >

                                <div class="bm-help">
                                    Nomor transaksi dibuat otomatis.
                                </div>

                            </div>


                            {{-- TANGGAL --}}

                            <div class="bm-field">

                                <label>
                                    Tanggal Keluar
                                    <span class="bm-required">*</span>
                                </label>

                                <input
                                    type="date"
                                    name="tanggal_keluar"
                                    class="bm-input"
                                    value="{{ old(
                                        'tanggal_keluar',
                                        now()->format('Y-m-d')
                                    ) }}"
                                    required
                                >

                                @error('tanggal_keluar')

                                    <div class="bm-error">
                                        {{ $message }}
                                    </div>

                                @enderror

                            </div>


                            {{-- BARANG --}}

                            <div class="bm-field bm-field-full">

                                <label>
                                    Barang / Sparepart
                                    <span class="bm-required">*</span>
                                </label>

                                <div class="bm-input-search">

                                    <input
                                        type="text"
                                        id="nama_barang"
                                        class="bm-input"
                                        value="{{ old('nama_barang') }}"
                                        placeholder="Pilih dari daftar stok barang"
                                        readonly
                                        required
                                    >

                                    <button
                                        type="button"
                                        class="bm-search-btn"
                                        onclick="openBarangModal()"
                                        title="Cari barang"
                                    >
                                        <x-icon name="search"></x-icon>
                                    </button>

                                </div>

                                <input
                                    type="hidden"
                                    name="barang_id"
                                    id="barang_id"
                                    value="{{ old('barang_id') }}"
                                >

                                @error('barang_id')

                                    <div class="bm-error">
                                        {{ $message }}
                                    </div>

                                @enderror

                            </div>


                            {{-- KODE BARANG --}}

                            <div class="bm-field">

                                <label>
                                    Kode Barang
                                </label>

                                <input
                                    type="text"
                                    id="kode_barang"
                                    class="bm-input bm-readonly"
                                    value="{{ old('kode_barang') }}"
                                    readonly
                                >

                            </div>


                            {{-- SATUAN --}}

                            <div class="bm-field">

                                <label>
                                    Satuan
                                </label>

                                <input
                                    type="text"
                                    name="satuan"
                                    id="satuan"
                                    class="bm-input bm-readonly"
                                    value="{{ old('satuan') }}"
                                    placeholder="Otomatis dari master barang"
                                    readonly
                                >

                            </div>


                            {{-- INFORMASI STOK --}}

                            <div class="bm-field bm-field-full">

                                <label>
                                    Informasi Stok
                                </label>

                                <div class="bm-stock-summary">

                                    <div class="bm-stock-box">

                                        <div class="bm-stock-box-label">
                                            Stok Awal
                                        </div>

                                        <div
                                            class="bm-stock-box-value"
                                            id="stok_awal_display"
                                        >
                                            0
                                        </div>

                                    </div>


                                    <div class="bm-stock-box">

                                        <div class="bm-stock-box-label">
                                            Qty Keluar
                                        </div>

                                        <div
                                            class="bm-stock-box-value"
                                            id="qty_display"
                                        >
                                            0
                                        </div>

                                    </div>


                                    <div class="bm-stock-box">

                                        <div class="bm-stock-box-label">
                                            Sisa Stok
                                        </div>

                                        <div
                                            class="bm-stock-box-value success"
                                            id="sisa_stok_display"
                                        >
                                            0
                                        </div>

                                    </div>

                                </div>

                            </div>


                            {{-- QTY --}}

                            <div class="bm-field">

                                <label>
                                    Qty Keluar
                                    <span class="bm-required">*</span>
                                </label>

                                <input
                                    type="number"
                                    name="qty"
                                    id="qty"
                                    class="bm-input"
                                    value="{{ old('qty') }}"
                                    min="1"
                                    step="1"
                                    placeholder="0"
                                    required
                                    oninput="hitungSisaStok()"
                                >

                                @error('qty')

                                    <div class="bm-error">
                                        {{ $message }}
                                    </div>

                                @enderror

                            </div>


                            {{-- AREA / LINE --}}

                            <div class="bm-field">

                                <label>
                                    Area / Line
                                </label>

                                <select
                                    name="area_id"
                                    id="area_id"
                                    class="bm-select"
                                    onchange="loadMachines()"
                                >

                                    <option value="">
                                        -- Tidak dipilih --
                                    </option>

                                    @foreach($areas as $area)

                                        <option
                                            value="{{ $area->id }}"
                                            @selected(
                                                old('area_id') == $area->id
                                            )
                                        >
                                            {{ $area->nama_area }}
                                        </option>

                                    @endforeach

                                </select>

                                <div class="bm-help">
                                    Area / Line boleh dikosongkan.
                                </div>

                            </div>


                            {{-- MESIN --}}

                            <div class="bm-field">

                                <label>
                                    Mesin
                                </label>

                                <select
                                    name="machine_id"
                                    id="machine_id"
                                    class="bm-select"
                                >

                                    <option value="">
                                        -- Tidak dipilih --
                                    </option>

                                </select>

                                <div class="bm-help">
                                    Mesin otomatis mengikuti Area / Line.
                                </div>

                            </div>


                            {{-- DIPAKAI OLEH --}}

                            <div class="bm-field">

                                <label>
                                    Dipakai Oleh
                                    <span class="bm-required">*</span>
                                </label>

                                <select
                                    name="dipakai_oleh"
                                    class="bm-select"
                                    required
                                >

                                    <option value="">
                                        -- Pilih --
                                    </option>

                                    <option
                                        value="ME_PREV"
                                        @selected(
                                            old('dipakai_oleh') === 'ME_PREV'
                                        )
                                    >
                                        ME &amp; PREV MAINT
                                    </option>

                                    <option
                                        value="PREV"
                                        @selected(
                                            old('dipakai_oleh') === 'PREV'
                                        )
                                    >
                                        PREV MAINT
                                    </option>

                                    <option
                                        value="SIPIL"
                                        @selected(
                                            old('dipakai_oleh') === 'SIPIL'
                                        )
                                    >
                                        SIPIL
                                    </option>

                                </select>

                                @error('dipakai_oleh')

                                    <div class="bm-error">
                                        {{ $message }}
                                    </div>

                                @enderror

                            </div>


                            {{-- NO WO --}}

                            <div class="bm-field">

                                <label>
                                    No WO
                                </label>

                                <div class="bm-input-search">

                                    <input
                                        type="text"
                                        id="no_wo_display"
                                        class="bm-input"
                                        value="{{ old('no_wo_display') }}"
                                        placeholder="Pilih Work Order terkait"
                                        readonly
                                    >

                                    <button
                                        type="button"
                                        class="bm-search-btn"
                                        onclick="openWoModal()"
                                        title="Pilih Work Order"
                                    >
                                        <x-icon name="search"></x-icon>
                                    </button>

                                </div>

                                <input
                                    type="hidden"
                                    name="no_wo"
                                    id="no_wo"
                                    value="{{ old('no_wo') }}"
                                >

                                <div class="bm-help">
                                    Pilih No WO dari data Work Order.
                                </div>

                                @error('no_wo')

                                    <div class="bm-error">
                                        {{ $message }}
                                    </div>

                                @enderror

                            </div>


                            {{-- DIBUAT OLEH --}}

                            <div class="bm-field">

                                <label>
                                    Dibuat Oleh
                                </label>

                                <input
                                    type="text"
                                    class="bm-input bm-readonly"
                                    value="{{ auth()->user()->name ?? '-' }}"
                                    readonly
                                >

                                <div class="bm-help">
                                    Diambil otomatis dari user yang login.
                                </div>

                            </div>


                            {{-- KETERANGAN --}}

                            <div class="bm-field bm-field-full">

                                <label>
                                    Keterangan
                                </label>

                                <textarea
                                    name="keterangan"
                                    class="bm-textarea"
                                    placeholder="Tambahkan keterangan bila diperlukan..."
                                >{{ old('keterangan') }}</textarea>

                                @error('keterangan')

                                    <div class="bm-error">
                                        {{ $message }}
                                    </div>

                                @enderror

                            </div>

                        </div>

                    </div>


                    {{-- FOOTER --}}

                    <div class="bm-form-footer">

                        <a
                            href="{{ route('barang-keluar.index') }}"
                            class="btn btn-gray"
                        >
                            Batal
                        </a>

                        <button
                            type="submit"
                            class="btn btn-primary"
                            id="btnSimpan"
                        >
                            Simpan Barang Keluar
                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

</div>


{{-- =====================================================
     MODAL PILIH BARANG
===================================================== --}}

<div
    id="barangModal"
    class="bm-modal-overlay"
    onclick="closeBarangModal(event)"
>

    <div
        class="bm-modal"
        onclick="event.stopPropagation()"
    >

        <div class="bm-modal-header">

            <div class="bm-modal-title">

                <h3>
                    Pilih Barang
                </h3>

                <p>
                    Pilih barang dari tabel Stok Barang.
                </p>

            </div>

            <button
                type="button"
                class="bm-modal-close"
                onclick="closeBarangModal()"
            >
                ×
            </button>

        </div>


        <div class="bm-modal-search">

            <input
                type="text"
                id="searchStokBarang"
                class="bm-input"
                placeholder="Cari kode barang atau nama / spesifikasi..."
                autocomplete="off"
            >

        </div>


        <div class="bm-stock-table-wrap">

            <table class="bm-stock-table">

                <thead>

                    <tr>

                        <th>
                            Kode Barang
                        </th>

                        <th>
                            Nama Barang / Spesifikasi
                        </th>

                        <th>
                            Satuan
                        </th>

                        <th>
                            Stok
                        </th>

                        <th>
                            Aksi
                        </th>

                    </tr>

                </thead>


                <tbody id="stokBarangBody">

                    @forelse($barangs as $barang)

                        <tr
                            data-search="{{ strtolower(
                                $barang->kode_barang .
                                ' ' .
                                $barang->nama_spesifikasi
                            ) }}"
                        >

                            <td class="bm-stock-code">
                                {{ $barang->kode_barang }}
                            </td>

                            <td>
                                {{ $barang->nama_spesifikasi }}
                            </td>

                            <td>
                                {{ $barang->satuan?->nama ?? '-' }}
                            </td>

                            <td class="bm-stock-qty">
                                {{ $barang->stok }}
                            </td>

                            <td>

                                <button
                                    type="button"
                                    class="bm-pilih-btn"
                                    onclick="pilihBarang(
                                        @js($barang->id),
                                        @js($barang->kode_barang),
                                        @js($barang->nama_spesifikasi),
                                        @js($barang->satuan?->nama ?? '-'),
                                        @js($barang->stok)
                                    )"
                                >
                                    Pilih
                                </button>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="5"
                                class="bm-stock-empty"
                            >
                                Belum ada data stok barang.
                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>


{{-- =====================================================
     MODAL PILIH WORK ORDER
===================================================== --}}

<div
    id="woModal"
    class="bm-modal-overlay"
    onclick="closeWoModal(event)"
>

    <div
        class="bm-modal"
        onclick="event.stopPropagation()"
    >

        <div class="bm-modal-header">

            <div class="bm-modal-title">

                <h3>
                    Pilih Work Order
                </h3>

                <p>
                    Pilih No WO dari data Work Order.
                </p>

            </div>

            <button
                type="button"
                class="bm-modal-close"
                onclick="closeWoModal()"
            >
                ×
            </button>

        </div>


        <div class="bm-modal-search">

            <input
                type="text"
                id="searchWorkOrder"
                class="bm-input"
                placeholder="Cari No WO, job, mesin, atau area..."
                autocomplete="off"
            >

        </div>


        <div class="bm-stock-table-wrap">

            <table class="bm-stock-table">

                <thead>

                    <tr>

                        <th>
                            No WO
                        </th>

                        <th>
                            Job / Deskripsi
                        </th>

                        <th>
                            Mesin
                        </th>

                        <th>
                            Status
                        </th>

                        <th>
                            Aksi
                        </th>

                    </tr>

                </thead>


                <tbody id="workOrderBody">

                    @forelse($workOrders as $workOrder)

                        <tr
                            data-search="{{ strtolower(
                                ($workOrder->no_wo ?? '') .
                                ' ' .
                                ($workOrder->job ?? '') .
                                ' ' .
                                ($workOrder->mesin ?? '') .
                                ' ' .
                                ($workOrder->area ?? '')
                            ) }}"
                        >

                            <td class="bm-stock-code">
                                {{ $workOrder->no_wo }}
                            </td>

                            <td>
                                {{ $workOrder->job ?: '-' }}
                            </td>

                            <td>
                                {{ $workOrder->mesin ?: '-' }}
                            </td>

                            <td>
                                {{ $workOrder->status ?: '-' }}
                            </td>

                            <td>

                                <button
                                    type="button"
                                    class="bm-pilih-btn"
                                    onclick="pilihWorkOrder(
                                        @js($workOrder->no_wo),
                                        @js($workOrder->job ?? '')
                                    )"
                                >
                                    Pilih
                                </button>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="5"
                                class="bm-stock-empty"
                            >
                                Belum ada Work Order.
                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

@endsection


@push('scripts')

<script>

    let stokSekarang = 0;


    /* =====================================================
       MODAL BARANG
    ===================================================== */

    function openBarangModal()
    {
        const modal =
            document.getElementById('barangModal');

        const searchInput =
            document.getElementById('searchStokBarang');

        modal.classList.add('active');

        searchInput.value = '';

        filterStokBarang();

        setTimeout(function()
        {
            searchInput.focus();
        }, 100);
    }


    function closeBarangModal(event = null)
    {
        const modal =
            document.getElementById('barangModal');

        if (
            event &&
            event.target !== modal
        ) {
            return;
        }

        modal.classList.remove('active');
    }


    function filterStokBarang()
    {
        const searchInput =
            document.getElementById('searchStokBarang');

        const keyword =
            searchInput.value
                .toLowerCase()
                .trim();

        document
            .querySelectorAll('#stokBarangBody tr')
            .forEach(function(row)
            {
                const text =
                    row.dataset.search || '';

                row.style.display =
                    text.includes(keyword)
                        ? ''
                        : 'none';
            });
    }


    function pilihBarang(
        id,
        kode,
        nama,
        satuan,
        stok
    )
    {
        document.getElementById('barang_id').value = id;

        document.getElementById('kode_barang').value = kode;

        document.getElementById('nama_barang').value = nama;

        document.getElementById('satuan').value = satuan;

        stokSekarang =
            parseInt(stok) || 0;

        document.getElementById(
            'stok_awal_display'
        ).textContent =
            formatNumber(stokSekarang) +
            ' ' +
            satuan;

        document.getElementById('qty').value = '';

        document.getElementById(
            'qty_display'
        ).textContent = '0';

        document.getElementById(
            'sisa_stok_display'
        ).textContent =
            formatNumber(stokSekarang) +
            ' ' +
            satuan;

        const sisa =
            document.getElementById(
                'sisa_stok_display'
            );

        sisa.classList.remove('danger');

        sisa.classList.add('success');

        closeBarangModal();
    }


    /* =====================================================
       HITUNG STOK
    ===================================================== */

    function hitungSisaStok()
    {
        const qty =
            parseInt(
                document.getElementById('qty').value
            ) || 0;

        const satuan =
            document.getElementById('satuan').value || '';

        const sisaElement =
            document.getElementById(
                'sisa_stok_display'
            );

        document.getElementById(
            'qty_display'
        ).textContent =
            formatNumber(qty);

        if (qty > stokSekarang) {

            sisaElement.textContent =
                'Stok tidak cukup';

            sisaElement.classList.remove(
                'success'
            );

            sisaElement.classList.add(
                'danger'
            );

        } else {

            const sisa =
                stokSekarang - qty;

            sisaElement.textContent =
                formatNumber(sisa) +
                ' ' +
                satuan;

            sisaElement.classList.remove(
                'danger'
            );

            sisaElement.classList.add(
                'success'
            );
        }
    }


    function formatNumber(number)
    {
        return new Intl.NumberFormat(
            'id-ID'
        ).format(number);
    }


    /* =====================================================
       MESIN BERDASARKAN AREA
    ===================================================== */

    async function loadMachines()
    {
        const areaId =
            document.getElementById(
                'area_id'
            ).value;

        const machineSelect =
            document.getElementById(
                'machine_id'
            );

        machineSelect.innerHTML =
            '<option value="">Memuat mesin...</option>';

        if (!areaId) {

            machineSelect.innerHTML =
                '<option value="">-- Tidak dipilih --</option>';

            return;
        }

        try {

            const response =
                await fetch(
                    `{{ url('/barang-keluar/area') }}/${areaId}/machines`
                );

            if (!response.ok) {

                throw new Error(
                    'Gagal mengambil data mesin.'
                );
            }

            const machines =
                await response.json();

            machineSelect.innerHTML =
                '<option value="">-- Tidak dipilih --</option>';

            machines.forEach(function(machine)
            {
                const option =
                    document.createElement('option');

                option.value =
                    machine.id;

                option.textContent =
                    machine.code +
                    ' - ' +
                    machine.name;

                machineSelect.appendChild(option);
            });

        } catch (error) {

            machineSelect.innerHTML =
                '<option value="">Gagal memuat mesin</option>';

            console.error(error);
        }
    }


    /* =====================================================
       MODAL WORK ORDER
    ===================================================== */

    function openWoModal()
    {
        const modal =
            document.getElementById('woModal');

        const searchInput =
            document.getElementById('searchWorkOrder');

        modal.classList.add('active');

        searchInput.value = '';

        filterWorkOrder();

        setTimeout(function()
        {
            searchInput.focus();
        }, 100);
    }


    function closeWoModal(event = null)
    {
        const modal =
            document.getElementById('woModal');

        if (
            event &&
            event.target !== modal
        ) {
            return;
        }

        modal.classList.remove('active');
    }


    function filterWorkOrder()
    {
        const searchInput =
            document.getElementById('searchWorkOrder');

        const keyword =
            searchInput.value
                .toLowerCase()
                .trim();

        document
            .querySelectorAll('#workOrderBody tr')
            .forEach(function(row)
            {
                const text =
                    row.dataset.search || '';

                row.style.display =
                    text.includes(keyword)
                        ? ''
                        : 'none';
            });
    }


    /* =====================================================
       PILIH WORK ORDER

       DATABASE:
       no_wo = WO-20

       TAMPILAN FORM:
       WO-20 - Kerusakan pada mesin sealer
    ===================================================== */

    function pilihWorkOrder(
        noWo,
        job
    )
    {
        document.getElementById(
            'no_wo'
        ).value = noWo;

        const display =
            job &&
            job.trim() !== ''
                ? noWo + ' - ' + job
                : noWo;

        document.getElementById(
            'no_wo_display'
        ).value = display;

        closeWoModal();
    }


    /* =====================================================
       SEARCH EVENT
    ===================================================== */

    const searchStokBarang =
        document.getElementById(
            'searchStokBarang'
        );

    if (searchStokBarang) {

        searchStokBarang.addEventListener(
            'input',
            filterStokBarang
        );
    }


    const searchWorkOrder =
        document.getElementById(
            'searchWorkOrder'
        );

    if (searchWorkOrder) {

        searchWorkOrder.addEventListener(
            'input',
            filterWorkOrder
        );
    }


    /* =====================================================
       ESCAPE
    ===================================================== */

    document.addEventListener(
        'keydown',
        function(event)
        {
            if (event.key === 'Escape') {

                closeBarangModal();

                closeWoModal();

            }
        }
    );


    /* =====================================================
       SUBMIT FORM
    ===================================================== */

    document
        .getElementById('barangKeluarForm')
        .addEventListener(
            'submit',
            function(event)
            {
                const barangId =
                    document.getElementById(
                        'barang_id'
                    ).value;

                const qty =
                    parseInt(
                        document.getElementById(
                            'qty'
                        ).value
                    ) || 0;

                if (!barangId) {

                    event.preventDefault();

                    alert(
                        'Silakan pilih Barang / Sparepart terlebih dahulu.'
                    );

                    return;
                }

                if (qty <= 0) {

                    event.preventDefault();

                    alert(
                        'Qty Keluar harus lebih dari 0.'
                    );

                    return;
                }

                if (qty > stokSekarang) {

                    event.preventDefault();

                    alert(
                        'Qty Keluar melebihi stok yang tersedia.'
                    );

                    return;
                }
            }
        );

</script>

@endpush