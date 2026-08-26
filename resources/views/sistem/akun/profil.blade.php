<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Profil Saya</title>

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
            max-width: 800px;
            margin: 40px auto;
            padding: 20px;
        }

        .card {
            background: var(--pds-card);
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 3px 12px rgba(0, 0, 0, 0.06);
        }

        h1 {
            font-size: 26px;
            margin-bottom: 6px;
        }

        .subtitle {
            color: var(--pds-muted);
            font-size: 14px;
            margin-bottom: 25px;
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

        .profile-header {
            display: flex;
            align-items: center;
            gap: 20px;
            padding-bottom: 25px;
            margin-bottom: 25px;
            border-bottom: 1px solid var(--pds-line);
        }

        .profile-photo {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            overflow: hidden;
            background: var(--pds-line);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .profile-photo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .profile-letter {
            font-size: 36px;
            font-weight: bold;
            color: var(--pds-muted);
        }

        .profile-name {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .profile-role {
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

        input {
            width: 100%;
            padding: 11px 12px;
            border: 1px solid var(--pds-line-2);
            border-radius: 8px;
            font-size: 14px;
        }

        input:focus {
            outline: none;
            border-color: #2563eb;
        }

        .readonly {
            background: var(--pds-soft-2);
            color: var(--pds-muted);
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

            .profile-header {
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

        <h1>Profil Saya</h1>

        <p class="subtitle">
            Kelola informasi akun Anda
        </p>


        @if(session('success'))

            <div class="message success">
                {{ session('success') }}
            </div>

        @endif


        @if($errors->any())

            <div class="message error">
                {{ $errors->first() }}
            </div>

        @endif


        <div class="profile-header">

            <div class="profile-photo">

                @if(auth()->user()->foto)

                    <img
                        src="{{ asset('storage/' . auth()->user()->foto) }}"
                        alt="Foto Profil"
                    >

                @else

                    <span class="profile-letter">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </span>

                @endif

            </div>


            <div>

                <div class="profile-name">
                    {{ auth()->user()->name }}
                </div>

                <div class="profile-role">
                    {{ auth()->user()->bagian }}
                </div>

            </div>

        </div>


        <form
            action="{{ route('profile.update') }}"
            method="POST"
        >

            @csrf
            @method('PUT')


            <div class="form-group">

                <label for="name">
                    Nama
                </label>

                <input
                    type="text"
                    id="name"
                    name="name"
                    value="{{ auth()->user()->name }}"
                    required
                >

            </div>


            <div class="form-group">

                <label for="bagian">
                    Bagian
                </label>

                <input
                    type="text"
                    id="bagian"
                    value="{{ auth()->user()->bagian }}"
                    class="readonly"
                    readonly
                >

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
                    Simpan Perubahan
                </button>

            </div>

        </form>

    </div>

</div>

</body>

</html>