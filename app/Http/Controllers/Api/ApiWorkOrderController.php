<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WorkOrder\WorkOrder;
use App\Models\WorkOrder\WorkOrderStatusHistory;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ApiWorkOrderController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | STATUS YANG VALID
    |--------------------------------------------------------------------------
    */

    private const STATUSES = [
        'OPEN',
        'DITERIMA',
        'DITOLAK',
        'SCHEDULED',
        'IN PROGRESS',
        'PENDING',
        'SERVICE LUAR',
        'CLOSE',
    ];

    /*
    |--------------------------------------------------------------------------
    | GET /api/work-orders
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $user = $request->user();

        $role = \App\Support\DepartmentAccess::normalizeRole($user->role ?? '');

        $query = WorkOrder::query();

        /*
        | PRODUKSI hanya melihat work order miliknya.
        */

        if ($role === \App\Support\DepartmentAccess::PRODUKSI) {

            $query->whereRaw('LOWER(TRIM(dibuat_oleh)) = ?', [
                strtolower(trim((string) $user->name)),
            ]);
        }

        if ($search = trim((string) $request->query('search'))) {

            $query->where(function ($q) use ($search) {

                $q->where('no_wo', 'like', "%{$search}%")
                    ->orWhere('job', 'like', "%{$search}%")
                    ->orWhere('mesin', 'like', "%{$search}%");
            });
        }

        if ($status = strtoupper(trim((string) $request->query('status')))) {

            if (in_array($status, self::STATUSES, true)) {

                $query->whereRaw('LOWER(TRIM(status)) = ?', [strtolower($status)]);
            }
        }

        if ($prio = strtoupper(trim((string) $request->query('prioritas')))) {

            if (in_array($prio, ['NORMAL', 'URGENT', 'EMERGENCY'], true)) {

                $query->whereRaw(
                    'LOWER(TRIM(priority)) = ?',
                    [strtolower($prio)]
                );
            }
        }

        $orders = $query
            ->orderByDesc('created_at')
            ->paginate(min(max((int) $request->query('per_page', 20), 1), 100));

        return response()->json([
            'success' => true,
            'data' => collect($orders->items())->map(
                fn (WorkOrder $w) => $this->summary($w)
            ),
            'pagination' => [
                'current_page' => $orders->currentPage(),
                'per_page' => $orders->perPage(),
                'total' => $orders->total(),
                'last_page' => $orders->lastPage(),
            ],
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | GET /api/work-orders/{id}
    |--------------------------------------------------------------------------
    */

    public function show(Request $request, int $id)
    {
        $workOrder = WorkOrder::query()->find($id);

        if (!$workOrder) {
            return response()->json([
                'success' => false,
                'message' => 'Work Order tidak ditemukan.',
            ], 404);
        }

        /*
        | PRODUKSI tidak boleh melihat WO milik orang lain.
        */

        $user = $request->user();

        if (
            \App\Support\DepartmentAccess::normalizeRole($user->role ?? '') === \App\Support\DepartmentAccess::PRODUKSI
            && strtolower(trim((string) $workOrder->dibuat_oleh))
                !== strtolower(trim((string) $user->name))
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak berhak melihat Work Order ini.',
            ], 403);
        }

        $histories = WorkOrderStatusHistory::query()
            ->where('work_order_id', $workOrder->id)
            ->orderBy('started_at')
            ->get([
                'id',
                'status',
                'alasan',
                'keterangan',
                'started_at',
                'ended_at',
                'created_by',
            ]);

        return response()->json([
            'success' => true,
            'data' => array_merge($this->summary($workOrder), [
                'detail' => $workOrder->toArray(),
                'histories' => $histories,
            ]),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | PATCH /api/work-orders/{id}/status
    |--------------------------------------------------------------------------
    */

    public function updateStatus(Request $request, int $id)
    {
        $data = $request->validate([
            'status' => ['required', 'string', Rule::in(self::STATUSES)],
            'keterangan' => ['nullable', 'string'],
            'alasan_pending' => ['nullable', 'string', 'max:100'],
        ]);

        $workOrder = WorkOrder::query()->find($id);

        if (!$workOrder) {
            return response()->json([
                'success' => false,
                'message' => 'Work Order tidak ditemukan.',
            ], 404);
        }

        $newStatus = strtoupper(trim($data['status']));

        $oldStatus = strtoupper(trim((string) $workOrder->status));

        /*
        | PENDING wajib menyertakan alasan.
        */

        if ($newStatus === 'PENDING' && empty($data['alasan_pending'])) {
            return response()->json([
                'success' => false,
                'message' => 'Status PENDING wajib menyertakan alasan_pending.',
            ], 422);
        }

        /*
        | Update kolom waktu pendukung bila relevan.
        */

        $update = ['status' => $newStatus];

        if (
            $newStatus === 'IN PROGRESS'
            && empty($workOrder->tanggal_mulai_perbaikan)
        ) {
            $update['tanggal_mulai_perbaikan'] = now()->toDateString();
            $update['jam_mulai_perbaikan'] = now()->format('H:i');
        }

        if ($newStatus === 'CLOSE') {
            $update['tanggal_selesai_perbaikan'] = now()->toDateString();
            $update['jam_selesai_perbaikan'] = now()->format('H:i');
        }

        $workOrder->update($update);

        /*
        | Histori transisi status (tidak boleh dihapus).
        */

        if ($oldStatus !== $newStatus) {

            WorkOrderStatusHistory::recordTransition(
                $workOrder,
                $newStatus,
                $newStatus === 'PENDING'
                    ? ($data['alasan_pending'] ?? null)
                    : null,
                $data['keterangan'] ?? null
            );
        }

        return response()->json([
            'success' => true,
            'message' => "Status Work Order diperbarui menjadi {$newStatus}.",
            'data' => $this->summary($workOrder->refresh()),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | RINGKASAN DATA WO
    |--------------------------------------------------------------------------
    */

    private function summary(WorkOrder $w): array
    {
        return [
            'id' => $w->id,
            'no_wo' => $w->no_wo,
            'kategori' => $w->kategori,
            'job' => $w->job,
            'departemen' => $w->departemen,
            'area' => $w->area,
            'mesin' => $w->mesin,
            'dibuat_oleh' => $w->dibuat_oleh,
            'status' => $w->status,
            'prioritas' => $w->priority,
            'tanggal_kerusakan' => $w->tanggal_kerusakan
                ? (string) $w->tanggal_kerusakan
                : null,
            'created_at' => optional($w->created_at)->toIso8601String(),
        ];
    }
}
