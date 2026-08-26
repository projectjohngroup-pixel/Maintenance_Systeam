<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

use App\Http\Middleware\FlashCrudMessage;
use App\Http\Middleware\GlobalLoading;
use App\Http\Middleware\UserAccess;
use App\Http\Middleware\UpdateLastActivity;

return Application::configure(
    basePath: dirname(__DIR__)
)
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )

    ->withMiddleware(function (
        Middleware $middleware
    ): void {

        /*
        |--------------------------------------------------------------------------
        | TRUSTED PROXY (CLOUDFLARE TUNNEL)
        |--------------------------------------------------------------------------
        |
        | Request dari Cloudflare Tunnel masuk sebagai HTTP lokal
        | (cloudflared -> web server). Tanpa konfigurasi ini Laravel
        | menganggap request tidak aman sehingga:
        |
        | - asset()/route() menghasilkan URL http:// (mixed content)
        | - form login submit ke http:// (peringatan "not secure")
        |
        | Dengan mempercayai proxy dan membaca forwarded headers,
        | Laravel mengenali skema HTTPS asli dari Cloudflare.
        |
        */

        $middleware->trustProxies(
            at: '*',
            headers: Request::HEADER_X_FORWARDED_FOR
                | Request::HEADER_X_FORWARDED_HOST
                | Request::HEADER_X_FORWARDED_PORT
                | Request::HEADER_X_FORWARDED_PROTO
                | Request::HEADER_X_FORWARDED_PREFIX
                | Request::HEADER_FORWARDED,
        );


        /*
        |--------------------------------------------------------------------------
        | ALIAS HAK AKSES USER
        |--------------------------------------------------------------------------
        */

        $middleware->alias([
            'user.access' => UserAccess::class,
        ]);


        /*
        |--------------------------------------------------------------------------
        | FLASH CRUD
        |--------------------------------------------------------------------------
        */

        $middleware->web(
            append: [
                FlashCrudMessage::class,
                UpdateLastActivity::class,
            ]
        );


        /*
        |--------------------------------------------------------------------------
        | GLOBAL LOADING
        |--------------------------------------------------------------------------
        */

        $middleware->append(
            GlobalLoading::class
        );

    })

    ->withExceptions(function (
        Exceptions $exceptions
    ): void {

        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) =>
                $request->is('api/*')
                ||
                $request->expectsJson(),
        );

    })

    ->create();