<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WorkOrder\WorkOrder;
use App\Support\DepartmentAccess;

class WorkOrderPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, WorkOrder $workOrder): bool
    {
        return DepartmentAccess::canAccessWorkOrder($user, $workOrder);
    }

    public function update(User $user, WorkOrder $workOrder): bool
    {
        return DepartmentAccess::canAccessWorkOrder($user, $workOrder);
    }

    public function delete(User $user, WorkOrder $workOrder): bool
    {
        if (DepartmentAccess::isAdmin($user)) {
            return true;
        }

        $userName = trim((string) ($user->name ?? ''));
        $creator = trim((string) ($workOrder->dibuat_oleh ?? ''));

        return $userName !== ''
            && $creator !== ''
            && strcasecmp($userName, $creator) === 0;
    }

    public function followUp(User $user, WorkOrder $workOrder): bool
    {
        return DepartmentAccess::isMaintenanceStaff($user)
            && DepartmentAccess::canAccessWorkOrder($user, $workOrder);
    }
}
