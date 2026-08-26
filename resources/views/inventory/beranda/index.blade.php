<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Inventory -
        {{ $settings['system_name'] ?? 'PACHIRA MAINTENANCE SYSTEM' }}
    </title>

    <style>

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {

            font-family:
                Arial,
                Helvetica,
                sans-serif;

            background:
                var(--pds-soft);

            color:
                var(--pds-ink);
        }

        a {
            text-decoration: none;
            color: inherit;
        }


        /* =====================================================
           TOPBAR
        ===================================================== */

        .topbar {

            min-height:
                76px;

            background:
                var(--pds-card);

            border-bottom:
                1px solid var(--pds-line);

            display:
                flex;

            align-items:
                center;

            justify-content:
                space-between;

            gap:
                15px;

            padding:
                0 32px;
        }


        .brand {

            font-size:
                20px;

            font-weight:
                700;

            color:
                var(--pds-ink);
        }


        .topbar-right {

            display:
                flex;

            align-items:
                center;

            gap:
                10px;
        }


        .back,
        .logout-button {

            min-height:
                38px;

            padding:
                0 14px;

            border-radius:
                8px;

            font-size:
                13px;

            font-weight:
                600;
        }


        .back {

            display:
                inline-flex;

            align-items:
                center;

            justify-content:
                center;

            border:
                1px solid var(--pds-line-2);

            background:
                var(--pds-soft-2);

            color:
                var(--pds-ink-2);
        }


        .back:hover {

            background:
                var(--pds-line);
        }


        .logout-form {

            margin:
                0;
        }


        .logout-button {

            border:
                1px solid #fecaca;

            background:
                var(--pds-card);

            color:
                #b91c1c;

            cursor:
                pointer;
        }


        .logout-button:hover {

            background:
                #fef2f2;
        }


        /* =====================================================
           PAGE
        ===================================================== */

        .page {

            width:
                100%;

            max-width:
                1200px;

            margin:
                0 auto;

            padding:
                40px 28px;
        }


        .page-header {

            margin-bottom:
                28px;
        }


        .page-header h1 {

            font-size:
                28px;

            margin-bottom:
                8px;

            color:
                var(--pds-ink);
        }


        .page-header p {

            color:
                var(--pds-muted);

            font-size:
                14px;
        }


        /* =====================================================
           DUA PILIHAN INVENTORY
        ===================================================== */

        .menu-grid {

            display:
                grid;

            grid-template-columns:
                repeat(
                    2,
                    minmax(
                        300px,
                        1fr
                    )
                );

            gap:
                24px;
        }


        .menu-card {

            text-decoration:
                none;

            color:
                inherit;
        }


        .card {

            background:
                var(--pds-card);

            border:
                1px solid var(--pds-line);

            border-radius:
                18px;

            padding:
                30px;

            min-height:
                250px;

            box-shadow:
                0 4px 18px
                rgba(0,0,0,.05);

            transition:
                .2s ease;
        }


        .card:hover {

            transform:
                translateY(-5px);

            box-shadow:
                0 14px 30px
                rgba(0,0,0,.10);
        }


        .card.me-prev:hover {

            border-color:
                #2563eb;
        }


        .card.prev:hover {

            border-color:
                #16a34a;
        }


        /* =====================================================
           ICON
        ===================================================== */

        .icon {

            width:
                64px;

            height:
                64px;

            border-radius:
                16px;

            display:
                flex;

            align-items:
                center;

            justify-content:
                center;

            font-size:
                27px;

            font-weight:
                bold;

            margin-bottom:
                22px;
        }


        .me-prev .icon {

            background:
                #dbeafe;

            color:
                #2563eb;
        }


        .prev .icon {

            background:
                #dcfce7;

            color:
                #16a34a;
        }


        /* =====================================================
           CARD TEXT
        ===================================================== */

        .card h2 {

            font-size:
                21px;

            margin-bottom:
                10px;

            color:
                var(--pds-ink);
        }


        .card p {

            color:
                var(--pds-muted);

            font-size:
                13px;

            line-height:
                1.6;
        }


        .card-action {

            margin-top:
                24px;

            font-size:
                13px;

            font-weight:
                700;
        }


        .me-prev .card-action {

            color:
                #2563eb;
        }


        .prev .card-action {

            color:
                #16a34a;
        }


        /* =====================================================
           CATATAN
        ===================================================== */

        .inventory-note {

            margin-top:
                22px;

            padding:
                13px 15px;

            border:
                1px solid var(--pds-line);

            border-radius:
                10px;

            background:
                var(--pds-soft);

            color:
                var(--pds-muted);

            font-size:
                12px;

            line-height:
                1.5;
        }


        /* =====================================================
           FOOTER
        ===================================================== */

        .page-footer {

            margin-top:
                24px;
        }


        .back-dashboard {

            display:
                inline-flex;

            align-items:
                center;

            justify-content:
                center;

            min-height:
                38px;

            padding:
                0 14px;

            border:
                1px solid var(--pds-line-2);

            border-radius:
                8px;

            background:
                var(--pds-soft-2);

            color:
                var(--pds-ink-2);

            font-size:
                13px;

            font-weight:
                600;
        }


        .back-dashboard:hover {

            background:
                var(--pds-line);
        }


        /* =====================================================
           RESPONSIVE
        ===================================================== */

        @media (max-width: 750px) {

            .topbar {

                padding:
                    0 18px;

                flex-wrap:
                    wrap;

                min-height:
                    70px;
            }


            .brand {

                font-size:
                    17px;
            }


            .menu-grid {

                grid-template-columns:
                    1fr;
            }


            .page {

                padding:
                    28px 18px;
            }
        }


        @media (max-width: 500px) {

            .topbar-right {

                width:
                    100%;

                padding-bottom:
                    10px;
            }


            .back,
            .logout-button {

                width:
                    100%;
            }


            .topbar {

                height:
                    auto;

                padding-top:
                    14px;
            }
        }

    </style>

</head>


<body>


<!-- =====================================================
     TOPBAR
===================================================== -->

<header class="topbar">

    <div class="brand">

        {{
            $settings['system_name']
            ?? 'PACHIRA MAINTENANCE SYSTEM'
        }}

    </div>


    <div class="topbar-right">

        <a
            href="{{ route('dashboard') }}"
            class="back"
        >
            <x-icon name="back"></x-icon> Kembali
        </a>


        <form
            action="{{ route('logout') }}"
            method="POST"
            class="logout-form"
        >

            @csrf

            <button
                type="submit"
                class="logout-button"
            >
                Keluar
            </button>

        </form>

    </div>

</header>


<!-- =====================================================
     PAGE
===================================================== -->

<main class="page">


    <div class="page-header">

        <h1>
            Inventory
        </h1>

        <p>
            Pilih kelompok inventory yang ingin dikelola.
        </p>

    </div>


    <!-- =====================================================
         PILIHAN ME & PREV / PREV
    ===================================================== -->

    <div class="menu-grid">


        <!-- =================================================
             ME & PREV MAINT
        ================================================= -->

        <a
            href="{{ route('inventory.me_prev') }}"
            class="
                menu-card
                me-prev
            "
        >

            <div class="card">

                <div class="icon">
                    <x-icon name="gear"></x-icon>
                </div>


                <h2>
                    ME & PREV MAINT
                </h2>


                <p>
                    Kelola stok dan transaksi inventory
                    untuk kebutuhan Maintenance Engineering
                    dan Preventive Maintenance.
                </p>


                <div class="card-action">
                    Buka Inventory →
                </div>

            </div>

        </a>


        <!-- =================================================
             PREV MAINT
        ================================================= -->

        <a
            href="{{ route('inventory.prev') }}"
            class="
                menu-card
                prev
            "
        >

            <div class="card">

                <div class="icon">
                    <x-icon name="wrench"></x-icon>
                </div>


                <h2>
                    PREV MAINT
                </h2>


                <p>
                    Kelola stok dan transaksi inventory
                    khusus untuk kegiatan Preventive
                    Maintenance.
                </p>


                <div class="card-action">
                    Buka Inventory →
                </div>

            </div>

        </a>


    </div>


    <!-- =====================================================
         CATATAN
    ===================================================== -->

    <div class="inventory-note">

        <strong style="color:var(--pds-ink-2);">
            Catatan:
        </strong>

        Setelah memilih kelompok, pengguna masuk ke
        tabel inventory kelompok tersebut. Menu
        Stok Barang, Barang Masuk, Barang Keluar,
        dan menu inventory lainnya berada di halaman
        kelompok masing-masing.

    </div>


    <!-- =====================================================
         FOOTER
    ===================================================== -->

    <div class="page-footer">

        <a
            href="{{ route('dashboard') }}"
            class="back-dashboard"
        >
            <x-icon name="back"></x-icon> Kembali ke Dashboard
        </a>

    </div>


</main>


</body>

</html>