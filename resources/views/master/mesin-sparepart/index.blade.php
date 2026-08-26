@extends('layouts.app')

@section('title', 'Mesin & Sparepart')

@section('page_title', 'Mesin & Sparepart')

@section('page_subtitle', 'Pemetaan sparepart standar per mesin')

@section('content')

<div class="card">

    {{-- ================================================
         HEADER: SEARCH + TAMBAH
    ================================================= --}}

    <form
        method="GET"
        action="{{ route('machine-spareparts.index') }}"
        style="display:flex;flex-wrap:wrap;gap:10px;align-items:center;margin-bottom:16px;"
    >
        <div style="position:relative;flex:1 1 260px;">
            <x-icon name="search"
                style="position:absolute;left:11px;top:50%;transform:translateY(-50%);font-size:14px;color:var(--pds-muted);" />
            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Cari mesin / kode barang..."
                style="width:100%;padding:9px 12px 9px 34px;border:1px solid var(--pds-line-2);border-radius:10px;background:var(--pds-card);color:var(--pds-ink);font-size:13px;"
            >
        </div>
        <button type="submit" class="btn btn-primary" title="Cari">Cari</button>
        @if (request()->filled('search'))
            <a href="{{ route('machine-spareparts.index') }}" style="display:inline-flex;align-items:center;gap:6px;font-size:12.5px;color:var(--pds-muted);text-decoration:none;">
                <x-icon name="close" /> Reset
            </a>
        @endif
        <button
            type="button"
            class="btn btn-primary"
            onclick="openMappingModal()"
            style="margin-left:auto;display:inline-flex;align-items:center;gap:6px;"
        >
            <x-icon name="plus" /> Tambah Pemetaan
        </button>
    </form>

    {{-- ================================================
         TABEL PEMETAAN
    ================================================= --}}

    <div class="pch-scroll">
        <table class="table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Mesin</th>
                    <th>Barang / Sparepart</th>
                    <th>Satuan</th>
                    <th>Qty Standar</th>
                    <th>Keterangan</th>
                    <th style="width:130px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($machineSpareparts as $item)
                    @php
                        $mappingPayload = [
                            'id' => $item->id,
                            'machine_id' => $item->machine_id,
                            'barang_id' => $item->barang_id,
                            'qty' => $item->qty,
                            'keterangan' => $item->keterangan,
                            'mesin' => trim(($item->machine?->kode_mesin ? $item->machine->kode_mesin . ' — ' : '') . $item->machine?->nama_mesin) ?: '-',
                            'barang' => trim(($item->barang?->kode_barang ? $item->barang->kode_barang . ' — ' : '') . $item->barang?->nama_spesifikasi) ?: '-',
                            'satuan' => $item->barang?->satuan?->nama_satuan ?? '-',
                        ];
                    @endphp
                    <tr data-mapping='@json($mappingPayload)'>
                        <td>{{ ($machineSpareparts->currentPage() - 1) * $machineSpareparts->perPage() + $loop->iteration }}</td>
                        <td><b>{{ $item->machine?->kode_mesin }}</b> — {{ $item->machine?->nama_mesin }}</td>
                        <td>{{ $item->barang?->kode_barang }} — {{ $item->barang?->nama_spesifikasi }}</td>
                        <td>{{ $item->barang?->satuan?->nama_satuan ?? '-' }}</td>
                        <td>{{ number_format((float) $item->qty, 2) }}</td>
                        <td>{{ $item->keterangan ?? '-' }}</td>
                        <td>
                            <div class="act-group">
                                <button
                                    type="button"
                                    class="act-btn act-view"
                                    title="Lihat Detail Pemetaan"
                                    aria-label="Lihat Detail Pemetaan"
                                    onclick="viewMapping(this.closest('tr').dataset.mapping)"
                                >
                                    <x-icon name="eye" />
                                </button>

                                <button
                                    type="button"
                                    class="act-btn act-edit"
                                    title="Edit Pemetaan"
                                    aria-label="Edit Pemetaan"
                                    onclick="editMapping(this.closest('tr').dataset.mapping)"
                                >
                                    <x-icon name="edit" />
                                </button>

                                <form
                                    action="{{ route('machine-spareparts.destroy', $item) }}"
                                    method="POST"
                                    data-confirm="Hapus pemetaan ini?"
                                    style="display:inline;"
                                >
                                    @csrf
                                    @method('DELETE')
                                    <button
                                        type="submit"
                                        class="act-btn act-delete"
                                        title="Hapus Pemetaan"
                                        aria-label="Hapus Pemetaan"
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
                            {{ request('search') ? 'Tidak ada data yang cocok.' : 'Belum ada pemetaan sparepart.' }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:16px;">{{ $machineSpareparts->links() }}</div>

</div>


{{-- ====================================================
     MODAL FORM PEMETAAN (TAMBAH / EDIT)
===================================================== --}}

<div id="mappingModalOverlay" class="pch-modal-overlay" aria-hidden="true">
    <div class="pch-modal" role="dialog" aria-modal="true" style="max-width:480px;width:min(480px,calc(100vw - 24px));">

        <div style="display:flex;align-items:flex-start;justify-content:space-between;padding:16px 18px 10px;">
            <div>
                <h2 id="mappingModalTitle" style="margin:0;font-size:15.5px;">Tambah Pemetaan Sparepart</h2>
                <p style="margin:2px 0 0;font-size:12px;color:var(--pds-muted);">Satu barang hanya boleh dipetakan sekali per mesin.</p>
            </div>
            <button type="button" class="act-btn" title="Tutup" aria-label="Tutup" onclick="closeMappingModal()">
                <x-icon name="close" />
            </button>
        </div>

        <form id="mappingForm" method="POST" action="{{ route('machine-spareparts.store') }}">
            @csrf
            <div class="pch-modal-body" style="padding:4px 18px 14px;display:grid;gap:12px;">

                <div>
                    <label style="display:block;font-size:12px;color:var(--pds-ink-2);margin-bottom:5px;">Mesin *</label>
                    <select name="machine_id" id="mf_machine" required style="width:100%;padding:9px 12px;border:1px solid var(--pds-line-2);border-radius:10px;background:var(--pds-card);color:var(--pds-ink);">
                        <option value="">- Pilih Mesin -</option>
                        @foreach ($machines as $machine)
                            <option value="{{ $machine->id }}">
                                {{ $machine->kode_mesin }} — {{ $machine->nama_mesin }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label style="display:block;font-size:12px;color:var(--pds-ink-2);margin-bottom:5px;">Barang / Sparepart *</label>
                    <select name="barang_id" id="mf_barang" required style="width:100%;padding:9px 12px;border:1px solid var(--pds-line-2);border-radius:10px;background:var(--pds-card);color:var(--pds-ink);">
                        <option value="">- Pilih Barang -</option>
                        @foreach ($barangs as $barang)
                            <option value="{{ $barang->id }}">
                                {{ $barang->kode_barang }} — {{ $barang->nama_spesifikasi }}{{ $barang->satuan ? ' (' . $barang->satuan->nama_satuan . ')' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label style="display:block;font-size:12px;color:var(--pds-ink-2);margin-bottom:5px;">Qty Standar *</label>
                    <input type="number" step="0.01" min="0.01" name="qty" id="mf_qty" required style="width:100%;padding:9px 12px;border:1px solid var(--pds-line-2);border-radius:10px;background:var(--pds-card);color:var(--pds-ink);">
                </div>

                <div>
                    <label style="display:block;font-size:12px;color:var(--pds-ink-2);margin-bottom:5px;">Keterangan</label>
                    <input type="text" name="keterangan" id="mf_ket" placeholder="opsional" style="width:100%;padding:9px 12px;border:1px solid var(--pds-line-2);border-radius:10px;background:var(--pds-card);color:var(--pds-ink);">
                </div>

            </div>

            <div style="display:flex;justify-content:flex-end;gap:8px;padding:0 18px 16px;">
                <button type="button" class="btn" onclick="closeMappingModal()">Batal</button>
                <button type="submit" class="btn btn-primary" id="mappingSubmitBtn">Simpan</button>
            </div>
        </form>

    </div>
</div>


{{-- ====================================================
     MODAL DETAIL PEMETAAN (READ ONLY)
===================================================== --}}

<div id="mappingViewOverlay" class="pch-modal-overlay" aria-hidden="true">
    <div class="pch-modal" role="dialog" aria-modal="true" style="max-width:460px;width:min(460px,calc(100vw - 24px));">

        <div style="display:flex;align-items:center;justify-content:space-between;padding:16px 18px 8px;">
            <h2 style="margin:0;font-size:15.5px;">Detail Pemetaan</h2>
            <button type="button" class="act-btn" title="Tutup" aria-label="Tutup" onclick="closeMappingView()">
                <x-icon name="close" />
            </button>
        </div>

        <div class="pch-modal-body" style="padding:6px 18px 18px;" id="mappingViewBody"></div>

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
    function openOverlayMs(id) {
        document.getElementById(id).classList.add('active');
        document.getElementById(id).setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
    }

    function hideOverlayMs(id) {
        document.getElementById(id).classList.remove('active');
        document.getElementById(id).setAttribute('aria-hidden', 'true');
        if (!document.querySelector('.pch-modal-overlay.active')) {
            document.body.style.overflow = '';
        }
    }

    function openMappingModal() {
        var f = document.getElementById('mappingForm');

        f.action = '{{ route('machine-spareparts.store') }}';
        f.method = 'POST';

        var m = f.querySelector('input[name="_method"]');
        if (m) m.remove();

        f.reset();
        document.getElementById('mappingModalTitle').textContent = 'Tambah Pemetaan Sparepart';
        document.getElementById('mappingSubmitBtn').textContent = 'Simpan';

        openOverlayMs('mappingModalOverlay');
    }

    function editMapping(json) {
        var d = JSON.parse(json);

        var f = document.getElementById('mappingForm');

        f.action = '{{ url('machine-spareparts') }}/' + d.id;

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

        document.getElementById('mf_machine').value = String(d.machine_id);
        document.getElementById('mf_barang').value = String(d.barang_id);
        document.getElementById('mf_qty').value = d.qty;
        document.getElementById('mf_ket').value = d.keterangan || '';

        document.getElementById('mappingModalTitle').textContent = 'Edit Pemetaan Sparepart';
        document.getElementById('mappingSubmitBtn').textContent = 'Simpan Perubahan';

        openOverlayMs('mappingModalOverlay');
    }

    function closeMappingModal() {
        hideOverlayMs('mappingModalOverlay');
    }

    function escMs(s) {
        var el = document.createElement('span');
        el.textContent = s === null || s === undefined ? '' : String(s);
        return el.innerHTML;
    }

    function viewMapping(json) {
        var d = JSON.parse(json);

        var rows = [
            ['Mesin', d.mesin],
            ['Barang / Sparepart', d.barang],
            ['Satuan', d.satuan],
            ['Qty Standar', Number(d.qty).toLocaleString('id-ID')],
            ['Keterangan', d.keterangan || '-'],
        ];

        document.getElementById('mappingViewBody').innerHTML =
            '<dl style="margin:0;display:grid;grid-template-columns:140px 1fr;row-gap:9px;column-gap:12px;font-size:13px;">'
            + rows.map(function (r) {
                return '<dt style="color:var(--pds-muted);font-weight:600;">' + escMs(r[0]) + '</dt>'
                    + '<dd style="margin:0;color:var(--pds-ink);">' + escMs(r[1]) + '</dd>';
            }).join('')
            + '</dl>';

        openOverlayMs('mappingViewOverlay');
    }

    function closeMappingView() {
        hideOverlayMs('mappingViewOverlay');
    }

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            closeMappingModal();
            closeMappingView();
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
