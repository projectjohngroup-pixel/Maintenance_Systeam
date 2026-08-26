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
        (
            function () {

                try {

                    var t =
                        localStorage.getItem('mgr-theme')
                        || 'light';

                    document.documentElement.setAttribute(
                        'data-theme',
                        t
                    );

                } catch (e) {

                    document.documentElement.setAttribute(
                        'data-theme',
                        'light'
                    );

                }

            }
        )();
    </script>


    <title>
        PACHIRA DISTRINUSA - DASHBOARD MANAGER
    </title>


    <link
        rel="icon"
        type="image/x-icon"
        href="{{ asset('favicon.ico') }}"
    >


    <style>

        /* =====================================================
           VARIABEL TEMA
        ===================================================== */

        :root {

            --bg: #f1f5f9;

            --card: #ffffff;

            --card-soft: #f8fafc;

            --text: #0f172a;

            --text-2: #334155;

            --muted: #64748b;

            --border: #e2e8f0;

            --primary: #2563eb;

            --blue: #2563eb;

            --green: #16a34a;

            --amber: #f59e0b;

            --orange: #f97316;

            --red: #dc2626;

            --violet: #7c3aed;

            --teal: #0d9488;

            --gray: #64748b;

            --shadow:
                0 1px 2px rgba(15, 23, 42, .04),
                0 3px 12px rgba(15, 23, 42, .05);

            --radius: 14px;

        }

        [data-theme="dark"] {

            --bg: #0b1120;

            --card: #111a2e;

            --card-soft: #0e1729;

            --text: #e2e8f0;

            --text-2: #cbd5e1;

            --muted: #94a3b8;

            --border: #24304d;

            --shadow:
                0 1px 2px rgba(0, 0, 0, .35),
                0 6px 18px rgba(0, 0, 0, .28);

        }


        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html,
        body {
            min-height: 100%;
        }


        body {

            font-family:
                Arial,
                "Segoe UI",
                Helvetica,
                sans-serif;

            background: var(--bg);

            color: var(--text);

            transition:
                background .25s ease,
                color .25s ease;

        }


        /* =====================================================
           TOPBAR
        ===================================================== */

        .topbar {

            position: sticky;

            top: 0;

            z-index: 500;

            display: flex;

            align-items: center;

            gap: 14px;

            padding: 12px 22px;

            background: var(--card);

            border-bottom: 1px solid var(--border);

            box-shadow: var(--shadow);

        }


        .title h1 {

            font-size: 18px;

            letter-spacing: .02em;

            white-space: nowrap;

        }

        .title p {

            font-size: 11.5px;

            color: var(--muted);

            white-space: nowrap;

        }


        .top-right {

            margin-left: auto;

            display: flex;

            align-items: center;

            gap: 10px;

        }


        /* =====================================================
           PERIODE (SEGMENTED CONTROL)
        ===================================================== */

        .period-switch {

            display: inline-flex;

            background: var(--card-soft);

            border: 1px solid var(--border);

            border-radius: 999px;

            padding: 3px;

            gap: 2px;

            margin-left: 12px;

        }

        .period-btn {

            border: none;

            background: transparent;

            color: var(--muted);

            font-size: 12.5px;

            font-weight: 700;

            padding: 7px 16px;

            border-radius: 999px;

            cursor: pointer;

            transition:
                background .15s ease,
                color .15s ease,
                box-shadow .15s ease;

            white-space: nowrap;

        }

        .period-btn:hover {
            color: var(--text);
        }

        .period-btn.active {

            background: var(--primary);

            color: #fff;

            box-shadow:
                0 4px 12px rgba(37, 99, 235, .35);

        }


        .icon-btn {

            width: 38px;

            height: 38px;

            display: inline-flex;

            align-items: center;

            justify-content: center;

            font-size: 16px;

            color: var(--text-2);

            background: transparent;

            border: 1px solid var(--border);

            border-radius: 10px;

            cursor: pointer;

            transition:
                background .15s ease;

        }

        .icon-btn:hover {
            background: var(--card-soft);
        }

        .icon-btn.loading {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }


        /* =====================================================
           ACCOUNT DROPDOWN
        ===================================================== */

        .manager-account {

            position: relative;

            display: flex;

            align-items: center;

            gap: 10px;

            padding: 6px 12px;

            background: var(--card-soft);

            border: 1px solid var(--border);

            border-radius: 12px;

            cursor: pointer;

            user-select: none;

        }

        .manager-avatar {

            width: 34px;

            height: 34px;

            display: flex;

            align-items: center;

            justify-content: center;

            border-radius: 50%;

            background: linear-gradient(
                135deg,
                var(--primary),
                #7c3aed
            );

            color: #fff;

            font-weight: 700;

            font-size: 15px;

        }

        .manager-name {

            font-size: 13px;

            font-weight: 700;

            max-width: 140px;

            overflow: hidden;

            text-overflow: ellipsis;

            white-space: nowrap;

        }

        .manager-role {

            display: block;

            font-size: 11px;

            color: var(--muted);

        }

        .manager-dropdown {

            position: absolute;

            top: calc(100% + 8px);

            right: 0;

            min-width: 200px;

            background: var(--card);

            border: 1px solid var(--border);

            border-radius: 12px;

            box-shadow: var(--shadow);

            padding: 6px;

            display: none;

            z-index: 600;

        }

        .manager-dropdown.open {
            display: block;
        }

        .manager-dropdown a,
        .manager-dropdown button.danger {

            display: flex;

            align-items: center;

            gap: 10px;

            width: 100%;

            padding: 10px 12px;

            font-size: 13px;

            color: var(--text-2);

            text-decoration: none;

            border: none;

            background: transparent;

            border-radius: 8px;

            cursor: pointer;

            text-align: left;

        }

        .manager-dropdown a:hover,
        .manager-dropdown button.danger:hover {
            background: var(--card-soft);
        }

        .manager-dropdown button.danger {
            color: var(--red);
        }

        .manager-logout-form { width: 100%; }


        /* =====================================================
           LAYOUT UTAMA
        ===================================================== */

        .wrap {

            max-width: 1560px;

            margin: 0 auto;

            padding: 16px 22px 48px;

        }


        .section-title {

            margin: 20px 0 12px;

        }

        .section-title h2 {

            font-size: 15.5px;

            letter-spacing: .02em;

        }

        .section-title p {

            font-size: 12.5px;

            color: var(--muted);

            margin-top: 3px;

        }

        .sec-head {

            display: flex;

            align-items: flex-end;

            justify-content: space-between;

            gap: 12px;

            flex-wrap: wrap;

        }


        .card {

            background: var(--card);

            border: 1px solid var(--border);

            border-radius: var(--radius);

            box-shadow: var(--shadow);

            padding: 14px 16px 13px;

        }

        .card h3 {

            font-size: 13.5px;

            margin-bottom: 2px;

        }

        .card-sub {

            font-size: 11.5px;

            color: var(--muted);

            margin-bottom: 10px;

        }


        .grid-kpi {

            display: grid;

            grid-template-columns:
                repeat(4, minmax(0, 1fr));

            gap: 12px;

        }

        .grid-2 {

            display: grid;

            grid-template-columns:
                repeat(2, minmax(0, 1fr));

            gap: 12px;

        }

        .grid-3 {

            display: grid;

            grid-template-columns:
                repeat(3, minmax(0, 1fr));

            gap: 12px;

        }

        .mt-14 { margin-top: 12px; }

        /* cegah deadlock ukuran grid vs canvas Chart.js
           (tanpa ini kartu tidak bisa menyusut -> overflow horizontal) */

        .grid-kpi > *,
        .grid-2 > *,
        .grid-3 > * {
            min-width: 0;
        }


        /* =====================================================
           KPI CARD PREMIUM
        ===================================================== */

        .kpi-card {

            position: relative;

            overflow: hidden;

            background: var(--card);

            border: 1px solid var(--border);

            border-radius: var(--radius);

            box-shadow: var(--shadow);

            padding: 11px 13px 10px;

            display: flex;

            flex-direction: column;

            gap: 3px;

            transition:
                transform .15s ease;

        }

        .kpi-card:hover {
            transform: translateY(-2px);
        }

        .kpi-card::before {

            content: "";

            position: absolute;

            inset: 0 auto 0 0;

            width: 4px;

        }

        .kc-blue::before   { background: var(--blue); }
        .kc-green::before  { background: var(--green); }
        .kc-amber::before  { background: var(--amber); }
        .kc-red::before    { background: var(--red); }
        .kc-violet::before { background: var(--violet); }
        .kc-teal::before   { background: var(--teal); }
        .kc-orange::before { background: var(--orange); }

        .kpi-top {

            display: flex;

            align-items: center;

            justify-content: space-between;

            gap: 8px;

        }

        .kpi-label {

            font-size: 12px;

            font-weight: 700;

            text-transform: uppercase;

            letter-spacing: .04em;

            color: var(--muted);

        }

        .kpi-icon {

            width: 27px;

            height: 27px;

            display: flex;

            align-items: center;

            justify-content: center;

            border-radius: 8px;

            font-size: 13px;

        }

        .ki-blue   { background: rgba(37,99,235,.12); }
        .ki-green  { background: rgba(22,163,74,.12); }
        .ki-amber  { background: rgba(245,158,11,.14); }
        .ki-red    { background: rgba(220,38,38,.12); }
        .ki-violet { background: rgba(124,58,237,.12); }
        .ki-teal   { background: rgba(13,148,136,.12); }
        .ki-orange { background: rgba(249,115,22,.13); }

        .kpi-value {

            font-size: 26px;

            font-weight: 800;

            line-height: 1.15;

            letter-spacing: -.01em;

        }

        .kpi-value small {

            font-size: 13px;

            font-weight: 700;

            color: var(--muted);

        }

        .kpi-note {

            font-size: 11px;

            color: var(--muted);

        }

        .delta-badge {

            display: inline-flex;

            align-items: center;

            gap: 4px;

            font-size: 11.5px;

            font-weight: 800;

            padding: 3px 9px;

            border-radius: 999px;

        }

        .delta-up-good,
        .delta-down-good {

            background: rgba(22, 163, 74, .13);

            color: var(--green);

        }

        .delta-up-bad,
        .delta-down-bad {

            background: rgba(220, 38, 38, .11);

            color: var(--red);

        }

        .delta-flat {

            background: rgba(100, 116, 139, .12);

            color: var(--muted);

        }

        .mini-split {

            display: flex;

            gap: 4px;

            flex-wrap: wrap;

        }

        .chip {

            display: inline-flex;

            align-items: center;

            gap: 5px;

            font-size: 10.5px;

            font-weight: 700;

            padding: 3px 8px;

            border-radius: 999px;

            background: var(--card-soft);

            border: 1px solid var(--border);

            color: var(--text-2);

        }

        .chip .dot {

            width: 7px;

            height: 7px;

            border-radius: 50%;

        }


        /* =====================================================
           ALERT PANEL
        ===================================================== */

        .alert-panel {

            display: flex;

            flex-direction: column;

            gap: 8px;

            margin-bottom: 16px;

        }

        .alert-item {

            display: flex;

            align-items: center;

            gap: 10px;

            padding: 11px 14px;

            border-radius: 12px;

            font-size: 13px;

            font-weight: 600;

            border: 1px solid transparent;

        }

        .alert-danger {

            background: rgba(220, 38, 38, .09);

            border-color: rgba(220, 38, 38, .3);

            color: #b91c1c;

        }

        .alert-warning {

            background: rgba(245, 158, 11, .1);

            border-color: rgba(245, 158, 11, .35);

            color: #b45309;

        }

        .alert-info {

            background: rgba(37, 99, 235, .08);

            border-color: rgba(37, 99, 235, .28);

            color: var(--primary);

        }

        [data-theme="dark"] .alert-danger { color: #fca5a5; }
        [data-theme="dark"] .alert-warning { color: #fcd34d; }
        [data-theme="dark"] .alert-info { color: #93c5fd; }


        /* =====================================================
           CHART CONTAINER
        ===================================================== */

        .chart-box {

            position: relative;

            width: 100%;

            height: 270px;

        }

        .chart-box.tall { height: 320px; }

        .chart-box.short { height: 235px; }


        /* =====================================================
           RANKING LIST
        ===================================================== */

        .ranking {

            display: flex;

            flex-direction: column;

            gap: 8px;

        }

        .ranking-item {

            display: flex;

            align-items: center;

            gap: 12px;

        }

        .ranking-no {

            flex-shrink: 0;

            width: 26px;

            height: 26px;

            display: flex;

            align-items: center;

            justify-content: center;

            font-size: 12px;

            font-weight: 800;

            border-radius: 8px;

            background: var(--card-soft);

            border: 1px solid var(--border);

            color: var(--muted);

        }

        .ranking-item:nth-child(1) .ranking-no {

            background: var(--primary);

            border-color: var(--primary);

            color: #fff;

        }

        .ranking-main { flex: 1; min-width: 0; }

        .ranking-name {

            font-size: 12px;

            font-weight: 700;

            margin-bottom: 4px;

            overflow: hidden;

            text-overflow: ellipsis;

            white-space: nowrap;

        }

        .ranking-name span.pct {

            float: right;

            font-weight: 800;

            color: var(--primary);

        }

        .ranking-bar {

            height: 8px;

            border-radius: 999px;

            background: var(--card-soft);

            border: 1px solid var(--border);

            overflow: hidden;

        }

        .ranking-fill {

            height: 100%;

            border-radius: 999px;

            background: linear-gradient(
                90deg,
                var(--primary),
                #60a5fa
            );

            transition: width .5s ease;

        }

        .rf-green  { background: linear-gradient(90deg,#16a34a,#4ade80) !important; }
        .rf-orange { background: linear-gradient(90deg,#f97316,#fdba74) !important; }
        .rf-violet { background: linear-gradient(90deg,#7c3aed,#c4b5fd) !important; }
        .rf-teal   { background: linear-gradient(90deg,#0d9488,#5eead4) !important; }
        .rf-red    { background: linear-gradient(90deg,#dc2626,#fca5a5) !important; }

        .ranking-value {

            flex-shrink: 0;

            font-size: 13px;

            font-weight: 800;

            min-width: 44px;

            text-align: right;

        }

        .empty-state {

            padding: 26px 10px;

            text-align: center;

            color: var(--muted);

            font-size: 13px;

        }


        /* =====================================================
           PROGRESS INDIKATOR
        ===================================================== */

        .prog-row { margin-bottom: 11px; }

        .prog-head {

            display: flex;

            justify-content: space-between;

            font-size: 12px;

            font-weight: 700;

            margin-bottom: 5px;

        }

        .prog-track {

            height: 10px;

            border-radius: 999px;

            background: var(--card-soft);

            border: 1px solid var(--border);

            overflow: hidden;

        }

        .prog-fill {

            height: 100%;

            border-radius: 999px;

            transition: width .6s ease;

        }


        .stat-chips {

            display: flex;

            flex-wrap: wrap;

            gap: 8px;

            margin-top: 4px;

        }

        .stat-chip {

            background: var(--card-soft);

            border: 1px solid var(--border);

            border-radius: 10px;

            padding: 8px 12px;

            min-width: 118px;

        }

        .stat-chip b {

            display: block;

            font-size: 16px;

            margin-top: 2px;

        }

        .stat-chip span {

            font-size: 11px;

            color: var(--muted);

            font-weight: 700;

            text-transform: uppercase;

            letter-spacing: .04em;

        }


        /* =====================================================
           STOK MINI CARDS & BADGE KONDISI
        ===================================================== */

        .stok-grid {

            display: grid;

            grid-template-columns: repeat(5, minmax(0, 1fr));

            gap: 8px;

            margin-bottom: 12px;

        }

        .stok-mini {

            background: var(--card-soft);

            border: 1px solid var(--border);

            border-radius: 10px;

            padding: 8px 10px;

        }

        .stok-mini b {

            display: block;

            font-size: 18px;

            margin-top: 2px;

        }

        .stok-mini span {

            font-size: 11px;

            color: var(--muted);

            font-weight: 700;

            text-transform: uppercase;

            letter-spacing: .04em;

        }


        .badge-status {

            padding: 3px 9px;

            font-size: 11px;

            font-weight: 800;

            border-radius: 999px;

            background: var(--card-soft);

            border: 1px solid var(--border);

            color: var(--text-2);

            white-space: nowrap;

        }

        .bs-open     { background: rgba(37,99,235,.12);  color: var(--blue);   border-color: rgba(37,99,235,.3); }
        .bs-progress { background: rgba(245,158,11,.14); color: var(--amber);  border-color: rgba(245,158,11,.35); }
        .bs-hold     { background: rgba(124,58,237,.12); color: var(--violet); border-color: rgba(124,58,237,.3); }
        .bs-selesai  { background: rgba(22,163,74,.12);  color: var(--green);  border-color: rgba(22,163,74,.3); }
        .bs-habis    { background: rgba(220,38,38,.12);  color: var(--red);    border-color: rgba(220,38,38,.32); }
        .bs-menipis  { background: rgba(249,115,22,.13); color: var(--orange); border-color: rgba(249,115,22,.34); }


        .rising-flag {

            display: inline-flex;

            align-items: center;

            gap: 6px;

            font-size: 12px;

            font-weight: 800;

            padding: 5px 12px;

            border-radius: 999px;

            margin-right: 8px;

        }

        .rising-danger {

            background: rgba(220, 38, 38, .12);

            color: var(--red);

            border: 1px solid rgba(220, 38, 38, .32);

        }

        .rising-ok {

            background: rgba(22, 163, 74, .12);

            color: var(--green);

            border: 1px dashed rgba(22, 163, 74, .4);

        }


        /* =====================================================
           MANAGEMENT INSIGHT
        ===================================================== */

        .insight-list {

            display: flex;

            flex-direction: column;

            gap: 8px;

        }

        .insight-item {

            display: flex;

            align-items: flex-start;

            gap: 10px;

            padding: 9px 12px;

            background: var(--card-soft);

            border: 1px solid var(--border);

            border-radius: 10px;

            font-size: 12.5px;

            line-height: 1.45;

            color: var(--text-2);

        }

        .insight-item .ic {

            flex-shrink: 0;

            width: 26px;

            height: 26px;

            display: flex;

            align-items: center;

            justify-content: center;

            border-radius: 8px;

            font-size: 13px;

        }

        .ic-red    { background: rgba(220,38,38,.12); }
        .ic-green  { background: rgba(22,163,74,.12); }
        .ic-blue   { background: rgba(37,99,235,.12); }
        .ic-violet { background: rgba(124,58,237,.12); }
        .ic-orange { background: rgba(249,115,22,.13); }
        .ic-teal   { background: rgba(13,148,136,.12); }

        .insight-item b { color: var(--text); }


        /* =====================================================
           TOMBOL LIHAT DETAIL
        ===================================================== */

        .detail-btn {

            padding: 6px 13px;

            font-size: 12px;

            font-weight: 700;

            color: var(--primary);

            background: rgba(37, 99, 235, .08);

            border: 1px solid rgba(37, 99, 235, .28);

            border-radius: 999px;

            cursor: pointer;

            transition:
                background .15s ease,
                transform .15s ease;

            white-space: nowrap;

        }

        .detail-btn:hover {

            background: rgba(37, 99, 235, .16);

            transform: translateY(-1px);

        }

        [data-theme="dark"] .detail-btn {

            color: #93c5fd;

            background: rgba(96, 165, 250, .1);

            border-color: rgba(96, 165, 250, .35);

        }


        /* =====================================================
           MODAL DETAIL
        ===================================================== */

        .detail-overlay {

            position: fixed;

            inset: 0;

            z-index: 1200;

            display: none;

            align-items: center;

            justify-content: center;

            padding: 16px;

            background: rgba(15, 23, 42, .55);

            backdrop-filter: blur(2px);

        }

        .detail-overlay.active { display: flex; }

        .detail-modal {

            width: min(1020px, 100%);

            max-height: min(84vh, 800px);

            display: flex;

            flex-direction: column;

            background: var(--card);

            border-radius: 16px;

            box-shadow: var(--shadow);

            overflow: hidden;

        }

        .detail-head {

            display: flex;

            align-items: flex-start;

            justify-content: space-between;

            gap: 12px;

            padding: 18px 20px 12px;

            border-bottom: 1px solid var(--border);

        }

        .detail-head h3 { font-size: 16.5px; }

        .detail-head p {

            margin-top: 4px;

            font-size: 12.5px;

            color: var(--muted);

        }

        .detail-close {

            flex-shrink: 0;

            width: 32px;

            height: 32px;

            font-size: 18px;

            line-height: 1;

            color: var(--muted);

            background: transparent;

            border: none;

            border-radius: 8px;

            cursor: pointer;

        }

        .detail-close:hover { background: var(--card-soft); }

        .detail-body {

            padding: 14px 20px 20px;

            overflow: auto;

        }

        .detail-loading,
        .detail-empty,
        .detail-error {

            padding: 28px 8px;

            text-align: center;

            color: var(--muted);

            font-size: 13.5px;

        }

        .detail-error { color: var(--red); }

        .detail-table-wrap {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .detail-table {

            width: 100%;

            border-collapse: collapse;

            font-size: 13px;

        }

        .detail-table th,

        .detail-table td {

            padding: 9px 10px;

            text-align: left;

            border-bottom: 1px solid var(--border);

            white-space: nowrap;

        }

        .detail-table thead th {

            position: sticky;

            top: 0;

            background: var(--card-soft);

            color: var(--text-2);

            font-size: 11.5px;

            text-transform: uppercase;

            letter-spacing: .04em;

        }

        .detail-total {

            margin-top: 12px;

            font-size: 13px;

            color: var(--muted);

        }


        /* =====================================================
           AI ASSISTANT
        ===================================================== */

        .ai-button {

            position: fixed;

            right: 22px;

            bottom: 22px;

            z-index: 900;

            width: 54px;

            height: 54px;

            border: none;

            border-radius: 50%;

            font-size: 22px;

            cursor: pointer;

            color: #fff;

            background: linear-gradient(
                135deg,
                var(--primary),
                var(--violet)
            );

            box-shadow: var(--shadow);

        }

        .ai-panel {

            position: fixed;

            right: 22px;

            bottom: 88px;

            z-index: 950;

            width: 380px;

            height: 520px;

            display: flex;

            flex-direction: column;

            background: var(--card);

            border: 1px solid var(--border);

            border-radius: 16px;

            box-shadow: var(--shadow);

            overflow: hidden;

            opacity: 0;

            pointer-events: none;

            transform: translateY(10px);

            transition:
                opacity .2s ease,
                transform .2s ease;

        }

        .ai-panel.active {

            opacity: 1;

            pointer-events: auto;

            transform: translateY(0);

        }

        .ai-header {

            display: flex;

            align-items: center;

            justify-content: space-between;

            padding: 12px 14px;

            background: linear-gradient(
                135deg,
                var(--primary),
                var(--violet)
            );

            color: #fff;

        }

        .ai-header-title { font-size: 14px; font-weight: 800; }

        .ai-header-status { font-size: 11px; opacity: .85; }

        .ai-body {

            flex: 1;

            padding: 12px;

            overflow-y: auto;

            display: flex;

            flex-direction: column;

            gap: 8px;

        }

        .ai-msg {

            max-width: 85%;

            padding: 9px 12px;

            font-size: 13px;

            border-radius: 12px;

            line-height: 1.45;

            word-wrap: break-word;

            white-space: pre-wrap;

        }

        .ai-msg.bot {

            align-self: flex-start;

            background: var(--card-soft);

            border: 1px solid var(--border);

        }

        .ai-msg.user {

            align-self: flex-end;

            background: var(--primary);

            color: #fff;

        }

        .ai-msg.error {

            align-self: flex-start;

            background: rgba(220, 38, 38, .1);

            color: var(--red);

            border: 1px solid rgba(220, 38, 38, .3);

        }

        .ai-footer {

            display: flex;

            gap: 8px;

            padding: 10px;

            border-top: 1px solid var(--border);

        }

        .ai-footer input {

            flex: 1;

            padding: 10px 12px;

            font-size: 13px;

            border: 1px solid var(--border);

            border-radius: 10px;

            background: var(--card-soft);

            color: var(--text);

            outline: none;

        }

        .ai-footer input:focus {
            border-color: var(--primary);
        }

        .ai-footer button {

            padding: 10px 14px;

            border: none;

            border-radius: 10px;

            background: var(--primary);

            color: #fff;

            font-weight: 800;

            cursor: pointer;

        }


        /* =====================================================
           RESPONSIVE
        ===================================================== */

        @media (max-width: 1280px) {

            .grid-kpi { grid-template-columns: repeat(4, minmax(0, 1fr)); }

            .grid-3 { grid-template-columns: repeat(2, minmax(0, 1fr)); }

        }

        @media (max-width: 1100px) {

            .grid-kpi { grid-template-columns: repeat(2, minmax(0, 1fr)); }

        }

        @media (max-width: 1024px) {

            .grid-2 { grid-template-columns: 1fr; }

            .grid-3 { grid-template-columns: repeat(2, minmax(0, 1fr)); }

            .stok-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); }

            .title p { display: none; }

        }

        @media (max-width: 768px) {

            .topbar {

                flex-wrap: wrap;

                padding: 10px 14px;

                row-gap: 8px;

            }

            .top-right { margin-left: auto; }

            .period-switch {

                order: 5;

                width: 100%;

                margin-left: 0;

                justify-content: stretch;

                overflow-x: auto;

            }

            .period-btn { flex: 1; padding: 7px 10px; }

            .wrap { padding: 12px 12px 80px; }

            .grid-2,

            .grid-3 { grid-template-columns: 1fr; }

            .grid-kpi { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 8px; }

            .kpi-card { padding: 9px 11px 9px; }

            .kpi-value { font-size: 22px; }

            .kpi-icon { display: none; }

            .stok-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }

            .chart-box,

            .chart-box.tall { height: 240px; }

            .chart-box.short { height: 220px; }

            .section-title { margin: 16px 0 10px; }

            .manager-name,
            .manager-role { display: none; }

            .ai-panel {

                right: 10px;

                left: 10px;

                bottom: 86px;

                width: auto;

                height: min(520px, calc(100vh - 110px));

            }

            .ai-button { right: 14px; bottom: 14px; }

            .detail-overlay { padding: 8px; }

            .detail-head { padding: 14px 14px 10px; }

            .detail-body { padding: 10px 14px 14px; }

            .detail-table { font-size: 12px; }

        }

        @media (max-width: 400px) {

            .grid-kpi { grid-template-columns: 1fr; }

            .kpi-value { font-size: 24px; }

            .stok-grid { grid-template-columns: 1fr; }

        }

        @media (prefers-reduced-motion: reduce) {

            * {
                animation: none !important;
                transition: none !important;
            }

        }

    </style>

</head>


<body>


    <!-- =========================================================
         TOPBAR
    ========================================================== -->

    <header class="topbar">

        <div class="title">

            <h1>
                Dashboard Manager
            </h1>

            <p>
                Pusat Monitoring Manajemen Maintenance
            </p>

        </div>


        {{-- =============================================
             FILTER PERIODE
        ============================================== --}}

        <div
            class="period-switch"
            id="periodSwitch"
        >

            <button
                type="button"
                class="period-btn"
                data-period="day"
            >
                Hari
            </button>

            <button
                type="button"
                class="period-btn"
                data-period="week"
            >
                Minggu
            </button>

            <button
                type="button"
                class="period-btn active"
                data-period="month"
            >
                Bulan
            </button>

            <button
                type="button"
                class="period-btn"
                data-period="year"
            >
                Tahun
            </button>

        </div>


        <div class="top-right">


            <button
                type="button"
                class="icon-btn"
                id="refreshBtn"
                title="Muat ulang data"
            >
                &#8635;
            </button>


            <button
                type="button"
                class="icon-btn"
                id="themeToggleMgr"
                title="Ganti tema"
            >
                <span class="tt-icon">&#9728;</span>
            </button>


            {{-- =========================================
                 ACCOUNT
            ========================================== --}}

            <div
                class="manager-account"
                id="managerAccountButton"
                role="button"
                tabindex="0"
                aria-haspopup="true"
                aria-expanded="false"
            >

                <div class="manager-avatar">
                    {{ strtoupper(substr(auth()->user()->name ?? 'M', 0, 1)) }}
                </div>

                <div>
                    <div class="manager-name">
                        {{ auth()->user()->name ?? 'Manager' }}
                    </div>
                    <span class="manager-role">
                        Management
                    </span>
                </div>

                <div
                    class="manager-dropdown"
                    id="managerAccountDropdown"
                >
                    <a href="{{ route('profile.photo') }}">
                        &#128100;
                        <span>
                            Ubah Foto Profil
                        </span>
                    </a>

                    <a href="{{ route('password.edit') }}">
                        &#128274;
                        <span>
                            Ubah Password
                        </span>
                    </a>

                    <form
                        action="{{ route('logout') }}"
                        method="POST"
                        class="manager-logout-form"
                    >
                        @csrf
                        <button
                            type="submit"
                            class="danger"
                        >
                            &#8677;
                            <span>
                                Keluar
                            </span>
                        </button>
                    </form>
                </div>

            </div>

        </div>

    </header>


    <main class="wrap">


        {{-- =============================================
             ALERT PANEL
        ============================================== --}}

        <div
            class="alert-panel"
            id="alertPanel"
        ></div>


        {{-- =============================================
             1. RINGKASAN KPI UTAMA
        ============================================== --}}

        <div class="grid-kpi" id="kpiGrid">

            <div class="kpi-card kc-blue">
                <div class="kpi-top">
                    <span class="kpi-label">
                        WO Masuk
                    </span>
                    <span class="kpi-icon ki-blue">&#128203;</span>
                </div>
                <div class="kpi-value" id="kpiMasuk">-</div>
                <div class="kpi-note" id="kpiMasukNote">
                    Periode aktif
                </div>
            </div>

            <div class="kpi-card kc-green">
                <div class="kpi-top">
                    <span class="kpi-label">
                        WO Selesai
                    </span>
                    <span class="kpi-icon ki-green">&#9989;</span>
                </div>
                <div class="kpi-value" id="kpiSelesai">-</div>
                <div class="kpi-note" id="kpiSelesaiNote">
                    Periode aktif
                </div>
            </div>

            <div class="kpi-card kc-amber">
                <div class="kpi-top">
                    <span class="kpi-label">
                        Belum Selesai
                    </span>
                    <span class="kpi-icon ki-amber">&#9203;</span>
                </div>
                <div class="kpi-value" id="kpiBelum">-</div>
                <div class="kpi-note">
                    <span class="mini-split">
                        <span class="chip">
                            <span class="dot" style="background:var(--blue)"></span>
                            OPEN <b id="kpiOpen">-</b>
                        </span>
                        <span class="chip">
                            <span class="dot" style="background:var(--amber)"></span>
                            PROSES <b id="kpiProgress">-</b>
                        </span>
                        <span class="chip">
                            <span class="dot" style="background:var(--violet)"></span>
                            HOLD <b id="kpiHold">-</b>
                        </span>
                    </span>
                </div>
            </div>

            <div class="kpi-card kc-red">
                <div class="kpi-top">
                    <span class="kpi-label">
                        Overdue (SLA)
                    </span>
                    <span class="kpi-icon ki-red">&#9888;</span>
                </div>
                <div class="kpi-value" id="kpiOverdue">-</div>
                <div class="kpi-note">
                    Melewati batas waktu prioritas
                </div>
            </div>

            <div class="kpi-card kc-red">
                <div class="kpi-top">
                    <span class="kpi-label">
                        Emergency
                    </span>
                    <span class="kpi-icon ki-red">&#128680;</span>
                </div>
                <div class="kpi-value" id="kpiEmergency">-</div>
                <div class="kpi-note" id="kpiEmergencyNote">
                    Prioritas tertinggi
                </div>
            </div>

            <div class="kpi-card kc-orange">
                <div class="kpi-top">
                    <span class="kpi-label">
                        Urgent
                    </span>
                    <span class="kpi-icon ki-orange">&#128295;</span>
                </div>
                <div class="kpi-value" id="kpiUrgent">-</div>
                <div class="kpi-note" id="kpiUrgentNote">
                    Prioritas menengah
                </div>
            </div>

            <div class="kpi-card kc-teal">
                <div class="kpi-top">
                    <span class="kpi-label">
                        Rata-rata Penyelesaian
                    </span>
                    <span class="kpi-icon ki-teal">&#8986;</span>
                </div>
                <div class="kpi-value" id="kpiAvgCompletion">-</div>
                <div class="kpi-note">
                    Kerusakan sampai selesai
                </div>
            </div>

            <div class="kpi-card kc-violet">
                <div class="kpi-top">
                    <span class="kpi-label">
                        Total Downtime
                    </span>
                    <span class="kpi-icon ki-violet">&#9881;</span>
                </div>
                <div class="kpi-value" id="kpiDowntime">-</div>
                <div class="kpi-note">
                    Estimasi dari durasi WO
                </div>
            </div>

        </div>


        {{-- =============================================
             2. WO MASUK & PENYELESAIAN (TREND)
        ============================================== --}}

        <div class="sec-head section-title">

            <div>
                <h2>
                    Work Order Masuk &amp; Penyelesaian
                </h2>

                <p id="trendSubtitle">
                    Trend WO yang masuk dibandingkan yang selesai
                </p>
            </div>

            <button
                type="button"
                class="detail-btn"
                data-metric="chart-trend"
            >
                Lihat Detail
            </button>

        </div>

        <div class="card">

            <div
                class="chart-box tall"
                id="woTrendBox"
            >
                <canvas id="woTrendChart"></canvas>
            </div>

        </div>


        {{-- =============================================
             3. WO BERDASARKAN DEPARTMENT
        ============================================== --}}

        <div class="sec-head section-title">

            <div>
                <h2>
                    Work Order per Department Pengaju
                </h2>

                <p>
                    Department yang paling banyak mengajukan Work Order beserta kontribusinya
                </p>
            </div>

            <button
                type="button"
                class="detail-btn"
                data-metric="top-departments"
            >
                Lihat Detail
            </button>

        </div>

        <div class="grid-2 mt-0">

            <div class="card">

                <h3>
                    Komposisi Department
                </h3>

                <div class="card-sub">
                    Jumlah WO per department pada periode aktif
                </div>

                <div class="chart-box">
                    <canvas id="deptChart"></canvas>
                </div>

            </div>

            <div class="card">

                <h3>
                    Ranking Department
                </h3>

                <div class="card-sub">
                    Kontribusi persentase tiap department
                </div>

                <div id="deptRanking"></div>

            </div>

        </div>


        {{-- =============================================
             4. STATUS WORK ORDER
        ============================================== --}}

        <div class="sec-head section-title">

            <div>
                <h2>
                    Status Work Order
                </h2>

                <p>
                    Komposisi status dan perkembangannya selama periode berjalan
                </p>
            </div>

            <button
                type="button"
                class="detail-btn"
                data-metric="chart-status"
            >
                Lihat Detail
            </button>

        </div>

        <div class="grid-2">

            <div class="card">

                <h3>
                    Komposisi Status Saat Ini
                </h3>

                <div class="card-sub">
                    OPEN / IN PROSES / SELESAI / HOLD
                </div>

                <div class="chart-box short">
                    <canvas id="statusDonut"></canvas>
                </div>

            </div>

            <div class="card">

                <h3>
                    Perkembangan Status
                </h3>

                <div class="card-sub">
                    Distribusi status WO baru per titik waktu
                </div>

                <div class="chart-box short">
                    <canvas id="statusTrendChart"></canvas>
                </div>

            </div>

        </div>


        {{-- =============================================
             5. PRIORITAS WORK ORDER
        ============================================== --}}

        <div class="sec-head section-title">

            <div>
                <h2>
                    Prioritas Work Order
                </h2>

                <p>
                    EMERGENCY / URGENT / NORMAL beserta indikator kenaikan
                </p>
            </div>

            <div>

                <span
                    id="flagEmergency"
                    style="display:none;"
                >
                    <span class="rising-flag rising-danger">
                        &#9888; EMERGENCY NAIK
                    </span>
                </span>

                <span
                    id="flagUrgent"
                    style="display:none;"
                >
                    <span class="rising-flag rising-danger">
                        &#9888; URGENT NAIK
                    </span>
                </span>

                <button
                    type="button"
                    class="detail-btn"
                    data-metric="chart-priority"
                >
                    Lihat Detail
                </button>

            </div>

        </div>

        <div class="grid-2">

            <div class="card">

                <h3>
                    Komposisi Prioritas
                </h3>

                <div class="card-sub">
                    Persentase masing-masing prioritas
                </div>

                <div class="chart-box short">
                    <canvas id="priorityDonut"></canvas>
                </div>

            </div>

            <div class="card">

                <h3>
                    Trend Prioritas
                </h3>

                <div class="card-sub">
                    Jumlah WO per prioritas di tiap titik waktu
                </div>

                <div class="chart-box short">
                    <canvas id="priorityTrendChart"></canvas>
                </div>

            </div>

        </div>


        {{-- =============================================
             6. TOP MESIN / AREA / KATEGORI
        ============================================== --}}

        <div class="sec-head section-title">

            <div>
                <h2>
                    Analisis Mesin, Area &amp; Kategori Kerusakan
                </h2>

                <p>
                    Sumber masalah utama berdasarkan data Work Order aktual
                </p>
            </div>

        </div>

        <div class="grid-3">

            <div class="card">

                <div class="sec-head" style="margin-bottom:10px;">
                    <h3>
                        Top Mesin Bermasalah
                    </h3>
                    <button
                        type="button"
                        class="detail-btn"
                        data-metric="top-machines"
                    >
                        Detail
                    </button>
                </div>

                <div id="mesinRanking"></div>

            </div>

            <div class="card">

                <div class="sec-head" style="margin-bottom:10px;">
                    <h3>
                        Top Area Kerusakan
                    </h3>
                    <button
                        type="button"
                        class="detail-btn"
                        data-metric="top-areas"
                    >
                        Detail
                    </button>
                </div>

                <div id="areaRanking"></div>

            </div>

            <div class="card">

                <div class="sec-head" style="margin-bottom:10px;">
                    <h3>
                        Top Kategori Kerusakan
                    </h3>
                    <button
                        type="button"
                        class="detail-btn"
                        data-metric="top-categories"
                    >
                        Detail
                    </button>
                </div>

                <div id="kategoriRanking"></div>

            </div>

        </div>


        {{-- =============================================
             8. TREND KERUSAKAN MESIN & DOWNTIME
        ============================================== --}}

        <div class="sec-head section-title">

            <div>
                <h2>
                    Trend Kerusakan Mesin &amp; Downtime
                </h2>

                <p>
                    Perbandingan antar periode waktu dan estimasi downtime per mesin
                </p>
            </div>

        </div>

        <div class="grid-2">

            <div class="card">

                <h3>
                    Kerusakan per Titik Waktu
                </h3>

                <div class="card-sub">
                    Jumlah WO kerusakan mesin
                </div>

                <div class="chart-box">
                    <canvas id="machineTrendChart"></canvas>
                </div>

            </div>

            <div class="card">

                <div class="sec-head" style="margin-bottom:10px;">
                    <div>
                        <h3>
                            Downtime Terbesar per Mesin
                        </h3>

                        <div class="card-sub">
                            Estimasi dari durasi kerusakan sampai selesai
                            <b id="downtimeTotalInline"></b>
                        </div>
                    </div>

                    <button
                        type="button"
                        class="detail-btn"
                        data-metric="downtime-by-mesin"
                    >
                        Detail
                    </button>
                </div>

                <div id="downtimeRanking"></div>

                <button
                    type="button"
                    class="detail-btn"
                    data-metric="downtime-by-area"
                    style="margin-top:12px;"
                >
                    Lihat Downtime per Area
                </button>

            </div>

        </div>


        {{-- =============================================
             9. MONITORING BARANG / SPAREPART
        ============================================== --}}

        <div class="sec-head section-title">

            <div>
                <h2>
                    Monitoring Barang / Sparepart
                </h2>

                <p>
                    Stok, barang masuk, barang keluar, dan barang kritis dari modul Inventory
                </p>
            </div>

        </div>

        <div class="card mb-14">

            <div class="stok-grid">

                <div class="stok-mini">
                    <span>
                        Total Item
                    </span>
                    <b id="stokTotal">-</b>
                </div>

                <div class="stok-mini">
                    <span>
                        Stok Aman
                    </span>
                    <b style="color:var(--green)" id="stokAman">-</b>
                </div>

                <div class="stok-mini">
                    <span>
                        Stok Menipis
                    </span>
                    <b style="color:var(--orange)" id="stokMenipis">-</b>
                </div>

                <div class="stok-mini">
                    <span>
                        Stok Habis
                    </span>
                    <b style="color:var(--red)" id="stokHabis">-</b>
                </div>

                <div class="stok-mini">
                    <span>
                        Rata-rata Stok
                    </span>
                    <b id="stokRata">-</b>
                </div>

            </div>

            <div class="stat-chips">

                <div class="stat-chip">
                    <span>
                        Barang Masuk
                    </span>
                    <b id="invMasukTotal">-</b>
                </div>

                <div class="stat-chip">
                    <span>
                        Barang Keluar
                    </span>
                    <b id="invKeluarTotal">-</b>
                </div>

                <div class="stat-chip">
                    <span>
                        Rata-rata Masuk / Titik
                    </span>
                    <b id="invMasukAvg">-</b>
                </div>

                <div class="stat-chip">
                    <span>
                        Rata-rata Keluar / Titik
                    </span>
                    <b id="invKeluarAvg">-</b>
                </div>

                <button
                    type="button"
                    class="detail-btn"
                    data-metric="inventory-stok"
                    style="align-self:center;"
                >
                    Daftar Stok
                </button>

            </div>

        </div>

        <div class="grid-2 mt-14">

            <div class="card">

                <h3>
                    Trend Barang Masuk vs Keluar
                </h3>

                <div class="card-sub">
                    Total qty per titik waktu pada periode aktif
                </div>

                <div class="chart-box">
                    <canvas id="inventoryTrendChart"></canvas>
                </div>

            </div>

            <div class="card">

                <div class="sec-head" style="margin-bottom:10px;">
                    <div>
                        <h3>
                            Barang Paling Banyak Keluar
                        </h3>

                        <div class="card-sub">
                            Sparepart yang paling sering digunakan
                        </div>
                    </div>

                    <button
                        type="button"
                        class="detail-btn"
                        data-metric="inventory-top-keluar"
                    >
                        Detail
                    </button>
                </div>

                <div id="topKeluarRanking"></div>

            </div>

        </div>

        <div class="card mt-14">

            <div class="sec-head" style="margin-bottom:10px;">
                <div>
                    <h3>
                        Barang Kritis
                    </h3>

                    <div class="card-sub">
                        Habis, stok rendah, atau mendekati stok minimum
                    </div>
                </div>

                <button
                    type="button"
                    class="detail-btn"
                    data-metric="inventory-kritis"
                >
                    Lihat Detail
                </button>
            </div>

            <div
                id="barangKritisList"
                class="ranking"
            ></div>

        </div>


        {{-- =============================================
             10. REKAP DELAY & PERFORMA
        ============================================== --}}

        <div class="sec-head section-title">

            <div>
                <h2>
                    Rekap Delay &amp; Performa Maintenance
                </h2>

                <p>
                    Work order terlambat, belum selesai, dan sumber delay utama
                </p>
            </div>

            <button
                type="button"
                class="detail-btn"
                data-metric="delay-reasons"
            >
                Alasan Pending
            </button>

        </div>

        <div class="grid-2">

            <div class="card">

                <h3>
                    Ringkasan Delay
                </h3>

                <div class="card-sub">
                    Berdasarkan SLA prioritas (E 8 jam / U 24 jam / N 72 jam)
                </div>

                <div class="stat-chips">

                    <div class="stat-chip">
                        <span>
                            WO Terlambat
                        </span>
                        <b style="color:var(--red)" id="delayTerlambat">-</b>
                    </div>

                    <div class="stat-chip">
                        <span>
                            Belum Selesai
                        </span>
                        <b style="color:var(--orange)" id="delayBelum">-</b>
                    </div>

                    <div class="stat-chip">
                        <span>
                            WO Terlama Berjalan
                        </span>
                        <b id="delayTerlama">-</b>
                    </div>

                </div>

                <div
                    id="delayTerlamaCard"
                    style="margin-top:14px;"
                ></div>

                <button
                    type="button"
                    class="detail-btn"
                    data-metric="overdue"
                    style="margin-top:12px;"
                >
                    Lihat Semua WO Terlambat
                </button>

            </div>

            <div class="card">

                <h3>
                    Sumber Delay Utama
                </h3>

                <div class="card-sub">
                    Area / mesin / department dengan WO terlambat terbanyak
                </div>

                <div class="sec-head" style="margin-bottom:6px;">
                    <span class="chip">
                        <span class="dot" style="background:var(--primary)"></span>
                        per Area
                    </span>
                    <button
                        type="button"
                        class="detail-btn"
                        data-metric="delay-by-area"
                    >
                        Detail
                    </button>
                </div>

                <div id="delayByArea"></div>

                <div class="sec-head" style="margin:12px 0 6px;">
                    <span class="chip">
                        <span class="dot" style="background:var(--teal)"></span>
                        per Mesin
                    </span>
                    <button
                        type="button"
                        class="detail-btn"
                        data-metric="delay-by-mesin"
                    >
                        Detail
                    </button>
                </div>

                <div id="delayByMesin"></div>

                <div class="sec-head" style="margin:12px 0 6px;">
                    <span class="chip">
                        <span class="dot" style="background:var(--violet)"></span>
                        per Department
                    </span>
                    <button
                        type="button"
                        class="detail-btn"
                        data-metric="delay-by-department"
                    >
                        Detail
                    </button>
                </div>

                <div id="delayByDepartment"></div>

            </div>

        </div>


        {{-- =============================================
             11. MANAGEMENT INSIGHT & PERFORMA
        ============================================== --}}

        <div class="sec-head section-title">

            <div>
                <h2>
                    Management Insight
                </h2>

                <p>
                    Ringkasan kondisi maintenance dan apakah tim mampu mengejar beban kerja
                </p>
            </div>

            <button
                type="button"
                class="detail-btn"
                data-metric="completion"
            >
                Lihat Detail Performa
            </button>

        </div>

        <div class="grid-2">

            <div class="card">

                <h3>
                    Insight Otomatis
                </h3>

                <div class="card-sub">
                    Disarikan langsung dari data periode aktif
                </div>

                <div
                    class="insight-list"
                    id="insightList"
                >
                    <div class="empty-state">
                        Memuat insight...
                    </div>
                </div>

            </div>

            <div class="card">

                <h3>
                    Performa Penyelesaian
                </h3>

                <div class="card-sub">
                    Completion rate, beban aktif, dan kecepatan respons tim
                </div>

                <div class="prog-row">

                    <div class="prog-head">
                        <span>
                            Completion Rate (Selesai vs Masuk)
                        </span>
                        <b id="completionRateText">-</b>
                    </div>

                    <div class="prog-track">
                        <div
                            class="prog-fill rf-green"
                            id="completionRateBar"
                            style="width:0%"
                        ></div>
                    </div>

                </div>

                <div class="prog-row">

                    <div class="prog-head">
                        <span>
                            Beban Aktif (Belum selesai vs Masuk)
                        </span>
                        <b id="backlogRateText">-</b>
                    </div>

                    <div class="prog-track">
                        <div
                            class="prog-fill rf-orange"
                            id="backlogRateBar"
                            style="width:0%"
                        ></div>
                    </div>

                </div>

                <div class="stat-chips">

                    <div class="stat-chip">
                        <span>
                            Rata-rata Response
                        </span>
                        <b id="statResponse">-</b>
                    </div>

                    <div class="stat-chip">
                        <span>
                            Rata-rata Pengerjaan
                        </span>
                        <b id="statWork">-</b>
                    </div>

                    <div class="stat-chip">
                        <span>
                            Rata-rata Penyelesaian
                        </span>
                        <b id="statCompletion">-</b>
                    </div>

                </div>

            </div>

        </div>


        <div
            class="card mt-14"
            style="display:flex;justify-content:space-between;flex-wrap:wrap;gap:8px;"
        >

            <span style="font-size:12px;color:var(--muted);">
                Data aktual sistem &bull; diperbarui otomatis saat halaman dibuka
                &bull; <span id="generatedAt"></span>
            </span>

            <button
                type="button"
                class="detail-btn"
                data-metric="total"
            >
                Seluruh Work Order Periode Ini
            </button>

        </div>


    </main>


    {{-- =========================================================
         AI BUTTON + PANEL
    ========================================================== --}}

    <button
        type="button"
        id="aiAssistantButton"
        class="ai-button"
        title="Pachira AI Assistant"
        aria-label="Pachira AI Assistant"
    >
        &#129302;
    </button>

    <div
        id="aiAssistantPanel"
        class="ai-panel"
    >

        <div class="ai-header">

            <div>
                <div class="ai-header-title">
                    Pachira AI Assistant
                </div>
                <div class="ai-header-status">
                    &#9679; Online
                </div>
            </div>

            <button
                type="button"
                id="aiClose"
                style="background:none;border:none;color:#fff;font-size:16px;cursor:pointer;"
            >
                &#10005;
            </button>

        </div>

        <div
            class="ai-body"
            id="aiBody"
        >
            <div class="ai-msg bot">
                Halo! Tanyakan apa saja tentang monitoring maintenance Anda.
            </div>
        </div>

        <div class="ai-footer">

            <input
                type="text"
                id="aiQuestion"
                placeholder="Tulis pertanyaan..."
                autocomplete="off"
            >

            <button
                type="button"
                id="aiSend"
            >
                &#10148;
            </button>

        </div>

    </div>


    {{-- =========================================================
         MODAL LIHAT DETAIL (READ ONLY)
    ========================================================== --}}

    <div
        class="detail-overlay"
        id="detailOverlay"
        aria-hidden="true"
    >

        <div
            class="detail-modal"
            role="dialog"
            aria-modal="true"
            aria-labelledby="detailTitle"
        >

            <div class="detail-head">

                <div>
                    <h3 id="detailTitle">
                        Detail
                    </h3>
                    <p id="detailSubtitle"></p>
                </div>

                <button
                    type="button"
                    class="detail-close"
                    id="detailClose"
                    aria-label="Tutup"
                >
                    &times;
                </button>

            </div>

            <div
                class="detail-body"
                id="detailBody"
            >
                <div class="detail-empty">
                    Klik tombol "Lihat Detail" untuk melihat data.
                </div>
            </div>

        </div>

    </div>


    <script src="{{ asset('assets/vendor/chart.umd.min.js') }}"></script>

    <script>
    (
        function () {

            'use strict';


            var DATA_URL =
                "{{ route('dashboard.manager.data') }}";

            var DETAIL_URL =
                "{{ route('dashboard.manager.detail') }}";


            var state = {

                period: 'month',

                payload: null,

                charts: {}

            };


            /* -------------------------------------------------
               PALET WARNA - SAMA DENGAN DASHBOARD ADMIN
            ------------------------------------------------- */

            function theme() {

                return document.documentElement.getAttribute(
                    'data-theme'
                ) === 'dark'
                    ? 'dark'
                    : 'light';

            }

            function palette() {

                var dark =
                    theme() === 'dark';

                return {

                    dark: dark,

                    text:
                        dark ? '#cbd5e1' : '#334155',

                    muted:
                        dark ? '#94a3b8' : '#64748b',

                    grid:
                        dark
                            ? 'rgba(148,163,184,.12)'
                            : 'rgba(15,23,42,.07)',

                    primary: '#2563eb',

                    green: '#16a34a',

                    amber: '#f59e0b',

                    red: '#dc2626',

                    cyan: '#0891b2',

                    violet: '#7c3aed',

                    pink: '#db2777',

                    orange: '#f97316',

                    teal: '#0d9488',

                    slate: '#64748b'

                };

            }

            function hexToRgba(hex, alpha) {

                var r =
                    parseInt(hex.slice(1, 3), 16);

                var g =
                    parseInt(hex.slice(3, 5), 16);

                var b =
                    parseInt(hex.slice(5, 7), 16);

                return 'rgba('
                    + r + ', '
                    + g + ', '
                    + b + ', '
                    + alpha + ')';

            }


            function fmtNum(value) {

                if (
                    value === null
                    || value === undefined
                ) {
                    return '-';
                }

                return Number(value)
                    .toLocaleString('id-ID');

            }

            function fmtQty(value) {

                if (
                    value === null
                    || value === undefined
                ) {
                    return '-';
                }

                var n = Number(value);

                if (Number.isInteger(n)) {

                    return n.toLocaleString('id-ID');

                }

                return n.toLocaleString(
                    'id-ID',
                    {
                        maximumFractionDigits: 1
                    }
                );

            }

            function fmtHours(value) {

                if (
                    value === null
                    || value === undefined
                ) {
                    return '-';
                }

                var h = Number(value);

                if (h >= 72) {

                    return (h / 24).toFixed(1)
                        + ' hari';

                }

                return h.toFixed(1) + ' jam';

            }


            function esc(value) {

                var div =
                    document.createElement('div');

                div.textContent =
                    (value === null || value === undefined)
                        ? ''
                        : String(value);

                return div.innerHTML;

            }


            /* -------------------------------------------------
               MUAT DATA DASHBOARD
            ------------------------------------------------- */

            function loadDashboard(period) {

                state.period = period || state.period;


                var refreshBtn =
                    document.getElementById('refreshBtn');

                if (refreshBtn) {

                    refreshBtn.classList.add('loading');

                }


                fetch(
                    DATA_URL
                    + '?period='
                    + encodeURIComponent(state.period),

                    {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        credentials: 'same-origin'
                    }
                )

                    .then(function (r) {
                        return r.json();
                    })

                    .then(function (data) {

                        state.payload = data;

                        renderAll();

                    })

                    .catch(function () {

                        var panel =
                            document.getElementById('alertPanel');

                        if (panel) {

                            panel.innerHTML =
                                '<div class="alert-item alert-danger">'
                                + 'Gagal memuat data dashboard. Coba muat ulang.'
                                + '</div>';

                        }

                    })

                    .finally(function () {

                        if (refreshBtn) {

                            refreshBtn.classList.remove(
                                'loading'
                            );

                        }

                    });

            }


            /* -------------------------------------------------
               HAPUS CHART LAMA SEBELUM RENDER ULANG
            ------------------------------------------------- */

            function killChart(id) {

                if (
                    state.charts[id]
                    && typeof state.charts[id].destroy === 'function'
                ) {

                    state.charts[id].destroy();

                }

                delete state.charts[id];

            }


            /* -------------------------------------------------
               OPSI DASAR CHART - SAMA DENGAN DASHBOARD ADMIN
               opts.legend     : tampilkan legenda bawah
               opts.horizontal : bar horizontal (indexAxis y)
               opts.noScales   : tanpa sumbu (doughnut)
               opts.stacked    : sumbu bertumpuk
            ------------------------------------------------- */

            function baseOptions(p, extra) {

                extra = extra || {};

                var conf = {

                    responsive: true,

                    maintainAspectRatio: false,

                    animation: { duration: 550 },

                    interaction: {

                        mode: 'index',

                        intersect: false

                    },

                    plugins: {

                        legend: {

                            display:
                                !!extra.legend,

                            position: 'bottom',

                            labels: {

                                color: p.text,

                                boxWidth: 12,

                                boxHeight: 12,

                                font: { size: 11 }

                            }

                        },

                        tooltip: {

                            backgroundColor:
                                p.dark ? '#1e293b' : '#0f172a',

                            titleColor: '#fff',

                            bodyColor: '#e2e8f0',

                            padding: 10,

                            cornerRadius: 8

                        }

                    }

                };


                if (extra.noScales) {
                    return conf;
                }


                function tickConf(withPrecision) {

                    return {

                        color: p.muted,

                        font: { size: 10.5 },

                        precision:
                            withPrecision ? 0 : undefined,

                        autoSkip: true,

                        maxRotation: 0,

                        maxTicksLimit: 14

                    };

                }


                if (extra.horizontal) {

                    conf.indexAxis = 'y';

                    conf.scales = {

                        x: {
                            ticks: tickConf(true),
                            grid: { color: p.grid }
                        },

                        y: {
                            ticks: tickConf(false),
                            grid: { color: p.grid }
                        }

                    };

                    return conf;

                }


                if (extra.stacked) {

                    conf.scales = {

                        x: {
                            stacked: true,
                            ticks: tickConf(false),
                            grid: { color: p.grid }
                        },

                        y: {
                            stacked: true,
                            beginAtZero: true,
                            ticks: tickConf(true),
                            grid: { color: p.grid }
                        }

                    };

                    return conf;

                }


                conf.scales = {

                    x: {
                        ticks: tickConf(false),
                        grid: { color: p.grid }
                    },

                    y: {
                        beginAtZero: true,
                        ticks: tickConf(true),
                        grid: { color: p.grid }
                    }

                };

                return conf;

            }


            /* -------------------------------------------------
               RENDER SEMUA SECTION
            ------------------------------------------------- */

            function renderAll() {

                var d = state.payload;

                if (!d) {
                    return;
                }


                renderAlerts(d.alerts);

                renderKpis(d);

                renderWoTrend(d.trends.wo);

                renderDepartment(d.rankings.department);

                renderStatus(d);

                renderPriority(d);

                renderCompletion(d.completion);

                renderMachineSections(d);

                renderInventory(d);

                renderDelay(d.delay);

                renderInsights(d);


                var genEl =
                    document.getElementById('generatedAt');

                if (genEl) {

                    genEl.textContent =
                        'Data per '
                        + (d.meta.generatedAt || '-');

                }

            }


            /* -------------------------------------------------
               ALERTS
            ------------------------------------------------- */

            function renderAlerts(alerts) {

                var panel =
                    document.getElementById('alertPanel');

                if (!panel) {
                    return;
                }

                var icons = {
                    danger: '&#9888;',
                    warning: '&#9888;',
                    info: '&#8505;'
                };

                panel.innerHTML = (alerts || [])
                    .map(function (a) {

                        return '<div class="alert-item alert-'
                            + esc(a.level)
                            + '"><span>'
                            + (icons[a.level] || '')
                            + '</span><span>'
                            + esc(a.text)
                            + '</span></div>';

                    })
                    .join('');

            }


            /* -------------------------------------------------
               KPI
            ------------------------------------------------- */

            function deltaBadge(delta, badWhenUp) {

                if (
                    delta === null
                    || delta === undefined
                ) {

                    return '<span class="delta-badge delta-flat">'
                        + 'data awal'
                        + '</span>';

                }

                if (delta === 0) {

                    return '<span class="delta-badge delta-flat">'
                        + '&#8212; stabil'
                        + '</span>';

                }


                var up = delta > 0;

                var good =
                    badWhenUp ? !up : up;

                var cls = good
                    ? (up ? 'delta-up-good' : 'delta-down-good')
                    : (up ? 'delta-up-bad' : 'delta-down-bad');

                var arrow = up ? '&#9650;' : '&#9660;';

                return '<span class="delta-badge ' + cls + '">'
                    + arrow + ' '
                    + Math.abs(delta) + '%'
                    + '</span>';

            }


            function setText(id, text) {

                var el =
                    document.getElementById(id);

                if (el) {
                    el.innerHTML = text;
                }

            }


            function renderKpis(d) {

                var k = d.kpi;

                setText(
                    'kpiMasuk',
                    fmtNum(k.woMasuk.total)
                );

                setText(
                    'kpiMasukNote',
                    esc(d.meta.label) + ' '
                    + deltaBadge(k.woMasuk.delta, false)
                );

                setText(
                    'kpiSelesai',
                    fmtNum(k.woSelesai.total)
                );

                setText(
                    'kpiSelesaiNote',
                    'vs periode sebelumnya '
                    + deltaBadge(k.woSelesai.delta, false)
                );


                setText(
                    'kpiBelum',
                    fmtNum(
                        (d.statusCounts.open || 0)
                        + (d.statusCounts.progress || 0)
                        + (d.statusCounts.hold || 0)
                    )
                );

                setText('kpiOpen', fmtNum(d.statusCounts.open));

                setText(
                    'kpiProgress',
                    fmtNum(d.statusCounts.progress)
                );

                setText(
                    'kpiHold',
                    fmtNum(d.statusCounts.hold)
                );

                setText(
                    'kpiOverdue',
                    fmtNum(k.woOverdue.total)
                );

                setText(
                    'kpiEmergency',
                    fmtNum(k.emergency.total)
                );

                setText(
                    'kpiEmergencyNote',
                    'vs periode lalu '
                    + deltaBadge(k.emergency.delta, true)
                );

                setText(
                    'kpiUrgent',
                    fmtNum(k.urgent.total)
                );

                setText(
                    'kpiUrgentNote',
                    'vs periode lalu '
                    + deltaBadge(k.urgent.delta, true)
                );

                setText(
                    'kpiAvgCompletion',
                    fmtHours(k.avgCompletionHours)
                );

                setText(
                    'kpiDowntime',
                    fmtHours(k.downtimeHours)
                );

            }


            /* -------------------------------------------------
               WO TREND (LINE / AREA)
            ------------------------------------------------- */

            function renderWoTrend(t) {

                killChart('woTrend');

                var p = palette();

                var ctx =
                    document.getElementById('woTrendChart');

                if (!ctx) {
                    return;
                }

                state.charts.woTrend =
                    new Chart(ctx, {

                        type: 'line',

                        data: {

                            labels: t.labels,

                            datasets: [

                                {
                                    label: 'WO Masuk',
                                    data: t.masuk,
                                    borderColor: p.primary,
                                    backgroundColor:
                                        p.dark
                                            ? 'rgba(37,99,235,.20)'
                                            : 'rgba(37,99,235,.12)',
                                    fill: true,
                                    tension: .38,
                                    pointRadius: 3,
                                    pointBackgroundColor: p.primary
                                },

                                {
                                    label: 'WO Selesai',
                                    data: t.selesai,
                                    borderColor: p.green,
                                    backgroundColor:
                                        p.dark
                                            ? 'rgba(22,163,74,.18)'
                                            : 'rgba(22,163,74,.10)',
                                    fill: true,
                                    tension: .38,
                                    pointRadius: 3,
                                    pointBackgroundColor: p.green
                                }

                            ]

                        },

                        options: baseOptions(p, { legend: true })

                    });

            }


            /* -------------------------------------------------
               DEPARTMENT
            ------------------------------------------------- */

            function renderDepartment(ranking) {

                killChart('dept');

                var p = palette();


                var labels =
                    (ranking || []).map(function (x) {
                        return x.label;
                    });

                var values =
                    (ranking || []).map(function (x) {
                        return x.total;
                    });


                var ctx =
                    document.getElementById('deptChart');

                if (ctx) {

                    /* Rotasi warna - sama dengan chart "per tujuan" admin */

                    var tujuanCols = [
                        p.green,
                        p.primary,
                        p.amber,
                        p.violet,
                        p.cyan,
                        p.red,
                        p.pink,
                        p.slate
                    ];

                    state.charts.dept =
                        new Chart(ctx, {

                            type: 'bar',

                            data: {

                                labels: labels,

                                datasets: [{

                                    label:
                                        'Jumlah WO',

                                    data: values,

                                    backgroundColor:
                                        labels.map(function (_, i) {
                                            return tujuanCols[
                                                i % tujuanCols.length
                                            ];
                                        }),

                                    borderRadius: 6,

                                    maxBarThickness: 26

                                }]

                            },

                            options: baseOptions(
                                p,
                                { horizontal: true }
                            )

                        });

                }


                setText(
                    'deptRanking',
                    buildRanking(ranking, 'blue', true)
                );

            }


            /* -------------------------------------------------
               STATUS
            ------------------------------------------------- */

            function statusColor(group, p) {

                /* Keluarga warna doughnut status admin:
                   #2563eb  #0891b2  #7c3aed  #16a34a */

                var map = {
                    open: p.primary,
                    progress: p.cyan,
                    hold: p.violet,
                    selesai: p.green
                };

                return map[group] || p.slate;

            }

            function statusLabel(group) {

                var map = {
                    open: 'OPEN',
                    progress: 'IN PROSES',
                    hold: 'HOLD',
                    selesai: 'SELESAI'
                };

                return map[group] || group;

            }


            function renderStatus(d) {

                killChart('statusDonut');
                killChart('statusTrend');


                var p = palette();

                var counts = d.statusCounts;

                var groups = [
                    'open',
                    'progress',
                    'hold',
                    'selesai'
                ];


                var values =
                    groups.map(function (g) {
                        return counts[g] || 0;
                    });


                var ctx =
                    document.getElementById('statusDonut');

                if (ctx) {

                    var total =
                        values.reduce(function (a, b) {
                            return a + b;
                        }, 0);


                    state.charts.statusDonut =
                        new Chart(ctx, {

                            type: 'doughnut',

                            data: {

                                labels:
                                    groups.map(statusLabel),

                                datasets: [{

                                    data: values,

                                    backgroundColor:
                                        groups.map(function (g) {
                                            return statusColor(g, p);
                                        }),

                                    borderWidth: 0,

                                    hoverOffset: 6

                                }]

                            },

                            options: (function () {

                                var o = baseOptions(
                                    p,
                                    { noScales: true, legend: true }
                                );

                                o.cutout = '62%';

                                o.plugins.tooltip.callbacks = {

                                    label: function (item) {

                                        var pct =
                                            total > 0
                                                ? Math.round(
                                                    item.parsed
                                                    / total
                                                    * 100
                                                )
                                                : 0;

                                        return ' '
                                            + item.label
                                            + ': '
                                            + fmtNum(item.parsed)
                                            + ' ('
                                            + pct + '%)';

                                    }

                                };

                                return o;

                            })()

                        });

                }


                var series = d.trends.status.series;

                var tctx =
                    document.getElementById('statusTrendChart');

                if (tctx) {

                    state.charts.statusTrend =
                        new Chart(tctx, {

                            type: 'bar',

                            data: {

                                labels: d.trends.status.labels,

                                datasets:

                                    ['open', 'progress', 'hold', 'selesai']
                                        .map(function (g) {

                                            return {

                                                label:
                                                    statusLabel(g),

                                                data:
                                                    series[g],

                                                backgroundColor:
                                                    statusColor(g, p),

                                                borderRadius: 5,

                                                stack: 's',

                                                maxBarThickness: 16

                                            };

                                        })

                            },

                            options: baseOptions(
                                p,
                                { legend: true, stacked: true }
                            )

                        });

                }

            }


            /* -------------------------------------------------
               PRIORITAS
            ------------------------------------------------- */

            function renderPriority(d) {

                killChart('priorityDonut');
                killChart('priorityTrend');


                var p = palette();

                var counts = d.priorities.counts;

                var keys = [
                    'emergency',
                    'urgent',
                    'normal'
                ];

                /* Warna prioritas - sama dengan chart prioritas admin:
                   NORMAL = slate, URGENT = amber, EMERGENCY = red */

                var colors = {
                    emergency: p.red,
                    urgent: p.amber,
                    normal: p.slate
                };

                var values =
                    keys.map(function (k) {
                        return counts[k] || 0;
                    });


                var flagE =
                    document.getElementById('flagEmergency');

                var flagU =
                    document.getElementById('flagUrgent');

                if (flagE) {

                    flagE.style.display =
                        d.priorities.rising.emergency
                            ? 'inline'
                            : 'none';

                }

                if (flagU) {

                    flagU.style.display =
                        d.priorities.rising.urgent
                            ? 'inline'
                            : 'none';

                }


                var ctx =
                    document.getElementById('priorityDonut');

                if (ctx) {

                    state.charts.priorityDonut =
                        new Chart(ctx, {

                            type: 'bar',

                            data: {

                                labels:
                                    keys.map(function (k) {
                                        return k.toUpperCase();
                                    }),

                                datasets: [{

                                    label: 'WO',

                                    data: values,

                                    backgroundColor:
                                        keys.map(function (k) {
                                            return colors[k];
                                        }),

                                    borderRadius: 7,

                                    maxBarThickness: 52

                                }]

                            },

                            options: baseOptions(p)

                        });

                }


                var series = d.trends.priority.series;

                var tctx =
                    document.getElementById('priorityTrendChart');

                if (tctx) {

                    state.charts.priorityTrend =
                        new Chart(tctx, {

                            type: 'bar',

                            data: {

                                labels: d.trends.priority.labels,

                                datasets:
                                    keys.map(function (k) {

                                        return {

                                            label:
                                                k.toUpperCase(),

                                            data:
                                                series[k.toUpperCase()]
                                                || series[k],

                                            backgroundColor:
                                                colors[k],

                                            borderRadius: 5,

                                            maxBarThickness: 16

                                        };

                                    })

                            },

                            options: baseOptions(
                                p,
                                { legend: true }
                            )

                        });

                }

            }


            /* -------------------------------------------------
               PENYELESAIAN
            ------------------------------------------------- */

            function renderCompletion(c) {

                var rate =
                    c.rate || 0;

                setText(
                    'completionRateText',
                    rate + '% (' + fmtNum(c.selesai)
                    + ' dari ' + fmtNum(c.masuk) + ')'
                );

                var bar =
                    document.getElementById('completionRateBar');

                if (bar) {
                    bar.style.width = Math.min(rate, 100) + '%';
                }


                var backlog =
                    c.masuk > 0
                        ? Math.round(
                            ((c.open || 0)
                                + (c.progress || 0)
                                + (c.hold || 0))
                            / c.masuk * 100
                        )
                        : 0;

                setText(
                    'backlogRateText',
                    backlog + '% ('
                    + fmtNum(
                        (c.open || 0)
                        + (c.progress || 0)
                        + (c.hold || 0)
                    )
                    + ' WO aktif)'
                );

                var bbar =
                    document.getElementById('backlogRateBar');

                if (bbar) {
                    bbar.style.width = Math.min(backlog, 100) + '%';
                }


                setText(
                    'statResponse',
                    fmtHours(c.avgResponseHours)
                );

                setText(
                    'statWork',
                    fmtHours(c.avgWorkHours)
                );

                setText(
                    'statCompletion',
                    fmtHours(c.avgCompletionHours)
                );

            }


            /* -------------------------------------------------
               RANKING GENERIK
            ------------------------------------------------- */

            function buildRanking(items, colorClass, withPercent) {


                if (!items || !items.length) {

                    return '<div class="empty-state">'
                        + 'Belum ada data pada periode ini.'
                        + '</div>';

                }


                var max =
                    Math.max.apply(
                        null,
                        items.map(function (x) {
                            return Number(x.total) || 0;
                        })
                    );

                max = Math.max(max, 1);


                return '<div class="ranking">'
                    + items.map(function (x, i) {

                        var width =
                            Math.max(
                                2,
                                Math.round(
                                    (Number(x.total) || 0)
                                    / max * 100
                                )
                            );

                        var extra =
                            withPercent && x.percent !== undefined
                                ? ' <span class="pct">'
                                    + Number(x.percent)
                                        .toLocaleString('id-ID')
                                    + '%</span>'
                                : '';

                        return '<div class="ranking-item">'
                            + '<div class="ranking-no">'
                            + (i + 1)
                            + '</div>'
                            + '<div class="ranking-main">'
                            + '<div class="ranking-name">'
                            + esc(x.label)
                            + extra
                            + '</div>'
                            + '<div class="ranking-bar">'
                            + '<div class="ranking-fill'
                            + (colorClass
                                ? ' rf-' + colorClass
                                : '')
                            + '" style="width:' + width + '%">'
                            + '</div></div></div>'
                            + '<div class="ranking-value">'
                            + fmtNum(x.total)
                            + '</div></div>';

                    }).join('')
                    + '</div>';

            }


            function renderMachineSections(d) {


                setText(
                    'mesinRanking',
                    buildRanking(d.rankings.mesin, 'blue', true)
                );

                setText(
                    'areaRanking',
                    buildRanking(d.rankings.area, 'teal', true)
                );

                setText(
                    'kategoriRanking',
                    buildRanking(d.rankings.kategori, 'violet', true)
                );


                /* Trend kerusakan */

                killChart('machineTrend');

                var p = palette();

                var mctx =
                    document.getElementById('machineTrendChart');

                if (mctx) {

                    state.charts.machineTrend =
                        new Chart(mctx, {

                            type: 'line',

                            data: {

                                labels:
                                    d.trends.kerusakanMesin.labels,

                                datasets: [{

                                    label:
                                        'Kerusakan / WO Masuk',

                                    data:
                                        d.trends.kerusakanMesin.values,

                                    borderColor: p.red,

                                    backgroundColor:
                                        p.dark
                                            ? 'rgba(220,38,38,.18)'
                                            : 'rgba(220,38,38,.12)',

                                    fill: true,

                                    tension: .38,

                                    pointRadius: 3,

                                    pointBackgroundColor: p.red

                                }]

                            },

                            options: baseOptions(p)

                        });

                }


                /* Downtime ranking */

                setText(
                    'downtimeRanking',
                    buildRanking(
                        d.machines.downtime.byMesin,
                        'red',
                        false
                    )
                );

                var dtEl =
                    document.getElementById('downtimeTotalInline');

                if (dtEl) {

                    dtEl.textContent =
                        d.machines.downtime.totalHours !== null
                            ? ' - Total: '
                                + fmtHours(
                                    d.machines.downtime.totalHours
                                )
                            : '';

                }

            }


            /* -------------------------------------------------
               INVENTORY
            ------------------------------------------------- */

            function renderInventory(d) {

                var inv = d.inventory;

                var s = inv.stok;


                setText('stokTotal', fmtNum(s.total));
                setText('stokAman', fmtNum(s.aman));
                setText('stokMenipis', fmtNum(s.menipis));
                setText('stokHabis', fmtNum(s.habis));
                setText('stokRata', fmtQty(s.rataRata));


                setText(
                    'invMasukTotal',
                    fmtQty(inv.masukTotal)
                );

                setText(
                    'invKeluarTotal',
                    fmtQty(inv.keluarTotal)
                );


                var points = d.meta.labels.length || 1;

                setText(
                    'invMasukAvg',
                    fmtQty(inv.masukTotal / points)
                );

                setText(
                    'invKeluarAvg',
                    fmtQty(inv.keluarTotal / points)
                );


                /* Trend masuk vs keluar */

                killChart('inventoryTrend');

                var p = palette();

                var ictx =
                    document.getElementById('inventoryTrendChart');

                if (ictx) {

                    /* Bar masuk vs keluar - sama dengan chart
                       inventory admin (hijau = masuk, merah = keluar) */

                    state.charts.inventoryTrend =
                        new Chart(ictx, {

                            type: 'bar',

                            data: {

                                labels:
                                    d.trends.inventory.labels,

                                datasets: [

                                    {
                                        label: 'Barang Masuk',
                                        data: d.trends.inventory.masuk,
                                        backgroundColor: p.green,
                                        borderRadius: 6,
                                        maxBarThickness: 18
                                    },

                                    {
                                        label: 'Barang Keluar',
                                        data: d.trends.inventory.keluar,
                                        backgroundColor: p.red,
                                        borderRadius: 6,
                                        maxBarThickness: 18
                                    }

                                ]

                            },

                            options: baseOptions(
                                p,
                                { legend: true }
                            )

                        });

                }


                setText(
                    'topKeluarRanking',
                    buildRanking(inv.topKeluar, 'orange', false)
                );


                /* Barang kritis */

                var list =
                    document.getElementById('barangKritisList');

                if (!list) {
                    return;
                }


                if (!inv.kritis.length) {

                    list.innerHTML =
                        '<div class="empty-state">'
                        + 'Tidak ada barang kritis. Stok dalam kondisi aman.'
                        + '</div>';

                    return;

                }


                list.innerHTML =
                    inv.kritis.map(function (b) {

                        var cls =
                            b.stok <= 0
                                ? 'bs-habis'
                                : 'bs-menipis';

                        return '<div class="ranking-item">'
                            + '<div class="ranking-main">'
                            + '<div class="ranking-name">'
                            + esc(b.kode) + ' &mdash; '
                            + esc(b.label)
                            + '</div>'
                            + '<div class="ranking-bar">'
                            + '<div class="ranking-fill rf-red" style="width:'
                            + Math.min(
                                100,
                                b.minimum > 0
                                    ? Math.round(
                                        b.stok / b.minimum * 100
                                    )
                                    : 4
                            )
                            + '%"></div></div></div>'
                            + '<span class="badge-status ' + cls + '">'
                            + esc(b.kondisi)
                            + ' ' + b.stok + '/'
                            + b.minimum
                            + '</span></div>';

                    }).join('');

            }


            /* -------------------------------------------------
               DELAY
            ------------------------------------------------- */

            function renderDelay(delay) {

                setText(
                    'delayTerlambat',
                    fmtNum(delay.terlambat)
                );

                setText(
                    'delayBelum',
                    fmtNum(delay.belumSelesai)
                );


                setText(
                    'delayTerlama',
                    delay.terlama
                        ? delay.terlama.hours + ' jam'
                        : '-'
                );


                setText(
                    'delayTerlamaCard',
                    delay.terlama
                        ? '<div class="alert-item alert-warning">'
                            + '<span>&#9888;</span><span><b>'
                            + esc(delay.terlama.no_wo)
                            + '</b> - '
                            + esc(delay.terlama.job)
                            + ' (' 
                            + esc(delay.terlama.mesin)
                            + ' / '
                            + esc(delay.terlama.area)
                            + ') berjalan '
                            + esc(String(delay.terlama.hours))
                            + ' jam sejak '
                            + esc(delay.terlama.tanggal)
                            + '</span></div>'
                        : '<div class="empty-state">'
                            + 'Tidak ada WO yang menumpuk.'
                            + '</div>'
                );


                setText(
                    'delayByArea',
                    buildRanking(delay.byArea, 'blue', false)
                );

                setText(
                    'delayByMesin',
                    buildRanking(delay.byMesin, 'teal', false)
                );

                setText(
                    'delayByDepartment',
                    buildRanking(
                        delay.byDepartment,
                        'violet',
                        false
                    )
                );

            }


            /* -------------------------------------------------
               MANAGEMENT INSIGHT
            ------------------------------------------------- */

            function renderInsights(d) {

                var list =
                    document.getElementById('insightList');

                if (!list) {
                    return;
                }

                var items = [];


                /* Prioritas darurat */
                var em = Number((d.kpi.emergency || {}).total) || 0;
                var ur = Number((d.kpi.urgent || {}).total) || 0;

                if (em > 0) {

                    items.push({
                        c: 'ic-red',
                        i: '&#128680;',
                        t: '<b>' + fmtNum(em) + ' WO Emergency</b>'
                            + ' aktif periode ini'
                            + (Number(d.kpi.emergency.delta) > 0
                                ? ' (naik ' + d.kpi.emergency.delta + '%)'
                                : '')
                            + ' - prioritaskan penanganan.'
                    });

                } else {

                    items.push({
                        c: 'ic-green',
                        i: '&#9989;',
                        t: 'Tidak ada WO Emergency pada periode ini.'
                    });

                }


                /* Completion rate */
                var comp = d.completion || {};
                var rate = Number(comp.rate) || 0;

                items.push({
                    c: rate >= 70
                        ? 'ic-green'
                        : (rate >= 45 ? 'ic-blue' : 'ic-orange'),
                    i: '&#9889;',
                    t: 'Completion rate <b>' + rate + '%</b> - '
                        + fmtNum(comp.selesai)
                        + ' dari '
                        + fmtNum(comp.masuk)
                        + ' WO masuk berhasil diselesaikan.'
                });


                /* Department pengaju terbanyak */
                var dep = (d.rankings.department || [])[0];

                if (dep) {

                    items.push({
                        c: 'ic-blue',
                        i: '&#127970;',
                        t: 'Department pengaju terbanyak: <b>'
                            + esc(dep.label) + '</b> ('
                            + fmtNum(dep.total) + ' WO'
                            + (dep.percent !== undefined
                                ? ', ' + Number(dep.percent) + '% kontribusi'
                                : '')
                            + ').'
                    });

                }


                /* Mesin paling bermasalah */
                var mes = (d.rankings.mesin || [])[0];

                if (mes) {

                    items.push({
                        c: 'ic-violet',
                        i: '&#9881;',
                        t: 'Mesin paling bermasalah: <b>'
                            + esc(mes.label) + '</b> ('
                            + fmtNum(mes.total) + 'x kerusakan).'
                    });

                }


                /* Area paling bermasalah */
                var ar = (d.rankings.area || [])[0];

                if (ar) {

                    items.push({
                        c: 'ic-teal',
                        i: '&#128205;',
                        t: 'Area dengan kerusakan terbanyak: <b>'
                            + esc(ar.label) + '</b> ('
                            + fmtNum(ar.total) + ' WO).'
                    });

                }


                /* Downtime total */
                var dt = ((d.machines || {}).downtime || {});

                if (dt.totalHours !== null
                    && dt.totalHours !== undefined) {

                    items.push({
                        c: 'ic-red',
                        i: '&#8987;',
                        t: 'Estimasi total downtime <b>'
                            + fmtHours(dt.totalHours)
                            + '</b> pada periode ini.'
                    });

                }


                /* Kondisi stok sparepart */
                var stok = (d.inventory || {}).stok || {};
                var habis = Number(stok.habis) || 0;
                var menipis = Number(stok.menipis) || 0;

                if (habis > 0 || menipis > 0) {

                    items.push({
                        c: habis > 0 ? 'ic-red' : 'ic-orange',
                        i: '&#128230;',
                        t: 'Stok sparepart: <b>' + habis + ' habis</b>, '
                            + menipis + ' menipis - jadwalkan pengadaan.'
                    });

                } else {

                    items.push({
                        c: 'ic-green',
                        i: '&#128230;',
                        t: 'Stok sparepart dalam kondisi aman.'
                    });

                }


                /* Delay / SLA */
                var dl = d.delay || {};

                if (Number(dl.terlambat) > 0) {

                    items.push({
                        c: 'ic-red',
                        i: '&#9203;',
                        t: '<b>' + fmtNum(dl.terlambat)
                            + ' WO melewati SLA</b> prioritasnya'
                            + (dl.belumSelesai
                                ? ', ' + fmtNum(dl.belumSelesai)
                                    + ' WO masih berjalan.'
                                : '.')
                    });

                } else {

                    items.push({
                        c: 'ic-green',
                        i: '&#9203;',
                        t: 'Semua WO masih dalam batas SLA prioritas.'
                    });

                }


                if (!items.length) {

                    list.innerHTML =
                        '<div class="empty-state">'
                        + 'Belum ada data untuk dianalisis.'
                        + '</div>';

                    return;

                }


                list.innerHTML = items
                    .map(function (x) {

                        return '<div class="insight-item">'
                            + '<span class="ic ' + x.c + '">'
                            + x.i
                            + '</span><span>'
                            + x.t
                            + '</span></div>';

                    })
                    .join('');

            }


            /* -------------------------------------------------
               MODAL DETAIL
            ------------------------------------------------- */

            var overlay =
                document.getElementById('detailOverlay');

            var titleEl =
                document.getElementById('detailTitle');

            var subtitleEl =
                document.getElementById('detailSubtitle');

            var bodyEl =
                document.getElementById('detailBody');

            var closeEl =
                document.getElementById('detailClose');


            var currentMetric = null;


            function closeModal() {

                overlay.classList.remove('active');

                overlay.setAttribute('aria-hidden', 'true');

                currentMetric = null;

            }


            function statusBadgeClass(status) {

                var s =
                    String(status || '').toUpperCase();

                if (s === 'OPEN') return 'bs-open';

                if (s === 'PENDING' || s === 'HOLD') return 'bs-hold';

                if (s === 'CLOSE' || s.indexOf('DONE') !== -1
                    || s.indexOf('SELESAI') !== -1) return 'bs-selesai';

                if (s.indexOf('PROGRES') !== -1
                    || s.indexOf('GRESS') !== -1
                    || s.indexOf('PROSES') !== -1) return 'bs-progress';

                return '';

            }


            function renderDetail(payload) {

                titleEl.textContent =
                    payload.title || 'Detail';

                subtitleEl.textContent =
                    payload.subtitle || '';

                var columns =
                    payload.columns || {};

                var rows =
                    payload.rows || [];


                if (!rows.length) {

                    bodyEl.innerHTML =
                        '<div class="detail-empty">'
                        + 'Tidak ada data untuk ditampilkan.'
                        + '</div>';

                    return;

                }


                var colKeys =
                    Object.keys(columns);


                var html =
                    '<div class="detail-table-wrap">'
                    + '<table class="detail-table"><thead><tr>';


                colKeys.forEach(function (key) {

                    html += '<th>'
                        + esc(columns[key])
                        + '</th>';

                });

                html += '</tr></thead><tbody>';


                rows.forEach(function (row) {

                    html += '<tr>';

                    colKeys.forEach(function (key) {

                        var value = row[key];


                        if (key === 'status') {

                            html += '<td><span class="badge-status '
                                + statusBadgeClass(value)
                                + '">'
                                + esc(value === null
                                    || value === undefined
                                    ? '-'
                                    : value)
                                + '</span></td>';

                            return;

                        }

                        if (
                            key === 'hours'
                            || key === 'durasi'
                        ) {

                            html += '<td>'
                                + esc(
                                    value === null
                                    || value === undefined
                                        ? '-'
                                        : value
                                )
                                + '</td>';

                            return;

                        }

                        html += '<td>'
                            + esc(
                                (value === null
                                 || value === undefined)
                                    ? '-'
                                    : value
                            )
                            + '</td>';

                    });

                    html += '</tr>';

                });

                html += '</tbody></table></div>';


                var rowCount =
                    rows.length;

                var grandTotal =
                    payload.total != null
                        ? payload.total
                        : rowCount;


                html +=
                    '<div class="detail-total">'
                    + 'Total: <strong>'
                    + fmtNum(grandTotal)
                    + '</strong>'
                    + (rowCount >= 200
                        ? ' (menampilkan maksimal 200 baris)'
                        : '')
                    + '</div>';


                bodyEl.innerHTML = html;

            }


            function loadDetail(metric, button) {

                currentMetric = metric;

                bodyEl.innerHTML =
                    '<div class="detail-loading">'
                    + 'Memuat data...'
                    + '</div>';


                var params =
                    new URLSearchParams();

                params.set('metric', metric);


                if (state.payload && state.payload.window) {

                    params.set(
                        'start',
                        state.payload.window.start
                    );

                    params.set(
                        'end',
                        state.payload.window.end
                    );

                }


                fetch(
                    DETAIL_URL
                    + '?'
                    + params.toString(),

                    {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        credentials: 'same-origin'
                    }
                )

                    .then(function (response) {

                        return response.json()
                            .then(function (data) {

                                return {
                                    ok: response.ok,
                                    data: data
                                };

                            });

                    })

                    .then(function (result) {

                        if (!result.ok) {

                            bodyEl.innerHTML =
                                '<div class="detail-error">'
                                + esc(
                                    result.data.message
                                    || 'Gagal memuat data.'
                                )
                                + '</div>';

                            return;

                        }

                        renderDetail(result.data);

                        overlay.classList.add('active');

                        overlay.setAttribute('aria-hidden', 'false');

                    })

                    .catch(function () {

                        bodyEl.innerHTML =
                            '<div class="detail-error">'
                            + 'Terjadi kesalahan jaringan.'
                            + '</div>';

                    });

            }


            /* -------------------------------------------------
               EVENT LISTENERS
            ------------------------------------------------- */


            document.addEventListener(
                'click',
                function (event) {


                    /* Tombol periode */

                    var periodBtn =
                        event.target.closest('.period-btn');

                    if (periodBtn) {

                        document
                            .querySelectorAll('.period-btn')
                            .forEach(function (b) {
                                b.classList.remove('active');
                            });

                        periodBtn.classList.add('active');

                        loadDashboard(
                            periodBtn.getAttribute('data-period')
                        );

                        return;

                    }


                    /* Refresh */

                    if (
                        event.target.closest('#refreshBtn')
                    ) {

                        loadDashboard(null);

                        return;

                    }


                    /* Detail */

                    var button =
                        event.target.closest('.detail-btn');

                    if (button) {

                        var metric =
                            button.getAttribute('data-metric');

                        if (!metric) {
                            return;
                        }

                        loadDetail(metric, button);

                        return;

                    }


                    /* Dropdown akun */

                    if (
                        event.target.closest('#managerAccountButton')
                    ) {

                        var dd =
                            document.getElementById(
                                'managerAccountDropdown'
                            );

                        if (dd) {
                            dd.classList.toggle('open');
                        }

                        return;

                    }


                    /* Tutup dropdown bila klik di luar */

                    var dd2 =
                        document.getElementById(
                            'managerAccountDropdown'
                        );

                    if (
                        dd2
                        && dd2.classList.contains('open')
                        && !event.target.closest('.manager-account')
                    ) {

                        dd2.classList.remove('open');

                    }


                    /* Tutup modal bila klik overlay */

                    if (event.target === overlay) {

                        closeModal();

                    }

                }
            );


            var themeToggle =
                document.getElementById('themeToggleMgr');

            if (themeToggle) {

                themeToggle.addEventListener(
                    'click',
                    function () {

                        var next =
                            theme() === 'dark'
                                ? 'light'
                                : 'dark';

                        document.documentElement.setAttribute(
                            'data-theme',
                            next
                        );

                        try {
                            localStorage.setItem('mgr-theme', next);
                        } catch (e) {}

                        renderAll();

                        document.dispatchEvent(
                            new CustomEvent('pachira:theme')
                        );

                    }
                );

            }


            var refreshBtn =
                document.getElementById('refreshBtn');

            if (refreshBtn) {

                refreshBtn.addEventListener(
                    'click',
                    function () {
                        loadDashboard(state.period);
                    }
                );

            }


            /* Re-render chart saat tema berubah
               (konvensi sama dengan dashboard admin) */

            document.addEventListener(
                'pachira:theme',
                renderAll
            );


            document.addEventListener(
                'keydown',
                function (event) {

                    if (
                        event.key === 'Escape'
                        && overlay.classList.contains('active')
                    ) {

                        closeModal();

                    }

                }
            );


            if (closeEl) {

                closeEl.addEventListener('click', closeModal);

            }


            /* -------------------------------------------------
               AI ASSISTANT
            ------------------------------------------------- */

            var aiPanel =
                document.getElementById('aiAssistantPanel');

            var aiBtn =
                document.getElementById('aiAssistantButton');

            var aiClose =
                document.getElementById('aiClose');

            var aiBody =
                document.getElementById('aiBody');

            var aiQuestion =
                document.getElementById('aiQuestion');

            var aiSend =
                document.getElementById('aiSend');


            if (aiBtn && aiPanel) {

                aiBtn.addEventListener('click', function () {

                    aiPanel.classList.toggle('active');

                    if (
                        aiPanel.classList.contains('active')
                        && aiQuestion
                    ) {

                        setTimeout(function () {
                            aiQuestion.focus();
                        }, 150);

                    }

                });

            }

            if (aiClose && aiPanel) {

                aiClose.addEventListener('click', function () {

                    aiPanel.classList.remove('active');

                });

            }


            function addAiMessage(text, cls) {

                if (!aiBody) return;

                var div =
                    document.createElement('div');

                div.className = 'ai-msg ' + cls;

                div.textContent = text;

                aiBody.appendChild(div);

                aiBody.scrollTop =
                    aiBody.scrollHeight;

            }


            function sendAiQuestion() {

                if (!aiQuestion) return;

                var q =
                    aiQuestion.value.trim();

                if (!q) return;


                addAiMessage(q, 'user');

                aiQuestion.value = '';


                if (aiSend) {
                    aiSend.disabled = true;
                }


                var typing =
                    document.createElement('div');

                typing.className =
                    'ai-msg bot';

                typing.textContent = 'Mengetik...';

                aiBody.appendChild(typing);

                aiBody.scrollTop =
                    aiBody.scrollHeight;


                fetch(
                    "{{ route('ai-assistant.ask') }}",
                    {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN':
                                document
                                    .querySelector('meta[name="csrf-token"]')
                                    .getAttribute('content'),
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({
                            question: q
                        })
                    }
                )

                    .then(function (r) {
                        return r.json();
                    })

                    .then(function (data) {

                        typing.remove();

                        if (data.success && data.answer) {

                            addAiMessage(data.answer, 'bot');

                        } else {

                            addAiMessage(
                                data.answer
                                || 'Maaf, AI Assistant tidak dapat menjawab.',
                                'error'
                            );

                        }

                    })

                    .catch(function () {

                        typing.remove();

                        addAiMessage(
                            'Maaf, terjadi masalah koneksi ke AI Assistant.',
                            'error'
                        );

                    })

                    .finally(function () {

                        if (aiSend) {
                            aiSend.disabled = false;
                        }

                    });

            }


            if (aiSend) {

                aiSend.addEventListener(
                    'click',
                    sendAiQuestion
                );

            }

            if (aiQuestion) {

                aiQuestion.addEventListener(
                    'keydown',
                    function (event) {

                        if (
                            event.key === 'Enter'
                            && !event.shiftKey
                        ) {

                            event.preventDefault();

                            if (aiSend && !aiSend.disabled) {

                                sendAiQuestion();

                            }

                        }

                    }
                );

            }


            /* -------------------------------------------------
               MULAI
            ------------------------------------------------- */

            loadDashboard('month');

        }
    )();
    </script>


</body>

</html>
