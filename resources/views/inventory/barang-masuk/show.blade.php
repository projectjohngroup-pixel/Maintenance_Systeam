@extends('layouts.app')

@section('title', 'Detail Barang Masuk')

@section('page_title', 'Inventory')

@section(
    'page_subtitle',
    'Detail transaksi barang masuk'
)

@push('styles')

<style>

.bkm-page {
    padding: 4px 0 30px;
}

.bkm-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 20px;
    margin-bottom: 22px;
}

.bkm-title h2 {
    margin: 0 0 5px;
    font-size: 24px;
    font-weight: 700;
    color: var(--pds-ink);
}

.bkm-title p {
    margin: 0;
    color: var(--pds-muted);
    font-size: 13px;
}

.bkm-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 16px;
}

.bkm-card {
    background: var(--pds-card);
    border: 1px solid var(--pds-line);
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,.04);
}

.bkm-card.full {
    grid-column: 1 / -1;
}

.bkm-card-header {
    padding: 15px 18px;
    border-bottom: 1px solid var(--pds-line);
    font-size: 14px;
    font-weight: 700;
    color: var(--pds-ink);
}

.bkm-card-body {
    padding: 6px 0;
}

.bkm-row {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 16px;
    padding: 10px 18px;
}

.bkm-row:nth-child(even) {
    background: #fafbfc;
}

.bkm-label {
    color: var(--pds-muted);
    font-size: 13px;
    white-space: nowrap;
    min-width: 150px;
}

.bkm-value {
    color: var(--pds-ink);
    font-size: 13px;
    font-weight: 600;
    text-align: right;
    word-break: break-word;
}

.bkm-value.plain {
    font-weight: 400;
    color: var(--pds-ink-2);
}

.badge {
    display: inline-flex;
    align-items: center;
    padding: 3px 10px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 700;
    white-space: nowrap;
}

.badge-success {
    background: #dcfce7;
    color: #15803d;
}

.badge-danger {
    background: #fee2e2;
    color: #b91c1c;
}

@media (max-width: 800px) {

    .bkm-header {
        flex-direction: column;
        align-items: flex-start;
    }

    .bkm-grid {
        grid-template-columns: 1fr;
    }

    .bkm-row {
        flex-direction: column;
        gap: 3px;
    }

    .bkm-value {
        text-align: left;
    }

}

</style>

@endpush


@section('content')

<div class="bkm-page">


    <!-- =================================================
         HEADER
    ================================================== -->

    <div class="bkm-header">

        <div class="bkm-title">

            <h2>
                Detail Barang Masuk
            </h2>

            <p>
                {{ $barangMasuk->no_transaksi }}
                &bull;
                {{ $barangMasuk->tanggal_masuk?->format('d/m/Y') }}
            </p>

        </div>


        <div class="bkm-header-actions">

            <a
                href="{{ route('barang-masuk.index') }}"
                class="btn btn-gray"
            >
                <x-icon name="back"></x-icon> Kembali
            </a>


            @if($barangMasuk->status !== 'CANCELLED')

                <a
                    href="{{ route('barang-masuk.edit', $barangMasuk) }}"
                    class="btn btn-primary"
                >
                    Edit Transaksi
                </a>

            @endif

        </div>

    </div>


    <!-- =================================================
         DETAIL CARDS
    ================================================== -->

    <div class="bkm-grid">


        <!-- TRANSAKSI -->

        <div class="bkm-card">

            <div class="bkm-card-header">
                Informasi Transaksi
            </div>

            <div class="bkm-card-body">

                <div class="bkm-row">
                    <span class="bkm-label">No Transaksi</span>
                    <span class="bkm-value">{{ $barangMasuk->no_transaksi }}</span>
                </div>

                <div class="bkm-row">
                    <span class="bkm-label">Tanggal Masuk</span>
                    <span class="bkm-value">{{ $barangMasuk->tanggal_masuk?->format('d/m/Y') }}</span>
                </div>

                <div class="bkm-row">
                    <span class="bkm-label">Status</span>
                    <span class="bkm-value">
                        @if($barangMasuk->status === 'CANCELLED')
                            <span class="badge badge-danger">DIBATALKAN</span>
                        @else
                            <span class="badge badge-success">DITERIMA</span>
                        @endif
                    </span>
                </div>

                <div class="bkm-row">
                    <span class="bkm-label">Received By</span>
                    <span class="bkm-value plain">
                        {{ $barangMasuk->receivedBy?->name ?? '-' }}
                    </span>
                </div>

            </div>

        </div>


        <!-- BARANG -->

        <div class="bkm-card">

            <div class="bkm-card-header">
                Barang & Stok
            </div>

            <div class="bkm-card-body">

                <div class="bkm-row">
                    <span class="bkm-label">Kode Barang</span>
                    <span class="bkm-value">{{ $barangMasuk->barang?->kode_barang ?? '-' }}</span>
                </div>

                <div class="bkm-row">
                    <span class="bkm-label">Nama / Spesifikasi</span>
                    <span class="bkm-value plain">{{ $barangMasuk->barang?->nama_spesifikasi ?? '-' }}</span>
                </div>

                <div class="bkm-row">
                    <span class="bkm-label">Qty Masuk</span>
                    <span class="bkm-value">
                        {{
                            pdsNumber($barangMasuk->qty, ',', '.')
                        }}
                        {{ $barangMasuk->satuan?->nama ?? '' }}
                    </span>
                </div>

                <div class="bkm-row">
                    <span class="bkm-label">Stok Saat Ini</span>
                    <span class="bkm-value plain">
                        {{ pdsNumber($barangMasuk->barang?->stok ?? 0, ',', '.') }}
                        {{ $barangMasuk->satuan?->nama ?? '' }}
                    </span>
                </div>

            </div>

        </div>


        <!-- SUPPLIER -->

        <div class="bkm-card full">

            <div class="bkm-card-header">
                Supplier & Faktur
            </div>

            <div class="bkm-card-body">

                <div class="bkm-row">
                    <span class="bkm-label">Supplier</span>
                    <span class="bkm-value plain">{{ $barangMasuk->supplier ?: '-' }}</span>
                </div>

                <div class="bkm-row">
                    <span class="bkm-label">No Faktur</span>
                    <span class="bkm-value plain">{{ $barangMasuk->no_faktur ?: '-' }}</span>
                </div>

                <div class="bkm-row">
                    <span class="bkm-label">Harga</span>
                    <span class="bkm-value plain">
                        {{
                            $barangMasuk->harga !== null
                                ? 'Rp ' . pdsNumber($barangMasuk->harga, ',', '.')
                                : '-'
                        }}
                    </span>
                </div>

                <div class="bkm-row">
                    <span class="bkm-label">Keterangan</span>
                    <span class="bkm-value plain">{{ $barangMasuk->keterangan ?: '-' }}</span>
                </div>

            </div>

        </div>


    </div>


</div>

@endsection
