<?php

namespace App\Http\Middleware;

use App\Support\DepartmentAccess;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserAccess
{
    public function handle(
        Request $request,
        Closure $next
    ): Response {

        $user = $request->user();

        /*
        |--------------------------------------------------------------------------
        | BELUM LOGIN
        |--------------------------------------------------------------------------
        */

        if (!$user) {
            return redirect()->route('login');
        }


        /*
        |--------------------------------------------------------------------------
        | NORMALISASI ROLE
        |--------------------------------------------------------------------------
        */

        $role = DepartmentAccess::normalizeRole(
            $user->role ?? ''
        );


        /*
        |--------------------------------------------------------------------------
        | ROUTE BERSAMA SEMUA ROLE
        |--------------------------------------------------------------------------
        |
        | Profil, password dan AI Assistant tersedia untuk
        | semua role sesuai hak masing-masing.
        |
        */

        $commonRoutes = [

            /*
            | Profil
            */
            'profile',
            'profile.update',
            'profile.photo',
            'profile.photo.update',

            /*
            | Password
            */
            'password.edit',
            'password.update',

            /*
            | AI Assistant
            */
            'ai-assistant.ask',

            'notifications.index',
            'notifications.unread',
            'notifications.mark-read',
            'notifications.mark-all-read',
        ];


        /*
        |--------------------------------------------------------------------------
        | ADMINISTRATOR - FULL ACCESS
        |--------------------------------------------------------------------------
        */

        if (
            in_array(
                $role,
                [
                    DepartmentAccess::ADMINISTRATOR,
                ],
                true
            )
        ) {
            return $next($request);
        }


        /*
        |--------------------------------------------------------------------------
        | MANAGER - MONITORING SAJA
        |--------------------------------------------------------------------------
        |
        | Manager hanya memiliki Dashboard Manager.
        | Tidak ada menu administrasi lain.
        |
        */

        if ($role === DepartmentAccess::MANAGER) {

            if (
                $request->routeIs(
                    ...array_merge(
                        $commonRoutes,
                        [

                            /*
                            | Dashboard
                            |
                            | Diizinkan agar DashboardController dapat
                            | mengarahkan Manager langsung ke
                            | Dashboard Manager.
                            */
                            'dashboard',

                            /*
                            | Dashboard Manager
                            */
                            'dashboard.manager',

                            /*
                            | Data analitik & detail monitoring
                            | Dashboard Manager (read-only,
                            | tanpa akses CRUD).
                            */
                            'dashboard.manager.detail',
                            'dashboard.manager.data',
                        ]
                    )
                )
            ) {
                return $next($request);
            }

            abort(
                403,
                'Manager hanya memiliki akses ke Dashboard.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | DIREKTUR - MONITORING + Akses Maintenance
        |--------------------------------------------------------------------------
        |
        | Direktur memiliki akses ke Dashboard,
        | Work Order seluruh departemen,
        | Inventory seluruh departemen,
        | tetapi tidak ke Manajemen User.
        |
        */

        if ($role === DepartmentAccess::DIREKTUR) {

            if (
                $request->routeIs(
                    ...array_merge(
                        $commonRoutes,
                        [

                            'dashboard',

                            'work-orders.index',
                            'work-orders.create',
                            'work-orders.store',
                            'work-orders.show',
                            'work-orders.edit',
                            'work-orders.update',
                            'work-orders.update.patch',
                            'work-orders.destroy',

                            'work-orders.admin.index',
                            'work-orders.admin.show',
                            'work-orders.admin.create',
                            'work-orders.admin.store',
                            'work-orders.admin.edit',
                            'work-orders.admin.update',
                            'work-orders.admin.update.patch',
                            'work-orders.admin.delete',
                            'work-orders.admin.follow-up',
                            'work-orders.admin.all-validation',
                            'work-orders.admin.photos',

                            'work-orders.maintenance',
                            'work-orders.maintenance.show',
                            'work-orders.maintenance.create',
                            'work-orders.maintenance.store',
                            'work-orders.maintenance.edit',
                            'work-orders.maintenance.update',
                            'work-orders.maintenance.update.patch',
                            'work-orders.maintenance.delete',
                            'work-orders.maintenance.mekanik',
                            'work-orders.maintenance.prev',
                            'work-orders.maintenance.follow-up',
                            'work-orders.maintenance.report',
                            'work-orders.maintenance.all-validation',
                            'work-orders.maintenance.photos',

                            'inventory.*',
                            'master.*',
                            'notifications.*',
                        ]
                    )
                )
            ) {
                return $next($request);
            }

            if (
                $request->routeIs('users.*', 'settings.*', 'activity.*')
            ) {
                abort(
                    403,
                    'Halaman ini khusus Administrator.'
                );
            }

            return $next($request);
        }


        /*
        |--------------------------------------------------------------------------
        | DASHBOARD MANAGER KHUSUS MANAGER
        |--------------------------------------------------------------------------
        |
        | Role selain ADMIN dan MANAGER tidak boleh
        | mengakses Dashboard Manager maupun endpoint
        | detail monitoring-nya.
        |
        */

        if (
            $request->routeIs(
                'dashboard.manager',
                'dashboard.manager.detail',
                'dashboard.manager.data'
            )
        ) {
            abort(
                403,
                'Halaman ini khusus Manager.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | MAINTENANCE - CUSTOMER SERVICE MAINTENANCE
        |--------------------------------------------------------------------------
        |
        | Maintenance menangani seluruh Work Order,
        | Inventory dan Master sebagai pendukung pekerjaan.
        |
        */

        if (
            in_array(
                $role,
                [
                    DepartmentAccess::MAINTENANCE,
                    DepartmentAccess::MEKANIK_MAINT,
                    DepartmentAccess::PREV_MAINT,
                ],
                true
            )
        ) {

            if (
                $request->routeIs(
                    'users.*',
                    'settings.*',
                    'activity.*',
                    'work-orders.admin.*'
                )
            ) {
                abort(
                    403,
                    'Halaman ini khusus Administrator.'
                );
            }

            if (
                $role === DepartmentAccess::MEKANIK_MAINT
                && $request->routeIs(
                    'work-orders.maintenance.prev',
                    'inventory.prev'
                )
            ) {
                abort(
                    403,
                    'Anda tidak memiliki akses ke data Prev-Maint.'
                );
            }

            if (
                $role === DepartmentAccess::PREV_MAINT
                && $request->routeIs(
                    'work-orders.maintenance.mekanik',
                    'inventory.me_prev'
                )
            ) {
                abort(
                    403,
                    'Anda tidak memiliki akses ke data Mekanik / Maintenance.'
                );
            }

            return $next($request);
        }


        /*
        |--------------------------------------------------------------------------
        | PRODUKSI - PEMBUAT WORK ORDER
        |--------------------------------------------------------------------------
        |
        | Produksi boleh:
        |
        | Dashboard
        | Profil
        | Password
        | Work Order (CRUD sendiri)
        |
        */

        if ($role === DepartmentAccess::PRODUKSI) {

            if (
                $request->routeIs(
                    ...array_merge(
                        $commonRoutes,
                        [

                            'dashboard',

                            'work-orders.index',
                            'work-orders.create',
                            'work-orders.store',
                            'work-orders.show',
                            'work-orders.edit',
                            'work-orders.update',
                            'work-orders.update.patch',
                        ]
                    )
                )
            ) {
                return $next($request);
            }

            if (
                $request->routeIs('work-orders.destroy')
            ) {
                abort(
                    403,
                    'Anda tidak memiliki akses untuk menghapus Work Order.'
                );
            }

            if (
                $request->routeIs('users.*', 'settings.*', 'activity.*', 'work-orders.admin.*', 'work-orders.maintenance.*', 'inventory.*', 'master.*')
            ) {
                abort(
                    403,
                    'Anda tidak memiliki hak akses ke halaman ini.'
                );
            }
        }


        /*
        |--------------------------------------------------------------------------
        | AKSES LAIN DITOLAK
        |--------------------------------------------------------------------------
        */

        abort(
            403,
            'Anda tidak memiliki hak akses ke halaman ini.'
        );
    }
}
