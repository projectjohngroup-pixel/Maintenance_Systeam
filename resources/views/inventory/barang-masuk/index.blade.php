@extends('layouts.app')

@section('title', 'Barang Masuk')

@section('page_title', 'Inventory')

@section(
    'page_subtitle',
    'Pencatatan dan penerimaan barang masuk'
)

@push('styles')

<style>

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

.inventory-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 15px;
    margin-bottom: 20px;
}

.inventory-title h1 {
    margin: 0 0 5px;
    font-size: 24px;
}

.inventory-title p {
    margin: 0;
    color: var(--pds-muted);
    font-size: 13px;
}

.inventory-actions {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}

.inventory-summary {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
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

.inventory-filter-card {
    padding: 15px 18px;
    margin-bottom: 20px;
}

.inventory-filter {
    display: flex;
    align-items: flex-end;
    gap: 10px;
    width: 100%;
    flex-wrap: wrap;
}

.inventory-filter-group {
    flex: 1;
    min-width: 200px;
}

.inventory-filter-label {
    display: block;
    margin-bottom: 6px;
    color: var(--pds-ink-2);
    font-size: 12px;
    font-weight: 700;
}

.inventory-search,
.inventory-filter-control {
    width: 100%;
    height: 42px;
    padding: 0 13px;
    border: 1px solid var(--pds-line-2);
    border-radius: 8px;
    background: var(--pds-card);
    font-size: 13px;
}

.inventory-search:focus,
.inventory-filter-control:focus,
.inventory-form-control:focus,
.inventory-form-textarea:focus {
    outline: none;
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37,99,235,.08);
}

.inventory-filter-actions {
    display: flex;
    gap: 8px;
}

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

.inventory-table-info {
    color: var(--pds-muted);
    font-size: 12px;
}

.inventory-table-wrapper {
    overflow-x: auto;
}

.inventory-table {
    width: 100%;
    min-width: 1050px;
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

.inventory-number {
    color: var(--pds-muted);
    width: 55px;
}

.inventory-transaction {
    font-weight: 700;
    color: var(--pds-ink);
    white-space: nowrap;
}

.inventory-date {
    color: var(--pds-ink-2);
    white-space: nowrap;
}

.inventory-qty {
    font-weight: 700;
    color: var(--pds-ink);
    white-space: nowrap;
}

.inventory-actions-cell {
    display: flex;
    align-items: center;
    gap: 6px;
}

.inventory-action {
    min-width: 34px;
    height: 34px;
    padding: 0 10px;
    border-radius: 8px;
    border: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 11px;
    font-weight: 600;
    text-decoration: none;
}

.inventory-action-view {
    background: #eff6ff;
    color: #2563eb;
}

.inventory-action-edit {
    background: #fffbeb;
    color: #d97706;
}

.inventory-action-delete {
    background: #fef2f2;
    color: #dc2626;
}

.inventory-empty {
    text-align: center;
    padding: 45px 20px !important;
    color: var(--pds-muted-2);
}

.inventory-pagination {
    margin-top: 18px;
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
    max-width: 760px;
    max-height: calc(100vh - 40px);
    overflow-y: auto;
    background: var(--pds-card);
    border-radius: 16px;
    box-shadow: 0 25px 70px rgba(0,0,0,.25);
}

.inventory-modal-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 12px;
    padding: 20px 22px;
    border-bottom: 1px solid var(--pds-line);
}

.inventory-modal-header h2 {
    margin: 0 0 5px;
    font-size: 20px;
}

.inventory-modal-header p {
    margin: 0;
    color: var(--pds-muted);
    font-size: 13px;
}

.inventory-modal-close {
    width: 34px;
    height: 34px;
    border: none;
    border-radius: 8px;
    background: var(--pds-soft-2);
    color: var(--pds-ink-2);
    cursor: pointer;
    font-size: 20px;
}

.inventory-modal-body {
    padding: 22px;
}

.inventory-form-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 16px;
}

.inventory-form-full {
    grid-column: 1 / -1;
}

.inventory-form-group label {
    display: block;
    margin-bottom: 7px;
    font-size: 13px;
    font-weight: 700;
    color: var(--pds-ink-2);
}

.inventory-required {
    color: #dc2626;
}

.inventory-form-control {
    width: 100%;
    height: 42px;
    padding: 0 11px;
    border: 1px solid var(--pds-line-2);
    border-radius: 8px;
    background: var(--pds-card);
    font-size: 13px;
}

.inventory-form-control.readonly {
    background: var(--pds-soft);
    color: var(--pds-ink-2);
    font-weight: 700;
}

.inventory-form-textarea {
    width: 100%;
    min-height: 110px;
    padding: 10px 11px;
    border: 1px solid var(--pds-line-2);
    border-radius: 8px;
    background: var(--pds-card);
    font-size: 13px;
    resize: vertical;
}

.inventory-form-error {
    margin-top: 5px;
    color: #dc2626;
    font-size: 11px;
}

.inventory-search-row {
    display: flex;
    gap: 8px;
}

.inventory-search-row .inventory-form-control {
    flex: 1;
}

.inventory-search-button {
    width: 44px;
    height: 42px;
    border: 1px solid var(--pds-line-2);
    border-radius: 8px;
    background: var(--pds-soft);
    cursor: pointer;
    font-size: 17px;
}

.inventory-modal-footer {
    display: flex;
    justify-content: flex-end;
    gap: 8px;
    padding: 18px 22px;
    border-top: 1px solid var(--pds-line);
    background: var(--pds-soft);
}


/* =====================================================
   PICKER BARANG
===================================================== */

.barang-picker-modal {
    width: 100%;
    max-width: 950px;
    max-height: 85vh;
    overflow: hidden;
    background: var(--pds-card);
    border-radius: 16px;
    box-shadow: 0 25px 70px rgba(0,0,0,.25);
}

.barang-picker-search {
    padding: 15px 20px;
    border-bottom: 1px solid var(--pds-line);
}

.barang-picker-table-wrap {
    max-height: 55vh;
    overflow: auto;
}

.barang-picker-table {
    width: 100%;
    min-width: 700px;
    border-collapse: collapse;
}

.barang-picker-table th,
.barang-picker-table td {
    padding: 12px 14px;
    border-bottom: 1px solid var(--pds-line);
    text-align: left;
    font-size: 13px;
}

.barang-picker-table th {
    position: sticky;
    top: 0;
    background: var(--pds-soft);
    z-index: 2;
}

.barang-picker-code {
    font-weight: 700;
}

.barang-picker-button {
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


/* =====================================================
   RESPONSIVE
===================================================== */

@media (max-width: 900px) {

    .inventory-filter {
        flex-direction: column;
        align-items: stretch;
    }

    .inventory-filter-group {
        min-width: 100%;
    }

    .inventory-filter-actions {
        width: 100%;
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

    .inventory-table-header {
        flex-direction: column;
        align-items: flex-start;
    }

    .inventory-form-grid {
        grid-template-columns: 1fr;
    }

    .inventory-form-full {
        grid-column: auto;
    }

    .inventory-modal {
        max-height: 92vh;
    }

}

</style>

@endpush


@section('content')


<!-- =====================================================
     MENU INVENTORY
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
        class="inventory-module-link active"
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
        href="{{ route('purchase-requests.index') }}"
        class="inventory-module-link"
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
            Barang Masuk
        </h1>

        <p>
            Pencatatan dan penerimaan barang masuk.
        </p>

    </div>

    <div class="inventory-actions">

        <a
            href="{{ route('dashboard') }}"
            class="btn btn-gray"
        >
            <x-icon name="back"></x-icon> Kembali
        </a>

        <button
            type="button"
            class="btn btn-primary"
            onclick="openBarangMasukModal()"
        >
            + Barang Masuk
        </button>

    </div>

</div>


<!-- =====================================================
     SUMMARY
===================================================== -->

<div class="inventory-summary">

    <div class="inventory-summary-card">

        <div class="inventory-summary-label">
            Jumlah Transaksi
        </div>

        <div class="inventory-summary-value">
            {{ $jumlahTransaksi }}
        </div>

    </div>


    <div class="inventory-summary-card">

        <div class="inventory-summary-label">
            Total Qty Masuk
        </div>

        <div class="inventory-summary-value">

            {{
                pdsNumber($totalMasuk, ',', '.')
            }}

        </div>

    </div>

</div>


<!-- =====================================================
     FILTER
===================================================== -->

<div class="card inventory-filter-card">

    <form
        method="GET"
        action="{{ route('barang-masuk.index') }}"
        class="inventory-filter"
    >

        <div class="inventory-filter-group">

            <label class="inventory-filter-label">
                Search
            </label>

            <input
                type="text"
                name="search"
                class="inventory-search"
                value="{{ request('search') }}"
                placeholder="Cari no transaksi atau nama barang / spesifikasi..."
            >

        </div>


        <div class="inventory-filter-group">

            <label class="inventory-filter-label">
                Tanggal Dari
            </label>

            <input
                type="date"
                name="tanggal_dari"
                class="inventory-filter-control"
                value="{{ request('tanggal_dari') }}"
            >

        </div>


        <div class="inventory-filter-group">

            <label class="inventory-filter-label">
                Tanggal Sampai
            </label>

            <input
                type="date"
                name="tanggal_sampai"
                class="inventory-filter-control"
                value="{{ request('tanggal_sampai') }}"
            >

        </div>


        <div class="inventory-filter-actions">

            <button
                type="submit"
                class="btn btn-primary"
            >
                Cari
            </button>

            <a
                href="{{ route('barang-masuk.index') }}"
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
                Daftar Barang Masuk
            </div>

            <div class="inventory-table-info">
                {{ $barangMasuks->count() }}
                data pada halaman ini
            </div>

        </div>

    </div>


    <div class="inventory-table-wrapper">

        <table class="inventory-table">

            <thead>

                <tr>

                    <th>No</th>
                    <th>No Transaksi</th>
                    <th>Tanggal</th>
                    <th>Kode Barang</th>
                    <th>Nama Barang / Spesifikasi</th>
                    <th>Qty</th>
                    <th>Satuan</th>
                    <th>Keterangan</th>
                    <th>Received By</th>
                    <th>Aksi</th>

                </tr>

            </thead>


            <tbody>

                @forelse(
                    $barangMasuks
                    as $index
                    => $barangMasuk
                )

                    <tr>

                        <td class="inventory-number">
                            {{ $barangMasuks->firstItem() + $index }}
                        </td>

                        <td class="inventory-transaction">
                            {{ $barangMasuk->no_transaksi }}
                        </td>

                        <td class="inventory-date">
                            {{
                                $barangMasuk
                                    ->tanggal_masuk
                                    ?->format('d/m/Y')
                            }}
                        </td>

                        <td class="inventory-transaction">
                            {{ $barangMasuk->barang?->kode_barang ?? '-' }}
                        </td>

                        <td>
                            {{ $barangMasuk->barang?->nama_spesifikasi ?? '-' }}
                        </td>

                        <td class="inventory-qty">
                            {{
                                pdsNumber($barangMasuk->qty, ',', '.')
                            }}
                        </td>

                        <td>
                            {{
                                $barangMasuk
                                    ->satuan?->nama
                                ?? $barangMasuk
                                    ->barang?->satuan?->nama
                                ?? '-'
                            }}
                        </td>

                        <td>
                            {{ $barangMasuk->keterangan ?: '-' }}
                        </td>

                        <td>
                            {{
                                $barangMasuk
                                    ->receivedBy
                                    ?->name
                                ?? '-'
                            }}
                        </td>

                        <td>

                            <div class="inventory-actions-cell">

                                <a
                                    href="{{
                                        route(
                                            'barang-masuk.show',
                                            $barangMasuk
                                        )
                                    }}"
                                    class="act-btn act-view"
                                    title="Lihat Detail Transaksi"
                                    aria-label="Lihat Detail Transaksi"
                                >
                                    <x-icon name="eye" />
                                </a>

                                <a
                                    href="{{
                                        route(
                                            'barang-masuk.edit',
                                            $barangMasuk
                                        )
                                    }}"
                                    class="act-btn act-edit"
                                    title="Edit Transaksi"
                                    aria-label="Edit Transaksi"
                                >
                                    <x-icon name="edit" />
                                </a>

                                <form
                                    action="{{
                                        route(
                                            'barang-masuk.destroy',
                                            $barangMasuk
                                        )
                                    }}"
                                    method="POST"
                                    data-confirm="Hapus transaksi barang masuk ini? Stok akan dikembalikan."
                                    style="display:inline;"
                                >

                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="act-btn act-delete"
                                        title="Hapus Transaksi"
                                        aria-label="Hapus Transaksi"
                                    >
                                        <x-icon name="trash" />
                                    </button>

                                </form>

                            </div>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td
                            colspan="10"
                            class="inventory-empty"
                        >
                            Belum ada data barang masuk.
                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>


    @if(
        method_exists($barangMasuks, 'hasPages')
        &&
        $barangMasuks->hasPages()
    )

        <div class="inventory-pagination">
            {{ $barangMasuks->links() }}
        </div>

    @endif

</div>


<!-- =====================================================
     MODAL TAMBAH BARANG MASUK
===================================================== -->

<div
    id="barangMasukModal"
    class="inventory-modal-overlay"
    onclick="closeBarangMasukOverlay(event)"
>

    <div class="inventory-modal">

        <div class="inventory-modal-header">

            <div>

                <h2>
                    Tambah Barang Masuk
                </h2>

                <p>
                    Isi data penerimaan barang.
                </p>

            </div>

            <button
                type="button"
                class="inventory-modal-close"
                onclick="closeBarangMasukModal()"
            >
                Ã—
            </button>

        </div>


        @if($errors->any())

            <div style="padding:18px 22px 0;">

                <div
                    style="
                        padding:12px 14px;
                        border-radius:8px;
                        background:#fef2f2;
                        color:#991b1b;
                        border:1px solid #fecaca;
                        font-size:13px;
                    "
                >

                    <strong>
                        Data belum dapat disimpan.
                    </strong>

                    <ul style="margin:7px 0 0 18px;">

                        @foreach($errors->all() as $error)

                            <li>
                                {{ $error }}
                            </li>

                        @endforeach

                    </ul>

                </div>

            </div>

        @endif


        <form
            action="{{ route('barang-masuk.store') }}"
            method="POST"
        >

            @csrf

            <div class="inventory-modal-body">

                <div class="inventory-form-grid">


                    <!-- NO TRANSAKSI -->

                    <div class="inventory-form-group">

                        <label>
                            No Transaksi
                        </label>

                        <input
                            type="text"
                            class="inventory-form-control readonly"
                            value="{{ 'BM-' . str_pad($nextNoTransaksi, 3, '0', STR_PAD_LEFT) }}"
                            readonly
                        >

                    </div>


                    <!-- TANGGAL -->

                    <div class="inventory-form-group">

                        <label>
                            Tanggal Masuk
                            <span class="inventory-required">*</span>
                        </label>

                        <input
                            type="date"
                            name="tanggal_masuk"
                            class="inventory-form-control"
                            value="{{ old(
                                'tanggal_masuk',
                                now()->format('Y-m-d')
                            ) }}"
                            required
                        >

                        @error('tanggal_masuk')

                            <div class="inventory-form-error">
                                {{ $message }}
                            </div>

                        @enderror

                    </div>


                    <!-- BARANG (ID TERSEMBUNYI) -->

                    <input
                        type="hidden"
                        name="barang_id"
                        id="barang_id"
                        value="{{ old('barang_id') }}"
                    >


                    <!-- KODE BARANG -->

                    <div class="inventory-form-group">

                        <label>
                            Kode Barang
                        </label>

                        <input
                            type="text"
                            name="kode_barang_display"
                            id="kode_barang"
                            class="inventory-form-control readonly"
                            value="{{ old('kode_barang_display') }}"
                            placeholder="Otomatis saat pilih barang"
                            readonly
                        >

                        @error('barang_id')

                            <div class="inventory-form-error">
                                {{ $message }}
                            </div>

                        @enderror

                    </div>


                    <!-- NAMA BARANG -->

                    <div
                        class="
                            inventory-form-group
                            inventory-form-full
                        "
                    >

                        <label>
                            Nama Barang / Spesifikasi
                            <span class="inventory-required">*</span>
                        </label>

                        <div class="inventory-search-row">

                            <input
                                type="text"
                                name="nama_barang_display"
                                id="nama_barang_spesifikasi"
                                class="inventory-form-control"
                                value="{{ old('nama_barang_display') }}"
                                placeholder="Pilih dari stok barang"
                                readonly
                            >

                            <button
                                type="button"
                                class="inventory-search-button"
                                onclick="openBarangPicker()"
                                title="Pilih barang"
                            >
                                <x-icon name="search"></x-icon>
                            </button>

                        </div>

                        @error('barang_id')

                            <div class="inventory-form-error">
                                {{ $message }}
                            </div>

                        @enderror

                    </div>


                    <!-- QTY -->

                    <div class="inventory-form-group">

                        <label>
                            Qty
                            <span class="inventory-required">*</span>
                        </label>

                        <input
                            type="number"
                            name="qty"
                            class="inventory-form-control"
                            value="{{ old('qty') }}"
                            min="0.01"
                            step="0.01"
                            placeholder="0"
                            required
                        >

                        @error('qty')

                            <div class="inventory-form-error">
                                {{ $message }}
                            </div>

                        @enderror

                    </div>


                    <!-- SATUAN -->

                    <div class="inventory-form-group">

                        <label>
                            Satuan
                        </label>

                        <input
                            type="text"
                            name="satuan_display"
                            id="satuan"
                            class="inventory-form-control readonly"
                            value="{{ old('satuan_display') }}"
                            placeholder="Otomatis dari master barang"
                            readonly
                        >

                    </div>


                    <!-- RECEIVED BY -->

                    <div class="inventory-form-group">

                        <label>
                            Received By
                        </label>

                        <input
                            type="text"
                            class="inventory-form-control readonly"
                            value="{{ auth()->user()->name ?? '-' }}"
                            readonly
                        >

                    </div>


                    <!-- KETERANGAN -->

                    <div
                        class="
                            inventory-form-group
                            inventory-form-full
                        "
                    >

                        <label>
                            Keterangan
                        </label>

                        <textarea
                            name="keterangan"
                            class="inventory-form-textarea"
                            placeholder="Tambahkan keterangan bila diperlukan..."
                        >{{ old('keterangan') }}</textarea>

                        @error('keterangan')

                            <div class="inventory-form-error">
                                {{ $message }}
                            </div>

                        @enderror

                    </div>

                </div>

            </div>


            <div class="inventory-modal-footer">

                <button
                    type="button"
                    class="btn btn-gray"
                    onclick="closeBarangMasukModal()"
                >
                    Batal
                </button>

                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    Simpan Barang Masuk
                </button>

            </div>

        </form>

    </div>

</div>


<!-- =====================================================
     MODAL PILIH BARANG
===================================================== -->

<div
    id="barangPickerModal"
    class="inventory-modal-overlay"
    onclick="closeBarangPickerOverlay(event)"
>

    <div class="barang-picker-modal">

        <div class="inventory-modal-header">

            <div>

                <h2>
                    Pilih Barang
                </h2>

                <p>
                    Pilih dari master stok barang.
                </p>

            </div>

            <button
                type="button"
                class="inventory-modal-close"
                onclick="closeBarangPicker()"
            >
                Ã—
            </button>

        </div>


        <div class="barang-picker-search">

            <input
                type="text"
                id="searchStokBarang"
                class="inventory-form-control"
                placeholder="Cari kode barang atau nama / spesifikasi..."
                autocomplete="off"
            >

        </div>


        <div class="barang-picker-table-wrap">

            <table class="barang-picker-table">

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

                    @forelse($barangs ?? [] as $barang)

                        <tr
                            data-search="{{ strtolower(
                                $barang->kode_barang .
                                ' ' .
                                $barang->nama_spesifikasi
                            ) }}"
                        >

                            <td class="barang-picker-code">
                                {{ $barang->kode_barang }}
                            </td>

                            <td>
                                {{ $barang->nama_spesifikasi }}
                            </td>

                            <td>
                                {{ $barang->satuan?->nama ?? '-' }}
                            </td>

                            <td>
                                {{ $barang->stok }}
                            </td>

                            <td>

                                <button
                                    type="button"
                                    class="barang-picker-button"
                                    onclick="pilihBarang(
                                        @js($barang->id),
                                        @js($barang->kode_barang),
                                        @js($barang->nama_spesifikasi),
                                        @js($barang->satuan?->nama ?? '-')
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
                                style="
                                    padding:40px;
                                    text-align:center;
                                    color:var(--pds-muted-2);
                                "
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


@endsection


@push('scripts')

<script>

function openBarangMasukModal()
{
    const modal =
        document.getElementById(
            'barangMasukModal'
        );

    if (modal) {
        modal.classList.add('active');
    }
}


function closeBarangMasukModal()
{
    const modal =
        document.getElementById(
            'barangMasukModal'
        );

    if (modal) {
        modal.classList.remove('active');
    }
}


function closeBarangMasukOverlay(event)
{
    const modal =
        document.getElementById(
            'barangMasukModal'
        );

    if (
        modal &&
        event.target === modal
    ) {
        closeBarangMasukModal();
    }
}


function openBarangPicker()
{
    const modal =
        document.getElementById(
            'barangPickerModal'
        );

    const search =
        document.getElementById(
            'searchStokBarang'
        );

    if (!modal) {
        return;
    }

    modal.classList.add('active');

    if (search) {

        search.value = '';

        filterStokBarang();

        setTimeout(
            function () {
                search.focus();
            },
            100
        );
    }
}


function closeBarangPicker()
{
    const modal =
        document.getElementById(
            'barangPickerModal'
        );

    if (modal) {
        modal.classList.remove('active');
    }
}


function closeBarangPickerOverlay(event)
{
    const modal =
        document.getElementById(
            'barangPickerModal'
        );

    if (
        modal &&
        event.target === modal
    ) {
        closeBarangPicker();
    }
}


function filterStokBarang()
{
    const search =
        document.getElementById(
            'searchStokBarang'
        );

    if (!search) {
        return;
    }

    const keyword =
        search.value
            .toLowerCase()
            .trim();

    document
        .querySelectorAll(
            '#stokBarangBody tr'
        )
        .forEach(
            function (row) {

                const text =
                    row.dataset.search || '';

                row.style.display =
                    text.includes(keyword)
                        ? ''
                        : 'none';

            }
        );
}


function pilihBarang(
    id,
    kode,
    nama,
    satuan
)
{
    const idInput =
        document.getElementById(
            'barang_id'
        );

    const kodeInput =
        document.getElementById(
            'kode_barang'
        );

    const namaInput =
        document.getElementById(
            'nama_barang_spesifikasi'
        );

    const satuanInput =
        document.getElementById(
            'satuan'
        );

    if (idInput) {
        idInput.value = id;
    }

    if (kodeInput) {
        kodeInput.value = kode;
    }

    if (namaInput) {
        namaInput.value = nama;
    }

    if (satuanInput) {
        satuanInput.value = satuan;
    }

    closeBarangPicker();
}


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


document.addEventListener(
    'keydown',
    function (event) {

        if (event.key === 'Escape') {

            closeBarangPicker();
            closeBarangMasukModal();

        }

    }
);

</script>

@endpush