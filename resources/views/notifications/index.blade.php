@extends('layouts.app')

@section('title', 'Notifikasi')

@section('page_title', 'Notifikasi')

@section(
    'page_subtitle',
    'Daftar notifikasi dan perkembangan Work Order'
)

@push('styles')

<style>
    .notif-page {
        padding: 12px 0 18px;
    }

    .notif-card {
        background: var(--pds-card);
        border-radius: 12px;
        box-shadow: 0 3px 12px rgba(0, 0, 0, .06);
        overflow: hidden;
    }

    .notif-card-header {
        padding: 12px 16px;
        border-bottom: 1px solid var(--pds-line);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .notif-card-title {
        font-size: 16px;
        font-weight: 700;
        color: var(--pds-ink);
    }

    .notif-card-count {
        color: var(--pds-muted);
        font-size: 13px;
    }

    .notif-item-page {
        display: block;
        padding: 12px 16px;
        border-bottom: 1px solid var(--pds-line);
        text-decoration: none;
        color: inherit;
        transition: background .12s ease;
    }

    .notif-item-page:hover {
        background: var(--pds-soft);
    }

    .notif-item-page.unread {
        background: rgba(37, 99, 235, .04);
    }

    .notif-item-top {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 3px;
    }

    .notif-item-title-p {
        font-size: 14px;
        font-weight: 600;
        color: var(--pds-ink);
    }

    .notif-item-time-p {
        font-size: 12px;
        color: var(--pds-muted-2);
        white-space: nowrap;
    }

    .notif-item-msg-p {
        font-size: 13px;
        color: var(--pds-muted);
        line-height: 1.4;
    }

    .notif-empty-state {
        padding: 40px 16px;
        text-align: center;
        color: var(--pds-muted);
        font-size: 14px;
    }

    .notif-empty-state svg {
        width: 48px;
        height: 48px;
        margin: 0 auto 12px;
        stroke: var(--pds-muted-2);
        fill: none;
        stroke-width: 1.5;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .notif-actions {
        display: flex;
        gap: 8px;
    }

    .notif-btn {
        display: inline-flex;
        align-items: center;
        min-height: 34px;
        padding: 6px 14px;
        border: 1px solid var(--pds-line-2);
        border-radius: 7px;
        background: var(--pds-card);
        color: var(--pds-ink-2);
        font-family: inherit;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: .15s ease;
    }

    .notif-btn:hover {
        background: var(--pds-soft);
    }

    .notif-btn-primary {
        background: #2563eb;
        color: #fff;
        border-color: #2563eb;
    }

    .notif-btn-primary:hover {
        background: #1d4ed8;
    }

    .notif-pagination {
        display: flex;
        justify-content: center;
        gap: 6px;
        padding: 14px 16px;
        border-top: 1px solid var(--pds-line);
    }

    .notif-pagination a,
    .notif-pagination span {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 34px;
        height: 34px;
        padding: 4px 10px;
        border-radius: 7px;
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
        color: var(--pds-ink-2);
        border: 1px solid var(--pds-line-2);
        background: var(--pds-card);
        transition: .15s ease;
    }

    .notif-pagination a:hover {
        background: var(--pds-soft);
    }

    .notif-pagination .active {
        background: #2563eb;
        color: #fff;
        border-color: #2563eb;
    }
</style>

@endpush

@section('content')

<div class="notif-page">

    <div class="notif-card">

        <div class="notif-card-header">

            <div>
                <div class="notif-card-title">
                    Notifikasi
                </div>

                <div class="notif-card-count">
                    {{ $unreadCount }} belum dibaca
                </div>
            </div>

            @if($unreadCount > 0)

                <div class="notif-actions">

                    <button
                        type="button"
                        class="notif-btn notif-btn-primary"
                        onclick="markAllRead()"
                    >
                        Tandai semua sudah dibaca
                    </button>

                </div>

            @endif

        </div>

        @if($notifications->isEmpty())

            <div class="notif-empty-state">

                <svg viewBox="0 0 24 24">
                    <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                    <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                </svg>

                Belum ada notifikasi.

            </div>

        @else

            @foreach($notifications as $notif)

                @php

                    $woUrl = '#';

                    if ($notif->work_order_id) {

                        $role = \App\Support\DepartmentAccess::normalizeRole(
                            auth()->user()->role ?? ''
                        );

                        if (in_array($role, [\App\Support\DepartmentAccess::ADMINISTRATOR])) {

                            $woUrl = route(
                                'work-orders.admin.show',
                                $notif->work_order_id
                            );

                        } elseif ($role === \App\Support\DepartmentAccess::MAINTENANCE) {

                            $woUrl = route(
                                'work-orders.maintenance.show',
                                $notif->work_order_id
                            );

                        } else {

                            $woUrl = route(
                                'work-orders.show',
                                $notif->work_order_id
                            );
                        }
                    }

                @endphp

                <a
                    href="{{ $woUrl }}"
                    class="notif-item-page {{ $notif->status === 'UNREAD' ? 'unread' : '' }}"
                    data-notif-id="{{ $notif->id }}"
                >

                    <div class="notif-item-top">

                        <span class="notif-item-title-p">
                            {{ $notif->title }}
                        </span>

                        <span class="notif-item-time-p">
                            {{ $notif->created_at->diffForHumans() }}
                        </span>

                    </div>

                    <div class="notif-item-msg-p">
                        {{ $notif->message }}
                    </div>

                </a>

            @endforeach

            <div class="notif-pagination">
                {{ $notifications->links() }}
            </div>

        @endif

    </div>

</div>

<script>
function markAllRead()
{
    fetch(
        '{{ route("notifications.mark-all-read") }}',
        {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
            credentials: 'same-origin',
        }
    )
    .then(function (res) {
        return res.json();
    })
    .then(function () {
        location.reload();
    });
}
</script>

@endsection
