<?php

namespace App\Providers;

use App\Models\Inventory\Barang;
use App\Models\WorkOrder\WorkOrder;
use App\Policies\BarangPolicy;
use App\Policies\WorkOrderPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        require_once app_path('Support/helpers.php');
    }

    public function boot(): void
    {
        Gate::policy(WorkOrder::class, WorkOrderPolicy::class);
        Gate::policy(Barang::class, BarangPolicy::class);

        /*
        |--------------------------------------------------------------------------
        | HTTPS CLOUDFLARE / LAN
        |--------------------------------------------------------------------------
        |
        | - Saat request datang melalui Cloudflare (X-Forwarded-Proto: https),
        |   seluruh URL yang dihasilkan Laravel dipaksa HTTPS sehingga form
        |   login, asset, dan redirect tidak menghasilkan mixed content.
        | - Cookie session otomatis "secure" hanya pada request HTTPS,
        |   sehingga login tetap berfungsi juga via LAN/HTTP lokal.
        |
        */

        $request = request();

        $cfVisitorHttps = str_contains(
            (string) $request->header('CF-Visitor', ''),
            '"scheme":"https"'
        );

        if (
            $request->header('X-Forwarded-Proto') === 'https'
            || $cfVisitorHttps
        ) {

            \Illuminate\Support\Facades\URL::forceScheme('https');

            config([
                'session.secure' => true,
                'app.url' => $request->getSchemeAndHttpHost(),
            ]);

        } elseif ($request->isSecure()) {

            /*
            | HTTPS langsung (tanpa proxy): cookie tetap secure.
            */

            config(['session.secure' => true]);

        } else {

            /*
            | HTTP lokal / LAN: cookie tidak secure agar
            | login tetap berfungsi tanpa HTTPS.
            */

            config(['session.secure' => false]);

        }

        $migrationFolders = [

            /*
            |--------------------------------------------------------------------------
            | CORE
            |--------------------------------------------------------------------------
            */

            database_path(
                'migrations/Core'
            ),


            /*
            |--------------------------------------------------------------------------
            | USER
            |--------------------------------------------------------------------------
            */

            database_path(
                'migrations/User'
            ),


            /*
            |--------------------------------------------------------------------------
            | WORK ORDER
            |--------------------------------------------------------------------------
            */

            database_path(
                'migrations/WorkOrder'
            ),


            /*
            |--------------------------------------------------------------------------
            | API (REACT NATIVE)
            |--------------------------------------------------------------------------
            */

            database_path(
                'migrations/Api'
            ),


            /*
            |--------------------------------------------------------------------------
            | MACHINE
            |--------------------------------------------------------------------------
            */

            database_path(
                'migrations/Machine'
            ),


            /*
            |--------------------------------------------------------------------------
            | SETTINGS
            |--------------------------------------------------------------------------
            */

            database_path(
                'migrations/Settings'
            ),


            /*
            |--------------------------------------------------------------------------
            | ACTIVITY
            |--------------------------------------------------------------------------
            */

            database_path(
                'migrations/Activity'
            ),


            /*
            |--------------------------------------------------------------------------
            | INVENTORY
            |--------------------------------------------------------------------------
            */

            database_path(
                'migrations/Inventory'
            ),


            /*
            |--------------------------------------------------------------------------
            | NOTIFICATION
            |--------------------------------------------------------------------------
            */

            database_path(
                'migrations/Notif'
            ),


            /*
            |--------------------------------------------------------------------------
            | SYSTEM GUARD
            |--------------------------------------------------------------------------
            */

            database_path(
                'migrations/SystemGuard'
            ),

        ];


        foreach (
            $migrationFolders as $folder
        ) {

            if (
                is_dir($folder)
            ) {

                $this->loadMigrationsFrom(
                    $folder
                );

            }

        }
    }
}