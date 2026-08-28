<!DOCTYPE html>
<html lang="id" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Incident {{ $incident->incident_id }} — System Guard</title>
    <style>
        :root {
            --bg: #0a0e1a;
            --bg-2: #0f1424;
            --bg-3: #141b2d;
            --surface: #1a2236;
            --surface-2: #1e2841;
            --border: #253050;
            --text: #e2e8f0;
            --text-2: #94a3b8;
            --text-3: #64748b;
            --green: #22c55e;
            --green-soft: rgba(34,197,94,.12);
            --yellow: #eab308;
            --yellow-soft: rgba(234,179,8,.12);
            --red: #ef4444;
            --red-soft: rgba(239,68,68,.12);
            --blue: #3b82f6;
            --blue-soft: rgba(59,130,246,.12);
            --radius: 12px;
            --radius-sm: 8px;
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

        .sg-header {
            background: var(--bg-2);
            border-bottom: 1px solid var(--border);
            padding: 16px 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .sg-header h1 { font-size: 16px; font-weight: 700; }

        .sg-main { max-width: 960px; margin: 0 auto; padding: 24px 28px 60px; }

        .sg-panel {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            overflow: hidden;
            margin-bottom: 16px;
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

        .sg-panel-body { padding: 20px; }

        .sg-badge {
            padding: 3px 10px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .8px;
        }

        .sg-badge.normal { background: var(--green-soft); color: var(--green); }
        .sg-badge.perhatian { background: var(--yellow-soft); color: var(--yellow); }
        .sg-badge.gangguan { background: var(--red-soft); color: var(--red); }
        .sg-badge.serius { background: var(--red-soft); color: var(--red); }
        .sg-badge.open { background: var(--red-soft); color: var(--red); }
        .sg-badge.resolved { background: var(--green-soft); color: var(--green); }
        .sg-badge.recovering { background: var(--blue-soft); color: var(--blue); }
        .sg-badge.failed { background: var(--red-soft); color: var(--red); }
        .sg-badge.escalated { background: var(--yellow-soft); color: var(--yellow); }

        .sg-detail-grid {
            display: grid;
            grid-template-columns: 140px 1fr;
            gap: 12px 16px;
        }

        .sg-detail-label {
            font-size: 11px;
            font-weight: 700;
            color: var(--text-3);
            text-transform: uppercase;
            letter-spacing: 1px;
            padding-top: 4px;
        }

        .sg-detail-value {
            font-size: 14px;
            color: var(--text);
            word-break: break-word;
        }

        .sg-detail-value pre {
            background: var(--bg-3);
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            padding: 12px;
            font-size: 12px;
            font-family: 'Cascadia Code', 'Fira Code', monospace;
            overflow-x: auto;
            white-space: pre-wrap;
            word-break: break-word;
        }

        .sg-timeline {
            padding: 0;
            list-style: none;
        }

        .sg-timeline-item {
            display: flex;
            gap: 16px;
            padding: 12px 0;
            border-bottom: 1px solid var(--border);
        }

        .sg-timeline-item:last-child { border-bottom: none; }

        .sg-timeline-time {
            font-size: 12px;
            color: var(--text-3);
            min-width: 80px;
            font-variant-numeric: tabular-nums;
        }

        .sg-timeline-action { font-weight: 600; color: var(--text); min-width: 100px; }
        .sg-timeline-msg { font-size: 12px; color: var(--text-2); }
        .sg-timeline-status { font-size: 11px; color: var(--text-3); }

        .sg-action-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
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
            border-color: var(--blue);
            color: var(--text);
            text-decoration: none;
        }

        @media (max-width: 600px) {
            .sg-detail-grid { grid-template-columns: 1fr; }
            .sg-main { padding: 16px; }
        }
    </style>
</head>
<body>

<header class="sg-header">
    <h1>Incident {{ $incident->incident_id }}</h1>
    <div style="display:flex; gap:10px; align-items:center;">
        <span class="sg-badge {{ $incident->severity }}">{{ $incident->severity }}</span>
        <a href="{{ route('system-guard.dashboard') }}" class="sg-action-btn">Kembali</a>
    </div>
</header>

<main class="sg-main">

    <div class="sg-panel">
        <div class="sg-panel-head">
            <span class="sg-panel-title">Incident Detail</span>
            <span class="sg-badge {{ $incident->status }}">{{ $incident->status }}</span>
        </div>
        <div class="sg-panel-body">
            <div class="sg-detail-grid">
                <span class="sg-detail-label">Incident ID</span>
                <span class="sg-detail-value">{{ $incident->incident_id }}</span>

                <span class="sg-detail-label">Target</span>
                <span class="sg-detail-value">{{ $incident->monitorConfig?->name ?? $incident->target }}</span>

                <span class="sg-detail-label">URL</span>
                <span class="sg-detail-value">
                    <a href="{{ $incident->target }}" target="_blank">{{ $incident->target }}</a>
                </span>

                <span class="sg-detail-label">Error Category</span>
                <span class="sg-detail-value">{{ $incident->error_category }}</span>

                <span class="sg-detail-label">Error Type</span>
                <span class="sg-detail-value">{{ $incident->error_type }}</span>

                <span class="sg-detail-label">Error Message</span>
                <span class="sg-detail-value"><pre>{{ $incident->error_message }}</pre></span>

                <span class="sg-detail-label">Severity</span>
                <span class="sg-detail-value"><span class="sg-badge {{ $incident->severity }}">{{ strtoupper($incident->severity) }}</span></span>

                <span class="sg-detail-label">Status</span>
                <span class="sg-detail-value"><span class="sg-badge {{ $incident->status }}">{{ $incident->status }}</span></span>

                <span class="sg-detail-label">Detected At</span>
                <span class="sg-detail-value">{{ $incident->detected_at->format('d M Y H:i:s') }}</span>

                @if($incident->recovered_at)
                    <span class="sg-detail-label">Recovered At</span>
                    <span class="sg-detail-value">{{ $incident->recovered_at->format('d M Y H:i:s') }}</span>
                @endif

                @if($incident->duration_seconds)
                    <span class="sg-detail-label">Duration</span>
                    <span class="sg-detail-value">{{ round($incident->duration_seconds / 60, 1) }} menit</span>
                @endif

                <span class="sg-detail-label">Retry Count</span>
                <span class="sg-detail-value">{{ $incident->retry_count }}</span>

                @if($incident->recovery_summary)
                    <span class="sg-detail-label">Recovery Summary</span>
                    <span class="sg-detail-value"><pre>{{ json_encode($incident->recovery_summary, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre></span>
                @endif
            </div>
        </div>
    </div>

    @if($recoveryLogs->count() > 0)
    <div class="sg-panel">
        <div class="sg-panel-head">
            <span class="sg-panel-title">Recovery Timeline</span>
            <span class="sg-panel-badge">{{ $recoveryLogs->count() }} actions</span>
        </div>
        <div class="sg-panel-body">
            <ul class="sg-timeline">
                @foreach($recoveryLogs as $log)
                    <li class="sg-timeline-item">
                        <span class="sg-timeline-time">{{ $log->started_at->format('H:i:s') }}</span>
                        <span class="sg-timeline-action">{{ $log->action }}</span>
                        <span class="sg-timeline-msg">
                            {{ $log->result_message }}
                            @if($log->verification_passed !== null)
                                — {{ $log->verification_passed ? 'Verified OK' : 'Verification Failed' }}
                            @endif
                        </span>
                        <span class="sg-timeline-status">{{ $log->status }} @if($log->duration_ms)({{ $log->duration_ms }}ms)@endif</span>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif

</main>

</body>
</html>
