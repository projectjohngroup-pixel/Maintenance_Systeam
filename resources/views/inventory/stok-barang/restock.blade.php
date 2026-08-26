@extends('layouts.app')

@section('title', 'Re-stock Barang')

@section('page_title', 'Inventory')

@section(
    'page_subtitle',
    'Daftar barang yang perlu di-restock'
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

[data-theme="dark"] .inventory-module-nav {
    background: var(--surface);
    border-color: var(--border);
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

[data-theme="dark"] .inventory-module-link {
    color: #cbd5e1;
}

.inventory-module-link:hover {
    background: var(--pds-soft-2);
    color: var(--pds-ink);
}

[data-theme="dark"] .inventory-module-link:hover {
    background: var(--surface-soft);
    color: #f1f5f9;
}

.inventory-module-link.active {
    background: #2563eb;
    color: #ffffff;
}

.restock-summary {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
    gap: 12px;
    margin-bottom: 20px;
}

.restock-kpi {
    background: var(--surface);
    border: 1px solid var(--border);
    border-left: 3px solid var(--kc, var(--primary));
    border-radius: 12px;
    padding: 15px;
    box-shadow: 0 3px 12px rgba(0,0,0,.04);
}

.restock-kpi .label {
    font-size: 10.5px;
    font-weight: 700;
    letter-spacing: 1px;
    text-transform: uppercase;
    color: var(--muted);
}

.restock-kpi .value {
    font-size: 24px;
    font-weight: 800;
    margin-top: 6px;
}

.restock-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 14px;
    box-shadow: 0 3px 12px rgba(0,0,0,.04);
    overflow: hidden;
}

.restock-table-wrap {
    overflow-x: auto;
}

table.restock-table {
    width: 100%;
    border-collapse: collapse;
    min-width: 760px;
}

.restock-table th {
    text-align: left;
    padding: 12px 14px;
    font-size: 10.5px;
    font-weight: 800;
    letter-spacing: 1.2px;
    text-transform: uppercase;
    color: var(--muted);
    background: var(--surface-soft);
    border-bottom: 1px solid var(--border);
    white-space: nowrap;
}

.restock-table td {
    padding: 12px 14px;
    font-size: 13px;
    color: var(--text-2, var(--text));
    border-bottom: 1px solid var(--border);
    white-space: nowrap;
}

.restock-table tr:last-child td {
    border-bottom: none;
}

.badge-habis {
    display: inline-block;
    padding: 3px 10px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 700;
    background: rgba(220,38,38,.12);
    color: #dc2626;
}

.badge-minimum {
    display: inline-block;
    padding: 3px 10px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 700;
    background: rgba(245,158,11,.16);
    color: #b45309;
}

[data-theme="dark"] .badge-minimum {
    color: #fbbf24;
}

.empty-state {
    padding: 42px 20px;
    text-align: center;
    color: var(--muted);
    font-size: 13px;
}

</style>

@endpush

@section('content')

<div class="inventory-module-nav">

    <a href="{{ route('barang.index') }}" class="inventory-module-link">
        Stok Barang
    </a>

    <a href="{{ route('barang-masuk.index') }}" class="inventory-module-link">
        Barang Masuk
    </a>

    <a href="{{ route('barang-keluar.index') }}" class="inventory-module-link">
        Barang Keluar
    </a>

    <a href="{{ route('purchase-requests.index') }}" class="inventory-module-link">
        Purchase Request
    </a>

    <a href="{{ route('laporan-harian.index') }}" class="inventory-module-link">
        Laporan Harian
    </a>

    <a href="{{ route('rata-rata-pemakaian.index') }}" class="inventory-module-link">
        Rata-rata Pemakaian
    </a>

    <a href="{{ route('barang.restock') }}" class="inventory-module-link active">
        Re-stock
    </a>

</div>

@php
    $habisCount = $barangs->filter(fn ($b) => (int) $b->stok === 0)->count();
    $minimumCount = $barangs->count() - $habisCount;
@endphp

<div class="restock-summary">

    <div class="restock-kpi" style="--kc:#2563eb;">
        <div class="label">Perlu Restock</div>
        <div class="value">{{ number_format($barangs->count()) }}</div>
    </div>

    <div class="restock-kpi" style="--kc:#dc2626;">
        <div class="label">Stok Habis</div>
        <div class="value">{{ number_format($habisCount) }}</div>
    </div>

    <div class="restock-kpi" style="--kc:#d97706;">
        <div class="label">Mencapai Minimum</div>
        <div class="value">{{ number_format($minimumCount) }}</div>
    </div>

</div>

<div class="restock-card">

    <div class="restock-table-wrap">

        <table class="restock-table">

            <thead>

                <tr>
                    <th>No</th>
                    <th>Nama / Spesifikasi</th>
                    <th>Stok</th>
                    <th>Satuan</th>
                    <th>Stok Minimum</th>
                    <th>Kebutuhan</th>
                    <th>Status</th>
                </tr>

            </thead>

            <tbody>

                @forelse($barangs as $barang)

                    @php
                        $habis = (int) $barang->stok === 0;
                        $kebutuhan = max(0, (int) $barang->stok_minimum - (int) $barang->stok);
                    @endphp

                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td style="font-weight:600; white-space:normal; min-width:220px;">
                            {{ $barang->nama_spesifikasi }}
                        </td>
                        <td style="font-weight:700; color:{{ $habis ? '#dc2626' : 'inherit' }};">
                            {{ number_format($barang->stok) }}
                        </td>
                        <td>{{ $barang->satuan->nama ?? '-' }}</td>
                        <td>{{ number_format($barang->stok_minimum) }}</td>
                        <td style="font-weight:700;">+{{ number_format($kebutuhan) }}</td>
                        <td>
                            <span class="{{ $habis ? 'badge-habis' : 'badge-minimum' }}">
                                {{ $habis ? 'HABIS' : 'MINIMUM' }}
                            </span>
                        </td>
                    </tr>

                @empty

                    <tr>
                        <td colspan="7">
                            <div class="empty-state">
                                Semua stok barang dalam kondisi aman. Tidak ada yang perlu di-restock.
                            </div>
                        </td>
                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</div>

@endsection
