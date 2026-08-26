<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UpdateLastActivity
{
    /*
    |--------------------------------------------------------------------------
    | BATAS WAKTU ONLINE
    |--------------------------------------------------------------------------
    */

    public const ONLINE_THRESHOLD_MINUTES = 5;


    public function handle(
        Request $request,
        Closure $next
    ): Response {

        $response =
            $next($request);


        /*
        |--------------------------------------------------------------------------
        | UPDATE AKTIVITAS TERAKHIR
        |--------------------------------------------------------------------------
        |
        | Diperbarui maksimal sekali per menit per sesi
        | agar tidak membebani database.
        |
        */

        try {

            $user = $request->user();

            if ($user) {

                $now = now();

                $last = $user->last_activity_at;

                if (
                    !$last
                    || $last->diffInMinutes($now) >= 1
                ) {

                    $user->forceFill([
                        'last_activity_at' => $now,
                    ])->save();
                }
            }

        } catch (\Throwable $e) {

            /*
            | Kegagalan update aktivitas
            | tidak boleh mengganggu request.
            */
        }


        return $response;
    }
}
