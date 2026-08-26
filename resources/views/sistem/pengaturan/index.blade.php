<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Pengaturan Sistem</title>


    <style>

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }


        body {
            font-family: Arial, sans-serif;

            background: var(--pds-soft);

            color: var(--pds-ink);
        }


        .container {
            width: 100%;

            max-width: 1000px;

            margin: 35px auto;

            padding: 20px;
        }


        .card {
            background: var(--pds-card);

            border-radius: 14px;

            padding: 30px;

            margin-bottom: 20px;

            box-shadow:
                0 4px 15px rgba(0, 0, 0, 0.06);
        }


        .header {
            margin-bottom: 28px;
        }


        .header h1 {
            font-size: 26px;

            margin-bottom: 7px;
        }


        .header p {
            color: var(--pds-muted);

            font-size: 14px;
        }


        /* =====================================================
           MESSAGE
        ===================================================== */

        .message {
            padding: 13px 15px;

            border-radius: 8px;

            margin-bottom: 20px;

            font-size: 14px;
        }


        .success {
            background: #ecfdf5;

            border: 1px solid #a7f3d0;

            color: #047857;
        }


        .error {
            background: #fef2f2;

            border: 1px solid #fecaca;

            color: #b91c1c;
        }


        /* =====================================================
           SETTING GROUP
        ===================================================== */

        .setting-group {
            padding-bottom: 28px;

            margin-bottom: 28px;

            border-bottom:
                1px solid var(--pds-line);
        }


        .setting-group:last-child {
            border-bottom: none;

            padding-bottom: 0;

            margin-bottom: 0;
        }


        .setting-title {
            font-size: 17px;

            font-weight: bold;

            margin-bottom: 6px;
        }


        .setting-description {
            color: var(--pds-muted);

            font-size: 13px;

            margin-bottom: 15px;

            line-height: 1.5;
        }


        label {
            display: block;

            font-size: 14px;

            font-weight: bold;

            margin-bottom: 8px;
        }


        input[type="text"],
        input[type="file"] {
            width: 100%;

            padding: 11px 12px;

            border:
                1px solid var(--pds-line-2);

            border-radius: 8px;

            background: var(--pds-card);

            font-size: 14px;
        }


        input:focus {
            outline: none;

            border-color: #2563eb;
        }


        /* =====================================================
           PREVIEW
        ===================================================== */

        .preview-title {
            margin-top: 18px;

            margin-bottom: 8px;

            font-size: 13px;

            font-weight: bold;

            color: var(--pds-ink-2);
        }


        .preview {
            width: 100%;

            min-height: 130px;

            margin-top: 10px;

            border:
                1px dashed var(--pds-line-2);

            border-radius: 10px;

            background: var(--pds-soft);

            display: flex;

            align-items: center;

            justify-content: center;

            padding: 20px;

            overflow: hidden;
        }


        .preview img {
            max-width: 100%;

            max-height: 150px;

            object-fit: contain;

            display: block;
        }


        .preview-text {
            color: var(--pds-muted-2);

            font-size: 13px;

            text-align: center;
        }


        .background-preview {
            min-height: 220px;

            background-size: cover;

            background-position: center;

            background-repeat: no-repeat;
        }


        /* =====================================================
           BUTTON
        ===================================================== */

        .actions {
            display: flex;

            justify-content: flex-end;

            gap: 10px;

            margin-top: 28px;
        }


        .btn {
            display: inline-block;

            padding: 11px 18px;

            border-radius: 8px;

            border: none;

            font-size: 14px;

            cursor: pointer;

            text-decoration: none;
        }


        .btn-back {
            background: var(--pds-line);

            color: var(--pds-ink-2);
        }


        .btn-save {
            background: #2563eb;

            color: white;
        }


        .btn-save:hover {
            background: #1d4ed8;
        }


        /* =====================================================
           MOBILE
        ===================================================== */

        @media (max-width: 650px) {

            .container {
                margin: 20px auto;

                padding: 12px;
            }


            .card {
                padding: 20px;
            }


            .actions {
                flex-direction: column-reverse;
            }


            .btn {
                width: 100%;

                text-align: center;
            }

        }

    </style>

</head>


<body>


<div class="container">


    <div class="card">


        <!-- =================================================
             HEADER
        ================================================== -->

        <div class="header">

            <h1>
                Pengaturan Sistem
            </h1>

            <p>
                Atur nama sistem, logo dashboard,
                logo login, dan background login.
            </p>

        </div>


        <!-- =================================================
             SUCCESS
        ================================================== -->

        @if(session('success'))

            <div class="message success">

                {{ session('success') }}

            </div>

        @endif


        <!-- =================================================
             ERROR
        ================================================== -->

        @if($errors->any())

            <div class="message error">

                {{ $errors->first() }}

            </div>

        @endif


        <!-- =================================================
             FORM
        ================================================== -->

        <form
            action="{{ route('settings.update') }}"
            method="POST"
            enctype="multipart/form-data"
        >

            @csrf

            @method('PUT')


            <!-- =================================================
                 NAMA SISTEM
            ================================================== -->

            <div class="setting-group">


                <div class="setting-title">
                    Nama Sistem
                </div>


                <div class="setting-description">

                    Nama ini digunakan pada Dashboard
                    dan Login.

                </div>


                <label for="system_name">
                    Nama Sistem
                </label>


                <input
                    type="text"
                    id="system_name"
                    name="system_name"
                    value="{{ $settings['system_name'] ?? 'PACHIRA MAINTENANCE SYSTEM' }}"
                    required
                >


            </div>


            <!-- =================================================
                 LOGO DASHBOARD
            ================================================== -->

            <div class="setting-group">


                <div class="setting-title">
                    Logo Dashboard
                </div>


                <div class="setting-description">

                    Logo yang akan digunakan pada
                    Dashboard PACHIRA MAINTENANCE SYSTEM.

                </div>


                <label for="logo_dashboard">
                    Pilih Logo Dashboard
                </label>


                <input
                    type="file"
                    id="logo_dashboard"
                    name="logo_dashboard"
                    accept="image/*"
                >


                <div class="preview-title">
                    Preview Logo Dashboard
                </div>


                <div
                    id="dashboardPreview"
                    class="preview"
                >

                    @if(!empty($settings['logo_dashboard']))

                        <img
                            src="{{ asset('storage/' . $settings['logo_dashboard']) }}?v={{ time() }}"
                            alt="Logo Dashboard"
                        >

                    @else

                        <span class="preview-text">

                            Belum ada Logo Dashboard

                        </span>

                    @endif

                </div>


            </div>


            <!-- =================================================
                 LOGO LOGIN
            ================================================== -->

            <div class="setting-group">


                <div class="setting-title">
                    Logo Login
                </div>


                <div class="setting-description">

                    Logo yang akan muncul pada
                    halaman Login.

                </div>


                <label for="logo_login">
                    Pilih Logo Login
                </label>


                <input
                    type="file"
                    id="logo_login"
                    name="logo_login"
                    accept="image/*"
                >


                <div class="preview-title">
                    Preview Logo Login
                </div>


                <div
                    id="loginPreview"
                    class="preview"
                >

                    @if(!empty($settings['logo_login']))

                        <img
                            src="{{ asset('storage/' . $settings['logo_login']) }}?v={{ time() }}"
                            alt="Logo Login"
                        >

                    @else

                        <span class="preview-text">

                            Belum ada Logo Login

                        </span>

                    @endif

                </div>


            </div>


            <!-- =================================================
                 BACKGROUND LOGIN
            ================================================== -->

            <div class="setting-group">


                <div class="setting-title">
                    Background Login
                </div>


                <div class="setting-description">

                    Background yang akan digunakan
                    pada halaman Login.

                </div>


                <label for="background_login">
                    Pilih Background Login
                </label>


                <input
                    type="file"
                    id="background_login"
                    name="background_login"
                    accept="image/*"
                >


                <div class="preview-title">
                    Preview Background Login
                </div>


                <div
                    id="backgroundPreview"
                    class="preview background-preview"

                    @if(!empty($settings['background_login']))

                        style="
                            background-image:
                                url('{{ asset('storage/' . $settings['background_login']) }}?v={{ time() }}');
                        "

                    @endif
                >

                    @if(empty($settings['background_login']))

                        <span class="preview-text">

                            Belum ada Background Login

                        </span>

                    @endif

                </div>


            </div>


            <!-- =================================================
                 BUTTON
            ================================================== -->

            <div class="actions">


                <a
                    href="{{ route('dashboard') }}"
                    class="btn btn-back"
                >

                    Kembali

                </a>


                <button
                    type="submit"
                    class="btn btn-save"
                >

                    Simpan Pengaturan

                </button>


            </div>


        </form>


    </div>


</div>


<script>


    /* =====================================================
       VALIDASI DAN PREVIEW GAMBAR
    ===================================================== */

    function previewImage(
        input,
        previewElement,
        type
    ) {

        const file =
            input.files[0];


        if (!file) {
            return;
        }


        /* ===============================
           CEK GAMBAR
        =============================== */

        if (!file.type.startsWith('image/')) {

            alert(
                'File yang dipilih harus berupa gambar.'
            );

            input.value = '';

            return;
        }


        /* ===============================
           BATAS UKURAN
        =============================== */

        const maxSize =
            type === 'background'
                ? 10 * 1024 * 1024
                : 5 * 1024 * 1024;


        if (file.size > maxSize) {

            alert(
                type === 'background'
                    ? 'Background maksimal 10 MB.'
                    : 'Logo maksimal 5 MB.'
            );

            input.value = '';

            return;
        }


        /* ===============================
           BACA FILE
        =============================== */

        const reader =
            new FileReader();


        reader.onload =
            function(event) {


                /* ===============================
                   BACKGROUND
                =============================== */

                if (type === 'background') {

                    previewElement.style.backgroundImage =
                        `url("${event.target.result}")`;


                    previewElement.innerHTML = '';

                    return;
                }


                /* ===============================
                   LOGO
                =============================== */

                previewElement.innerHTML = '';


                const image =
                    document.createElement('img');


                image.src =
                    event.target.result;


                image.alt =
                    'Preview';


                previewElement.appendChild(image);

            };


        reader.readAsDataURL(file);

    }


    /* =====================================================
       LOGO DASHBOARD
    ===================================================== */

    document
        .getElementById('logo_dashboard')
        .addEventListener(
            'change',
            function () {

                previewImage(
                    this,
                    document.getElementById(
                        'dashboardPreview'
                    ),
                    'logo'
                );

            }
        );


    /* =====================================================
       LOGO LOGIN
    ===================================================== */

    document
        .getElementById('logo_login')
        .addEventListener(
            'change',
            function () {

                previewImage(
                    this,
                    document.getElementById(
                        'loginPreview'
                    ),
                    'logo'
                );

            }
        );


    /* =====================================================
       BACKGROUND LOGIN
    ===================================================== */

    document
        .getElementById('background_login')
        .addEventListener(
            'change',
            function () {

                previewImage(
                    this,
                    document.getElementById(
                        'backgroundPreview'
                    ),
                    'background'
                );

            }
        );


</script>


</body>

</html>