<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\Auth\LoginController;

/*
|--------------------------------------------------------------------------
| DASHBOARD
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Dashboard\ManagerDashboardController;

/*
|--------------------------------------------------------------------------
| USER
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\User\UserManagementController;

/*
|--------------------------------------------------------------------------
| SETTINGS / ACTIVITY
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\Settings\SettingsController;
use App\Http\Controllers\Activity\ActivityLogController;

/*
|--------------------------------------------------------------------------
| SYSTEM GUARD
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\SystemGuard\SystemGuardController;

/*
|--------------------------------------------------------------------------
| WORK ORDER
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\WorkOrder\UserWorkOrderController;
use App\Http\Controllers\WorkOrder\MaintenanceWorkOrderController;
use App\Http\Controllers\WorkOrder\AdminWorkOrderController;
use App\Http\Controllers\WorkOrder\NotificationController;

/*
|--------------------------------------------------------------------------
| MASTER / MACHINE
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\Machine\MachineController;
use App\Http\Controllers\Machine\AreaController;
use App\Http\Controllers\Machine\MachineSparepartController;

/*
|--------------------------------------------------------------------------
| INVENTORY
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\Inventory\BarangController;
use App\Http\Controllers\Inventory\SatuanController;
use App\Http\Controllers\Inventory\BarangMasukController;
use App\Http\Controllers\Inventory\BarangKeluarController;
use App\Http\Controllers\Inventory\PurchaseRequestController;
use App\Http\Controllers\Inventory\LaporanHarianController;
use App\Http\Controllers\Inventory\RataRataPemakaianController;

/*
|--------------------------------------------------------------------------
| AI
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\AiAssistant\AiAssistantController;

/*
|--------------------------------------------------------------------------
| MIDDLEWARE
|--------------------------------------------------------------------------
*/

use App\Http\Middleware\UserAccess;


/*
|--------------------------------------------------------------------------
| AUTHENTICATED
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth',
    UserAccess::class,
])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | DASHBOARD
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/dashboard',
        [DashboardController::class, 'index']
    )->name('dashboard');


    /*
    |--------------------------------------------------------------------------
    | INVENTORY HOME
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/inventory',
        function () {
            return view('inventory.beranda.index');
        }
    )->name('inventory.index');


    /*
    |--------------------------------------------------------------------------
    | INVENTORY - ME & PREV
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/inventory/me-prev',
        function () {
            return redirect()->route(
                'barang.index',
                [
                    'bucket' => 'me_prev',
                ]
            );
        }
    )->name('inventory.me_prev');


    /*
    |--------------------------------------------------------------------------
    | INVENTORY - PREV
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/inventory/prev',
        function () {
            return redirect()->route(
                'barang.index',
                [
                    'bucket' => 'prev',
                ]
            );
        }
    )->name('inventory.prev');


    /*
    |--------------------------------------------------------------------------
    | MASTER
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/master',
        function () {
            return view('master.beranda.index');
        }
    )->name('master.index');


    /*
    |--------------------------------------------------------------------------
    | PROFILE
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/profil',
        [ProfileController::class, 'index']
    )->name('profile');


    Route::put(
        '/profil',
        [ProfileController::class, 'update']
    )->name('profile.update');


    /*
    |--------------------------------------------------------------------------
    | FOTO PROFILE
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/profil/foto',
        [ProfileController::class, 'photo']
    )->name('profile.photo');


    Route::match(
        ['post', 'put'],
        '/profil/foto',
        [ProfileController::class, 'updatePhoto']
    )->name('profile.photo.update');


    /*
    |--------------------------------------------------------------------------
    | PASSWORD
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/ubah-password',
        [ProfileController::class, 'password']
    )->name('password.edit');


    Route::put(
        '/ubah-password',
        [ProfileController::class, 'updatePassword']
    )->name('password.update');


    /*
    |--------------------------------------------------------------------------
    | MANAJEMEN USER
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/manajemen-user',
        [UserManagementController::class, 'index']
    )->name('users.index');


    Route::post(
        '/manajemen-user',
        [UserManagementController::class, 'store']
    )->name('users.store');


    Route::patch(
        '/manajemen-user/{user}',
        [UserManagementController::class, 'update']
    )->name('users.update');


    Route::delete(
        '/manajemen-user/{user}',
        [UserManagementController::class, 'destroy']
    )->name('users.destroy');


    Route::patch(
        '/manajemen-user/{user}/status',
        [UserManagementController::class, 'updateStatus']
    )->name('users.status');


    /*
    |--------------------------------------------------------------------------
    | ROLE / BAGIAN
    |--------------------------------------------------------------------------
    */

    Route::post(
        '/manajemen-user/role',
        [UserManagementController::class, 'storeRole']
    )->name('users.role.store');


    Route::post(
        '/manajemen-user/system-role',
        [UserManagementController::class, 'storeSystemRole']
    )->name('users.system-role.store');


    Route::patch(
        '/manajemen-user/system-role/{role}',
        [UserManagementController::class, 'updateSystemRole']
    )->name('users.system-role.update');


    Route::delete(
        '/manajemen-user/system-role/{role}',
        [UserManagementController::class, 'deleteSystemRole']
    )->name('users.system-role.delete');


    Route::patch(
        '/manajemen-user/role/{role}',
        [UserManagementController::class, 'updateRole']
    )->name('users.role.update');


    Route::delete(
        '/manajemen-user/role/{role}',
        [UserManagementController::class, 'deleteRole']
    )->name('users.role.delete');


    /*
    |--------------------------------------------------------------------------
    | SETTINGS
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/pengaturan',
        [SettingsController::class, 'index']
    )->name('settings.index');


    Route::put(
        '/pengaturan',
        [SettingsController::class, 'update']
    )->name('settings.update');


    /*
    |--------------------------------------------------------------------------
    | ACTIVITY LOG
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/log-aktivitas',
        [ActivityLogController::class, 'index']
    )->name('activity.index');


    /*
    |--------------------------------------------------------------------------
    | SYSTEM GUARD
    |--------------------------------------------------------------------------
    */

    Route::prefix('system-guard')
        ->name('system-guard.')
        ->controller(SystemGuardController::class)
        ->group(function () {

            Route::get('/', 'dashboard')
                ->name('dashboard');

            Route::get('/api/status', 'apiStatus')
                ->name('api.status');

            Route::get('/api/poll', 'apiPoll')
                ->name('api.poll');

            Route::get('/incident/{incidentId}', 'incidentDetail')
                ->name('incident.detail');

            Route::get('/check', 'dashboard')
                ->name('check');

        });


    /*
    |--------------------------------------------------------------------------
    | WORK ORDER - MAINTENANCE
    |--------------------------------------------------------------------------
    |
    | Semua WO yang ditujukan ke Maintenance diproses di sini.
    |
    */

    Route::get(
        '/work-orders/maintenance',
        [
            MaintenanceWorkOrderController::class,
            'index',
        ]
    )->name('work-orders.maintenance');


    Route::get(
        '/work-orders/maintenance/mekanik',
        [
            MaintenanceWorkOrderController::class,
            'indexMekanik',
        ]
    )->name('work-orders.maintenance.mekanik');


    Route::get(
        '/work-orders/maintenance/prev',
        [
            MaintenanceWorkOrderController::class,
            'indexPrev',
        ]
    )->name('work-orders.maintenance.prev');


    /*
    |--------------------------------------------------------------------------
    | LAPORAN WORK ORDER - MAINTENANCE
    |--------------------------------------------------------------------------
    |
    | HARUS sebelum route /{work_order}.
    |
    */

    Route::get(
        '/work-orders/maintenance/laporan',
        [
            MaintenanceWorkOrderController::class,
            'report',
        ]
    )->name('work-orders.maintenance.report');


    /*
    |--------------------------------------------------------------------------
    | WORK ORDER - MAINTENANCE DETAIL / TINDAK LANJUT
    |--------------------------------------------------------------------------
    */

    Route::prefix('work-orders/maintenance')
        ->controller(MaintenanceWorkOrderController::class)
        ->group(function () {

            Route::get(
                '/{work_order}',
                'show'
            )->name('work-orders.maintenance.show');


            Route::get(
                '/{work_order}/edit',
                'edit'
            )->name('work-orders.maintenance.edit');


            Route::put(
                '/{work_order}',
                'update'
            )->name('work-orders.maintenance.update');


            Route::delete(
                '/{work_order}',
                'destroy'
            )->name('work-orders.maintenance.destroy');

        });


    /*
    |--------------------------------------------------------------------------
    | WORK ORDER - ADMINISTRATOR
    |--------------------------------------------------------------------------
    */

    Route::prefix('work-orders/admin')
        ->name('work-orders.admin.')
        ->controller(AdminWorkOrderController::class)
        ->group(function () {

            Route::get(
                '/',
                'index'
            )->name('index');


            Route::get(
                '/create',
                'create'
            )->name('create');


            Route::post(
                '/',
                'store'
            )->name('store');


            Route::get(
                '/laporan',
                'report'
            )->name('report');


            Route::get(
                '/{work_order}',
                'show'
            )->name('show');


            Route::get(
                '/{work_order}/edit',
                'edit'
            )->name('edit');


            Route::put(
                '/{work_order}',
                'update'
            )->name('update');


            Route::patch(
                '/{work_order}',
                'update'
            )->name('update.patch');


            Route::delete(
                '/{work_order}',
                'destroy'
            )->name('destroy');

        });


    /*
    |--------------------------------------------------------------------------
    | ALIAS LAPORAN
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/work-orders/laporan',
        function () {
            return redirect()->route(
                'work-orders.admin.report'
            );
        }
    )->name('work-orders.report');


    /*
    |--------------------------------------------------------------------------
    | WORK ORDER - USER
    |--------------------------------------------------------------------------
    |
    | Semua route User tetap memakai auth + UserAccess,
    | KECUALI DELETE.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | CHECK NO WO AVAILABILITY
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/work-orders/check-no-wo',
        [UserWorkOrderController::class, 'checkNoWo']
    )->name('work-orders.check-no-wo');


    /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/work-orders/create',
        [UserWorkOrderController::class, 'create']
    )->name('work-orders.create');


    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */

    Route::post(
        '/work-orders',
        [UserWorkOrderController::class, 'store']
    )->name('work-orders.store');


    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/work-orders',
        [UserWorkOrderController::class, 'index']
    )->name('work-orders.index');


    /*
    |--------------------------------------------------------------------------
    | USER SHOW
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/work-orders/{work_order}',
        [UserWorkOrderController::class, 'show']
    )->name('work-orders.show');


    /*
    |--------------------------------------------------------------------------
    | USER EDIT
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/work-orders/{work_order}/edit',
        [UserWorkOrderController::class, 'edit']
    )->name('work-orders.edit');


    /*
    |--------------------------------------------------------------------------
    | USER UPDATE
    |--------------------------------------------------------------------------
    */

    Route::put(
        '/work-orders/{work_order}',
        [UserWorkOrderController::class, 'update']
    )->name('work-orders.update');


    /*
    |--------------------------------------------------------------------------
    | USER PATCH
    |--------------------------------------------------------------------------
    */

    Route::patch(
        '/work-orders/{work_order}',
        [UserWorkOrderController::class, 'update']
    )->name('work-orders.update.patch');


    /*
    |--------------------------------------------------------------------------
    | MASTER - AREA
    |--------------------------------------------------------------------------
    */

    Route::resource(
        'areas',
        AreaController::class
    );


    /*
    |--------------------------------------------------------------------------
    | MASTER - MESIN
    |--------------------------------------------------------------------------
    */

    Route::resource(
        'machines',
        MachineController::class
    )->except([
        'show',
    ]);


    /*
    |--------------------------------------------------------------------------
    | MASTER - MESIN SPAREPART
    |--------------------------------------------------------------------------
    */

    Route::resource(
        'machine-spareparts',
        MachineSparepartController::class
    )->only([
        'index',
        'store',
        'update',
        'destroy',
    ]);


    /*
    |--------------------------------------------------------------------------
    | INVENTORY - BARANG
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/barang',
        [BarangController::class, 'index']
    )->name('barang.index');


    Route::post(
        '/barang',
        [BarangController::class, 'store']
    )->name('barang.store');


    Route::patch(
        '/barang/{barang}',
        [BarangController::class, 'update']
    )->name('barang.update');


    Route::delete(
        '/barang/{barang}',
        [BarangController::class, 'destroy']
    )->name('barang.destroy');


    /*
    |--------------------------------------------------------------------------
    | INVENTORY - SATUAN
    |--------------------------------------------------------------------------
    */

    Route::post(
        '/barang/satuan',
        [SatuanController::class, 'store']
    )->name('satuan.store');


    Route::patch(
        '/barang/satuan/{satuan}',
        [SatuanController::class, 'update']
    )->name('satuan.update');


    Route::delete(
        '/barang/satuan/{satuan}',
        [SatuanController::class, 'destroy']
    )->name('satuan.destroy');


    /*
    |--------------------------------------------------------------------------
    | INVENTORY - RESTOCK
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/restock',
        [BarangController::class, 'restock']
    )->name('barang.restock');


    /*
    |--------------------------------------------------------------------------
    | INVENTORY - PURCHASE REQUEST
    |--------------------------------------------------------------------------
    */

    Route::resource(
        'purchase-requests',
        PurchaseRequestController::class
    );


    /*
    |--------------------------------------------------------------------------
    | INVENTORY - LAPORAN HARIAN
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/laporan-harian',
        [LaporanHarianController::class, 'index']
    )->name('laporan-harian.index');


    /*
    |--------------------------------------------------------------------------
    | INVENTORY - RATA-RATA PEMAKAIAN
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/rata-rata-pemakaian',
        [RataRataPemakaianController::class, 'index']
    )->name('rata-rata-pemakaian.index');

});


/*
|--------------------------------------------------------------------------
| USER WORK ORDER - DELETE
|--------------------------------------------------------------------------
|
| PENTING:
| Route ini sengaja berada di luar UserAccess.
|
| Kalau dimasukkan ke UserAccess, request DELETE
| sebelumnya terkena 403:
|
| "User tidak memiliki akses untuk menghapus Work Order."
|
| Controller destroy() tetap melakukan pengecekan
| pemilik Work Order.
|
*/

Route::delete(
    '/work-orders/{work_order}',
    [UserWorkOrderController::class, 'destroy']
)
    ->middleware([
        'auth',
    ])
    ->name('work-orders.destroy');


/*
|--------------------------------------------------------------------------
| INVENTORY - BARANG MASUK
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth',
    UserAccess::class,
])
    ->prefix('barang-masuk')
    ->name('barang-masuk.')
    ->controller(BarangMasukController::class)
    ->group(function () {

        Route::get(
            '/',
            'index'
        )->name('index');


        Route::post(
            '/',
            'store'
        )->name('store');


        Route::get(
            '/{barangMasuk}',
            'show'
        )->name('show');


        Route::get(
            '/{barangMasuk}/edit',
            'edit'
        )->name('edit');


        Route::put(
            '/{barangMasuk}',
            'update'
        )->name('update');


        Route::delete(
            '/{barangMasuk}',
            'destroy'
        )->name('destroy');

    });


/*
|--------------------------------------------------------------------------
| INVENTORY - BARANG KELUAR
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth',
    UserAccess::class,
])
    ->prefix('barang-keluar')
    ->name('barang-keluar.')
    ->controller(BarangKeluarController::class)
    ->group(function () {

        Route::get(
            '/',
            'index'
        )->name('index');


        Route::get(
            '/create',
            'create'
        )->name('create');


        Route::post(
            '/',
            'store'
        )->name('store');


        Route::get(
            '/area/{areaId}/machines',
            'machinesByArea'
        )->name('machines');


        Route::get(
            '/barang/{id}',
            'barang'
        )->name('barang');


        Route::get(
            '/{barangKeluar}',
            'show'
        )->name('show');


        Route::patch(
            '/{barangKeluar}/cancel',
            'cancel'
        )->name('cancel');

    });

/*
|--------------------------------------------------------------------------
| ROOT
|--------------------------------------------------------------------------
|
| Arahkan root ke login / dashboard.
|
*/

Route::get(
    '/',
    function () {
        return auth()->check()
            ? redirect()->route('dashboard')
            : redirect()->route('login');
    }
)->name('home');


/*
|--------------------------------------------------------------------------
| LOGIN
|--------------------------------------------------------------------------
*/

Route::get(
    '/login',
    [LoginController::class, 'showLogin']
)->name('login');


Route::get(
    '/login/users',
    [LoginController::class, 'getUsersByBagian']
)->name('login.users');


Route::post(
    '/login',
    [LoginController::class, 'login']
)->name('login.process');


/*
|--------------------------------------------------------------------------
| LOGOUT
|--------------------------------------------------------------------------
*/

Route::post(
    '/logout',
    [LoginController::class, 'logout']
)
    ->middleware('auth')
    ->name('logout');


/*
|--------------------------------------------------------------------------
| AI ASSISTANT
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth',
    UserAccess::class,
])->group(function () {

    Route::post(
        '/ai-assistant/ask',
        [AiAssistantController::class, 'ask']
    )->name('ai-assistant.ask');


    /*
    |--------------------------------------------------------------------------
    | NOTIFICATIONS
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/notifications',
        [NotificationController::class, 'index']
    )->name('notifications.index');


    Route::get(
        '/notifications/unread',
        [NotificationController::class, 'unread']
    )->name('notifications.unread');


    Route::post(
        '/notifications/{notification}/read',
        [NotificationController::class, 'markAsRead']
    )->name('notifications.mark-read');


    Route::post(
        '/notifications/read-all',
        [NotificationController::class, 'markAllAsRead']
    )->name('notifications.mark-all-read');

});


/*
|--------------------------------------------------------------------------
| DASHBOARD MANAGER
|--------------------------------------------------------------------------
|
| Manager hanya monitoring dashboard.
|
*/

Route::get(
    '/dashboard/manager',
    [
        ManagerDashboardController::class,
        'index',
    ]
)
    ->middleware([
        'auth',
        UserAccess::class,
    ])
    ->name('dashboard.manager');

/*
|--------------------------------------------------------------------------
| DETAIL MONITORING (LIHAT DETAIL) - READ ONLY
|--------------------------------------------------------------------------
|
| Endpoint JSON khusus MANAGER untuk melihat data di balik
| KPI / grafik Dashboard Manager. Tanpa akses CRUD.
|
*/

Route::get(
    '/dashboard/manager/detail',
    [
        ManagerDashboardController::class,
        'detail',
    ]
)
    ->middleware([
        'auth',
        UserAccess::class,
    ])
    ->name('dashboard.manager.detail');

/*
|--------------------------------------------------------------------------
| DATA ANALITIK DASHBOARD MANAGER - READ ONLY
|--------------------------------------------------------------------------
*/

Route::get(
    '/dashboard/manager/data',
    [
        ManagerDashboardController::class,
        'data',
    ]
)
    ->middleware([
        'auth',
        UserAccess::class,
    ])
    ->name('dashboard.manager.data');
