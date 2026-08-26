<?php

namespace App\Http\Controllers\WorkOrder;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | INDEX - NOTIFICATION LIST (FULL PAGE)
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $user = auth()->user();

        $notifications = Notification::query()
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->paginate(20);

        $unreadCount = Notification::query()
            ->where('user_id', $user->id)
            ->where('status', 'UNREAD')
            ->count();

        return view(
            'notifications.index',
            compact(
                'notifications',
                'unreadCount'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | MARK AS READ
    |--------------------------------------------------------------------------
    */

    public function markAsRead(Request $request, $id)
    {
        $user = auth()->user();

        $notification = Notification::query()
            ->where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if ($notification && $notification->status === 'UNREAD') {

            $notification->update([
                'status' => 'READ',
                'read_at' => now(),
            ]);
        }

        if ($request->ajax() || $request->wantsJson()) {

            return response()->json([
                'success' => true,
            ]);
        }

        return redirect()->back();
    }


    /*
    |--------------------------------------------------------------------------
    | MARK ALL AS READ
    |--------------------------------------------------------------------------
    */

    public function markAllAsRead(Request $request)
    {
        $user = auth()->user();

        Notification::query()
            ->where('user_id', $user->id)
            ->where('status', 'UNREAD')
            ->update([
                'status' => 'READ',
                'read_at' => now(),
            ]);

        if ($request->ajax() || $request->wantsJson()) {

            return response()->json([
                'success' => true,
            ]);
        }

        return redirect()->back();
    }


    /*
    |--------------------------------------------------------------------------
    | UNREAD LIST (AJAX FOR DROPDOWN)
    |--------------------------------------------------------------------------
    */

    public function unread(Request $request)
    {
        $user = auth()->user();

        $notifications = Notification::query()
            ->where('user_id', $user->id)
            ->where('status', 'UNREAD')
            ->orderByDesc('created_at')
            ->limit(15)
            ->get();

        $unreadCount = Notification::query()
            ->where('user_id', $user->id)
            ->where('status', 'UNREAD')
            ->count();

        return response()->json([
            'success' => true,
            'unread_count' => $unreadCount,
            'data' => $notifications->map(
                function (Notification $n) {

                    $woUrl = null;

                    if (
                        $n->work_order_id
                    ) {

                        $role = \App\Support\DepartmentAccess::normalizeRole(
                            auth()->user()->role ?? ''
                        );

                        if ($role === \App\Support\DepartmentAccess::ADMINISTRATOR) {

                            $woUrl = route(
                                'work-orders.admin.show',
                                $n->work_order_id
                            );

                        } elseif ($role === \App\Support\DepartmentAccess::MAINTENANCE) {

                            $woUrl = route(
                                'work-orders.maintenance.show',
                                $n->work_order_id
                            );

                        } else {

                            $woUrl = route(
                                'work-orders.show',
                                $n->work_order_id
                            );
                        }
                    }

                    return [
                        'id' => $n->id,
                        'title' => $n->title,
                        'message' => $n->message,
                        'type' => $n->type,
                        'work_order_id' => $n->work_order_id,
                        'wo_url' => $woUrl,
                        'created_at' => $n->created_at
                            ? $n->created_at->diffForHumans()
                            : '',
                    ];
                }
            ),
        ]);
    }
}
