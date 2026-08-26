@extends('layouts.app')

@section('title', 'Purchase Request')

@section('page_title', 'Purchase Request')

@section('page_subtitle', 'Daftar permintaan pembelian barang')

@section('content')

<div class="card">

    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">

        <div>
            <h2 style="margin:0;">Purchase Request</h2>
            <p style="margin:5px 0 0;color:var(--pds-muted);font-size:13px;">
                Daftar permintaan pembelian barang.
            </p>
        </div>

        <a
            href="{{ route('purchase-requests.create') }}"
            class="btn btn-primary"
        >
            + Buat Purchase Request
        </a>

    </div>

    <div style="overflow-x:auto;">

        <table style="width:100%;border-collapse:collapse;">

            <thead>

                <tr>

                    <th style="padding:12px;border-bottom:1px solid var(--pds-line);text-align:left;">
                        No
                    </th>

                    <th style="padding:12px;border-bottom:1px solid var(--pds-line);text-align:left;">
                        No PR
                    </th>

                    <th style="padding:12px;border-bottom:1px solid var(--pds-line);text-align:left;">
                        Tanggal
                    </th>

                    <th style="padding:12px;border-bottom:1px solid var(--pds-line);text-align:left;">
                        Keperluan
                    </th>

                    <th style="padding:12px;border-bottom:1px solid var(--pds-line);text-align:left;">
                        Prioritas
                    </th>

                    <th style="padding:12px;border-bottom:1px solid var(--pds-line);text-align:left;">
                        Status
                    </th>

                </tr>

            </thead>

            <tbody>

                @forelse($purchaseRequests as $index => $pr)

                    <tr>

                        <td style="padding:12px;border-bottom:1px solid var(--pds-line);">
                            {{ $purchaseRequests->firstItem() + $index }}
                        </td>

                        <td style="padding:12px;border-bottom:1px solid var(--pds-line);font-weight:700;">
                            {{ $pr->no_pr }}
                        </td>

                        <td style="padding:12px;border-bottom:1px solid var(--pds-line);">
                            {{ $pr->tanggal_pr?->format('d/m/Y') }}
                        </td>

                        <td style="padding:12px;border-bottom:1px solid var(--pds-line);">
                            {{ $pr->keperluan }}
                        </td>

                        <td style="padding:12px;border-bottom:1px solid var(--pds-line);">
                            {{ $pr->prioritas }}
                        </td>

                        <td style="padding:12px;border-bottom:1px solid var(--pds-line);">
                            {{ $pr->status }}
                        </td>

                    </tr>

                @empty

                    <tr>

                        <td
                            colspan="6"
                            style="padding:30px;text-align:center;color:var(--pds-muted-2);"
                        >
                            Belum ada Purchase Request.
                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

    <div style="margin-top:20px;">
        {{ $purchaseRequests->links() }}
    </div>

</div>

@endsection