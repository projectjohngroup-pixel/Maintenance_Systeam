<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckApiToken
{
    public function handle(
        Request $request,
        Closure $next
    ): Response {

        $user = \App\Models\ApiToken::resolveUser(
            $request->bearerToken()
        );

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Token tidak valid atau sudah kedaluwarsa.',
            ], 401);
        }

        /*
        | User nonaktif tidak boleh mengakses API.
        */

        if (strtolower(trim((string) $user->status)) !== 'aktif') {
            return response()->json([
                'success' => false,
                'message' => 'Akun dinonaktifkan.',
            ], 403);
        }

        auth()->setUser($user);

        return $next($request);
    }
}
