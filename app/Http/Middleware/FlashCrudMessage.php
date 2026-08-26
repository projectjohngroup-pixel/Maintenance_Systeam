<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FlashCrudMessage
{
    /**
     * Handle an incoming request.
     */
    public function handle(
        Request $request,
        Closure $next
    ): Response {

        $response = $next($request);


        /*
        |--------------------------------------------------------------------------
        | Hanya proses request yang mengubah data
        |--------------------------------------------------------------------------
        */

        if (
            $request->isMethod('POST') ||
            $request->isMethod('PUT') ||
            $request->isMethod('PATCH') ||
            $request->isMethod('DELETE')
        ) {

            /*
            |--------------------------------------------------------------------------
            | Hanya otomatis memberi pesan jika controller
            | belum memberikan pesan success/error sendiri.
            |--------------------------------------------------------------------------
            */

            if (
                $response->isRedirection() &&
                !session()->has('success') &&
                !session()->has('error') &&
                !session()->has('errors')
            ) {

                /*
                |--------------------------------------------------------------------------
                | SIMPAN
                |--------------------------------------------------------------------------
                */

                if (
                    $request->isMethod('POST')
                ) {

                    session()->flash(
                        'success',
                        'Data berhasil disimpan.'
                    );

                }


                /*
                |--------------------------------------------------------------------------
                | UPDATE
                |--------------------------------------------------------------------------
                */

                elseif (
                    $request->isMethod('PUT') ||
                    $request->isMethod('PATCH')
                ) {

                    session()->flash(
                        'success',
                        'Data berhasil diperbarui.'
                    );

                }


                /*
                |--------------------------------------------------------------------------
                | HAPUS
                |--------------------------------------------------------------------------
                */

                elseif (
                    $request->isMethod('DELETE')
                ) {

                    session()->flash(
                        'success',
                        'Data berhasil dihapus.'
                    );

                }

            }

        }


        return $response;
    }
}