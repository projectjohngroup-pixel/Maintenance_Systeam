<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Administrator -
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

            background: var(--pds-soft);

            color: var(--pds-ink);
        }


        /* =====================================================
           TOPBAR
        ===================================================== */

        .topbar {
            height: 76px;

            background: var(--pds-card);

            border-bottom:
                1px solid var(--pds-line);

            display: flex;

            align-items: center;

            justify-content: space-between;

            padding: 0 32px;
        }

        .brand {
            font-size: 20px;

            font-weight: 700;

            color: var(--pds-ink);
        }

        .back {
            text-decoration: none;

            color: #2563eb;

            font-size: 14px;

            font-weight: 600;
        }

        .back:hover {
            color: #1d4ed8;
        }


        /* =====================================================
           PAGE
        ===================================================== */

        .page {
            max-width: 1250px;

            margin: 0 auto;

            padding:
                40px
                28px
                60px;
        }

        .page-header {
            margin-bottom: 30px;
        }

        .page-header h1 {
            font-size: 28px;

            margin-bottom: 8px;

            color: var(--pds-ink);
        }

        .page-header p {
            color: var(--pds-muted);

            font-size: 14px;
        }


        /* =====================================================
           MENU GRID
        ===================================================== */

        .menu-grid {
            display: grid;

            grid-template-columns:
                repeat(
                    3,
                    minmax(260px, 1fr)
                );

            gap: 22px;
        }


        /* =====================================================
           MENU CARD
        ===================================================== */

        .menu-card {
            text-decoration: none;

            color: inherit;
        }

        .card {
            background: var(--pds-card);

            border:
                1px solid var(--pds-line);

            border-radius: 16px;

            padding: 26px;

            min-height: 190px;

            box-shadow:
                0 4px 15px
                rgba(0,0,0,.05);

            transition:
                transform .18s ease,
                box-shadow .18s ease,
                border-color .18s ease;
        }

        .card:hover {
            transform:
                translateY(-4px);

            box-shadow:
                0 12px 28px
                rgba(0,0,0,.10);

            border-color:
                var(--pds-line-2);
        }


        /* =====================================================
           ICON
        ===================================================== */

        .icon {
            width: 56px;
            height: 56px;

            border-radius: 14px;

            display: flex;

            align-items: center;
            justify-content: center;

            font-size: 23px;

            font-weight: 700;

            margin-bottom: 20px;
        }

        .icon-dashboard {
            background: #dbeafe;
            color: #2563eb;
        }

        .icon-inventory {
            background: #dcfce7;
            color: #16a34a;
        }

        .icon-workorder {
            background: #fef3c7;
            color: #d97706;
        }

        .icon-users {
            background: #ede9fe;
            color: #7c3aed;
        }

        .icon-machine {
            background: #e0f2fe;
            color: #0284c7;
        }

        .icon-area {
            background: #dbeafe;
            color: #2563eb;
        }

        .icon-sparepart {
            background: #fef3c7;
            color: #d97706;
        }

        .icon-kpi {
            background: #fce7f3;
            color: #db2777;
        }

        .icon-setting {
            background: var(--pds-soft-2);
            color: var(--pds-ink-2);
        }


        /* =====================================================
           TEXT
        ===================================================== */

        .card h2 {
            font-size: 19px;

            margin-bottom: 8px;

            color: var(--pds-ink);
        }

        .card p {
            color: var(--pds-muted);

            font-size: 13px;

            line-height: 1.55;
        }


        /* =====================================================
           DISABLED
        ===================================================== */

        .menu-card.disabled {
            cursor: not-allowed;
        }

        .menu-card.disabled .card {
            opacity: .65;
        }


        /* =====================================================
           RESPONSIVE
        ===================================================== */

        @media (max-width: 1000px) {

            .menu-grid {
                grid-template-columns:
                    repeat(
                        2,
                        minmax(260px, 1fr)
                    );
            }

        }


        @media (max-width: 700px) {

            .menu-grid {
                grid-template-columns:
                    1fr;
            }

            .topbar {
                padding:
                    0 18px;
            }

            .page {
                padding:
                    28px
                    18px
                    45px;
            }

            .page-header h1 {
                font-size: 24px;
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


    <a
        href="{{ route('dashboard') }}"
        class="back"
    >
        <x-icon name="back"></x-icon> Kembali
    </a>

</header>


<!-- =====================================================
     ADMINISTRATOR
===================================================== -->

<main class="page">


    <div class="page-header">

        <h1>
            Administrator
        </h1>

        <p>
            Pilih modul yang ingin dikelola.
        </p>

    </div>


    <!-- =================================================
         MENU
    ================================================= -->

    <div class="menu-grid">


        <!-- DASHBOARD -->

        <a
            href="{{ route('dashboard') }}"
            class="menu-card"
        >

            <div class="card">

                <div class="icon icon-dashboard">
                    <x-icon name="chart"></x-icon>
                </div>

                <h2>
                    Dashboard
                </h2>

                <p>
                    Melihat ringkasan sistem,
                    grafik, KPI, dan informasi utama.
                </p>

            </div>

        </a>


        <!-- INVENTORY -->

        <a
            href="{{ route('inventory.index') }}"
            class="menu-card"
        >

            <div class="card">

                <div class="icon icon-inventory">
                    <x-icon name="box"></x-icon>
                </div>

                <h2>
                    Inventory
                </h2>

                <p>
                    Kelola stok barang,
                    barang masuk, barang keluar,
                    purchase request, dan restock.
                </p>

            </div>

        </a>


        <!-- WORK ORDER -->

        <a
            href="{{ route('work-orders.index') }}"
            class="menu-card"
        >

            <div class="card">

                <div class="icon icon-workorder">
                    <x-icon name="wrench"></x-icon>
                </div>

                <h2>
                    Work Order
                </h2>

                <p>
                    Kelola pekerjaan maintenance,
                    kerusakan, perbaikan, dan
                    riwayat pekerjaan.
                </p>

            </div>

        </a>


        <!-- USERS -->

        <a
            href="{{ route('users.index') }}"
            class="menu-card"
        >

            <div class="card">

                <div class="icon icon-users">
                    <x-icon name="user"></x-icon>
                </div>

                <h2>
                    Users
                </h2>

                <p>
                    Kelola pengguna,
                    role, bagian, dan hak akses.
                </p>

            </div>

        </a>


        <!-- MESIN -->

        <a
            href="{{ route('machines.index') }}"
            class="menu-card"
        >

            <div class="card">

                <div class="icon icon-machine">
                    <x-icon name="gear"></x-icon>
                </div>

                <h2>
                    Mesin
                </h2>

                <p>
                    Kelola data mesin yang
                    digunakan dalam maintenance.
                </p>

            </div>

        </a>


        <!-- AREA -->

        <a
            href="{{ route('areas.index') }}"
            class="menu-card"
        >

            <div class="card">

                <div class="icon icon-area">
                    <x-icon name="pin"></x-icon>
                </div>

                <h2>
                    Area
                </h2>

                <p>
                    Kelola area atau lokasi
                    tempat mesin berada.
                </p>

            </div>

        </a>


        <!-- MESIN & SPAREPART -->

        <a
            href="{{ route('machine-spareparts.index') }}"
            class="menu-card"
        >

            <div class="card">

                <div class="icon icon-sparepart">
                    <x-icon name="box"></x-icon>
                </div>

                <h2>
                    Mesin & Sparepart
                </h2>

                <p>
                    Atur sparepart yang digunakan
                    setiap mesin beserta jumlah
                    kebutuhannya.
                </p>

            </div>

        </a>


        <!-- KPI / GRAFIK -->

        <a
            href="{{ route('dashboard') }}"
            class="menu-card"
        >

            <div class="card">

                <div class="icon icon-kpi">
                    <x-icon name="trend"></x-icon>
                </div>

                <h2>
                    Grafik & KPI
                </h2>

                <p>
                    Melihat grafik dan indikator
                    kinerja utama melalui Dashboard.
                </p>

            </div>

        </a>


        <!-- SETTING -->

        <a
            href="{{ route('settings.index') }}"
            class="menu-card"
        >

            <div class="card">

                <div class="icon icon-setting">
                    <x-icon name="gear"></x-icon>
                </div>

                <h2>
                    Setting
                </h2>

                <p>
                    Kelola pengaturan sistem,
                    konfigurasi, dan preferensi aplikasi.
                </p>

            </div>

        </a>


    </div>


</main>


</body>

</html>