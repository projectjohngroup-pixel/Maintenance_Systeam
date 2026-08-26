<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script>
        (function () {
            try {
                var t = localStorage.getItem('pachira-theme');
                if (t !== 'dark' && t !== 'light') { t = 'light'; }
                document.documentElement.setAttribute('data-theme', t);
            } catch (e) {
                document.documentElement.setAttribute('data-theme', 'light');
            }
        })();
    </script>

    <title>PACHIRA DISTRINUSA - PACHIRA MAINTENANCE SYSTEM</title>

    <style>
        :root {
            --primary:#2563eb;
            --primary-dark:#1d4ed8;
            --sidebar:#0b1220;
            --sidebar-2:#111827;
            --background:#f4f7fb;
            --surface:#ffffff;
            --surface-soft:#f8fafc;
            --text:#0f172a;
            --text-2:#334155;
            --muted:#64748b;
            --border:#e2e8f0;
            --border-dark:#cbd5e1;
            --success:#16a34a;
            --warning:#d97706;
            --danger:#dc2626;
            --shadow-sm:0 2px 8px rgba(15,23,42,.05);
            --shadow-md:0 8px 25px rgba(15,23,42,.08);
        }

        [data-theme="dark"] {
            --sidebar:#0d1526;
            --sidebar-2:#131e36;
            --background:#0b1120;
            --surface:#111a2e;
            --surface-soft:#16213a;
            --text:#e2e8f0;
            --text-2:#cbd5e1;
            --muted:#94a3b8;
            --border:#24304d;
            --border-dark:#33415580;
            --shadow-sm:0 2px 8px rgba(0,0,0,.35);
            --shadow-md:0 8px 25px rgba(0,0,0,.45);
        }

        * { margin:0; padding:0; box-sizing:border-box; }

        body {
            font-family:'Segoe UI', system-ui, -apple-system, Arial, sans-serif;
            background:var(--background);
            color:var(--text);
            font-size:14px;
            transition:background .25s ease, color .25s ease;
        }

        .app { display:flex; min-height:100vh; }

        /* ============ MAIN ============ */
        .main { flex:1; margin-left:0; min-width:0; display:flex; flex-direction:column; }

        .topbar {
            position:sticky; top:0; z-index:50;
            display:flex; align-items:center; justify-content:space-between; gap:14px;
            padding:12px 26px;
            background:var(--surface);
            border-bottom:1px solid var(--border);
        }

        /* Kiri: garis-3 + judul dalam satu grup rapih */
        .topbar-left {
            display:flex; align-items:center; gap:14px;
            min-width:0; flex:1 1 auto;
        }
        .topbar-title-wrap { min-width:0; }
        .topbar-title-wrap .page-title,
        .topbar-title-wrap .page-subtitle {
            overflow:hidden; text-overflow:ellipsis; white-space:nowrap;
        }

        .page-title { font-size:17px; font-weight:700; }
        .page-subtitle { font-size:12px; color:var(--muted); margin-top:2px; }

        .topbar-right {
            display:flex; align-items:center; gap:10px;
            flex-shrink:0; margin-left:auto;
        }

        .theme-toggle {
            display:inline-flex; align-items:center; gap:6px;
            height:38px; padding:0 13px; border-radius:10px;
            border:1px solid var(--border); background:var(--surface);
            color:var(--text); font-size:12.5px; font-weight:600; cursor:pointer;
        }
        .theme-toggle:hover { border-color:var(--primary); color:var(--primary); }

        .account-wrap { position:relative; }
        .account-btn {
            display:flex; align-items:center; gap:9px;
            height:40px; padding:0 12px; border-radius:10px;
            border:1px solid var(--border); background:var(--surface-soft);
            color:var(--text); cursor:pointer; font-size:13px;
        }
        .avatar {
            width:30px; height:30px; border-radius:50%;
            background:var(--primary); color:#fff;
            display:flex; align-items:center; justify-content:center;
            font-size:13px; font-weight:700; overflow:hidden; flex-shrink:0;
        }
        .avatar img { width:100%; height:100%; object-fit:cover; }

        .dropdown {
            position:absolute; right:0; top:calc(100% + 8px);
            min-width:190px; background:var(--surface);
            border:1px solid var(--border); border-radius:12px;
            box-shadow:var(--shadow-md); padding:6px; display:none;
        }
        .account-wrap.open .dropdown { display:block; }
        .dropdown a, .dropdown button {
            display:block; width:100%; text-align:left;
            padding:9px 12px; border:none; background:none;
            border-radius:8px; font-size:13px; color:var(--text);
            text-decoration:none; cursor:pointer;
        }
        .dropdown a:hover, .dropdown button:hover { background:var(--surface-soft); }

        .content { padding:24px 26px 60px; }

        /* ============ COMMAND CENTER HEADER ============ */
        .cc-header {
            display:flex; justify-content:space-between; align-items:flex-end;
            gap:18px; flex-wrap:wrap;
            background:
                linear-gradient(135deg, rgba(37,99,235,.10), transparent 46%),
                var(--surface);
            border:1px solid var(--border);
            border-radius:18px;
            padding:22px 24px;
            margin-bottom:14px;
            box-shadow:var(--shadow-sm);
        }
        .cc-kicker {
            font-size:10px; font-weight:800; letter-spacing:.28em;
            color:var(--primary); text-transform:uppercase;
        }
        .cc-title { font-size:23px; font-weight:800; letter-spacing:-.4px; margin-top:5px; }
        .cc-sub { font-size:12.5px; color:var(--muted); margin-top:4px; }

        .cc-right { display:flex; gap:10px; flex-wrap:wrap; }
        .cc-select { display:flex; flex-direction:column; gap:5px; }
        .cc-select label {
            font-size:9.5px; font-weight:800; letter-spacing:1.2px;
            color:var(--muted); text-transform:uppercase;
        }
        .cc-select select {
            height:38px; min-width:175px; padding:0 12px;
            border-radius:10px; border:1px solid var(--border);
            background:var(--surface-soft); color:var(--text);
            font-size:13px; font-weight:600;
        }

        /* ============ FILTER BAR ============ */
        .filter-card {
            background:var(--surface); border:1px solid var(--border);
            border-radius:14px; padding:14px 18px; margin-bottom:20px;
            box-shadow:var(--shadow-sm);
        }
        .filter-row { display:flex; flex-wrap:wrap; gap:10px; align-items:end; }
        .filter-field { display:flex; flex-direction:column; gap:5px; }
        .filter-field label { font-size:10.5px; font-weight:700; letter-spacing:1px; color:var(--muted); text-transform:uppercase; }
        .filter-field select, .filter-field input {
            height:38px; min-width:130px; padding:0 10px;
            border-radius:9px; border:1px solid var(--border);
            background:var(--surface-soft); color:var(--text); font-size:13px;
        }
        .btn-primary {
            height:38px; padding:0 20px; border-radius:9px; border:none;
            background:var(--primary); color:#fff; font-size:13px; font-weight:600; cursor:pointer;
        }
        .btn-primary:hover { background:var(--primary-dark); }
        .btn-ghost {
            height:38px; padding:0 16px; border-radius:9px;
            border:1px solid var(--border); background:transparent;
            color:var(--muted); font-size:13px; font-weight:600; cursor:pointer;
            text-decoration:none; display:inline-flex; align-items:center;
        }

        /* ============ SECTION TITLE ============ */
        .section-title {
            font-size:11px; font-weight:800; letter-spacing:1.8px;
            color:var(--muted); text-transform:uppercase; margin:24px 0 12px;
        }

        /* ============ HERO KPI (4 UTAMA) ============ */
        .hero-grid {
            display:grid; grid-template-columns:repeat(4,minmax(0,1fr));
            gap:14px; margin-bottom:14px;
        }
        .kpi-hero {
            position:relative; overflow:hidden;
            background:
                linear-gradient(160deg, var(--hc-soft, rgba(37,99,235,.08)), transparent 55%),
                var(--surface);
            border:1px solid var(--border);
            border-radius:16px;
            padding:18px 18px 16px;
            box-shadow:var(--shadow-sm);
        }
        .kpi-hero::before {
            content:''; position:absolute; left:0; top:0; bottom:0;
            width:4px; background:var(--hc, var(--primary));
        }
        .kh-top { display:flex; align-items:center; gap:9px; }
        .kh-icon {
            width:34px; height:34px; border-radius:10px; flex-shrink:0;
            display:flex; align-items:center; justify-content:center;
            background:var(--hc-soft, rgba(37,99,235,.08)); color:var(--hc, var(--primary));
        }
        .kh-icon svg {
            width:17px; height:17px;
            stroke:currentColor; fill:none;
            stroke-width:2; stroke-linecap:round; stroke-linejoin:round;
        }
        .kh-label {
            font-size:10px; font-weight:800; letter-spacing:1.4px;
            text-transform:uppercase; color:var(--muted); line-height:1.35;
        }
        .kh-value { font-size:32px; font-weight:800; letter-spacing:-1px; margin-top:10px; line-height:1; }
        .kh-foot { font-size:11.5px; color:var(--muted); margin-top:7px; }
        .kh-bar { height:5px; border-radius:99px; background:var(--border); margin-top:12px; overflow:hidden; }
        .kh-bar i { display:block; height:100%; border-radius:99px; background:var(--hc, var(--primary)); }

        /* ============ CHIP RINGKASAN ============ */
        .chip-row { display:flex; flex-wrap:wrap; gap:10px; }
        .chip {
            display:inline-flex; align-items:center; gap:8px;
            background:var(--surface); border:1px solid var(--border);
            border-radius:999px; padding:7px 14px; box-shadow:var(--shadow-sm);
        }
        .chip-label { font-size:9.5px; font-weight:800; letter-spacing:1.1px; text-transform:uppercase; color:var(--muted); }
        .chip-value { font-size:13px; font-weight:800; }

        /* ============ CHART GRID 2 KOLOM ============ */
        .grid-2 {
            display:grid; grid-template-columns:repeat(auto-fit,minmax(340px,1fr));
            gap:14px; margin-bottom:14px;
        }
        .chart-card {
            background:var(--surface); border:1px solid var(--border);
            border-radius:14px; padding:18px; box-shadow:var(--shadow-sm);
            min-width:0;
        }
        .chart-card h3 { font-size:13.5px; font-weight:700; }
        .chart-card p { font-size:11.5px; color:var(--muted); margin-top:3px; }
        .chart-box { position:relative; height:280px; margin-top:14px; }
        .chart-box.sm { height:215px; }
        .empty-note { color:var(--muted); font-size:12px; padding:30px 0; text-align:center; }

        /* ============ MINI STATS STATUS ============ */
        .mini-stats {
            display:grid; grid-template-columns:repeat(auto-fit,minmax(120px,1fr));
            gap:8px; margin-top:12px;
        }
        .mini-stat {
            display:flex; align-items:center; gap:7px;
            padding:7px 10px;
            background:var(--surface-soft); border:1px solid var(--border);
            border-radius:10px; min-width:0;
        }
        .ms-dot { width:8px; height:8px; border-radius:50%; flex-shrink:0; }
        .mini-stat span {
            font-size:9.8px; font-weight:700; letter-spacing:.4px; color:var(--muted);
            overflow:hidden; text-overflow:ellipsis; white-space:nowrap;
        }
        .mini-stat strong { margin-left:auto; font-size:12px; white-space:nowrap; }

        /* ============ MACHINE HEALTH PANEL ============ */
        .health-tiles {
            display:grid; grid-template-columns:repeat(2,minmax(0,1fr));
            gap:10px; margin-top:14px;
        }
        .htile {
            background:var(--surface-soft); border:1px solid var(--border);
            border-radius:12px; padding:12px 14px;
        }
        .htile span {
            display:block; font-size:9.5px; font-weight:800;
            letter-spacing:1.1px; text-transform:uppercase; color:var(--muted);
        }
        .htile strong { display:block; font-size:21px; font-weight:800; margin-top:5px; }
        .htile.ok strong { color:var(--success); }
        .htile.off strong { color:var(--muted); }
        .htile.kw strong { color:var(--primary); font-size:17px; }

        .avail { margin-top:14px; }
        .avail-head {
            display:flex; justify-content:space-between; align-items:center;
            font-size:11px; font-weight:700; color:var(--muted); margin-bottom:6px;
        }
        .avail-head strong { color:var(--success); font-size:12.5px; }
        .avail-bar { height:7px; border-radius:99px; background:var(--border); overflow:hidden; }
        .avail-bar i {
            display:block; height:100%; border-radius:99px;
            background:linear-gradient(90deg,#15803d,#4ade80);
        }

        .kw-scroll {
            margin-top:14px; max-height:198px; overflow-y:auto;
            display:flex; flex-direction:column; gap:8px; padding-right:3px;
        }
        .line-card {
            background:var(--surface-soft); border:1px solid var(--border);
            border-radius:11px; padding:10px 13px;
            display:flex; justify-content:space-between; align-items:center; gap:10px;
        }
        .line-name { font-size:12px; font-weight:700; }
        .line-sub { font-size:10.5px; color:var(--muted); margin-top:2px; }
        .line-kw { font-size:14px; font-weight:800; color:var(--primary); white-space:nowrap; }

        /* ============ TABLE ============ */
        .table-card {
            background:var(--surface); border:1px solid var(--border);
            border-radius:14px; box-shadow:var(--shadow-sm); overflow:hidden;
        }
        .table-scroll { overflow-x:auto; }
        table.monitoring { width:100%; border-collapse:collapse; min-width:780px; }
        .monitoring th {
            text-align:left; padding:12px 14px;
            font-size:10.5px; font-weight:800; letter-spacing:1.2px; text-transform:uppercase;
            color:var(--muted); background:var(--surface-soft);
            border-bottom:1px solid var(--border); white-space:nowrap;
        }
        .monitoring td {
            padding:12px 14px; font-size:13px; color:var(--text-2,var(--text));
            border-bottom:1px solid var(--border); white-space:nowrap;
        }
        .monitoring tr:last-child td { border-bottom:none; }
        .monitoring tbody tr:nth-child(even) td { background:var(--surface-soft); }
        .monitoring tbody tr:hover td { background:rgba(37,99,235,.07); }
        [data-theme="dark"] .monitoring tbody tr:nth-child(even) td { background:#15203a; }
        [data-theme="dark"] .monitoring tbody tr:hover td { background:rgba(96,165,250,.10); }
        [data-theme="dark"] .monitoring th { color:#b6c4dd; }
        [data-theme="dark"] .monitoring td { color:#dbe4f2; }

        .badge {
            display:inline-flex; align-items:center; gap:5px;
            padding:3px 10px; border-radius:999px; font-size:11px; font-weight:700;
        }
        .badge.on { background:rgba(22,163,74,.12); color:#16a34a; }
        .badge.off { background:rgba(100,116,139,.14); color:#64748b; }
        .dot { width:7px; height:7px; border-radius:50%; background:currentColor; }
        [data-theme="dark"] .badge.on { background:rgba(74,222,128,.16); color:#4ade80; }
        [data-theme="dark"] .badge.off { background:rgba(148,163,184,.18); color:#a8b6cd; }

        /* ============ KPI KECIL (SEKSI DETAIL) ============ */
        .kpi-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(150px,1fr)); gap:12px; }
        .kpi-grid.wide { grid-template-columns:repeat(auto-fit,minmax(165px,1fr)); }
        .kpi-card {
            background:var(--surface); border:1px solid var(--border);
            border-radius:14px; padding:14px 15px; box-shadow:var(--shadow-sm);
            position:relative; overflow:hidden;
        }
        .kpi-card::before {
            content:''; position:absolute; left:0; top:0; bottom:0;
            width:3px; background:var(--kc, var(--primary));
        }
        .kpi-label { font-size:10.5px; font-weight:700; letter-spacing:1px; text-transform:uppercase; color:var(--muted); }
        .kpi-value { font-size:24px; font-weight:800; margin-top:6px; letter-spacing:-.5px; }
        .kpi-foot { font-size:11px; color:var(--muted); margin-top:4px; }

        /* ============ DETAILS / SEKSI LIPAT ============ */
        .panel-details {
            background:var(--surface); border:1px solid var(--border);
            border-radius:14px; box-shadow:var(--shadow-sm);
            margin-bottom:14px; overflow:hidden;
        }
        .panel-details summary {
            list-style:none; cursor:pointer; user-select:none;
            display:flex; align-items:center; justify-content:space-between; gap:10px;
            padding:15px 18px;
            font-size:11px; font-weight:800; letter-spacing:1.6px;
            text-transform:uppercase; color:var(--muted);
        }
        .panel-details summary::-webkit-details-marker { display:none; }
        .panel-details summary::after {
            content:''; width:8px; height:8px; flex-shrink:0;
            border-right:2px solid currentColor; border-bottom:2px solid currentColor;
            transform:rotate(45deg); transition:transform .2s ease;
        }
        .panel-details[open] summary::after { transform:rotate(225deg); }
        .panel-details summary:hover { color:var(--primary); }
        .panel-inner { padding:0 18px 18px; }
        .chart-duo {
            display:grid; grid-template-columns:repeat(auto-fit,minmax(300px,1fr));
            gap:14px;
        }

        /* ============ MENU CEPAT ============ */
        .quick-grid {
            display:grid;
            grid-template-columns:repeat(auto-fit,minmax(150px,1fr));
            gap:12px;
        }
        .quick-card {
            display:flex; flex-direction:column; align-items:center; text-align:center; gap:10px;
            padding:18px 12px 15px;
            background:var(--surface); border:1px solid var(--border);
            border-radius:14px; text-decoration:none;
            color:var(--text); box-shadow:var(--shadow-sm);
            transition:transform .16s ease, border-color .16s ease, box-shadow .16s ease;
        }
        .quick-card:hover {
            transform:translateY(-2px);
            border-color:var(--primary);
            box-shadow:0 8px 20px rgba(37,99,235,.12);
        }
        .quick-icon {
            width:44px; height:44px; border-radius:12px; flex-shrink:0;
            display:flex; align-items:center; justify-content:center;
            background:rgba(37,99,235,.10); color:var(--primary);
            transition:background .16s ease, color .16s ease;
        }
        .quick-card:hover .quick-icon { background:var(--primary); color:#fff; }
        .quick-icon svg {
            width:21px; height:21px;
            stroke:currentColor; fill:none;
            stroke-width:1.9; stroke-linecap:round;
            stroke-linejoin:round;
        }
        .quick-text strong { display:block; font-size:12.5px; font-weight:700; }
        .quick-text span { display:block; font-size:10.5px; color:var(--muted); margin-top:2px; }

        /* ============ TOPBAR MENU (GARIS 3) ============ */
        .burger-btn {
            width:48px; height:46px; padding:0;
            border:1px solid var(--border); border-radius:12px;
            background:var(--surface); cursor:pointer;
            display:inline-flex; align-items:center; justify-content:center;
            flex-shrink:0;
            transition:border-color .15s ease, background .15s ease, box-shadow .15s ease, transform .15s ease;
        }
        .burger-btn:hover {
            border-color:var(--primary); background:rgba(37,99,235,.06);
            box-shadow:0 4px 14px rgba(37,99,235,.12);
        }
        .burger-btn:active { transform:scale(.95); }
        .burger-lines { display:flex; flex-direction:column; gap:5.5px; }
        .burger-lines i {
            display:block; width:24px; height:3px; border-radius:99px;
            background:var(--text-2,var(--text));
            transition:background .15s ease;
        }
        .burger-btn:hover .burger-lines i { background:var(--primary); }

        /* ============ LAUNCHER SEMUA MENU ============ */
        .launcher {
            position:fixed; inset:0; z-index:80; display:none;
            background:rgba(2,6,23,.66); backdrop-filter:blur(5px);
            padding:30px 18px 50px; overflow-y:auto;
        }
        .launcher.open { display:block; }
        .launcher-panel {
            max-width:1060px; margin:0 auto;
            background:var(--surface);
            border:1px solid var(--border);
            border-radius:20px;
            box-shadow:var(--shadow-md);
            padding:22px 22px 26px;
        }
        @keyframes launcher-in {
            from { opacity:0; transform:translateY(12px) scale(.985); }
            to   { opacity:1; transform:none; }
        }
        .launcher.open .launcher-panel {
            animation:launcher-in .28s cubic-bezier(.2,.7,.3,1) both;
        }
        .launcher-head {
            display:flex; justify-content:space-between; align-items:center;
            gap:12px;
            margin-bottom:18px;
            padding-bottom:14px;
            border-bottom:1px solid var(--border);
        }
        .launcher-title { color:var(--text); font-size:17px; font-weight:800; letter-spacing:.3px; }
        .launcher-subtitle {
            margin-top:2px; color:var(--muted);
            font-size:10.5px; font-weight:700;
            letter-spacing:.22em; text-transform:uppercase;
        }
        .launcher-close {
            width:42px; height:42px; border-radius:12px;
            border:1px solid var(--border); background:transparent; color:var(--muted);
            font-size:17px; cursor:pointer; flex-shrink:0;
            transition:background .15s ease, border-color .15s ease, color .15s ease;
        }
        .launcher-close:hover { background:rgba(239,68,68,.08); border-color:#fca5a5; color:#dc2626; }

        /* =====================================================
           MENU UTAMA - GRID 3 KOLOM: KIRI / TENGAH / KANAN
        ====================================================== */

        .menu-grid {
            display:grid;
            grid-template-columns:repeat(3, minmax(0,1fr));
            gap:14px;
            align-items:start;
        }

        .menu-col {
            display:flex;
            flex-direction:column;
            gap:14px;
            min-width:0;
        }

        .mgroup { min-width:0; }

        /* ==== CARD MENU UTAMA ==== */
        .mcard {
            width:100%;
            position:relative;
            display:flex;
            flex-direction:column;
            align-items:center;
            text-align:center;
            gap:4px;
            padding:20px 14px 16px;
            background:
                linear-gradient(180deg, rgba(37,99,235,.05), transparent 55%),
                var(--surface-soft);
            border:1px solid var(--border);
            border-radius:16px;
            color:var(--text);
            cursor:pointer;
            font-family:inherit;
            transition:
                transform .16s ease,
                border-color .16s ease,
                box-shadow .16s ease,
                background .16s ease;
        }
        a.mcard { text-decoration:none; }
        .mcard:hover {
            transform:translateY(-2px);
            border-color:var(--primary);
            box-shadow:0 10px 26px rgba(37,99,235,.14);
        }
        .mcard:focus-visible {
            outline:2px solid var(--primary);
            outline-offset:2px;
        }
        .mcard-icon {
            width:52px; height:52px;
            display:flex; align-items:center; justify-content:center;
            border-radius:15px;
            background:rgba(37,99,235,.10);
            color:var(--primary);
            margin-bottom:6px;
        }
        .mcard-icon svg {
            width:25px; height:25px;
            stroke:currentColor;
            fill:none;
            stroke-width:1.9;
            stroke-linecap:round;
            stroke-linejoin:round;
        }
        .mcard-name {
            font-size:14.5px;
            font-weight:800;
            letter-spacing:.02em;
        }
        .mcard-desc {
            font-size:11.5px;
            color:var(--muted);
        }
        .mcard-chevron {
            position:absolute;
            right:13px; top:13px;
            width:8px; height:8px;
            border-right:2px solid var(--muted);
            border-bottom:2px solid var(--muted);
            transform:rotate(45deg);
            transition:transform .22s ease, border-color .22s ease;
        }

        /* ==== STATUS CARD TERBUKA ==== */
        .mgroup.open > .mcard {
            border-color:var(--primary);
            background:
                linear-gradient(180deg, rgba(37,99,235,.08), transparent 60%),
                var(--surface-soft);
            box-shadow:0 10px 26px rgba(37,99,235,.12);
        }
        .mgroup.open > .mcard .mcard-chevron {
            transform:rotate(225deg);
            border-color:var(--primary);
        }

        /* ==== SUBMENU (hanya muncul saat card diklik) ==== */
        .msub {
            margin-top:8px;
            overflow:hidden;
            max-height:0;
            opacity:0;
            transform:translateY(-4px);
            transition:max-height .3s ease, opacity .2s ease, transform .2s ease;
        }
        .mgroup.open .msub {
            max-height:340px;
            opacity:1;
            transform:none;
        }
        .msub-inner {
            background:var(--surface);
            border:1px solid var(--border);
            border-radius:14px;
            overflow:hidden;
            box-shadow:var(--shadow-sm);
        }
        .msub a {
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:10px;
            padding:12px 14px;
            font-size:13px;
            font-weight:600;
            color:var(--text-2,var(--text));
            text-decoration:none;
            transition:
                background .14s ease,
                color .14s ease,
                padding-left .14s ease;
        }
        .msub a + a {
            border-top:1px solid var(--border);
        }
        .msub a::after {
            content:"\203A";
            font-size:16px;
            line-height:1;
            color:var(--muted);
            opacity:.6;
            transition:color .14s ease, opacity .14s ease, transform .14s ease;
        }
        .msub a:hover {
            background:rgba(37,99,235,.06);
            color:var(--primary-dark);
            padding-left:18px;
        }
        .msub a:hover::after {
            color:var(--primary);
            opacity:1;
            transform:translateX(2px);
        }

        /* ==== RESPONSIVE MENU UTAMA: 3 -> 2 -> 1 KOLOM ==== */
        @media (max-width:900px) {
            .menu-grid { grid-template-columns:repeat(2, minmax(0,1fr)); }
        }
        @media (max-width:600px) {
            .launcher { padding:18px 10px 40px; }
            .launcher-panel { padding:16px 14px 20px; border-radius:16px; }
            .menu-grid { grid-template-columns:1fr; gap:12px; }
            .mcard { padding:16px 12px 13px; }
            .mcard-icon { width:46px; height:46px; border-radius:13px; }
            .mcard-icon svg { width:22px; height:22px; }
            .mgroup.open .msub { max-height:420px; }
        }

        /* ============ DARK MODE TAMBAHAN ============ */
        [data-theme="dark"] .kpi-foot,
        [data-theme="dark"] .chart-card p,
        [data-theme="dark"] .line-sub,
        [data-theme="dark"] .kh-foot,
        [data-theme="dark"] .cc-sub { color:#9fb0cc; }
        [data-theme="dark"] select,
        [data-theme="dark"] input,
        [data-theme="dark"] textarea { color-scheme:dark; }
        [data-theme="dark"] .avail-bar { background:#1d2946; }
        [data-theme="dark"] .avail-bar i { background:linear-gradient(90deg,#16a34a,#4ade80); }

        /* ============ RESPONSIVE UMUM ============ */
        @media (max-width:1100px) {
            .hero-grid { grid-template-columns:repeat(2,minmax(0,1fr)); }
        }
        @media (max-width:900px) {
            /* Layar sempit: tema + akun pindah ke baris bawah sendiri
               supaya tidak menabrak judul / tombol garis-3 */
            .topbar { flex-wrap:wrap; row-gap:10px; padding:12px 18px; }
            .topbar-left { width:100%; }
            .topbar-right { width:100%; justify-content:flex-end; }
        }
        @media (max-width:640px) {
            .content { padding:16px 14px 50px; }
            .topbar { padding:10px 14px; }
            .burger-btn { width:46px; height:44px; }
            .page-title { font-size:15.5px; }
            .account-info-txt { display:none; }
            .chart-box { height:240px; }
            .chart-box.sm { height:200px; }
        }
        @media (max-width:600px) {
            .hero-grid { grid-template-columns:1fr; }
            .kpi-hero { padding:16px; }
            .kh-value { font-size:28px; }
            .cc-header { padding:18px 16px; }
            .cc-title { font-size:19px; }
            .cc-right { width:100%; }
            .cc-select { flex:1; min-width:140px; }
            .cc-select select { width:100%; min-width:0; }
        }
    </style>
</head>
<body>

<div class="app">

    <!-- ==================== MAIN ==================== -->
    <div class="main">

        <header class="topbar">

            <div class="topbar-left">

                <button type="button" id="launcherBtn" class="burger-btn" title="Buka Menu" aria-label="Buka semua menu">
                    <span class="burger-lines"><i></i><i></i><i></i></span>
                </button>

                <div class="topbar-title-wrap">
                    <div class="page-title">Dashboard Administrator</div>
                    <div class="page-subtitle">Pusat monitoring PACHIRA DISTRINUSA</div>
                </div>

            </div>

            <div class="topbar-right">

                <button type="button" id="themeToggle" class="theme-toggle">
                    <span class="tt-icon">&#9728;</span>
                    <span class="tt-text">Light</span>
                </button>

                <div class="account-wrap" id="accountWrap">

                    <button type="button" id="accountBtn" class="account-btn">

                        <span class="avatar">
                            @if(!empty(auth()->user()->foto))
                                <img src="{{ asset('storage/' . auth()->user()->foto) }}" alt="">
                            @else
                                {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                            @endif
                        </span>

                        <span class="account-info-txt">
                            <strong style="display:block; font-size:12.5px;">{{ auth()->user()->name }}</strong>
                            <span style="font-size:11px; color:var(--muted);">{{ auth()->user()->role }}</span>
                        </span>

                    </button>

                    <div class="dropdown">

                        <a href="{{ route('profile.photo') }}">Ubah Foto Profil</a>
                        <a href="{{ route('password.edit') }}">Ubah Password</a>

                        <form method="POST" action="{{ route('logout') }}" data-no-loading>
                            @csrf
                            <button type="submit" style="color:var(--danger); font-weight:600;">Logout</button>
                        </form>

                    </div>

                </div>

            </div>

        </header>

        <section class="content">

            @php
                if (!function_exists('pdsFormatMinutes')) {
                    function pdsFormatMinutes($m) {
                        if ($m <= 0) return '-';
                        $days = floor($m / 1440);
                        $hours = floor(($m % 1440) / 60);
                        $mins = $m % 60;
                        $out = '';
                        if ($days > 0) $out .= $days . 'h ';
                        if ($hours > 0) $out .= $hours . 'j ';
                        if ($mins > 0 || $out === '') $out .= $mins . 'm';
                        return trim($out);
                    }
                }

                $statusColors = [
                    'OPEN' => '#2563eb',
                    'DITERIMA' => '#0891b2',
                    'SCHEDULED' => '#7c3aed',
                    'IN PROGRESS' => '#d97706',
                    'PENDING' => '#f59e0b',
                    'SERVICE LUAR' => '#db2777',
                    'DITOLAK' => '#b91c1c',
                    'CLOSE' => '#16a34a',
                ];

                $deptLabel = $filterDepartemen !== ''
                    ? strtoupper($filterDepartemen)
                    : 'SEMUA DEPARTEMEN';

                $woBerjalan = max(0, $totalWo - ($statusCounts['CLOSE'] ?? 0));

                $totalPendingMenit = (int) array_sum($delayReasonTotals);

                $availPct = $totalMesin > 0
                    ? (int) round($mesinAktif / $totalMesin * 100)
                    : 0;
            @endphp

            <!-- ============================================================
                 FILTER FORM: header command center (tahun + departemen)
                 dan baris filter tambahan ada dalam SATU form GET yang sama.
            ============================================================= -->
            <form method="GET" action="{{ route('dashboard') }}" id="filterForm">

                <!-- ============ COMMAND CENTER HEADER ============ -->
                <div class="cc-header">

                    <div class="cc-left">
                        <div class="cc-kicker">Pachira Distrinusa</div>
                        <h1 class="cc-title">Maintenance Systeam</h1>
                        <div class="cc-sub">
                            Monitoring Work Order &bull; Mesin &bull; Inventory &mdash; {{ $deptLabel }} &bull; {{ $filterTahun }}
                        </div>
                    </div>

                    <div class="cc-right">

                        <div class="cc-select">
                            <label for="hdrTahun">Tahun</label>
                            <select name="tahun" id="hdrTahun">
                                @foreach($tahunList as $t)
                                    <option value="{{ $t }}" {{ $filterTahun == $t ? 'selected' : '' }}>{{ $t }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="cc-select">
                            <label for="hdrDepartemen">Departemen</label>
                            <select name="departemen" id="hdrDepartemen">
                                <option value="">Semua</option>
                                @foreach($departemenList as $d)
                                    <option value="{{ $d }}" {{ strtolower($filterDepartemen) === strtolower($d) ? 'selected' : '' }}>{{ strtoupper($d) }}</option>
                                @endforeach
                            </select>
                        </div>

                    </div>

                </div>

                <!-- ============ FILTER TAMBAHAN ============ -->
                <div class="filter-card">
                    <div class="filter-row">

                        <div class="filter-field">
                            <label>Bulan</label>
                            <select name="bulan">
                                <option value="">Semua</option>
                                @foreach($monthNames as $i => $nm)
                                    <option value="{{ $i+1 }}" {{ $filterBulan == $i+1 ? 'selected' : '' }}>{{ $nm }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="filter-field">
                            <label>Status</label>
                            <select name="status">
                                <option value="">Semua</option>
                                @foreach($statusKeys as $sk)
                                    <option value="{{ $sk }}" {{ strtoupper($filterStatus) === $sk ? 'selected' : '' }}>{{ $sk }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="filter-field">
                            <label>Prioritas</label>
                            <select name="prioritas">
                                <option value="">Semua</option>
                                @foreach($prioritasKeys as $pk)
                                    <option value="{{ $pk }}" {{ strtoupper($filterPrioritas) === $pk ? 'selected' : '' }}>{{ $pk }}</option>
                                @endforeach
                            </select>
                        </div>

                        <button type="submit" class="btn-primary">Terapkan Filter</button>
                        <a href="{{ route('dashboard') }}" class="btn-ghost">Reset</a>

                    </div>
                </div>

            </form>

            <!-- ============ 4 HERO KPI UTAMA ============ -->
            <div class="hero-grid">

                <div class="kpi-hero" style="--hc:#2563eb; --hc-soft:rgba(37,99,235,.10);">
                    <div class="kh-top">
                        <span class="kh-icon">
                            <svg viewBox="0 0 24 24"><rect x="8" y="2" width="8" height="4" rx="1"/><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><path d="M9 12h6"/><path d="M9 16h6"/></svg>
                        </span>
                        <span class="kh-label">Total<br>Work Order</span>
                    </div>
                    <div class="kh-value">{{ number_format($totalWo) }}</div>
                    <div class="kh-foot">{{ $deptLabel }} &bull; {{ $filterTahun }}</div>
                </div>

                <div class="kpi-hero" style="--hc:#16a34a; --hc-soft:rgba(22,163,74,.10);">
                    <div class="kh-top">
                        <span class="kh-icon">
                            <svg viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                        </span>
                        <span class="kh-label">Completion<br>Rate</span>
                    </div>
                    <div class="kh-value">{{ $completionRate }}%</div>
                    <div class="kh-foot">{{ number_format($statusCounts['CLOSE'] ?? 0) }} CLOSE dari {{ number_format($totalWo) }} WO</div>
                    <div class="kh-bar"><i style="width:{{ max(0, min(100, $completionRate)) }}%;"></i></div>
                </div>

                <div class="kpi-hero" style="--hc:#0891b2; --hc-soft:rgba(8,145,178,.10);">
                    <div class="kh-top">
                        <span class="kh-icon">
                            <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        </span>
                        <span class="kh-label">On-Time<br>Rate</span>
                    </div>
                    <div class="kh-value">{{ $onTimeRate }}%</div>
                    <div class="kh-foot">{{ number_format($closedOnTime) }} dari {{ number_format($closedTotal) }} close sesuai SLA</div>
                    <div class="kh-bar"><i style="width:{{ max(0, min(100, $onTimeRate)) }}%;"></i></div>
                </div>

                <div class="kpi-hero" style="--hc:#dc2626; --hc-soft:rgba(220,38,38,.10);">
                    <div class="kh-top">
                        <span class="kh-icon">
                            <svg viewBox="0 0 24 24"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                        </span>
                        <span class="kh-label">Overdue<br>Work Order</span>
                    </div>
                    <div class="kh-value">{{ number_format($overdueWo) }}</div>
                    <div class="kh-foot">Melewati batas SLA prioritas</div>
                </div>

            </div>

            <!-- ============ CHIP METRIK PENDUKUNG ============ -->
            <div class="chip-row" style="margin-bottom:20px;">
                <span class="chip">
                    <span class="chip-label">WO Berjalan</span>
                    <span class="chip-value">{{ number_format($woBerjalan) }}</span>
                </span>
                <span class="chip">
                    <span class="chip-label">Avg Lead Time</span>
                    <span class="chip-value">{{ pdsFormatMinutes($avgLeadMinutes) }}</span>
                </span>
                <span class="chip">
                    <span class="chip-label">Avg Work Time</span>
                    <span class="chip-value">{{ pdsFormatMinutes($avgWorkMinutes) }}</span>
                </span>
                <span class="chip">
                    <span class="chip-label">Total Pending</span>
                    <span class="chip-value">{{ number_format($totalPendingMenit) }} mnt</span>
                </span>
            </div>

            <!-- ============ BARIS: WO TREND + WO STATUS ============ -->
            <div class="grid-2">

                <div class="chart-card">
                    <h3>Tren Work Order {{ $filterTahun }}</h3>
                    <p>Jumlah WO per bulan (area grafik)</p>
                    <div class="chart-box"><canvas id="chTrend"></canvas></div>
                </div>

                <div class="chart-card">
                    <h3>Komposisi Status WO</h3>
                    <p>Semua status {{ $deptLabel }} {{ $filterTahun }}</p>
                    <div class="chart-box sm"><canvas id="chStatus"></canvas></div>
                    <div class="mini-stats">
                        @foreach($statusKeys as $sk)
                            <div class="mini-stat">
                                <i class="ms-dot" style="background:{{ $statusColors[$sk] }};"></i>
                                <span>{{ $sk }}</span>
                                <strong>{{ number_format($statusCounts[$sk]) }}</strong>
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>

            <!-- ============ BARIS: TOP MESIN + MACHINE HEALTH ============ -->
            <div class="grid-2">

                <div class="chart-card">
                    <h3>Top Kerusakan Mesin</h3>
                    <p>Mesin dengan WO terbanyak (data aktual)</p>
                    <div class="chart-box">
                        @if($topMesinLabels->count() > 0)
                            <canvas id="chTopMesin"></canvas>
                        @else
                            <div class="empty-note">Belum ada data kerusakan mesin pada filter ini.</div>
                        @endif
                    </div>
                </div>

                <div class="chart-card">
                    <h3>Machine Health</h3>
                    <p>Kondisi mesin terdaftar &amp; beban daya per line</p>

                    <div class="health-tiles">
                        <div class="htile">
                            <span>Total Mesin</span>
                            <strong>{{ number_format($totalMesin) }}</strong>
                        </div>
                        <div class="htile ok">
                            <span>Aktif</span>
                            <strong>{{ number_format($mesinAktif) }}</strong>
                        </div>
                        <div class="htile off">
                            <span>Tidak Aktif</span>
                            <strong>{{ number_format($mesinTidakAktif) }}</strong>
                        </div>
                        <div class="htile kw">
                            <span>Total Daya</span>
                            <strong>{{ number_format($totalKwAll, 2) }} kW</strong>
                        </div>
                    </div>

                    <div class="avail">
                        <div class="avail-head">
                            <span>Ketersediaan Mesin</span>
                            <strong>{{ $availPct }}%</strong>
                        </div>
                        <div class="avail-bar"><i style="width:{{ $availPct }}%;"></i></div>
                    </div>

                    @if($kwPerArea->count() > 0)
                        <div class="kw-scroll">
                            @foreach($kwPerArea as $area)
                                <div class="line-card">
                                    <div>
                                        <div class="line-name">{{ strtoupper($area->nama_area) }}</div>
                                        <div class="line-sub">{{ number_format($area->machines_count) }} mesin</div>
                                    </div>
                                    <div class="line-kw">{{ number_format((float) ($area->machines_sum_kw ?? 0), 2) }} kW</div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

            </div>

            <!-- ============ BARIS: PREVENTIVE VS BREAKDOWN + PERMINTAAN PEKERJAAN ============ -->
            <div class="grid-2">

                <div class="chart-card">
                    <h3>Preventive vs Breakdown {{ $filterTahun }}</h3>
                    <p>Perbandingan bulanan tujuan pekerjaan</p>
                    <div class="chart-box">
                        @if($tujuanKeys->count() > 0)
                            <canvas id="chPrevBrk"></canvas>
                        @else
                            <div class="empty-note">Belum ada data tujuan pekerjaan.</div>
                        @endif
                    </div>
                </div>

                <div class="chart-card">
                    <h3>Permintaan Pekerjaan {{ $filterTahun }}</h3>
                    <p>Total permintaan per jenis (kolom tujuan)</p>
                    <div class="chart-box">
                        @if($tujuanKeys->count() > 0)
                            <canvas id="chTujuan"></canvas>
                        @else
                            <div class="empty-note">Belum ada data permintaan pekerjaan.</div>
                        @endif
                    </div>
                </div>

            </div>

            <!-- ============ BARIS: KERUSAKAN PER AREA + INVENTORY BULANAN ============ -->
            <div class="grid-2">

                <div class="chart-card">
                    <h3>Kerusakan per Area / Line</h3>
                    <p>Area dengan laporan kerusakan terbanyak</p>
                    <div class="chart-box">
                        @if($topAreaLabels->count() > 0)
                            <canvas id="chTopArea"></canvas>
                        @else
                            <div class="empty-note">Belum ada data kerusakan per area.</div>
                        @endif
                    </div>
                </div>

                <div class="chart-card">
                    <h3>Barang Masuk vs Keluar {{ $filterTahun }}</h3>
                    <p>Total qty per bulan</p>
                    <div class="chart-box"><canvas id="chInventory"></canvas></div>
                </div>

            </div>

            <!-- ============ RINGKASAN INVENTORY ============ -->
            <div class="section-title">Ringkasan Inventory {{ $filterTahun }}</div>
            <div class="kpi-grid wide" style="margin-bottom:6px;">

                <div class="kpi-card" style="--kc:#0ea5e9;">
                    <div class="kpi-label">Total Item Barang</div>
                    <div class="kpi-value">{{ number_format($totalItems) }}</div>
                    <div class="kpi-foot">Jenis barang terdaftar</div>
                </div>

                <div class="kpi-card" style="--kc:#0891b2;">
                    <div class="kpi-label">Total Stok</div>
                    <div class="kpi-value">{{ number_format($totalStock) }}</div>
                    <div class="kpi-foot">Akumulasi stok gudang</div>
                </div>

                <div class="kpi-card" style="--kc:#16a34a;">
                    <div class="kpi-label">Barang Masuk</div>
                    <div class="kpi-value">{{ number_format($barangMasukQty) }}</div>
                    <div class="kpi-foot">{{ number_format($barangMasukCount) }} transaksi masuk</div>
                </div>

                <div class="kpi-card" style="--kc:#dc2626;">
                    <div class="kpi-label">Barang Keluar</div>
                    <div class="kpi-value">{{ number_format($barangKeluarQty) }}</div>
                    <div class="kpi-foot">{{ number_format($barangKeluarCount) }} transaksi keluar</div>
                </div>

                <div class="kpi-card" style="--kc:#d97706;">
                    <div class="kpi-label">Stok Kritis</div>
                    <div class="kpi-value">{{ number_format($lowStockItems) }}</div>
                    <div class="kpi-foot">Stok &le; stok minimum</div>
                </div>

            </div>

            <!-- ============ DETAIL: KINERJA PER DEPARTEMEN ============ -->
            <details class="panel-details">
                <summary>Kinerja per Departemen {{ $filterTahun }}</summary>
                <div class="panel-inner">

                    <div class="table-card" style="box-shadow:none;">
                        <div class="table-scroll">
                            <table class="monitoring">
                                <thead>
                                    <tr>
                                        <th>Departemen</th>
                                        <th>Total WO</th>
                                        <th>Selesai (Close)</th>
                                        <th>Rata2 Lapor &rarr; Selesai</th>
                                        <th>Rata2 Waktu Perbaikan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($deptPerformance as $dp)
                                        <tr>
                                            <td style="font-weight:600;">{{ $dp['departemen'] }}</td>
                                            <td>{{ number_format($dp['total']) }}</td>
                                            <td>{{ number_format($dp['selesai']) }}</td>
                                            <td>{{ pdsFormatMinutes($dp['avgLeadMinutes'] ?? 0) }}</td>
                                            <td>{{ pdsFormatMinutes($dp['avgWorkMinutes'] ?? 0) }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="5"><div class="empty-note">Belum ada data work order pada filter ini.</div></td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    @if($deptPerformance->count() > 0)
                        <div class="chart-box" style="max-width:720px;">
                            <canvas id="chDeptAvg"></canvas>
                        </div>
                    @endif

                </div>
            </details>

            <!-- ============ DETAIL: PRIORITAS & PENDING ============ -->
            <details class="panel-details">
                <summary>Prioritas &amp; Analisis Pending</summary>
                <div class="panel-inner">
                    <div class="chart-duo">

                        <div class="chart-card" style="box-shadow:none;">
                            <h3>Prioritas Work Order</h3>
                            <p>Normal / Urgent / Emergency</p>
                            <div class="chart-box sm"><canvas id="chPrioritas"></canvas></div>
                        </div>

                        <div class="chart-card" style="box-shadow:none;">
                            <h3>Pending / Delay Reason</h3>
                            <p>Total menit pending per alasan (histori status)</p>
                            <div class="chart-box sm">
                                @if(count($delayReasonTotals) > 0)
                                    <canvas id="chDelay"></canvas>
                                @else
                                    <div class="empty-note">Belum ada histori pending.</div>
                                @endif
                            </div>
                        </div>

                    </div>
                </div>
            </details>

            <!-- ============ DETAIL: MONITORING USER ============ -->
            <details class="panel-details">
                <summary>Monitoring User &amp; Aktivitas Realtime</summary>
                <div class="panel-inner">

                    <div class="kpi-grid wide" style="margin-bottom:14px;">

                        <div class="kpi-card" style="--kc:#2563eb;">
                            <div class="kpi-label">Total User</div>
                            <div class="kpi-value">{{ number_format($userStats['total']) }}</div>
                            <div class="kpi-foot">Seluruh akun terdaftar</div>
                        </div>

                        <div class="kpi-card" style="--kc:#16a34a;">
                            <div class="kpi-label">Aktif</div>
                            <div class="kpi-value">{{ number_format($userStats['aktif']) }}</div>
                            <div class="kpi-foot">Status akun aktif</div>
                        </div>

                        <div class="kpi-card" style="--kc:#64748b;">
                            <div class="kpi-label">Nonaktif</div>
                            <div class="kpi-value">{{ number_format($userStats['nonaktif']) }}</div>
                            <div class="kpi-foot">Akun dinonaktifkan</div>
                        </div>

                        <div class="kpi-card" style="--kc:#059669;">
                            <div class="kpi-label">Online</div>
                            <div class="kpi-value">{{ number_format($userStats['online']) }}</div>
                            <div class="kpi-foot">Aktivitas &le; 5 menit</div>
                        </div>

                        <div class="kpi-card" style="--kc:#94a3b8;">
                            <div class="kpi-label">Offline</div>
                            <div class="kpi-value">{{ number_format($userStats['offline']) }}</div>
                            <div class="kpi-foot">Tidak aktif saat ini</div>
                        </div>

                        <div class="kpi-card" style="--kc:#7c3aed;">
                            <div class="kpi-label">Login Hari Ini</div>
                            <div class="kpi-value">{{ number_format($userStats['loginHariIni']) }}</div>
                            <div class="kpi-foot">Last login hari ini</div>
                        </div>

                    </div>

                    <div class="table-card" style="box-shadow:none;">
                        <div class="table-scroll">
                            <table class="monitoring">
                                <thead>
                                    <tr>
                                        <th>Nama</th>
                                        <th>Role</th>
                                        <th>Bagian</th>
                                        <th>Status Akun</th>
                                        <th>Online</th>
                                        <th>Last Login</th>
                                        <th>Last Activity</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($monitoringUsers as $mu)
                                        @php
                                            $isOnline =
                                                $mu->last_activity_at &&
                                                \Carbon\Carbon::parse($mu->last_activity_at)
                                                    ->greaterThan(now()->subMinutes(
                                                        \App\Http\Middleware\UpdateLastActivity::ONLINE_THRESHOLD_MINUTES
                                                    ));
                                        @endphp
                                        <tr>
                                            <td style="font-weight:600;">{{ $mu->name }}</td>
                                            <td>{{ strtoupper($mu->role ?? '-') }}</td>
                                            <td>{{ $mu->bagian ?? '-' }}</td>
                                            <td>
                                                <span class="badge {{ strtolower(trim((string)$mu->status)) === 'aktif' ? 'on' : 'off' }}">
                                                    {{ strtoupper($mu->status ?? 'NONAKTIF') }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge {{ $isOnline ? 'on' : 'off' }}">
                                                    <span class="dot"></span> {{ $isOnline ? 'ONLINE' : 'OFFLINE' }}
                                                </span>
                                            </td>
                                            <td>{{ $mu->last_login_at ? \Carbon\Carbon::parse($mu->last_login_at)->format('d M Y H:i') : '-' }}</td>
                                            <td>{{ $mu->last_activity_at ? \Carbon\Carbon::parse($mu->last_activity_at)->format('d M Y H:i') : '-' }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="7"><div class="empty-note">Belum ada user.</div></td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </details>

            <!-- ============ MENU CEPAT ============ -->
            <div class="section-title">Menu Cepat</div>
            <div class="quick-grid">

                <a class="quick-card" href="{{ route('work-orders.admin.create') }}">
                    <span class="quick-icon">
                        <svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><line x1="12" y1="18" x2="12" y2="12"/><line x1="9" y1="15" x2="15" y2="15"/></svg>
                    </span>
                    <span class="quick-text"><strong>Buat WO</strong><span>Work order baru</span></span>
                </a>

                <a class="quick-card" href="{{ route('barang.index') }}">
                    <span class="quick-icon">
                        <svg viewBox="0 0 24 24"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><path d="M3.27 6.96L12 12.01l8.73-5.05"/><path d="M12 22.08V12"/></svg>
                    </span>
                    <span class="quick-text"><strong>Stok Barang</strong><span>Inventory</span></span>
                </a>

                <a class="quick-card" href="{{ route('users.index') }}">
                    <span class="quick-icon">
                        <svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    </span>
                    <span class="quick-text"><strong>Manajemen User</strong><span>Akun &amp; role</span></span>
                </a>

                <a class="quick-card" href="{{ route('settings.index') }}">
                    <span class="quick-icon">
                        <svg viewBox="0 0 24 24"><line x1="4" y1="21" x2="4" y2="14"/><line x1="4" y1="10" x2="4" y2="3"/><line x1="12" y1="21" x2="12" y2="12"/><line x1="12" y1="8" x2="12" y2="3"/><line x1="20" y1="21" x2="20" y2="16"/><line x1="20" y1="12" x2="20" y2="3"/><circle cx="4" cy="12" r="2"/><circle cx="12" cy="10" r="2"/><circle cx="20" cy="14" r="2"/></svg>
                    </span>
                    <span class="quick-text"><strong>Setting</strong><span>Konfigurasi sistem</span></span>
                </a>

                <a class="quick-card" href="{{ route('activity.index') }}">
                    <span class="quick-icon">
                        <svg viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                    </span>
                    <span class="quick-text"><strong>Log Aktivitas</strong><span>Audit trail</span></span>
                </a>

            </div>

        </section>

    </div>

</div>

<!-- ==================== LAUNCHER SEMUA MENU ==================== -->
<div class="launcher" id="launcher">

    <div class="launcher-panel">

        <div class="launcher-head">
            <div>
                <div class="launcher-title">Semua Menu</div>
                <div class="launcher-subtitle">PACHIRA MAINTENANCE SYSTEM</div>
            </div>
            <button type="button" id="launcherClose" class="launcher-close" title="Tutup">&#10005;</button>
        </div>

        <!-- ============ GRID UTAMA: KIRI | TENGAH | KANAN ============ -->
        <div class="menu-grid">

            <!-- ================= KOLOM KIRI ================= -->
            <div class="menu-col">

                {{-- DASHBOARD: langsung masuk dashboard --}}
                <a class="mcard" href="{{ route('dashboard') }}">
                    <span class="mcard-icon">
                        <svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="9" rx="1"/><rect x="14" y="3" width="7" height="5" rx="1"/><rect x="14" y="12" width="7" height="9" rx="1"/><rect x="3" y="16" width="7" height="5" rx="1"/></svg>
                    </span>
                    <span class="mcard-name">Dashboard</span>
                    <span class="mcard-desc">Pusat Monitoring</span>
                </a>

                {{-- MASTER MESIN --}}
                <div class="mgroup">

                    <button type="button" class="mcard" aria-expanded="false">
                        <span class="mcard-chevron"></span>
                        <span class="mcard-icon">
                            <svg viewBox="0 0 24 24"><path d="M12 3l8 4.5v9L12 21l-8-4.5v-9z"/><path d="M12 12l8-4.5"/><path d="M12 12v9"/><path d="M12 12L4 7.5"/></svg>
                        </span>
                        <span class="mcard-name">Master Mesin</span>
                        <span class="mcard-desc">Data Master</span>
                    </button>

                    <div class="msub">
                        <div class="msub-inner">
                            <a href="{{ route('areas.index') }}">Area / Line</a>
                            <a href="{{ route('machines.index') }}">Mesin</a>
                            <a href="{{ route('machine-spareparts.index') }}">Mesin &amp; Sparepart</a>
                        </div>
                    </div>

                </div>

            </div>

            <!-- ================= KOLOM TENGAH ================= -->
            <div class="menu-col">

                {{-- WORK ORDER --}}
                <div class="mgroup">

                    <button type="button" class="mcard" aria-expanded="false">
                        <span class="mcard-chevron"></span>
                        <span class="mcard-icon">
                            <svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><path d="M16 13H8"/><path d="M16 17H8"/></svg>
                        </span>
                        <span class="mcard-name">Work Order</span>
                        <span class="mcard-desc">Kelola WO</span>
                    </button>

                    <div class="msub">
                        <div class="msub-inner">
                            <a href="{{ route('work-orders.admin.index') }}">Daftar Work Order</a>
                            <a href="{{ route('work-orders.admin.report') }}">Laporan Work Order</a>
                        </div>
                    </div>

                </div>

                {{-- SETTING --}}
                <div class="mgroup">

                    <button type="button" class="mcard" aria-expanded="false">
                        <span class="mcard-chevron"></span>
                        <span class="mcard-icon">
                            <svg viewBox="0 0 24 24"><line x1="4" y1="21" x2="4" y2="14"/><line x1="4" y1="10" x2="4" y2="3"/><line x1="12" y1="21" x2="12" y2="12"/><line x1="12" y1="8" x2="12" y2="3"/><line x1="20" y1="21" x2="20" y2="16"/><line x1="20" y1="12" x2="20" y2="3"/><circle cx="4" cy="12" r="2"/><circle cx="12" cy="10" r="2"/><circle cx="20" cy="14" r="2"/></svg>
                        </span>
                        <span class="mcard-name">Setting</span>
                        <span class="mcard-desc">Pengaturan Sistem</span>
                    </button>

                    <div class="msub">
                        <div class="msub-inner">
                            <a href="{{ route('users.index') }}">Manajemen User</a>
                            <a href="{{ route('activity.index') }}">Log Aktivitas</a>
                            <a href="{{ route('settings.index') }}">Pengaturan Sistem</a>
                        </div>
                    </div>

                </div>

            </div>

            <!-- ================= KOLOM KANAN ================= -->
            <div class="menu-col">

                {{-- INVENTORY --}}
                <div class="mgroup">

                    <button type="button" class="mcard" aria-expanded="false">
                        <span class="mcard-chevron"></span>
                        <span class="mcard-icon">
                            <svg viewBox="0 0 24 24"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><path d="M3.27 6.96L12 12.01l8.73-5.05"/><path d="M12 22.08V12"/></svg>
                        </span>
                        <span class="mcard-name">Inventory</span>
                        <span class="mcard-desc">Kelola Sparepart</span>
                    </button>

                    <div class="msub">
                        <div class="msub-inner">
                            <a href="{{ route('barang.index') }}">Stok Barang</a>
                            <a href="{{ route('barang-masuk.index') }}">Barang Masuk</a>
                            <a href="{{ route('barang-keluar.index') }}">Barang Keluar</a>
                            <a href="{{ route('purchase-requests.index') }}">Purchase Request</a>
                            <a href="{{ route('laporan-harian.index') }}">Laporan Harian</a>
                        </div>
                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

<script src="{{ asset('assets/vendor/chart.umd.min.js') }}"></script>
<script>
(function () {

    'use strict';


    /* ============ THEME ============ */

    var toggle = document.getElementById('themeToggle');

    function currentTheme() {
        return document.documentElement.getAttribute('data-theme') || 'light';
    }

    function applyThemeUi() {
        var dark = currentTheme() === 'dark';
        var icon = toggle ? toggle.querySelector('.tt-icon') : null;
        var text = toggle ? toggle.querySelector('.tt-text') : null;
        if (icon) icon.innerHTML = dark ? '&#9790;' : '&#9728;';
        if (text) text.textContent = dark ? 'Dark' : 'Light';
    }

    applyThemeUi();

    if (toggle) {
        toggle.addEventListener('click', function () {
            var next = currentTheme() === 'dark' ? 'light' : 'dark';
            document.documentElement.setAttribute('data-theme', next);
            try { localStorage.setItem('pachira-theme', next); } catch (e) {}
            applyThemeUi();
            document.dispatchEvent(new CustomEvent('pachira:theme', { detail: { theme: next } }));
        });
    }


    /* ============ ACCOUNT DROPDOWN ============ */

    var wrap = document.getElementById('accountWrap');
    var btn = document.getElementById('accountBtn');
    if (wrap && btn) {
        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            wrap.classList.toggle('open');
        });
        document.addEventListener('click', function (e) {
            if (!wrap.contains(e.target)) wrap.classList.remove('open');
        });
    }


    /* ============ LAUNCHER SEMUA MENU ============ */

    var launcher = document.getElementById('launcher');
    var launcherBtn = document.getElementById('launcherBtn');
    var launcherClose = document.getElementById('launcherClose');

    function openLauncher() {
        if (launcher) {
            launcher.classList.add('open');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeLauncher() {
        if (launcher) {
            launcher.classList.remove('open');
            document.body.style.overflow = '';
        }
    }

    if (launcherBtn) launcherBtn.addEventListener('click', openLauncher);
    if (launcherClose) launcherClose.addEventListener('click', closeLauncher);
    if (launcher) {
        launcher.addEventListener('click', function (e) {
            if (e.target === launcher) closeLauncher();
        });
    }


    /* ============ SUBMENU KARTU UTAMA (ACCORDION) ============ */
    /* Klik satu card utama -> HANYA submenu card itu yang tampil.
       Card lain yang sedang terbuka otomatis ditutup. */

    var mainCards = document.querySelectorAll('.mgroup > .mcard');

    Array.prototype.forEach.call(mainCards, function (mbtn) {

        mbtn.addEventListener('click', function () {

            var group = mbtn.closest('.mgroup');
            var wasOpen = group.classList.contains('open');

            Array.prototype.forEach.call(
                document.querySelectorAll('.mgroup.open'),
                function (openGroup) {
                    openGroup.classList.remove('open');
                    var b = openGroup.querySelector('.mcard');
                    if (b) b.setAttribute('aria-expanded', 'false');
                }
            );

            if (!wasOpen) {
                group.classList.add('open');
                mbtn.setAttribute('aria-expanded', 'true');
            }

        });

    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            closeLauncher();
        }
    });


    /* ============ HEADER SELECT: AUTO SUBMIT ============ */

    Array.prototype.forEach.call(
        document.querySelectorAll('#hdrTahun, #hdrDepartemen'),
        function (sel) {
            sel.addEventListener('change', function () {
                if (sel.form) sel.form.submit();
            });
        }
    );


    /* ============ CHARTS ============ */

    var charts = {};

    function cssVar(name, fallback) {
        var v = getComputedStyle(document.documentElement).getPropertyValue(name).trim();
        return v || fallback;
    }

    function palette() {
        var dark = currentTheme() === 'dark';
        return {
            dark: dark,
            text: dark ? '#cbd5e1' : '#334155',
            muted: dark ? '#94a3b8' : '#64748b',
            grid: dark ? 'rgba(148,163,184,.12)' : 'rgba(15,23,42,.07)',
            primary: '#2563eb',
            green: '#16a34a',
            amber: '#f59e0b',
            red: '#dc2626',
            cyan: '#0891b2',
            violet: '#7c3aed',
            pink: '#db2777',
            slate: '#64748b'
        };
    }

    /*
     | Opsi dasar Chart.js yang sadar tema.
     | opts.legend     : tampilkan legenda bawah
     | opts.horizontal : bar horizontal (indexAxis y)
     | opts.noScales   : tanpa konfigurasi sumbu (doughnut)
     */
    function baseOpts(p, opts) {

        opts = opts || {};

        var conf = {
            responsive: true,
            maintainAspectRatio: false,
            animation: { duration: 550 },
            plugins: {
                legend: {
                    display: !!opts.legend,
                    position: 'bottom',
                    labels: { color: p.text, boxWidth: 12, boxHeight: 12, font: { size: 11 } }
                },
                tooltip: {
                    backgroundColor: p.dark ? '#1e293b' : '#0f172a',
                    titleColor: '#fff',
                    bodyColor: '#e2e8f0',
                    padding: 10,
                    cornerRadius: 8
                }
            }
        };

        if (opts.noScales) {
            return conf;
        }

        if (opts.horizontal) {

            conf.indexAxis = 'y';
            conf.scales = {
                x: {
                    ticks: { color: p.muted, font: { size: 10.5 }, precision: 0 },
                    grid: { color: p.grid }
                },
                y: {
                    ticks: { color: p.muted, font: { size: 10.5 } },
                    grid: { color: p.grid }
                }
            };

        } else {

            conf.scales = {
                x: {
                    ticks: { color: p.muted, font: { size: 10.5 } },
                    grid: { color: p.grid }
                },
                y: {
                    ticks: { color: p.muted, font: { size: 10.5 }, precision: 0 },
                    grid: { color: p.grid }
                }
            };
        }

        return conf;
    }

    function make(id, cfg) {
        var el = document.getElementById(id);
        if (!el) return;
        charts[id] = new Chart(el.getContext('2d'), cfg);
    }

    function renderAll() {

        Object.keys(charts).forEach(function (k) {
            charts[k].destroy();
            delete charts[k];
        });

        var p = palette();

        var tujuanCols = [p.green, p.primary, p.amber, p.violet, p.cyan, p.red, p.pink, p.slate];

        var tk = @json($tujuanKeys->values());
        var tm = @json($tujuanMonthly->values());
        var tt = @json($tujuanTotals->values());


        /* --- Tren Work Order (line/area) --- */

        make('chTrend', {
            type: 'line',
            data: {
                labels: @json($monthNames),
                datasets: [{
                    label: 'Work Order',
                    data: @json($trendValues),
                    borderColor: p.primary,
                    backgroundColor: p.dark ? 'rgba(37,99,235,.20)' : 'rgba(37,99,235,.12)',
                    fill: true,
                    tension: .38,
                    pointRadius: 3,
                    pointBackgroundColor: p.primary
                }]
            },
            options: baseOpts(p)
        });


        /* --- Komposisi Status WO (doughnut) --- */

        make('chStatus', {
            type: 'doughnut',
            data: {
                labels: @json($statusChartLabels),
                datasets: [{
                    data: @json($statusChartValues),
                    backgroundColor: [
                        '#2563eb','#0891b2','#7c3aed','#f59e0b',
                        '#fbbf24','#db2777','#b91c1c','#16a34a'
                    ],
                    borderWidth: 0
                }]
            },
            options: baseOpts(p, { legend: true, noScales: true })
        });


        /* --- Top kerusakan mesin (horizontal bar) --- */

        make('chTopMesin', {
            type: 'bar',
            data: {
                labels: @json($topMesinLabels),
                datasets: [{
                    label: 'WO Kerusakan',
                    data: @json($topMesinValues),
                    backgroundColor: p.primary,
                    borderRadius: 6,
                    maxBarThickness: 20
                }]
            },
            options: baseOpts(p, { horizontal: true })
        });


        /* --- Preventive vs Breakdown per bulan --- */

        make('chPrevBrk', {
            type: 'bar',
            data: {
                labels: @json($monthNames),
                datasets: tk.map(function (key, i) {
                    return {
                        label: key,
                        data: tm[i] || [],
                        backgroundColor: tujuanCols[i % tujuanCols.length],
                        borderRadius: 5,
                        maxBarThickness: 16
                    };
                })
            },
            options: baseOpts(p, { legend: true })
        });


        /* --- Total permintaan pekerjaan per tujuan --- */

        make('chTujuan', {
            type: 'bar',
            data: {
                labels: tk,
                datasets: [{
                    label: 'Permintaan',
                    data: tt,
                    backgroundColor: tk.map(function (key, i) {
                        return tujuanCols[i % tujuanCols.length];
                    }),
                    borderRadius: 6,
                    maxBarThickness: 26
                }]
            },
            options: baseOpts(p, { horizontal: true })
        });


        /* --- Kerusakan per area (horizontal bar) --- */

        make('chTopArea', {
            type: 'bar',
            data: {
                labels: @json($topAreaLabels),
                datasets: [{
                    label: 'WO Kerusakan',
                    data: @json($topAreaValues),
                    backgroundColor: p.cyan,
                    borderRadius: 6,
                    maxBarThickness: 20
                }]
            },
            options: baseOpts(p, { horizontal: true })
        });


        /* --- Inventory masuk vs keluar per bulan --- */

        make('chInventory', {
            type: 'bar',
            data: {
                labels: @json($monthNames),
                datasets: [
                    {
                        label: 'Barang Masuk',
                        data: @json($inventoryInValues),
                        backgroundColor: p.green,
                        borderRadius: 6,
                        maxBarThickness: 18
                    },
                    {
                        label: 'Barang Keluar',
                        data: @json($inventoryOutValues),
                        backgroundColor: p.red,
                        borderRadius: 6,
                        maxBarThickness: 18
                    }
                ]
            },
            options: baseOpts(p, { legend: true })
        });


        /* --- Rata-rata waktu per departemen (detail) --- */

        @if($deptPerformance->count() > 0)
        make('chDeptAvg', {
            type: 'bar',
            data: {
                labels: @json($deptPerformance->pluck('departemen')),
                datasets: [
                    {
                        label: 'Lapor - Selesai (menit)',
                        data: @json($deptPerformance->map(fn ($d) => $d['avgLeadMinutes'] ?? 0)),
                        backgroundColor: p.cyan,
                        borderRadius: 6,
                        maxBarThickness: 16
                    },
                    {
                        label: 'Waktu Perbaikan (menit)',
                        data: @json($deptPerformance->map(fn ($d) => $d['avgWorkMinutes'] ?? 0)),
                        backgroundColor: p.violet,
                        borderRadius: 6,
                        maxBarThickness: 16
                    }
                ]
            },
            options: baseOpts(p, { legend: true })
        });
        @endif


        /* --- Prioritas WO (detail) --- */

        make('chPrioritas', {
            type: 'bar',
            data: {
                labels: @json($prioritasKeys),
                datasets: [{
                    label: 'WO',
                    data: @json($prioritasValues),
                    backgroundColor: [p.slate, p.amber, p.red],
                    borderRadius: 7,
                    maxBarThickness: 52
                }]
            },
            options: baseOpts(p)
        });


        /* --- Pending / delay reason (detail) --- */

        @if(count($delayReasonTotals) > 0)
        make('chDelay', {
            type: 'bar',
            data: {
                labels: @json(array_keys($delayReasonTotals)),
                datasets: [{
                    label: 'Menit Pending',
                    data: @json(array_values($delayReasonTotals)),
                    backgroundColor: p.amber,
                    borderRadius: 7,
                    maxBarThickness: 40
                }]
            },
            options: baseOpts(p, { horizontal: true })
        });
        @endif
    }

    renderAll();

    document.addEventListener('pachira:theme', renderAll);

})();
</script>

</body>
</html>
