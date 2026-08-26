@extends('layouts.app')

@section('title', 'Permintaan Work Order')

@section('page_title', 'Permintaan Work Order')

@section(
    'page_subtitle',
    'Pengajuan kerusakan atau pekerjaan kepada Maintenance'
)

@push('styles')
<style>
    .wo-create-page {
        width: 100%;
        max-width: 1280px;
        margin: 0 auto;
        padding: 6px 0 16px;
    }

    .wo-create-card {
        width: 100%;
        background: var(--pds-card);
        border: 1px solid var(--pds-line);
        border-radius: 12px;
        box-shadow: 0 3px 12px rgba(0, 0, 0, .05);
        overflow: hidden;
    }

    .wo-create-card-header {
        padding: 10px 16px;
        border-bottom: 1px solid var(--pds-line);
    }

    .wo-create-card-title {
        font-size: 15px;
        font-weight: 700;
        line-height: 1.3;
        color: var(--pds-ink);
    }

    .wo-create-card-subtitle {
        margin-top: 2px;
        color: var(--pds-muted);
        font-size: 12px;
        line-height: 1.35;
    }

    .wo-create-body {
        padding: 14px 16px;
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 10px 12px;
        align-items: start;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        min-width: 0;
    }

    .full {
        grid-column: 1 / -1;
    }

    .field-duo {
        display: grid;
        grid-template-columns: 1.35fr .9fr;
        gap: 7px;
    }

    label {
        margin: 0 0 4px;
        font-size: 12px;
        line-height: 1.3;
        font-weight: 700;
        color: var(--pds-ink-2);
    }

    .required {
        color: #dc2626;
    }

    .label-optional {
        color: var(--pds-muted-2);
        font-weight: 500;
    }

    .wo-create-page input,
    .wo-create-page select,
    .wo-create-page textarea {
        width: 100%;
        min-width: 0;
        border: 1px solid var(--pds-line-2);
        border-radius: 7px;
        background: var(--pds-card);
        color: var(--pds-ink-2);
        font-family: inherit;
        font-size: 13px;
        line-height: 1.35;
        transition:
            border-color .15s ease,
            box-shadow .15s ease;
    }

    .wo-create-page input,
    .wo-create-page select {
        height: 38px;
        min-height: 38px;
        padding: 7px 10px;
    }

    .wo-create-page textarea {
        min-height: 68px;
        height: 68px;
        padding: 8px 10px;
        resize: vertical;
    }

    .wo-create-page input:focus,
    .wo-create-page select:focus,
    .wo-create-page textarea:focus {
        outline: none;
        border-color: #2563eb;
        box-shadow: 0 0 0 2px rgba(37, 99, 235, .08);
    }

    .wo-create-page select {
        cursor: pointer;
    }

    .wo-create-page select:disabled {
        background: var(--pds-soft-2);
        color: var(--pds-muted-2);
        cursor: not-allowed;
    }

    .wo-create-page input[type="file"] {
        height: 38px;
        min-height: 38px;
        padding: 6px 8px;
    }

    .upload-box {
        padding: 4px;
        border: 1px dashed var(--pds-line-2);
        border-radius: 7px;
        background: var(--pds-soft);
    }

    .field-info {
        margin-top: 3px;
        color: var(--pds-muted-2);
        font-size: 10.5px;
        line-height: 1.25;
    }

    .error-box {
        margin-bottom: 10px;
        padding: 9px 11px;
        border: 1px solid #fecaca;
        border-radius: 7px;
        background: #fef2f2;
        color: #b91c1c;
        font-size: 12px;
    }

    .error-box ul {
        margin: 5px 0 0 16px;
    }

    .wo-create-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        padding: 9px 16px;
        border-top: 1px solid var(--pds-line);
        background: var(--pds-soft);
    }

    .footer-actions {
        display: flex;
        align-items: center;
        gap: 7px;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 36px;
        padding: 7px 14px;
        border: 0;
        border-radius: 7px;
        text-decoration: none;
        cursor: pointer;
        font-family: inherit;
        font-size: 12.5px;
        font-weight: 600;
        white-space: nowrap;
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
        color: #fff;
    }

    .btn-save:hover {
        background: #1d4ed8;
    }

    .btn:disabled {
        opacity: .65;
        cursor: wait;
    }

    /*
    |--------------------------------------------------------------------------
    | MODAL KONFIRMASI
    |--------------------------------------------------------------------------
    */

    .confirm-overlay {
        display: none;
        position: fixed;
        inset: 0;
        z-index: 99999;
        align-items: center;
        justify-content: center;
        padding: 16px;
        background: rgba(15, 23, 42, .58);
        backdrop-filter: blur(4px);
        -webkit-backdrop-filter: blur(4px);
    }

    .confirm-overlay.active {
        display: flex;
    }

    .confirm-modal {
        width: 100%;
        max-width: 460px;
        max-height: calc(100vh - 32px);
        background: var(--pds-card);
        border-radius: 14px;
        overflow: hidden;
        box-shadow: 0 25px 70px rgba(0, 0, 0, .25);
    }

    .confirm-header {
        padding: 14px 18px 9px;
        text-align: center;
        border-bottom: 1px solid var(--pds-line);
    }

    .confirm-icon {
        width: 40px;
        height: 40px;
        margin: 0 auto 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background: #dbeafe;
        color: #2563eb;
        font-size: 18px;
    }

    .confirm-header h3 {
        margin: 0 0 3px;
        font-size: 16px;
        color: var(--pds-ink);
    }

    .confirm-header p {
        margin: 2px 0 0;
        color: var(--pds-muted);
        font-size: 13px;
        line-height: 1.45;
    }

    .confirm-body {
        padding: 14px 16px;
        font-size: 13px;
        line-height: 1.5;
        background: var(--pds-card);
    }

    .confirm-info {
        margin-top: 10px;
        padding: 9px 11px;
        border-radius: 7px;
        background: var(--pds-soft);
        color: var(--pds-muted-2);
        font-size: 12px;
        line-height: 1.45;
    }

    .confirm-footer {
        display: flex;
        justify-content: flex-end;
        gap: 7px;
        padding: 10px 16px;
        border-top: 1px solid var(--pds-line);
        background: var(--pds-soft);
    }

    .confirm-cancel,
    .confirm-submit {
        min-height: 38px;
        padding: 8px 16px;
        border: 0;
        border-radius: 7px;
        cursor: pointer;
        font-family: inherit;
        font-size: 14px;
        font-weight: 600;
    }

    .confirm-cancel {
        background: var(--pds-line);
        color: var(--pds-ink-2);
    }

    .confirm-cancel:hover {
        background: var(--pds-line-2);
    }

    .confirm-submit {
        background: #2563eb;
        color: #fff;
    }

    .confirm-submit:hover {
        background: #1d4ed8;
    }

    .confirm-submit:disabled {
        opacity: .65;
        cursor: wait;
    }

    /*
    |--------------------------------------------------------------------------
    | RESPONSIVE
    |--------------------------------------------------------------------------
    */

    @media (max-width: 1150px) {
        .form-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
    }

    @media (max-width: 850px) {
        .wo-create-page {
            padding: 4px 0 12px;
        }

        .wo-create-body {
            padding: 12px;
        }

        .form-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 8px 10px;
        }

        .wo-create-footer {
            padding: 9px 12px;
        }
    }

    @media (max-width: 600px) {
        .wo-create-body {
            padding: 10px;
        }

        .form-grid {
            grid-template-columns: 1fr;
            gap: 8px;
        }

        .full {
            grid-column: auto;
        }

        .field-duo {
            grid-template-columns: 1fr 1fr;
        }

        .wo-create-footer {
            flex-direction: column;
            align-items: stretch;
        }

        .footer-actions {
            width: 100%;
            display: grid;
            grid-template-columns: 1fr 1fr;
        }

        .wo-create-footer > .btn,
        .footer-actions .btn {
            width: 100%;
        }

        .confirm-footer {
            flex-direction: column-reverse;
        }

        .confirm-footer button {
            width: 100%;
        }
    }
</style>
@endpush


@section('content')

<div class="wo-create-page">

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


    <div class="wo-create-card">

        {{-- HEADER FORM --}}
        <div class="wo-create-card-header">

            <div class="wo-create-card-title">
                Form Permintaan Work Order
            </div>

            <div class="wo-create-card-subtitle">
                Isi hanya data permintaan Anda. Data tindak lanjut akan diisi oleh Maintenance.
            </div>

        </div>


        {{-- FORM --}}
        <form
            action="{{ route('work-orders.store') }}"
            method="POST"
            enctype="multipart/form-data"
            id="formWorkOrder"
        >
            @csrf

            <div class="wo-create-body">

                <div class="form-grid">

                    {{-- NO WO --}}
                    <div class="form-group">

                        <label for="no_wo">
                            No. WO
                            <span class="required">*</span>
                        </label>

                        <input
                            type="text"
                            id="no_wo"
                            name="no_wo"
                            value="{{ old('no_wo', $nextNoWo ?? '') }}"
                            placeholder="{{ $nextNoWo ?? '001' }}"
                            required
                        >

                        <div id="no_wo_status" style="margin-top:4px;font-size:0.85em;display:none;"></div>

                    </div>


                    {{-- TANGGAL + JAM --}}
                    <div class="form-group">

                        <label for="tanggal_kerusakan">
                            Tanggal Kerusakan
                            <span class="required">*</span>
                            <span class="label-optional">
                                - jam opsional
                            </span>
                        </label>

                        <div class="field-duo">

                            <input
                                type="date"
                                id="tanggal_kerusakan"
                                name="tanggal_kerusakan"
                                value="{{ old('tanggal_kerusakan', date('Y-m-d')) }}"
                                required
                            >

                            <input
                                type="time"
                                id="jam_kerusakan"
                                name="jam_kerusakan"
                                value="{{ old('jam_kerusakan') }}"
                                title="Jam kerusakan (opsional)"
                                aria-label="Jam kerusakan (opsional)"
                            >

                        </div>

                    </div>


                    {{-- KATEGORI --}}
                    <div class="form-group">

                        <label for="kategori">
                            Kategori
                            <span class="required">*</span>
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
                                    old('kategori') === 'PERMINTAAN PERBAIKAN / KERUSAKAN'
                                )
                            >
                                PERMINTAAN PERBAIKAN / KERUSAKAN
                            </option>

                            <option
                                value="PEMBUATAN BARU / MODIFIKASI"
                                @selected(
                                    old('kategori') === 'PEMBUATAN BARU / MODIFIKASI'
                                )
                            >
                                PEMBUATAN BARU / MODIFIKASI
                            </option>

                        </select>

                    </div>


                    {{-- DITUJUKAN --}}
                    <div class="form-group">

                        <label for="tujuan">
                            Ditujukan
                            <span class="required">*</span>
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
                                    old('tujuan') === 'PREV-MAINT'
                                )
                            >
                                PREV-MAINT
                            </option>

                            <option
                                value="MEKANIK/MAINTENANCE"
                                @selected(
                                    old('tujuan') === 'MEKANIK/MAINTENANCE'
                                )
                            >
                                MEKANIK / MAINTENANCE
                            </option>

                        </select>

                    </div>


                    {{-- AREA --}}
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
                                        old('area') === $area->nama_area
                                    )
                                >
                                    {{ $area->nama_area }}
                                </option>

                            @endforeach

                        </select>

                        <div class="field-info">
                            Opsional. Pilih jika pekerjaan berkaitan dengan Area / Line tertentu.
                        </div>

                    </div>


                    {{-- MESIN --}}
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
                            Opsional. Jika Area dipilih, daftar mesin mengikuti Area tersebut.
                        </div>

                    </div>


                    {{-- PRIORITAS --}}
                    <div class="form-group">

                        <label for="priority">
                            Prioritas
                            <span class="required">*</span>
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
                                    old('priority') === 'NORMAL'
                                )
                            >
                                NORMAL
                            </option>

                            <option
                                value="URGENT"
                                @selected(
                                    old('priority') === 'URGENT'
                                )
                            >
                                URGENT
                            </option>

                            <option
                                value="EMERGENCY"
                                @selected(
                                    old('priority') === 'EMERGENCY'
                                )
                            >
                                EMERGENCY
                            </option>

                        </select>

                    </div>


                    {{-- UPLOAD FOTO --}}
                    <div class="form-group">

                        <label for="foto">
                            Upload Foto Kerusakan
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

                    </div>


                    {{-- JOB --}}
                    <div class="form-group full">

                        <label for="job">
                            Job
                            <span class="required">*</span>
                        </label>

                        <input
                            type="text"
                            id="job"
                            name="job"
                            value="{{ old('job') }}"
                            placeholder="Masukkan pekerjaan yang diminta"
                            required
                        >

                    </div>


                    {{-- DESKRIPSI --}}
                    <div class="form-group full">

                        <label for="deskripsi">
                            Deskripsi Kerusakan / Permintaan
                            <span class="required">*</span>
                        </label>

                        <textarea
                            id="deskripsi"
                            name="deskripsi"
                            placeholder="Jelaskan kerusakan atau permintaan pekerjaan..."
                            required
                        >{{ old('deskripsi') }}</textarea>

                    </div>

                </div>

            </div>


            {{-- FOOTER --}}
            <div class="wo-create-footer">

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
                        id="btnSubmitWO"
                    >
                        Kirim Permintaan WO
                    </button>

                </div>

            </div>

        </form>

    </div>

</div>


{{-- MODAL KONFIRMASI SIMPAN --}}
<div
    id="confirmWOModal"
    class="confirm-overlay"
    onclick="klikOverlayKonfirmasi(event)"
>

    <div class="confirm-modal">

        <div class="confirm-header">

            <div class="confirm-icon">
                <x-icon name="check"></x-icon>
            </div>

            <h3>
                Konfirmasi Permintaan WO
            </h3>

            <p>
                Pastikan data permintaan Work Order sudah benar.
            </p>

        </div>


        <div class="confirm-body">

            Apakah Anda yakin ingin mengirim
            <strong>Permintaan Work Order</strong>
            ini?

            <div class="confirm-info">

                Setelah dikonfirmasi, data akan disimpan dan
                dikirim ke Maintenance untuk ditindaklanjuti.

            </div>

        </div>


        <div class="confirm-footer">

            <button
                type="button"
                class="confirm-cancel"
                id="btnCancelConfirm"
                onclick="tutupKonfirmasiWO()"
            >
                Batal
            </button>

            <button
                type="button"
                class="confirm-submit"
                id="btnConfirmSubmit"
                onclick="konfirmasiSimpanWO()"
            >
                Ya, Simpan & Kirim
            </button>

        </div>

    </div>

</div>


<script>
/*
|--------------------------------------------------------------------------
| DOM READY
|--------------------------------------------------------------------------
*/

document.addEventListener(
    'DOMContentLoaded',
    function () {

        const areaSelect =
            document.getElementById('area');

        const mesinSelect =
            document.getElementById('mesin');

        const form =
            document.getElementById('formWorkOrder');

        const confirmModal =
            document.getElementById('confirmWOModal');

        const btnSubmitWO =
            document.getElementById('btnSubmitWO');

        const machines =
            @json($machines);

        const oldMesin =
            @json(old('mesin'));


        /*
        |--------------------------------------------------------------------------
        | LOAD MESIN SESUAI AREA
        |--------------------------------------------------------------------------
        */

        function loadMachines() {

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


            mesinSelect.innerHTML = '';


            if (!areaId) {

                mesinSelect.disabled = true;

                const option =
                    document.createElement('option');

                option.value = '';

                option.textContent =
                    '-- Pilih Area Terlebih Dahulu --';

                mesinSelect.appendChild(option);

                return;
            }


            const filteredMachines =
                machines.filter(
                    function (machine) {

                        return String(
                            machine.area_id
                        ) === String(
                            areaId
                        );

                    }
                );


            if (
                filteredMachines.length === 0
            ) {

                mesinSelect.disabled = true;

                const option =
                    document.createElement('option');

                option.value = '';

                option.textContent =
                    '-- Tidak ada mesin pada Area ini --';

                mesinSelect.appendChild(option);

                return;
            }


            mesinSelect.disabled = false;


            const defaultOption =
                document.createElement('option');

            defaultOption.value = '';

            defaultOption.textContent =
                '-- Pilih Mesin --';

            mesinSelect.appendChild(
                defaultOption
            );


            filteredMachines.forEach(
                function (machine) {

                    const option =
                        document.createElement('option');

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

                        option.selected = true;

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
        | LOAD AWAL
        |--------------------------------------------------------------------------
        */

        loadMachines();


        /*
        |--------------------------------------------------------------------------
        | TOMBOL SUBMIT → TAMPILKAN MODAL
        |--------------------------------------------------------------------------
        |
        | Menggunakan click handler pada tombol,
        | BUKAN submit handler pada form.
        |
        | Ini menghindari konflik dengan GlobalLoading
        | yang menangkap submit event.
        |
        | Alur:
        | 1. Klik tombol → validasi HTML → tampilkan modal
        | 2. Klik konfirmasi → tutup modal → requestSubmit()
        | 3. GlobalLoading menangkap submit → tampilkan loading
        | 4. Form terkirim ke Laravel
        |
        */

        if (btnSubmitWO) {

            btnSubmitWO.addEventListener(
                'click',
                function (event) {

                    event.preventDefault();


                    if (
                        form &&
                        !form.checkValidity()
                    ) {

                        form.reportValidity();

                        return;

                    }


                    bukaKonfirmasiWO();

                }
            );

        }


        /*
        |--------------------------------------------------------------------------
        | ESC
        |--------------------------------------------------------------------------
        */

        document.addEventListener(
            'keydown',
            function (event) {

                if (
                    event.key === 'Escape' &&
                    confirmModal &&
                    confirmModal.classList.contains(
                        'active'
                    )
                ) {

                    tutupKonfirmasiWO();

                }

            }
        );

    }
);


/*
|--------------------------------------------------------------------------
| BUKA KONFIRMASI
|--------------------------------------------------------------------------
*/

function bukaKonfirmasiWO()
{

    const modal =
        document.getElementById(
            'confirmWOModal'
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


/*
|--------------------------------------------------------------------------
| TUTUP KONFIRMASI
|--------------------------------------------------------------------------
*/

function tutupKonfirmasiWO()
{

    const modal =
        document.getElementById(
            'confirmWOModal'
        );

    const button =
        document.getElementById(
            'btnConfirmSubmit'
        );

    const cancelButton =
        document.getElementById(
            'btnCancelConfirm'
        );


    if (!modal) {
        return;
    }


    modal.classList.remove(
        'active'
    );


    document.body.style.overflow =
        '';


    if (button) {

        button.disabled = false;

        button.textContent =
            'Ya, Simpan & Kirim';

    }


    if (cancelButton) {

        cancelButton.disabled = false;

    }

}


/*
|--------------------------------------------------------------------------
| KLIK OVERLAY
|--------------------------------------------------------------------------
*/

function klikOverlayKonfirmasi(event)
{

    const modal =
        document.getElementById(
            'confirmWOModal'
        );


    if (
        modal &&
        event.target === modal
    ) {

        tutupKonfirmasiWO();

    }

}


/*
|--------------------------------------------------------------------------
| KONFIRMASI SIMPAN
|--------------------------------------------------------------------------
*/

function konfirmasiSimpanWO()
{

    const form =
        document.getElementById(
            'formWorkOrder'
        );

    const button =
        document.getElementById(
            'btnConfirmSubmit'
        );

    const cancelButton =
        document.getElementById(
            'btnCancelConfirm'
        );


    if (!form) {
        return;
    }


    if (button) {

        button.disabled = true;

        button.textContent =
            'Menyimpan...';

    }


    if (cancelButton) {

        cancelButton.disabled = true;

    }


    /*
    |--------------------------------------------------------------------------
    | TUTUP MODAL LALU SUBMIT
    |--------------------------------------------------------------------------
    |
    | Tutup modal sebelum requestSubmit()
    | agar overlay loading GlobalLoading
    | tidak tumpang tindih dengan modal.
    |
    */

    const modal =
        document.getElementById(
            'confirmWOModal'
        );

    if (modal) {

        modal.classList.remove(
            'active'
        );

        document.body.style.overflow =
            '';

    }


    /*
    |--------------------------------------------------------------------------
    | SUBMIT FORM
    |--------------------------------------------------------------------------
    |
    | requestSubmit() memicu submit event.
    | GlobalLoading akan menangkapnya,
    | menampilkan loading overlay,
    | dan mencegah double submit.
    |
    */

    form.requestSubmit();

}


/*
|--------------------------------------------------------------------------
| RESET FORM
|--------------------------------------------------------------------------
*/

document.addEventListener(
    'DOMContentLoaded',
    function () {

        const form =
            document.getElementById(
                'formWorkOrder'
            );

        const areaSelect =
            document.getElementById(
                'area'
            );


        if (
            form &&
            areaSelect
        ) {

            form.addEventListener(
                'reset',
                function () {

                    setTimeout(
                        function () {

                            const mesinSelect =
                                document.getElementById(
                                    'mesin'
                                );


                            if (!mesinSelect) {
                                return;
                            }


                            mesinSelect.disabled =
                                true;


                            mesinSelect.innerHTML =
                                `
                                <option value="">
                                    -- Pilih Area Terlebih Dahulu --
                                </option>
                                `;

                        },
                        0
                    );

                }
            );

        }

    }


    /* ================================
       NO WO AVAILABILITY CHECK
       ================================ */

    var noWoInput =
        document.getElementById(
            'no_wo'
        );

    var noWoStatus =
        document.getElementById(
            'no_wo_status'
        );

    if (noWoInput && noWoStatus) {

        var noWoTimer = null;

        noWoInput.addEventListener(
            'input',
            function () {

                clearTimeout(
                    noWoTimer
                );

                var val =
                    this.value
                        .trim();

                if (
                    val === ''
                ) {

                    noWoStatus.style.display =
                        'none';

                    return;
                }

                noWoTimer =
                    setTimeout(
                        function () {

                            fetch(
                                '{{ route("work-orders.check-no-wo") }}?no_wo=' +
                                encodeURIComponent(
                                    val
                                )
                            )
                            .then(
                                function (
                                    r
                                ) {
                                    return r.json();
                                }
                            )
                            .then(
                                function (
                                    data
                                ) {

                                    noWoStatus.style.display =
                                        'block';

                                    if (
                                        data.available
                                    ) {

                                        noWoStatus.innerHTML =
                                            '<span style="color:#27ae60;">&#10003; ' +
                                            data.message +
                                            '</span>';

                                    } else {

                                        noWoStatus.innerHTML =
                                            '<span style="color:#e74c3c;">&#10007; ' +
                                            data.message +
                                            '</span>';
                                    }
                                }
                            )
                            .catch(
                                function () {

                                    noWoStatus.style.display =
                                        'none';
                                }
                            );

                        },
                        400
                    );
            }
        );
    }

</script>

@endsection