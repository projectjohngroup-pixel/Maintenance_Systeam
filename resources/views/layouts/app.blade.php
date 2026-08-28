<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <meta
        name="csrf-token"
        content="{{ csrf_token() }}"
    >

    <script>
        /*
        | Theme dipasang SEBELUM render
        | untuk mencegah flash light/dark.
        */

        (function () {

            try {

                var theme =
                    localStorage.getItem('pachira-theme');

                if (
                    theme !== 'dark' &&
                    theme !== 'light'
                ) {
                    theme = 'light';
                }

                document.documentElement.setAttribute(
                    'data-theme',
                    theme
                );

            } catch (e) {

                document.documentElement.setAttribute(
                    'data-theme',
                    'light'
                );
            }
        })();
    </script>

    <title>
        @yield('title', 'PACHIRA DISTRINUSA - PACHIRA MAINTENANCE SYSTEM')
    </title>

    <link
        rel="icon"
        type="image/x-icon"
        href="{{ asset('favicon.ico') }}"
    >

    <style>

        :root {
            --primary: #2563eb;
            --primary-dark: #1d4ed8;

            --sidebar: #0b1220;
            --sidebar-2: #111827;

            --bg: #f4f7fb;
            --card: #ffffff;
            --card-soft: #f8fafc;

            --text: #0f172a;
            --text-2: #334155;
            --muted: #64748b;

            --border: #e2e8f0;
            --border-dark: #cbd5e1;

            --success: #16a34a;
            --warning: #d97706;
            --danger: #dc2626;

            --shadow-sm:
                0 2px 8px rgba(15,23,42,.05);

            --shadow-md:
                0 8px 25px rgba(15,23,42,.08);

            --shadow-lg:
                0 20px 60px rgba(15,23,42,.14);
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            min-height: 100%;
        }

        body {
            min-height: 100vh;

            font-family:
                Inter,
                ui-sans-serif,
                system-ui,
                -apple-system,
                BlinkMacSystemFont,
                "Segoe UI",
                Arial,
                Helvetica,
                sans-serif;

            background:
                linear-gradient(
                    180deg,
                    #f8fafc 0%,
                    #f4f7fb 100%
                );

            color: var(--text);

            -webkit-font-smoothing: antialiased;
            text-rendering: optimizeLegibility;
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        button,
        input,
        select,
        textarea {
            font-family: inherit;
        }


        /* =====================================================
           APP
        ====================================================== */

        .app {
            display: flex;
            min-height: 100vh;
        }


        /* =====================================================
           SIDEBAR
        ====================================================== */

        .sidebar {
            position: fixed;

            top: 0;
            left: 0;
            bottom: 0;

            width: 255px;

            background:
                linear-gradient(
                    180deg,
                    #0b1220 0%,
                    #111827 48%,
                    #0f172a 100%
                );

            color: #ffffff;

            overflow-y: auto;

            z-index: 1000;

            border-right:
                1px solid rgba(255,255,255,.05);

            box-shadow:
                8px 0 35px rgba(15,23,42,.08);
        }

        .sidebar::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: transparent;
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,.12);
            border-radius: 20px;
        }

        .sidebar-logo {
            padding: 24px 20px 22px;

            border-bottom:
                1px solid rgba(255,255,255,.06);
        }

        .sidebar-logo-title {
            font-size: 20px;
            font-weight: 800;
            letter-spacing: -.02em;
            color: #ffffff;
        }

        .sidebar-logo-subtitle {
            margin-top: 6px;
            font-size: 11px;
            color: #94a3b8;
            letter-spacing: .03em;
        }

        .sidebar-menu {
            padding: 16px 11px 32px;
        }

        .sidebar-section {
            margin-top: 20px;
            margin-bottom: 8px;

            padding: 0 11px;

            font-size: 9px;
            font-weight: 800;
            color: #64748b;

            text-transform: uppercase;
            letter-spacing: .16em;
        }


        /* =====================================================
           SIDEBAR LINKS
        ====================================================== */

        .sidebar-link,
        .user-sidebar-link {
            position: relative;

            width: 100%;

            display: flex;

            align-items: center;

            gap: 11px;

            padding: 11px 12px;

            margin-bottom: 4px;

            border:
                1px solid transparent;

            border-radius: 10px;

            background: transparent;

            color: #cbd5e1;

            font-size: 13px;

            text-align: left;

            cursor: pointer;

            transition:
                background .18s ease,
                color .18s ease,
                border-color .18s ease,
                transform .18s ease,
                box-shadow .18s ease;
        }

        .sidebar-link:hover,
        .user-sidebar-link:hover {
            background: rgba(255,255,255,.055);

            border-color:
                rgba(255,255,255,.06);

            color: #ffffff;

            transform: translateX(2px);
        }

        .sidebar-link.active,
        .user-sidebar-link.active {
            background:
                linear-gradient(
                    135deg,
                    rgba(37,99,235,.96),
                    rgba(29,78,216,.90)
                );

            border-color:
                rgba(147,197,253,.18);

            color: #ffffff;

            box-shadow:
                0 7px 18px rgba(37,99,235,.23);
        }

        .sidebar-link.active::before,
        .user-sidebar-link.active::before {
            content: "";

            position: absolute;

            left: -11px;

            top: 8px;
            bottom: 8px;

            width: 3px;

            border-radius: 999px;

            background: #60a5fa;
        }


        /* =====================================================
           ICON UMUM & TOMBOL AKSI TABEL
        ====================================================== */

        svg.ic {
            width: 1em;
            height: 1em;
            display: inline-block;
            vertical-align: -0.125em;
            flex-shrink: 0;
        }

        .act-group {
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .act-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 30px;
            height: 30px;
            border-radius: 8px;
            border: 1px solid transparent;
            background: transparent;
            color: var(--pds-ink-2, #334155);
            cursor: pointer;
            text-decoration: none;
            font-size: 15px;
            transition:
                background .15s ease,
                color .15s ease,
                border-color .15s ease;
        }

        .act-btn:hover {
            transform: none;
        }

        .act-btn.act-view {
            color: #2563eb;
        }

        .act-btn.act-view:hover {
            background: rgba(37, 99, 235, .1);
            border-color: rgba(37, 99, 235, .25);
        }

        .act-btn.act-edit {
            color: #d97706;
        }

        .act-btn.act-edit:hover {
            background: rgba(217, 119, 6, .1);
            border-color: rgba(217, 119, 6, .25);
        }

        .act-btn.act-delete {
            color: #dc2626;
        }

        .act-btn.act-delete:hover {
            background: rgba(220, 38, 38, .1);
            border-color: rgba(220, 38, 38, .28);
        }

        .btn-add-master {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 9px 16px;
            font-size: 13px;
            font-weight: 700;
            color: #fff;
            background: var(--pds-accent, #2563eb);
            border: none;
            border-radius: 10px;
            cursor: pointer;
            white-space: nowrap;
        }

        .btn-add-master svg.ic {
            font-size: 14px;
        }

        /* =====================================================
           SIDEBAR ICON
        ====================================================== */

        .sidebar-link-icon,
        .user-sidebar-link-icon {
            width: 28px;
            min-width: 28px;

            height: 28px;

            display: inline-flex;

            align-items: center;
            justify-content: center;

            border-radius: 8px;

            background:
                rgba(255,255,255,.055);

            color: #cbd5e1;

            transition:
                background .18s ease,
                color .18s ease;
        }

        .sidebar-link-icon svg,
        .user-sidebar-link-icon svg {
            width: 16px;
            height: 16px;

            display: block;

            stroke: currentColor;

            fill: none;

            stroke-width: 1.9;

            stroke-linecap: round;

            stroke-linejoin: round;
        }

        .sidebar-link:hover .sidebar-link-icon,
        .user-sidebar-link:hover .user-sidebar-link-icon,
        .sidebar-link.active .sidebar-link-icon,
        .user-sidebar-link.active .user-sidebar-link-icon {
            background:
                rgba(255,255,255,.12);

            color: #ffffff;
        }


        /* =====================================================
           SIDEBAR GROUP
        ====================================================== */

        .sidebar-group {
            width: 100%;
        }

        .sidebar-chevron {
            width: 8px;
            height: 8px;

            margin-left: auto;

            border-right:
                2px solid #64748b;

            border-bottom:
                2px solid #64748b;

            transform:
                rotate(45deg);

            transition:
                transform .18s ease,
                border-color .18s ease;

            flex-shrink: 0;
        }

        .sidebar-group.open .sidebar-chevron {
            transform: rotate(225deg);
            border-color: #cbd5e1;
        }


        /* =====================================================
           SUBMENU
        ====================================================== */

        .sidebar-submenu {
            display: none;

            margin: 3px 0 7px 17px;

            padding-left: 12px;

            border-left:
                1px solid rgba(255,255,255,.08);
        }

        .sidebar-group.open .sidebar-submenu {
            display: block;
        }

        .sidebar-sub-link {
            display: flex;

            align-items: center;

            gap: 8px;

            padding: 9px 11px;

            margin-bottom: 3px;

            border:
                1px solid transparent;

            border-radius: 8px;

            color: #94a3b8;

            font-size: 12px;

            transition:
                background .18s ease,
                color .18s ease,
                border-color .18s ease,
                transform .18s ease;
        }

        .sidebar-sub-link::before {
            content: "";

            width: 5px;
            height: 5px;

            border-radius: 50%;

            background: #475569;

            transition:
                background .18s ease,
                transform .18s ease;
        }

        .sidebar-sub-link:hover {
            background:
                rgba(255,255,255,.045);

            color: #ffffff;

            transform: translateX(2px);
        }

        .sidebar-sub-link:hover::before {
            background: #60a5fa;
        }

        .sidebar-sub-link.active {
            background:
                rgba(37,99,235,.12);

            border-color:
                rgba(96,165,250,.08);

            color: #ffffff;
        }

        .sidebar-sub-link.active::before {
            background: #60a5fa;
            transform: scale(1.35);
        }


        /* =====================================================
           BADGE
        ====================================================== */

        .sidebar-badge {
            margin-left: auto;

            min-width: 21px;
            height: 21px;

            padding: 0 6px;

            display: inline-flex;

            align-items: center;
            justify-content: center;

            border-radius: 999px;

            background:
                linear-gradient(
                    135deg,
                    #ef4444,
                    #b91c1c
                );

            color: #ffffff;

            font-size: 10px;
            font-weight: 800;

            box-shadow:
                0 3px 10px rgba(220,38,38,.25);
        }


        /* =====================================================
           MANAGER INFO
        ====================================================== */

        .manager-sidebar-info {
            margin-top: 12px;

            padding: 13px;

            border:
                1px solid rgba(255,255,255,.06);

            border-radius: 10px;

            background:
                rgba(255,255,255,.035);

            color: #94a3b8;

            font-size: 11px;

            line-height: 1.6;
        }


        /* =====================================================
           MAIN
        ====================================================== */

        .main {
            width:
                calc(100% - 255px);

            margin-left:
                255px;

            min-height: 100vh;
        }


        /* =====================================================
           TOPBAR
        ====================================================== */

        .topbar {
            position: sticky;

            top: 0;

            z-index: 900;

            display: flex;

            justify-content: space-between;

            align-items: center;

            min-height: 74px;

            padding: 0 28px;

            background:
                rgba(255,255,255,.88);

            border-bottom:
                1px solid rgba(226,232,240,.9);

            backdrop-filter:
                blur(14px);

            -webkit-backdrop-filter:
                blur(14px);
        }

        .topbar-left {
            min-width: 0;
        }

        .page-title {
            margin: 0;

            font-size: 22px;

            font-weight: 800;

            letter-spacing: -.025em;

            color: #0f172a;
        }

        .page-subtitle {
            margin-top: 5px;

            font-size: 12px;

            color: #64748b;
        }

        .topbar-right {
            display: flex;

            align-items: center;

            gap: 10px;

            flex-shrink: 0;
        }


        /* =====================================================
           NOTIFICATION BELL
        ====================================================== */

        .notif-wrapper {
            position: relative;
        }

        .notif-bell {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 38px;
            height: 38px;
            border: none;
            border-radius: 8px;
            background: transparent;
            cursor: pointer;
            color: var(--pds-ink-2, #334155);
            transition: background .15s ease;
        }

        .notif-bell:hover {
            background: var(--pds-soft, #f1f5f9);
        }

        .notif-bell svg {
            width: 20px;
            height: 20px;
            stroke: currentColor;
            fill: none;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .notif-badge {
            position: absolute;
            top: 4px;
            right: 4px;
            min-width: 18px;
            height: 18px;
            padding: 0 5px;
            border-radius: 9px;
            background: #dc2626;
            color: #fff;
            font-size: 11px;
            font-weight: 700;
            line-height: 18px;
            text-align: center;
            pointer-events: none;
            transform: translate(50%, -50%);
        }

        .notif-dropdown {
            display: none;
            position: absolute;
            top: calc(100% + 8px);
            right: 0;
            width: 380px;
            max-height: 480px;
            background: var(--pds-card, #fff);
            border: 1px solid var(--pds-line, #e2e8f0);
            border-radius: 12px;
            box-shadow: 0 12px 40px rgba(0,0,0,.15);
            z-index: 10000;
            overflow: hidden;
        }

        .notif-dropdown.active {
            display: block;
        }

        .notif-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 14px;
            border-bottom: 1px solid var(--pds-line, #e2e8f0);
        }

        .notif-header-title {
            font-size: 15px;
            font-weight: 700;
            color: var(--pds-ink, #0f172a);
        }

        .notif-mark-all {
            border: none;
            background: none;
            color: #2563eb;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            padding: 4px 8px;
            border-radius: 6px;
            transition: background .15s ease;
        }

        .notif-mark-all:hover {
            background: rgba(37, 99, 235, .08);
        }

        .notif-list {
            max-height: 400px;
            overflow-y: auto;
        }

        .notif-item {
            display: block;
            padding: 10px 14px;
            border-bottom: 1px solid var(--pds-line, #e2e8f0);
            text-decoration: none;
            color: inherit;
            transition: background .12s ease;
            cursor: pointer;
        }

        .notif-item:last-child {
            border-bottom: none;
        }

        .notif-item:hover {
            background: var(--pds-soft, #f1f5f9);
        }

        .notif-item-title {
            font-size: 13px;
            font-weight: 600;
            color: var(--pds-ink, #0f172a);
            margin-bottom: 2px;
        }

        .notif-item-msg {
            font-size: 12px;
            color: var(--pds-muted, #64748b);
            line-height: 1.35;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .notif-item-time {
            font-size: 11px;
            color: var(--pds-muted-2, #94a3b8);
            margin-top: 3px;
        }

        .notif-empty {
            padding: 28px 14px;
            text-align: center;
            color: var(--pds-muted, #64748b);
            font-size: 13px;
        }

        .notif-empty svg {
            width: 36px;
            height: 36px;
            margin: 0 auto 8px;
            stroke: var(--pds-muted-2, #94a3b8);
            fill: none;
            stroke-width: 1.5;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        @media (max-width: 500px) {
            .notif-dropdown {
                width: calc(100vw - 24px);
                right: -8px;
            }
        }


        /* =====================================================
           ACCOUNT
        ====================================================== */

        .topbar-left {

            display: flex;

            align-items: center;

            min-width: 0;

            flex: 1 1 auto;
        }

        .topbar-title-wrap {

            min-width: 0;

            display: flex;

            flex-direction: column;

            justify-content: center;

            padding-right: 14px;
        }

        .topbar-title-wrap .page-title {

            overflow: hidden;

            text-overflow: ellipsis;

            white-space: nowrap;
        }

        .topbar-title-wrap .page-subtitle {

            overflow: hidden;

            text-overflow: ellipsis;

            white-space: nowrap;
        }

        /* Layar sempit (laptop scaling/zoom): sembunyikan subjudul
           topbar agar judul tidak menempel ke tombol tema & profil */
        @media (max-width: 1160px) {

            .topbar-title-wrap .page-subtitle {
                display: none;
            }

            .topbar-title-wrap .page-title {
                font-size: 18px;
            }
        }


        /* =====================================================
           ACCOUNT
        ====================================================== */

        .account-wrapper {
            position: relative;
        }

        .account-button {
            min-width: 195px;

            display: flex;

            align-items: center;

            gap: 10px;

            padding: 7px 10px;

            border:
                1px solid #e2e8f0;

            border-radius: 12px;

            background:
                rgba(248,250,252,.95);

            color: #111827;

            cursor: pointer;

            text-align: left;

            transition:
                background .18s ease,
                border-color .18s ease,
                box-shadow .18s ease;
        }

        .account-button:hover,
        .account-wrapper.open .account-button {
            background: #ffffff;

            border-color:
                #cbd5e1;

            box-shadow:
                0 5px 18px rgba(15,23,42,.07);
        }

        .account-avatar {
            width: 38px;
            height: 38px;

            min-width: 38px;

            display: flex;

            align-items: center;
            justify-content: center;

            overflow: hidden;

            border-radius: 50%;

            background:
                linear-gradient(
                    135deg,
                    #2563eb,
                    #1d4ed8
                );

            color: #ffffff;

            font-size: 13px;

            font-weight: 800;

            box-shadow:
                0 4px 12px rgba(37,99,235,.22);
        }

        .account-avatar img {
            width: 100%;
            height: 100%;

            display: block;

            object-fit: cover;
        }

        .account-info {
            min-width: 0;
            flex: 1;
        }

        .account-name {
            overflow: hidden;

            text-overflow: ellipsis;

            white-space: nowrap;

            font-size: 12px;

            font-weight: 800;

            color: #0f172a;
        }

        .account-role {
            margin-top: 2px;

            font-size: 10px;

            color: #64748b;
        }

        .account-chevron {
            width: 7px;
            height: 7px;

            margin-left: auto;

            border-right:
                2px solid #64748b;

            border-bottom:
                2px solid #64748b;

            transform: rotate(45deg);

            transition:
                transform .18s ease;
        }

        .account-wrapper.open .account-chevron {
            transform: rotate(225deg);
        }

        .account-dropdown {
            display: none;

            position: absolute;

            top:
                calc(100% + 10px);

            right: 0;

            width: 260px;

            padding: 8px;

            background: #ffffff;

            border:
                1px solid #e2e8f0;

            border-radius: 14px;

            box-shadow:
                0 20px 55px rgba(15,23,42,.14);

            z-index: 5000;
        }

        .account-wrapper.open .account-dropdown {
            display: block;
        }

        .account-dropdown-user {
            padding:
                11px 12px;

            margin-bottom: 6px;

            border-bottom:
                1px solid #f1f5f9;
        }

        .account-dropdown-name {
            font-size: 12px;
            font-weight: 800;
            color: #0f172a;
        }

        .account-dropdown-meta {
            margin-top: 4px;

            font-size: 10px;

            color: #64748b;

            line-height: 1.5;
        }

        .account-menu-link,
        .account-menu-button {
            width: 100%;

            display: flex;

            align-items: center;

            gap: 10px;

            min-height: 42px;

            padding:
                9px 11px;

            border:
                1px solid transparent;

            border-radius: 9px;

            background: transparent;

            color: #334155;

            font-size: 12px;

            text-align: left;

            text-decoration: none;

            cursor: pointer;

            transition:
                background .16s ease,
                border-color .16s ease,
                color .16s ease;
        }

        .account-menu-link:hover,
        .account-menu-button:hover {
            background: #f8fafc;

            border-color:
                #e2e8f0;

            color: #0f172a;
        }

        .account-menu-danger {
            color: #b91c1c;
        }

        .account-menu-danger:hover {
            background: #fef2f2;

            border-color:
                #fecaca;

            color: #991b1b;
        }

        .account-logout-form {
            margin: 0;
        }


        /* =====================================================
           CONTENT
        ====================================================== */

        .content {
            padding: 28px;
        }


        /* =====================================================
           UNIVERSAL CARD
        ====================================================== */

        .card,
        .dashboard-card,
        .kpi-card,
        .welcome-card,
        .panel,
        .box,
        .table-card,
        .form-card,
        .stat-card {
            background:
                rgba(255,255,255,.96);

            border:
                1px solid #e2e8f0;

            border-radius:
                16px;

            box-shadow:
                var(--shadow-sm);
        }


        /* =====================================================
           NORMAL TABLE
        ====================================================== */

        table {
            width: 100%;

            border-collapse:
                separate;

            border-spacing: 0;

            background: #ffffff;

            border:
                1px solid #dbe3ed;

            border-radius: 12px;

            overflow: hidden;

            box-shadow:
                0 4px 16px rgba(15,23,42,.045);
        }

        table thead th {
            padding:
                12px 13px;

            background:
                linear-gradient(
                    180deg,
                    #f8fafc,
                    #eef2f7
                );

            color:
                #334155;

            font-size:
                11px;

            font-weight:
                800;

            border-right:
                1px solid #dbe3ed;

            border-bottom:
                1px solid #cfd8e3;

            white-space:
                nowrap;
        }

        table thead th:last-child {
            border-right: none;
        }

        table tbody td {
            padding:
                11px 13px;

            font-size:
                12px;

            color:
                #475569;

            background:
                #ffffff;

            border-right:
                1px solid #e2e8f0;

            border-bottom:
                1px solid #e2e8f0;

            vertical-align:
                middle;
        }

        table tbody td:last-child {
            border-right: none;
        }

        table tbody tr:last-child td {
            border-bottom: none;
        }

        table tbody tr {
            transition:
                background .16s ease;
        }

        table tbody tr:hover td {
            background:
                #f8fbff;
        }


        /* =====================================================
           INPUT
        ====================================================== */

        input,
        select,
        textarea {
            border:
                1px solid #dbe3ed;

            border-radius:
                10px;

            background:
                #ffffff;

            color:
                #0f172a;

            outline:
                none;

            transition:
                border-color .18s ease,
                box-shadow .18s ease;
        }

        input:focus,
        select:focus,
        textarea:focus {
            border-color:
                #60a5fa;

            box-shadow:
                0 0 0 3px
                rgba(37,99,235,.10);
        }


        /* =====================================================
           MESSAGE
        ====================================================== */

        .message {
            margin-bottom:
                20px;

            padding:
                13px 16px;

            border-radius:
                10px;

            font-size:
                13px;

            box-shadow:
                0 3px 10px rgba(15,23,42,.04);
        }

        .message.success {
            background:
                linear-gradient(
                    135deg,
                    #ecfdf5,
                    #f0fdf4
                );

            color:
                #047857;

            border:
                1px solid #a7f3d0;
        }

        .message.error {
            background:
                linear-gradient(
                    135deg,
                    #fef2f2,
                    #fff1f2
                );

            color:
                #b91c1c;

            border:
                1px solid #fecaca;
        }


        /* =====================================================
           TOAST
        ====================================================== */

        .crud-toast-container {
            position: fixed;

            top: 88px;
            right: 24px;

            z-index: 1000000;

            display: flex;

            flex-direction: column;

            gap: 10px;

            pointer-events: none;
        }

        .crud-toast {
            min-width:
                290px;

            max-width:
                410px;

            padding:
                13px 16px;

            border-radius:
                12px;

            background:
                rgba(255,255,255,.96);

            border:
                1px solid #e2e8f0;

            box-shadow:
                var(--shadow-md);

            font-size:
                13px;

            line-height:
                1.45;
        }

        .crud-toast.success {
            border-left:
                4px solid #16a34a;

            color:
                #166534;
        }

        .crud-toast.error {
            border-left:
                4px solid #dc2626;

            color:
                #991b1b;
        }


        /* =====================================================
           AI BUTTON
        ====================================================== */

        .ai-assistant-button {
            position: fixed;

            right: 24px;
            bottom: 22px;

            z-index: 99999;

            width: 58px;
            height: 58px;

            display: flex;

            align-items: center;
            justify-content: center;

            border:
                3px solid rgba(255,255,255,.95);

            border-radius:
                50%;

            background:
                linear-gradient(
                    135deg,
                    #fbbf24,
                    #f59e0b
                );

            color:
                #ffffff;

            font-size:
                28px;

            cursor:
                pointer;

            box-shadow:
                0 12px 30px rgba(217,119,6,.28);

            transition:
                transform .2s ease,
                box-shadow .2s ease;
        }

        .ai-assistant-button:hover {
            transform:
                translateY(-2px) scale(1.04);

            box-shadow:
                0 16px 35px rgba(217,119,6,.32);
        }


        /* =====================================================
           AI PANEL
        ====================================================== */

        .ai-assistant-panel {
            position: fixed;

            right: 24px;
            bottom: 92px;

            z-index: 99998;

            width:
                480px;

            max-width:
                calc(100vw - 30px);

            height:
                610px;

            max-height:
                calc(100vh - 120px);

            display:
                none;

            flex-direction:
                column;

            overflow:
                hidden;

            background:
                rgba(255,255,255,.98);

            border:
                1px solid #dbe3ed;

            border-radius:
                20px;

            box-shadow:
                0 25px 70px rgba(15,23,42,.18);
        }

        .ai-assistant-panel.active {
            display: flex;
        }


        /* =====================================================
           AI HEADER
        ====================================================== */

        .ai-assistant-header {
            display:
                flex;

            align-items:
                center;

            justify-content:
                space-between;

            padding:
                16px 18px;

            background:
                linear-gradient(
                    135deg,
                    #0f172a,
                    #172033
                );

            color:
                #ffffff;
        }

        .ai-assistant-header-left {
            display:
                flex;

            align-items:
                center;

            gap:
                11px;
        }

        .ai-assistant-header-icon {
            width:
                40px;

            height:
                40px;

            display:
                flex;

            align-items:
                center;

            justify-content:
                center;

            border-radius:
                50%;

            background:
                linear-gradient(
                    135deg,
                    #fbbf24,
                    #f59e0b
                );

            font-size:
                21px;

            box-shadow:
                0 5px 15px rgba(245,158,11,.25);
        }

        .ai-assistant-title {
            font-size:
                13px;

            font-weight:
                800;
        }

        .ai-assistant-status {
            margin-top:
                3px;

            font-size:
                10px;

            color:
                #94a3b8;
        }

        .ai-assistant-close {
            width:
                31px;

            height:
                31px;

            display:
                flex;

            align-items:
                center;

            justify-content:
                center;

            border:
                1px solid transparent;

            border-radius:
                8px;

            background:
                rgba(255,255,255,.05);

            color:
                #cbd5e1;

            font-size:
                19px;

            cursor:
                pointer;
        }

        .ai-assistant-close:hover {
            background:
                rgba(255,255,255,.10);

            color:
                #ffffff;
        }


        /* =====================================================
           AI BODY
        ====================================================== */

        .ai-assistant-body {
            flex: 1;

            padding:
                17px;

            overflow-y:
                auto;

            background:
                linear-gradient(
                    180deg,
                    #f8fafc,
                    #f1f5f9
                );
        }

        .ai-assistant-body::-webkit-scrollbar {
            width: 7px;
        }

        .ai-assistant-body::-webkit-scrollbar-track {
            background: transparent;
        }

        .ai-assistant-body::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }

        .ai-message {
            display:
                flex;

            margin-bottom:
                13px;
        }

        .ai-message.ai {
            justify-content:
                flex-start;

            width:
                100%;
        }

        .ai-message.user {
            justify-content:
                flex-end;
        }

        .ai-bubble {
            max-width:
                88%;

            padding:
                11px 13px;

            border-radius:
                14px;

            font-size:
                12px;

            line-height:
                1.5;

            word-break:
                break-word;
        }

        .ai-message.ai .ai-bubble {
            width:
                100%;

            max-width:
                100%;

            background:
                rgba(255,255,255,.97);

            border:
                1px solid #e2e8f0;

            color:
                #334155;

            border-top-left-radius:
                6px;

            box-shadow:
                0 3px 10px rgba(15,23,42,.035);
        }

        .ai-message.user .ai-bubble {
            background:
                linear-gradient(
                    135deg,
                    #2563eb,
                    #1d4ed8
                );

            color:
                #ffffff;

            border-top-right-radius:
                6px;

            box-shadow:
                0 5px 15px rgba(37,99,235,.18);
        }


        /* =====================================================
           AI RESULT
        ====================================================== */

        .ai-result-title {
            margin-bottom:
                11px;

            font-size:
                14px;

            font-weight:
                800;

            color:
                #0f172a;
        }

        .ai-result-summary {
            margin-bottom:
                11px;

            padding:
                10px 12px;

            border:
                1px solid #dbe3ed;

            border-radius:
                9px;

            background:
                linear-gradient(
                    135deg,
                    #f8fafc,
                    #f1f5f9
                );

            color:
                #475569;

            font-size:
                11px;

            line-height:
                1.5;
        }

        .ai-result-section {
            width:
                100%;

            margin-top:
                15px;
        }

        .ai-result-section:first-child {
            margin-top:
                0;
        }

        .ai-result-section-title {
            margin-bottom:
                8px;

            color:
                #0f172a;

            font-size:
                12px;

            font-weight:
                800;
        }

        .ai-result-text {
            font-size:
                12px;

            line-height:
                1.65;

            color:
                #475569;

            white-space:
                normal;
        }


        /* =====================================================
           AI TABLE
        ====================================================== */

        .ai-table-wrap {
            width:
                100%;

            max-width:
                100%;

            margin-top:
                8px;

            overflow-x:
                auto;

            background:
                #ffffff;

            border:
                1px solid #cbd5e1;

            border-radius:
                11px;

            box-shadow:
                0 4px 14px rgba(15,23,42,.045);

            cursor:
                pointer;

            transition:
                border-color .18s ease,
                box-shadow .18s ease,
                transform .18s ease;
        }

        .ai-table-wrap:hover {
            border-color:
                #93c5fd;

            box-shadow:
                0 9px 22px rgba(37,99,235,.10);

            transform:
                translateY(-1px);
        }

        .ai-table-wrap::-webkit-scrollbar {
            height:
                7px;
        }

        .ai-table-wrap::-webkit-scrollbar-track {
            background:
                #f1f5f9;
        }

        .ai-table-wrap::-webkit-scrollbar-thumb {
            background:
                #cbd5e1;

            border-radius:
                10px;
        }

        .ai-table-wrap::-webkit-scrollbar-thumb:hover {
            background:
                #94a3b8;
        }

        .ai-result-table {
            width:
                100%;

            min-width:
                700px;

            border-collapse:
                separate;

            border-spacing:
                0;

            background:
                #ffffff;

            font-size:
                11px;

            color:
                #334155;
        }

        .ai-result-table th {
            padding:
                11px 10px;

            background:
                linear-gradient(
                    180deg,
                    #f8fafc,
                    #eef2f7
                );

            color:
                #1e293b;

            font-size:
                10px;

            font-weight:
                800;

            border-right:
                1px solid #dbe3ed;

            border-bottom:
                1px solid #cbd5e1;

            white-space:
                nowrap;

            position:
                sticky;

            top:
                0;

            z-index:
                2;
        }

        .ai-result-table th:last-child {
            border-right:
                none;
        }

        .ai-result-table td {
            padding:
                10px;

            background:
                #ffffff;

            color:
                #475569;

            border-right:
                1px solid #e2e8f0;

            border-bottom:
                1px solid #e2e8f0;

            vertical-align:
                middle;
        }

        .ai-result-table td:last-child {
            border-right:
                none;
        }

        .ai-result-table tr:last-child td {
            border-bottom:
                none;
        }

        .ai-result-table tbody tr:hover td {
            background:
                #f8fbff;
        }

        .ai-result-table th:first-child,
        .ai-result-table td:first-child {
            width:
                48px;

            min-width:
                48px;

            text-align:
                center;

            font-weight:
                700;
        }


        /* =====================================================
           AI FOOTER
        ====================================================== */

        .ai-assistant-footer {
            padding:
                12px;

            background:
                #ffffff;

            border-top:
                1px solid #e5e7eb;
        }

        .ai-assistant-form {
            display:
                flex;

            align-items:
                center;

            gap:
                8px;
        }

        .ai-assistant-input {
            flex:
                1;

            height:
                43px;

            padding:
                0 15px;

            border:
                1px solid #dbe3ed;

            border-radius:
                22px;

            outline:
                none;

            font-size:
                12px;

            background:
                #ffffff;

            color:
                #0f172a;
        }

        .ai-assistant-input:focus {
            border-color:
                #60a5fa;

            box-shadow:
                0 0 0 3px
                rgba(37,99,235,.10);
        }

        .ai-assistant-send {
            width:
                58px;

            height:
                43px;

            flex-shrink:
                0;

            border:
                none;

            border-radius:
                22px;

            background:
                linear-gradient(
                    135deg,
                    #2563eb,
                    #1d4ed8
                );

            color:
                #ffffff;

            cursor:
                pointer;

            font-size:
                10px;

            font-weight:
                800;

            box-shadow:
                0 5px 14px rgba(37,99,235,.20);
        }

        .ai-assistant-send:hover {
            background:
                linear-gradient(
                    135deg,
                    #1d4ed8,
                    #1e40af
                );
        }

        .ai-assistant-send:disabled {
            opacity:
                1;

            cursor:
                pointer;
        }


        /* =====================================================
           FULLSCREEN TABLE
        ====================================================== */

        .ai-fullscreen-modal {
            display:
                none;

            position:
                fixed;

            inset:
                0;

            z-index:
                200000;

            flex-direction:
                column;

            padding:
                26px;

            background:
                linear-gradient(
                    180deg,
                    #f8fafc,
                    #eef2f7
                );
        }

        .ai-fullscreen-modal.active {
            display:
                flex;
        }

        .ai-fullscreen-header {
            display:
                flex;

            justify-content:
                space-between;

            align-items:
                center;

            gap:
                15px;

            margin-bottom:
                17px;

            padding-bottom:
                16px;

            border-bottom:
                1px solid #dbe3ed;
        }

        .ai-fullscreen-title {
            font-size:
                21px;

            font-weight:
                800;

            letter-spacing:
                -.02em;

            color:
                #0f172a;
        }

        .ai-fullscreen-actions {
            display:
                flex;

            gap:
                8px;
        }

        .ai-fullscreen-btn {
            min-height:
                40px;

            padding:
                0 16px;

            border:
                1px solid transparent;

            border-radius:
                10px;

            font-size:
                12px;

            font-weight:
                800;

            cursor:
                pointer;
        }

        .ai-fullscreen-btn.pdf {
            background:
                linear-gradient(
                    135deg,
                    #ef4444,
                    #dc2626
                );

            color:
                #ffffff;

            box-shadow:
                0 6px 15px rgba(220,38,38,.20);
        }

        .ai-fullscreen-btn.pdf:hover {
            background:
                linear-gradient(
                    135deg,
                    #dc2626,
                    #b91c1c
                );
        }

        .ai-fullscreen-btn.close {
            background:
                #ffffff;

            color:
                #334155;

            border-color:
                #cbd5e1;
        }

        .ai-fullscreen-btn.close:hover {
            background:
                #f8fafc;
        }

        .ai-fullscreen-body {
            flex:
                1;

            overflow:
                auto;

            padding:
                5px;

            background:
                rgba(255,255,255,.45);

            border:
                1px solid #e2e8f0;

            border-radius:
                15px;
        }

        .ai-fullscreen-body .ai-result-section {
            margin-top: 0;
        }

        .ai-fullscreen-body .ai-table-wrap {
            border:
                none;

            box-shadow:
                none;

            cursor:
                default;

            transform:
                none;

            margin-top:
                0;
        }

        .ai-fullscreen-body .ai-result-table {
            min-width:
                100%;

            font-size:
                13px;
        }

        .ai-fullscreen-body .ai-result-table th {
            padding:
                13px;

            font-size:
                12px;
        }

        .ai-fullscreen-body .ai-result-table td {
            padding:
                13px;

            font-size:
                12px;
        }


        /* =====================================================
           PRINT PDF
        ====================================================== */

        @media print {

            body * {
                visibility:
                    hidden !important;
            }

            #aiFullscreenModal,
            #aiFullscreenModal * {
                visibility:
                    visible !important;
            }

            #aiFullscreenModal {
                position:
                    absolute;

                inset:
                    0;

                display:
                    flex !important;

                padding:
                    10mm;

                background:
                    #ffffff;
            }

            .ai-fullscreen-header {
                margin-bottom:
                    10px;
            }

            .ai-fullscreen-actions {
                display:
                    none !important;
            }

            .ai-fullscreen-body {
                overflow:
                    visible;

                border:
                    none;

                box-shadow:
                    none;
            }

            .ai-fullscreen-body .ai-result-table {
                width:
                    100%;

                min-width:
                    100%;

                font-size:
                    10pt;
            }

            .ai-fullscreen-body .ai-result-table th,
            .ai-fullscreen-body .ai-result-table td {
                border:
                    1px solid #000000 !important;

                padding:
                    7px;
            }
        }


        /* =====================================================
           MOBILE
        ====================================================== */

        @media (max-width: 900px) {

            .sidebar {
                width:
                    220px;
            }

            .main {
                width:
                    calc(100% - 220px);

                margin-left:
                    220px;
            }
        }

        @media (max-width: 700px) {

            .sidebar {
                display:
                    none;
            }

            .main {
                width:
                    100%;

                margin-left:
                    0;
            }

            .topbar {
                padding:
                    0 15px;
            }

            .content {
                padding:
                    16px;
            }

            .account-button {
                min-width:
                    0;

                padding:
                    5px;

                background:
                    transparent;

                border-color:
                    transparent;
            }

            .account-info,
            .account-chevron {
                display:
                    none;
            }

            .account-dropdown {
                width:
                    240px;
            }

            .ai-assistant-button {
                right:
                    15px;

                bottom:
                    15px;
            }

            .ai-assistant-panel {
                right:
                    15px;

                bottom:
                    78px;

                width:
                    calc(100vw - 30px);

                height:
                    540px;
            }

            .ai-result-table {
                min-width:
                    650px;
            }

            .ai-fullscreen-modal {
                padding:
                    12px;
            }

            .ai-fullscreen-header {
                align-items:
                    flex-start;

                flex-direction:
                    column;
            }

            .ai-fullscreen-actions {
                width:
                    100%;
            }

            .ai-fullscreen-btn {
                flex:
                    1;
            }

            .crud-toast-container {
                top:
                    75px;

                left:
                    15px;

                right:
                    15px;
            }

            .crud-toast {
                min-width:
                    0;

                width:
                    100%;
            }
        }


        /* =====================================================
           GLOBAL SCROLLBAR
        ====================================================== */

        ::-webkit-scrollbar {
            width:
                9px;

            height:
                9px;
        }

        ::-webkit-scrollbar-track {
            background:
                #eef2f7;
        }

        ::-webkit-scrollbar-thumb {
            background:
                #cbd5e1;

            border-radius:
                20px;

            border:
                2px solid #eef2f7;
        }

        ::-webkit-scrollbar-thumb:hover {
            background:
                #94a3b8;
        }

        /* =====================================================
           THEME TOKENS - LIGHT (DEFAULT)
        ===================================================== */

        :root {

            --surface:
                #ffffff;

            --surface-soft:
                #f8fafc;

            --background:
                #f4f7fb;

            --text-main:
                #0f172a;

            --text-muted:
                #64748b;

            --line:
                #e2e8f0;
        }


        /* =====================================================
           DARK MODE
        ===================================================== */

        [data-theme="dark"] {

            --bg:
                #0b1120;

            --card:
                #111a2e;

            --card-soft:
                #16213a;

            --sidebar:
                #0d1526;

            --sidebar-2:
                #131e36;

            --surface:
                #111a2e;

            --surface-soft:
                #16213a;

            --background:
                #0b1120;

            --text:
                #e2e8f0;

            --text-2:
                #cbd5e1;

            --muted:
                #94a3b8;

            --border:
                #24304d;

            --border-dark:
                #33415580;

            --shadow-sm:
                0 2px 8px rgba(0,0,0,.35);

            --shadow-md:
                0 8px 25px rgba(0,0,0,.45);

            --shadow-lg:
                0 20px 60px rgba(0,0,0,.55);
        }


        /* =====================================================
           DARK MODE - SURFACES UTAMA
        ===================================================== */

        [data-theme="dark"] body,
        [data-theme="dark"] .app {

            background:
                var(--background);

            color:
                var(--text);
        }

        [data-theme="dark"] .main {

            background:
                var(--background);
        }

        [data-theme="dark"] .content {

            background:
                transparent;

            color:
                var(--text);
        }

        [data-theme="dark"] .topbar {

            background:
                var(--card);

            border-bottom-color:
                var(--border);

            color:
                var(--text);
        }

        [data-theme="dark"] .page-title {

            color:
                var(--text);
        }

        [data-theme="dark"] .page-subtitle {

            color:
                var(--muted);
        }

        [data-theme="dark"] .card,
        [data-theme="dark"] .dashboard-card,
        [data-theme="dark"] .kpi-card,
        [data-theme="dark"] .welcome-card,
        [data-theme="dark"] .panel,
        [data-theme="dark"] .box,
        [data-theme="dark"] .table-card,
        [data-theme="dark"] .form-card,
        [data-theme="dark"] .stat-card {

            background:
                var(--card);

            border-color:
                var(--border);

            color:
                var(--text);

            box-shadow:
                var(--shadow-sm);
        }

        [data-theme="dark"] .message.success {
            background: #052e1b;
            border-color: #14532d;
            color: #86efac;
        }

        [data-theme="dark"] .message.error {
            background: #2f0a0a;
            border-color: #7f1d1d;
            color: #fca5a5;
        }

        [data-theme="dark"] .crud-toast {

            background:
                var(--card);

            border-color:
                var(--border);

            color:
                var(--text);
        }

        [data-theme="dark"] .account-button {

            background:
                var(--card-soft);

            border-color:
                var(--border);

            color:
                var(--text);
        }

        [data-theme="dark"] .account-name {

            color: var(--text);
        }

        [data-theme="dark"] .account-role,
        [data-theme="dark"] .account-chevron {

            color: var(--muted);
        }

        [data-theme="dark"] .account-dropdown {

            background:
                var(--card);

            border-color:
                var(--border);
        }

        [data-theme="dark"] .account-dropdown-user {

            border-bottom-color:
                var(--border);
        }

        [data-theme="dark"] .account-menu-link,
        [data-theme="dark"] .account-menu-button {

            color:
                var(--text-2, var(--text));
        }

        [data-theme="dark"] .account-menu-link:hover,
        [data-theme="dark"] .account-menu-button:hover {

            background:
                var(--card-soft);

            color:
                var(--text);
        }

        [data-theme="dark"] .ai-assistant-panel,
        [data-theme="dark"] .ai-assistant-header,
        [data-theme="dark"] .ai-assistant-footer,
        [data-theme="dark"] .ai-fullscreen-modal,
        [data-theme="dark"] .ai-fullscreen-header,
        [data-theme="dark"] .ai-fullscreen-body {

            background:
                var(--card);

            border-color:
                var(--border);

            color:
                var(--text);
        }

        [data-theme="dark"] .ai-assistant-body {

            background:
                var(--background);
        }

        [data-theme="dark"] .ai-message.ai .ai-bubble {

            background:
                var(--card-soft);

            color:
                var(--text);

            border-color:
                var(--border);
        }

        [data-theme="dark"] .ai-assistant-input,
        [data-theme="dark"] .ai-fullscreen-btn {

            background:
                var(--card-soft);

            border-color:
                var(--border);

            color:
                var(--text);
        }

        [data-theme="dark"] .ai-result-table th {

            background:
                var(--card-soft);

            color:
                var(--text);
        }

        [data-theme="dark"] .ai-result-table td {

            border-color:
                var(--border);

            color:
                var(--text);
        }

        [data-theme="dark"] ::-webkit-scrollbar-track {

            background:
                var(--background);
        }

        [data-theme="dark"] ::-webkit-scrollbar-thumb {

            background:
                #334155;
        }


        /* =====================================================
           TOMBOL THEME
        ===================================================== */

        .theme-toggle {

            display: inline-flex;

            align-items: center;

            justify-content: center;

            gap: 6px;

            height: 38px;

            padding: 0 14px;

            margin-right: 12px;

            border-radius: 10px;

            border: 1px solid var(--border);

            background:
                var(--card);

            color:
                var(--text);

            font-size: 13px;

            font-weight: 600;

            cursor: pointer;

            transition:
                background .2s ease,
                border-color .2s ease;
        }

        .theme-toggle:hover {

            border-color:
                var(--primary);

            color:
                var(--primary);
        }


        /* #####################################################
           #####################################################
           
           PACHIRA AURORA - MODERN EXCLUSIVE UI LAYER
           
           Layer ini berada paling akhir <style> sehingga
           meng-override seluruh rule di atasnya secara aman.
           Tidak ada selector lama yang dihapus.
           
           #####################################################
           ##################################################### */

        :root {

            --primary: #6366f1;
            --primary-dark: #4f46e5;
            --primary-soft: rgba(99,102,241,.12);

            --accent: #22d3ee;
            --accent-2: #8b5cf6;

            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;

            --bg: #eef1f9;
            --card: #ffffff;
            --card-soft: #f6f8fd;

            --text: #0b1220;
            --text-2: #313d55;
            --muted: #69748c;

            --border: #e4e8f4;
            --border-dark: #cdd5ec;

            --radius-card: 18px;
            --radius-input: 12px;

            --shadow-sm:
                0 1px 2px rgba(15,23,42,.04),
                0 4px 14px rgba(15,23,42,.05);

            --shadow-md:
                0 2px 6px rgba(15,23,42,.05),
                0 14px 34px rgba(15,23,42,.09);

            --shadow-lg:
                0 8px 22px rgba(15,23,42,.08),
                0 30px 80px rgba(15,23,42,.16);

            --glow-primary:
                0 0 0 1px rgba(99,102,241,.25),
                0 8px 26px rgba(99,102,241,.32);

            --grad-brand:
                linear-gradient(
                    120deg,
                    #6366f1 0%,
                    #8b5cf6 52%,
                    #22d3ee 130%
                );
        }


        [data-theme="dark"] {

            --bg: #070b18;
            --card: #0e1530;
            --card-soft: #131c3d;

            --sidebar: #060a15;
            --sidebar-2: #0d1730;

            --surface: #0e1530;
            --surface-soft: #131c3d;
            --background: #070b18;

            --text: #e7ebfa;
            --text-2: #c3cbe6;
            --muted: #8b96b8;

            --border: #202b4e;
            --border-dark: #2a3763;

            --primary-soft: rgba(99,102,241,.20);

            --shadow-sm:
                0 1px 2px rgba(0,0,0,.45),
                0 4px 16px rgba(0,0,0,.35);

            --shadow-md:
                0 4px 10px rgba(0,0,0,.45),
                0 18px 40px rgba(0,0,0,.45);

            --shadow-lg:
                0 10px 28px rgba(0,0,0,.5),
                0 34px 90px rgba(0,0,0,.6);
        }


        /* =====================================================
           AURORA BACKGROUND
        ====================================================== */

        body {

            background:
                #eef1f9;
        }

        body::before {

            content: "";

            position: fixed;

            inset: 0;

            z-index: -1;

            pointer-events: none;

            background:

                radial-gradient(
                    900px 480px at 88% -10%,
                    rgba(99,102,241,.14),
                    transparent 62%
                ),

                radial-gradient(
                    720px 420px at -12% 108%,
                    rgba(34,211,238,.12),
                    transparent 60%
                ),

                radial-gradient(
                    560px 380px at 50% 118%,
                    rgba(139,92,246,.08),
                    transparent 65%
                );
        }

        [data-theme="dark"] body,
        [data-theme="dark"] .app {

            background:
                radial-gradient(
                    1100px 560px at 85% -12%,
                    rgba(99,102,241,.16),
                    transparent 60%
                ),
                radial-gradient(
                    800px 480px at -10% 110%,
                    rgba(34,211,238,.10),
                    transparent 58%
                ),
                #070b18;
        }

        ::selection {

            background:
                rgba(99,102,241,.25);
        }


        /* =====================================================
           SIDEBAR - EXCLUSIVE GLASS
        ====================================================== */

        .sidebar {

            width: 264px;

            background:

                linear-gradient(
                    180deg,
                    rgba(99,102,241,.10),
                    transparent 26%
                ),

                linear-gradient(
                    200deg,
                    #0a1024 0%,
                    #0b1228 44%,
                    #080d1d 100%
                );

            border-right:
                1px solid rgba(148,163,255,.09);

            box-shadow:
                14px 0 48px rgba(5,8,20,.35);
        }

        [data-theme="dark"] .sidebar {

            background:

                linear-gradient(
                    180deg,
                    rgba(99,102,241,.12),
                    transparent 26%
                ),

                linear-gradient(
                    200deg,
                    #0a1128 0%,
                    #0b132c 44%,
                    #070c1c 100%
                );
        }

        .sidebar-logo {

            position: relative;

            padding: 26px 20px 24px;

            border-bottom:
                1px solid rgba(148,163,255,.08);
        }

        .sidebar-logo::after {

            content: "";

            position: absolute;

            left: 20px;
            right: 20px;
            bottom: -1px;

            height: 2px;

            border-radius: 999px;

            background:
                linear-gradient(
                    90deg,
                    transparent,
                    #6366f1,
                    #22d3ee,
                    transparent
                );

            opacity: .85;
        }

        .sidebar-logo-title {

            font-size: 19px;

            font-weight: 800;

            letter-spacing: -.02em;

            background:
                linear-gradient(
                    100deg,
                    #ffffff 30%,
                    #b9c2ff 68%,
                    #7ee7fb
                );

            -webkit-background-clip: text;

            background-clip: text;

            -webkit-text-fill-color: transparent;

            color: transparent;
        }

        .sidebar-logo-subtitle {

            margin-top: 7px;

            font-size: 10px;

            font-weight: 600;

            color: #7c89b4;

            letter-spacing: .18em;

            text-transform: uppercase;
        }

        .sidebar-section {

            color: #5d6b96;

            letter-spacing: .2em;
        }

        .sidebar-link,
        .user-sidebar-link {

            padding: 11px 13px;

            border-radius: 12px;

            color: #aab4d6;

            font-size: 13px;

            font-weight: 500;
        }

        .sidebar-link:hover,
        .user-sidebar-link:hover {

            background:
                rgba(129,140,248,.10);

            border-color:
                rgba(129,140,248,.16);

            color: #ffffff;

            transform: translateX(3px);
        }

        .sidebar-link:hover .sidebar-link-icon,
        .user-sidebar-link:hover .user-sidebar-link-icon,
        .sidebar-link.active .sidebar-link-icon,
        .user-sidebar-link.active .user-sidebar-link-icon {

            background:
                rgba(255,255,255,.14);

            box-shadow:
                inset 0 0 0 1px rgba(255,255,255,.10);

            color: #ffffff;
        }

        .sidebar-link.active,
        .user-sidebar-link.active {

            background:
                linear-gradient(
                    120deg,
                    rgba(99,102,241,.95),
                    rgba(79,70,229,.88)
                );

            border-color:
                rgba(165,180,252,.28);

            box-shadow:
                0 10px 26px rgba(79,70,229,.38),
                inset 0 1px 0 rgba(255,255,255,.14);
        }

        .sidebar-link.active::before,
        .user-sidebar-link.active::before {

            left: -12px;

            width: 3px;

            background:
                linear-gradient(
                    180deg,
                    #22d3ee,
                    #818cf8
                );

            box-shadow:
                0 0 12px rgba(34,211,238,.75);
        }

        .sidebar-submenu {

            border-left-color:
                rgba(148,163,255,.10);
        }

        .sidebar-sub-link {

            border-radius: 9px;

            color: #8e99c2;

            font-weight: 500;
        }

        .sidebar-sub-link::before {

            background: #39456e;
        }

        .sidebar-sub-link:hover {

            background:
                rgba(129,140,248,.09);

            color: #ffffff;
        }

        .sidebar-sub-link.active {

            background:
                linear-gradient(
                    90deg,
                    rgba(99,102,241,.16),
                    rgba(99,102,241,.04)
                );

            border-color:
                rgba(129,140,248,.18);

            color: #dbe3ff;
        }

        .sidebar-badge {

            background:
                linear-gradient(
                    135deg,
                    #f43f5e,
                    #be123c
                );

            box-shadow:
                0 0 0 3px rgba(244,63,94,.16),
                0 4px 12px rgba(244,63,94,.4);

            animation:
                pch-pulse 2.4s ease-in-out infinite;
        }

        .manager-sidebar-info {

            border-radius: 12px;

            border-color:
                rgba(148,163,255,.10);

            background:
                linear-gradient(
                    160deg,
                    rgba(99,102,241,.10),
                    rgba(34,211,238,.05)
                );

            color: #93a0c8;
        }

        @keyframes pch-pulse {

            0%, 100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.12);
            }
        }


        /* =====================================================
           TOPBAR - GLASS
        ====================================================== */

        .topbar {

            min-height: 76px;

            background:
                rgba(255,255,255,.72);

            border-bottom:
                1px solid rgba(228,232,244,.85);

            backdrop-filter:
                blur(18px) saturate(150%);

            -webkit-backdrop-filter:
                blur(18px) saturate(150%);
        }

        [data-theme="dark"] .topbar {

            background:
                rgba(10,16,36,.72);

            border-bottom-color:
                rgba(32,43,78,.9);
        }

        .page-title {

            position: relative;

            font-size: 21px;

            letter-spacing: -.03em;
        }

        .page-title::after {

            content: "";

            display: block;

            width: 44px;

            height: 3px;

            margin-top: 7px;

            border-radius: 999px;

            background:
                var(--grad-brand);
        }


        /* =====================================================
           CARDS
        ====================================================== */

        .card,
        .dashboard-card,
        .kpi-card,
        .welcome-card,
        .panel,
        .box,
        .table-card,
        .form-card,
        .stat-card {

            border-radius:
                var(--radius-card);

            border-color:
                var(--border);

            box-shadow:
                var(--shadow-sm);

            transition:
                transform .25s cubic-bezier(.2,.7,.3,1),
                box-shadow .25s cubic-bezier(.2,.7,.3,1),
                border-color .25s ease;
        }

        .content .card:hover,
        .content .dashboard-card:hover,
        .content .kpi-card:hover,
        .content .welcome-card:hover,
        .content .panel:hover,
        .content .box:hover,
        .content .table-card:hover,
        .content .form-card:hover,
        .content .stat-card:hover {

            transform:
                translateY(-3px);

            box-shadow:
                var(--shadow-md);

            border-color:
                var(--border-dark);
        }


        /* =====================================================
           ENTRANCE ANIMATION
        ====================================================== */

        @keyframes pch-rise {

            from {
                opacity: 0;
                transform: translateY(14px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .content > * {

            animation:
                pch-rise .45s cubic-bezier(.2,.7,.3,1) both;
        }

        .content > *:nth-child(2) {
            animation-delay: .05s;
        }

        .content > *:nth-child(3) {
            animation-delay: .1s;
        }

        .content > *:nth-child(4) {
            animation-delay: .15s;
        }

        .content > *:nth-child(5) {
            animation-delay: .2s;
        }

        .content > *:nth-child(n+6) {
            animation-delay: .25s;
        }


        /* =====================================================
           TABLE
        ====================================================== */

        table {

            border-radius: 14px;

            border-color:
                var(--border);

            box-shadow:
                var(--shadow-sm);
        }

        table thead th {

            background:
                linear-gradient(
                    180deg,
                    #f8faff,
                    #eef2fc
                );

            color:
                #46527a;

            font-size: 10.5px;

            letter-spacing: .08em;

            text-transform: uppercase;

            border-right-color:
                var(--border);

            border-bottom-color:
                var(--border-dark);
        }

        table tbody td {

            border-right-color:
                var(--border);

            border-bottom-color:
                var(--border);
        }

        table tbody tr:hover td {

            background:
                linear-gradient(
                    90deg,
                    rgba(99,102,241,.06),
                    rgba(34,211,238,.04)
                );
        }

        [data-theme="dark"] table {

            background:
                var(--card);

            border-color:
                var(--border);
        }

        [data-theme="dark"] table thead th {

            background:
                linear-gradient(
                    180deg,
                    #141d40,
                    #101833
                );

            color:
                #aeb9de;

            border-right-color:
                var(--border);

            border-bottom-color:
                var(--border-dark);
        }

        [data-theme="dark"] table tbody td {

            background:
                transparent;

            color:
                var(--text-2);

            border-right-color:
                var(--border);

            border-bottom-color:
                var(--border);
        }

        [data-theme="dark"] table tbody tr:hover td {

            background:
                linear-gradient(
                    90deg,
                    rgba(99,102,241,.14),
                    rgba(34,211,238,.07)
                );
        }


        /* =====================================================
           INPUT
        ====================================================== */

        input,
        select,
        textarea {

            border-radius:
                var(--radius-input);

            border-color:
                var(--border);

            transition:
                border-color .2s ease,
                box-shadow .2s ease,
                background .2s ease;
        }

        input:focus,
        select:focus,
        textarea:focus {

            border-color:
                #818cf8;

            box-shadow:
                0 0 0 4px
                rgba(99,102,241,.14);
        }

        [data-theme="dark"] input,
        [data-theme="dark"] select,
        [data-theme="dark"] textarea {

            background:
                var(--card-soft);

            border-color:
                var(--border);

            color:
                var(--text);
        }


        /* =====================================================
           PDS FORM SYSTEM - TOKEN WARNA FORM (LIGHT + DARK)
           =====================================================
           Dipakai seluruh halaman form (work order, inventory,
           master, sistem) agar Light/Dark mode konsisten.
           Nilai di-overwrite oleh [data-theme="dark"].
        ====================================================== */

        :root {

            --pds-card: #ffffff;

            --pds-soft: #f8fafc;

            --pds-soft-2: #f3f4f6;

            --pds-line: #e5e7eb;

            --pds-line-2: #d1d5db;

            --pds-ink: #111827;

            --pds-ink-2: #374151;

            --pds-muted: #6b7280;

            --pds-muted-2: #9ca3af;

            --pds-accent: #2563eb;
        }

        [data-theme="dark"] {

            --pds-card: #131c31;

            --pds-soft: #182342;

            --pds-soft-2: #1d2a4d;

            --pds-line: #2a3858;

            --pds-line-2: #33436c;

            --pds-ink: #e6ecf9;

            --pds-ink-2: #c7d2e8;

            --pds-muted: #93a3c0;

            --pds-muted-2: #7c8db0;

            --pds-accent: #3b82f6;
        }

        /* Placeholder tetap terbaca di dark mode */

        [data-theme="dark"] input::placeholder,
        [data-theme="dark"] textarea::placeholder {

            color:
                var(--pds-muted-2);

            opacity: 1;
        }

        /* Elemen form tidak boleh meluber dari container */

        input,
        select,
        textarea {

            max-width: 100%;
        }

        textarea {

            height: auto;
        }

        input:disabled,
        select:disabled,
        textarea:disabled {

            cursor: not-allowed;
        }

        button:focus-visible,
        a:focus-visible {

            outline:
                2px solid #818cf8;

            outline-offset: 2px;
        }


        /* =====================================================
           BUTTON SYSTEM (SEBELUMNYA TAK BERSTYLE)
        ====================================================== */

        .btn {

            display:
                inline-flex;

            align-items:
                center;

            justify-content:
                center;

            gap: 7px;

            min-height: 38px;

            padding:
                0 16px;

            border:
                1px solid transparent;

            border-radius:
                11px;

            font-size:
                12.5px;

            font-weight:
                700;

            letter-spacing:
                .01em;

            cursor:
                pointer;

            white-space:
                nowrap;

            position:
                relative;

            overflow:
                hidden;

            transition:
                transform .18s ease,
                box-shadow .18s ease,
                background .18s ease,
                border-color .18s ease,
                color .18s ease;
        }

        .btn:hover {

            transform:
                translateY(-1px);
        }

        .btn:active {

            transform:
                translateY(0) scale(.985);
        }

        .btn.btn-primary,
        .btn.btn-save {

            background:
                var(--grad-brand);

            color:
                #ffffff;

            box-shadow:
                0 8px 20px rgba(99,102,241,.32);
        }

        .btn.btn-primary:hover,
        .btn.btn-save:hover {

            box-shadow:
                0 12px 26px rgba(99,102,241,.42);
        }

        .btn.btn-danger {

            background:
                linear-gradient(
                    135deg,
                    #f43f5e,
                    #dc2626
                );

            color:
                #ffffff;

            box-shadow:
                0 8px 20px rgba(239,68,68,.30);
        }

        .btn.btn-danger:hover {

            box-shadow:
                0 12px 26px rgba(239,68,68,.40);
        }

        .btn.btn-warning {

            background:
                linear-gradient(
                    135deg,
                    #fbbf24,
                    #f59e0b
                );

            color:
                #451a03;

            box-shadow:
                0 8px 20px rgba(245,158,11,.30);
        }

        .btn.btn-gray,
        .btn.btn-back {

            background:
                var(--card);

            border-color:
                var(--border-dark);

            color:
                var(--text-2);

            box-shadow:
                var(--shadow-sm);
        }

        .btn.btn-gray:hover,
        .btn.btn-back:hover {

            border-color:
                #818cf8;

            color:
                var(--primary);
        }

        .btn.btn-ghost {

            background:
                transparent;

            border-color:
                var(--border);

            color:
                var(--muted);
        }

        .btn.btn-ghost:hover {

            background:
                var(--primary-soft);

            border-color:
                rgba(99,102,241,.35);

            color:
                var(--primary);
        }

        [data-theme="dark"] .btn.btn-gray,
        [data-theme="dark"] .btn.btn-back {

            background:
                var(--card-soft);

            color:
                var(--text-2);
        }

        [data-theme="dark"] .btn.btn-ghost {

            color:
                var(--muted);
        }


        /* RIPPLE SPAWN */
        .btn,
        .sidebar-link,
        .user-sidebar-link,
        .account-menu-link {

            position: relative;

            overflow: hidden;
        }

        .pch-ripple {

            position:
                absolute;

            border-radius:
                50%;

            pointer-events:
                none;

            background:
                rgba(255,255,255,.35);

            transform:
                scale(0);

            animation:
                pch-ripple .55s ease-out forwards;
        }

        .btn.btn-gray .pch-ripple,
        .btn.btn-back .pch-ripple,
        .btn.btn-ghost .pch-ripple {

            background:
                rgba(99,102,241,.22);
        }

        @keyframes pch-ripple {

            to {
                transform: scale(2.6);
                opacity: 0;
            }
        }


        /* =====================================================
           BADGE & STATUS PILL
        ====================================================== */

        .badge.on {

            background:
                rgba(16,185,129,.14);

            color:
                #059669;

            box-shadow:
                inset 0 0 0 1px rgba(16,185,129,.28);
        }

        .badge.off {

            background:
                rgba(100,116,139,.13);

            color:
                #64748b;

            box-shadow:
                inset 0 0 0 1px rgba(100,116,139,.24);
        }

        .status-active {

            display:
                inline-flex;

            align-items:
                center;

            gap: 6px;

            padding:
                4px 11px;

            border-radius:
                999px;

            background:
                rgba(16,185,129,.14);

            color:
                #059669;

            font-size:
                11px;

            font-weight:
                700;

            box-shadow:
                inset 0 0 0 1px rgba(16,185,129,.28);
        }

        .status-inactive {

            display:
                inline-flex;

            align-items:
                center;

            gap: 6px;

            padding:
                4px 11px;

            border-radius:
                999px;

            background:
                rgba(239,68,68,.12);

            color:
                #dc2626;

            font-size:
                11px;

            font-weight:
                700;

            box-shadow:
                inset 0 0 0 1px rgba(239,68,68,.26);
        }

        [data-theme="dark"] .status-active {

            background:
                rgba(16,185,129,.16);

            color:
                #6ee7b7;
        }

        [data-theme="dark"] .status-inactive {

            background:
                rgba(239,68,68,.16);

            color:
                #fca5a5;
        }


        /* =====================================================
           MESSAGE & TOAST
        ====================================================== */

        .message {

            border-radius: 13px;

            backdrop-filter:
                blur(8px);

            -webkit-backdrop-filter:
                blur(8px);
        }

        .message.success {

            background:
                linear-gradient(
                    120deg,
                    rgba(16,185,129,.12),
                    rgba(16,185,129,.05)
                );

            border:
                1px solid rgba(16,185,129,.32);

            color:
                #047857;
        }

        .message.error {

            background:
                linear-gradient(
                    120deg,
                    rgba(239,68,68,.12),
                    rgba(239,68,68,.05)
                );

            border:
                1px solid rgba(239,68,68,.32);

            color:
                #b91c1c;
        }

        [data-theme="dark"] .message.success {

            background:
                linear-gradient(
                    120deg,
                    rgba(16,185,129,.16),
                    rgba(16,185,129,.06)
                );

            color:
                #6ee7b7;
        }

        [data-theme="dark"] .message.error {

            background:
                linear-gradient(
                    120deg,
                    rgba(239,68,68,.16),
                    rgba(239,68,68,.06)
                );

            color:
                #fca5a5;
        }

        .crud-toast {

            border-radius: 14px;

            backdrop-filter:
                blur(14px);

            -webkit-backdrop-filter:
                blur(14px);

            animation:
                pch-toast-in .4s cubic-bezier(.2,.7,.3,1);

            transition:
                opacity .35s ease,
                transform .35s ease;
        }

        .crud-toast.success {

            border-left:
                4px solid #10b981;

            box-shadow:
                0 0 0 1px rgba(16,185,129,.14),
                var(--shadow-md);
        }

        .crud-toast.error {

            border-left:
                4px solid #ef4444;

            box-shadow:
                0 0 0 1px rgba(239,68,68,.14),
                var(--shadow-md);
        }

        .crud-toast.pch-hide {

            opacity:
                0;

            transform:
                translateX(24px);
        }

        @keyframes pch-toast-in {

            from {
                opacity: 0;
                transform: translateX(30px) scale(.97);
            }

            to {
                opacity: 1;
                transform: translateX(0) scale(1);
            }
        }

        [data-theme="dark"] .crud-toast {

            background:
                rgba(14,21,48,.92);
        }


        /* =====================================================
           ACCOUNT DROPDOWN
        ====================================================== */

        .account-button {

            border-radius:
                13px;

            border-color:
                var(--border);

            background:
                rgba(255,255,255,.66);

            backdrop-filter:
                blur(10px);

            -webkit-backdrop-filter:
                blur(10px);
        }

        .account-wrapper.open .account-button {

            border-color:
                rgba(99,102,241,.45);

            box-shadow:
                0 0 0 4px rgba(99,102,241,.12);
        }

        .account-avatar {

            background:
                var(--grad-brand);

            box-shadow:
                0 6px 16px rgba(99,102,241,.35);
        }

        .account-dropdown {

            border-radius:
                16px;

            border-color:
                var(--border);

            box-shadow:
                var(--shadow-lg);

            animation:
                pch-pop .28s cubic-bezier(.2,.7,.3,1);

            transform-origin:
                top right;
        }

        @keyframes pch-pop {

            from {
                opacity: 0;
                transform: translateY(-8px) scale(.96);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        [data-theme="dark"] .account-button {

            background:
                rgba(19,28,61,.66);

            border-color:
                var(--border);
        }


        /* =====================================================
           THEME TOGGLE
        ====================================================== */

        .theme-toggle {

            border-radius:
                11px;

            border-color:
                var(--border);

            background:
                rgba(255,255,255,.66);

            backdrop-filter:
                blur(10px);

            -webkit-backdrop-filter:
                blur(10px);
        }

        .theme-toggle:hover {

            border-color:
                rgba(99,102,241,.5);

            color:
                var(--primary);

            box-shadow:
                0 0 0 4px rgba(99,102,241,.10);
        }

        [data-theme="dark"] .theme-toggle {

            background:
                rgba(19,28,61,.66);

            color:
                var(--text);
        }


        /* =====================================================
           AI ASSISTANT
        ====================================================== */

        .ai-assistant-button {

            background:
                var(--grad-brand);

            border-width: 0;

            outline:
                3px solid rgba(255,255,255,.9);

            font-size: 24px;

            box-shadow:
                0 14px 34px rgba(99,102,241,.45);
        }

        .ai-assistant-button::after {

            content: "";

            position:
                absolute;

            inset:
                -3px;

            border-radius:
                50%;

            border:
                2px solid
                rgba(99,102,241,.5);

            animation:
                pch-ring 2.6s ease-out infinite;
        }

        @keyframes pch-ring {

            0% {
                transform: scale(1);
                opacity: .8;
            }

            70%, 100% {
                transform: scale(1.45);
                opacity: 0;
            }
        }

        .ai-assistant-panel {

            border-radius:
                22px;

            border-color:
                var(--border);

            box-shadow:
                var(--shadow-lg);

            animation:
                pch-pop .3s cubic-bezier(.2,.7,.3,1);

            transform-origin:
                bottom right;
        }

        .ai-assistant-header {

            background:
                linear-gradient(
                    135deg,
                    #10173a,
                    #1c1450 55%,
                    #0e3a54
                );
        }

        .ai-assistant-header-icon {

            background:
                var(--grad-brand);

            box-shadow:
                0 6px 18px rgba(99,102,241,.45);
        }

        .ai-message.user .ai-bubble {

            background:
                var(--grad-brand);

            box-shadow:
                0 8px 20px rgba(99,102,241,.30);
        }

        .ai-assistant-send {

            background:
                var(--grad-brand);

            border-radius:
                999px;

            box-shadow:
                0 8px 18px rgba(99,102,241,.32);
        }

        .ai-table-wrap {

            border-radius:
                13px;

            border-color:
                var(--border);

            box-shadow:
                var(--shadow-sm);
        }

        .ai-table-wrap:hover {

            border-color:
                rgba(129,140,248,.55);

            box-shadow:
                0 12px 30px rgba(99,102,241,.14);
        }

        [data-theme="dark"] .ai-assistant-header {

            background:
                linear-gradient(
                    135deg,
                    #0c1230,
                    #171040 55%,
                    #0a2c40
                );
        }


        /* =====================================================
           SCROLLBAR
        ====================================================== */

        ::-webkit-scrollbar {

            width: 8px;

            height: 8px;
        }

        ::-webkit-scrollbar-track {

            background:
                transparent;
        }

        ::-webkit-scrollbar-thumb {

            background:
                linear-gradient(
                    180deg,
                    #c7cdf0,
                    #aab3e8
                );

            border-radius:
                999px;

            border:
                none;
        }

        ::-webkit-scrollbar-thumb:hover {

            background:
                #818cf8;
        }

        [data-theme="dark"] ::-webkit-scrollbar-thumb {

            background:
                #2c3a6b;
        }

        [data-theme="dark"] ::-webkit-scrollbar-thumb:hover {

            background:
                #4c5bb0;
        }


        /* =====================================================
           TABLE ACTION BUTTONS
           Eye / Edit / Delete tetap rapi dan sejajar.
        ====================================================== */

        .table-actions,
        .action-buttons,
        .actions,
        .aksi,
        table td:last-child .btn-group {

            display: inline-flex;

            align-items: center;

            justify-content: center;

            gap: 7px;

            flex-wrap: nowrap;

            white-space: nowrap;
        }

        table td:last-child {

            white-space: nowrap;
        }

        table td:last-child .btn.btn-sm,
        table td:last-child a.btn.btn-sm,
        table td:last-child button.btn.btn-sm {

            width: 34px;

            min-width: 34px;

            height: 34px;

            min-height: 34px;

            padding: 0;

            margin: 0;

            gap: 0;

            border-radius: 9px;

            display: inline-flex;

            align-items: center;

            justify-content: center;

            line-height: 1;

            flex: 0 0 34px;
        }

        table td:last-child .btn.btn-sm i,
        table td:last-child .btn.btn-sm svg,
        table td:last-child a.btn.btn-sm i,
        table td:last-child button.btn.btn-sm i {

            width: 15px;

            height: 15px;

            font-size: 14px;

            line-height: 1;

            display: inline-flex;

            align-items: center;

            justify-content: center;

            margin: 0;
        }

        table td:last-child .btn.btn-sm:has(.fa-eye),
        table td:last-child .btn.btn-sm:has(.fa-eye-slash) {

            background: #eff6ff;

            border-color: #bfdbfe;

            color: #2563eb;
        }

        table td:last-child .btn.btn-sm:has(.fa-edit),
        table td:last-child .btn.btn-sm:has(.fa-pencil),
        table td:last-child .btn.btn-sm:has(.fa-pen) {

            background: #f5f3ff;

            border-color: #ddd6fe;

            color: #7c3aed;
        }

        table td:last-child .btn.btn-sm:has(.fa-trash),
        table td:last-child .btn.btn-sm:has(.fa-trash-alt),
        table td:last-child .btn.btn-sm:has(.fa-delete-left) {

            background: #fef2f2;

            border-color: #fecaca;

            color: #dc2626;
        }

        table td:last-child .btn.btn-sm:has(.fa-eye):hover,
        table td:last-child .btn.btn-sm:has(.fa-edit):hover,
        table td:last-child .btn.btn-sm:has(.fa-pencil):hover,
        table td:last-child .btn.btn-sm:has(.fa-pen):hover,
        table td:last-child .btn.btn-sm:has(.fa-trash):hover,
        table td:last-child .btn.btn-sm:has(.fa-trash-alt):hover,
        table td:last-child .btn.btn-sm:has(.fa-delete-left):hover {

            transform: translateY(-1px);

            box-shadow: 0 6px 14px rgba(15,23,42,.10);
        }

        [data-theme="dark"] table td:last-child .btn.btn-sm:has(.fa-eye) {

            background: rgba(37,99,235,.16);

            border-color: rgba(96,165,250,.28);

            color: #93c5fd;
        }

        [data-theme="dark"] table td:last-child .btn.btn-sm:has(.fa-edit),
        [data-theme="dark"] table td:last-child .btn.btn-sm:has(.fa-pencil),
        [data-theme="dark"] table td:last-child .btn.btn-sm:has(.fa-pen) {

            background: rgba(124,58,237,.16);

            border-color: rgba(167,139,250,.28);

            color: #c4b5fd;
        }

        [data-theme="dark"] table td:last-child .btn.btn-sm:has(.fa-trash),
        [data-theme="dark"] table td:last-child .btn.btn-sm:has(.fa-trash-alt),
        [data-theme="dark"] table td:last-child .btn.btn-sm:has(.fa-delete-left) {

            background: rgba(220,38,38,.16);

            border-color: rgba(248,113,113,.28);

            color: #fca5a5;
        }


        /* =====================================================
           RESPONSIVE GUARD
        ====================================================== */

        @media (max-width: 900px) {

            .sidebar {

                width: 232px;
            }

            .main {

                width:
                    calc(100% - 232px);

                margin-left:
                    232px;
            }
        }

        @media (prefers-reduced-motion: reduce) {

            *,
            *::before,
            *::after {

                animation-duration: .01ms !important;

                animation-iteration-count: 1 !important;

                transition-duration: .01ms !important;
            }
        }


        /* =====================================================
           PACHIRA SIDEBAR TOGGLE - FINAL
           Tidak membuat menu baru / tidak membuat overlay "Semua Menu".
           Hamburger hanya mengontrol sidebar yang sudah ada.
        ====================================================== */

        .pch-burger {
            display: inline-flex !important;
            width: 42px;
            height: 42px;
            margin-right: 12px;
            flex: 0 0 42px;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            gap: 5px;
            border: 1px solid #dbe3ed;
            border-radius: 11px;
            background: #ffffff;
            color: #334155;
            cursor: pointer;
            position: relative;
            z-index: 1105;
            transition: .2s ease;
        }

        .pch-burger:hover {
            border-color: #93c5fd;
            color: #1d4ed8;
            box-shadow: 0 5px 16px rgba(15,23,42,.10);
        }

        .pch-burger span {
            display: block;
            width: 19px;
            height: 2px;
            border-radius: 999px;
            background: currentColor;
            transition: transform .22s ease, opacity .18s ease;
        }

        .pch-burger.pch-open span:nth-child(1) {
            transform: translateY(7px) rotate(45deg);
        }

        .pch-burger.pch-open span:nth-child(2) {
            opacity: 0;
        }

        .pch-burger.pch-open span:nth-child(3) {
            transform: translateY(-7px) rotate(-45deg);
        }

        .pch-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 995;
            background: rgba(7,11,24,.62);
            backdrop-filter: blur(2px);
            -webkit-backdrop-filter: blur(2px);
            opacity: 0;
            pointer-events: none;
            transition: opacity .22s ease;
        }

        .pch-backdrop.pch-show {
            opacity: 1;
            pointer-events: auto;
        }

        /* DESKTOP: sidebar asli tetap ada. Hamburger hanya hide/show sidebar. */
        .sidebar {
            transform: translate3d(0,0,0);
            transition: transform .25s ease;
        }

        body.pch-sidebar-collapsed .sidebar {
            transform: translate3d(-105%,0,0);
        }

        body.pch-sidebar-collapsed .main {
            width: 100%;
            margin-left: 0;
        }

        /* MOBILE: sidebar menjadi drawer kiri. */
        @media (max-width: 700px) {

            body.pch-drawer-open {
                overflow: hidden;
            }

            .topbar {
                min-height: 68px;
                padding: 0 14px;
            }

            .topbar-left {
                min-width: 0;
                flex: 1 1 auto;
            }

            .topbar-title-wrap {
                min-width: 0;
                max-width: calc(100vw - 150px);
            }

            .topbar-title-wrap .page-title {
                font-size: 17px;
            }

            .topbar-title-wrap .page-subtitle {
                font-size: 9px;
                margin-top: 3px;
            }

            .sidebar {
                display: flex !important;
                flex-direction: column;
                position: fixed !important;
                top: 0 !important;
                left: 0 !important;
                bottom: 0 !important;
                width: 292px !important;
                max-width: 86vw;
                height: 100vh;
                transform: translate3d(-110%,0,0);
                visibility: hidden;
                pointer-events: none;
                overflow-x: hidden;
                overflow-y: auto;
                transition: transform .28s cubic-bezier(.2,.7,.3,1), visibility 0s linear .28s;
                box-shadow: 28px 0 80px rgba(5,8,20,.50);
                z-index: 1005;
                will-change: transform;
            }

            .sidebar.pch-open {
                transform: translate3d(0,0,0) !important;
                visibility: visible;
                pointer-events: auto;
                transition: transform .28s cubic-bezier(.2,.7,.3,1), visibility 0s linear 0s;
            }

            .sidebar-logo {
                flex: 0 0 auto;
            }

            .sidebar-menu {
                padding-bottom: 40px;
            }

            .main,
            body.pch-sidebar-collapsed .main {
                width: 100% !important;
                margin-left: 0 !important;
            }

            .pch-backdrop {
                display: block;
            }

            body.pch-sidebar-collapsed .sidebar {
                transform: translate3d(-110%,0,0) !important;
            }
        }

        [data-theme="dark"] .pch-burger {
            background: #111a2e;
            border-color: #24304d;
            color: #e2e8f0;
        }

        /* =====================================================
           IKON AKSI TABEL - FINAL
           Eye = lihat, pencil = edit, trash = hapus.
        ====================================================== */

        table td:last-child {
            white-space: nowrap !important;
        }

        table td:last-child .table-actions,
        table td:last-child .action-buttons,
        table td:last-child .actions,
        table td:last-child .aksi,
        table td:last-child .btn-group,
        table td:last-child > div:has(> .btn) {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            flex-wrap: nowrap;
            white-space: nowrap;
        }

        table td:last-child .btn.btn-sm,
        table td:last-child a.btn.btn-sm,
        table td:last-child button.btn.btn-sm {
            width: 34px !important;
            min-width: 34px !important;
            height: 34px !important;
            min-height: 34px !important;
            padding: 0 !important;
            margin: 0 !important;
            border-radius: 9px !important;
            display: inline-flex !important;
            align-items: center;
            justify-content: center;
            line-height: 1 !important;
            flex: 0 0 34px !important;
            font-size: 0 !important;
        }

        table td:last-child .btn.btn-sm i,
        table td:last-child .btn.btn-sm svg,
        table td:last-child .btn.btn-sm span {
            width: 15px;
            height: 15px;
            font-size: 14px !important;
            line-height: 1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin: 0 !important;
        }

        table td:last-child .btn.btn-sm:has(.fa-eye),
        table td:last-child .btn.btn-sm:has(.fa-eye-slash),
        table td:last-child .btn.btn-sm[aria-label*="Lihat"],
        table td:last-child .btn.btn-sm[title*="Lihat"] {
            background: #eff6ff !important;
            border: 1px solid #bfdbfe !important;
            color: #2563eb !important;
        }

        table td:last-child .btn.btn-sm:has(.fa-edit),
        table td:last-child .btn.btn-sm:has(.fa-pencil),
        table td:last-child .btn.btn-sm:has(.fa-pen),
        table td:last-child .btn.btn-sm[aria-label*="Edit"],
        table td:last-child .btn.btn-sm[title*="Edit"] {
            background: #f5f3ff !important;
            border: 1px solid #ddd6fe !important;
            color: #7c3aed !important;
        }

        table td:last-child .btn.btn-sm:has(.fa-trash),
        table td:last-child .btn.btn-sm:has(.fa-trash-alt),
        table td:last-child .btn.btn-sm:has(.fa-delete-left),
        table td:last-child .btn.btn-sm[aria-label*="Hapus"],
        table td:last-child .btn.btn-sm[title*="Hapus"] {
            background: #fef2f2 !important;
            border: 1px solid #fecaca !important;
            color: #dc2626 !important;
        }

        table td:last-child .btn.btn-sm:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 14px rgba(15,23,42,.10);
        }

        [data-theme="dark"] table td:last-child .btn.btn-sm:has(.fa-eye),
        [data-theme="dark"] table td:last-child .btn.btn-sm[aria-label*="Lihat"] {
            background: rgba(37,99,235,.16) !important;
            border-color: rgba(96,165,250,.28) !important;
            color: #93c5fd !important;
        }

        [data-theme="dark"] table td:last-child .btn.btn-sm:has(.fa-edit),
        [data-theme="dark"] table td:last-child .btn.btn-sm:has(.fa-pencil),
        [data-theme="dark"] table td:last-child .btn.btn-sm:has(.fa-pen),
        [data-theme="dark"] table td:last-child .btn.btn-sm[aria-label*="Edit"] {
            background: rgba(124,58,237,.16) !important;
            border-color: rgba(167,139,250,.28) !important;
            color: #c4b5fd !important;
        }

        [data-theme="dark"] table td:last-child .btn.btn-sm:has(.fa-trash),
        [data-theme="dark"] table td:last-child .btn.btn-sm:has(.fa-trash-alt),
        [data-theme="dark"] table td:last-child .btn.btn-sm:has(.fa-delete-left),
        [data-theme="dark"] table td:last-child .btn.btn-sm[aria-label*="Hapus"] {
            background: rgba(220,38,38,.16) !important;
            border-color: rgba(248,113,113,.28) !important;
            color: #fca5a5 !important;
        }

        /* =====================================================
           SAFETY: HILANGKAN "SEMUA MENU" FULLSCREEN
           Sidebar asli tetap menjadi satu-satunya menu.
        ====================================================== */
        .pch-all-menu,
        #pchAllMenu,
        .all-menu-overlay,
        #allMenuOverlay,
        .all-menu-modal,
        #allMenuModal {
            display: none !important;
        }

        /* =====================================================
           TABLE RESPONSIVE SCROLL
        ====================================================== */

        .pch-scroll {

            width:
                100%;

            overflow-x:
                auto;

            -webkit-overflow-scrolling:
                touch;

            scrollbar-width:
                thin;
        }

        canvas {

            max-width:
                100%;
        }

        svg {

            max-width:
                100%;
        }


        /* =====================================================
           CONFIRM MODAL
        ====================================================== */

        .pch-modal-backdrop {

            position:
                fixed;

            inset:
                0;

            z-index:
                300000;

            display:
                flex;

            align-items:
                center;

            justify-content:
                center;

            padding:
                20px;

            background:
                rgba(7,11,24,.62);

            backdrop-filter:
                blur(6px);

            -webkit-backdrop-filter:
                blur(6px);

            opacity:
                0;

            visibility:
                hidden;

            transition:
                opacity .25s ease,
                visibility .25s ease;
        }

        .pch-modal-backdrop.pch-show {

            opacity:
                1;

            visibility:
                visible;
        }

        .pch-modal {

            width:
                100%;

            max-width:
                400px;

            padding:
                26px 24px 22px;

            text-align:
                center;

            border-radius:
                20px;

            background:
                var(--card);

            border:
                1px solid var(--border);

            box-shadow:
                var(--shadow-lg);

            transform:
                translateY(14px) scale(.96);

            transition:
                transform .28s cubic-bezier(.2,.7,.3,1);
        }

        .pch-modal-backdrop.pch-show .pch-modal {

            transform:
                translateY(0) scale(1);
        }

        .pch-modal-icon {

            width:
                58px;

            height:
                58px;

            margin:
                0 auto 16px;

            display:
                flex;

            align-items:
                center;

            justify-content:
                center;

            border-radius:
                18px;

            font-size:
                26px;

            background:
                linear-gradient(
                    135deg,
                    rgba(239,68,68,.16),
                    rgba(239,68,68,.06)
                );

            box-shadow:
                inset 0 0 0 1px rgba(239,68,68,.28);
        }

        .pch-modal-title {

            font-size:
                16px;

            font-weight:
                800;

            color:
                var(--text);

            letter-spacing:
                -.01em;
        }

        .pch-modal-text {

            margin-top:
                9px;

            font-size:
                13px;

            line-height:
                1.6;

            color:
                var(--muted);

            word-break:
                break-word;
        }

        .pch-modal-actions {

            margin-top:
                22px;

            display:
                flex;

            gap:
                10px;
        }

        .pch-modal-actions .btn {

            flex:
                1;

            min-height:
                44px;

            border-radius:
                12px;
        }

    </style>

    @stack('styles')

</head>

<body>

<div
    id="crudToastContainer"
    class="crud-toast-container"
>

    @if(session('success'))

        <div class="crud-toast success">
            BERHASIL: {{ session('success') }}
        </div>

    @endif

    @if(session('error'))

        <div class="crud-toast error">
            GAGAL: {{ session('error') }}
        </div>

    @endif

</div>


@php

    $rawRole =
        auth()->check()
            ? trim(auth()->user()->role ?? '')
            : '';

    $normalizedRole =
        \App\Support\DepartmentAccess::normalizeRole($rawRole);

    $isAdministrator =
        $normalizedRole === \App\Support\DepartmentAccess::ADMINISTRATOR;

    $isManager =
        $normalizedRole === \App\Support\DepartmentAccess::MANAGER;

    $isDirektur =
        $normalizedRole === \App\Support\DepartmentAccess::DIREKTUR;

    $isProduksi =
        $normalizedRole === \App\Support\DepartmentAccess::PRODUKSI;

    $isMaintenance =
        $normalizedRole === \App\Support\DepartmentAccess::MAINTENANCE;

    $isMekanikMaint =
        $normalizedRole === \App\Support\DepartmentAccess::MEKANIK_MAINT;

    $isPrevMaint =
        $normalizedRole === \App\Support\DepartmentAccess::PREV_MAINT;

    $isMaintenanceStaff =
        $isMaintenance
        || $isMekanikMaint
        || $isPrevMaint;

    $userRole = strtoupper($rawRole);


    $workOrderUnreadCount = 0;

    if (
        auth()->check() &&
        class_exists(
            \App\Models\Notification::class
        )
    ) {

        $workOrderUnreadCount =
            \App\Models\Notification::where(
                'user_id',
                auth()->id()
            )
            ->where(
                'status',
                'UNREAD'
            )
            ->whereNotNull(
                'work_order_id'
            )
            ->count();
    }


    $allUnreadCount = 0;

    if (
        auth()->check() &&
        class_exists(
            \App\Models\Notification::class
        )
    ) {

        $allUnreadCount =
            \App\Models\Notification::where(
                'user_id',
                auth()->id()
            )
            ->where(
                'status',
                'UNREAD'
            )
            ->count();
    }


    $inventoryActive =
        request()->routeIs(
            'inventory.index',
            'barang.index',
            'barang-masuk.*',
            'barang-keluar.*',
            'barang.restock',
            'purchase-requests.*',
            'laporan-harian.*',
            'rata-rata-pemakaian.*'
        );


    $userWorkOrderActive =
        request()->routeIs(
            'work-orders.index',
            'work-orders.create',
            'work-orders.show',
            'work-orders.edit',
            'work-orders.update',
            'work-orders.update.patch'
        );


    $maintenanceWorkOrderActive =
        request()->routeIs(
            'work-orders.maintenance',
            'work-orders.maintenance.*'
        );


    $adminWorkOrderActive =
        request()->routeIs(
            'work-orders.admin.*',
            'work-orders.report'
        );


    $masterActive =
        request()->routeIs(
            'areas.*',
            'machines.*',
            'machine-spareparts.*'
        );

@endphp


<div class="app">

    <div
        id="pchBackdrop"
        class="pch-backdrop"
        aria-hidden="true"
    ></div>

    <aside class="sidebar">

            <div class="sidebar-logo">

                <div class="sidebar-logo-title">
                    PACHIRA DISTRINUSA
                </div>

                <div class="sidebar-logo-subtitle">
                    PACHIRA MAINTENANCE SYSTEM
                </div>

        </div>


        {{-- =====================================================
             USER
        ====================================================== --}}

        @if($isProduksi)

            <nav class="sidebar-menu">

                <div class="sidebar-section">
                    MENU
                </div>


                {{-- DASHBOARD --}}

                <a
                    href="{{ route('dashboard') }}"
                    class="user-sidebar-link {{
                        request()->routeIs('dashboard')
                            ? 'active'
                            : ''
                    }}"
                >

                    <span class="user-sidebar-link-icon">

                        <svg
                            viewBox="0 0 24 24"
                            aria-hidden="true"
                        >
                            <rect
                                x="3"
                                y="3"
                                width="7"
                                height="7"
                                rx="1"
                            />
                            <rect
                                x="14"
                                y="3"
                                width="7"
                                height="7"
                                rx="1"
                            />
                            <rect
                                x="3"
                                y="14"
                                width="7"
                                height="7"
                                rx="1"
                            />
                            <rect
                                x="14"
                                y="14"
                                width="7"
                                height="7"
                                rx="1"
                            />
                        </svg>

                    </span>

                    <span>
                        Dashboard
                    </span>

                </a>


                <div class="sidebar-section">
                    WORK ORDER
                </div>


                {{-- WORK ORDER LIST --}}

                <a
                    href="{{ route('work-orders.index') }}"
                    class="user-sidebar-link {{
                        $userWorkOrderActive
                            ? 'active'
                            : ''
                    }}"
                >

                    <span class="user-sidebar-link-icon">

                        <svg
                            viewBox="0 0 24 24"
                            aria-hidden="true"
                        >
                            <path d="M9 11h6" />
                            <path d="M9 15h6" />
                            <path d="M9 7h3" />
                            <path d="M6 3h9l3 3v15H6z" />
                            <path d="M15 3v4h4" />
                        </svg>

                    </span>

                    <span>
                        Work Order Saya
                    </span>

                </a>


                {{-- BUAT WORK ORDER --}}

                <a
                    href="{{ route('work-orders.create') }}"
                    class="user-sidebar-link {{
                        request()->routeIs('work-orders.create')
                            ? 'active'
                            : ''
                    }}"
                >

                    <span class="user-sidebar-link-icon">

                        <svg
                            viewBox="0 0 24 24"
                            aria-hidden="true"
                        >
                            <path d="M12 5v14" />
                            <path d="M5 12h14" />
                        </svg>

                    </span>

                    <span>
                        Buat Work Order
                    </span>

                </a>

            </nav>


        {{-- =====================================================
             MANAGER
        ====================================================== --}}

        @elseif($isManager)

            <nav class="sidebar-menu">

                <div class="sidebar-section">
                    MENU
                </div>


                <a
                    href="{{ route('dashboard.manager') }}"
                    class="sidebar-link {{
                        request()->routeIs('dashboard.manager')
                            ? 'active'
                            : ''
                    }}"
                >

                    <span class="sidebar-link-icon">

                        <svg
                            viewBox="0 0 24 24"
                            aria-hidden="true"
                        >
                            <rect
                                x="3"
                                y="3"
                                width="7"
                                height="7"
                                rx="1"
                            />
                            <rect
                                x="14"
                                y="3"
                                width="7"
                                height="7"
                                rx="1"
                            />
                            <rect
                                x="3"
                                y="14"
                                width="7"
                                height="7"
                                rx="1"
                            />
                            <rect
                                x="14"
                                y="14"
                                width="7"
                                height="7"
                                rx="1"
                            />
                        </svg>

                    </span>

                    <span>
                        Dashboard Manager
                    </span>

                </a>


                <div class="manager-sidebar-info">
                    Manager hanya memiliki akses
                    monitoring Dashboard.
                </div>

            </nav>


        {{-- =====================================================
             MAINTENANCE (CS MAINTENANCE)
             Bisa melihat MEKANIK_MAINT + PREV_MAINT
        ====================================================== --}}

        @elseif($isMaintenance)

            <nav class="sidebar-menu">

                <div class="sidebar-section">
                    MENU
                </div>


                {{-- DASHBOARD --}}

                <a
                    href="{{ route('dashboard') }}"
                    class="sidebar-link {{
                        request()->routeIs('dashboard')
                            ? 'active'
                            : ''
                    }}"
                >

                    <span class="sidebar-link-icon">

                        <svg
                            viewBox="0 0 24 24"
                            aria-hidden="true"
                        >
                            <rect
                                x="3"
                                y="3"
                                width="7"
                                height="7"
                                rx="1"
                            />
                            <rect
                                x="14"
                                y="3"
                                width="7"
                                height="7"
                                rx="1"
                            />
                            <rect
                                x="3"
                                y="14"
                                width="7"
                                height="7"
                                rx="1"
                            />
                            <rect
                                x="14"
                                y="14"
                                width="7"
                                height="7"
                                rx="1"
                            />
                        </svg>

                    </span>

                    <span>
                        Dashboard
                    </span>

                </a>


                {{-- WORK ORDER --}}

                <div class="sidebar-section">
                    WORK ORDER
                </div>


                <div
                    class="sidebar-group {{
                        $maintenanceWorkOrderActive
                            ? 'open'
                            : ''
                    }}"
                >

                    <button
                        type="button"
                        class="sidebar-link"
                        onclick="toggleSidebarGroup(this.closest('.sidebar-group'))"
                    >

                        <span class="sidebar-link-icon">

                            <svg
                                viewBox="0 0 24 24"
                                aria-hidden="true"
                            >
                                <path d="M9 11h6" />
                                <path d="M9 15h6" />
                                <path d="M9 7h3" />
                                <path d="M6 3h9l3 3v15H6z" />
                                <path d="M15 3v4h4" />
                            </svg>

                        </span>

                        <span>
                            Work Order
                        </span>


                        @if($workOrderUnreadCount > 0)

                            <span
                                class="sidebar-badge"
                                title="WO belum dibuka"
                            >
                                {{ $workOrderUnreadCount }}
                            </span>

                        @endif


                        <span class="sidebar-chevron"></span>

                    </button>


                    <div class="sidebar-submenu">

                        <a
                            href="{{ route('work-orders.maintenance') }}"
                            class="sidebar-sub-link {{
                                request()->routeIs(
                                    'work-orders.maintenance'
                                ) && !request()->routeIs(
                                    'work-orders.maintenance.mekanik',
                                    'work-orders.maintenance.prev',
                                    'work-orders.maintenance.report'
                                )
                                    ? 'active'
                                    : ''
                            }}"
                        >

                            <span>
                                Semua Work Order
                            </span>


                            @if($workOrderUnreadCount > 0)

                                <span class="sidebar-badge">
                                    {{ $workOrderUnreadCount }}
                                </span>

                            @endif

                        </a>


                        <a
                            href="{{ route('work-orders.maintenance.mekanik') }}"
                            class="sidebar-sub-link {{
                                request()->routeIs(
                                    'work-orders.maintenance.mekanik'
                                )
                                    ? 'active'
                                    : ''
                            }}"
                        >
                            <span>
                                Mekanik & Maint
                            </span>
                        </a>


                        <a
                            href="{{ route('work-orders.maintenance.prev') }}"
                            class="sidebar-sub-link {{
                                request()->routeIs(
                                    'work-orders.maintenance.prev'
                                )
                                    ? 'active'
                                    : ''
                            }}"
                        >
                            <span>
                                Prev-Maint
                            </span>
                        </a>

                    </div>

                </div>


                {{-- MEKANIK & MAINT --}}

                <div class="sidebar-section">
                    MEKANIK & MAINT
                </div>


                <div
                    class="sidebar-group {{
                        request()->routeIs('work-orders.maintenance.mekanik')
                            ? 'open'
                            : ''
                    }}"
                >

                    <button
                        type="button"
                        class="sidebar-link"
                        onclick="toggleSidebarGroup(this.closest('.sidebar-group'))"
                    >

                        <span class="sidebar-link-icon">
                            M
                        </span>

                        <span>
                            Mekanik & Maint
                        </span>

                        <span class="sidebar-chevron"></span>

                    </button>


                    <div class="sidebar-submenu">

                        <a
                            href="{{ route('work-orders.maintenance.mekanik') }}"
                            class="sidebar-sub-link {{
                                request()->routeIs(
                                    'work-orders.maintenance.mekanik'
                                )
                                    ? 'active'
                                    : ''
                            }}"
                        >
                            <span>
                                Work Order
                            </span>
                        </a>


                        <a
                            href="{{ route('barang.index', ['bucket' => 'me_prev']) }}"
                            class="sidebar-sub-link {{
                                request()->routeIs('barang.*')
                                    ? 'active'
                                    : ''
                            }}"
                        >
                            <span>
                                Inventory
                            </span>
                        </a>

                    </div>

                </div>


                {{-- PREV-MAINT --}}

                <div class="sidebar-section">
                    PREV-MAINT
                </div>


                <div
                    class="sidebar-group {{
                        request()->routeIs('work-orders.maintenance.prev')
                            ? 'open'
                            : ''
                    }}"
                >

                    <button
                        type="button"
                        class="sidebar-link"
                        onclick="toggleSidebarGroup(this.closest('.sidebar-group'))"
                    >

                        <span class="sidebar-link-icon">
                            P
                        </span>

                        <span>
                            Prev-Maint
                        </span>

                        <span class="sidebar-chevron"></span>

                    </button>


                    <div class="sidebar-submenu">

                        <a
                            href="{{ route('work-orders.maintenance.prev') }}"
                            class="sidebar-sub-link {{
                                request()->routeIs(
                                    'work-orders.maintenance.prev'
                                )
                                    ? 'active'
                                    : ''
                            }}"
                        >
                            <span>
                                Work Order
                            </span>
                        </a>


                        <a
                            href="{{ route('barang.index', ['bucket' => 'prev']) }}"
                            class="sidebar-sub-link {{
                                request()->routeIs('barang.*')
                                    ? 'active'
                                    : ''
                            }}"
                        >
                            <span>
                                Inventory
                            </span>
                        </a>

                    </div>

                </div>


                {{-- LAPORAN --}}

                <div class="sidebar-section">
                    LAPORAN
                </div>


                <a
                    href="{{ route('work-orders.maintenance.report') }}"
                    class="sidebar-link {{
                        request()->routeIs(
                            'work-orders.maintenance.report'
                        )
                            ? 'active'
                            : ''
                    }}"
                >

                    <span class="sidebar-link-icon">
                        L
                    </span>

                    <span>
                        Laporan Work Order
                    </span>

                </a>


                {{-- MASTER --}}

                <div class="sidebar-section">
                    MASTER
                </div>


                <div
                    class="sidebar-group {{
                        $masterActive
                            ? 'open'
                            : ''
                    }}"
                >

                    <button
                        type="button"
                        class="sidebar-link"
                        onclick="toggleSidebarGroup(this.closest('.sidebar-group'))"
                    >

                        <span class="sidebar-link-icon">

                            <svg
                                viewBox="0 0 24 24"
                                aria-hidden="true"
                            >
                                <path d="M12 3l8 4.5v9L12 21l-8-4.5v-9z" />
                                <path d="M12 12l8-4.5" />
                                <path d="M12 12v9" />
                                <path d="M12 12L4 7.5" />
                            </svg>

                        </span>

                        <span>
                            Master
                        </span>

                        <span class="sidebar-chevron"></span>

                    </button>


                    <div class="sidebar-submenu">

                        <a
                            href="{{ route('areas.index') }}"
                            class="sidebar-sub-link {{
                                request()->routeIs('areas.*')
                                    ? 'active'
                                    : ''
                            }}"
                        >
                            <span>
                                Area
                            </span>
                        </a>


                        <a
                            href="{{ route('machines.index') }}"
                            class="sidebar-sub-link {{
                                request()->routeIs('machines.*')
                                    ? 'active'
                                    : ''
                            }}"
                        >
                            <span>
                                Mesin
                            </span>
                        </a>


                        <a
                            href="{{ route('machine-spareparts.index') }}"
                            class="sidebar-sub-link {{
                                request()->routeIs('machine-spareparts.*')
                                    ? 'active'
                                    : ''
                            }}"
                        >
                            <span>
                                Mesin & Sparepart
                            </span>
                        </a>

                    </div>

                </div>

            </nav>


        {{-- =====================================================
             MEKANIK & MAINT
             Hanya bisa melihat data MEKANIK_MAINT
        ====================================================== --}}

        @elseif($isMekanikMaint)

            <nav class="sidebar-menu">

                <div class="sidebar-section">
                    MENU
                </div>


                {{-- DASHBOARD --}}

                <a
                    href="{{ route('dashboard') }}"
                    class="sidebar-link {{
                        request()->routeIs('dashboard')
                            ? 'active'
                            : ''
                    }}"
                >

                    <span class="sidebar-link-icon">

                        <svg
                            viewBox="0 0 24 24"
                            aria-hidden="true"
                        >
                            <rect
                                x="3"
                                y="3"
                                width="7"
                                height="7"
                                rx="1"
                            />
                            <rect
                                x="14"
                                y="3"
                                width="7"
                                height="7"
                                rx="1"
                            />
                            <rect
                                x="3"
                                y="14"
                                width="7"
                                height="7"
                                rx="1"
                            />
                            <rect
                                x="14"
                                y="14"
                                width="7"
                                height="7"
                                rx="1"
                            />
                        </svg>

                    </span>

                    <span>
                        Dashboard
                    </span>

                </a>


                {{-- WORK ORDER --}}

                <div class="sidebar-section">
                    WORK ORDER
                </div>


                <a
                    href="{{ route('work-orders.maintenance.mekanik') }}"
                    class="sidebar-link {{
                        request()->routeIs(
                            'work-orders.maintenance.mekanik'
                        )
                            ? 'active'
                            : ''
                    }}"
                >

                    <span class="sidebar-link-icon">

                        <svg
                            viewBox="0 0 24 24"
                            aria-hidden="true"
                        >
                            <path d="M9 11h6" />
                            <path d="M9 15h6" />
                            <path d="M9 7h3" />
                            <path d="M6 3h9l3 3v15H6z" />
                            <path d="M15 3v4h4" />
                        </svg>

                    </span>

                    <span>
                        WO Mekanik & Maint
                    </span>


                    @if($workOrderUnreadCount > 0)

                        <span
                            class="sidebar-badge"
                            title="WO belum dibuka"
                        >
                            {{ $workOrderUnreadCount }}
                        </span>

                    @endif

                </a>


                {{-- MEKANIK & MAINT SECTION --}}

                <div class="sidebar-section">
                    MEKANIK & MAINT
                </div>


                <div
                    class="sidebar-group {{
                        request()->routeIs('barang.*')
                            ? 'open'
                            : ''
                    }}"
                >

                    <button
                        type="button"
                        class="sidebar-link"
                        onclick="toggleSidebarGroup(this.closest('.sidebar-group'))"
                    >

                        <span class="sidebar-link-icon">
                            M
                        </span>

                        <span>
                            Mekanik & Maint
                        </span>

                        <span class="sidebar-chevron"></span>

                    </button>


                    <div class="sidebar-submenu">

                        <a
                            href="{{ route('work-orders.maintenance.mekanik') }}"
                            class="sidebar-sub-link {{
                                request()->routeIs(
                                    'work-orders.maintenance.mekanik'
                                )
                                    ? 'active'
                                    : ''
                            }}"
                        >
                            <span>
                                Work Order
                            </span>
                        </a>


                        <a
                            href="{{ route('barang.index', ['bucket' => 'me_prev']) }}"
                            class="sidebar-sub-link {{
                                request()->routeIs('barang.*')
                                    ? 'active'
                                    : ''
                            }}"
                        >
                            <span>
                                Inventory
                            </span>
                        </a>

                    </div>

                </div>


                {{-- LAPORAN --}}

                <div class="sidebar-section">
                    LAPORAN
                </div>


                <a
                    href="{{ route('work-orders.maintenance.report') }}"
                    class="sidebar-link {{
                        request()->routeIs(
                            'work-orders.maintenance.report'
                        )
                            ? 'active'
                            : ''
                    }}"
                >

                    <span class="sidebar-link-icon">
                        L
                    </span>

                    <span>
                        Laporan Work Order
                    </span>

                </a>


                {{-- MASTER --}}

                <div class="sidebar-section">
                    MASTER
                </div>


                <div
                    class="sidebar-group {{
                        $masterActive
                            ? 'open'
                            : ''
                    }}"
                >

                    <button
                        type="button"
                        class="sidebar-link"
                        onclick="toggleSidebarGroup(this.closest('.sidebar-group'))"
                    >

                        <span class="sidebar-link-icon">

                            <svg
                                viewBox="0 0 24 24"
                                aria-hidden="true"
                            >
                                <path d="M12 3l8 4.5v9L12 21l-8-4.5v-9z" />
                                <path d="M12 12l8-4.5" />
                                <path d="M12 12v9" />
                                <path d="M12 12L4 7.5" />
                            </svg>

                        </span>

                        <span>
                            Master
                        </span>

                        <span class="sidebar-chevron"></span>

                    </button>


                    <div class="sidebar-submenu">

                        <a
                            href="{{ route('areas.index') }}"
                            class="sidebar-sub-link {{
                                request()->routeIs('areas.*')
                                    ? 'active'
                                    : ''
                            }}"
                        >
                            <span>
                                Area
                            </span>
                        </a>


                        <a
                            href="{{ route('machines.index') }}"
                            class="sidebar-sub-link {{
                                request()->routeIs('machines.*')
                                    ? 'active'
                                    : ''
                            }}"
                        >
                            <span>
                                Mesin
                            </span>
                        </a>


                        <a
                            href="{{ route('machine-spareparts.index') }}"
                            class="sidebar-sub-link {{
                                request()->routeIs('machine-spareparts.*')
                                    ? 'active'
                                    : ''
                            }}"
                        >
                            <span>
                                Mesin & Sparepart
                            </span>
                        </a>

                    </div>

                </div>

            </nav>


        {{-- =====================================================
             PREV-MAINT
             Hanya bisa melihat data PREV_MAINT
        ====================================================== --}}

        @elseif($isPrevMaint)

            <nav class="sidebar-menu">

                <div class="sidebar-section">
                    MENU
                </div>


                {{-- DASHBOARD --}}

                <a
                    href="{{ route('dashboard') }}"
                    class="sidebar-link {{
                        request()->routeIs('dashboard')
                            ? 'active'
                            : ''
                    }}"
                >

                    <span class="sidebar-link-icon">

                        <svg
                            viewBox="0 0 24 24"
                            aria-hidden="true"
                        >
                            <rect
                                x="3"
                                y="3"
                                width="7"
                                height="7"
                                rx="1"
                            />
                            <rect
                                x="14"
                                y="3"
                                width="7"
                                height="7"
                                rx="1"
                            />
                            <rect
                                x="3"
                                y="14"
                                width="7"
                                height="7"
                                rx="1"
                            />
                            <rect
                                x="14"
                                y="14"
                                width="7"
                                height="7"
                                rx="1"
                            />
                        </svg>

                    </span>

                    <span>
                        Dashboard
                    </span>

                </a>


                {{-- WORK ORDER --}}

                <div class="sidebar-section">
                    WORK ORDER
                </div>


                <a
                    href="{{ route('work-orders.maintenance.prev') }}"
                    class="sidebar-link {{
                        request()->routeIs(
                            'work-orders.maintenance.prev'
                        )
                            ? 'active'
                            : ''
                    }}"
                >

                    <span class="sidebar-link-icon">

                        <svg
                            viewBox="0 0 24 24"
                            aria-hidden="true"
                        >
                            <path d="M9 11h6" />
                            <path d="M9 15h6" />
                            <path d="M9 7h3" />
                            <path d="M6 3h9l3 3v15H6z" />
                            <path d="M15 3v4h4" />
                        </svg>

                    </span>

                    <span>
                        WO Prev-Maint
                    </span>


                    @if($workOrderUnreadCount > 0)

                        <span
                            class="sidebar-badge"
                            title="WO belum dibuka"
                        >
                            {{ $workOrderUnreadCount }}
                        </span>

                    @endif

                </a>


                {{-- PREV-MAINT SECTION --}}

                <div class="sidebar-section">
                    PREV-MAINT
                </div>


                <div
                    class="sidebar-group {{
                        request()->routeIs('barang.*')
                            ? 'open'
                            : ''
                    }}"
                >

                    <button
                        type="button"
                        class="sidebar-link"
                        onclick="toggleSidebarGroup(this.closest('.sidebar-group'))"
                    >

                        <span class="sidebar-link-icon">
                            P
                        </span>

                        <span>
                            Prev-Maint
                        </span>

                        <span class="sidebar-chevron"></span>

                    </button>


                    <div class="sidebar-submenu">

                        <a
                            href="{{ route('work-orders.maintenance.prev') }}"
                            class="sidebar-sub-link {{
                                request()->routeIs(
                                    'work-orders.maintenance.prev'
                                )
                                    ? 'active'
                                    : ''
                            }}"
                        >
                            <span>
                                Work Order
                            </span>
                        </a>


                        <a
                            href="{{ route('barang.index', ['bucket' => 'prev']) }}"
                            class="sidebar-sub-link {{
                                request()->routeIs('barang.*')
                                    ? 'active'
                                    : ''
                            }}"
                        >
                            <span>
                                Inventory
                            </span>
                        </a>

                    </div>

                </div>


                {{-- LAPORAN --}}

                <div class="sidebar-section">
                    LAPORAN
                </div>


                <a
                    href="{{ route('work-orders.maintenance.report') }}"
                    class="sidebar-link {{
                        request()->routeIs(
                            'work-orders.maintenance.report'
                        )
                            ? 'active'
                            : ''
                    }}"
                >

                    <span class="sidebar-link-icon">
                        L
                    </span>

                    <span>
                        Laporan Work Order
                    </span>

                </a>


                {{-- MASTER --}}

                <div class="sidebar-section">
                    MASTER
                </div>


                <div
                    class="sidebar-group {{
                        $masterActive
                            ? 'open'
                            : ''
                    }}"
                >

                    <button
                        type="button"
                        class="sidebar-link"
                        onclick="toggleSidebarGroup(this.closest('.sidebar-group'))"
                    >

                        <span class="sidebar-link-icon">

                            <svg
                                viewBox="0 0 24 24"
                                aria-hidden="true"
                            >
                                <path d="M12 3l8 4.5v9L12 21l-8-4.5v-9z" />
                                <path d="M12 12l8-4.5" />
                                <path d="M12 12v9" />
                                <path d="M12 12L4 7.5" />
                            </svg>

                        </span>

                        <span>
                            Master
                        </span>

                        <span class="sidebar-chevron"></span>

                    </button>


                    <div class="sidebar-submenu">

                        <a
                            href="{{ route('areas.index') }}"
                            class="sidebar-sub-link {{
                                request()->routeIs('areas.*')
                                    ? 'active'
                                    : ''
                            }}"
                        >
                            <span>
                                Area
                            </span>
                        </a>


                        <a
                            href="{{ route('machines.index') }}"
                            class="sidebar-sub-link {{
                                request()->routeIs('machines.*')
                                    ? 'active'
                                    : ''
                            }}"
                        >
                            <span>
                                Mesin
                            </span>
                        </a>


                        <a
                            href="{{ route('machine-spareparts.index') }}"
                            class="sidebar-sub-link {{
                                request()->routeIs('machine-spareparts.*')
                                    ? 'active'
                                    : ''
                            }}"
                        >
                            <span>
                                Mesin & Sparepart
                            </span>
                        </a>

                    </div>

                </div>

            </nav>


        {{-- =====================================================
             DIREKTUR
             Akses: Dashboard, WO (semua), Inventory, Master
             Tidak ada: Manajemen User, Settings, Activity
        ====================================================== --}}

        @elseif($isDirektur)

            <nav class="sidebar-menu">

                <div class="sidebar-section">
                    MENU
                </div>


                {{-- DASHBOARD --}}

                <a
                    href="{{ route('dashboard') }}"
                    class="user-sidebar-link {{
                        request()->routeIs('dashboard')
                            ? 'active'
                            : ''
                    }}"
                >
                    <x-icon name="home"></x-icon>
                    Dashboard
                </a>


                {{-- WORK ORDER --}}

                <div
                    class="sidebar-section"
                    style="margin-top:18px;"
                >
                    WORK ORDER
                </div>

                <a
                    href="{{ route('work-orders.admin.index') }}"
                    class="user-sidebar-link {{
                        request()->routeIs('work-orders.admin.*')
                            ? 'active'
                            : ''
                    }}"
                >
                    <x-icon name="work-order"></x-icon>
                    Semua Work Order
                </a>


                {{-- INVENTORY --}}

                <div
                    class="sidebar-section"
                    style="margin-top:18px;"
                >
                    INVENTORY
                </div>

                <a
                    href="{{ route('barang.index', ['bucket' => 'all']) }}"
                    class="user-sidebar-link {{
                        request()->routeIs('barang.*')
                            ? 'active'
                            : ''
                    }}"
                >
                    <x-icon name="inventory"></x-icon>
                    Stok Barang
                </a>


                {{-- MASTER --}}

                <div
                    class="sidebar-section"
                    style="margin-top:18px;"
                >
                    MASTER
                </div>

                <a
                    href="{{ route('master.area.index') }}"
                    class="user-sidebar-link {{
                        request()->routeIs('master.*')
                            ? 'active'
                            : ''
                    }}"
                >
                    <x-icon name="master"></x-icon>
                    Master Data
                </a>

            </nav>


        {{-- =====================================================
             ADMINISTRATOR
             TETAP SEPERTI SEBELUMNYA
        ====================================================== --}}

        @elseif($isAdministrator)

            <nav class="sidebar-menu">

                <div class="sidebar-section">
                    MENU
                </div>


                <a
                    href="{{ route('dashboard') }}"
                    class="sidebar-link {{
                        request()->routeIs('dashboard')
                            ? 'active'
                            : ''
                    }}"
                >

                    <span class="sidebar-link-icon">
                        D
                    </span>

                    <span>
                        Dashboard
                    </span>

                </a>


                <div class="sidebar-section">
                    INVENTORY
                </div>


                <div
                    class="sidebar-group {{
                        $inventoryActive
                            ? 'open'
                            : ''
                    }}"
                >

                    <button
                        type="button"
                        class="sidebar-link"
                        onclick="toggleSidebarGroup(this.closest('.sidebar-group'))"
                    >

                        <span class="sidebar-link-icon">
                            I
                        </span>

                        <span>
                            Inventory
                        </span>

                        <span class="sidebar-chevron"></span>

                    </button>


                    <div class="sidebar-submenu">

                        <a
                            href="{{ route('barang.index') }}"
                            class="sidebar-sub-link {{
                                request()->routeIs('barang.*')
                                    ? 'active'
                                    : ''
                            }}"
                        >
                            <span>
                                Stok Barang
                            </span>
                        </a>


                        <a
                            href="{{ route('barang-masuk.index') }}"
                            class="sidebar-sub-link {{
                                request()->routeIs('barang-masuk.*')
                                    ? 'active'
                                    : ''
                            }}"
                        >
                            <span>
                                Barang Masuk
                            </span>
                        </a>


                        <a
                            href="{{ route('barang-keluar.index') }}"
                            class="sidebar-sub-link {{
                                request()->routeIs('barang-keluar.*')
                                    ? 'active'
                                    : ''
                            }}"
                        >
                            <span>
                                Barang Keluar
                            </span>
                        </a>


                        <a
                            href="{{ route('laporan-harian.index') }}"
                            class="sidebar-sub-link {{
                                request()->routeIs('laporan-harian.*')
                                    ? 'active'
                                    : ''
                            }}"
                        >
                            <span>
                                Laporan Harian
                            </span>
                        </a>


                        <a
                            href="{{ route('purchase-requests.index') }}"
                            class="sidebar-sub-link {{
                                request()->routeIs('purchase-requests.*')
                                    ? 'active'
                                    : ''
                            }}"
                        >
                            <span>
                                Purchase Request
                            </span>
                        </a>

                    </div>

                </div>


                <div class="sidebar-section">
                    WORK ORDER
                </div>


                <div
                    class="sidebar-group {{
                        $adminWorkOrderActive
                            ? 'open'
                            : ''
                    }}"
                >

                    <button
                        type="button"
                        class="sidebar-link"
                        onclick="toggleSidebarGroup(this.closest('.sidebar-group'))"
                    >

                        <span class="sidebar-link-icon">
                            WO
                        </span>

                        <span>
                            Work Order
                        </span>

                        <span class="sidebar-chevron"></span>

                    </button>


                    <div class="sidebar-submenu">

                        <a
                            href="{{ route('work-orders.admin.index') }}"
                            class="sidebar-sub-link {{
                                request()->routeIs(
                                    'work-orders.admin.index',
                                    'work-orders.admin.show',
                                    'work-orders.admin.edit'
                                )
                                    ? 'active'
                                    : ''
                            }}"
                        >
                            <span>
                                Daftar Work Order
                            </span>
                        </a>


                        <a
                            href="{{ route('work-orders.admin.report') }}"
                            class="sidebar-sub-link {{
                                request()->routeIs(
                                    'work-orders.admin.report',
                                    'work-orders.report'
                                )
                                    ? 'active'
                                    : ''
                            }}"
                        >
                            <span>
                                Laporan Work Order
                            </span>
                        </a>

                    </div>

                </div>


                <div class="sidebar-section">
                    MASTER
                </div>


                <div
                    class="sidebar-group {{
                        $masterActive
                            ? 'open'
                            : ''
                    }}"
                >

                    <button
                        type="button"
                        class="sidebar-link"
                        onclick="toggleSidebarGroup(this.closest('.sidebar-group'))"
                    >

                        <span class="sidebar-link-icon">
                            M
                        </span>

                        <span>
                            Master
                        </span>

                        <span class="sidebar-chevron"></span>

                    </button>


                    <div class="sidebar-submenu">

                        <a
                            href="{{ route('areas.index') }}"
                            class="sidebar-sub-link {{
                                request()->routeIs('areas.*')
                                    ? 'active'
                                    : ''
                            }}"
                        >
                            <span>
                                Area
                            </span>
                        </a>


                        <a
                            href="{{ route('machines.index') }}"
                            class="sidebar-sub-link {{
                                request()->routeIs('machines.*')
                                    ? 'active'
                                    : ''
                            }}"
                        >
                            <span>
                                Mesin
                            </span>
                        </a>


                        <a
                            href="{{ route('machine-spareparts.index') }}"
                            class="sidebar-sub-link {{
                                request()->routeIs('machine-spareparts.*')
                                    ? 'active'
                                    : ''
                            }}"
                        >
                            <span>
                                Mesin & Sparepart
                            </span>
                        </a>

                    </div>

                </div>


                <div class="sidebar-section">
                    SYSTEM
                </div>


                <a
                    href="{{ route('users.index') }}"
                    class="sidebar-link {{
                        request()->routeIs('users.*')
                            ? 'active'
                            : ''
                    }}"
                >

                    <span class="sidebar-link-icon">
                        U
                    </span>

                    <span>
                        Manajemen User
                    </span>

                </a>


                <a
                    href="{{ route('activity.index') }}"
                    class="sidebar-link {{
                        request()->routeIs('activity.*')
                            ? 'active'
                            : ''
                    }}"
                >

                    <span class="sidebar-link-icon">
                        A
                    </span>

                    <span>
                        Log Aktivitas
                    </span>

                </a>


                <a
                    href="{{ route('settings.index') }}"
                    class="sidebar-link {{
                        request()->routeIs('settings.*')
                            ? 'active'
                            : ''
                    }}"
                >

                    <span class="sidebar-link-icon">
                        S
                    </span>

                    <span>
                        Setting
                    </span>

                </a>

            </nav>

        @endif

    </aside>


    {{-- =====================================================
         MAIN
    ====================================================== --}}

    <main class="main">

        <header class="topbar">

            <div class="topbar-left">

                <button
                    type="button"
                    id="pchBurger"
                    class="pch-burger"
                    aria-label="Buka / tutup sidebar"
                    aria-expanded="false"
                >
                    <span></span>
                    <span></span>
                    <span></span>
                </button>

                <div class="topbar-title-wrap">

                    <h1 class="page-title">

                        @yield(
                            'page_title',
                            'PACHIRA MAINTENANCE SYSTEM'
                        )

                    </h1>

                    <div class="page-subtitle">

                        @yield(
                            'page_subtitle',
                            'Maintenance & Inventory Management'
                        )

                    </div>

                </div>

            </div>


            <div class="topbar-right">

                @auth

                    <button
                        type="button"
                        id="themeToggle"
                        class="theme-toggle"
                        aria-label="Ganti tema"
                        title="Light / Dark mode"
                    >

                        <span class="theme-toggle-icon">
                            <x-icon name="sun"></x-icon>
                        </span>

                        <span class="theme-toggle-text">
                            Light
                        </span>

                    </button>

                    <div
                        id="notifWrapper"
                        class="notif-wrapper"
                    >

                        <button
                            type="button"
                            id="notifBell"
                            class="notif-bell"
                            aria-label="Notifikasi"
                            title="Notifikasi"
                        >

                            <svg viewBox="0 0 24 24">
                                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9" />
                                <path d="M13.73 21a2 2 0 0 1-3.46 0" />
                            </svg>

                            @if($allUnreadCount > 0)

                                <span
                                    class="notif-badge"
                                    id="notifBadge"
                                >
                                    {{ $allUnreadCount > 99 ? '99+' : $allUnreadCount }}
                                </span>

                            @else

                                <span
                                    class="notif-badge"
                                    id="notifBadge"
                                    style="display: none;"
                                >
                                    0
                                </span>

                            @endif

                        </button>

                        <div
                            id="notifDropdown"
                            class="notif-dropdown"
                        >

                            <div class="notif-header">

                                <span class="notif-header-title">
                                    Notifikasi
                                </span>

                                <button
                                    type="button"
                                    class="notif-mark-all"
                                    id="notifMarkAll"
                                    onclick="notifMarkAllRead()"
                                >
                                    Tandai semua sudah dibaca
                                </button>

                            </div>

                            <div
                                class="notif-list"
                                id="notifList"
                            >

                                <div class="notif-empty">
                                    Memuat notifikasi...
                                </div>

                            </div>

                        </div>

                    </div>

                    <div
                        id="accountWrapper"
                        class="account-wrapper"
                    >

                        <button
                            type="button"
                            id="accountButton"
                            class="account-button"
                            aria-expanded="false"
                            aria-haspopup="true"
                        >

                            <div class="account-avatar">

                                @if(!empty(
                                    auth()->user()->foto_profil
                                ))

                                    <img
                                        src="{{ asset(
                                            'storage/' .
                                            auth()->user()->foto_profil
                                        ) }}"
                                        alt="Foto Profil"
                                    >

                                @else

                                    {{
                                        strtoupper(
                                            substr(
                                                auth()->user()->name ?? 'U',
                                                0,
                                                1
                                            )
                                        )
                                    }}

                                @endif

                            </div>


                            <div class="account-info">

                                <div class="account-name">
                                    {{ auth()->user()->name ?? 'User' }}
                                </div>

                                <div class="account-role">
                                    {{ auth()->user()->role ?? 'User' }}
                                </div>

                            </div>


                            <span class="account-chevron"></span>

                        </button>


                        <div
                            id="accountDropdown"
                            class="account-dropdown"
                        >

                            <div class="account-dropdown-user">

                                <div class="account-dropdown-name">
                                    {{ auth()->user()->name ?? 'User' }}
                                </div>

                                <div class="account-dropdown-meta">

                                    {{ auth()->user()->role ?? 'User' }}

                                    @if(!empty(
                                        auth()->user()->bagian
                                    ))

                                        |
                                        {{ auth()->user()->bagian }}

                                    @endif

                                </div>

                            </div>


                            <a
                                href="{{ route('profile.photo') }}"
                                class="account-menu-link"
                                data-no-loading
                            >
                                Ubah Foto Profil
                            </a>


                            <a
                                href="{{ route('password.edit') }}"
                                class="account-menu-link"
                                data-no-loading
                            >
                                Ubah Password
                            </a>


                            <form
                                action="{{ route('logout') }}"
                                method="POST"
                                class="account-logout-form"
                                data-no-loading
                            >

                                @csrf

                                <button
                                    type="submit"
                                    class="account-menu-button account-menu-danger"
                                >
                                    Keluar
                                </button>

                            </form>

                        </div>

                    </div>

                @endauth

            </div>

        </header>


        <section class="content">

            @if(session('success'))

                <div class="message success">
                    {{ session('success') }}
                </div>

            @endif


            @if(session('error'))

                <div class="message error">
                    {{ session('error') }}
                </div>

            @endif


            @yield('content')

        </section>

    </main>

</div>


{{-- =========================================================
     AI ASSISTANT
========================================================= --}}

<button
    type="button"
    id="aiAssistantButton"
    class="ai-assistant-button"
    aria-label="Pachira Assistant"
    title="Pachira Assistant"
>

    <span aria-hidden="true">
        <x-icon name="robot"></x-icon>
    </span>

</button>


<div
    id="aiAssistantPanel"
    class="ai-assistant-panel"
>

    <div class="ai-assistant-header">

        <div class="ai-assistant-header-left">

            <div
                class="ai-assistant-header-icon"
                aria-hidden="true"
            >
                <x-icon name="robot"></x-icon>
            </div>

            <div>

                <div class="ai-assistant-title">
                    Pachira Assistant
                </div>

                <div class="ai-assistant-status">
                    Data Maintenance & Inventory
                </div>

            </div>

        </div>


        <button
            type="button"
            id="aiAssistantClose"
            class="ai-assistant-close"
            aria-label="Tutup Assistant"
        >
            <x-icon name="close"></x-icon>
        </button>

    </div>


    <div
        id="aiAssistantBody"
        class="ai-assistant-body"
    >

        <div class="ai-message ai">

            <div class="ai-bubble">
                Ada yang bisa saya bantu?
            </div>

        </div>

    </div>


    <div class="ai-assistant-footer">

        <form
            id="aiAssistantForm"
            class="ai-assistant-form"
            data-no-loading
            autocomplete="off"
        >

            <input
                type="text"
                id="aiAssistantInput"
                class="ai-assistant-input"
                placeholder="Ketik pertanyaan..."
                autocomplete="off"
                spellcheck="false"
            >

            <button
                type="submit"
                id="aiAssistantSend"
                class="ai-assistant-send"
                aria-label="Kirim pertanyaan"
            >
                KIRIM
            </button>

        </form>

    </div>

</div>


{{-- =========================================================
     AI FULLSCREEN
========================================================= --}}

<div
    id="aiFullscreenModal"
    class="ai-fullscreen-modal"
>

    <div class="ai-fullscreen-header">

        <div
            id="aiFullscreenTitle"
            class="ai-fullscreen-title"
        >
            Hasil AI
        </div>


        <div class="ai-fullscreen-actions">

            <button
                type="button"
                id="aiFullscreenPdf"
                class="ai-fullscreen-btn pdf"
            >
                Simpan PDF
            </button>


            <button
                type="button"
                id="aiFullscreenClose"
                class="ai-fullscreen-btn close"
            >
                Tutup
            </button>

        </div>

    </div>


    <div
        id="aiFullscreenBody"
        class="ai-fullscreen-body"
    ></div>

</div>


@stack('scripts')


<script>

    /* =====================================================
       SIDEBAR GROUP
    ====================================================== */

    function toggleSidebarGroup(group)
    {
        if (!group) {
            return;
        }

        group.classList.toggle('open');
    }


    document.addEventListener(
        'DOMContentLoaded',
        function () {

            /* =================================================
               THEME (LIGHT / DARK)
            ================================================== */

            const themeToggle =
                document.getElementById(
                    'themeToggle'
                );

            function applyThemeUi() {

                const current =
                    document.documentElement.getAttribute(
                        'data-theme'
                    ) || 'light';

                if (themeToggle) {

                    const icon =
                        themeToggle.querySelector(
                            '.theme-toggle-icon'
                        );

                    const text =
                        themeToggle.querySelector(
                            '.theme-toggle-text'
                        );

                    if (icon) {
                        icon.textContent =
                            current === 'dark'
                                ? "\u263d"
                                : "\u2600";
                    }

                    if (text) {
                        text.textContent =
                            current === 'dark'
                                ? 'Dark'
                                : 'Light';
                    }
                }
            }

            applyThemeUi();


            if (themeToggle) {

                themeToggle.addEventListener(
                    'click',
                    function () {

                        const next =
                            (
                                document.documentElement.getAttribute(
                                    'data-theme'
                                )
                                === 'dark'
                            )
                                ? 'light'
                                : 'dark';

                        document.documentElement.setAttribute(
                            'data-theme',
                            next
                        );

                        try {
                            localStorage.setItem(
                                'pachira-theme',
                                next
                            );
                        } catch (e) {}

                        applyThemeUi();

                        document.dispatchEvent(
                            new CustomEvent(
                                'pachira:theme',
                                {
                                    detail: {
                                        theme: next,
                                    },
                                }
                            )
                        );

                    }
                );
            }


            /* =================================================
               ACCOUNT
            ================================================== */

            const accountWrapper =
                document.getElementById(
                    'accountWrapper'
                );

            const accountButton =
                document.getElementById(
                    'accountButton'
                );


            if (
                accountWrapper &&
                accountButton
            ) {

                accountButton.addEventListener(
                    'click',
                    function (event) {

                        event.stopPropagation();

                        const opened =
                            accountWrapper.classList.toggle(
                                'open'
                            );

                        accountButton.setAttribute(
                            'aria-expanded',
                            opened
                                ? 'true'
                                : 'false'
                        );

                    }
                );


                document.addEventListener(
                    'click',
                    function (event) {

                        if (
                            !accountWrapper.contains(
                                event.target
                            )
                        ) {

                            accountWrapper.classList.remove(
                                'open'
                            );

                            accountButton.setAttribute(
                                'aria-expanded',
                                'false'
                            );

                        }

                    }
                );

            }


            /* =================================================
               NOTIFICATION BELL
            ================================================== */

            const notifWrapper =
                document.getElementById(
                    'notifWrapper'
                );

            const notifBell =
                document.getElementById(
                    'notifBell'
                );

            const notifDropdown =
                document.getElementById(
                    'notifDropdown'
                );

            const notifBadge =
                document.getElementById(
                    'notifBadge'
                );

            const notifList =
                document.getElementById(
                    'notifList'
                );

            const notifMarkAllBtn =
                document.getElementById(
                    'notifMarkAll'
                );

            let notifLoaded = false;


            if (
                notifBell &&
                notifDropdown
            ) {

                notifBell.addEventListener(
                    'click',
                    function (event) {

                        event.stopPropagation();

                        const opened =
                            notifDropdown.classList.toggle(
                                'active'
                            );

                        if (
                            opened &&
                            !notifLoaded
                        ) {

                            notifLoadUnread();
                            notifLoaded = true;
                        }

                    }
                );

                document.addEventListener(
                    'click',
                    function (event) {

                        if (
                            notifWrapper &&
                            !notifWrapper.contains(
                                event.target
                            )
                        ) {

                            notifDropdown.classList.remove(
                                'active'
                            );
                        }
                    }
                );

            }


            function notifLoadUnread()
            {
                if (!notifList) { return; }

                fetch(
                    '{{ route("notifications.unread") }}',
                    {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        },
                        credentials: 'same-origin',
                    }
                )
                .then(function (res) {

                    return res.json();
                })
                .then(function (json) {

                    if (
                        !json.success ||
                        !json.data
                    ) {

                        return;
                    }

                    notifUpdateBadge(
                        json.unread_count
                    );

                    if (
                        json.data.length === 0
                    ) {

                        notifList.innerHTML =
                            '<div class="notif-empty">' +
                            '<svg viewBox="0 0 24 24"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>' +
                            'Tidak ada notifikasi baru.' +
                            '</div>';

                        return;
                    }

                    let html = '';

                    json.data.forEach(function (n) {

                        const title = escapeHtml(n.title);

                        const msg = escapeHtml(n.message);

                        const time = escapeHtml(n.created_at);

                        const woUrl = n.wo_url
                            ? n.wo_url
                            : '#';

                        const dataAttr = n.wo_url
                            ? ' onclick="notifClickItem(this, ' + n.id + ')" '
                            : ' onclick="notifClickItem(this, ' + n.id + ')" ';

                        html +=
                            '<a class="notif-item" href="' + woUrl + '"' + dataAttr + '>' +
                            '<div class="notif-item-title">' + title + '</div>' +
                            '<div class="notif-item-msg">' + msg + '</div>' +
                            '<div class="notif-item-time">' + time + '</div>' +
                            '</a>';
                    });

                    notifList.innerHTML = html;
                })
                .catch(function () {

                    notifList.innerHTML =
                        '<div class="notif-empty">Gagal memuat notifikasi.</div>';
                });
            }


            function notifClickItem(el, id)
            {
                if (el && el.tagName === 'A') {
                    el.style.pointerEvents = 'none';
                }

                fetch(
                    '{{ route("notifications.mark-read", "__ID__") }}'.replace('__ID__', id),
                    {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        },
                        credentials: 'same-origin',
                    }
                )
                .then(function (res) {

                    return res.json();
                })
                .then(function () {

                    if (el) {
                        el.style.opacity = '0.5';
                    }

                    if (notifBadge) {

                        let current = parseInt(
                            notifBadge.textContent
                        ) || 0;

                        current = Math.max(0, current - 1);

                        notifUpdateBadge(current);
                    }
                })
                .catch(function () {

                    if (el) {
                        el.style.pointerEvents = '';
                    }
                });
            }


            function notifMarkAllRead()
            {
                fetch(
                    '{{ route("notifications.mark-all-read") }}',
                    {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        },
                        credentials: 'same-origin',
                    }
                )
                .then(function (res) {

                    return res.json();
                })
                .then(function () {

                    notifUpdateBadge(0);

                    if (notifList) {

                        notifList.innerHTML =
                            '<div class="notif-empty">' +
                            '<svg viewBox="0 0 24 24"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>' +
                            'Tidak ada notifikasi baru.' +
                            '</div>';
                    }
                });
            }


            function notifUpdateBadge(count)
            {
                if (!notifBadge) { return; }

                if (count > 0) {

                    notifBadge.textContent =
                        count > 99
                            ? '99+'
                            : count;

                    notifBadge.style.display = '';

                } else {

                    notifBadge.style.display = 'none';
                }
            }


            /* =================================================
               AI ELEMENTS
            ================================================== */

            const aiButton =
                document.getElementById(
                    'aiAssistantButton'
                );

            const aiPanel =
                document.getElementById(
                    'aiAssistantPanel'
                );

            const aiClose =
                document.getElementById(
                    'aiAssistantClose'
                );

            const aiForm =
                document.getElementById(
                    'aiAssistantForm'
                );

            const aiInput =
                document.getElementById(
                    'aiAssistantInput'
                );

            const aiSend =
                document.getElementById(
                    'aiAssistantSend'
                );

            const aiBody =
                document.getElementById(
                    'aiAssistantBody'
                );


            const aiFullscreenModal =
                document.getElementById(
                    'aiFullscreenModal'
                );

            const aiFullscreenBody =
                document.getElementById(
                    'aiFullscreenBody'
                );

            const aiFullscreenTitle =
                document.getElementById(
                    'aiFullscreenTitle'
                );

            const aiFullscreenClose =
                document.getElementById(
                    'aiFullscreenClose'
                );

            const aiFullscreenPdf =
                document.getElementById(
                    'aiFullscreenPdf'
                );


            /* =================================================
               ESCAPE HTML
            ================================================== */

            function escapeHtml(
                value
            ) {

                const div =
                    document.createElement(
                        'div'
                    );

                div.textContent =
                    String(
                        value ?? ''
                    );

                return div.innerHTML;
            }


            /* =================================================
               PARSE AI TABLE ROW
            ================================================== */

            function parseAiTableRow(
                row
            ) {

                let value =
                    String(
                        row ?? ''
                    ).trim();


                if (
                    value.startsWith('|')
                ) {

                    value =
                        value.substring(
                            1
                        );

                }


                if (
                    value.endsWith('|')
                ) {

                    value =
                        value.substring(
                            0,
                            value.length - 1
                        );

                }


                return value
                    .split('|')
                    .map(
                        function (cell) {

                            return cell.trim();

                        }
                    )
                    .filter(
                        function (cell) {

                            return cell !== '';

                        }
                    );

            }


            /* =================================================
               FORMAT AI ANSWER
            ================================================== */

            function formatAiAnswer(
                text
            ) {

                const raw =
                    String(
                        text ?? ''
                    )
                    .replace(
                        /\r/g,
                        ''
                    )
                    .trim();


                if (
                    !raw
                ) {

                    return `
                        <div class="ai-result-text">
                            Belum ada jawaban.
                        </div>
                    `;

                }


                const lines =
                    raw
                    .split('\n')
                    .map(
                        line =>
                            line.trim()
                    )
                    .filter(
                        line =>
                            line.length > 0
                    );


                if (
                    !lines.length
                ) {

                    return `
                        <div class="ai-result-text">
                            Belum ada jawaban.
                        </div>
                    `;

                }


                let html = '';

                let currentTitle = '';

                let currentTable = [];


                function addNormalText(
                    textValue
                ) {

                    if (
                        !textValue
                    ) {
                        return;
                    }


                    html += `
                        <div class="ai-result-text">
                            ${
                                escapeHtml(
                                    textValue
                                ).replace(
                                    /\n/g,
                                    '<br>'
                                )
                            }
                        </div>
                    `;

                }


                function flushTable()
                {

                    if (
                        currentTable.length < 2
                    ) {

                        currentTable = [];

                        return;

                    }


                    const rows =
                        currentTable
                        .filter(
                            function (row) {

                                const cells =
                                    parseAiTableRow(
                                        row
                                    );


                                if (
                                    cells.length === 0
                                ) {
                                    return false;
                                }


                                return !cells.every(
                                    function (cell) {

                                        return /^:?-{3,}:?$/.test(
                                            cell.trim()
                                        );

                                    }
                                );

                            }
                        );


                    if (
                        rows.length < 2
                    ) {

                        currentTable = [];

                        return;

                    }


                    const header =
                        parseAiTableRow(
                            rows[0]
                        );


                    html += `
                        <div class="ai-result-section">
                    `;


                    if (
                        currentTitle
                    ) {

                        html += `
                            <div class="ai-result-section-title">
                                ${
                                    escapeHtml(
                                        currentTitle
                                    )
                                }
                            </div>
                        `;

                    }


                    html += `
                        <div
                            class="ai-table-wrap"
                            title="Klik untuk membuka tabel penuh"
                        >

                            <table class="ai-result-table">

                                <thead>

                                    <tr>
                    `;


                    header.forEach(
                        function (cell) {

                            html += `
                                <th>
                                    ${
                                        escapeHtml(
                                            cell
                                        )
                                    }
                                </th>
                            `;

                        }
                    );


                    html += `
                                    </tr>

                                </thead>

                                <tbody>
                    `;


                    rows
                        .slice(1)
                        .forEach(
                            function (row) {

                                const cells =
                                    parseAiTableRow(
                                        row
                                    );


                                html += `
                                    <tr>
                                `;


                                cells.forEach(
                                    function (cell) {

                                        html += `
                                            <td>
                                                ${
                                                    escapeHtml(
                                                        cell
                                                    )
                                                }
                                            </td>
                                        `;

                                    }
                                );


                                html += `
                                    </tr>
                                `;

                            }
                        );


                    html += `
                                </tbody>

                            </table>

                        </div>

                    </div>
                    `;


                    currentTable = [];
                    currentTitle = '';
                }


                for (
                    let i = 0;
                    i < lines.length;
                    i++
                ) {

                    const line =
                        lines[i];


                    if (
                        line.includes('|')
                    ) {

                        currentTable.push(
                            line
                        );

                        continue;
                    }


                    if (
                        currentTable.length > 0
                    ) {

                        flushTable();

                    }


                    if (
                        line &&
                        !/^[-=*]+$/.test(
                            line
                        )
                    ) {

                        if (
                            currentTitle
                        ) {

                            addNormalText(
                                line
                            );

                        } else {

                            currentTitle =
                                line;

                        }

                    }

                }


                flushTable();


                if (
                    !html.trim()
                ) {

                    return `
                        <div class="ai-result-text">
                            ${
                                escapeHtml(
                                    raw
                                ).replace(
                                    /\n/g,
                                    '<br>'
                                )
                            }
                        </div>
                    `;

                }


                return html;
            }


            /* =================================================
               OPEN FULLSCREEN
            ================================================== */

            function openAiFullscreen(
                tableWrap
            ) {

                if (
                    !tableWrap ||
                    !aiFullscreenModal ||
                    !aiFullscreenBody
                ) {
                    return;
                }


                const table =
                    tableWrap.querySelector(
                        '.ai-result-table'
                    );


                if (
                    !table
                ) {
                    return;
                }


                let title =
                    'Hasil AI';


                const section =
                    tableWrap.closest(
                        '.ai-result-section'
                    );


                if (
                    section
                ) {

                    const sectionTitle =
                        section.querySelector(
                            '.ai-result-section-title'
                        );


                    if (
                        sectionTitle
                    ) {

                        title =
                            sectionTitle
                                .textContent
                                .trim();

                    }

                }


                if (
                    aiFullscreenTitle
                ) {

                    aiFullscreenTitle.textContent =
                        title;

                }


                aiFullscreenBody.innerHTML =
                    '';


                const clone =
                    tableWrap.cloneNode(
                        true
                    );


                clone.style.border =
                    'none';

                clone.style.boxShadow =
                    'none';

                clone.style.cursor =
                    'default';

                clone.style.transform =
                    'none';


                aiFullscreenBody.appendChild(
                    clone
                );


                aiFullscreenModal.classList.add(
                    'active'
                );


                document.body.style.overflow =
                    'hidden';
            }


            /* =================================================
               CLOSE FULLSCREEN
            ================================================== */

            function closeAiFullscreen()
            {

                if (
                    aiFullscreenModal
                ) {

                    aiFullscreenModal.classList.remove(
                        'active'
                    );

                }


                if (
                    aiFullscreenBody
                ) {

                    aiFullscreenBody.innerHTML =
                        '';

                }


                document.body.style.overflow =
                    '';
            }


            /* =================================================
               USER MESSAGE
            ================================================== */

            function addUserMessage(
                text
            ) {

                const message =
                    document.createElement(
                        'div'
                    );

                message.className =
                    'ai-message user';


                const bubble =
                    document.createElement(
                        'div'
                    );

                bubble.className =
                    'ai-bubble';

                bubble.textContent =
                    text;


                message.appendChild(
                    bubble
                );

                aiBody.appendChild(
                    message
                );


                aiBody.scrollTop =
                    aiBody.scrollHeight;
            }


            /* =================================================
               ASSISTANT MESSAGE
            ================================================== */

            function addAssistantMessage(
                html
            ) {

                const message =
                    document.createElement(
                        'div'
                    );

                message.className =
                    'ai-message ai';


                const bubble =
                    document.createElement(
                        'div'
                    );

                bubble.className =
                    'ai-bubble';

                bubble.innerHTML =
                    html;


                message.appendChild(
                    bubble
                );

                aiBody.appendChild(
                    message
                );


                aiBody.scrollTop =
                    aiBody.scrollHeight;
            }


            /* =================================================
               ERROR
            ================================================== */

            function addErrorMessage(
                text
            ) {

                addAssistantMessage(
                    '<div class="ai-result-text">' +
                    escapeHtml(
                        text
                    ) +
                    '</div>'
                );

            }


            /* =================================================
               OPEN AI
            ================================================== */

            if (
                aiButton &&
                aiPanel
            ) {

                aiButton.addEventListener(
                    'click',
                    function () {

                        aiPanel.classList.add(
                            'active'
                        );

                        setTimeout(
                            function () {

                                if (
                                    aiInput
                                ) {

                                    aiInput.focus();

                                }

                            },
                            50
                        );

                    }
                );

            }


            /* =================================================
               CLOSE AI
            ================================================== */

            if (
                aiClose &&
                aiPanel
            ) {

                aiClose.addEventListener(
                    'click',
                    function () {

                        aiPanel.classList.remove(
                            'active'
                        );

                    }
                );

            }


            /* =================================================
               TABLE -> FULLSCREEN
            ================================================== */

            if (
                aiBody
            ) {

                aiBody.addEventListener(
                    'click',
                    function (event) {

                        const tableWrap =
                            event.target.closest(
                                '.ai-table-wrap'
                            );


                        if (
                            !tableWrap
                        ) {
                            return;
                        }


                        openAiFullscreen(
                            tableWrap
                        );

                    }
                );

            }


            /* =================================================
               FULLSCREEN CLOSE
            ================================================== */

            if (
                aiFullscreenClose
            ) {

                aiFullscreenClose.addEventListener(
                    'click',
                    function () {

                        closeAiFullscreen();

                    }
                );

            }


            /* =================================================
               FULLSCREEN PDF
            ================================================== */

            if (
                aiFullscreenPdf
            ) {

                aiFullscreenPdf.addEventListener(
                    'click',
                    function () {

                        window.print();

                    }
                );

            }


            /* =================================================
               CLICK OUTSIDE FULLSCREEN
            ================================================== */

            if (
                aiFullscreenModal
            ) {

                aiFullscreenModal.addEventListener(
                    'click',
                    function (event) {

                        if (
                            event.target ===
                            aiFullscreenModal
                        ) {

                            closeAiFullscreen();

                        }

                    }
                );

            }


            /* =================================================
               SUBMIT AI
               BENAR-BENAR TANPA LOADING
            ================================================== */

            if (
                aiForm &&
                aiInput &&
                aiSend &&
                aiBody
            ) {

                aiForm.addEventListener(
                    'submit',
                    async function (event) {

                        event.preventDefault();

                        event.stopPropagation();


                        const question =
                            aiInput.value.trim();


                        if (
                            !question
                        ) {

                            aiInput.focus();

                            return;

                        }


                        addUserMessage(
                            question
                        );


                        aiInput.value =
                            '';


                        aiSend.disabled =
                            false;

                        aiSend.textContent =
                            'KIRIM';


                        const csrfElement =
                            document.querySelector(
                                'meta[name="csrf-token"]'
                            );


                        const csrfToken =
                            csrfElement
                                ? csrfElement.getAttribute(
                                    'content'
                                )
                                : '';


                        try {

                            const response =
                                await fetch(
                                    "{{ route('ai-assistant.ask') }}",
                                    {
                                        method:
                                            'POST',

                                        headers: {

                                            'Content-Type':
                                                'application/json',

                                            'Accept':
                                                'application/json',

                                            'X-CSRF-TOKEN':
                                                csrfToken,

                                            'X-Requested-With':
                                                'XMLHttpRequest'

                                        },

                                        body:
                                            JSON.stringify({
                                                question:
                                                    question
                                            })

                                    }
                                );


                            let data =
                                {};


                            try {

                                data =
                                    await response.json();

                            } catch (
                                jsonError
                            ) {

                                data =
                                    {};

                            }


                            if (
                                !response.ok
                            ) {

                                addErrorMessage(
                                    data.answer ??
                                    (
                                        'Terjadi kesalahan pada server. HTTP ' +
                                        response.status
                                    )
                                );

                                return;

                            }


                            if (
                                data.html &&
                                String(
                                    data.html
                                ).trim() !== ''
                            ) {

                                addAssistantMessage(
                                    data.html
                                );

                                return;

                            }


                            if (
                                data.answer !== undefined &&
                                data.answer !== null
                            ) {

                                addAssistantMessage(
                                    formatAiAnswer(
                                        data.answer
                                    )
                                );

                                return;

                            }


                            addErrorMessage(
                                'Belum ada jawaban.'
                            );

                        } catch (
                            error
                        ) {

                            console.error(
                                'Pachira Assistant Error:',
                                error
                            );


                            addErrorMessage(
                                'Tidak dapat menghubungi Assistant.'
                            );

                        } finally {

                            aiSend.disabled =
                                false;

                            aiSend.textContent =
                                'KIRIM';

                            aiInput.focus();

                            aiBody.scrollTop =
                                aiBody.scrollHeight;
                        }

                    },
                    false
                );


                /* =================================================
                   ENTER
                ================================================== */

                aiInput.addEventListener(
                    'keydown',
                    function (event) {

                        if (
                            event.key === 'Enter' &&
                            !event.shiftKey
                        ) {

                            event.preventDefault();

                            aiForm.requestSubmit();

                        }

                    }
                );

            }


            /* =================================================
               ESC
            ================================================== */

            document.addEventListener(
                'keydown',
                function (event) {

                    if (
                        event.key !== 'Escape'
                    ) {
                        return;
                    }


                    if (
                        accountWrapper
                    ) {

                        accountWrapper.classList.remove(
                            'open'
                        );

                    }


                    if (
                        accountButton
                    ) {

                        accountButton.setAttribute(
                            'aria-expanded',
                            'false'
                        );

                    }


                    if (
                        aiFullscreenModal &&
                        aiFullscreenModal.classList.contains(
                            'active'
                        )
                    ) {

                        closeAiFullscreen();

                        return;

                    }


                    if (
                        aiPanel
                    ) {

                        aiPanel.classList.remove(
                            'active'
                        );

                    }

                }
            );

        }
    );

</script>


{{-- =========================================================
     PCH CONFIRM MODAL
========================================================= --}}

<div
    id="pchModalBackdrop"
    class="pch-modal-backdrop"
    role="dialog"
    aria-modal="true"
    aria-labelledby="pchModalTitle"
>

    <div class="pch-modal">

        <div
            class="pch-modal-icon"
            aria-hidden="true"
        >
            <x-icon name="trash"></x-icon>
        </div>

        <div
            id="pchModalTitle"
            class="pch-modal-title"
        >
            Konfirmasi Hapus
        </div>

        <div
            id="pchModalText"
            class="pch-modal-text"
        >
            Data yang dihapus tidak dapat dikembalikan.
        </div>

        <div class="pch-modal-actions">

            <button
                type="button"
                id="pchModalCancel"
                class="btn btn-gray"
            >
                Batal
            </button>

            <button
                type="button"
                id="pchModalConfirm"
                class="btn btn-danger"
            >
                Ya, Hapus
            </button>

        </div>

    </div>

</div>


<script>
    /*
    |=====================================================
    | PACHIRA AURORA - MODERN INTERACTIONS
    |
    | Enhancement visual murni:
    | 1. Ripple pada tombol & link sidebar
    | 2. Count-up angka KPI (.kpi-value)
    | 3. Auto-dismiss toast
    |
    | Tidak menyentuh logika bisnis apa pun.
    |=====================================================
    */

    (function () {

        'use strict';

        const reducedMotion =
            window.matchMedia(
                '(prefers-reduced-motion: reduce)'
            ).matches;

        if (reducedMotion) {
            return;
        }


        /* =================================================
           1. RIPPLE EFFECT
        ================================================== */

        const rippleTargets =
            '.btn, .sidebar-link, ' +
            '.user-sidebar-link, ' +
            '.account-menu-link';

        document.addEventListener(
            'pointerdown',
            function (event) {

                const host =
                    event.target.closest(
                        rippleTargets
                    );

                if (!host) {
                    return;
                }

                const rect =
                    host.getBoundingClientRect();

                const size =
                    Math.max(
                        rect.width,
                        rect.height
                    ) * 1.1;

                const ripple =
                    document.createElement(
                        'span'
                    );

                ripple.className =
                    'pch-ripple';

                ripple.style.width =
                    size + 'px';

                ripple.style.height =
                    size + 'px';

                ripple.style.left =
                    (event.clientX - rect.left - size / 2) + 'px';

                ripple.style.top =
                    (event.clientY - rect.top - size / 2) + 'px';

                host.appendChild(ripple);

                ripple.addEventListener(
                    'animationend',
                    function () {
                        ripple.remove();
                    }
                );

            },
            { passive: true }
        );


        /* =================================================
           2. COUNT-UP KPI
           Format aman: "1.234" / "1,234" / "42"
        ================================================== */

        function formatLike(
            originalText,
            numberValue
        ) {

            const trimmed =
                String(originalText).trim();

            const separator =
                trimmed.search(/[,\.]/) !== -1
                    ? (
                        trimmed.lastIndexOf(',') >
                        trimmed.lastIndexOf('.')
                            ? ','
                            : '.'
                    )
                    : '';

            if (!separator) {
                return String(Math.round(numberValue));
            }

            return String(
                Math.round(numberValue)
            ).replace(
                /\B(?=(\d{3})+(?!\d))/g,
                separator
            );
        }

        function animateCountUp(element) {

            if (
                element.dataset.pchCounted === '1'
            ) {
                return;
            }

            element.dataset.pchCounted = '1';

            const original =
                element.textContent.trim();

            const target =
                parseInt(
                    original.replace(/[^\d]/g, ''),
                    10
                );

            if (
                !isFinite(target) ||
                target === 0 ||
                target > 100000000
            ) {
                return;
            }

            const duration = 900;

            const start =
                performance.now();

            function tick(now) {

                const progress =
                    Math.min(
                        (now - start) / duration,
                        1
                    );

                const eased =
                    1 -
                    Math.pow(1 - progress, 3);

                element.textContent =
                    formatLike(
                        original,
                        target * eased
                    );

                if (progress < 1) {
                    requestAnimationFrame(tick);
                } else {
                    element.textContent = original;
                }
            }

            requestAnimationFrame(tick);
        }

        const kpiElements =
            document.querySelectorAll('.kpi-value');

        if (
            kpiElements.length &&
            'IntersectionObserver' in window
        ) {

            const observer =
                new IntersectionObserver(
                    function (entries) {

                        entries.forEach(
                            function (entry) {

                                if (
                                    entry.isIntersecting
                                ) {

                                    animateCountUp(
                                        entry.target
                                    );

                                    observer.unobserve(
                                        entry.target
                                    );
                                }
                            }
                        );
                    },
                    {
                        threshold: 0.4,
                    }
                );

            kpiElements.forEach(
                function (element) {
                    observer.observe(element);
                }
            );

        }


        /* =================================================
           3. AUTO-DISMISS TOAST
        ================================================== */

        setTimeout(
            function () {

                document
                    .querySelectorAll('.crud-toast')
                    .forEach(
                        function (toast) {

                            toast.classList.add(
                                'pch-hide'
                            );

                            setTimeout(
                                function () {
                                    toast.remove();
                                },
                                400
                            );
                        }
                    );

            },
            4500
        );

        /* =================================================
           4. SIDEBAR ASLI - HAMBURGER
           Tidak membuka menu fullscreen.
        ================================================== */

        const pchBurger = document.getElementById('pchBurger');
        const pchSidebar = document.querySelector('.sidebar');
        const pchBackdrop = document.getElementById('pchBackdrop');

        function pchIsMobile() {
            return window.matchMedia('(max-width: 700px)').matches;
        }

        function pchDrawerClose() {
            if (!pchSidebar) return;

            if (pchIsMobile()) {
                pchSidebar.classList.remove('pch-open');
                document.body.classList.remove('pch-drawer-open');
                pchBurger?.classList.remove('pch-open');
                pchBurger?.setAttribute('aria-expanded', 'false');

                if (pchBackdrop) {
                    pchBackdrop.classList.remove('pch-show');
                    setTimeout(function () {
                        if (!pchSidebar.classList.contains('pch-open')) {
                            pchBackdrop.style.display = '';
                        }
                    }, 290);
                }
            } else {
                document.body.classList.add('pch-sidebar-collapsed');
                pchBurger?.classList.remove('pch-open');
                pchBurger?.setAttribute('aria-expanded', 'false');
            }
        }

        function pchDrawerOpen() {
            if (!pchSidebar) return;

            if (pchIsMobile()) {
                pchSidebar.classList.add('pch-open');
                document.body.classList.add('pch-drawer-open');
                pchBurger?.classList.add('pch-open');
                pchBurger?.setAttribute('aria-expanded', 'true');

                if (pchBackdrop) {
                    pchBackdrop.style.display = 'block';
                    requestAnimationFrame(function () {
                        pchBackdrop.classList.add('pch-show');
                    });
                }
            } else {
                document.body.classList.remove('pch-sidebar-collapsed');
                pchBurger?.classList.add('pch-open');
                pchBurger?.setAttribute('aria-expanded', 'true');
            }
        }

        function pchDrawerToggle() {
            if (!pchSidebar) return;

            if (pchIsMobile()) {
                pchSidebar.classList.contains('pch-open')
                    ? pchDrawerClose()
                    : pchDrawerOpen();
            } else {
                document.body.classList.contains('pch-sidebar-collapsed')
                    ? pchDrawerOpen()
                    : pchDrawerClose();
            }
        }

        if (pchBurger && pchSidebar) {
            pchBurger.addEventListener('click', pchDrawerToggle);

            pchSidebar.addEventListener('click', function (event) {
                const link = event.target.closest(
                    'a.sidebar-link, a.user-sidebar-link, .sidebar-sub-link'
                );

                if (link && pchIsMobile()) {
                    pchDrawerClose();
                }
            });
        }

        if (pchBackdrop) {
            pchBackdrop.addEventListener('click', pchDrawerClose);
        }

        document.addEventListener('keydown', function (event) {
            if (event.key !== 'Escape') return;

            if (pchIsMobile()) {
                if (pchSidebar?.classList.contains('pch-open')) {
                    pchDrawerClose();
                }
            } else if (document.body.classList.contains('pch-sidebar-collapsed') === false) {
                // Desktop: ESC tidak membuat menu baru. Biarkan sidebar tetap ada.
            }
        });

        window.addEventListener('resize', function () {
            if (!pchSidebar) return;

            if (pchIsMobile()) {
                document.body.classList.remove('pch-sidebar-collapsed');
                if (pchSidebar.classList.contains('pch-open')) {
                    return;
                }
                pchSidebar.classList.remove('pch-open');
                pchBurger?.classList.remove('pch-open');
                pchBurger?.setAttribute('aria-expanded', 'false');
                pchBackdrop?.classList.remove('pch-show');
            } else {
                pchSidebar.classList.remove('pch-open');
                document.body.classList.remove('pch-drawer-open');
                pchBackdrop?.classList.remove('pch-show');
                if (pchBackdrop) pchBackdrop.style.display = '';
                pchBurger?.classList.toggle(
                    'pch-open',
                    !document.body.classList.contains('pch-sidebar-collapsed')
                );
                pchBurger?.setAttribute(
                    'aria-expanded',
                    String(!document.body.classList.contains('pch-sidebar-collapsed'))
                );
            }
        }, { passive: true });

        /*
         * Safety guard untuk halaman yang masih memiliki panel "Semua Menu"
         * dari implementasi lama. Yang dihapus hanya panel tersebut,
         * sidebar asli tidak disentuh.
         */
        function pchRemoveOldAllMenu() {
            document.querySelectorAll('body *').forEach(function (element) {
                const text = (element.textContent || '').trim();
                if (!text || !/^Semua Menu\s*[—-]\s*PACHIRA MAINTENANCE SYSTEM$/i.test(text)) {
                    return;
                }

                let target = element;
                for (let i = 0; i < 6 && target && target !== document.body; i++) {
                    const style = window.getComputedStyle(target);
                    const rect = target.getBoundingClientRect();
                    if (
                        (style.position === 'fixed' || style.position === 'absolute') &&
                        rect.width >= window.innerWidth * .85 &&
                        rect.height >= window.innerHeight * .75
                    ) {
                        target.remove();
                        return;
                    }
                    target = target.parentElement;
                }
            });
        }

        pchRemoveOldAllMenu();

        const pchMenuGuard = new MutationObserver(function () {
            pchRemoveOldAllMenu();
        });

        pchMenuGuard.observe(document.body, {
            childList: true,
            subtree: true
        });

        /* =================================================
           5. TABEL SCROLL HORIZONTAL (MOBILE)
           Membungkus tabel agar bisa digeser di HP.
        ================================================== */

        document.querySelectorAll('.content table').forEach(
            function (table) {

                if (
                    table.closest('.pch-scroll') ||
                    table.closest('.ai-table-wrap')
                ) {
                    return;
                }

                const wrapper =
                    document.createElement('div');

                wrapper.className = 'pch-scroll';

                table.parentNode.insertBefore(
                    wrapper,
                    table
                );

                wrapper.appendChild(table);
            }
        );


        /* =================================================
           6. MODAL KONFIRMASI HAPUS
           Form dengan [data-confirm] ditahan dulu,
           dikirim hanya setelah tombol "Ya, Hapus".
           Logic DELETE server tidak berubah.
        ================================================== */

        const pchModal =
            document.getElementById('pchModalBackdrop');

        const pchModalText =
            document.getElementById('pchModalText');

        const pchModalConfirm =
            document.getElementById('pchModalConfirm');

        const pchModalCancel =
            document.getElementById('pchModalCancel');

        let pchPendingForm = null;

        function pchModalOpen(message, form) {

            if (!pchModal) {
                return;
            }

            /*
            | Pastikan tidak ada overlay loading yang masih menutupi
            | modal konfirmasi (misal sisa loading yang tidak selesai).
            */

            if (
                window.hideGlobalLoading
            ) {
                window.hideGlobalLoading();
            }

            pchPendingForm = form;

            if (pchModalText && message) {
                pchModalText.textContent = message;
            }

            pchModal.classList.add('pch-show');
        }

        function pchModalClose() {

            if (!pchModal) {
                return;
            }

            pchModal.classList.remove('pch-show');

            pchPendingForm = null;
        }

        document.addEventListener(
            'submit',
            function (event) {

                const form =
                    event.target.closest('form[data-confirm]');

                if (!form) {
                    return;
                }

                event.preventDefault();

                pchModalOpen(
                    form.getAttribute('data-confirm'),
                    form
                );

            },
            true
        );

        if (pchModalConfirm) {

            pchModalConfirm.addEventListener(
                'click',
                function () {

                    const form =
                        pchPendingForm;

                    pchModalClose();

                    if (form) {

                        /*
                        | Tampilkan loading global SAMPAI halaman baru selesai
                        | dimuat. GlobalLoading middleware sengaja melewatkan
                        | form [data-confirm], jadi loading dipicu di sini
                        | setelah pengguna benar-benar mengkonfirmasi.
                        */

                        const methodOverride =
                            form.querySelector(
                                'input[name="_method"]'
                            );

                        const method =
                            (
                                methodOverride &&
                                methodOverride.value
                            )
                            ? String(
                                methodOverride.value
                            ).toUpperCase()
                            : String(
                                form.getAttribute(
                                    'method'
                                ) || 'POST'
                            ).toUpperCase();

                        let loadingText =
                            'Memproses...';

                        if (
                            method === 'DELETE'
                        ) {
                            loadingText =
                                'Menghapus...';
                        }
                        else if (
                            method === 'PUT' ||
                            method === 'PATCH'
                        ) {
                            loadingText =
                                'Memperbarui...';
                        }
                        else if (
                            method === 'POST'
                        ) {
                            loadingText =
                                'Menyimpan...';
                        }

                        if (
                            window.showGlobalLoading
                        ) {
                            window.showGlobalLoading(
                                loadingText
                            );
                        }

                        /*
                        | submit() native: tidak memicu event,
                        | jadi tidak terjebak loop interceptor.
                        */
                        form.submit();
                    }
                }
            );
        }

        if (pchModalCancel) {
            pchModalCancel.addEventListener('click', pchModalClose);
        }

        if (pchModal) {

            pchModal.addEventListener(
                'click',
                function (event) {

                    if (event.target === pchModal) {
                        pchModalClose();
                    }
                }
            );
        }

        document.addEventListener(
            'keydown',
            function (event) {

                if (
                    event.key === 'Escape' &&
                    pchModal?.classList.contains('pch-show')
                ) {
                    pchModalClose();
                }
            }
        );

    })();
</script>

</body>

</html>