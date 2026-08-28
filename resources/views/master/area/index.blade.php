@extends('layouts.app')

@section('title', 'Master Area')

@section('page_title', 'Data Area')

@section('page_subtitle', 'Kelola daftar area / line produksi')

@section('content')

<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(170px,1fr));gap:12px;margin-bottom:20px;">
    <div class="card" style="padding:14px 18px;">
        <div style="font-size:11.5px;color:var(--pds-muted);text-transform:uppercase;letter-spacing:.06em;">Total Area</div>
        <div style="font-size:26px;font-weight:700;margin-top:2px;">{{ $areas->count() }}</div>
    </div>
    <div class="card" style="padding:14px 18px;">
        <div style="font-size:11.5px;color:var(--pds-muted);text-transform:uppercase;letter-spacing:.06em;">Total Mesin</div>
        <div style="font-size:26px;font-weight:700;margin-top:2px;">{{ $totalMachines }}</div>
    </div>
    <div class="card" style="padding:14px 18px;">
        <div style="font-size:11.5px;color:var(--pds-muted);text-transform:uppercase;letter-spacing:.06em;">Total KW</div>
        <div style="font-size:26px;font-weight:700;margin-top:2px;">{{ pdsNumber($totalKw) }}</div>
    </div>
</div>

<div class="card">

    {{-- ================================================
         HEADER TABEL: SEARCH + TAMBAH
    ================================================= --}}

    <form
        method="GET"
        action="{{ route('areas.index') }}"
        style="display:flex;flex-wrap:wrap;gap:10px;align-items:center;margin-bottom:16px;"
    >
        <div style="position:relative;flex:1 1 260px;">
            <x-icon name="search"
                style="position:absolute;left:11px;top:50%;transform:translateY(-50%);font-size:14px;color:var(--pds-muted);" />
            <input
                type="text"
                name="q"
                value="{{ request('q') }}"
                placeholder="Cari nama area / keterangan..."
                style="width:100%;padding:9px 12px 9px 34px;border:1px solid var(--pds-line-2);border-radius:10px;background:var(--pds-card);color:var(--pds-ink);font-size:13px;"
            >
        </div>
        <button type="submit" class="btn btn-primary" title="Cari">Cari</button>
        @if (request()->filled('q'))
            <a href="{{ route('areas.index') }}" style="display:inline-flex;align-items:center;gap:6px;font-size:12.5px;color:var(--pds-muted);text-decoration:none;">
                <x-icon name="close" /> Reset
            </a>
        @endif
        <button
            type="button"
            class="btn-add-master"
            onclick="openAreaModal()"
            style="margin-left:auto;"
        >
            <x-icon name="plus" /> Tambah Area
        </button>
    </form>

    {{-- ================================================
         TABEL AREA
    ================================================= --}}

    <div class="pch-scroll">
        <table class="table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Area</th>
                    <th>Keterangan</th>
                    <th>Jumlah Mesin</th>
                    <th>Total KW</th>
                    <th style="width:130px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($areas as $area)
                    @php
                        $areaPayload = [
                            'id' => $area->id,
                            'nama' => strtoupper($area->nama_area),
                            'keterangan' => $area->keterangan,
                            'mesin' => $area->machines_count,
                            'kw' => pdsNumber((float) ($area->machines_sum_kw ?? 0), '.', ''),
                        ];
                    @endphp
                    <tr data-area='@json($areaPayload)'>
                        <td>{{ $loop->iteration }}</td>
                        <td><b>{{ strtoupper($area->nama_area) }}</b></td>
                        <td>{{ $area->keterangan ?? '-' }}</td>
                        <td>{{ $area->machines_count }}</td>
                        <td>{{ pdsNumber((float) ($area->machines_sum_kw ?? 0)) }}</td>
                        <td>
                            <div class="act-group">
                                <button
                                    type="button"
                                    class="act-btn act-view"
                                    title="Lihat Detail Area"
                                    aria-label="Lihat Detail Area"
                                    onclick="viewArea(this.closest('tr').dataset.area)"
                                >
                                    <x-icon name="eye" />
                                </button>

                                <button
                                    type="button"
                                    class="act-btn act-edit"
                                    title="Edit Area"
                                    aria-label="Edit Area"
                                    onclick="editArea(this.closest('tr').dataset.area)"
                                >
                                    <x-icon name="edit" />
                                </button>

                                <form
                                    action="{{ route('areas.destroy', $area) }}"
                                    method="POST"
                                    data-confirm="Hapus area {{ strtoupper($area->nama_area) }}?"
                                    style="display:inline;"
                                >
                                    @csrf
                                    @method('DELETE')
                                    <button
                                        type="submit"
                                        class="act-btn act-delete"
                                        title="Hapus Area"
                                        aria-label="Hapus Area"
                                    >
                                        <x-icon name="trash" />
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align:center;color:var(--pds-muted);padding:28px;">
                            Belum ada data area.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>


{{-- ====================================================
     MODAL FORM AREA (TAMBAH / EDIT)
===================================================== --}}

<div id="areaModalOverlay" class="pch-modal-overlay" aria-hidden="true">
    <div class="pch-modal" role="dialog" aria-modal="true" style="max-width:480px;width:min(480px,calc(100vw - 24px));">

        <div style="display:flex;align-items:flex-start;justify-content:space-between;padding:16px 18px 10px;">
            <div>
                <h2 id="areaModalTitle" style="margin:0;font-size:15.5px;">Tambah Area / Line</h2>
                <p style="margin:2px 0 0;font-size:12px;color:var(--pds-muted);">Nama area harus unik.</p>
            </div>
            <button type="button" class="act-btn" title="Tutup" aria-label="Tutup" onclick="closeAreaModal()">
                <x-icon name="close" />
            </button>
        </div>

        <form id="areaForm" method="POST" action="{{ route('areas.store') }}">
            @csrf
            <div class="pch-modal-body" style="padding:4px 18px 14px;">

                <div style="margin-bottom:12px;">
                    <label style="display:block;font-size:12px;color:var(--pds-ink-2);margin-bottom:5px;">Nama Area *</label>
                    <input type="text" name="nama_area" id="af_nama" required maxlength="255" placeholder="cth: LINE 1" style="width:100%;padding:9px 12px;border:1px solid var(--pds-line-2);border-radius:10px;background:var(--pds-card);color:var(--pds-ink);">
                </div>

                <div>
                    <label style="display:block;font-size:12px;color:var(--pds-ink-2);margin-bottom:5px;">Keterangan</label>
                    <input type="text" name="keterangan" id="af_ket" placeholder="opsional" style="width:100%;padding:9px 12px;border:1px solid var(--pds-line-2);border-radius:10px;background:var(--pds-card);color:var(--pds-ink);">
                </div>

            </div>

            <div style="display:flex;justify-content:flex-end;gap:8px;padding:0 18px 16px;">
                <button type="button" class="btn" onclick="closeAreaModal()">Batal</button>
                <button type="submit" class="btn btn-primary" id="areaSubmitBtn">Simpan</button>
            </div>
        </form>

    </div>
</div>


{{-- ====================================================
     MODAL DETAIL AREA (READ ONLY)
===================================================== --}}

<div id="areaViewOverlay" class="pch-modal-overlay" aria-hidden="true">
    <div class="pch-modal" role="dialog" aria-modal="true" style="max-width:460px;width:min(460px,calc(100vw - 24px));">

        <div style="display:flex;align-items:center;justify-content:space-between;padding:16px 18px 8px;">
            <h2 style="margin:0;font-size:15.5px;">Detail Area</h2>
            <button type="button" class="act-btn" title="Tutup" aria-label="Tutup" onclick="closeAreaView()">
                <x-icon name="close" />
            </button>
        </div>

        <div class="pch-modal-body" style="padding:6px 18px 18px;" id="areaViewBody"></div>

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
    function openOverlayA(id) {
        document.getElementById(id).classList.add('active');
        document.getElementById(id).setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
    }

    function hideOverlayA(id) {
        document.getElementById(id).classList.remove('active');
        document.getElementById(id).setAttribute('aria-hidden', 'true');
        if (!document.querySelector('.pch-modal-overlay.active')) {
            document.body.style.overflow = '';
        }
    }

    function openAreaModal() {
        var f = document.getElementById('areaForm');

        f.action = '{{ route('areas.store') }}';
        f.method = 'POST';

        var m = f.querySelector('input[name="_method"]');
        if (m) m.remove();

        f.reset();
        document.getElementById('areaModalTitle').textContent = 'Tambah Area / Line';
        document.getElementById('areaSubmitBtn').textContent = 'Simpan';

        openOverlayA('areaModalOverlay');
    }

    function editArea(json) {
        var d = JSON.parse(json);

        var f = document.getElementById('areaForm');

        f.action = '{{ url('areas') }}/' + d.id;

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

        document.getElementById('af_nama').value = d.nama;
        document.getElementById('af_ket').value = d.keterangan || '';

        document.getElementById('areaModalTitle').textContent = 'Edit Area';
        document.getElementById('areaSubmitBtn').textContent = 'Simpan Perubahan';

        openOverlayA('areaModalOverlay');
    }

    function closeAreaModal() {
        hideOverlayA('areaModalOverlay');
    }

    function escA(s) {
        var el = document.createElement('span');
        el.textContent = s === null || s === undefined ? '' : String(s);
        return el.innerHTML;
    }

    function viewArea(json) {
        var d = JSON.parse(json);

        var rows = [
            ['Nama Area', d.nama],
            ['Keterangan', d.keterangan || '-'],
            ['Jumlah Mesin', d.mesin],
            ['Total KW', Number(d.kw).toLocaleString('id-ID')],
        ];

        document.getElementById('areaViewBody').innerHTML =
            '<dl style="margin:0;display:grid;grid-template-columns:130px 1fr;row-gap:9px;column-gap:12px;font-size:13px;">'
            + rows.map(function (r) {
                return '<dt style="color:var(--pds-muted);font-weight:600;">' + escA(r[0]) + '</dt>'
                    + '<dd style="margin:0;color:var(--pds-ink);">' + escA(r[1]) + '</dd>';
            }).join('')
            + '</dl>';

        openOverlayA('areaViewOverlay');
    }

    function closeAreaView() {
        hideOverlayA('areaViewOverlay');
    }

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            closeAreaModal();
            closeAreaView();
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
