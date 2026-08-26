<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <meta
        name="csrf-token"
        content="{{ csrf_token() }}"
    >

    <title>
        Detail Work Order
    </title>


    <style>

        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            background: var(--pds-soft);
            color: var(--pds-ink);
        }


        /* =====================================================
           CONTAINER
        ====================================================== */

        .container {
            max-width: 1200px;
            margin: 35px auto;
            padding: 20px;
        }


        /* =====================================================
           HEADER
        ====================================================== */

        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 20px;
            margin-bottom: 25px;
        }

        .header-content h1 {
            margin: 0 0 6px;
            font-size: 28px;
            color: var(--pds-ink);
        }

        .header-content p {
            margin: 0;
            color: var(--pds-muted);
            font-size: 14px;
        }


        /* =====================================================
           BUTTON
        ====================================================== */

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 40px;
            padding: 10px 16px;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            cursor: pointer;
            font-size: 13px;
            font-weight: 700;
            transition: .15s;
        }

        .btn-print {
            background: #2563eb;
            color: #ffffff;
        }

        .btn-print:hover {
            background: #1d4ed8;
        }


        /* =====================================================
           CARD
        ====================================================== */

        .card {
            background: var(--pds-card);
            border-radius: 12px;
            box-shadow: 0 3px 12px rgba(0, 0, 0, .06);
            margin-bottom: 20px;
            overflow: hidden;
        }

        .card-header {
            padding: 18px 20px;
            border-bottom: 1px solid var(--pds-line);
        }

        .card-header h2 {
            margin: 0;
            font-size: 17px;
            color: var(--pds-ink);
        }

        .card-header p {
            margin: 5px 0 0;
            color: var(--pds-muted);
            font-size: 13px;
        }

        .card-body {
            padding: 20px;
        }


        /* =====================================================
           DETAIL GRID
        ====================================================== */

        .grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 18px 22px;
        }

        .field {
            display: flex;
            flex-direction: column;
            min-width: 0;
        }

        .field.full {
            grid-column: 1 / -1;
        }

        .label {
            margin-bottom: 6px;
            color: var(--pds-muted);
            font-size: 12px;
            font-weight: 600;
        }

        .value {
            color: var(--pds-ink);
            font-size: 14px;
            line-height: 1.55;
            word-break: break-word;
        }


        /* =====================================================
           STATUS
        ====================================================== */

        .status {
            display: inline-block;
            width: fit-content;
            padding: 6px 11px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            white-space: nowrap;
        }

        .status-open {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .status-diterima {
            background: #e0f2fe;
            color: #0369a1;
        }

        .status-scheduled {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .status-pending {
            background: var(--pds-soft-2);
            color: var(--pds-muted);
        }

        .status-progress {
            background: #fef3c7;
            color: #92400e;
        }

        .status-service-luar {
            background: #ede9fe;
            color: #6d28d9;
        }

        .status-close {
            background: #dcfce7;
            color: #166534;
        }


        /* =====================================================
           PRIORITY
        ====================================================== */

        .priority {
            display: inline-block;
            width: fit-content;
            padding: 6px 11px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            white-space: nowrap;
        }

        .priority-normal {
            background: var(--pds-line);
            color: var(--pds-ink-2);
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
           PROGRESS
        ====================================================== */

        .progress-box {
            padding: 18px;
            border: 1px solid var(--pds-line);
            border-radius: 10px;
            background: var(--pds-soft);
        }

        .progress-title {
            margin-bottom: 12px;
            font-size: 14px;
            font-weight: 700;
            color: var(--pds-ink);
        }

        .progress-track {
            display: grid;
            grid-template-columns: repeat(6, minmax(0, 1fr));
            gap: 8px;
        }

        .progress-step {
            padding: 10px 8px;
            text-align: center;
            border-radius: 8px;
            background: var(--pds-line);
            color: var(--pds-muted-2);
            font-size: 10px;
            font-weight: 700;
            line-height: 1.3;
        }

        .progress-step.active {
            background: #2563eb;
            color: #ffffff;
        }


        /* =====================================================
           FOTO
        ====================================================== */

        .photo-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 15px;
        }

        .photo-item {
            border: 1px solid var(--pds-line);
            border-radius: 10px;
            overflow: hidden;
            background: var(--pds-card);
        }

        .photo-item img {
            width: 100%;
            height: 220px;
            object-fit: cover;
            display: block;
        }

        .photo-caption {
            padding: 9px 10px;
            font-size: 12px;
            color: var(--pds-muted);
        }

        .empty-photo {
            padding: 20px;
            text-align: center;
            color: var(--pds-muted-2);
            border: 1px dashed var(--pds-line-2);
            border-radius: 10px;
        }


        /* =====================================================
           PRINT ORIENTATION MODAL
        ====================================================== */

        .print-orientation-overlay {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 99999;
            align-items: center;
            justify-content: center;
            padding: 20px;
            background: rgba(15, 23, 42, .58);
            backdrop-filter: blur(4px);
            -webkit-backdrop-filter: blur(4px);
        }

        .print-orientation-overlay.active {
            display: flex;
        }

        .print-orientation-modal {
            width: 100%;
            max-width: 500px;
            background: var(--pds-card);
            border-radius: 16px;
            box-shadow: 0 25px 70px rgba(0, 0, 0, .25);
            overflow: hidden;
        }

        .print-orientation-header {
            padding: 22px 24px 16px;
            text-align: center;
            border-bottom: 1px solid var(--pds-line);
        }

        .print-orientation-header h3 {
            margin: 0 0 6px;
            color: var(--pds-ink);
            font-size: 19px;
        }

        .print-orientation-header p {
            margin: 0;
            color: var(--pds-muted);
            font-size: 13px;
        }

        .print-orientation-options {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
            padding: 22px;
        }

        .orientation-option {
            min-height: 155px;
            border: 1px solid var(--pds-line-2);
            border-radius: 12px;
            background: var(--pds-card);
            cursor: pointer;

            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;

            gap: 7px;

            color: var(--pds-ink);

            transition: .15s;
        }

        .orientation-option:hover {
            border-color: #2563eb;
            background: #eff6ff;
        }

        .orientation-icon {
            font-size: 40px;
            line-height: 1;
            margin-bottom: 6px;
        }

        .orientation-option strong {
            font-size: 14px;
        }

        .orientation-option span {
            color: var(--pds-muted);
            font-size: 11px;
        }

        .print-orientation-footer {
            padding: 14px 22px;
            border-top: 1px solid var(--pds-line);
            background: var(--pds-soft);
            text-align: right;
        }

        .print-cancel {
            border: none;
            border-radius: 8px;
            padding: 10px 16px;
            background: var(--pds-line);
            color: var(--pds-ink-2);
            cursor: pointer;
            font-size: 13px;
            font-weight: 700;
        }


        /* =====================================================
           MOBILE
        ====================================================== */

        @media (max-width: 800px) {

            .container {
                margin: 15px auto;
                padding: 12px;
            }

            .header {
                flex-direction: column;
            }

            .grid {
                grid-template-columns: 1fr;
            }

            .field.full {
                grid-column: auto;
            }

            .progress-track {
                grid-template-columns: repeat(2, 1fr);
            }

            .photo-grid {
                grid-template-columns: 1fr;
            }

            .print-orientation-options {
                grid-template-columns: 1fr;
            }

        }


        /* =====================================================
           PRINT
        ====================================================== */

        @media print {

            @page {
                margin: 12mm;
            }

            html,
            body {
                background: #ffffff !important;
            }

            body {
                color: #111827 !important;
                font-size: 10pt;
            }

            .container {
                width: 100% !important;
                max-width: none !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            .no-print,
            .print-orientation-overlay,
            nav,
            aside,
            footer,
            .sidebar,
            .navbar {
                display: none !important;
            }

            .header {
                display: block !important;
                margin-bottom: 14px !important;
            }

            .header-content h1 {
                font-size: 20px !important;
                margin-bottom: 4px !important;
            }

            .header-content p {
                font-size: 10px !important;
            }

            .card {
                width: 100% !important;
                margin-bottom: 12px !important;
                border: 1px solid #cbd5e1 !important;
                border-radius: 6px !important;
                box-shadow: none !important;
                overflow: visible !important;
                break-inside: avoid;
                page-break-inside: avoid;
            }

            .card-header {
                padding: 9px 11px !important;
                background: #f8fafc !important;
                border-bottom: 1px solid #cbd5e1 !important;
            }

            .card-header h2 {
                font-size: 11pt !important;
            }

            .card-header p {
                margin-top: 3px !important;
                font-size: 8.5pt !important;
            }

            .card-body {
                padding: 11px !important;
            }

            .grid {
                display: grid !important;
                grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
                gap: 10px 14px !important;
            }

            .field.full {
                grid-column: 1 / -1 !important;
            }

            .label {
                margin-bottom: 3px !important;
                font-size: 8pt !important;
            }

            .value {
                font-size: 9pt !important;
                line-height: 1.4 !important;
            }

            .status,
            .priority {
                padding: 4px 8px !important;
                font-size: 7.5pt !important;
            }

            .progress-box {
                padding: 10px !important;
                background: #ffffff !important;
            }

            .progress-title {
                margin-bottom: 8px !important;
                font-size: 9pt !important;
            }

            .progress-track {
                grid-template-columns: repeat(6, minmax(0, 1fr)) !important;
                gap: 5px !important;
            }

            .progress-step {
                padding: 7px 4px !important;
                font-size: 7pt !important;
                border: 1px solid #d1d5db !important;
            }

            .photo-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
                gap: 8px !important;
            }

            .photo-item {
                break-inside: avoid !important;
                page-break-inside: avoid !important;
                border: 1px solid #d1d5db !important;
            }

            .photo-item img {
                width: 100% !important;
                height: 145px !important;
                object-fit: contain !important;
                background: #f8fafc !important;
            }

            .photo-caption {
                padding: 5px 7px !important;
                font-size: 7.5pt !important;
            }

            .empty-photo {
                padding: 12px !important;
                font-size: 8pt !important;
            }

        }

    </style>

</head>


<body>

<div class="container">


    {{-- =====================================================
         HEADER
    ====================================================== --}}

    <div class="header">

        <div class="header-content">

            <h1>
                DETAIL WORK ORDER
            </h1>

            <p>
                Detail Work Order
            </p>

        </div>


        <div class="no-print">

            <button
                type="button"
                class="btn btn-print"
                onclick="bukaPilihanPrint()"
            >
                <x-icon name="print"></x-icon> Print / PDF
            </button>

        </div>

    </div>


    {{-- =====================================================
         DETAIL
    ====================================================== --}}

    <div class="card">

        <div class="card-header">

            <h2>
                Detail
            </h2>

        </div>


        <div class="card-body">

            <div class="grid">


                <div class="field">

                    <div class="label">
                        No. WO
                    </div>

                    <div class="value">
                        {{ $workOrder->no_wo ?: '-' }}
                    </div>

                </div>


                <div class="field">

                    <div class="label">
                        Tanggal Kerusakan
                    </div>

                    <div class="value">

                        {{
                            $workOrder->tanggal_kerusakan
                                ? \Carbon\Carbon::parse(
                                    $workOrder->tanggal_kerusakan
                                )->format('d-m-Y')
                                : '-'
                        }}

                    </div>

                </div>


                <div class="field">

                    <div class="label">
                        Jam Kerusakan
                    </div>

                    <div class="value">
                        {{ $workOrder->jam_kerusakan ?: '-' }}
                    </div>

                </div>


                <div class="field">

                    <div class="label">
                        Departemen
                    </div>

                    <div class="value">
                        {{ $workOrder->departemen ?: '-' }}
                    </div>

                </div>


                <div class="field">

                    <div class="label">
                        Kategori
                    </div>

                    <div class="value">
                        {{ $workOrder->kategori ?: '-' }}
                    </div>

                </div>


                <div class="field">

                    <div class="label">
                        Ditujukan
                    </div>

                    <div class="value">
                        {{ $workOrder->tujuan ?: '-' }}
                    </div>

                </div>


                <div class="field">

                    <div class="label">
                        Area / Line
                    </div>

                    <div class="value">
                        {{ $workOrder->area ?: '-' }}
                    </div>

                </div>


                <div class="field">

                    <div class="label">
                        Mesin
                    </div>

                    <div class="value">
                        {{ $workOrder->mesin ?: '-' }}
                    </div>

                </div>


                <div class="field full">

                    <div class="label">
                        Job
                    </div>

                    <div class="value">
                        {{ $workOrder->job ?: '-' }}
                    </div>

                </div>


                <div class="field">

                    <div class="label">
                        Prioritas
                    </div>

                    <div class="value">

                        @php

                            $priority =
                                strtoupper(
                                    trim(
                                        $workOrder->priority ?? ''
                                    )
                                );

                        @endphp


                        @if($priority === 'NORMAL')

                            <span class="priority priority-normal">
                                NORMAL
                            </span>

                        @elseif($priority === 'URGENT')

                            <span class="priority priority-urgent">
                                URGENT
                            </span>

                        @elseif($priority === 'EMERGENCY')

                            <span class="priority priority-emergency">
                                EMERGENCY
                            </span>

                        @else

                            -

                        @endif

                    </div>

                </div>


                <div class="field">

                    <div class="label">
                        Status
                    </div>

                    <div class="value">

                        @php

                            $status =
                                strtoupper(
                                    trim(
                                        $workOrder->status ?? ''
                                    )
                                );

                        @endphp


                        @if($status === 'OPEN')

                            <span class="status status-open">
                                OPEN
                            </span>

                        @elseif($status === 'DITERIMA')

                            <span class="status status-diterima">
                                DITERIMA
                            </span>

                        @elseif($status === 'SCHEDULED')

                            <span class="status status-scheduled">
                                SCHEDULED
                            </span>

                        @elseif($status === 'PENDING')

                            <span class="status status-pending">
                                PENDING
                            </span>

                        @elseif(
                            $status === 'IN PROGRESS' ||
                            $status === 'IN PROSES'
                        )

                            <span class="status status-progress">
                                IN PROGRESS
                            </span>

                        @elseif($status === 'SERVICE LUAR')

                            <span class="status status-service-luar">
                                SERVICE LUAR
                            </span>

                        @elseif(
                            $status === 'CLOSE' ||
                            $status === 'SELESAI'
                        )

                            <span class="status status-close">
                                CLOSE
                            </span>

                        @else

                            <span class="status status-open">
                                {{ $workOrder->status ?: '-' }}
                            </span>

                        @endif

                    </div>

                </div>


                <div class="field full">

                    <div class="label">
                        Deskripsi Kerusakan / Pekerjaan
                    </div>

                    <div class="value">
                        {{ $workOrder->deskripsi ?: '-' }}
                    </div>

                </div>


                <div class="field">

                    <div class="label">
                        Dibuat Oleh
                    </div>

                    <div class="value">
                        {{ $workOrder->dibuat_oleh ?: '-' }}
                    </div>

                </div>


                <div class="field">

                    <div class="label">
                        Tanggal Dibuat
                    </div>

                    <div class="value">

                        {{
                            $workOrder->created_at
                                ? \Carbon\Carbon::parse(
                                    $workOrder->created_at
                                )->format('d-m-Y H:i')
                                : '-'
                        }}

                    </div>

                </div>


            </div>

        </div>

    </div>


    {{-- =====================================================
         PROGRESS
    ====================================================== --}}

    <div class="card">

        <div class="card-header">

            <h2>
                Progress Work Order
            </h2>

            <p>
                Tahapan pekerjaan
            </p>

        </div>


        <div class="card-body">

            @php

                $progressStatus = match ($status) {

                    'OPEN' => 1,

                    'DITERIMA' => 2,

                    'SCHEDULED' => 3,

                    'PENDING' => 3,

                    'IN PROGRESS',
                    'IN PROSES' => 4,

                    'SERVICE LUAR' => 4,

                    'CLOSE',
                    'SELESAI' => 6,

                    default => 1,

                };

            @endphp


            <div class="progress-box">

                <div class="progress-title">
                    Status pekerjaan
                </div>

                <div class="progress-track">

                    <div class="progress-step {{ $progressStatus >= 1 ? 'active' : '' }}">
                        OPEN
                    </div>

                    <div class="progress-step {{ $progressStatus >= 2 ? 'active' : '' }}">
                        DITERIMA
                    </div>

                    <div class="progress-step {{ $progressStatus >= 3 ? 'active' : '' }}">
                        SCHEDULED
                    </div>

                    <div class="progress-step {{ $progressStatus >= 4 ? 'active' : '' }}">
                        IN PROGRESS
                    </div>

                    <div class="progress-step {{ $progressStatus >= 5 ? 'active' : '' }}">
                        PENYELESAIAN
                    </div>

                    <div class="progress-step {{ $progressStatus >= 6 ? 'active' : '' }}">
                        CLOSE
                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- =====================================================
         DATA PEKERJAAN
    ====================================================== --}}

    <div class="card">

        <div class="card-header">

            <h2>
                Data Pekerjaan
            </h2>

        </div>


        <div class="card-body">

            <div class="grid">


                <div class="field full">

                    <div class="label">
                        Laporan Diterima
                    </div>

                    <div class="value">
                        {{ $workOrder->laporan_diterima ?: '-' }}
                    </div>

                </div>


                <div class="field full">

                    <div class="label">
                        Perencanaan Pekerjaan
                    </div>

                    <div class="value">
                        {{ $workOrder->perencanaan_pekerjaan ?: '-' }}
                    </div>

                </div>


                <div class="field">

                    <div class="label">
                        Jadwal Perbaikan
                    </div>

                    <div class="value">

                        {{
                            $workOrder->jadwal_perbaikan
                                ? \Carbon\Carbon::parse(
                                    $workOrder->jadwal_perbaikan
                                )->format('d-m-Y H:i')
                                : '-'
                        }}

                    </div>

                </div>


                <div class="field">

                    <div class="label">
                        Teknisi
                    </div>

                    <div class="value">
                        {{ $workOrder->teknisi ?: '-' }}
                    </div>

                </div>


                <div class="field">

                    <div class="label">
                        Mulai Perbaikan
                    </div>

                    <div class="value">

                        @if($workOrder->tanggal_mulai_perbaikan)

                            {{
                                \Carbon\Carbon::parse(
                                    $workOrder->tanggal_mulai_perbaikan
                                )->format('d-m-Y')
                            }}

                        @else

                            -

                        @endif

                        @if($workOrder->jam_mulai_perbaikan)
                            {{ $workOrder->jam_mulai_perbaikan }}
                        @endif

                    </div>

                </div>


                <div class="field">

                    <div class="label">
                        Selesai Perbaikan
                    </div>

                    <div class="value">

                        @if($workOrder->tanggal_selesai_perbaikan)

                            {{
                                \Carbon\Carbon::parse(
                                    $workOrder->tanggal_selesai_perbaikan
                                )->format('d-m-Y')
                            }}

                        @else

                            -

                        @endif

                        @if($workOrder->jam_selesai_perbaikan)
                            {{ $workOrder->jam_selesai_perbaikan }}
                        @endif

                    </div>

                </div>


                <div class="field full">

                    <div class="label">
                        Keterangan
                    </div>

                    <div class="value">
                        {{ $workOrder->keterangan ?: '-' }}
                    </div>

                </div>


            </div>

        </div>

    </div>


    {{-- =====================================================
         FOTO KERUSAKAN
    ====================================================== --}}

    <div class="card">

        <div class="card-header">

            <h2>
                Foto Kerusakan
            </h2>

            <p>
                Dokumentasi saat Work Order dibuat
            </p>

        </div>


        <div class="card-body">

            @php

                $fotoKerusakan =
                    $workOrder->foto_kerusakan ?? [];


                if (is_string($fotoKerusakan)) {

                    $decoded =
                        json_decode(
                            $fotoKerusakan,
                            true
                        );

                    $fotoKerusakan =
                        is_array($decoded)
                            ? $decoded
                            : [$fotoKerusakan];

                }


                if (
                    empty($fotoKerusakan)
                    &&
                    $workOrder->foto
                ) {

                    $fotoKerusakan = [
                        $workOrder->foto
                    ];

                }


                if (
                    !is_array($fotoKerusakan)
                ) {

                    $fotoKerusakan = [];

                }

            @endphp


            @if(count($fotoKerusakan) > 0)

                <div class="photo-grid">

                    @foreach($fotoKerusakan as $foto)

                        @if($foto)

                            <div class="photo-item">

                                <img
                                    src="{{ asset(
                                        'storage/' . $foto
                                    ) }}"
                                    alt="Foto Kerusakan"
                                >

                                <div class="photo-caption">
                                    Foto kerusakan
                                </div>

                            </div>

                        @endif

                    @endforeach

                </div>

            @else

                <div class="empty-photo">
                    Belum ada foto kerusakan.
                </div>

            @endif

        </div>

    </div>


    {{-- =====================================================
         FOTO PERBAIKAN
    ====================================================== --}}

    <div class="card">

        <div class="card-header">

            <h2>
                Foto Perbaikan
            </h2>

            <p>
                Dokumentasi hasil pekerjaan
            </p>

        </div>


        <div class="card-body">

            @if($workOrder->foto_perbaikan)

                <div class="photo-grid">

                    <div class="photo-item">

                        <img
                            src="{{ asset(
                                'storage/' .
                                $workOrder->foto_perbaikan
                            ) }}"
                            alt="Foto Perbaikan"
                        >

                        <div class="photo-caption">
                            Foto hasil perbaikan
                        </div>

                    </div>

                </div>

            @else

                <div class="empty-photo">
                    Foto perbaikan belum tersedia.
                </div>

            @endif

        </div>

    </div>


</div>


{{-- =========================================================
     MODAL PILIH ORIENTASI
========================================================= --}}

<div
    id="printOrientationModal"
    class="print-orientation-overlay"
    onclick="klikPrintOverlay(event)"
>

    <div class="print-orientation-modal">


        <div class="print-orientation-header">

            <h3>
                Cetak Work Order
            </h3>

            <p>
                Pilih orientasi halaman.
            </p>

        </div>


        <div class="print-orientation-options">


            <button
                type="button"
                class="orientation-option"
                onclick="printWorkOrder('portrait')"
            >

                <div class="orientation-icon">
                    <x-icon name="file"></x-icon>
                </div>

                <strong>
                    Portrait
                </strong>

                <span>
                    Tegak
                </span>

            </button>


            <button
                type="button"
                class="orientation-option"
                onclick="printWorkOrder('landscape')"
            >

                <div class="orientation-icon">
                    <x-icon name="file"></x-icon>
                </div>

                <strong>
                    Landscape
                </strong>

                <span>
                    Mendatar
                </span>

            </button>


        </div>


        <div class="print-orientation-footer">

            <button
                type="button"
                class="print-cancel"
                onclick="tutupPilihanPrint()"
            >
                Batal
            </button>

        </div>

    </div>

</div>


<script>

    /*
    |--------------------------------------------------------------------------
    | MODAL
    |--------------------------------------------------------------------------
    */

    function bukaPilihanPrint()
    {
        const modal =
            document.getElementById(
                'printOrientationModal'
            );

        if (!modal) {
            return;
        }

        modal.classList.add(
            'active'
        );

        document.body.style.overflow =
            'hidden';
    }


    function tutupPilihanPrint()
    {
        const modal =
            document.getElementById(
                'printOrientationModal'
            );

        if (!modal) {
            return;
        }

        modal.classList.remove(
            'active'
        );

        document.body.style.overflow =
            '';
    }


    /*
    |--------------------------------------------------------------------------
    | PRINT
    |--------------------------------------------------------------------------
    | @page size ditulis langsung ke style element.
    | Ini lebih kompatibel daripada CSS variable.
    |--------------------------------------------------------------------------
    */

    function printWorkOrder(
        orientation
    )
    {
        tutupPilihanPrint();


        let printStyle =
            document.getElementById(
                'dynamicPrintStyle'
            );


        if (printStyle) {

            printStyle.remove();

        }


        printStyle =
            document.createElement(
                'style'
            );


        printStyle.id =
            'dynamicPrintStyle';


        const pageSize =
            orientation === 'landscape'
                ? 'A4 landscape'
                : 'A4 portrait';


        printStyle.textContent = `
            @media print {

                @page {
                    size: ${pageSize};
                    margin: 12mm;
                }

                html,
                body {
                    width: auto !important;
                    min-width: 0 !important;
                    max-width: none !important;
                    overflow: visible !important;
                }

                .container {
                    width: 100% !important;
                    max-width: none !important;
                    margin: 0 !important;
                    padding: 0 !important;
                }
            }
        `;


        document.head.appendChild(
            printStyle
        );


        /*
         * Beri waktu browser menerapkan
         * style @page sebelum dialog cetak.
         */

        setTimeout(
            function () {

                window.print();

            },
            300
        );
    }


    /*
    |--------------------------------------------------------------------------
    | OVERLAY
    |--------------------------------------------------------------------------
    */

    function klikPrintOverlay(
        event
    )
    {
        const modal =
            document.getElementById(
                'printOrientationModal'
            );

        if (
            event.target === modal
        ) {

            tutupPilihanPrint();

        }
    }


    /*
    |--------------------------------------------------------------------------
    | ESC
    |--------------------------------------------------------------------------
    */

    document.addEventListener(
        'keydown',
        function(event) {

            if (
                event.key === 'Escape'
            ) {

                tutupPilihanPrint();

            }

        }
    );


    /*
    |--------------------------------------------------------------------------
    | SETELAH PRINT
    |--------------------------------------------------------------------------
    */

    window.addEventListener(
        'afterprint',
        function() {

            const printStyle =
                document.getElementById(
                    'dynamicPrintStyle'
                );

            if (printStyle) {

                printStyle.remove();

            }

        }
    );


</script>


</body>

</html>