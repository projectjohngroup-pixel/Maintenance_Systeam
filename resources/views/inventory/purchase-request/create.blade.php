@extends('layouts.app')

@section('title', 'Buat Purchase Request')

@section('page_title', 'Buat Purchase Request')

@section('page_subtitle', 'Form pengajuan permintaan pembelian barang')

@section('content')

<div class="card" style="max-width:760px;">

    <form action="{{ route('purchase-requests.store') }}" method="POST">

        @csrf

        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:14px;">

            <div>
                <label style="display:block;font-size:12px;color:var(--pds-ink-2);margin-bottom:6px;">Tanggal PR</label>
                <input type="date" name="tanggal_pr" value="{{ old('tanggal_pr', now()->toDateString()) }}" required style="width:100%;padding:10px 12px;border:1px solid var(--pds-line-2);border-radius:10px;background:var(--pds-card);">
            </div>

            <div>
                <label style="display:block;font-size:12px;color:var(--pds-ink-2);margin-bottom:6px;">Prioritas</label>
                <select name="prioritas" style="width:100%;padding:10px 12px;border:1px solid var(--pds-line-2);border-radius:10px;background:var(--pds-card);">
                    <option value="NORMAL" {{ old('prioritas') === 'URGENT' ? '' : 'selected' }}>NORMAL</option>
                    <option value="URGENT" {{ old('prioritas') === 'URGENT' ? 'selected' : '' }}>URGENT</option>
                </select>
            </div>

            <div style="grid-column:1/-1;">
                <label style="display:block;font-size:12px;color:var(--pds-ink-2);margin-bottom:6px;">Keperluan</label>
                <input type="text" name="keperluan" value="{{ old('keperluan') }}" required maxlength="255" placeholder="cth: Penggantian bearing conveyor" style="width:100%;padding:10px 12px;border:1px solid var(--pds-line-2);border-radius:10px;background:var(--pds-card);">
            </div>

            <div style="grid-column:1/-1;">
                <label style="display:block;font-size:12px;color:var(--pds-ink-2);margin-bottom:6px;">Catatan</label>
                <textarea name="catatan" rows="3" placeholder="opsional" style="width:100%;padding:10px 12px;border:1px solid var(--pds-line-2);border-radius:10px;background:var(--pds-card);">{{ old('catatan') }}</textarea>
            </div>

        </div>

        <div style="margin-top:18px;display:flex;gap:10px;">
            <button type="submit" class="btn btn-primary">Ajukan PR</button>
            <a href="{{ route('purchase-requests.index') }}" class="btn btn-secondary">Kembali</a>
        </div>

    </form>

</div>

@endsection
