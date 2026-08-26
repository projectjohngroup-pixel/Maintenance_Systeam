<?php

namespace App\Policies;

use App\Models\Inventory\Barang;
use App\Models\User;
use App\Support\DepartmentAccess;

class BarangPolicy
{
    public function viewAny(User $user): bool
    {
        return DepartmentAccess::isAdmin($user)
            || DepartmentAccess::isMaintenanceStaff($user);
    }

    public function view(User $user, Barang $barang): bool
    {
        return DepartmentAccess::canAccessBarang($user, $barang);
    }

    public function update(User $user, Barang $barang): bool
    {
        return DepartmentAccess::canAccessBarang($user, $barang);
    }

    public function delete(User $user, Barang $barang): bool
    {
        return DepartmentAccess::canAccessBarang($user, $barang);
    }
}
