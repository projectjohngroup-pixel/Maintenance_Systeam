@extends('layouts.app')

@section('title', 'Edit Work Order')

@section('page_title', 'Edit Work Order')

@section(
    'page_subtitle',
    'Ubah data permintaan Work Order milik Anda'
)

@push('styles')
<style>

    * {
        box-sizing: border-box;
    }

    .wo-edit-page {
        padding: 20px 0 30px;
    }

    .wo-edit-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 20px;
        margin-bottom: 22px;
    }

    .wo-edit-header h1 {
        margin: 0 0 6px;
        font-size: 28px;
        color: var(--pds-ink);
    }

    .wo-edit-header p {
        margin: 0;
        color: var(--pds-muted);
        font-size: 14px;
    }

    .wo-edit-card {
        background: var(--pds-card);
        border-radius: 12px;
        box-shadow: 0 3px 12px rgba(0, 0, 0, .06);
        overflow: hidden;
    }

    .wo-edit-card-header {
        padding: 18px 22px;
        border-bottom: 1px solid var(--pds-line);
    }

    .wo-edit-card-title {
        font-size: 17px;
        font-weight: 700;
        color: var(--pds-ink);
    }

    .wo-edit-card-subtitle {
        margin-top: 5px;
        color: var(--pds-muted);
        font-size: 12px;
        line-height: 1.5;
    }

    .wo-edit-body {
        padding: 24px;
    }

    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 18px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
    }

    .full {
        grid-column: 1 / -1;
    }

    label {
        margin-bottom: 7px;
        font-size: 13px;
        font-weight: 700;
        color: var(--pds-ink-2);
    }

    .required {
        color: #dc2626;
    }

    input,
    select,
    textarea {
        width: 100%;
        padding: 10px 11px;
        border: 1px solid var(--pds-line-2);
        border-radius: 8px;
        background: var(--pds-card);
        color: var(--pds-ink);
        font-size: 13px;
    }

    input:focus,
    select:focus,
    textarea:focus {
        outline: none;
        border-color: #2563eb;
        box-shadow: 0 0 0 2px rgba(37, 99, 235, .08);
    }

    select {
        cursor: pointer;
    }

    select:disabled {
        background: var(--pds-soft-2);
        color: var(--pds-muted-2);
        cursor: not-allowed;
    }

    textarea {
        min-height: 125px;
        resize: vertical;
    }

    input[type="file"] {
        padding: 8px;
    }

    .upload-box {
        padding: 10px;
        border: 1px dashed var(--pds-line-2);
        border-radius: 8px;
        background: var(--pds-soft);
    }

    .field-info {
        margin-top: 5px;
        color: var(--pds-muted-2);
        font-size: 11px;
        line-height: 1.5;
    }

    .error-box {
        margin-bottom: 20px;
        padding: 12px 15px;
        border: 1px solid #fecaca;
        border-radius: 8px;
        background: #fef2f2;
        color: #b91c1c;
        font-size: 13px;
    }

    .error-box ul {
        margin: 7px 0 0 20px;
    }

    .info-box {
        margin-bottom: 20px;
        padding: 12px 15px;
        border: 1px solid #bfdbfe;
        border-radius: 8px;
        background: #eff6ff;
        color: #1e40af;
        font-size: 13px;
        line-height: 1.5;
    }

    .wo-edit-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 10px;
        padding: 16px 24px;
        border-top: 1px solid var(--pds-line);
        background: var(--pds-soft);
    }

    .footer-actions {
        display: flex;
        gap: 8px;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 38px;
        padding: 9px 15px;
        border: none;
        border-radius: 8px;
        text-decoration: none;
        cursor: pointer;
        font-size: 13px;
        font-weight: 600;
    }

    .btn-back {
        background: var(--pds-line);
        color: var(--pds-ink-2);
    }

    .btn-back:hover {
        background: var(--pds-line-2);
    }

    .btn-reset {
        background: var(--pds-soft-2);
        color: var(--pds-ink-2);
        border: 1px solid var(--pds-line-2);
    }

    .btn-reset:hover {
        background: var(--pds-line);
    }

    .btn-save {
        background: #2563eb;
        color: #ffffff;
    }

    .btn-save:hover {
        background: #1d4ed8;
    }

    /*
    |--------------------------------------------------------------------------
    | FOTO KERUSAKAN
    |--------------------------------------------------------------------------
    */

    .current-photo {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 12px;
        margin-top: 12px;
    }

    .current-photo-item {
        position: relative;
        width: 100%;
        height: 125px;
        padding: 0;
        border: 1px solid var(--pds-line);
        border-radius: 10px;
        overflow: hidden;
        background: var(--pds-soft);
        cursor: pointer;
    }

    .current-photo-item img {
        width: 100%;
        height: 100%;
        display: block;
        object-fit: cover;
        transition: transform .2s ease;
    }

    .current-photo-item:hover img {
        transform: scale(1.04);
    }

    .current-photo-overlay {
        position: absolute;
        left: 0;
        right: 0;
        bottom: 0;
        padding: 7px 8px;
        background: rgba(17, 24, 39, .72);
        color: #ffffff;
        font-size: 11px;
        font-weight: 600;
        text-align: center;
    }

    .no-photo {
        margin-top: 8px;
    }

    /*
    |--------------------------------------------------------------------------
    | FOTO PREVIEW BARU
    |--------------------------------------------------------------------------
    */

    .new-photo-preview {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 12px;
        margin-top: 12px;
    }

    .new-photo-item {
        height: 125px;
        border: 1px solid var(--pds-line);
        border-radius: 10px;
        overflow: hidden;
        background: var(--pds-soft);
    }

    .new-photo-item img {
        width: 100%;
        height: 100%;
        display: block;
        object-fit: cover;
    }

    /*
    |--------------------------------------------------------------------------
    | MODAL FOTO FULL
    |--------------------------------------------------------------------------
    */

    .photo-full-overlay {
        display: none;
        position: fixed;
        inset: 0;
        z-index: 99999;
        align-items: center;
        justify-content: center;
        padding: 25px;
        background: rgba(0, 0, 0, .82);
        backdrop-filter: blur(4px);
        -webkit-backdrop-filter: blur(4px);
    }

    .photo-full-overlay.active {
        display: flex;
    }

    .photo-full-container {
        position: relative;
        max-width: 95vw;
        max-height: 95vh;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .photo-full-image {
        max-width: 95vw;
        max-height: 90vh;
        width: auto;
        height: auto;
        object-fit: contain;
        border-radius: 8px;
        box-shadow: 0 15px 50px rgba(0, 0, 0, .35);
        background: var(--pds-card);
    }

    .photo-full-close {
        position: absolute;
        top: -15px;
        right: -15px;
        width: 38px;
        height: 38px;
        border: none;
        border-radius: 50%;
        background: var(--pds-card);
        color: var(--pds-ink);
        font-size: 22px;
        line-height: 1;
        cursor: pointer;
        box-shadow: 0 5px 20px rgba(0, 0, 0, .25);
    }

    .photo-full-close:hover {
        background: var(--pds-soft-2);
    }

    @media (max-width: 700px) {

        .wo-edit-page {
            padding: 12px;
        }

        .wo-edit-header {
            flex-direction: column;
        }

        .wo-edit-body {
            padding: 18px;
        }

        .form-grid {
            grid-template-columns: 1fr;
        }

        .full {
            grid-column: auto;
        }

        .wo-edit-footer {
            align-items: stretch;
            flex-direction: column;
        }

        .footer-actions {
            width: 100%;
            flex-direction: column-reverse;
        }

        .footer-actions .btn,
        .wo-edit-footer > .btn {
            width: 100%;
        }

        .current-photo,
        .new-photo-preview {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .photo-full-overlay {
            padding: 12px;
        }

        .photo-full-image {
            max-width: 96vw;
            max-height: 88vh;
        }

        .photo-full-close {
            top: -10px;
            right: -5px;
        }
    }

</style>
@endpush


@section('content')

@php

    /*
    |--------------------------------------------------------------------------
    | ROLE
    |--------------------------------------------------------------------------
    */

    $loginRole =
        \App\Support\DepartmentAccess::normalizeRole(
            auth()->user()->role ?? ''
        );

    $isUser =
        $loginRole === \App\Support\DepartmentAccess::PRODUKSI;


    /*
    |--------------------------------------------------------------------------
    | DATA FOTO KERUSAKAN
    |--------------------------------------------------------------------------
    */

    $fotoKerusakan =
        $workOrder->foto_kerusakan ?? [];

    if (
        is_string($fotoKerusakan)
    ) {

        $decoded =
            json_decode(
                $fotoKerusakan,
                true
            );

        $fotoKerusakan =
            is_array($decoded)
                ? $decoded
                : [
                    $fotoKerusakan
                ];
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


@if(!$isUser)

    <div class="wo-edit-page">

        <div class="error-box">

            Anda tidak memiliki akses ke form Edit Work Order User.

        </div>

    </div>

@else

<div class="wo-edit-page">


    {{-- =====================================================
         HEADER
    ====================================================== --}}

    <div class="wo-edit-header">

        <div>

            <h1>
                Edit Permintaan Work Order
            </h1>

            <p>
                Anda hanya dapat mengubah data permintaan Work Order milik Anda sendiri.
            </p>

        </div>

    </div>


    {{-- =====================================================
         ERROR
    ====================================================== --}}

    @if($errors->any())

        <div class="error-box">

            <strong>
                Data belum dapat disimpan.
            </strong>

            <ul>

                @foreach($errors->all() as $error)

                    <li>
                        {{ $error }}
                    </li>

                @endforeach

            </ul>

        </div>

    @endif


    <div class="info-box">

        <strong>Catatan:</strong>

        Anda hanya mengubah
        <strong>data permintaan Work Order</strong>.

        Status, tindak lanjut, teknisi, sparepart,
        foto perbaikan, dan data Maintenance
        tidak tersedia di form ini.

    </div>


    <div class="wo-edit-card">


        {{-- =================================================
             HEADER CARD
        ================================================== --}}

        <div class="wo-edit-card-header">

            <div class="wo-edit-card-title">

                Form Permintaan Work Order

            </div>

            <div class="wo-edit-card-subtitle">

                Ubah data permintaan kemudian simpan kembali.

            </div>

        </div>


        {{-- =================================================
             FORM
        ================================================== --}}

        <form
            action="{{ route(
                'work-orders.update',
                $workOrder->id
            ) }}"
            method="POST"
            enctype="multipart/form-data"
            id="formWorkOrder"
        >

            @csrf

            @method('PUT')


            <div class="wo-edit-body">

                <div class="form-grid">


                    {{-- =================================================
                         NO WO
                    ================================================== --}}

                    <div class="form-group">

                        <label for="no_wo">

                            No. WO

                            <span class="required">
                                *
                            </span>

                        </label>

                        <input
                            type="text"
                            id="no_wo"
                            name="no_wo"
                            value="{{ old(
                                'no_wo',
                                $workOrder->no_wo
                            ) }}"
                            required
                        >

                    </div>


                    {{-- =================================================
                         TANGGAL
                    ================================================== --}}

                    <div class="form-group">

                        <label for="tanggal_kerusakan">

                            Tanggal Kerusakan

                            <span class="required">
                                *
                            </span>

                        </label>

                        <input
                            type="date"
                            id="tanggal_kerusakan"
                            name="tanggal_kerusakan"
                            value="{{ old(
                                'tanggal_kerusakan',
                                $workOrder->tanggal_kerusakan
                                    ? \Carbon\Carbon::parse(
                                        $workOrder->tanggal_kerusakan
                                    )->format('Y-m-d')
                                    : ''
                            ) }}"
                            required
                        >

                    </div>


                    {{-- =================================================
                         JAM
                    ================================================== --}}

                    <div class="form-group">

                        <label for="jam_kerusakan">

                            Jam Kerusakan

                        </label>

                        <input
                            type="time"
                            id="jam_kerusakan"
                            name="jam_kerusakan"
                            value="{{ old(
                                'jam_kerusakan',
                                $workOrder->jam_kerusakan
                            ) }}"
                        >

                        <div class="field-info">

                            Opsional.

                        </div>

                    </div>


                    {{-- =================================================
                         KATEGORI
                    ================================================== --}}

                    <div class="form-group">

                        <label for="kategori">

                            Kategori

                            <span class="required">
                                *
                            </span>

                        </label>

                        <select
                            id="kategori"
                            name="kategori"
                            required
                        >

                            <option value="">
                                -- Pilih Kategori --
                            </option>

                            <option
                                value="PERMINTAAN PERBAIKAN / KERUSAKAN"
                                @selected(
                                    old(
                                        'kategori',
                                        $workOrder->kategori
                                    ) ===
                                    'PERMINTAAN PERBAIKAN / KERUSAKAN'
                                )
                            >
                                PERMINTAAN PERBAIKAN / KERUSAKAN
                            </option>

                            <option
                                value="PEMBUATAN BARU / MODIFIKASI"
                                @selected(
                                    old(
                                        'kategori',
                                        $workOrder->kategori
                                    ) ===
                                    'PEMBUATAN BARU / MODIFIKASI'
                                )
                            >
                                PEMBUATAN BARU / MODIFIKASI
                            </option>

                        </select>

                    </div>


                    {{-- =================================================
                         DITUJUKAN
                    ================================================== --}}

                    <div class="form-group">

                        <label for="tujuan">

                            Ditujukan

                            <span class="required">
                                *
                            </span>

                        </label>

                        <select
                            id="tujuan"
                            name="tujuan"
                            required
                        >

                            <option value="">
                                -- Pilih Tujuan --
                            </option>

                            <option
                                value="PREV-MAINT"
                                @selected(
                                    old(
                                        'tujuan',
                                        $workOrder->tujuan
                                    ) ===
                                    'PREV-MAINT'
                                )
                            >
                                PREV-MAINT
                            </option>

                            <option
                                value="MEKANIK/MAINTENANCE"
                                @selected(
                                    old(
                                        'tujuan',
                                        $workOrder->tujuan
                                    ) ===
                                    'MEKANIK/MAINTENANCE'
                                )
                            >
                                MEKANIK / MAINTENANCE
                            </option>

                        </select>

                    </div>


                    {{-- =================================================
                         AREA
                    ================================================== --}}

                    <div class="form-group">

                        <label for="area">

                            Area / Line

                        </label>

                        <select
                            id="area"
                            name="area"
                        >

                            <option value="">
                                -- Tidak Ada / Tidak Perlu Area --
                            </option>

                            @foreach($areas as $area)

                                <option
                                    value="{{ $area->nama_area }}"
                                    data-area-id="{{ $area->id }}"
                                    @selected(
                                        old(
                                            'area',
                                            $workOrder->area
                                        ) ===
                                        $area->nama_area
                                    )
                                >
                                    {{ $area->nama_area }}
                                </option>

                            @endforeach

                        </select>

                        <div class="field-info">

                            Opsional.

                        </div>

                    </div>


                    {{-- =================================================
                         MESIN
                    ================================================== --}}

                    <div class="form-group">

                        <label for="mesin">

                            Mesin

                        </label>

                        <select
                            id="mesin"
                            name="mesin"
                            disabled
                        >

                            <option value="">
                                -- Pilih Area Terlebih Dahulu --
                            </option>

                        </select>

                        <div class="field-info">

                            Opsional. Mesin akan mengikuti Area yang dipilih.

                        </div>

                    </div>


                    {{-- =================================================
                         JOB
                    ================================================== --}}

                    <div class="form-group full">

                        <label for="job">

                            Job

                            <span class="required">
                                *
                            </span>

                        </label>

                        <input
                            type="text"
                            id="job"
                            name="job"
                            value="{{ old(
                                'job',
                                $workOrder->job
                            ) }}"
                            placeholder="Masukkan pekerjaan yang diminta"
                            required
                        >

                    </div>


                    {{-- =================================================
                         DESKRIPSI
                    ================================================== --}}

                    <div class="form-group full">

                        <label for="deskripsi">

                            Deskripsi Kerusakan / Permintaan

                            <span class="required">
                                *
                            </span>

                        </label>

                        <textarea
                            id="deskripsi"
                            name="deskripsi"
                            placeholder="Jelaskan kerusakan atau permintaan pekerjaan..."
                            required
                        >{{ old(
                            'deskripsi',
                            $workOrder->deskripsi
                        ) }}</textarea>

                    </div>


                    {{-- =================================================
                         PRIORITAS
                    ================================================== --}}

                    <div class="form-group">

                        <label for="priority">

                            Prioritas

                            <span class="required">
                                *
                            </span>

                        </label>

                        <select
                            id="priority"
                            name="priority"
                            required
                        >

                            <option value="">
                                -- Pilih Prioritas --
                            </option>

                            <option
                                value="NORMAL"
                                @selected(
                                    old(
                                        'priority',
                                        $workOrder->priority
                                    ) === 'NORMAL'
                                )
                            >
                                NORMAL
                            </option>

                            <option
                                value="URGENT"
                                @selected(
                                    old(
                                        'priority',
                                        $workOrder->priority
                                    ) === 'URGENT'
                                )
                            >
                                URGENT
                            </option>

                            <option
                                value="EMERGENCY"
                                @selected(
                                    old(
                                        'priority',
                                        $workOrder->priority
                                    ) === 'EMERGENCY'
                                )
                            >
                                EMERGENCY
                            </option>

                        </select>

                    </div>


                    {{-- =================================================
                         FOTO KERUSAKAN
                    ================================================== --}}

                    <div class="form-group">

                        <label for="foto">

                            Foto Kerusakan

                        </label>

                        <div class="upload-box">

                            <input
                                type="file"
                                id="foto"
                                name="foto[]"
                                accept="image/*"
                                multiple
                            >

                        </div>

                        <div class="field-info">

                            Opsional. Maksimal 5 MB per foto.

                        </div>


                        {{-- FOTO LAMA --}}

                        @if(count($fotoKerusakan) > 0)

                            <div class="current-photo">

                                @foreach(
                                    $fotoKerusakan
                                    as $index => $foto
                                )

                                    @if($foto)

                                        <button
                                            type="button"
                                            class="current-photo-item"
                                            onclick="bukaFotoFull(
                                                @js(asset('storage/' . $foto))
                                            )"
                                            title="Klik untuk melihat foto ukuran penuh"
                                        >

                                            <img
                                                src="{{ asset(
                                                    'storage/' . $foto
                                                ) }}"
                                                alt="Foto Kerusakan {{ $index + 1 }}"
                                            >

                                            <span class="current-photo-overlay">
                                                <x-icon name="search"></x-icon> Lihat Foto
                                            </span>

                                        </button>

                                    @endif

                                @endforeach

                            </div>

                        @else

                            <div class="field-info no-photo">

                                Belum ada foto kerusakan.

                            </div>

                        @endif


                        {{-- PREVIEW FOTO BARU --}}

                        <div
                            id="newPhotoPreview"
                            class="new-photo-preview"
                        ></div>

                    </div>

                </div>

            </div>


            {{-- =================================================
                 FOOTER
            ================================================== --}}

            <div class="wo-edit-footer">

                <a
                    href="{{ route('work-orders.index') }}"
                    class="btn btn-back"
                >
                    Kembali
                </a>


                <div class="footer-actions">

                    <button
                        type="reset"
                        class="btn btn-reset"
                    >
                        Reset
                    </button>

                    <button
                        type="submit"
                        class="btn btn-save"
                        id="saveWorkOrder"
                    >
                        Simpan Perubahan
                    </button>

                </div>

            </div>

        </form>

    </div>

</div>


{{-- =========================================================
     MODAL FOTO FULL
========================================================= --}}

<div
    id="photoFullOverlay"
    class="photo-full-overlay"
    onclick="klikFotoFullOverlay(event)"
>

    <div class="photo-full-container">

        <img
            id="photoFullImage"
            class="photo-full-image"
            src=""
            alt="Foto Kerusakan"
        >

        <button
            type="button"
            class="photo-full-close"
            onclick="tutupFotoFull()"
            aria-label="Tutup foto"
        >
            ×
        </button>

    </div>

</div>

@endif


<script>

document.addEventListener(
    'DOMContentLoaded',
    function () {

        const areaSelect =
            document.getElementById(
                'area'
            );

        const mesinSelect =
            document.getElementById(
                'mesin'
            );

        const fotoInput =
            document.getElementById(
                'foto'
            );

        const preview =
            document.getElementById(
                'newPhotoPreview'
            );

        const machines =
            @json($machines);

        const oldMesin =
            @json(
                old(
                    'mesin',
                    $workOrder->mesin
                )
            );


        /*
        |--------------------------------------------------------------------------
        | LOAD MESIN BERDASARKAN AREA
        |--------------------------------------------------------------------------
        */

        function loadMachines()
        {
            if (
                !areaSelect ||
                !mesinSelect
            ) {
                return;
            }


            const selectedOption =
                areaSelect.options[
                    areaSelect.selectedIndex
                ];


            const areaId =
                selectedOption
                    ? selectedOption.getAttribute(
                        'data-area-id'
                    )
                    : '';


            mesinSelect.innerHTML =
                '';


            /*
            |----------------------------------------------------------------------
            | BELUM ADA AREA
            |----------------------------------------------------------------------
            */

            if (!areaId) {

                mesinSelect.disabled =
                    true;

                const option =
                    document.createElement(
                        'option'
                    );

                option.value =
                    '';

                option.textContent =
                    '-- Pilih Area Terlebih Dahulu --';

                mesinSelect.appendChild(
                    option
                );

                return;
            }


            /*
            |----------------------------------------------------------------------
            | FILTER MESIN
            |----------------------------------------------------------------------
            */

            const filteredMachines =
                machines.filter(
                    function (
                        machine
                    ) {

                        return String(
                            machine.area_id
                        ) === String(
                            areaId
                        );

                    }
                );


            /*
            |----------------------------------------------------------------------
            | TIDAK ADA MESIN
            |----------------------------------------------------------------------
            */

            if (
                filteredMachines.length ===
                0
            ) {

                mesinSelect.disabled =
                    true;

                const option =
                    document.createElement(
                        'option'
                    );

                option.value =
                    '';

                option.textContent =
                    '-- Tidak ada mesin pada Area ini --';

                mesinSelect.appendChild(
                    option
                );

                return;
            }


            /*
            |----------------------------------------------------------------------
            | ADA MESIN
            |----------------------------------------------------------------------
            */

            mesinSelect.disabled =
                false;


            const defaultOption =
                document.createElement(
                    'option'
                );

            defaultOption.value =
                '';

            defaultOption.textContent =
                '-- Pilih Mesin --';

            mesinSelect.appendChild(
                defaultOption
            );


            filteredMachines.forEach(
                function (
                    machine
                ) {

                    const option =
                        document.createElement(
                            'option'
                        );


                    option.value =
                        machine.nama_mesin;


                    option.textContent =
                        machine.kode_mesin
                            ? machine.kode_mesin +
                                ' - ' +
                                machine.nama_mesin
                            : machine.nama_mesin;


                    if (
                        oldMesin &&
                        oldMesin ===
                        machine.nama_mesin
                    ) {

                        option.selected =
                            true;

                    }


                    mesinSelect.appendChild(
                        option
                    );

                }
            );

        }


        /*
        |--------------------------------------------------------------------------
        | AREA BERUBAH
        |--------------------------------------------------------------------------
        */

        if (areaSelect) {

            areaSelect.addEventListener(
                'change',
                function () {

                    loadMachines();

                }
            );

        }


        /*
        |--------------------------------------------------------------------------
        | FOTO BARU PREVIEW
        |--------------------------------------------------------------------------
        */

        if (
            fotoInput &&
            preview
        ) {

            fotoInput.addEventListener(
                'change',
                function () {

                    preview.innerHTML =
                        '';

                    const files =
                        Array.from(
                            fotoInput.files ||
                            []
                        );


                    files.forEach(
                        function (
                            file
                        ) {

                            if (
                                !file.type.startsWith(
                                    'image/'
                                )
                            ) {
                                return;
                            }


                            const url =
                                URL.createObjectURL(
                                    file
                                );


                            const item =
                                document.createElement(
                                    'div'
                                );

                            item.className =
                                'new-photo-item';


                            const image =
                                document.createElement(
                                    'img'
                                );

                            image.src =
                                url;

                            image.alt =
                                file.name;


                            item.appendChild(
                                image
                            );

                            preview.appendChild(
                                item
                            );

                        }
                    );

                }
            );

        }


        /*
        |--------------------------------------------------------------------------
        | DOUBLE SUBMIT
        |--------------------------------------------------------------------------
        */

        const form =
            document.getElementById(
                'formWorkOrder'
            );

        const saveButton =
            document.getElementById(
                'saveWorkOrder'
            );


        if (
            form &&
            saveButton
        ) {

            form.addEventListener(
                'submit',
                function () {

                    saveButton.disabled =
                        true;

                    saveButton.textContent =
                        'Menyimpan...';

                }
            );

        }


        /*
        |--------------------------------------------------------------------------
        | LOAD AWAL
        |--------------------------------------------------------------------------
        */

        loadMachines();

    }
);


/*
|--------------------------------------------------------------------------
| BUKA FOTO FULL
|--------------------------------------------------------------------------
*/

function bukaFotoFull(
    fotoUrl
)
{
    const overlay =
        document.getElementById(
            'photoFullOverlay'
        );

    const image =
        document.getElementById(
            'photoFullImage'
        );


    if (
        !overlay ||
        !image ||
        !fotoUrl
    ) {
        return;
    }


    image.src =
        fotoUrl;


    overlay.classList.add(
        'active'
    );


    document.body.style.overflow =
        'hidden';
}


/*
|--------------------------------------------------------------------------
| TUTUP FOTO FULL
|--------------------------------------------------------------------------
*/

function tutupFotoFull()
{
    const overlay =
        document.getElementById(
            'photoFullOverlay'
        );

    const image =
        document.getElementById(
            'photoFullImage'
        );


    if (!overlay) {
        return;
    }


    overlay.classList.remove(
        'active'
    );


    document.body.style.overflow =
        '';


    if (image) {

        image.src =
            '';

    }
}


/*
|--------------------------------------------------------------------------
| KLIK AREA GELAP
|--------------------------------------------------------------------------
*/

function klikFotoFullOverlay(
    event
)
{
    const overlay =
        document.getElementById(
            'photoFullOverlay'
        );


    if (
        overlay &&
        event.target ===
        overlay
    ) {

        tutupFotoFull();

    }
}


/*
|--------------------------------------------------------------------------
| ESC
|--------------------------------------------------------------------------
*/

document.addEventListener(
    'keydown',
    function (
        event
    ) {

        if (
            event.key ===
            'Escape'
        ) {

            tutupFotoFull();

        }

    }
);

</script>

@endsection