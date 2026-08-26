<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        PACHIRA DISTRINUSA - {{ $settings['system_name'] ?? 'PACHIRA MAINTENANCE SYSTEM' }}
    </title>

    <link
        rel="icon"
        type="image/x-icon"
        href="{{ asset('favicon.ico') }}"
    >

    <style>

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html,
        body {
            width: 100%;
            min-height: 100%;
        }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 15px;
            font-family:
                "Segoe UI",
                Arial,
                sans-serif;
            color: #ffffff;
            background-color: #111827;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            position: relative;
            overflow-x: hidden;
        }

        /* =====================================================
           BACKGROUND
        ===================================================== */

        .background-overlay {
            position: fixed;
            inset: 0;
            background:
                linear-gradient(
                    135deg,
                    rgba(8, 15, 30, 0.72),
                    rgba(15, 23, 42, 0.42),
                    rgba(8, 15, 30, 0.76)
                );
            z-index: 0;
        }

        .background-glow {
            position: fixed;
            width: 360px;
            height: 360px;
            border-radius: 50%;
            background:
                radial-gradient(
                    circle,
                    rgba(59, 130, 246, 0.16),
                    rgba(59, 130, 246, 0)
                );
            top: -130px;
            right: -100px;
            z-index: 0;
            pointer-events: none;
        }

        .background-glow-2 {
            position: fixed;
            width: 330px;
            height: 330px;
            border-radius: 50%;
            background:
                radial-gradient(
                    circle,
                    rgba(14, 165, 233, 0.12),
                    rgba(14, 165, 233, 0)
                );
            bottom: -130px;
            left: -100px;
            z-index: 0;
            pointer-events: none;
        }

        /* =====================================================
           LOGIN WRAPPER
        ===================================================== */

        .login-wrapper {
            position: relative;
            z-index: 2;
            width: 100%;
            max-width: 400px;
        }

        /* =====================================================
           GLASS CARD
        ===================================================== */

        .login-card {
            width: 100%;
            padding: 25px 28px;
            border:
                1px solid rgba(255, 255, 255, 0.22);
            border-radius: 18px;
            background:
                rgba(255, 255, 255, 0.12);
            backdrop-filter:
                blur(18px);
            -webkit-backdrop-filter:
                blur(18px);
            box-shadow:
                0 20px 55px
                rgba(0, 0, 0, 0.35),
                inset 0 1px 0
                rgba(255, 255, 255, 0.12);
            animation:
                cardEnter 0.55s ease;
        }

        @keyframes cardEnter {
            from {
                opacity: 0;
                transform:
                    translateY(18px)
                    scale(0.98);
            }

            to {
                opacity: 1;
                transform:
                    translateY(0)
                    scale(1);
            }
        }

        /* =====================================================
           LOGO
        ===================================================== */

        .logo-area {
            text-align: center;
            margin-bottom: 20px;
        }

        .logo-image {
            width: 75px;
            height: 75px;
            object-fit: contain;
            display: block;
            margin:
                0 auto 10px;
            filter:
                drop-shadow(
                    0 7px 16px
                    rgba(0, 0, 0, 0.25)
                );
        }

        .logo-placeholder {
            width: 75px;
            height: 75px;
            margin:
                0 auto 10px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            background:
                rgba(255, 255, 255, 0.10);
            border:
                1px solid
                rgba(255, 255, 255, 0.18);
            color:
                rgba(255, 255, 255, 0.70);
            font-size: 10px;
            letter-spacing: 1px;
        }

        .logo-area h1 {
            font-size: 21px;
            font-weight: 800;
            letter-spacing: 2.5px;
            color: #ffffff;
            margin-bottom: 4px;
        }

        .brand-subtitle {
            font-size: 12.5px;
            font-weight: 600;
            letter-spacing: 1.6px;
            color: #93c5fd;
        }

        .login-hint {
            margin-top: 10px;
            font-size: 12px;
            color:
                rgba(255, 255, 255, 0.70);
        }

        .logo-area p {
            font-size: 12px;
            color:
                rgba(255, 255, 255, 0.70);
        }

        /* =====================================================
           ERROR
        ===================================================== */

        .error-box {
            margin-bottom: 14px;
            padding: 10px 12px;
            border-radius: 9px;
            background:
                rgba(239, 68, 68, 0.16);
            border:
                1px solid
                rgba(252, 165, 165, 0.28);
            color: #fee2e2;
            font-size: 12px;
        }

        /* =====================================================
           FORM
        ===================================================== */

        .form-group {
            margin-bottom: 14px;
        }

        .form-group label {
            display: block;
            margin-bottom: 6px;
            font-size: 12px;
            font-weight: 600;
            color:
                rgba(255, 255, 255, 0.90);
        }

        select,
        input {
            width: 100%;
            min-height: 42px;
            padding:
                9px 12px;
            border:
                1px solid
                rgba(255, 255, 255, 0.18);
            border-radius: 9px;
            outline: none;
            background:
                rgba(255, 255, 255, 0.10);
            color: #ffffff;
            font-size: 13px;
            transition:
                0.2s ease;
        }

        select {
            cursor: pointer;
        }

        select option {
            color: #111827;
            background: #ffffff;
        }

        input::placeholder {
            color:
                rgba(255, 255, 255, 0.48);
        }

        select:focus,
        input:focus {
            border-color:
                rgba(147, 197, 253, 0.75);
            background:
                rgba(255, 255, 255, 0.14);
            box-shadow:
                0 0 0 3px
                rgba(59, 130, 246, 0.12);
        }

        select:disabled {
            cursor: not-allowed;
            color:
                rgba(255, 255, 255, 0.45);
            background:
                rgba(255, 255, 255, 0.06);
        }

        /* =====================================================
           FIELD LOADING
        ===================================================== */

        .field-loading {
            display: none;
            margin-top: 6px;
            font-size: 11px;
            color:
                rgba(255, 255, 255, 0.65);
            align-items: center;
            gap: 6px;
        }

        .field-loading.show {
            display: flex;
        }

        .small-spinner {
            width: 12px;
            height: 12px;
            border:
                2px solid
                rgba(255, 255, 255, 0.22);
            border-top-color:
                rgba(255, 255, 255, 0.90);
            border-radius: 50%;
            animation:
                spin 0.8s linear infinite;
        }

        /* =====================================================
           BUTTON
        ===================================================== */

        .btn-login {
            width: 100%;
            min-height: 44px;
            margin-top: 3px;
            border: none;
            border-radius: 9px;
            background:
                linear-gradient(
                    135deg,
                    #2563eb,
                    #1d4ed8
                );
            color: #ffffff;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 9px;
            box-shadow:
                0 8px 20px
                rgba(37, 99, 235, 0.28);
            transition:
                transform 0.15s ease,
                box-shadow 0.15s ease,
                opacity 0.15s ease;
        }

        .btn-login:hover {
            transform:
                translateY(-1px);
            box-shadow:
                0 11px 24px
                rgba(37, 99, 235, 0.34);
        }

        .btn-login:active {
            transform:
                translateY(0);
        }

        .btn-login:disabled {
            cursor: not-allowed;
            opacity: 0.78;
        }

        /* =====================================================
           LOGIN SPINNER
        ===================================================== */

        .spinner {
            width: 16px;
            height: 16px;
            border:
                2px solid
                rgba(255, 255, 255, 0.35);
            border-top-color:
                #ffffff;
            border-radius: 50%;
            animation:
                spin 0.8s linear infinite;
            display: none;
        }

        .spinner.show {
            display: inline-block;
        }

        @keyframes spin {
            to {
                transform:
                    rotate(360deg);
            }
        }

        /* =====================================================
           FOOTER
        ===================================================== */

        .login-footer {
            margin-top: 12px;
            text-align: center;
        }

        .login-footer p {
            font-size: 10px;
            color:
                rgba(255, 255, 255, 0.48);
            letter-spacing: 0.3px;
        }

        /* =====================================================
           MOBILE
        ===================================================== */

        @media (max-width: 540px) {

            body {
                padding: 12px;
            }

            .login-wrapper {
                max-width: 370px;
            }

            .login-card {
                padding: 22px 18px;
                border-radius: 16px;
            }

            .logo-image,
            .logo-placeholder {
                width: 68px;
                height: 68px;
            }

            .logo-area h1 {
                font-size: 19px;
            }

            .logo-area p {
                font-size: 11px;
            }

            select,
            input {
                min-height: 40px;
                font-size: 12px;
            }

            .btn-login {
                min-height: 42px;
            }
        }

    </style>

</head>

<body
    @if(!empty($settings['background_login']))
        style="
            background-image:
                url('{{ asset('storage/' . $settings['background_login']) }}?v={{ time() }}');
        "
    @endif
>

<div class="background-overlay"></div>
<div class="background-glow"></div>
<div class="background-glow-2"></div>

<div class="login-wrapper">

    <div class="login-card">

        <!-- =================================================
             LOGO LOGIN
        ================================================== -->

        <div class="logo-area">

            @if(!empty($settings['logo_login']))

                <img
                    src="{{ asset('storage/' . $settings['logo_login']) }}?v={{ time() }}"
                    alt="Logo Login"
                    class="logo-image"
                >

            @else

                <div class="logo-placeholder">
                    LOGO
                </div>

            @endif

            <h1>
                PACHIRA DISTRINUSA
            </h1>

            <p class="brand-subtitle">
                {{ $settings['system_name'] ?? 'PACHIRA MAINTENANCE SYSTEM' }}
            </p>

            <p class="login-hint">
                Silakan masuk ke sistem
            </p>

        </div>

        <!-- =================================================
             ERROR
        ================================================== -->

        @if($errors->any())

            <div class="error-box">
                {{ $errors->first() }}
            </div>

        @endif

        <!-- =================================================
             FORM LOGIN
        ================================================== -->

        <form
            id="loginForm"
            action="{{ route('login.process') }}"
            method="POST"
        >

            @csrf

            <!-- BAGIAN -->

            <div class="form-group">

                <label for="bagian">
                    Bagian
                </label>

                <select
                    id="bagian"
                    name="bagian"
                    required
                >

                    <option value="">
                        -- Pilih Bagian --
                    </option>

                    @foreach($bagians as $bagian)

                        <option value="{{ $bagian }}">
                            {{ $bagian }}
                        </option>

                    @endforeach

                </select>

                <div
                    id="userLoading"
                    class="field-loading"
                >

                    <span class="small-spinner"></span>

                    <span>
                        Memuat nama user...
                    </span>

                </div>

            </div>

            <!-- NAMA -->

            <div class="form-group">

                <label for="user_id">
                    Nama
                </label>

                <select
                    id="user_id"
                    name="user_id"
                    required
                    disabled
                >

                    <option value="">
                        -- Pilih Bagian Terlebih Dahulu --
                    </option>

                </select>

            </div>

            <!-- PASSWORD -->

            <div class="form-group">

                <label for="password">
                    Password
                </label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Masukkan password"
                    required
                >

            </div>

            <!-- MASUK -->

            <button
                type="submit"
                id="loginButton"
                class="btn-login"
            >

                <span
                    id="loginSpinner"
                    class="spinner"
                ></span>

                <span id="loginText">
                    MASUK
                </span>

            </button>

        </form>

        <!-- =================================================
             FOOTER
        ================================================== -->

        <div class="login-footer">

            <p>
                @pachira_distrinusa
            </p>

        </div>

    </div>

</div>

<script>

    const bagianSelect =
        document.getElementById('bagian');

    const userSelect =
        document.getElementById('user_id');

    const userLoading =
        document.getElementById('userLoading');

    const loginForm =
        document.getElementById('loginForm');

    const loginButton =
        document.getElementById('loginButton');

    const loginSpinner =
        document.getElementById('loginSpinner');

    const loginText =
        document.getElementById('loginText');


    /* =====================================================
       PILIH BAGIAN
    ===================================================== */

    bagianSelect.addEventListener(
        'change',
        async function ()
        {

            const bagian =
                this.value;

            userSelect.innerHTML = `
                <option value="">
                    -- Memuat Nama --
                </option>
            `;

            userSelect.disabled = true;

            if (!bagian) {

                userSelect.innerHTML = `
                    <option value="">
                        -- Pilih Bagian Terlebih Dahulu --
                    </option>
                `;

                return;
            }

            userLoading.classList.add('show');

            try {

                /*
                 * PENTING:
                 * Gunakan origin browser saat ini.
                 * Ini membuat request tetap menuju domain
                 * yang sedang dibuka, termasuk Cloudflare HTTPS.
                 */

                const url =
                    `${window.location.origin}/login/users?bagian=${encodeURIComponent(bagian)}`;

                const response =
                    await fetch(
                        url,
                        {
                            method: 'GET',
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            credentials: 'same-origin',
                            cache: 'no-store'
                        }
                    );

                if (!response.ok) {

                    throw new Error(
                        `HTTP ${response.status}`
                    );

                }

                const users =
                    await response.json();

                userSelect.innerHTML = `
                    <option value="">
                        -- Pilih Nama --
                    </option>
                `;

                users.forEach(
                    function (user)
                    {

                        const option =
                            document.createElement(
                                'option'
                            );

                        option.value =
                            user.id;

                        option.textContent =
                            user.name;

                        userSelect.appendChild(
                            option
                        );

                    }
                );

                if (users.length > 0) {

                    userSelect.disabled = false;

                } else {

                    userSelect.innerHTML = `
                        <option value="">
                            -- Belum Ada User --
                        </option>
                    `;

                }

            } catch (error) {

                console.error(
                    'Gagal memuat user:',
                    error
                );

                userSelect.innerHTML = `
                    <option value="">
                        -- Gagal Memuat Nama --
                    </option>
                `;

            } finally {

                userLoading.classList.remove('show');

            }

        }
    );


    /* =====================================================
       LOADING LOGIN
    ===================================================== */

    loginForm.addEventListener(
        'submit',
        function ()
        {

            if (!loginForm.checkValidity()) {
                return;
            }

            loginButton.disabled = true;

            loginSpinner.classList.add('show');

            loginText.textContent =
                'MEMPROSES...';

        }
    );

</script>

</body>

</html>