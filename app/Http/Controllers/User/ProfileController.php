<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Activity\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | PROFIL
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        return view('sistem.akun.profil');
    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE PROFIL
    |--------------------------------------------------------------------------
    */

    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $user = $request->user();

        $user->name = $request->name;

        $user->save();

        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'UPDATE PROFILE',
            'description' => 'Mengubah nama profil.',
            'ip_address' => $request->ip(),
        ]);

        return back()->with(
            'success',
            'Profil berhasil diperbarui.'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | HALAMAN UBAH FOTO
    |--------------------------------------------------------------------------
    */

    public function photo()
    {
        return view('sistem.akun.foto');
    }


    /*
    |--------------------------------------------------------------------------
    | SIMPAN FOTO
    |--------------------------------------------------------------------------
    */

    public function updatePhoto(Request $request)
    {
        $request->validate([
            'foto' => 'required|image|max:5120',
        ]);

        $user = $request->user();

        $path = $request
            ->file('foto')
            ->store('users', 'public');

        $user->foto = $path;

        $user->save();


        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'UPDATE PHOTO',
            'description' => 'Mengubah foto profil.',
            'ip_address' => $request->ip(),
        ]);


        return back()->with(
            'success',
            'Foto profil berhasil diperbarui.'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | HALAMAN UBAH PASSWORD
    |--------------------------------------------------------------------------
    */

    public function password()
    {
        return view('sistem.akun.ubah-password');
    }


    /*
    |--------------------------------------------------------------------------
    | SIMPAN PASSWORD
    |--------------------------------------------------------------------------
    */

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:6|confirmed',
        ]);

        $user = $request->user();


        if (!Hash::check(
            $request->current_password,
            $user->password
        )) {

            return back()->withErrors([
                'current_password' => 'Password lama salah.',
            ]);
        }


        $user->password = Hash::make(
            $request->password
        );

        $user->save();


        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'CHANGE PASSWORD',
            'description' => 'Mengubah password akun.',
            'ip_address' => $request->ip(),
        ]);


        return back()->with(
            'success',
            'Password berhasil diubah.'
        );
    }
}