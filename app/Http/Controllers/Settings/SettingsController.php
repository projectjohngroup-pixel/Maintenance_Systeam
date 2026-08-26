<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Settings\SystemSetting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = SystemSetting::pluck('value', 'key');

        return view('sistem.pengaturan.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'system_name' => 'required|string|max:255',

            'logo_dashboard' => 'nullable|image|max:5120',

            'logo_login' => 'nullable|image|max:5120',

            'background_login' => 'nullable|image|max:10240',
        ]);


        /*
        |--------------------------------------------------------------------------
        | NAMA SISTEM
        |--------------------------------------------------------------------------
        */

        SystemSetting::updateOrCreate(
            ['key' => 'system_name'],
            ['value' => $request->system_name]
        );


        /*
        |--------------------------------------------------------------------------
        | LOGO DASHBOARD
        |--------------------------------------------------------------------------
        */

        if ($request->hasFile('logo_dashboard')) {

            $file = $request->file('logo_dashboard');

            $path = $file->store('system', 'public');

            SystemSetting::updateOrCreate(
                ['key' => 'logo_dashboard'],
                ['value' => $path]
            );
        }


        /*
        |--------------------------------------------------------------------------
        | LOGO LOGIN
        |--------------------------------------------------------------------------
        */

        if ($request->hasFile('logo_login')) {

            $file = $request->file('logo_login');

            $path = $file->store('system', 'public');

            SystemSetting::updateOrCreate(
                ['key' => 'logo_login'],
                ['value' => $path]
            );
        }


        /*
        |--------------------------------------------------------------------------
        | BACKGROUND LOGIN
        |--------------------------------------------------------------------------
        */

        if ($request->hasFile('background_login')) {

            $file = $request->file('background_login');

            $path = $file->store('system', 'public');

            SystemSetting::updateOrCreate(
                ['key' => 'background_login'],
                ['value' => $path]
            );
        }


        return back()->with(
            'success',
            'Pengaturan berhasil disimpan.'
        );
    }
}