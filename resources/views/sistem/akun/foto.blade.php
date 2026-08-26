<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Ubah Foto Profil</title>


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
            max-width: 650px;

            margin: 40px auto;

            padding: 20px;
        }


        .card {
            background: var(--pds-card);

            border-radius: 12px;

            padding: 30px;

            box-shadow:
                0 3px 12px rgba(0, 0, 0, 0.06);
        }


        .header {
            margin-bottom: 25px;
        }


        .header h1 {
            font-size: 26px;

            margin-bottom: 6px;
        }


        .header p {
            color: var(--pds-muted);

            font-size: 14px;
        }


        .message {
            padding: 12px 14px;

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


        .profile {
            display: flex;

            align-items: center;

            gap: 20px;

            padding-bottom: 25px;

            margin-bottom: 25px;

            border-bottom:
                1px solid var(--pds-line);
        }


        .photo-preview {
    width: 130px;
    height: 130px;
    min-width: 130px;
    min-height: 130px;

    border-radius: 50%;
    overflow: hidden;

    background: var(--pds-line);

    display: flex;
    align-items: center;
    justify-content: center;

    border: 3px solid var(--pds-line);
}

.photo-preview img {
    width: 100%;
    height: 100%;

    display: block;

    object-fit: cover;
    object-position: center center;

    image-rendering: auto;
}

        .photo-letter {
            font-size: 42px;

            font-weight: bold;

            color: var(--pds-muted);
        }


        .user-name {
            font-size: 20px;

            font-weight: bold;

            margin-bottom: 5px;
        }


        .user-role {
            font-size: 14px;

            color: var(--pds-muted);
        }


        .form-group {
            margin-bottom: 20px;
        }


        label {
            display: block;

            margin-bottom: 8px;

            font-size: 14px;

            font-weight: bold;
        }


        input[type="file"] {
            width: 100%;

            padding: 10px;

            border:
                1px solid var(--pds-line-2);

            border-radius: 8px;

            background: var(--pds-card);
        }


        input[type="file"]:focus {
            outline: none;

            border-color: #2563eb;
        }


        .info {
            margin-top: 6px;

            color: var(--pds-muted);

            font-size: 12px;
        }


        .actions {
            display: flex;

            justify-content: flex-end;

            gap: 10px;

            margin-top: 25px;
        }


        .btn {
            display: inline-block;

            padding: 11px 18px;

            border-radius: 8px;

            border: none;

            text-decoration: none;

            cursor: pointer;

            font-size: 14px;
        }


        .btn-back {
            background: var(--pds-line);

            color: var(--pds-ink-2);
        }


        .btn-save {
            background: #2563eb;

            color: #ffffff;
        }


        .btn-save:hover {
            background: #1d4ed8;
        }


        @media (max-width: 600px) {

            .container {
                margin: 20px auto;

                padding: 12px;
            }


            .card {
                padding: 20px;
            }


            .profile {
                flex-direction: column;

                align-items: flex-start;
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


        <div class="header">

            <h1>
                Ubah Foto Profil
            </h1>


            <p>
                Ganti foto profil akun Anda
            </p>

        </div>


        {{-- PESAN SUKSES --}}

        @if(session('success'))

            <div class="message success">

                {{ session('success') }}

            </div>

        @endif


        {{-- PESAN ERROR --}}

        @if($errors->any())

            <div class="message error">

                {{ $errors->first() }}

            </div>

        @endif


        <!-- =================================================
             PREVIEW FOTO
        ================================================== -->

        <div class="profile">


            <div class="photo-preview">


                @if(auth()->user()->foto)

                    <img
                        id="previewFoto"
                        src="{{ asset('storage/' . auth()->user()->foto) }}"
                        alt="Foto Profil"
                    >

                    <span
                        id="previewLetter"
                        class="photo-letter"
                        style="display:none;"
                    >
                    </span>

                @else

                    <img
                        id="previewFoto"
                        src=""
                        alt="Preview Foto"
                        style="display:none;"
                    >

                    <span
                        id="previewLetter"
                        class="photo-letter"
                    >
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </span>

                @endif


            </div>


            <div>


                <div class="user-name">

                    {{ auth()->user()->name }}

                </div>


                <div class="user-role">

                    {{ auth()->user()->bagian }}

                </div>


            </div>


        </div>


        <!-- =================================================
             FORM
        ================================================== -->

       <form
    action="{{ route('profile.photo.update') }}"
    method="POST"
    enctype="multipart/form-data"
>

    @csrf

    <div class="form-group">

        <label for="foto">
            Pilih Foto Profil
        </label>

        <input
            type="file"
            id="foto"
            name="foto"
            accept="image/*"
            required
        >

        <div class="info">
            Pilih satu foto. Maksimal 5 MB.
        </div>

    </div>

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
            Simpan Foto
        </button>

    </div>

</form>

    </div>


</div>


<script>

    const inputFoto =
        document.getElementById('foto');

    const previewFoto =
        document.getElementById('previewFoto');

    const previewLetter =
        document.getElementById('previewLetter');


    inputFoto.addEventListener(
        'change',
        function () {

            const file =
                this.files[0];


            if (!file) {
                return;
            }


            // Pastikan file berupa gambar

            if (!file.type.startsWith('image/')) {

                alert(
                    'File yang dipilih harus berupa gambar.'
                );

                this.value = '';

                return;
            }


            // Maksimal 5 MB

            if (file.size > 5 * 1024 * 1024) {

                alert(
                    'Ukuran foto maksimal 5 MB.'
                );

                this.value = '';

                return;
            }


            const reader =
                new FileReader();


            reader.onload =
                function (event) {

                    previewFoto.src =
                        event.target.result;


                    previewFoto.style.display =
                        'block';


                    if (previewLetter) {

                        previewLetter.style.display =
                            'none';

                    }

                };


            reader.readAsDataURL(file);

        }
    );

</script>


</body>

</html>