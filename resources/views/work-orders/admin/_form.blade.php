<style>
    * {
        box-sizing: border-box;
    }

    .wo-create-page {
        padding: 12px 0 18px;
    }

    .wo-create-header {
        margin-bottom: 10px;
    }

        .wo-create-header h1 {
        margin: 0 0 4px;
        font-size: 22px;
        color: var(--pds-ink);
    }

    .wo-create-header p {
        margin: 0;
        color: var(--pds-muted);
        font-size: 13px;
    }

    .wo-create-card {
        background: var(--pds-card);
        border-radius: 12px;
        box-shadow: 0 3px 12px rgba(0, 0, 0, .06);
        overflow: hidden;
    }

    .wo-create-card-header {
        padding: 11px 16px;
        border-bottom: 1px solid var(--pds-line);
    }

        .wo-create-card-title {
        font-size: 16px;
        font-weight: 700;
        color: var(--pds-ink);
    }

    .wo-create-card-subtitle {
        margin-top: 2px;
        color: var(--pds-muted);
        font-size: 13px;
    }

    .wo-create-body {
        padding: 14px 16px;
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 9px 12px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        min-width: 0;
    }

    .field-duo {
        display: flex;
        gap: 8px;
    }

    .field-duo input {
        flex: 1;
        min-width: 0;
    }

    .label-optional {
        color: var(--pds-muted-2);
        font-weight: 500;
    }

    .full {
        grid-column: 1 / -1;
    }

    /* Layar lebar: form memakai ruang penuh dengan 3 kolom */
    @media (min-width: 1200px) {

        .wo-create-body {
            padding: 20px 24px;
        }

        .form-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 12px 16px;
        }
    }

        label {
        margin-bottom: 6px;
        font-size: 13px;
        line-height: 1.3;
        font-weight: 700;
        color: var(--pds-ink-2);
    }

    .required {
        color: #dc2626;
    }

        input,
    select,
    textarea {
        min-height: 40px;
        height: auto;
        padding: 9px 12px;
        border: 1px solid var(--pds-line-2);
        border-radius: 7px;
        background: var(--pds-card);
        color: var(--pds-ink-2);
        font-family: inherit;
        font-size: 14px;
        resize: vertical;
        line-height: 1.45;
    }

    textarea {
        min-height: 88px;
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

        input[type="file"] {
        height: 40px;
        min-height: 40px;
        padding: 8px 10px;
    }

    .upload-box {
        padding: 5px;
        border: 1px dashed var(--pds-line-2);
        border-radius: 7px;
        background: var(--pds-soft);
    }

        .field-info {
        margin-top: 4px;
        color: var(--pds-muted-2);
        font-size: 12px;
        line-height: 1.3;
    }

        .error-box {
        margin-bottom: 12px;
        padding: 10px 12px;
        border: 1px solid #fecaca;
        border-radius: 7px;
        background: #fef2f2;
        color: #b91c1c;
        font-size: 13px;
    }

    .error-box ul {
        margin: 6px 0 0 18px;
    }

    .wo-create-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 8px;
        padding: 10px 16px;
        border-top: 1px solid var(--pds-line);
        background: var(--pds-soft);
    }

    .footer-actions {
        display: flex;
        gap: 7px;
    }

        .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 40px;
        padding: 9px 16px;
        border: none;
        border-radius: 7px;
        text-decoration: none;
        cursor: pointer;
        font-family: inherit;
        font-size: 14px;
        font-weight: 600;
    }

    .btn:disabled {
        opacity: .65;
        cursor: wait;
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
        border: none;
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
        color: #ffffff;
    }

    .confirm-submit:hover {
        background: #1d4ed8;
    }

    .confirm-submit:disabled {
        opacity: .65;
        cursor: wait;
    }

    @media (max-width: 900px) {
        .wo-create-body {
            padding: 13px;
        }

        .form-grid {
            gap: 8px 10px;
        }

        textarea {
            min-height: 68px;
        }
    }

    @media (max-width: 700px) {
        .wo-create-page {
            padding: 10px 0 14px;
        }

        .wo-create-card-header {
            padding: 10px 13px;
        }

        .wo-create-body {
            padding: 12px;
        }

        .form-grid {
            grid-template-columns: 1fr;
            gap: 8px;
        }

        .full {
            grid-column: auto;
        }

        textarea {
            min-height: 68px;
        }

        .wo-create-footer {
            align-items: stretch;
            flex-direction: column;
            padding: 10px 12px;
        }

        .footer-actions {
            width: 100%;
            flex-direction: column-reverse;
        }

        .footer-actions .btn,
        .wo-create-footer > .btn {
            width: 100%;
        }

        .confirm-footer {
            flex-direction: column-reverse;
        }

        .confirm-footer button {
            width: 100%;
        }

        .field-duo {
            flex-direction: row;
        }
    }

    

    
</style>

<div class="wo-create-page">

    <div class="wo-create-header">
        <h1>Form Work Order</h1>

        <p>
            Buat permintaan Work Order untuk ditindaklanjuti oleh Maintenance.
        </p>
    </div>

    <div class="wo-create-card">

        <div class="wo-create-card-header">

            <div class="wo-create-card-title">
                Form Permintaan Work Order
            </div>

            <div class="wo-create-card-subtitle">
                Isi hanya data permintaan Anda. Data tindak lanjut akan diisi oleh Maintenance.
            </div>

        </div>

        <form
            action="{{ route('work-orders.admin.store') }}"
            method="POST"
            enctype="multipart/form-data"
            id="formWorkOrder"
        >

            @csrf

            <div class="wo-create-body">

                @if ($errors->any())

                    <div class="error-box">

                        <strong>
                            Data belum dapat disimpan:
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

                <div class="form-grid">

                    <div class="form-group">

                        <label for="no_wo">
                            No. WO
                            <span class="required">*</span>
                        </label>

                        <input
                            type="text"
                            id="no_wo"
                            name="no_wo"
                            value="{{ old('no_wo', $nextNoWo ?? $workOrder->no_wo ?? '') }}"
                            placeholder="{{ $nextNoWo ?? $workOrder->no_wo ?? '001' }}"
                            required
                        >

                        <div id="no_wo_status" style="margin-top:4px;font-size:0.85em;display:none;"></div>

                    </div>

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
                                @selected(old('tujuan') === 'PREV-MAINT')
                            >
                                PREV-MAINT
                            </option>

                            <option
                                value="MEKANIK/MAINTENANCE"
                                @selected(old('tujuan') === 'MEKANIK/MAINTENANCE')
                            >
                                MEKANIK / MAINTENANCE
                            </option>

                        </select>

                    </div>

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
                                    @selected(old('area') === $area->nama_area)
                                >
                                    {{ $area->nama_area }}
                                </option>

                            @endforeach

                        </select>

                        <div class="field-info">
                            Opsional. Pilih jika pekerjaan berkaitan dengan Area / Line tertentu.
                        </div>

                    </div>

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
                            Opsional. Jika Area dipilih, daftar mesin akan mengikuti Area tersebut.
                        </div>

                    </div>

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
                                @selected(old('priority') === 'NORMAL')
                            >
                                NORMAL
                            </option>

                            <option
                                value="URGENT"
                                @selected(old('priority') === 'URGENT')
                            >
                                URGENT
                            </option>

                            <option
                                value="EMERGENCY"
                                @selected(old('priority') === 'EMERGENCY')
                            >
                                EMERGENCY
                            </option>

                        </select>

                    </div>

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

                </div>

            </div>

            <div class="wo-create-footer">

                <a
                    href="{{ route('work-orders.admin.index') }}"
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
document.addEventListener('DOMContentLoaded', function () {

    const areaSelect =
        document.getElementById('area');

    const mesinSelect =
        document.getElementById('mesin');

    const form =
        document.getElementById('formWorkOrder');

    const submitButton =
        document.getElementById('btnSubmitWO');

    const confirmModal =
        document.getElementById('confirmWOModal');

    const machines =
        @json($machines);

    const oldMesin =
        @json(old('mesin'));

    function loadMachines() {

        if (!areaSelect || !mesinSelect) {
            return;
        }

        const selectedOption =
            areaSelect.options[areaSelect.selectedIndex];

        const areaId =
            selectedOption
                ? selectedOption.getAttribute('data-area-id')
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
            machines.filter(function (machine) {

                return String(machine.area_id) ===
                    String(areaId);

            });

        if (filteredMachines.length === 0) {

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

        mesinSelect.appendChild(defaultOption);

        filteredMachines.forEach(function (machine) {

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
                oldMesin === machine.nama_mesin
            ) {
                option.selected = true;
            }

            mesinSelect.appendChild(option);

        });
    }

    if (areaSelect) {

        areaSelect.addEventListener(
            'change',
            function () {
                loadMachines();
            }
        );

    }

    loadMachines();

    if (submitButton) {

        submitButton.addEventListener(
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

    document.addEventListener(
        'keydown',
        function (event) {

            if (
                event.key === 'Escape' &&
                confirmModal &&
                confirmModal.classList.contains('active')
            ) {

                tutupKonfirmasiWO();

            }

        }
    );

    if (form && areaSelect) {

        form.addEventListener(
            'reset',
            function () {

                setTimeout(function () {

                    const mesinSelect =
                        document.getElementById('mesin');

                    if (!mesinSelect) {
                        return;
                    }

                    mesinSelect.disabled = true;

                    mesinSelect.innerHTML = `
                        <option value="">
                            -- Pilih Area Terlebih Dahulu --
                        </option>
                    `;

                }, 0);

            }
        );

    }

});

function bukaKonfirmasiWO() {

    const modal =
        document.getElementById('confirmWOModal');

    if (!modal) {
        return;
    }

    modal.classList.add('active');

    document.body.style.overflow = 'hidden';
}

function tutupKonfirmasiWO() {

    const modal =
        document.getElementById('confirmWOModal');

    const button =
        document.getElementById('btnConfirmSubmit');

    const cancelButton =
        document.getElementById('btnCancelConfirm');

    if (!modal) {
        return;
    }

    modal.classList.remove('active');

    document.body.style.overflow = '';

    if (button) {

        button.disabled = false;

        button.textContent =
            'Ya, Simpan & Kirim';

    }

    if (cancelButton) {

        cancelButton.disabled = false;

    }

}

function klikOverlayKonfirmasi(event) {

    const modal =
        document.getElementById('confirmWOModal');

    if (
        modal &&
        event.target === modal
    ) {

        tutupKonfirmasiWO();

    }
}

function konfirmasiSimpanWO() {

    const form =
        document.getElementById('formWorkOrder');

    const button =
        document.getElementById('btnConfirmSubmit');

    const cancelButton =
        document.getElementById('btnCancelConfirm');

    const modal =
        document.getElementById('confirmWOModal');

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

    if (modal) {

        modal.classList.remove('active');

        document.body.style.overflow = '';

    }

    form.requestSubmit();
}
</script>