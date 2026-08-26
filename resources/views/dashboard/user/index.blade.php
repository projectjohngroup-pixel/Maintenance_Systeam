@extends('layouts.app')

@section('title', 'Dashboard User')

@section('content')

<style>
    .user-dash { width:100%; }

    .welcome-card {
        background:var(--surface); border:1px solid var(--border);
        border-radius:14px; padding:22px 24px;
        box-shadow:var(--shadow-sm); margin-bottom:20px;
    }
    .welcome-card h2 { font-size:19px; margin-bottom:6px; }
    .welcome-card p { color:var(--muted); font-size:13px; }

    .section-title {
        font-size:11px; font-weight:800; letter-spacing:1.8px;
        color:var(--muted); text-transform:uppercase; margin:22px 0 12px;
    }

    .kpi-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(170px,1fr)); gap:12px; }
    .kpi-card {
        background:var(--surface); border:1px solid var(--border);
        border-radius:14px; padding:16px; box-shadow:var(--shadow-sm);
        position:relative; overflow:hidden;
    }
    .kpi-card::before {
        content:''; position:absolute; left:0; top:0; bottom:0;
        width:3px; background:var(--kc, var(--primary));
    }
    .kpi-title { font-size:10.5px; font-weight:700; letter-spacing:1px; text-transform:uppercase; color:var(--muted); }
    .kpi-value { font-size:27px; font-weight:800; margin-top:7px; letter-spacing:-.5px; }
    .kpi-foot { font-size:11px; color:var(--muted); margin-top:4px; }

    .table-card {
        background:var(--surface); border:1px solid var(--border);
        border-radius:14px; box-shadow:var(--shadow-sm); overflow:hidden;
    }
    .table-head { padding:15px 18px; border-bottom:1px solid var(--border); display:flex; justify-content:space-between; align-items:center; gap:10px; flex-wrap:wrap; }
    .table-head h3 { font-size:13.5px; }
    .table-head p { font-size:11.5px; color:var(--muted); margin-top:2px; }
    .btn-primary {
        display:inline-flex; align-items:center; gap:6px;
        height:36px; padding:0 16px; border-radius:9px; border:none;
        background:var(--primary); color:#fff; font-size:12.5px; font-weight:600; cursor:pointer;
    }
    .btn-primary:hover { background:var(--primary-dark); }
    .table-scroll { overflow-x:auto; }
    table.wo-table { width:100%; border-collapse:collapse; min-width:640px; }
    .wo-table th {
        text-align:left; padding:11px 14px;
        font-size:10.5px; font-weight:800; letter-spacing:1.2px; text-transform:uppercase;
        color:var(--muted); background:var(--surface-soft);
        border-bottom:1px solid var(--border); white-space:nowrap;
    }
    .wo-table td {
        padding:12px 14px; font-size:13px; color:var(--text-2,var(--text));
        border-bottom:1px solid var(--border); white-space:nowrap;
    }
    .wo-table tr:last-child td { border-bottom:none; }
    .empty-note { color:var(--muted); font-size:12.5px; padding:34px 0; text-align:center; }

    .badge {
        display:inline-flex; align-items:center; gap:5px;
        padding:3px 10px; border-radius:999px; font-size:11px; font-weight:700;
    }
    .badge.b-open      { background:rgba(37,99,235,.12);  color:#2563eb; }
    .badge.b-diterima  { background:rgba(8,145,178,.12);  color:#0891b2; }
    .badge.b-scheduled { background:rgba(124,58,237,.12); color:#7c3aed; }
    .badge.b-progress  { background:rgba(217,119,6,.14);  color:#d97706; }
    .badge.b-pending   { background:rgba(245,158,11,.16); color:#b45309; }
    .badge.b-service   { background:rgba(219,39,119,.12); color:#db2777; }
    .badge.b-ditolak   { background:rgba(185,28,28,.12);  color:#b91c1c; }
    .badge.b-close     { background:rgba(22,163,74,.12);  color:#16a34a; }

    .info-card {
        background:var(--surface); border:1px solid var(--border);
        border-radius:14px; padding:20px; box-shadow:var(--shadow-sm);
    }
    .info-card h3 { font-size:14px; margin-bottom:10px; }
    .info-list { list-style:none; }
    .info-list li {
        display:flex; justify-content:space-between; gap:12px;
        padding:9px 0; border-bottom:1px dashed var(--border); font-size:12.5px;
    }
    .info-list li:last-child { border-bottom:none; }
    .info-list span:first-child { color:var(--muted); }
</style>


<div class="user-dash">

    <div class="welcome-card">
        <h2>Selamat Datang, {{ auth()->user()->name }}</h2>
        <p>Anda login sebagai <strong>{{ auth()->user()->bagian }}</strong>. Berikut ringkasan Work Order yang Anda buat.</p>
    </div>

    <div class="kpi-grid">

        <div class="kpi-card" style="--kc:#2563eb;">
            <div class="kpi-title">Total WO Saya</div>
            <div class="kpi-value">{{ number_format($myTotal) }}</div>
            <div class="kpi-foot">Seluruh work order dibuat</div>
        </div>

        <div class="kpi-card" style="--kc:#d97706;">
            <div class="kpi-title">Belum Selesai</div>
            <div class="kpi-value">{{ number_format($myOpen) }}</div>
            <div class="kpi-foot">Sedang diproses maintenance</div>
        </div>

        <div class="kpi-card" style="--kc:#16a34a;">
            <div class="kpi-title">Selesai</div>
            <div class="kpi-value">{{ number_format($mySelesai) }}</div>
            <div class="kpi-foot">Status close / selesai</div>
        </div>

    </div>

    <div class="section-title">Work Order Terakhir</div>

    <div class="table-card">

        <div class="table-head">
            <div>
                <h3>Work Order Saya</h3>
                <p>8 permintaan terakhir yang Anda buat</p>
            </div>
            <a href="{{ route('work-orders.create') }}" class="btn-primary">+ Buat WO</a>
        </div>

        <div class="table-scroll">
            <table class="wo-table">
                <thead>
                    <tr>
                        <th>No. WO</th>
                        <th>Kategori</th>
                        <th>Pekerjaan</th>
                        <th>Prioritas</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($myRecent as $wo)
                        <?php
                            $stClass = [
                                'open' => 'b-open', 'diterima' => 'b-diterima',
                                'scheduled' => 'b-scheduled', 'in progress' => 'b-progress',
                                'pending' => 'b-pending', 'service luar' => 'b-service',
                                'ditolak' => 'b-ditolak', 'close' => 'b-close',
                            ];
                            $stKey = strtolower(trim((string) $wo->status));
                            $stCls = $stClass[$stKey] ?? 'b-open';
                            $prio = strtoupper(trim((string) ($wo->priority ?: '-')));
                        ?>
                        <tr>
                            <td style="font-weight:600;">{{ $wo->no_wo ?? ('#' . $wo->id) }}</td>
                            <td>{{ strtoupper($wo->kategori ?? '-') }}</td>
                            <td style="max-width:260px; white-space:normal;" title="{{ $wo->job }}">{{ \Illuminate\Support\Str::limit($wo->job ?? '-', 46) }}</td>
                            <td>{{ $prio }}</td>
                            <td>
                                {{ $wo->tanggal_kerusakan
                                    ? \Carbon\Carbon::parse($wo->tanggal_kerusakan)->format('d M Y')
                                    : '-' }}
                            </td>
                            <td><span class="badge {{ $stCls }}">{{ strtoupper($wo->status ?? '-') }}</span></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="empty-note">
                                    Belum ada Work Order &mdash; klik <strong>+ Buat WO</strong> untuk membuat permintaan pertama.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

    <div class="section-title">Informasi</div>

    <div class="info-card">
        <h3>Panduan Status Work Order</h3>
        <ul class="info-list">
            <li><span>OPEN</span> Laporan baru dibuat, menunggu diterima maintenance.</li>
            <li><span>DITERIMA</span> Laporan diterima untuk ditindaklanjuti.</li>
            <li><span>DITOLAK</span> Laporan tidak dapat ditindaklanjuti.</li>
            <li><span>SCHEDULED</span> Dijadwalkan pada waktu tertentu.</li>
            <li><span>IN PROGRESS</span> Sedang dikerjakan teknisi.</li>
            <li><span>PENDING</span> Menunggu sparepart / kondisi tertentu.</li>
            <li><span>SERVICE LUAR</span> Ditangani vendor eksternal.</li>
            <li><span>CLOSE</span> Pekerjaan selesai.</li>
        </ul>
    </div>

</div>

@endsection
