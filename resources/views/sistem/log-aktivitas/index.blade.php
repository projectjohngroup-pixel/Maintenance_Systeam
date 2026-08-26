<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Log Aktivitas</title>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            background: var(--pds-soft);
            color: var(--pds-ink);
        }

        .container {
            max-width: 1200px;
            margin: 35px auto;
            padding: 20px;
        }

        .card {
            background: var(--pds-card);
            border-radius: 14px;
            padding: 25px;
            box-shadow: 0 3px 12px rgba(0, 0, 0, 0.06);
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            margin-bottom: 25px;
        }

        .header h1 {
            font-size: 26px;
            margin-bottom: 6px;
        }

        .header p {
            color: var(--pds-muted);
            font-size: 14px;
        }

        .back {
            display: inline-block;
            padding: 10px 15px;
            background: var(--pds-line);
            color: var(--pds-ink-2);
            border-radius: 8px;
            text-decoration: none;
            font-size: 13px;
        }

        .table-wrapper {
            overflow-x: auto;
        }

        table {
            width: 100%;
            min-width: 900px;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 12px 14px;
            border-bottom: 1px solid var(--pds-line);
            text-align: left;
            vertical-align: top;
            font-size: 13px;
        }

        th {
            background: var(--pds-soft);
            font-weight: bold;
            color: var(--pds-ink-2);
        }

        .action {
            font-weight: bold;
            color: #2563eb;
        }

        .empty {
            text-align: center;
            color: var(--pds-muted-2);
            padding: 30px;
        }

        .pagination {
            margin-top: 20px;
        }

        .pagination nav {
            display: flex;
            justify-content: center;
        }

        @media (max-width: 700px) {
            .container {
                margin: 20px auto;
                padding: 12px;
            }

            .card {
                padding: 18px;
            }

            .header {
                align-items: flex-start;
                flex-direction: column;
            }
        }
    </style>
</head>

<body>

<div class="container">

    <div class="card">

        <div class="header">

            <div>

                <h1>
                    Log Aktivitas
                </h1>

                <p>
                    Riwayat aktivitas pengguna di dalam sistem.
                </p>

            </div>

            <a
                href="{{ route('dashboard') }}"
                class="back"
            >
                Kembali
            </a>

        </div>


        <div class="table-wrapper">

            <table>

                <thead>

                    <tr>
                        <th>Waktu</th>
                        <th>Nama</th>
                        <th>Bagian</th>
                        <th>Aktivitas</th>
                        <th>Keterangan</th>
                        <th>IP Address</th>
                    </tr>

                </thead>

                <tbody>

                    @forelse($logs as $log)

                        <tr>

                            <td>
                                {{ $log->created_at?->format('d-m-Y H:i:s') ?? '-' }}
                            </td>

                            <td>
                                {{ $log->user->name ?? '-' }}
                            </td>

                            <td>
                                {{ $log->user->bagian ?? '-' }}
                            </td>

                            <td class="action">
                                {{ $log->action ?? '-' }}
                            </td>

                            <td>
                                {{ $log->description ?? '-' }}
                            </td>

                            <td>
                                {{ $log->ip_address ?? '-' }}
                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="6"
                                class="empty"
                            >
                                Belum ada aktivitas.
                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        @if(method_exists($logs, 'links'))

            <div class="pagination">
                {{ $logs->links() }}
            </div>

        @endif

    </div>

</div>

</body>

</html>