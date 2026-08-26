<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Settings\SystemSetting;
use App\Models\Activity\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | HALAMAN LOGIN
    |--------------------------------------------------------------------------
    */

    public function showLogin()
    {
        /*
        |--------------------------------------------------------------------------
        | ROLE / BAGIAN DARI DATABASE
        |--------------------------------------------------------------------------
        | Tidak ada lagi role Produksi, Gudang, QC, dll yang ditulis
        | langsung di kode.
        */

        $value = SystemSetting::where(
            'key',
            'bagian_list'
        )->value('value');


        /*
        |--------------------------------------------------------------------------
        | CONVERT JSON KE ARRAY
        |--------------------------------------------------------------------------
        */

        $bagians = json_decode(
            $value ?? '[]',
            true
        );


        /*
        |--------------------------------------------------------------------------
        | PASTIKAN FORMAT VALID
        |--------------------------------------------------------------------------
        */

        if (!is_array($bagians)) {

            $bagians = [];

        }


        $bagians = array_values(
            array_unique(
                array_filter(
                    array_map(
                        function ($role) {

                            return is_string($role)
                                ? trim($role)
                                : null;

                        },
                        $bagians
                    )
                )
            )
        );


        /*
        |--------------------------------------------------------------------------
        | JIKA DATABASE BELUM PUNYA ROLE
        |--------------------------------------------------------------------------
        */

        if (empty($bagians)) {

            $bagians = [
                'Asisten System',
            ];

        }


        /*
        |--------------------------------------------------------------------------
        | USER AKTIF
        |--------------------------------------------------------------------------
        */

        $users = User::where(
                'status',
                'AKTIF'
            )
            ->select(
                'id',
                'name',
                'bagian'
            )
            ->orderBy(
                'name'
            )
            ->get();


        /*
        |--------------------------------------------------------------------------
        | PENGATURAN SISTEM
        |--------------------------------------------------------------------------
        */

        $settings = SystemSetting::pluck(
            'value',
            'key'
        );


        return view(
            'auth.login',
            compact(
                'bagians',
                'users',
                'settings'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | USER BERDASARKAN BAGIAN
    |--------------------------------------------------------------------------
    */

    public function getUsersByBagian(
        Request $request
    ) {
        $request->validate([
            'bagian' => 'required|string|max:100',
        ]);


        $users = User::where(
                'status',
                'AKTIF'
            )
            ->where(
                'bagian',
                $request->bagian
            )
            ->select(
                'id',
                'name',
                'bagian'
            )
            ->orderBy(
                'name'
            )
            ->get();


        return response()->json(
            $users
        );
    }


    /*
    |--------------------------------------------------------------------------
    | LOGIN
    |--------------------------------------------------------------------------
    */

    public function login(
        Request $request
    ) {
        $request->validate([
            'user_id' => [
                'required',
                'exists:users,id',
            ],

            'password' => [
                'required',
                'string',
            ],
        ]);


        /*
        |--------------------------------------------------------------------------
        | CARI USER AKTIF
        |--------------------------------------------------------------------------
        */

        $user = User::where(
                'id',
                $request->user_id
            )
            ->where(
                'status',
                'AKTIF'
            )
            ->first();


        if (!$user) {

            return back()
                ->withErrors([
                    'user_id' =>
                        'User tidak aktif atau tidak ditemukan.',
                ])
                ->withInput();

        }


        /*
        |--------------------------------------------------------------------------
        | CEK PASSWORD
        |--------------------------------------------------------------------------
        */

        if (!Hash::check(
            $request->password,
            $user->password
        )) {

            return back()
                ->withErrors([
                    'password' =>
                        'Password salah.',
                ])
                ->withInput();

        }


        /*
        |--------------------------------------------------------------------------
        | LOGIN
        |--------------------------------------------------------------------------
        */

        Auth::login(
            $user
        );


        $request
            ->session()
            ->regenerate();


        /*
        |--------------------------------------------------------------------------
        | LAST LOGIN
        |--------------------------------------------------------------------------
        */

        try {

            $user->last_login_at =
                now();

            $user->save();

        } catch (\Throwable $e) {

            // Login tetap berhasil.

        }


        /*
        |--------------------------------------------------------------------------
        | LOG AKTIVITAS
        |--------------------------------------------------------------------------
        */

        try {

            ActivityLog::create([
                'user_id' =>
                    $user->id,

                'action' =>
                    'LOGIN',

                'description' =>
                    'User masuk ke sistem.',

                'ip_address' =>
                    $request->ip(),
            ]);

        } catch (\Throwable $e) {

            // Login tetap berhasil.

        }


        /*
        |--------------------------------------------------------------------------
        | REDIRECT BERDASARKAN ROLE
        |--------------------------------------------------------------------------
        |
        | MANAGER langsung dibawa ke Dashboard Manager.
        | Role lain menuju dashboard masing-masing.
        |
        */

        $role = \App\Support\DepartmentAccess::normalizeRole(
            $user->role ?? ''
        );

        if ($role === \App\Support\DepartmentAccess::MANAGER) {

            return redirect()
                ->route(
                    'dashboard.manager'
                );

        }


        return redirect()
            ->route(
                'dashboard'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | LOGOUT
    |--------------------------------------------------------------------------
    */

    public function logout(
        Request $request
    ) {
        $user =
            $request->user();


        if ($user) {

            try {

                ActivityLog::create([
                    'user_id' =>
                        $user->id,

                    'action' =>
                        'LOGOUT',

                    'description' =>
                        'User keluar dari sistem.',

                    'ip_address' =>
                        $request->ip(),
                ]);

            } catch (\Throwable $e) {

                // Abaikan jika log belum tersedia.

            }

        }


        Auth::logout();


        $request
            ->session()
            ->invalidate();


        $request
            ->session()
            ->regenerateToken();


        return redirect()
            ->route(
                'login'
            );
    }
}
