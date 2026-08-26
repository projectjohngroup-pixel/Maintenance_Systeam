<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Settings\SystemSetting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserManagementController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | ROLE SISTEM
    |--------------------------------------------------------------------------
    */

    private function defaultSystemRoles(): array
{
    return [
        'Administrator',
        'Manager',
        'Direktur',
        'Produksi',
        'Maintenance',
        'Prev-Maint',
        'Mekanik / Maintenance',
    ];
}


    private function getSystemRoles(): array
    {
        $value = SystemSetting::where(
            'key',
            'system_role_list'
        )->value('value');

        if (!$value) {
            return $this->defaultSystemRoles();
        }

        $roles = json_decode($value, true);

        if (!is_array($roles) || empty($roles)) {
            return $this->defaultSystemRoles();
        }

        return array_values(
            array_unique(
                array_filter(
                    array_map(
                        fn ($role) =>
                            is_string($role)
                                ? trim($role)
                                : null,
                        $roles
                    )
                )
            )
        );
    }

    private function saveSystemRoles(array $roles): void
    {
        SystemSetting::updateOrCreate(
            [
                'key' => 'system_role_list',
            ],
            [
                'value' => json_encode(
                    array_values(
                        array_unique(
                            array_filter($roles)
                        )
                    )
                ),
            ]
        );
    }


    /*
    |--------------------------------------------------------------------------
    | BAGIAN
    |--------------------------------------------------------------------------
    */

    private function defaultBagian(): array
    {
        return [
            'Produksi',
            'Maintenance',
            'Gudang',
        ];
    }

    private function getBagian(): array
    {
        $value = SystemSetting::where(
            'key',
            'bagian_list'
        )->value('value');

        if (!$value) {
            return $this->defaultBagian();
        }

        $bagian = json_decode($value, true);

        if (!is_array($bagian) || empty($bagian)) {
            return $this->defaultBagian();
        }

        return array_values(
            array_unique(
                array_filter(
                    array_map(
                        fn ($item) =>
                            is_string($item)
                                ? trim($item)
                                : null,
                        $bagian
                    )
                )
            )
        );
    }

    private function saveBagian(array $bagian): void
    {
        SystemSetting::updateOrCreate(
            [
                'key' => 'bagian_list',
            ],
            [
                'value' => json_encode(
                    array_values(
                        array_unique(
                            array_filter($bagian)
                        )
                    )
                ),
            ]
        );
    }


    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $users = User::orderBy('bagian')
            ->orderBy('name')
            ->get();

        $roles = $this->getBagian();

        $systemRoles = $this->getSystemRoles();

        return view(
            'sistem.manajemen-user.index',
            compact(
                'users',
                'roles',
                'systemRoles'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | TAMBAH USER
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $request->validate([
            'role' => [
                'required',
                'string',
                'max:100',
            ],

            'bagian' => [
                'required',
                'string',
                'max:100',
            ],

            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'password' => [
                'required',
                'string',
                'min:6',
            ],
        ]);

        $systemRoles = $this->getSystemRoles();

        $bagianList = $this->getBagian();

        if (!in_array(
            $request->role,
            $systemRoles,
            true
        )) {
            return back()
                ->withErrors([
                    'role' => 'Role Sistem belum terdaftar.',
                ])
                ->withInput();
        }

        if (!in_array(
            $request->bagian,
            $bagianList,
            true
        )) {
            return back()
                ->withErrors([
                    'bagian' => 'Bagian belum terdaftar.',
                ])
                ->withInput();
        }

        $exists = User::where(
            'name',
            $request->name
        )
            ->where(
                'bagian',
                $request->bagian
            )
            ->exists();

        if ($exists) {
            return back()
                ->withErrors([
                    'name' =>
                        'Nama tersebut sudah ada pada Bagian tersebut.',
                ])
                ->withInput();
        }

        User::create([
            'name' => $request->name,

            'email' => null,

            'password' => Hash::make(
                $request->password
            ),

            'role' => $request->role,

            'bagian' => $request->bagian,

            'status' => 'AKTIF',
        ]);

        return back()->with(
            'success',
            'User berhasil ditambahkan.'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | EDIT USER
    |--------------------------------------------------------------------------
    */

    public function update(
        Request $request,
        User $user
    ) {
        $request->validate([
            'role' => [
                'required',
                'string',
                'max:100',
            ],

            'bagian' => [
                'required',
                'string',
                'max:100',
            ],

            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'password' => [
                'nullable',
                'string',
                'min:6',
            ],
        ]);

        $systemRoles = $this->getSystemRoles();

        $bagianList = $this->getBagian();

        if (!in_array(
            $request->role,
            $systemRoles,
            true
        )) {
            return back()
                ->withErrors([
                    'role' =>
                        'Role Sistem belum terdaftar.',
                ]);
        }

        if (!in_array(
            $request->bagian,
            $bagianList,
            true
        )) {
            return back()
                ->withErrors([
                    'bagian' =>
                        'Bagian belum terdaftar.',
                ]);
        }

        $exists = User::where(
            'name',
            $request->name
        )
            ->where(
                'bagian',
                $request->bagian
            )
            ->where(
                'id',
                '!=',
                $user->id
            )
            ->exists();

        if ($exists) {
            return back()
                ->withErrors([
                    'name' =>
                        'Nama tersebut sudah digunakan.',
                ]);
        }

        $user->name = $request->name;

        $user->role = $request->role;

        $user->bagian = $request->bagian;

        if ($request->filled('password')) {
            $user->password = Hash::make(
                $request->password
            );
        }

        $user->save();

        return back()->with(
            'success',
            'User berhasil diperbarui.'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | HAPUS USER
    |--------------------------------------------------------------------------
    */

    public function destroy(
        Request $request,
        User $user
    ) {
        if (
            $request->user()->id === $user->id
        ) {
            return back()
                ->withErrors([
                    'user' =>
                        'User yang sedang digunakan tidak dapat dihapus.',
                ]);
        }

        $user->delete();

        return back()->with(
            'success',
            'User berhasil dihapus.'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | AKTIF / NONAKTIF
    |--------------------------------------------------------------------------
    */

    public function updateStatus(
        Request $request,
        User $user
    ) {
        $user->status =
            $user->status === 'AKTIF'
                ? 'NONAKTIF'
                : 'AKTIF';

        $user->save();

        return back()->with(
            'success',
            'Status user berhasil diperbarui.'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | TAMBAH BAGIAN
    |--------------------------------------------------------------------------
    */

    public function storeRole(
        Request $request
    ) {
        $request->validate([
            'role' => [
                'required',
                'string',
                'max:100',
            ],
        ]);

        $bagian = trim($request->role);

        $bagianList = $this->getBagian();

        foreach ($bagianList as $item) {
            if (
                mb_strtolower($item) ===
                mb_strtolower($bagian)
            ) {
                return back()
                    ->withErrors([
                        'role' =>
                            'Bagian tersebut sudah ada.',
                    ]);
            }
        }

        $bagianList[] = $bagian;

        $this->saveBagian($bagianList);

        return back()->with(
            'success',
            'Bagian berhasil ditambahkan.'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | EDIT BAGIAN
    |--------------------------------------------------------------------------
    */

    public function updateRole(
        Request $request,
        string $role
    ) {
        $request->validate([
            'new_role' => [
                'required',
                'string',
                'max:100',
            ],
        ]);

        $newRole = trim(
            $request->new_role
        );

        $bagianList = $this->getBagian();

        foreach ($bagianList as $item) {
            if (
                mb_strtolower($item) ===
                mb_strtolower($newRole)
            ) {
                return back()
                    ->withErrors([
                        'role' =>
                            'Bagian baru tersebut sudah ada.',
                    ]);
            }
        }

        $bagianList = array_map(
            function ($item) use (
                $role,
                $newRole
            ) {
                return $item === $role
                    ? $newRole
                    : $item;
            },
            $bagianList
        );

        User::where(
            'bagian',
            $role
        )->update([
            'bagian' => $newRole,
        ]);

        $this->saveBagian(
            $bagianList
        );

        return back()->with(
            'success',
            'Bagian berhasil diperbarui.'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | HAPUS BAGIAN
    |--------------------------------------------------------------------------
    */

    public function deleteRole(
        Request $request,
        string $role
    ) {
        $dipakai = User::where(
            'bagian',
            $role
        )->exists();

        if ($dipakai) {
            return back()
                ->withErrors([
                    'role' =>
                        'Bagian masih digunakan oleh user.',
                ]);
        }

        $bagianList = $this->getBagian();

        $bagianList = array_values(
            array_filter(
                $bagianList,
                fn ($item) =>
                    $item !== $role
            )
        );

        $this->saveBagian(
            $bagianList
        );

        return back()->with(
            'success',
            'Bagian berhasil dihapus.'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | TAMBAH ROLE SISTEM
    |--------------------------------------------------------------------------
    */

    public function storeSystemRole(
        Request $request
    ) {
        $request->validate([
            'system_role' => [
                'required',
                'string',
                'max:100',
            ],
        ]);

        $role = trim(
            $request->system_role
        );

        $systemRoles = $this->getSystemRoles();

        foreach ($systemRoles as $item) {
            if (
                mb_strtolower($item) ===
                mb_strtolower($role)
            ) {
                return back()
                    ->withErrors([
                        'system_role' =>
                            'Role Sistem tersebut sudah ada.',
                    ]);
            }
        }

        $systemRoles[] = $role;

        $this->saveSystemRoles(
            $systemRoles
        );

        return back()->with(
            'success',
            'Role Sistem berhasil ditambahkan.'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | EDIT ROLE SISTEM
    |--------------------------------------------------------------------------
    */

    public function updateSystemRole(
        Request $request,
        string $role
    ) {
        $request->validate([
            'new_role' => [
                'required',
                'string',
                'max:100',
            ],
        ]);

        $newRole = trim(
            $request->new_role
        );

        $systemRoles = $this->getSystemRoles();

        $found = false;

        foreach ($systemRoles as $index => $item) {
            if (
                mb_strtolower($item) ===
                mb_strtolower($role)
            ) {
                $found = true;
                $systemRoles[$index] = $newRole;
                break;
            }
        }

        if (!$found) {
            return back()->withErrors([
                'system_role' =>
                    'Role Sistem tidak ditemukan.',
            ]);
        }

        if (
            mb_strtolower($newRole) !==
            mb_strtolower($role)
        ) {
            foreach ($systemRoles as $item) {
                if (
                    $item !== $newRole &&
                    mb_strtolower($item) ===
                    mb_strtolower($newRole)
                ) {
                    return back()->withErrors([
                        'system_role' =>
                            'Role Sistem tersebut sudah ada.',
                    ]);
                }
            }
        }

        $this->saveSystemRoles(
            $systemRoles
        );

        $user = auth()->user();

        if (
            $user &&
            mb_strtolower($user->role) ===
            mb_strtolower($role)
        ) {
            $user->role = $newRole;
            $user->save();
        }

        User::where(
            'role',
            $role
        )->update([
            'role' => $newRole,
        ]);

        return back()->with(
            'success',
            'Role Sistem berhasil diperbarui.'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | HAPUS ROLE SISTEM
    |--------------------------------------------------------------------------
    */

    public function deleteSystemRole(
        Request $request,
        string $role
    ) {
        $protected = [
            'Administrator',
            'Manager',
            'Direktur',
            'Produksi',
            'Maintenance',
            'Prev-Maint',
            'Mekanik / Maintenance',
        ];

        if (
            in_array(
                $role,
                $protected,
                true
            )
        ) {
            return back()->withErrors([
                'system_role' =>
                    'Role Sistem ini tidak dapat dihapus.',
            ]);
        }

        $dipakai = User::where(
            'role',
            $role
        )->exists();

        if ($dipakai) {
            return back()->withErrors([
                'system_role' =>
                    'Role Sistem masih digunakan oleh user.',
            ]);
        }

        $systemRoles = $this->getSystemRoles();

        $systemRoles = array_values(
            array_filter(
                $systemRoles,
                fn ($item) =>
                    $item !== $role
            )
        );

        $this->saveSystemRoles(
            $systemRoles
        );

        return back()->with(
            'success',
            'Role Sistem berhasil dihapus.'
        );
    }
}