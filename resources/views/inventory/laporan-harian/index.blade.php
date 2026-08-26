
@extends('layouts.app')

@section('title', 'Laporan Harian')

@section('page_title', 'Laporan Harian')

@section(
    'page_subtitle',
    ''
)

@push('styles')

<style>

/* =====================================================
   SIDEBAR HILANG KHUSUS LAPORAN
===================================================== */

body:has(.laporan-page) .sidebar {
    display: none !important;
}

body:has(.laporan-page) .main {
    width: 100% !important;
    margin-left: 0 !important;
}

body:has(.laporan-page) .content {
    width: 100% !important;
    padding: 22px 30px 30px !important;
}


/* =====================================================
   PAGE
===================================================== */

.laporan-page {
    width: 100%;
    min-height: calc(100vh - 70px);
}


/* =====================================================
   FILTER
===================================================== */

.laporan-filter-card {
    margin-bottom: 18px;
    padding: 16px 18px;
}

.laporan-filter {
    display: grid;

    grid-template-columns:
        minmax(170px, 1fr)
        minmax(170px, 1fr)
        auto
        auto
        auto;

    gap: 9px;

    align-items: end;
}

.laporan-field label {
    display: block;

    margin-bottom: 5px;

    color: var(--pds-ink-2);

    font-size: 12px;
    font-weight: 700;
}

.laporan-input {
    width: 100%;
    height: 40px;

    padding: 0 10px;

    border: 1px solid var(--pds-line-2);
    border-radius: 7px;

    background: var(--pds-card);

    color: var(--pds-ink);

    font-size: 12px;
}

.laporan-input:focus {
    outline: none;

    border-color: #2563eb;

    box-shadow:
        0 0 0 3px
        rgba(37,99,235,.08);
}


/* =====================================================
   JUDUL LAPORAN
===================================================== */

.laporan-title {
    margin: 8px 0 14px;

    text-align: center;
}

.laporan-title h1 {
    margin: 0 0 4px;

    color: var(--pds-ink);

    font-size: 22px;
    font-weight: 800;

    text-transform: uppercase;
}

.laporan-period {
    color: var(--pds-ink-2);

    font-size: 12px;
}


/* =====================================================
   META
===================================================== */

.laporan-meta {
    display: flex;

    justify-content: space-between;
    align-items: center;

    gap: 20px;

    padding: 0 0 12px;

    margin-bottom: 10px;

    border-bottom: 1px solid var(--pds-line-2);

    color: var(--pds-ink);

    font-size: 11px;
}

.laporan-meta-right {
    text-align: right;
}


/* =====================================================
   SECTION
===================================================== */

.laporan-card {
    overflow: hidden;

    margin-bottom: 16px;

    background: var(--pds-card);

    border: 1px solid var(--pds-line-2);
    border-radius: 0;

    box-shadow: none;
}

.laporan-section-header {
    padding: 10px 14px;

    background: #000000;

    color: #ffffff;
}

.laporan-section-header h2 {
    margin: 0;

    font-size: 13px;
    font-weight: 800;
}


/* =====================================================
   TABLE
===================================================== */

.laporan-table-wrap {
    overflow-x: auto;
}

.laporan-table {
    width: 100%;

    border-collapse: collapse;

    min-width: 760px;
}

.laporan-table th,
.laporan-table td {
    padding: 9px 10px;

    border-right: 1px solid var(--pds-muted);
    border-bottom: 1px solid var(--pds-muted);

    font-size: 12px;

    text-align: left;
    vertical-align: middle;
}

.laporan-table th:last-child,
.laporan-table td:last-child {
    border-right: none;
}

.laporan-table th {
    background: var(--pds-line);

    color: var(--pds-ink);

    font-weight: 700;

    white-space: nowrap;

    text-align: center;
}

.laporan-number {
    width: 45px;

    text-align: center !important;
}

.laporan-name {
    font-weight: 600;
    color: var(--pds-ink) !important;
}

.laporan-qty {
    font-weight: 700;

    color: var(--pds-ink) !important;

    text-align: center !important;
    white-space: nowrap;
}

.laporan-empty {
    height: 70px;

    text-align: center !important;

    color: var(--pds-muted) !important;
}


/* =====================================================
   MODAL ORIENTASI PDF
===================================================== */

.pdf-orientation-overlay {
    display: none;

    position: fixed;

    inset: 0;

    z-index: 99999;

    align-items: center;
    justify-content: center;

    padding: 20px;

    background:
        rgba(15,23,42,.65);

    backdrop-filter:
        blur(5px);

    -webkit-backdrop-filter:
        blur(5px);
}

.pdf-orientation-overlay.active {
    display: flex;
}

.pdf-orientation-modal {
    width: 100%;
    max-width: 390px;

    padding: 22px;

    border-radius: 12px;

    background: var(--pds-card);

    box-shadow:
        0 25px 70px
        rgba(0,0,0,.25);
}

.pdf-orientation-icon {
    width: 52px;
    height: 52px;

    margin: 0 auto 12px;

    border: 2px solid #93c5fd;

    border-radius: 50%;

    display: flex;
    align-items: center;
    justify-content: center;

    color: #2563eb;

    font-size: 25px;
}

.pdf-orientation-title {
    margin-bottom: 15px;

    text-align: center;
}

.pdf-orientation-title h3 {
    margin: 0;

    color: var(--pds-ink);

    font-size: 19px;
}

.pdf-orientation-select {
    width: 100%;
    height: 42px;

    padding: 0 10px;

    border: 1px solid var(--pds-line-2);
    border-radius: 7px;

    background: var(--pds-card);

    color: var(--pds-ink);

    font-size: 13px;
}

.pdf-orientation-actions {
    display: flex;

    justify-content: flex-end;

    gap: 8px;

    margin-top: 18px;
}


/* =====================================================
   RESPONSIVE
===================================================== */

@media (max-width: 950px) {

    .laporan-filter {
        grid-template-columns:
            1fr
            1fr
            auto;
    }

}

@media (max-width: 700px) {

    body:has(.laporan-page) .content {
        padding: 16px !important;
    }

    .laporan-filter {
        grid-template-columns: 1fr;
    }

    .laporan-meta {
        flex-direction: column;
        align-items: flex-start;
    }

    .laporan-meta-right {
        text-align: left;
    }

}


/* =====================================================
   PRINT
===================================================== */

@media print {

    html,
    body {
        background: #ffffff !important;
    }

    body:has(.laporan-page) .sidebar,
    body:has(.laporan-page) .topbar,
    body:has(.laporan-page) .laporan-filter-card,
    body:has(.laporan-page) .pdf-orientation-overlay {
        display: none !important;
    }

    body:has(.laporan-page) .main {
        width: 100% !important;
        margin-left: 0 !important;
    }

    body:has(.laporan-page) .content {
        width: 100% !important;

        padding: 0 !important;
        margin: 0 !important;
    }

    .laporan-page {
        width: 100% !important;
    }

    .laporan-title {
        margin-top: 0 !important;
    }

    .laporan-card {
        border: 1px solid #000000 !important;
        box-shadow: none !important;

        break-inside: avoid;
    }

    .laporan-section-header {
        background: #000000 !important;
        color: #ffffff !important;

        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }

    .laporan-table th {
        background: #e5e7eb !important;

        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }

    .laporan-table th,
    .laporan-table td {
        border-color: #000000 !important;
    }

}

</style>

@endpush


@section('content')

<div class="laporan-page">


    <!-- =================================================
         FILTER
    ================================================= -->

    <div class="card laporan-filter-card">

        <form
            action="{{ route('laporan-harian.index') }}"
            method="GET"
            class="laporan-filter"
        >


            <div class="laporan-field">

                <label>
                    Dari Tanggal
                </label>

                <input
                    type="date"
                    name="tanggal_dari"
                    class="laporan-input"
                    value="{{ $tanggalDari }}"
                    required
                >

            </div>


            <div class="laporan-field">

                <label>
                    Sampai Tanggal
                </label>

                <input
                    type="date"
                    name="tanggal_sampai"
                    class="laporan-input"
                    value="{{ $tanggalSampai }}"
                    required
                >

            </div>


            <button
                type="submit"
                class="btn btn-primary"
            >
                <x-icon name="search"></x-icon> Tampilkan
            </button>


            <a
                href="{{ route('laporan-harian.index') }}"
                class="btn btn-gray"
            >
                ⟳ Refresh
            </a>


            <button
                type="button"
                class="btn btn-gray"
                onclick="openPdfOrientationModal()"
            >
                <x-icon name="print"></x-icon> Print / PDF
            </button>

        </form>

    </div>


    <!-- =================================================
         JUDUL LAPORAN
    ================================================= -->

    <div class="laporan-title">

        <h1>
            Laporan Harian Stok Barang
        </h1>

        <div class="laporan-period">

            Periode:

            {{
                \Carbon\Carbon::parse(
                    $tanggalDari
                )->format('d-M-Y')
            }}

            s/d

            {{
                \Carbon\Carbon::parse(
                    $tanggalSampai
                )->format('d-M-Y')
            }}

        </div>

    </div>


    <!-- =================================================
         INFORMASI PELAPOR
    ================================================= -->

    <div class="laporan-meta">

        <div>

            <strong>
                Dilaporkan Oleh:
            </strong>

            {{ auth()->user()->name }}

        </div>


        <div class="laporan-meta-right">

            <strong>
                Tanggal Cetak:
            </strong>

            {{
                now()->translatedFormat(
                    'l, d-M-Y'
                )
            }}

            |

            <strong>
                Jam:
            </strong>

            {{
                now()->format('H:i:s')
            }}

        </div>

    </div>


    <!-- =================================================
         A. BARANG MASUK
    ================================================= -->

    <div class="laporan-card">

        <div class="laporan-section-header">

            <h2>
                A. BARANG MASUK
            </h2>

        </div>


        <div class="laporan-table-wrap">

            <table class="laporan-table">

                <thead>

                    <tr>

                        <th>
                            No
                        </th>

                        <th>
                            Nama Barang
                        </th>

                        <th>
                            Satuan
                        </th>

                        <th>
                            Qty Masuk
                        </th>

                        <th>
                            Keterangan
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @forelse(
                        $barangMasuks
                        as $index
                        => $barangMasuk
                    )

                        <tr>

                            <td class="laporan-number">
                                {{ $index + 1 }}
                            </td>


                            <td class="laporan-name">
                                {{
                                    $barangMasuk
                                        ->nama_barang_spesifikasi
                                }}
                            </td>


                            <td>
                                {{ $barangMasuk->satuan }}
                            </td>


                            <td class="laporan-qty">

                                {{
                                    number_format(
                                        $barangMasuk->qty,
                                        2,
                                        ',',
                                        '.'
                                    )
                                }}

                            </td>


                            <td>
                                {{
                                    $barangMasuk->keterangan
                                    ?: '-'
                                }}
                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="5"
                                class="laporan-empty"
                            >
                                Tidak ada
                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>


    <!-- =================================================
         B. BARANG KELUAR
    ================================================= -->

    <div class="laporan-card">

        <div class="laporan-section-header">

            <h2>
                B. BARANG KELUAR
            </h2>

        </div>


        <div class="laporan-table-wrap">

            <table class="laporan-table">

                <thead>

                    <tr>

                        <th>
                            No
                        </th>

                        <th>
                            Nama Barang
                        </th>

                        <th>
                            Satuan
                        </th>

                        <th>
                            Qty Keluar
                        </th>

                        <th>
                            Stok Akhir
                        </th>

                        <th>
                            Digunakan Untuk
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @forelse(
                        $barangKeluars
                        as $index
                        => $barangKeluar
                    )

                        <tr>

                            <td class="laporan-number">
                                {{ $index + 1 }}
                            </td>


                            <td class="laporan-name">

                                {{
                                    $barangKeluar
                                        ->barang
                                        ?->nama_spesifikasi
                                    ?? '-'
                                }}

                            </td>


                            <td>

                                {{
                                    $barangKeluar->satuan
                                    ?? (
                                        $barangKeluar
                                            ->barang
                                            ?->satuan
                                            ?->nama
                                        ?? '-'
                                    )
                                }}

                            </td>


                            <td class="laporan-qty">

                                {{
                                    number_format(
                                        $barangKeluar->qty,
                                        2,
                                        ',',
                                        '.'
                                    )
                                }}

                            </td>


                            <td class="laporan-qty">

                                {{
                                    number_format(
                                        $barangKeluar->sisa_stok,
                                        2,
                                        ',',
                                        '.'
                                    )
                                }}

                            </td>


                            <td>

                                @if(
                                    $barangKeluar->machine
                                )

                                    {{
                                        $barangKeluar
                                            ->machine
                                            ->nama_mesin
                                    }}

                                @elseif(
                                    $barangKeluar->dipakai_oleh
                                )

                                    {{
                                        $barangKeluar
                                            ->dipakai_oleh
                                    }}

                                @elseif(
                                    $barangKeluar->no_wo
                                )

                                    WO:
                                    {{
                                        $barangKeluar
                                            ->no_wo
                                    }}

                                @else

                                    -

                                @endif

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="6"
                                class="laporan-empty"
                            >
                                Tidak ada
                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>


</div>


<!-- =====================================================
     MODAL PILIH ORIENTASI PDF
===================================================== -->

<div
    id="pdfOrientationModal"
    class="pdf-orientation-overlay"
    onclick="closePdfOrientationOverlay(event)"
>

    <div class="pdf-orientation-modal">


        <div class="pdf-orientation-icon">
            ?
        </div>


        <div class="pdf-orientation-title">

            <h3>
                Pilih Orientasi PDF
            </h3>

        </div>


        <select
            id="pdfOrientation"
            class="pdf-orientation-select"
        >

            <option value="landscape">
                Lanskap (Horizontal) - Recommended
            </option>

            <option value="portrait">
                Potret (Vertical)
            </option>

        </select>


        <div class="pdf-orientation-actions">

            <button
                type="button"
                class="btn btn-primary"
                onclick="printLaporanPdf()"
            >
                Cetak
            </button>


            <button
                type="button"
                class="btn btn-gray"
                onclick="closePdfOrientationModal()"
            >
                Cancel
            </button>

        </div>

    </div>

</div>

@endsection


@push('scripts')

<script>

/* =====================================================
   MODAL
===================================================== */

function openPdfOrientationModal()
{
    const modal =
        document.getElementById(
            'pdfOrientationModal'
        );

    if (modal) {

        modal.classList.add(
            'active'
        );

    }
}


function closePdfOrientationModal()
{
    const modal =
        document.getElementById(
            'pdfOrientationModal'
        );

    if (modal) {

        modal.classList.remove(
            'active'
        );

    }
}


function closePdfOrientationOverlay(event)
{
    const modal =
        document.getElementById(
            'pdfOrientationModal'
        );

    if (
        modal &&
        event.target === modal
    ) {

        closePdfOrientationModal();

    }
}


/* =====================================================
   PRINT / PDF
===================================================== */

function printLaporanPdf()
{
    const orientation =
        document.getElementById(
            'pdfOrientation'
        ).value;


    const oldStyle =
        document.getElementById(
            'dynamicPrintStyle'
        );


    if (oldStyle) {

        oldStyle.remove();

    }


    const style =
        document.createElement(
            'style'
        );


    style.id =
        'dynamicPrintStyle';


    if (
        orientation ===
        'landscape'
    ) {

        style.innerHTML = `
            @media print {
                @page {
                    size: A4 landscape;
                    margin: 10mm;
                }
            }
        `;

    } else {

        style.innerHTML = `
            @media print {
                @page {
                    size: A4 portrait;
                    margin: 10mm;
                }
            }
        `;

    }


    document.head.appendChild(
        style
    );


    closePdfOrientationModal();


    setTimeout(
        function () {

            window.print();

        },
        150
    );
}


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

            closePdfOrientationModal();

        }

    }
);


/* =====================================================
   VALIDASI TANGGAL
===================================================== */

document.addEventListener(
    'DOMContentLoaded',
    function()
    {

        const dari =
            document.querySelector(
                'input[name="tanggal_dari"]'
            );

        const sampai =
            document.querySelector(
                'input[name="tanggal_sampai"]'
            );


        if (
            dari &&
            sampai
        ) {

            sampai.addEventListener(
                'change',
                function()
                {

                    if (
                        dari.value &&
                        sampai.value &&
                        sampai.value < dari.value
                    ) {

                        alert(
                            'Tanggal sampai tidak boleh lebih kecil dari tanggal dari.'
                        );

                        sampai.value =
                            dari.value;

                    }

                }
            );

        }

    }
);

</script>

@endpush