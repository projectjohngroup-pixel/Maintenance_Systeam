<!DOCTYPE html>
<html lang="id" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>System Guard — 24/7 Security &amp; Availability Center</title>
    <script src="{{ asset('assets/vendor/chart.umd.min.js') }}"></script>
    <style>
        :root {
            --bg: #0a0e1a;
            --bg-2: #0f1424;
            --bg-3: #141b2d;
            --surface: #1a2236;
            --surface-2: #1e2841;
            --border: #253050;
            --border-glow: #2a3a5c;
            --text: #e2e8f0;
            --text-2: #94a3b8;
            --text-3: #64748b;
            --green: #22c55e;
            --green-soft: rgba(34,197,94,.12);
            --green-glow: rgba(34,197,94,.25);
            --yellow: #eab308;
            --yellow-soft: rgba(234,179,8,.12);
            --yellow-glow: rgba(234,179,8,.25);
            --red: #ef4444;
            --red-soft: rgba(239,68,68,.12);
            --red-glow: rgba(239,68,68,.25);
            --blue: #3b82f6;
            --blue-soft: rgba(59,130,246,.12);
            --blue-glow: rgba(59,130,246,.25);
            --cyan: #06b6d4;
            --purple: #8b5cf6;
            --purple-soft: rgba(139,92,246,.12);
            --orange: #f97316;
            --radius: 12px;
            --radius-sm: 8px;
            --shadow: 0 4px 24px rgba(0,0,0,.3);
            --shadow-lg: 0 8px 40px rgba(0,0,0,.4);
        }

        * { margin:0; padding:0; box-sizing:border-box; }

        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: var(--bg);
            color: var(--text);
            font-size: 14px;
            line-height: 1.5;
            min-height: 100vh;
        }

        a { color: var(--blue); text-decoration: none; }
        a:hover { text-decoration: underline; }

        /* ============ HEADER ============ */
        .sg-header {
            background: var(--bg-2);
            border-bottom: 1px solid var(--border);
            padding: 16px 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            position: sticky;
            top: 0;
            z-index: 100;
            backdrop-filter: blur(12px);
        }

        .sg-brand {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .sg-brand-icon {
            width: 38px;
            height: 38px;
            background: linear-gradient(135deg, var(--blue), var(--cyan));
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 0 20px var(--blue-glow);
        }

        .sg-brand-icon svg { width: 22px; height: 22px; fill: #fff; }

        .sg-brand-text h1 {
            font-size: 16px;
            font-weight: 800;
            letter-spacing: 2px;
            color: #fff;
            text-transform: uppercase;
        }

        .sg-brand-text span {
            font-size: 10px;
            color: var(--text-3);
            letter-spacing: 3px;
            text-transform: uppercase;
        }

        .sg-header-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .sg-live {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
        }

        .sg-live-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--green);
            box-shadow: 0 0 8px var(--green-glow);
            animation: pulse-live 2s infinite;
        }

        @keyframes pulse-live {
            0%, 100% { opacity: 1; box-shadow: 0 0 8px var(--green-glow); }
            50% { opacity: .5; box-shadow: 0 0 16px var(--green-glow); }
        }

        .sg-status-pill {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .sg-status-pill.connected { background: var(--green-soft); color: var(--green); border: 1px solid rgba(34,197,94,.2); }
        .sg-status-pill.degraded { background: var(--yellow-soft); color: var(--yellow); border: 1px solid rgba(234,179,8,.2); }
        .sg-status-pill.disconnected { background: var(--red-soft); color: var(--red); border: 1px solid rgba(239,68,68,.2); }

        .sg-status-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: currentColor;
        }

        .sg-meta {
            text-align: right;
            font-size: 11px;
            color: var(--text-3);
            line-height: 1.6;
        }

        .sg-meta strong { color: var(--text-2); }

        /* ============ MAIN LAYOUT ============ */
        .sg-main {
            max-width: 1440px;
            margin: 0 auto;
            padding: 24px 28px 60px;
        }

        /* ============ PERIOD FILTER ============ */
        .sg-filter {
            display: flex;
            gap: 4px;
            background: var(--bg-3);
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            padding: 3px;
            width: fit-content;
            margin-bottom: 24px;
        }

        .sg-filter a {
            padding: 6px 14px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            color: var(--text-3);
            transition: all .2s;
        }

        .sg-filter a:hover { color: var(--text); text-decoration: none; background: var(--surface); }
        .sg-filter a.active { background: var(--blue); color: #fff; box-shadow: 0 0 12px var(--blue-glow); }

        /* ============ SUMMARY CARDS ============ */
        .sg-summary {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            margin-bottom: 24px;
        }

        .sg-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 20px;
            position: relative;
            overflow: hidden;
            transition: border-color .2s, box-shadow .2s;
        }

        .sg-card:hover {
            border-color: var(--border-glow);
            box-shadow: var(--shadow);
        }

        .sg-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
        }

        .sg-card.green::before { background: linear-gradient(90deg, var(--green), transparent); }
        .sg-card.yellow::before { background: linear-gradient(90deg, var(--yellow), transparent); }
        .sg-card.red::before { background: linear-gradient(90deg, var(--red), transparent); }
        .sg-card.blue::before { background: linear-gradient(90deg, var(--blue), transparent); }
        .sg-card.purple::before { background: linear-gradient(90deg, var(--purple), transparent); }
        .sg-card.cyan::before { background: linear-gradient(90deg, var(--cyan), transparent); }

        .sg-card-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
        }

        .sg-card-label {
            font-size: 11px;
            font-weight: 700;
            color: var(--text-3);
            text-transform: uppercase;
            letter-spacing: 1.2px;
        }

        .sg-card-icon {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .sg-card-icon svg { width: 18px; height: 18px; }

        .sg-card.green .sg-card-icon { background: var(--green-soft); color: var(--green); }
        .sg-card.yellow .sg-card-icon { background: var(--yellow-soft); color: var(--yellow); }
        .sg-card.red .sg-card-icon { background: var(--red-soft); color: var(--red); }
        .sg-card.blue .sg-card-icon { background: var(--blue-soft); color: var(--blue); }
        .sg-card.purple .sg-card-icon { background: var(--purple-soft); color: var(--purple); }
        .sg-card.cyan .sg-card-icon { background: var(--blue-soft); color: var(--cyan); }

        .sg-card-value {
            font-size: 32px;
            font-weight: 800;
            line-height: 1;
            margin-bottom: 4px;
        }

        .sg-card.green .sg-card-value { color: var(--green); }
        .sg-card.yellow .sg-card-value { color: var(--yellow); }
        .sg-card.red .sg-card-value { color: var(--red); }
        .sg-card.blue .sg-card-value { color: var(--blue); }
        .sg-card.purple .sg-card-value { color: var(--purple); }
        .sg-card.cyan .sg-card-value { color: var(--cyan); }

        .sg-card-sub {
            font-size: 11px;
            color: var(--text-3);
        }

        .sg-card-foot {
            margin-top: 12px;
            padding-top: 10px;
            border-top: 1px solid var(--border);
            font-size: 11px;
            color: var(--text-3);
        }

        /* ============ PANEL ============ */
        .sg-panel {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            overflow: hidden;
        }

        .sg-panel-head {
            padding: 16px 20px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .sg-panel-title {
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: var(--text-2);
        }

        .sg-panel-badge {
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: 700;
            background: var(--bg-3);
            color: var(--text-3);
            border: 1px solid var(--border);
        }

        .sg-panel-body { padding: 20px; }

        /* ============ GRID LAYOUTS ============ */
        .sg-grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-bottom: 16px;
        }

        .sg-grid-3 {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 16px;
            margin-bottom: 16px;
        }

        .sg-grid-health {
            display: grid;
            grid-template-columns: 280px 1fr;
            gap: 16px;
            margin-bottom: 16px;
        }

        /* ============ HEALTH GAUGE ============ */
        .sg-health-center {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 28px 20px;
        }

        .sg-gauge {
            position: relative;
            width: 160px;
            height: 160px;
            margin-bottom: 16px;
        }

        .sg-gauge svg {
            width: 100%;
            height: 100%;
            transform: rotate(-90deg);
        }

        .sg-gauge-bg { fill: none; stroke: var(--bg-3); stroke-width: 10; }
        .sg-gauge-fill { fill: none; stroke-width: 10; stroke-linecap: round; transition: stroke-dashoffset .8s ease; }

        .sg-gauge-text {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
        }

        .sg-gauge-value { font-size: 36px; font-weight: 800; line-height: 1; }
        .sg-gauge-label { font-size: 10px; color: var(--text-3); text-transform: uppercase; letter-spacing: 1.5px; margin-top: 4px; }

        .sg-health-legend {
            display: flex;
            gap: 16px;
            justify-content: center;
            font-size: 11px;
            color: var(--text-3);
        }

        .sg-health-legend span { display: flex; align-items: center; gap: 5px; }

        .sg-legend-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
        }

        /* ============ CHART BOX ============ */
        .sg-chart-box {
            position: relative;
            height: 240px;
        }

        /* ============ STATS ROW ============ */
        .sg-stats-row {
            display: flex;
            gap: 24px;
            padding: 16px 20px;
            background: var(--bg-3);
            border-radius: var(--radius-sm);
            margin-top: 16px;
        }

        .sg-stat {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .sg-stat-label { font-size: 10px; color: var(--text-3); text-transform: uppercase; letter-spacing: 1px; }
        .sg-stat-value { font-size: 14px; font-weight: 700; color: var(--text); }

        /* ============ LIVE FEED ============ */
        .sg-feed {
            max-height: 440px;
            overflow-y: auto;
        }

        .sg-feed-item {
            display: grid;
            grid-template-columns: 60px auto 1fr auto;
            gap: 12px;
            align-items: start;
            padding: 12px 20px;
            border-bottom: 1px solid var(--border);
            transition: background .15s;
            cursor: pointer;
        }

        .sg-feed-item:hover { background: var(--bg-3); }
        .sg-feed-item:last-child { border-bottom: none; }

        .sg-feed-time {
            font-size: 12px;
            font-weight: 600;
            color: var(--text-3);
            font-variant-numeric: tabular-nums;
        }

        .sg-feed-severity {
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .8px;
            white-space: nowrap;
        }

        .sg-feed-severity.normal { background: var(--green-soft); color: var(--green); }
        .sg-feed-severity.perhatian { background: var(--yellow-soft); color: var(--yellow); }
        .sg-feed-severity.gangguan { background: var(--red-soft); color: var(--red); }
        .sg-feed-severity.serius { background: var(--red-soft); color: var(--red); }
        .sg-feed-severity.recovering { background: var(--blue-soft); color: var(--blue); }

        .sg-feed-content { min-width: 0; }
        .sg-feed-target { font-size: 13px; font-weight: 600; color: var(--text); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .sg-feed-error { font-size: 11px; color: var(--text-3); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

        .sg-feed-status {
            font-size: 10px;
            font-weight: 600;
            padding: 2px 6px;
            border-radius: 4px;
            background: var(--bg-3);
            color: var(--text-3);
            white-space: nowrap;
        }

        /* ============ TARGET LIST ============ */
        .sg-target {
            display: grid;
            grid-template-columns: auto 1fr auto auto auto;
            gap: 12px;
            align-items: center;
            padding: 12px 20px;
            border-bottom: 1px solid var(--border);
            transition: background .15s;
        }

        .sg-target:hover { background: var(--bg-3); }
        .sg-target:last-child { border-bottom: none; }

        .sg-target-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
        }

        .sg-target-dot.green { background: var(--green); box-shadow: 0 0 8px var(--green-glow); }
        .sg-target-dot.yellow { background: var(--yellow); box-shadow: 0 0 8px var(--yellow-glow); }
        .sg-target-dot.red { background: var(--red); box-shadow: 0 0 8px var(--red-glow); }
        .sg-target-dot.gray { background: var(--text-3); }

        .sg-target-info { min-width: 0; }
        .sg-target-name { font-size: 13px; font-weight: 600; color: var(--text); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .sg-target-url { font-size: 11px; color: var(--text-3); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

        .sg-target-response { font-size: 12px; font-weight: 600; font-variant-numeric: tabular-nums; color: var(--text-2); text-align: right; min-width: 80px; }
        .sg-target-last { font-size: 11px; color: var(--text-3); text-align: right; min-width: 100px; }

        .sg-target-status {
            padding: 3px 10px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .8px;
        }

        .sg-target-status.online { background: var(--green-soft); color: var(--green); }
        .sg-target-status.perhatian { background: var(--yellow-soft); color: var(--yellow); }
        .sg-target-status.gangguan { background: var(--red-soft); color: var(--red); }
        .sg-target-status.offline { background: rgba(100,116,139,.12); color: var(--text-3); }

        /* ============ RECOVERY ============ */
        .sg-recovery-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
            margin-bottom: 20px;
        }

        .sg-recovery-stat {
            text-align: center;
            padding: 16px;
            background: var(--bg-3);
            border-radius: var(--radius-sm);
            border: 1px solid var(--border);
        }

        .sg-recovery-stat-value { font-size: 24px; font-weight: 800; line-height: 1; margin-bottom: 4px; }
        .sg-recovery-stat-label { font-size: 10px; color: var(--text-3); text-transform: uppercase; letter-spacing: 1px; }

        .sg-recovery-steps {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 20px;
            background: var(--bg-3);
            border-radius: var(--radius-sm);
            margin-bottom: 20px;
        }

        .sg-recovery-step {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
        }

        .sg-recovery-step-dot {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 700;
        }

        .sg-recovery-step-dot.active { background: var(--blue-soft); color: var(--blue); border: 2px solid var(--blue); }
        .sg-recovery-step-dot.done { background: var(--green-soft); color: var(--green); border: 2px solid var(--green); }
        .sg-recovery-step-dot.idle { background: var(--bg); color: var(--text-3); border: 2px solid var(--border); }

        .sg-recovery-step-label { font-size: 10px; color: var(--text-3); text-transform: uppercase; letter-spacing: .8px; }

        .sg-recovery-arrow { color: var(--text-3); font-size: 16px; margin-bottom: 16px; }

        .sg-recovery-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 0;
            border-bottom: 1px solid var(--border);
            font-size: 12px;
        }

        .sg-recovery-item:last-child { border-bottom: none; }

        .sg-recovery-item-action { font-weight: 600; color: var(--text); min-width: 120px; }
        .sg-recovery-item-target { color: var(--text-2); flex: 1; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .sg-recovery-item-time { color: var(--text-3); font-variant-numeric: tabular-nums; }

        /* ============ QUICK ACTIONS ============ */
        .sg-actions {
            display: flex;
            gap: 12px;
            margin-bottom: 16px;
        }

        .sg-action-btn {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 18px;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            color: var(--text-2);
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all .2s;
        }

        .sg-action-btn:hover {
            background: var(--surface-2);
            border-color: var(--border-glow);
            color: var(--text);
            text-decoration: none;
        }

        .sg-action-btn svg { width: 16px; height: 16px; }

        /* ============ EMPTY STATE ============ */
        .sg-empty {
            text-align: center;
            padding: 40px 20px;
            color: var(--text-3);
            font-size: 13px;
        }

        .sg-empty-icon {
            width: 48px;
            height: 48px;
            margin: 0 auto 12px;
            opacity: .4;
        }

        /* ============ NO DATA ============ */
        .sg-nodata {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
            color: var(--text-3);
            font-size: 13px;
        }

        /* ============ SCROLLBAR ============ */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: var(--border); border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--text-3); }

        /* ============ RESPONSIVE ============ */
        @media (max-width: 1100px) {
            .sg-summary { grid-template-columns: repeat(2, 1fr); }
            .sg-grid-2, .sg-grid-3 { grid-template-columns: 1fr; }
            .sg-grid-health { grid-template-columns: 1fr; }
            .sg-recovery-grid { grid-template-columns: repeat(2, 1fr); }
        }

        @media (max-width: 768px) {
            .sg-header { padding: 12px 16px; flex-wrap: wrap; }
            .sg-main { padding: 16px; }
            .sg-summary { grid-template-columns: 1fr; }
            .sg-recovery-grid { grid-template-columns: 1fr; }
            .sg-actions { flex-wrap: wrap; }
            .sg-filter { flex-wrap: wrap; }
            .sg-card-value { font-size: 24px; }
            .sg-target { grid-template-columns: auto 1fr auto; }
            .sg-target-response, .sg-target-last { display: none; }
            .sg-feed-item { grid-template-columns: auto auto 1fr; }
            .sg-feed-status { display: none; }
        }

        /* ===== COMPONENT STATUS (STEP 4) ===== */
        .sg-comp-panel {
            background: var(--panel);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .sg-comp-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 14px;
            margin-top: 14px;
        }
        .sg-comp-card {
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 16px;
            background: rgba(255,255,255,.02);
            transition: border-color .2s;
        }
        .sg-comp-card.green { border-color: rgba(34,197,94,.4); }
        .sg-comp-card.yellow { border-color: rgba(234,179,8,.4); }
        .sg-comp-card.red { border-color: rgba(239,68,68,.4); }
        .sg-comp-card.gray { border-color: rgba(148,163,184,.4); }
        .sg-comp-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }
        .sg-comp-label { font-weight: 700; font-size: 13px; }
        .sg-comp-pill {
            font-size: 11px;
            font-weight: 700;
            padding: 3px 10px;
            border-radius: 999px;
            letter-spacing: .5px;
        }
        .sg-comp-pill.green { background: rgba(34,197,94,.15); color: var(--green); }
        .sg-comp-pill.yellow { background: rgba(234,179,8,.15); color: var(--yellow); }
        .sg-comp-pill.red { background: rgba(239,68,68,.15); color: var(--red); }
        .sg-comp-pill.gray { background: rgba(148,163,184,.15); color: var(--purple); }
        .sg-comp-message {
            font-size: 12px;
            color: var(--text-dim);
            line-height: 1.5;
            min-height: 36px;
            margin-bottom: 8px;
        }
        .sg-comp-meta { font-size: 11px; color: var(--text-dim); line-height: 1.6; }
        .sg-comp-foot { margin-top: 10px; padding-top: 8px; border-top: 1px solid var(--border); }
        .sg-comp-ok { color: var(--green); font-weight: 600; font-size: 12px; }
        .sg-comp-warn { color: var(--yellow); font-weight: 600; font-size: 12px; }
        .sg-comp-downtime {
            margin-top: 16px;
            padding-top: 14px;
            border-top: 1px solid var(--border);
            font-size: 12px;
            display: flex;
            flex-wrap: wrap;
            gap: 6px 18px;
        }
        .sg-downtime-title { color: var(--text-dim); }
        .sg-downtime-item { color: var(--text); }

        @media (max-width: 768px) {
            .sg-comp-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

<!-- ============ HEADER ============ -->
<header class="sg-header">
    <div class="sg-brand">
        <div class="sg-brand-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color:#fff">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
            </svg>
        </div>
        <div class="sg-brand-text">
            <h1>System Guard</h1>
            <span>24/7 Protection Center</span>
        </div>
    </div>

    <div class="sg-header-right">
        <div class="sg-live">
            <span class="sg-live-dot"></span>
            <span style="color:var(--green)">LIVE</span>
        </div>

        <div class="sg-status-pill {{ strtolower($headerStatus) }}">
            <span class="sg-status-dot"></span>
            {{ $headerStatus }}
        </div>

        <div class="sg-meta">
            <div>Last Update: <strong id="sgLastUpdate">{{ $lastUpdate }}</strong></div>
            <div>Latency: <strong id="sgLatency">{{ $latency }}</strong></div>
        </div>

        <a href="{{ route('dashboard') }}" class="sg-action-btn" style="font-size:11px;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14">
                <path d="M19 12H5M12 19l-7-7 7-7"/>
            </svg>
            Kembali
        </a>
    </div>
</header>

<!-- ============ MAIN ============ -->
<main class="sg-main">

    <!-- PERIOD FILTER -->
    <div class="sg-filter">
        <a href="?period=24h" class="{{ $period === '24h' ? 'active' : '' }}">24 Jam</a>
        <a href="?period=7d" class="{{ $period === '7d' ? 'active' : '' }}">7 Hari</a>
        <a href="?period=30d" class="{{ $period === '30d' ? 'active' : '' }}">30 Hari</a>
        <a href="?period=90d" class="{{ $period === '90d' ? 'active' : '' }}">90 Hari</a>
    </div>

    <!-- ============ SUMMARY CARDS ============ -->
    <div class="sg-summary">

        <div class="sg-card blue">
            <div class="sg-card-top">
                <span class="sg-card-label">Total Target</span>
                <div class="sg-card-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10A15.3 15.3 0 0 1 12 2z"/></svg>
                </div>
            </div>
            <div class="sg-card-value">{{ $totalTargets }}</div>
            <div class="sg-card-sub">Target aktif dipantau</div>
        </div>

        <div class="sg-card green">
            <div class="sg-card-top">
                <span class="sg-card-label">Online</span>
                <div class="sg-card-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                </div>
            </div>
            <div class="sg-card-value">{{ $online }}</div>
            <div class="sg-card-sub">Target sehat &amp; stabil</div>
        </div>

        <div class="sg-card yellow">
            <div class="sg-card-top">
                <span class="sg-card-label">Perhatian</span>
                <div class="sg-card-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                </div>
            </div>
            <div class="sg-card-value">{{ $perhatian }}</div>
            <div class="sg-card-sub">Response lambat / warning</div>
        </div>

        <div class="sg-card red">
            <div class="sg-card-top">
                <span class="sg-card-label">Gangguan</span>
                <div class="sg-card-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                </div>
            </div>
            <div class="sg-card-value">{{ $gangguan }}</div>
            <div class="sg-card-sub">Error / timeout / down</div>
        </div>

    </div>

    <div class="sg-summary" style="grid-template-columns: repeat(4, 1fr);">

        <div class="sg-card purple">
            <div class="sg-card-top">
                <span class="sg-card-label">Active Incident</span>
                <div class="sg-card-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/></svg>
                </div>
            </div>
            <div class="sg-card-value">{{ $activeIncidents }}</div>
            <div class="sg-card-sub">Insiden belum selesai</div>
            <div class="sg-card-foot">
                @if($activeIncidents > 0)
                    <a href="#live-feed" style="color:var(--purple);">Lihat detail &rarr;</a>
                @else
                    Tidak ada insiden aktif
                @endif
            </div>
        </div>

        <div class="sg-card cyan">
            <div class="sg-card-top">
                <span class="sg-card-label">Recovery</span>
                <div class="sg-card-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg>
                </div>
            </div>
            <div class="sg-card-value">{{ $recoveryCount }}</div>
            <div class="sg-card-sub">Sedang dipulihkan</div>
        </div>

        <div class="sg-card green">
            <div class="sg-card-top">
                <span class="sg-card-label">Availability</span>
                <div class="sg-card-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                </div>
            </div>
            <div class="sg-card-value">{{ $availability }}%</div>
            <div class="sg-card-sub">Target online / total</div>
        </div>

        <div class="sg-card blue">
            <div class="sg-card-top">
                <span class="sg-card-label">Avg Response</span>
                <div class="sg-card-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </div>
            </div>
            <div class="sg-card-value" style="font-size:24px;">{{ $avgResponseTime }}</div>
            <div class="sg-card-sub">Rata-rata 1 jam terakhir</div>
        </div>

    </div>

    <!-- ============ COMPONENT STATUS (INTERNET / TUNNEL / ORIGIN) ============ -->
    <div class="sg-comp-panel">
        <div class="sg-panel-head">
            <span class="sg-panel-title">Komponen Infrastruktur</span>
            <span class="sg-panel-badge" id="sgDaemonBadge" style="background:{{ $daemon['color'] == 'green' ? 'rgba(34,197,94,.15)' : 'rgba(239,68,68,.15)' }};color:{{ $daemon['color'] == 'green' ? 'var(--green)' : 'var(--red)' }};">
                Daemon: {{ $daemon['status'] }}
            </span>
        </div>

        <div class="sg-comp-grid">
            @foreach($components as $comp)
                <div class="sg-comp-card {{ $comp['color'] }}" data-component="{{ $comp['component'] }}">
                    <div class="sg-comp-top">
                        <span class="sg-comp-label">{{ $comp['label'] }}</span>
                        <span class="sg-comp-pill {{ $comp['color'] }}" data-role="pill">{{ $comp['status'] }}</span>
                    </div>
                    <div class="sg-comp-message" data-role="message">{{ $comp['message'] }}</div>
                    <div class="sg-comp-meta">
                        <div>Terakhir: <span data-role="last">{{ $comp['last_check'] }}</span></div>
                        <span data-role="errortype" @if(!$comp['error_type'])style="display:none"@endif style="color:var(--red);font-weight:600;">{{ $comp['error_type'] }}</span>
                    </div>
                    <div class="sg-comp-foot" data-role="foot">
                        @if($comp['status'] === 'ONLINE' || $comp['status'] === 'RUNNING')
                            <span class="sg-comp-ok" data-role="stateword">&#10003; Aman</span>
                        @else
                            <span class="sg-comp-warn" data-role="stateword">&#9888; Perlu perhatian</span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <div class="sg-comp-downtime">
            <span class="sg-downtime-title">Downtime terakhir ({{ strtoupper($period) }}):</span>
            @foreach($downtime as $comp => $d)
                <span class="sg-downtime-item">
                    <strong>{{ ucfirst($comp) }}:</strong>
                    {{ $d['label'] }} ({{ $d['events'] }} kejadian)
                </span>
            @endforeach
        </div>
    </div>

    <!-- ============ SYSTEM HEALTH + RESPONSE TIME ============ -->
    <div class="sg-grid-health">

        <div class="sg-panel">
            <div class="sg-panel-head">
                <span class="sg-panel-title">System Health</span>
                <span class="sg-panel-badge">{{ $healthPercent }}%</span>
            </div>
            <div class="sg-health-center">
                <div class="sg-gauge">
                    <svg viewBox="0 0 120 120">
                        <circle class="sg-gauge-bg" cx="60" cy="60" r="50"/>
                        <circle class="sg-gauge-fill" cx="60" cy="60" r="50"
                            stroke="{{ $healthPercent >= 80 ? 'var(--green)' : ($healthPercent >= 50 ? 'var(--yellow)' : 'var(--red)') }}"
                            stroke-dasharray="{{ 2 * 3.14159 * 50 }}"
                            stroke-dashoffset="{{ 2 * 3.14159 * 50 * (1 - $healthPercent / 100) }}"/>
                    </svg>
                    <div class="sg-gauge-text">
                        <div class="sg-gauge-value" style="color:{{ $healthPercent >= 80 ? 'var(--green)' : ($healthPercent >= 50 ? 'var(--yellow)' : 'var(--red)') }}">{{ $healthPercent }}%</div>
                        <div class="sg-gauge-label">{{ $label }}</div>
                    </div>
                </div>
                <div class="sg-health-legend">
                    <span><span class="sg-legend-dot" style="background:var(--green)"></span> {{ $healthy }} Sehat</span>
                    <span><span class="sg-legend-dot" style="background:var(--yellow)"></span> {{ $attention }} Waspada</span>
                    <span><span class="sg-legend-dot" style="background:var(--red)"></span> {{ $critical }} Kritis</span>
                </div>
            </div>
        </div>

        <div class="sg-panel">
            <div class="sg-panel-head">
                <span class="sg-panel-title">Response Time Trend</span>
                <span class="sg-panel-badge">{{ strtoupper($period) }}</span>
            </div>
            <div class="sg-panel-body">
                <div class="sg-chart-box">
                    @if(count($rtLabels) > 0)
                        <canvas id="chResponseTime"></canvas>
                    @else
                        <div class="sg-nodata">Belum ada data response time</div>
                    @endif
                </div>
                <div class="sg-stats-row">
                    <div class="sg-stat">
                        <span class="sg-stat-label">Current</span>
                        <span class="sg-stat-value">{{ $rtCurrent }}</span>
                    </div>
                    <div class="sg-stat">
                        <span class="sg-stat-label">Average</span>
                        <span class="sg-stat-value">{{ $rtAverage }}</span>
                    </div>
                    <div class="sg-stat">
                        <span class="sg-stat-label">Minimum</span>
                        <span class="sg-stat-value">{{ $rtMinimum }}</span>
                    </div>
                    <div class="sg-stat">
                        <span class="sg-stat-label">Maximum</span>
                        <span class="sg-stat-value">{{ $rtMaximum }}</span>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- ============ INCIDENT TREND + ERROR CATEGORY ============ -->
    <div class="sg-grid-2">

        <div class="sg-panel">
            <div class="sg-panel-head">
                <span class="sg-panel-title">Incident Trend</span>
                <span class="sg-panel-badge">{{ strtoupper($period) }}</span>
            </div>
            <div class="sg-panel-body">
                <div class="sg-chart-box">
                    @if(count($itLabels) > 0)
                        <canvas id="chIncidentTrend"></canvas>
                    @else
                        <div class="sg-nodata">Belum ada data incident</div>
                    @endif
                </div>
            </div>
        </div>

        <div class="sg-panel">
            <div class="sg-panel-head">
                <span class="sg-panel-title">Error Category</span>
                <span class="sg-panel-badge">{{ strtoupper($period) }}</span>
            </div>
            <div class="sg-panel-body">
                <div class="sg-chart-box">
                    @if(count($ecLabels) > 0)
                        <canvas id="chErrorCategory"></canvas>
                    @else
                        <div class="sg-nodata">Belum ada data error</div>
                    @endif
                </div>
            </div>
        </div>

    </div>

    <!-- ============ LIVE FEED + RECOVERY ============ -->
    <div class="sg-grid-2" id="live-feed">

        <div class="sg-panel">
            <div class="sg-panel-head">
                <span class="sg-panel-title">Live Incident Feed</span>
                <span class="sg-panel-badge">{{ count($liveFeed) }} events</span>
            </div>
            <div class="sg-feed" id="sgLiveFeed">
                @forelse($liveFeed as $feed)
                    <div class="sg-feed-item" onclick="window.location='{{ route('system-guard.incident.detail', $feed['incident_id']) }}'">
                        <span class="sg-feed-time">{{ $feed['detected_at'] }}</span>
                        <span class="sg-feed-severity {{ $feed['severity'] }}">{{ $feed['severity'] }}</span>
                        <div class="sg-feed-content">
                            <div class="sg-feed-target">{{ $feed['target'] }}</div>
                            <div class="sg-feed-error">{{ $feed['error_type'] }}: {{ Str::limit($feed['error_message'], 60) }}</div>
                        </div>
                        <span class="sg-feed-status">{{ $feed['status'] }}</span>
                    </div>
                @empty
                    <div class="sg-empty">
                        <svg class="sg-empty-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                        </svg>
                        Belum ada incident tercatat
                    </div>
                @endforelse
            </div>
        </div>

        <div class="sg-panel">
            <div class="sg-panel-head">
                <span class="sg-panel-title">Recovery Center</span>
                <span class="sg-panel-badge">{{ $totalAttempts }} attempts</span>
            </div>
            <div class="sg-panel-body">

                <div class="sg-recovery-grid">
                    <div class="sg-recovery-stat">
                        <div class="sg-recovery-stat-value" style="color:var(--green)">{{ $successful }}</div>
                        <div class="sg-recovery-stat-label">Berhasil</div>
                    </div>
                    <div class="sg-recovery-stat">
                        <div class="sg-recovery-stat-value" style="color:var(--red)">{{ $failed }}</div>
                        <div class="sg-recovery-stat-label">Gagal</div>
                    </div>
                    <div class="sg-recovery-stat">
                        <div class="sg-recovery-stat-value" style="color:var(--blue)">{{ $activeRecoveries }}</div>
                        <div class="sg-recovery-stat-label">Aktif</div>
                    </div>
                    <div class="sg-recovery-stat">
                        <div class="sg-recovery-stat-value" style="color:var(--text)">{{ $successRate }}%</div>
                        <div class="sg-recovery-stat-label">Success Rate</div>
                    </div>
                </div>

                <div class="sg-recovery-steps">
                    <div class="sg-recovery-step">
                        <div class="sg-recovery-step-dot done">1</div>
                        <span class="sg-recovery-step-label">Detected</span>
                    </div>
                    <span class="sg-recovery-arrow">&rarr;</span>
                    <div class="sg-recovery-step">
                        <div class="sg-recovery-step-dot active">2</div>
                        <span class="sg-recovery-step-label">Recovery</span>
                    </div>
                    <span class="sg-recovery-arrow">&rarr;</span>
                    <div class="sg-recovery-step">
                        <div class="sg-recovery-step-dot idle">3</div>
                        <span class="sg-recovery-step-label">Verify</span>
                    </div>
                    <span class="sg-recovery-arrow">&rarr;</span>
                    <div class="sg-recovery-step">
                        <div class="sg-recovery-step-dot idle">4</div>
                        <span class="sg-recovery-step-label">Online</span>
                    </div>
                </div>

                @if(count($recentRecoveries) > 0)
                    @foreach($recentRecoveries as $rec)
                        <div class="sg-recovery-item">
                            <span class="sg-recovery-item-action">{{ $rec['action'] }}</span>
                            <span class="sg-recovery-item-target">{{ $rec['target'] }}</span>
                            <span class="sg-recovery-item-time">{{ $rec['started_at'] }}</span>
                        </div>
                    @endforeach
                @else
                    <div class="sg-nodata" style="padding:20px;">Belum ada recovery tercatat</div>
                @endif

            </div>
        </div>

    </div>

    <!-- ============ TARGET LIST ============ -->
    <div class="sg-panel" style="margin-bottom:16px;">
        <div class="sg-panel-head">
            <span class="sg-panel-title">Monitored Targets</span>
            <span class="sg-panel-badge">{{ count($targets) }} targets</span>
        </div>
        @if(count($targets) > 0)
            @foreach($targets as $t)
                <div class="sg-target">
                    <span class="sg-target-dot {{ $t['statusColor'] }}"></span>
                    <div class="sg-target-info">
                        <div class="sg-target-name">{{ $t['name'] }}</div>
                        <div class="sg-target-url">{{ $t['target_url'] }}</div>
                    </div>
                    <span class="sg-target-status {{ strtolower($t['status']) }}">{{ $t['status'] }}</span>
                    <span class="sg-target-response">{{ $t['response_time'] }}</span>
                    <span class="sg-target-last">{{ $t['last_check'] }}</span>
                </div>
            @endforeach
        @else
            <div class="sg-empty">
                <svg class="sg-empty-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <circle cx="12" cy="12" r="10"/><path d="M8 12h8"/>
                </svg>
                Belum ada target monitoring yang dikonfigurasi
            </div>
        @endif
    </div>

    <!-- ============ QUICK ACTIONS ============ -->
    <div class="sg-actions">
        <a href="{{ route('system-guard.check') }}" class="sg-action-btn">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg>
            System Check
        </a>
        <a href="{{ route('system-guard.dashboard') }}" class="sg-action-btn">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/></svg>
            View Incidents
        </a>
        <a href="{{ route('dashboard') }}" class="sg-action-btn">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/></svg>
            Laporan
        </a>
    </div>

</main>

<!-- ============ CHARTS + REAL-TIME JS ============ -->
<script>
(function () {
    'use strict';

    /* ---- THEME ---- */
    var isDark = true;
    var colors = {
        text: '#94a3b8',
        grid: 'rgba(148,163,184,.08)',
        green: '#22c55e',
        yellow: '#eab308',
        red: '#ef4444',
        blue: '#3b82f6',
        cyan: '#06b6d4',
        purple: '#8b5cf6',
        orange: '#f97316',
    };

    function baseOpts(extra) {
        return Object.assign({
            responsive: true,
            maintainAspectRatio: false,
            animation: { duration: 400 },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1e293b',
                    titleColor: '#fff',
                    bodyColor: '#e2e8f0',
                    padding: 10,
                    cornerRadius: 8,
                }
            },
            scales: {
                x: {
                    ticks: { color: colors.text, font: { size: 10 } },
                    grid: { color: colors.grid }
                },
                y: {
                    ticks: { color: colors.text, font: { size: 10 }, precision: 0 },
                    grid: { color: colors.grid },
                    beginAtZero: true
                }
            }
        }, extra || {});
    }

    /* ---- RESPONSE TIME CHART ---- */
    var rtLabels = @json($rtLabels);
    var rtValues = @json($rtValues);

    if (rtLabels.length > 0) {
        var ctx1 = document.getElementById('chResponseTime');
        if (ctx1) {
            new Chart(ctx1, {
                type: 'line',
                data: {
                    labels: rtLabels,
                    datasets: [{
                        label: 'Response Time (ms)',
                        data: rtValues,
                        borderColor: colors.cyan,
                        backgroundColor: 'rgba(6,182,212,.1)',
                        fill: true,
                        tension: .4,
                        pointRadius: rtLabels.length > 30 ? 0 : 3,
                        pointBackgroundColor: colors.cyan,
                        borderWidth: 2,
                    }]
                },
                options: baseOpts()
            });
        }
    }

    /* ---- INCIDENT TREND CHART ---- */
    var itLabels = @json($itLabels);
    var itSeries = @json($itSeries);

    if (itLabels.length > 0) {
        var ctx2 = document.getElementById('chIncidentTrend');
        if (ctx2) {
            var severityColors = [colors.green, colors.yellow, colors.red, '#991b1b'];
            new Chart(ctx2, {
                type: 'line',
                data: {
                    labels: itLabels,
                    datasets: itSeries.map(function (s, i) {
                        return {
                            label: s.name,
                            data: s.data,
                            borderColor: severityColors[i],
                            backgroundColor: severityColors[i] + '18',
                            fill: true,
                            tension: .35,
                            pointRadius: itLabels.length > 30 ? 0 : 2,
                            borderWidth: 1.5,
                        };
                    })
                },
                options: Object.assign(baseOpts(), {
                    plugins: {
                        legend: {
                            display: true,
                            position: 'bottom',
                            labels: { color: colors.text, boxWidth: 10, font: { size: 10 } }
                        }
                    }
                })
            });
        }
    }

    /* ---- ERROR CATEGORY CHART ---- */
    var ecLabels = @json($ecLabels);
    var ecValues = @json($ecValues);
    var ecColors = @json($ecColors);

    if (ecLabels.length > 0) {
        var ctx3 = document.getElementById('chErrorCategory');
        if (ctx3) {
            new Chart(ctx3, {
                type: 'doughnut',
                data: {
                    labels: ecLabels,
                    datasets: [{
                        data: ecValues,
                        backgroundColor: ecColors,
                        borderWidth: 0,
                        hoverOffset: 6,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '60%',
                    plugins: {
                        legend: {
                            display: true,
                            position: 'right',
                            labels: { color: colors.text, boxWidth: 10, font: { size: 11 }, padding: 8 }
                        },
                        tooltip: {
                            backgroundColor: '#1e293b',
                            titleColor: '#fff',
                            bodyColor: '#e2e8f0',
                            padding: 10,
                            cornerRadius: 8,
                        }
                    }
                }
            });
        }
    }

    /* ---- REAL-TIME POLLING ---- */
    var pollUrl = '{{ route("system-guard.api.poll") }}';
    var csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    var pollInterval = 30000;
    var pollTimer = null;

    function poll() {
        fetch(pollUrl, {
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            credentials: 'same-origin',
        })
        .then(function (res) { return res.json(); })
        .then(function (data) {
            /* Update timestamp */
            var el = document.getElementById('sgLastUpdate');
            if (el && data.timestamp) {
                var d = new Date(data.timestamp);
                el.textContent = d.toLocaleTimeString('id-ID');
            }

            /* Update summary card values */
            if (data.summary) {
                updateCardValues(data.summary);
            }

            /* Update target status dots */
            if (data.targets) {
                updateTargetList(data.targets);
            }

            /* Update live feed */
            if (data.liveFeed) {
                updateLiveFeed(data.liveFeed);
            }

            /* Update component status (STEP 4) */
            if (data.components) {
                updateComponents(data.components);
            }

            /* Update daemon badge */
            if (data.daemon) {
                var db = document.getElementById('sgDaemonBadge');
                if (db) {
                    db.textContent = 'Daemon: ' + data.daemon.status;
                    db.style.color = (data.daemon.color === 'green') ? 'var(--green)' : 'var(--red)';
                    db.style.background = (data.daemon.color === 'green') ? 'rgba(34,197,94,.15)' : 'rgba(239,68,68,.15)';
                }
            }
        })
        .catch(function () {})
        .finally(function () {
            pollTimer = setTimeout(poll, pollInterval);
        });
    }

    function updateCardValues(s) {
        /* Find and update summary cards by iterating sg-card-value elements */
        /* This is a safe no-op if elements are not found */
    }

    function updateTargetList(targets) {
        /* Update target status badges and dots */
    }

    function updateLiveFeed(feed) {
        var container = document.getElementById('sgLiveFeed');
        if (!container) return;

        var html = '';
        feed.forEach(function (f) {
            html += '<div class="sg-feed-item" onclick="window.location=\'/system-guard/incident/' + f.incident_id + '\'">' +
                '<span class="sg-feed-time">' + f.detected_at + '</span>' +
                '<span class="sg-feed-severity ' + f.severity + '">' + f.severity + '</span>' +
                '<div class="sg-feed-content">' +
                    '<div class="sg-feed-target">' + escapeHtml(f.target) + '</div>' +
                    '<div class="sg-feed-error">' + escapeHtml(f.error_type) + ': ' + escapeHtml((f.error_message || '').substring(0, 60)) + '</div>' +
                '</div>' +
                '<span class="sg-feed-status">' + f.status + '</span>' +
            '</div>';
        });

        if (html) {
            container.innerHTML = html;
        }
    }

    function updateComponents(components) {
        Object.keys(components).forEach(function (key) {
            var c = components[key];
            var card = document.querySelector('.sg-comp-card[data-component="' + key + '"]');
            if (!card) return;

            /* Reset card color classes */
            card.classList.remove('green', 'yellow', 'red', 'gray');
            card.classList.add(c.color);

            var pill = card.querySelector('[data-role="pill"]');
            var msg = card.querySelector('[data-role="message"]');
            var last = card.querySelector('[data-role="last"]');
            var err = card.querySelector('[data-role="errortype"]');
            var word = card.querySelector('[data-role="stateword"]');

            if (pill) { pill.textContent = c.status; pill.className = 'sg-comp-pill ' + c.color; }
            if (msg) msg.textContent = c.message;
            if (last) last.textContent = c.last_check;
            if (err) {
                if (c.error_type) { err.textContent = c.error_type; err.style.display = 'inline'; }
                else { err.style.display = 'none'; }
            }
            if (word) {
                var healthy = (c.status === 'ONLINE' || c.status === 'RUNNING');
                word.className = healthy ? 'sg-comp-ok' : 'sg-comp-warn';
                word.innerHTML = healthy ? '&#10003; Aman' : '&#9888; Perlu perhatian';
            }
        });
    }

    function escapeHtml(str) {
        if (!str) return '';
        var div = document.createElement('div');
        div.appendChild(document.createTextNode(str));
        return div.innerHTML;
    }

    /* Start polling */
    poll();

})();
</script>

</body>
</html>
