@extends('layouts.app')

@section('title', 'Master Mesin')

@section('page_title', 'Data Mesin')

@section('page_subtitle', 'Kelola daftar mesin per area')

@section('content')

<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(170px,1fr));gap:12px;margin-bottom:20px;">
    <div class="card" style="padding:14px 18px;">
        <div style="font-size:11.5px;color:var(--pds-muted);text-transform:uppercase;letter-spacing:.06em;">Total Mesin</div>
        <div style="font-size:26px;font-weight:700;margin-top:2px;">{{ $totalMesin }}</div>
    </div>
    <div class="card" style="padding:14px 18px;">
        <div style="font-size:11.5px;color:var(--pds-muted);text-transform:uppercase;letter-spacing:.06em;">Aktif</div>
        <div style="font-size:26px;font-weight:700;color:#059669;margin-top:2px;">{{ $totalAktif }}</div>
    </div>
    <div class="card" style="padding:14px 18px;">
        <div style="font-size:11.5px;color:var(--pds-muted);text-transform:uppercase;letter-spacing:.06em;">Tidak Aktif</div>
        <div style="font-size:26px;font-weight:700;color:#dc2626;margin-top:2px;">{{ $totalTidakAktif }}</div>
    </div>
    <div class="card" style="padding:14px 18px;">
        <div style="font-size:11.5px;color:var(--pds-muted);text-transform:uppercase;letter-spacing:.06em;">Total KW</div>
        <div style="font-size:26px;font-weight:700;margin-top:2px;">{{ pdsNumber((float) $totalKw) }}</div>
    </div>
</div>

<div class="card">

    {{-- ================================================
         HEADER TABEL: SEARCH + FILTER + TAMBAH
    ================================================= --}}

    <div style="display:flex;flex-wrap:wrap;gap:10px;align-items:center;margin-bottom:16px;">

        <form
            method="GET"
            action="{{ route('machines.index') }}"
            style="display:flex;gap:8px;flex:1 1 260px;min-width:220px;"
        >
            <div style="position:relative;flex:1;">
                <x-icon name="search"
                    style="position:absolute;left:11px;top:50%;transform:translateY(-50%);font-size:14px;color:var(--pds-muted);" />
                <input
                    type="text"
                    name="q"
                    value="{{ request('q') }}"
                    placeholder="Cari kode / nama mesin..."
                    style="width:100%;padding:9px 12px 9px 34px;border:1px solid var(--pds-line-2);border-radius:10px;background:var(--pds-card);color:var(--pds-ink);font-size:13px;"
                >
            </div>
            <button type="submit" class="btn btn-primary" title="Cari">Cari</button>
        </form>

        <select
            name="area_id"
            onchange="window.location='{{ route('machines.index') }}?q={{ request('q') }}&status={{ request('status') }}&area_id='+this.value;"
            style="padding:9px 12px;border:1px solid var(--pds-line-2);border-radius:10px;background:var(--pds-card);color:var(--pds-ink);font-size:13px;"
            title="Filter Area"
        >
            <option value="">Semua Area</option>
            @foreach ($allAreas as $area)
                <option value="{{ $area->id }}" {{ request('area_id') == $area->id ? 'selected' : '' }}>
                    {{ strtoupper($area->nama_area) }}
                </option>
            @endforeach
        </select>

        <select
            onchange="window.location='{{ route('machines.index') }}?q={{ request('q') }}&area_id={{ request('area_id') }}&status='+this.value;"
            style="padding:9px 12px;border:1px solid var(--pds-line-2);border-radius:10px;background:var(--pds-card);color:var(--pds-ink);font-size:13px;"
            title="Filter Status"
        >
            <option value="">Semua Status</option>
            <option value="Aktif" {{ request('status') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
            <option value="Tidak Aktif" {{ request('status') == 'Tidak Aktif' ? 'selected' : '' }}>Tidak Aktif</option>
        </select>

        <button
            type="button"
            class="btn-add-master"
            onclick="openMesinModal()"
        >
            <x-icon name="plus" /> Tambah Mesin
        </button>

    </div>

    @if (request()->filled('q') || request()->filled('area_id') || request()->filled('status'))
        <a href="{{ route('machines.index') }}" style="display:inline-flex;align-items:center;gap:6px;font-size:12.5px;color:var(--pds-muted);text-decoration:none;margin-bottom:12px;">
            <x-icon name="close" /> Reset filter
        </a>
    @endif

    {{-- ================================================
         TABEL MESIN
    ================================================= --}}

    <div class="pch-scroll">
        <table class="table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode</th>
                    <th>Nama Mesin</th>
                    <th>Area</th>
                    <th>KW</th>
                    <th>Status</th>
                    <th style="width:130px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($mesins as $mesin)
                    @php
                        $mesinPayload = [
                            'id' => $mesin->id,
                            'kode' => $mesin->kode_mesin,
                            'nama' => $mesin->nama_mesin,
                            'area_id' => $mesin->area_id,
                            'area' => $mesin->area ? strtoupper($mesin->area->nama_area) : '-',
                            'kw' => $mesin->kw,
                            'status' => $mesin->status,
                            'spesifikasi' => $mesin->spesifikasi,
                        ];
                    @endphp
                    <tr data-mesin='@json($mesinPayload)'>
                        <td>{{ $mesins->firstItem() + $loop->index }}</td>
                        <td><b>{{ $mesin->kode_mesin }}</b></td>
                        <td>{{ $mesin->nama_mesin }}</td>
                        <td>{{ $mesin->area ? strtoupper($mesin->area->nama_area) : '-' }}</td>
                        <td>{{ $mesin->kw !== null ? pdsNumber((float) $mesin->kw) : '-' }}</td>
                        <td>
                            <span class="badge {{ strtolower($mesin->status) === 'aktif' ? 'badge-success' : 'badge-danger' }}">
                                {{ $mesin->status }}
                            </span>
                        </td>
                        <td>
                            <div class="act-group">
                                <button
                                    type="button"
                                    class="act-btn act-view"
                                    title="Lihat Detail Mesin"
                                    aria-label="Lihat Detail Mesin"
                                    onclick="viewMesin(this.closest('tr').dataset.mesin)"
                                >
                                    <x-icon name="eye" />
                                </button>

                                <button
                                    type="button"
                                    class="act-btn act-edit"
                                    title="Edit Mesin"
                                    aria-label="Edit Mesin"
                                    onclick="editMesin(this.closest('tr').dataset.mesin)"
                                >
                                    <x-icon name="edit" />
                                </button>

                                <form
                                    action="{{ route('machines.destroy', $mesin) }}"
                                    method="POST"
                                    data-confirm="Hapus mesin {{ $mesin->kode_mesin }} - {{ $mesin->nama_mesin }}?"
                                    style="display:inline;"
                                >
                                    @csrf
                                    @method('DELETE')
                                    <button
                                        type="submit"
                                        class="act-btn act-delete"
                                        title="Hapus Mesin"
                                        aria-label="Hapus Mesin"
                                    >
                                        <x-icon name="trash" />
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align:center;color:var(--pds-muted);padding:28px;">
                            Belum ada data mesin.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($mesins instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator && $mesins->hasPages())
        <div style="margin-top:16px;">{{ $mesins->links() }}</div>
    @endif

</div>


{{-- ====================================================
     MODAL FORM MESIN (TAMBAH / EDIT)
===================================================== --}}

<div id="mesinModalOverlay" class="pch-modal-overlay" aria-hidden="true">
    <div class="pch-modal" role="dialog" aria-modal="true" style="max-width:640px;width:min(640px,calc(100vw - 24px));">

        <div style="display:flex;align-items:flex-start;justify-content:space-between;padding:16px 18px 10px;">
            <div>
                <h2 id="mesinModalTitle" style="margin:0;font-size:15.5px;">Tambah Mesin</h2>
                <p style="margin:2px 0 0;font-size:12px;color:var(--pds-muted);">Kode mesin harus unik.</p>
            </div>
            <button type="button" class="act-btn" title="Tutup" aria-label="Tutup" onclick="closeMesinModal()">
                <x-icon name="close" />
            </button>
        </div>

        <form id="mesinForm" method="POST" onsubmit="return submitMesinForm(this);">
            @csrf
            <div class="pch-modal-body" style="padding:4px 18px 14px;">
                <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:12px;">

                    <div>
                        <label style="display:block;font-size:12px;color:var(--pds-ink-2);margin-bottom:5px;">Kode Mesin *</label>
                        <input type="text" name="kode_mesin" id="mf_kode" required maxlength="255" placeholder="cth: MC-001" style="width:100%;padding:9px 12px;border:1px solid var(--pds-line-2);border-radius:10px;background:var(--pds-card);color:var(--pds-ink);">
                    </div>

                    <div>
                        <label style="display:block;font-size:12px;color:var(--pds-ink-2);margin-bottom:5px;">Nama Mesin *</label>
                        <input type="text" name="nama_mesin" id="mf_nama" required maxlength="255" placeholder="cth: Injection Molding 1" style="width:100%;padding:9px 12px;border:1px solid var(--pds-line-2);border-radius:10px;background:var(--pds-card);color:var(--pds-ink);">
                    </div>

                    <div>
                        <label style="display:block;font-size:12px;color:var(--pds-ink-2);margin-bottom:5px;">Area</label>
                        <select name="area_id" id="mf_area" style="width:100%;padding:9px 12px;border:1px solid var(--pds-line-2);border-radius:10px;background:var(--pds-card);color:var(--pds-ink);">
                            <option value="">- Pilih Area -</option>
                            @foreach ($allAreas as $area)
                                <option value="{{ $area->id }}">{{ strtoupper($area->nama_area) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label style="display:block;font-size:12px;color:var(--pds-ink-2);margin-bottom:5px;">KW (opsional)</label>
                        <input type="number" step="0.01" min="0" name="kw" id="mf_kw" placeholder="0.00" style="width:100%;padding:9px 12px;border:1px solid var(--pds-line-2);border-radius:10px;background:var(--pds-card);color:var(--pds-ink);">
                    </div>

                    <div>
                        <label style="display:block;font-size:12px;color:var(--pds-ink-2);margin-bottom:5px;">Status *</label>
                        <select name="status" id="mf_status" style="width:100%;padding:9px 12px;border:1px solid var(--pds-line-2);border-radius:10px;background:var(--pds-card);color:var(--pds-ink);">
                            <option value="Aktif">Aktif</option>
                            <option value="Tidak Aktif">Tidak Aktif</option>
                        </select>
                    </div>

                    <div style="grid-column:1/-1;">
                        <label style="display:block;font-size:12px;color:var(--pds-ink-2);margin-bottom:5px;">Spesifikasi (opsional)</label>
                        <textarea name="spesifikasi" id="mf_spec" rows="2" placeholder="opsional" style="width:100%;padding:9px 12px;border:1px solid var(--pds-line-2);border-radius:10px;background:var(--pds-card);color:var(--pds-ink);"></textarea>
                    </div>

                </div>
            </div>

            <div style="display:flex;justify-content:flex-end;gap:8px;padding:0 18px 16px;">
                <button type="button" class="btn" onclick="closeMesinModal()">Batal</button>
                <button type="submit" class="btn btn-primary" id="mesinSubmitBtn">Simpan Mesin</button>
            </div>
        </form>

    </div>
</div>


{{-- ====================================================
     MODAL DETAIL MESIN (READ ONLY)
===================================================== --}}

<div id="mesinViewOverlay" class="pch-modal-overlay" aria-hidden="true">
    <div class="pch-modal" role="dialog" aria-modal="true" style="max-width:520px;width:min(520px,calc(100vw - 24px));">

        <div style="display:flex;align-items:center;justify-content:space-between;padding:16px 18px 8px;">
            <h2 style="margin:0;font-size:15.5px;">Detail Mesin</h2>
            <button type="button" class="act-btn" title="Tutup" aria-label="Tutup" onclick="closeMesinView()">
                <x-icon name="close" />
            </button>
        </div>

        <div class="pch-modal-body" style="padding:6px 18px 18px;" id="mesinViewBody"></div>

    </div>
</div>

<style>
    .pch-modal-overlay {
        position: fixed;
        inset: 0;
        z-index: 1200;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 16px;
        background: rgba(15, 23, 42, .55);
    }

    .pch-modal-overlay.active {
        display: flex;
    }

    .pch-modal {
        background: var(--pds-card);
        border: 1px solid var(--pds-line);
        border-radius: 16px;
        box-shadow: 0 24px 60px rgba(15, 23, 42, .25);
        max-height: min(86vh, 760px);
        overflow-y: auto;
        width: 100%;
    }
</style>

<script>
    function openOverlay(id) {
        document.getElementById(id).classList.add('active');
        document.getElementById(id).setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
    }

    function hideOverlay(id) {
        document.getElementById(id).classList.remove('active');
        document.getElementById(id).setAttribute('aria-hidden', 'true');
        if (!document.querySelector('.pch-modal-overlay.active')) {
            document.body.style.overflow = '';
        }
    }

    function openMesinModal() {
        var f = document.getElementById('mesinForm');

        f.action = '{{ route('machines.store') }}';
        f.method = 'POST';
        f.dataset.mode = 'create';

        delete f.dataset.editId;

        var m = f.querySelector('input[name="_method"]');
        if (m) m.remove();

        f.reset();
        document.getElementById('mesinModalTitle').textContent = 'Tambah Mesin';
        document.getElementById('mesinSubmitBtn').textContent = 'Simpan Mesin';

        openOverlay('mesinModalOverlay');
    }

    function editMesin(json) {
        var d = JSON.parse(json);

        var f = document.getElementById('mesinForm');

        f.action = '{{ url('machines') }}/' + d.id;
        f.dataset.mode = 'edit';
        f.dataset.editId = d.id;

        var m = f.querySelector('input[name="_method"]');
        if (!m) {
            m = document.createElement('input');
            m.type = 'hidden';
            m.name = '_method';
            m.value = 'PUT';
            f.appendChild(m);
        } else {
            m.value = 'PUT';
        }

        f.reset();

        document.getElementById('mf_kode').value = d.kode;
        document.getElementById('mf_nama').value = d.nama;

        var areaSel = document.getElementById('mf_area');
        areaSel.value = d.area_id ? String(d.area_id) : '';

        document.getElementById('mf_kw').value = d.kw === null || d.kw === '' ? '' : d.kw;
        document.getElementById('mf_status').value = d.status;
        document.getElementById('mf_spec').value = d.spesifikasi || '';

        document.getElementById('mesinModalTitle').textContent = 'Edit Mesin';
        document.getElementById('mesinSubmitBtn').textContent = 'Simpan Perubahan';

        openOverlay('mesinModalOverlay');
    }

    function closeMesinModal() {
        hideOverlay('mesinModalOverlay');
    }

    function submitMesinForm(form) {
        form.submit();
        return true;
    }

    function esc(s) {
        var el = document.createElement('span');
        el.textContent = s === null || s === undefined ? '' : String(s);
        return el.innerHTML;
    }

    function viewMesin(json) {
        var d = JSON.parse(json);

        var rows = [
            ['Kode Mesin', d.kode],
            ['Nama Mesin', d.nama],
            ['Area', d.area],
            ['KW', d.kw === null || d.kw === '' ? '-' : Number(d.kw).toLocaleString('id-ID')],
            ['Status', d.status],
            ['Spesifikasi', d.spesifikasi || '-'],
        ];

        document.getElementById('mesinViewBody').innerHTML =
            '<dl style="margin:0;display:grid;grid-template-columns:140px 1fr;row-gap:9px;column-gap:12px;font-size:13px;">'
            + rows.map(function (r) {
                return '<dt style="color:var(--pds-muted);font-weight:600;">' + esc(r[0]) + '</dt>'
                    + '<dd style="margin:0;color:var(--pds-ink);">' + esc(r[1]) + '</dd>';
            }).join('')
            + '</dl>';

        openOverlay('mesinViewOverlay');
    }

    function closeMesinView() {
        hideOverlay('mesinViewOverlay');
    }

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            closeMesinModal();
            closeMesinView();
        }
    });

    document.querySelectorAll('.pch-modal-overlay').forEach(function (ov) {
        ov.addEventListener('mousedown', function (e) {
            if (e.target === ov) {
                ov.classList.remove('active');
                ov.setAttribute('aria-hidden', 'true');
                if (!document.querySelector('.pch-modal-overlay.active')) {
                    document.body.style.overflow = '';
                }
            }
        });
    });
</script>

@endsection
