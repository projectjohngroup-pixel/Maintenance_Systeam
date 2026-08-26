@extends('layouts.app')

@section('title', 'Edit Barang Masuk')

@section('page_title', 'Inventory')

@section(
    'page_subtitle',
    'Perubahan data transaksi barang masuk'
)

@push('styles')

<style>

.bke-page {
    padding: 4px 0 30px;
}

.bke-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 20px;
    margin-bottom: 22px;
}

.bke-title h2 {
    margin: 0 0 5px;
    font-size: 24px;
    font-weight: 700;
    color: var(--pds-ink);
}

.bke-title p {
    margin: 0;
    color: var(--pds-muted);
    font-size: 13px;
}

.bke-card {
    max-width: 860px;
    background: var(--pds-card);
    border: 1px solid var(--pds-line);
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,.04);
}

.bke-card-header {
    padding: 15px 18px;
    border-bottom: 1px solid var(--pds-line);
    font-size: 14px;
    font-weight: 700;
    color: var(--pds-ink);
}

.bke-card-body {
    padding: 20px 18px;
}

.bke-form-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 16px;
}

.bke-form-full {
    grid-column: 1 / -1;
}

.bke-field label {
    display: block;
    margin-bottom: 7px;
    font-size: 13px;
    font-weight: 700;
    color: var(--pds-ink-2);
}

.bke-required {
    color: #dc2626;
}

.bke-input {
    width: 100%;
    height: 42px;
    padding: 0 11px;
    border: 1px solid var(--pds-line-2);
    border-radius: 8px;
    background: var(--pds-card);
    font-size: 13px;
}

.bke-input:focus {
    outline: none;
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37,99,235,.08);
}

.bke-input.readonly {
    background: var(--pds-soft);
    color: var(--pds-ink-2);
    font-weight: 600;
}

.bke-textarea {
    width: 100%;
    min-height: 100px;
    padding: 10px 11px;
    border: 1px solid var(--pds-line-2);
    border-radius: 8px;
    background: var(--pds-card);
    font-size: 13px;
    resize: vertical;
}

.bke-error {
    margin-top: 5px;
    color: #dc2626;
    font-size: 11px;
}

.bke-info {
    margin-top: 14px;
    padding: 12px 14px;
    border-radius: 8px;
    background: #eff6ff;
    border: 1px solid #bfdbfe;
    color: #1e40af;
    font-size: 12px;
}

.bke-footer {
    display: flex;
    justify-content: flex-end;
    gap: 8px;
    margin-top: 18px;
}

@media (max-width: 700px) {

    .bke-header {
        flex-direction: column;
        align-items: flex-start;
    }

    .bke-form-grid {
        grid-template-columns: 1fr;
    }

}

</style>

@endpush


@section('content')

<div class="bke-page">


    <!-- =================================================
         HEADER
    ================================================== -->

    <div class="bke-header">

        <div class="bke-title">

            <h2>
                Edit Barang Masuk
            </h2>

            <p>
                {{ $barangMasuk->no_transaksi }}
                &bull;
                {{ $barangMasuk->barang?->kode_barang ?? '-' }}
                {{ $barangMasuk->barang?->nama_spesifikasi ?? '' }}
            </p>

        </div>


        <a
            href="{{ route('barang-masuk.index') }}"
            class="btn btn-gray"
        >
            <x-icon name="back"></x-icon> Kembali
        </a>

    </div>


    <!-- =================================================
         FORM
    ================================================== -->

    <div class="bke-card">

        <div class="bke-card-header">
            Data Transaksi
        </div>


        <div class="bke-card-body">


            @if($errors->any())

                <div
                    style="
                        margin-bottom:16px;
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

                            <li>{{ $error }}</li>

                        @endforeach

                    </ul>

                </div>

            @endif


            <form
                action="{{ route('barang-masuk.update', $barangMasuk) }}"
                method="POST"
            >

                @csrf
                @method('PUT')


                <div class="bke-form-grid">


                    <!-- NO TRANSAKSI -->

                    <div class="bke-field">

                        <label>
                            No Transaksi
                        </label>

                        <input
                            type="text"
                            class="bke-input readonly"
                            value="{{ $barangMasuk->no_transaksi }}"
                            readonly
                        >

                    </div>


                    <!-- TANGGAL -->

                    <div class="bke-field">

                        <label>
                            Tanggal Masuk
                            <span class="bke-required">*</span>
                        </label>

                        <input
                            type="date"
                            name="tanggal_masuk"
                            class="bke-input"
                            value="{{ old(
                                'tanggal_masuk',
                                $barangMasuk->tanggal_masuk?->format('Y-m-d')
                            ) }}"
                            required
                        >

                        @error('tanggal_masuk')

                            <div class="bke-error">{{ $message }}</div>

                        @enderror

                    </div>


                    <!-- BARANG (TERKUNCI) -->

                    <div class="bke-field">

                        <label>
                            Barang
                        </label>

                        <input
                            type="text"
                            class="bke-input readonly"
                            value="{{
                                ($barangMasuk->barang?->kode_barang ?? '-')
                                . ' - '
                                . ($barangMasuk->barang?->nama_spesifikasi ?? '-')
                            }}"
                            readonly
                        >

                        <small style="color:var(--pds-muted-2);">
                            Barang tidak dapat diubah. Hapus transaksi lalu buat baru bila salah barang.
                        </small>

                    </div>


                    <!-- QTY -->

                    <div class="bke-field">

                        <label>
                            Qty
                            <span class="bke-required">*</span>
                        </label>

                        <input
                            type="number"
                            name="qty"
                            class="bke-input"
                            value="{{ old('qty', $barangMasuk->qty) }}"
                            min="0.01"
                            step="0.01"
                            required
                        >

                        @error('qty')

                            <div class="bke-error">{{ $message }}</div>

                        @enderror

                    </div>


                    <!-- SATUAN -->

                    <div class="bke-field">

                        <label>
                            Satuan
                        </label>

                        <input
                            type="text"
                            class="bke-input readonly"
                            value="{{ $barangMasuk->satuan?->nama ?? '-' }}"
                            readonly
                        >

                    </div>


                    <!-- SUPPLIER -->

                    <div class="bke-field">

                        <label>
                            Supplier
                        </label>

                        <input
                            type="text"
                            name="supplier"
                            class="bke-input"
                            value="{{ old('supplier', $barangMasuk->supplier) }}"
                            placeholder="Nama supplier"
                        >

                    </div>


                    <!-- NO FAKTUR -->

                    <div class="bke-field">

                        <label>
                            No Faktur
                        </label>

                        <input
                            type="text"
                            name="no_faktur"
                            class="bke-input"
                            value="{{ old('no_faktur', $barangMasuk->no_faktur) }}"
                            placeholder="Nomor faktur"
                        >

                    </div>


                    <!-- HARGA -->

                    <div class="bke-field">

                        <label>
                            Harga
                        </label>

                        <input
                            type="number"
                            name="harga"
                            class="bke-input"
                            value="{{ old('harga', $barangMasuk->harga) }}"
                            min="0"
                            step="0.01"
                            placeholder="0"
                        >

                    </div>


                    <!-- KETERANGAN -->

                    <div class="bke-field bke-form-full">

                        <label>
                            Keterangan
                        </label>

                        <textarea
                            name="keterangan"
                            class="bke-textarea"
                            placeholder="Tambahkan keterangan bila diperlukan..."
                        >{{ old('keterangan', $barangMasuk->keterangan) }}</textarea>

                    </div>

                </div>


                <div class="bke-info">
                    Perubahan qty akan otomatis menyesuaikan stok barang:
                    stok sekarang dikurangi qty lama kemudian ditambah qty baru.
                </div>


                <div class="bke-footer">

                    <a
                        href="{{ route('barang-masuk.index') }}"
                        class="btn btn-gray"
                    >
                        Batal
                    </a>


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


</div>

@endsection
