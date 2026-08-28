@extends('layouts.app')

@section('title', 'Detail Barang Keluar')

@section('page_title', 'Inventory')

@section(
    'page_subtitle',
    'Detail transaksi barang keluar'
)

@push('styles')

<style>

/* =====================================================
   DETAIL PAGE
===================================================== */

.bkd-page {
    padding: 4px 0 30px;
}

.bkd-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 20px;
    margin-bottom: 22px;
}

.bkd-title h2 {
    margin: 0 0 5px;
    font-size: 24px;
    font-weight: 700;
    color: var(--pds-ink);
}

.bkd-title p {
    margin: 0;
    color: var(--pds-muted);
    font-size: 13px;
}

.bkd-header-actions {
    display: flex;
    gap: 8px;
    align-items: center;
    flex-wrap: wrap;
}


/* =====================================================
   CARDS
===================================================== */

.bkd-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 16px;
}

.bkd-card {
    background: var(--pds-card);
    border: 1px solid var(--pds-line);
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,.04);
}

.bkd-card.full {
    grid-column: 1 / -1;
}

.bkd-card-header {
    padding: 15px 18px;
    border-bottom: 1px solid var(--pds-line);
    font-size: 14px;
    font-weight: 700;
    color: var(--pds-ink);
}

.bkd-card-body {
    padding: 6px 0;
}

.bkd-row {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 16px;
    padding: 10px 18px;
}

.bkd-row:nth-child(even) {
    background: #fafbfc;
}

.bkd-label {
    color: var(--pds-muted);
    font-size: 13px;
    white-space: nowrap;
    min-width: 150px;
}

.bkd-value {
    color: var(--pds-ink);
    font-size: 13px;
    font-weight: 600;
    text-align: right;
    word-break: break-word;
}

.bkd-value.plain {
    font-weight: 400;
    color: var(--pds-ink-2);
}


/* =====================================================
   BADGES
===================================================== */

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


/* =====================================================
   RESPONSIVE
===================================================== */

@media (max-width: 800px) {

    .bkd-header {
        flex-direction: column;
        align-items: flex-start;
    }

    .bkd-grid {
        grid-template-columns: 1fr;
    }

    .bkd-row {
        flex-direction: column;
        gap: 3px;
    }

    .bkd-value {
        text-align: left;
    }

}

</style>

@endpush


@section('content')

<div class="bkd-page">


    <!-- =================================================
         HEADER
    ================================================== -->

    <div class="bkd-header">

        <div class="bkd-title">

            <h2>
                Detail Barang Keluar
            </h2>

            <p>
                {{ $barangKeluar->no_transaksi }}
                &bull;
                {{ $barangKeluar->tanggal_keluar?->format('d/m/Y') }}
            </p>

        </div>


        <div class="bkd-header-actions">

            <a
                href="{{ route('barang-keluar.index') }}"
                class="btn btn-gray"
            >
                <x-icon name="back"></x-icon> Kembali
            </a>


            @if($barangKeluar->status === 'RECEIVED')

                <button
                    type="button"
                    class="btn btn-danger"
                    onclick="if(confirm('Batalkan transaksi ini dan kembalikan stok?')) document.getElementById('cancelForm').submit();"
                >
                    Batalkan Transaksi
                </button>

            @endif

        </div>

    </div>


    <!-- =================================================
         CANCEL FORM
    ================================================== -->

    <form
        id="cancelForm"
        action="{{ route('barang-keluar.cancel', $barangKeluar) }}"
        method="POST"
        style="display:none;"
    >
        @csrf
        @method('PATCH')
    </form>


    <!-- =================================================
         DETAIL CARDS
    ================================================== -->

    <div class="bkd-grid">


        <!-- TRANSAKSI -->

        <div class="bkd-card">

            <div class="bkd-card-header">
                Informasi Transaksi
            </div>

            <div class="bkd-card-body">

                <div class="bkd-row">
                    <span class="bkd-label">No Transaksi</span>
                    <span class="bkd-value">{{ $barangKeluar->no_transaksi }}</span>
                </div>

                <div class="bkd-row">
                    <span class="bkd-label">Tanggal Keluar</span>
                    <span class="bkd-value">{{ $barangKeluar->tanggal_keluar?->format('d/m/Y') }}</span>
                </div>

                <div class="bkd-row">
                    <span class="bkd-label">Status</span>
                    <span class="bkd-value">
                        @if($barangKeluar->status === 'CANCELLED')
                            <span class="badge badge-danger">DIBATALKAN</span>
                        @else
                            <span class="badge badge-success">DITERIMA</span>
                        @endif
                    </span>
                </div>

                <div class="bkd-row">
                    <span class="bkd-label">Dibuat Oleh</span>
                    <span class="bkd-value plain">
                        {{ $barangKeluar->user?->name ?? '-' }}
                    </span>
                </div>

            </div>

        </div>


        <!-- BARANG & STOK -->

        <div class="bkd-card">

            <div class="bkd-card-header">
                Barang & Stok
            </div>

            <div class="bkd-card-body">

                <div class="bkd-row">
                    <span class="bkd-label">Kode Barang</span>
                    <span class="bkd-value">{{ $barangKeluar->barang?->kode_barang ?? '-' }}</span>
                </div>

                <div class="bkd-row">
                    <span class="bkd-label">Nama / Spesifikasi</span>
                    <span class="bkd-value plain">{{ $barangKeluar->barang?->nama_spesifikasi ?? '-' }}</span>
                </div>

                <div class="bkd-row">
                    <span class="bkd-label">Qty Keluar</span>
                    <span class="bkd-value">
                        {{ pdsNumber($barangKeluar->qty, ',', '.') }}
                        {{ $barangKeluar->satuan }}
                    </span>
                </div>

                <div class="bkd-row">
                    <span class="bkd-label">Stok Awal</span>
                    <span class="bkd-value plain">
                        {{ pdsNumber($barangKeluar->stok_awal, ',', '.') }}
                    </span>
                </div>

                <div class="bkd-row">
                    <span class="bkd-label">Sisa Stok</span>
                    <span class="bkd-value plain">
                        {{ pdsNumber($barangKeluar->sisa_stok, ',', '.') }}
                    </span>
                </div>

            </div>

        </div>


        <!-- LOKASI PEMAKAIAN -->

        <div class="bkd-card">

            <div class="bkd-card-header">
                Lokasi Pemakaian
            </div>

            <div class="bkd-card-body">

                <div class="bkd-row">
                    <span class="bkd-label">Area / Line</span>
                    <span class="bkd-value plain">{{ $barangKeluar->area?->nama_area ?? '-' }}</span>
                </div>

                <div class="bkd-row">
                    <span class="bkd-label">Mesin</span>
                    <span class="bkd-value plain">
                        @if($barangKeluar->machine)
                            {{ $barangKeluar->machine->kode_mesin }} - {{ $barangKeluar->machine->nama_mesin }}
                        @else
                            -
                        @endif
                    </span>
                </div>

            </div>

        </div>


        <!-- PEMAKAIAN -->

        <div class="bkd-card">

            <div class="bkd-card-header">
                Pemakaian
            </div>

            <div class="bkd-card-body">

                <div class="bkd-row">
                    <span class="bkd-label">Dipakai Oleh</span>
                    <span class="bkd-value">
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
                                {{ $barangKeluar->dipakai_oleh }}
                        @endswitch
                    </span>
                </div>

                <div class="bkd-row">
                    <span class="bkd-label">No WO</span>
                    <span class="bkd-value plain">{{ $barangKeluar->no_wo ?: '-' }}</span>
                </div>

                <div class="bkd-row">
                    <span class="bkd-label">Keterangan</span>
                    <span class="bkd-value plain">{{ $barangKeluar->keterangan ?: '-' }}</span>
                </div>

            </div>

        </div>


    </div>


</div>

@endsection
