<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Ubah Password</title>

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
            max-width: 600px;
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

        .user-box {
            padding: 14px;
            margin-bottom: 25px;
            background: var(--pds-soft);
            border-radius: 8px;
        }

        .user-name {
            font-weight: bold;
            margin-bottom: 4px;
        }

        .user-role {
            color: var(--pds-muted);
            font-size: 13px;
        }

        .group {
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

        <h1>Ubah Password</h1>

        <p class="subtitle">
            Ubah password akun Anda
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

        <div class="user-box">

            <div class="user-name">
                {{ auth()->user()->name }}
            </div>

            <div class="user-role">
                {{ auth()->user()->bagian }}
            </div>

        </div>

        <form
            action="{{ route('password.update') }}"
            method="POST"
        >

            @csrf
            @method('PUT')

            <div class="group">

                <label for="current_password">
                    Password Lama
                </label>

                <input
                    type="password"
                    id="current_password"
                    name="current_password"
                    required
                >

            </div>

            <div class="group">

                <label for="password">
                    Password Baru
                </label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    required
                    minlength="6"
                >

            </div>

            <div class="group">

                <label for="password_confirmation">
                    Konfirmasi Password Baru
                </label>

                <input
                    type="password"
                    id="password_confirmation"
                    name="password_confirmation"
                    required
                    minlength="6"
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
                    Simpan Password
                </button>

            </div>

        </form>

    </div>

</div>

</body>

</html>