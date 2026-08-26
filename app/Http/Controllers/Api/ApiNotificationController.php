<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;

use Illuminate\Http\Request;

class ApiNotificationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | GET /api/notifications
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $user = $request->user();

        $notifications = Notification::query()
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->limit(20)
            ->get([
                'id',
                'work_order_id',
                'type',
                'title',
                'message',
                'status',
                'read_at',
                'deadline_at',
                'created_at',
            ]);

        $unreadCount = Notification::query()
            ->where('user_id', $user->id)
            ->whereNull('read_at')
            ->count();

        return response()->json([
            'success' => true,
            'unread_count' => $unreadCount,
            'data' => $notifications->map(
                fn (Notification $n) => [
                    'id' => $n->id,
                    'work_order_id' => $n->work_order_id,
                    'type' => $n->type,
                    'title' => $n->title,
                    'message' => $n->message,
                    'status' => $n->status,
                    'read' => $n->read_at !== null,
                    'deadline_at' => optional($n->deadline_at)->toIso8601String(),
                    'created_at' => optional($n->created_at)->toIso8601String(),
                ]
            ),
        ]);
    }
}
