<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ApiAuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | POST /api/login
    |--------------------------------------------------------------------------
    */

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'device_name' => ['nullable', 'string', 'max:100'],
        ]);

        $user = User::query()
            ->where('email', $data['email'])
            ->first();

        if (
            !$user
            || !Hash::check($data['password'], (string) $user->password)
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau password salah.',
            ], 401);
        }

        if (strtolower(trim((string) $user->status)) !== 'aktif') {
            return response()->json([
                'success' => false,
                'message' => 'Akun dinonaktifkan. Hubungi Administrator.',
            ], 403);
        }

        $user->forceFill(['last_login_at' => now()])->save();

        $token = $user->issueApiToken(
            $data['device_name'] ?? 'react-native'
        );

        return response()->json([
            'success' => true,
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'bagian' => $user->bagian,
                'role' => $user->role,
            ],
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | POST /api/logout
    |--------------------------------------------------------------------------
    */

    public function logout(Request $request)
    {
        $plain = $request->bearerToken();

        if ($plain) {

            \App\Models\ApiToken::query()
                ->where('token', hash('sha256', $plain))
                ->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil.',
        ]);
    }
}
