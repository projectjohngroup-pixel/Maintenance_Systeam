<?php

namespace App\Support;

use App\Models\Inventory\Barang;
use App\Models\Notification;
use App\Models\User;
use App\Models\WorkOrder\WorkOrder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class DepartmentAccess
{
    public const ADMINISTRATOR = 'ADMINISTRATOR';

    public const MANAGER = 'MANAGER';

    public const DIREKTUR = 'DIREKTUR';

    public const PRODUKSI = 'PRODUKSI';

    public const MAINTENANCE = 'MAINTENANCE';

    public const MEKANIK_MAINT = 'MEKANIK_MAINT';

    public const PREV_MAINT = 'PREV_MAINT';

    public const TUJUAN_MEKANIK = 'MEKANIK/MAINTENANCE';

    public const TUJUAN_PREV = 'PREV-MAINT';

    public static function departments(): array
    {
        return [
            self::MEKANIK_MAINT,
            self::PREV_MAINT,
        ];
    }

    public static function allRoles(): array
    {
        return [
            self::ADMINISTRATOR,
            self::MANAGER,
            self::DIREKTUR,
            self::PRODUKSI,
            self::MAINTENANCE,
            self::PREV_MAINT,
            self::MEKANIK_MAINT,
        ];
    }

    public static function normalize(?string $value): string
    {
        return strtoupper(trim((string) $value));
    }

    public static function normalizeRole(?string $role): string
    {
        $role = self::normalize($role);

        return match ($role) {
            'ADMIN', 'ADMINISTRATOR' => self::ADMINISTRATOR,
            'MANAGER' => self::MANAGER,
            'DIREKTUR' => self::DIREKTUR,
            'USER',
            'PRODUKSI',
            'PRODUCTION' => self::PRODUKSI,
            'MAINTENANCE' => self::MAINTENANCE,
            'MEKANIK_MAINT',
            'MEKANIK & MAINT',
            'MEKANIK&MAINT',
            'MEKANIK/MAINT',
            'MEKANIK-MAINT',
            'MEKANIK / MAINTENANCE',
            'MEKANIK/MAINTENANCE',
            'MEKANIK' => self::MEKANIK_MAINT,
            'PREV_MAINT',
            'PREV-MAINT',
            'PREV MAINT',
            'PREVENTIVE',
            'PREVENTIVE MAINTENANCE' => self::PREV_MAINT,
            default => $role,
        };
    }

    public static function roleLabel(?string $role): string
    {
        return match (self::normalizeRole($role)) {
            self::ADMINISTRATOR => 'Administrator',
            self::MANAGER => 'Manager',
            self::DIREKTUR => 'Direktur',
            self::PRODUKSI => 'Produksi',
            self::MAINTENANCE => 'Maintenance',
            self::PREV_MAINT => 'Prev-Maint',
            self::MEKANIK_MAINT => 'Mekanik / Maintenance',
            default => $role,
        };
    }

    public static function fromTujuan(?string $tujuan): string
    {
        $tujuan = self::normalize($tujuan);

        if (
            $tujuan === self::TUJUAN_PREV
            || str_contains($tujuan, 'PREV')
            || str_contains($tujuan, 'PREVENT')
        ) {
            return self::PREV_MAINT;
        }

        return self::MEKANIK_MAINT;
    }

    public static function tujuanFromDepartment(?string $department): string
    {
        return self::normalize($department) === self::PREV_MAINT
            ? self::TUJUAN_PREV
            : self::TUJUAN_MEKANIK;
    }

    public static function label(?string $department): string
    {
        return self::normalize($department) === self::PREV_MAINT
            ? 'Prev-Maint'
            : 'Mekanik & Maint';
    }

    public static function fromBucket(?string $bucket): string
    {
        $bucket = strtolower(trim((string) $bucket));

        return $bucket === 'prev' ? self::PREV_MAINT : self::MEKANIK_MAINT;
    }

    public static function toBucket(?string $department): string
    {
        return self::normalize($department) === self::PREV_MAINT
            ? 'prev'
            : 'me_prev';
    }

    public static function isAdmin(?User $user): bool
    {
        return self::normalizeRole($user?->role) === self::ADMINISTRATOR;
    }

    public static function isManager(?User $user): bool
    {
        return self::normalizeRole($user?->role) === self::MANAGER;
    }

    public static function isDirektur(?User $user): bool
    {
        return self::normalizeRole($user?->role) === self::DIREKTUR;
    }

    public static function isProduksi(?User $user): bool
    {
        return self::normalizeRole($user?->role) === self::PRODUKSI;
    }

    public static function isMaintenanceCs(?User $user): bool
    {
        return self::normalizeRole($user?->role) === self::MAINTENANCE;
    }

    public static function isMekanikMaint(?User $user): bool
    {
        return self::normalizeRole($user?->role) === self::MEKANIK_MAINT;
    }

    public static function isPrevMaint(?User $user): bool
    {
        return self::normalizeRole($user?->role) === self::PREV_MAINT;
    }

    public static function isMaintenanceStaff(?User $user): bool
    {
        return in_array(
            self::normalizeRole($user?->role),
            [
                self::MAINTENANCE,
                self::MEKANIK_MAINT,
                self::PREV_MAINT,
            ],
            true
        );
    }

    public static function canViewAllDepartments(?User $user): bool
    {
        return in_array(
            self::normalizeRole($user?->role),
            [
                self::ADMINISTRATOR,
                self::MANAGER,
                self::DIREKTUR,
                self::MAINTENANCE,
            ],
            true
        );
    }

    public static function scopedDepartment(?User $user): ?string
    {
        $role = self::normalizeRole($user?->role);

        if ($role === self::MEKANIK_MAINT) {
            return self::MEKANIK_MAINT;
        }

        if ($role === self::PREV_MAINT) {
            return self::PREV_MAINT;
        }

        return null;
    }

    public static function canAccessDepartment(?User $user, ?string $department): bool
    {
        if ($department === null || $department === '') {
            return self::canViewAllDepartments($user);
        }

        $department = self::normalize($department) === self::PREV_MAINT
            ? self::PREV_MAINT
            : self::MEKANIK_MAINT;

        if (self::canViewAllDepartments($user)) {
            return true;
        }

        return self::scopedDepartment($user) === $department;
    }

    public static function assertCanAccessDepartment(?User $user, ?string $department): void
    {
        if (!self::canAccessDepartment($user, $department)) {
            abort(403, 'Anda tidak memiliki akses ke data department tersebut.');
        }
    }

    public static function workOrderDepartment(WorkOrder $workOrder): string
    {
        $assigned = self::normalize($workOrder->assigned_department ?? '');

        if (in_array($assigned, self::departments(), true)) {
            return $assigned;
        }

        return self::fromTujuan($workOrder->tujuan ?? null);
    }

    public static function canAccessWorkOrder(?User $user, WorkOrder $workOrder): bool
    {
        if (self::isAdmin($user) || self::isManager($user) || self::isDirektur($user) || self::isMaintenanceCs($user)) {
            return true;
        }

        if (self::isMaintenanceStaff($user)) {
            return self::canAccessDepartment($user, self::workOrderDepartment($workOrder));
        }

        if (self::isProduksi($user)) {
            $userName = trim((string) ($user?->name ?? ''));
            $creator = trim((string) ($workOrder->dibuat_oleh ?? ''));

            return $userName !== ''
                && $creator !== ''
                && strcasecmp($userName, $creator) === 0;
        }

        return false;
    }

    public static function assertCanAccessWorkOrder(?User $user, WorkOrder $workOrder): void
    {
        if (!self::canAccessWorkOrder($user, $workOrder)) {
            abort(403, 'Anda tidak memiliki akses ke Work Order ini.');
        }
    }

    public static function applyWorkOrderScope(Builder $query, ?User $user, ?string $forcedDepartment = null): Builder
    {
        if ($forcedDepartment) {
            self::assertCanAccessDepartment($user, $forcedDepartment);

            return self::whereDepartment($query, $forcedDepartment);
        }

        $scoped = self::scopedDepartment($user);

        if ($scoped) {
            return self::whereDepartment($query, $scoped);
        }

        return $query;
    }

    public static function whereDepartment(Builder $query, string $department): Builder
    {
        $department = self::normalize($department) === self::PREV_MAINT
            ? self::PREV_MAINT
            : self::MEKANIK_MAINT;

        $tujuan = self::tujuanFromDepartment($department);

        return $query->where(function (Builder $inner) use ($department, $tujuan) {
            $inner->where('assigned_department', $department)
                ->orWhere(function (Builder $fallback) use ($department, $tujuan) {
                    $fallback->where(function (Builder $emptyAssigned) {
                        $emptyAssigned->whereNull('assigned_department')
                            ->orWhere('assigned_department', '');
                    })->where('tujuan', $tujuan);
                });
        });
    }

    public static function applyBarangScope(Builder $query, ?User $user, ?string $forcedDepartment = null): Builder
    {
        if ($forcedDepartment) {
            self::assertCanAccessDepartment($user, $forcedDepartment);

            return $query->where('department', $forcedDepartment);
        }

        $scoped = self::scopedDepartment($user);

        if ($scoped) {
            return $query->where('department', $scoped);
        }

        return $query;
    }

    public static function canAccessBarang(?User $user, Barang $barang): bool
    {
        $department = self::normalize($barang->department ?? '') ?: self::MEKANIK_MAINT;

        return self::canAccessDepartment($user, $department);
    }

    public static function assertCanAccessBarang(?User $user, Barang $barang): void
    {
        if (!self::canAccessBarang($user, $barang)) {
            abort(403, 'Anda tidak memiliki akses ke inventory department tersebut.');
        }
    }

    public static function requestedDepartmentFromRequest($request, ?User $user): ?string
    {
        $raw = $request->input('department', $request->input('bucket'));

        if ($raw === null || $raw === '' || $raw === 'all') {
            $scoped = self::scopedDepartment($user);

            return $scoped;
        }

        $department = in_array(self::normalize((string) $raw), ['PREV', 'PREV_MAINT', 'PREV-MAINT'], true)
            || strtolower((string) $raw) === 'prev'
            ? self::PREV_MAINT
            : self::MEKANIK_MAINT;

        self::assertCanAccessDepartment($user, $department);

        return $department;
    }

    public static function notifyWorkOrderTeam(
        WorkOrder $workOrder,
        ?Carbon $deadline = null,
        string $type = 'WO_CREATED',
        string $title = 'Work Order Baru',
        ?string $customMessage = null
    ): void {
        $department = self::workOrderDepartment($workOrder);

        $message = $customMessage ?? (
            'WO ' .
            $workOrder->no_wo .
            ' baru masuk untuk ' .
            self::label($department) .
            '.'
        );

        $targets = self::notificationRecipients($department);

        if ($targets->isEmpty()) {
            return;
        }

        $now = now();
        $rows = [];

        foreach ($targets as $target) {
            $rows[] = [
                'user_id' => $target->id,
                'work_order_id' => $workOrder->id,
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'status' => 'UNREAD',
                'deadline_at' => $deadline,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        Notification::insert($rows);
    }

    public static function notificationRecipients(string $department): Collection
    {
        $department = self::normalize($department) === self::PREV_MAINT
            ? self::PREV_MAINT
            : self::MEKANIK_MAINT;

        $users = User::query()
            ->where(function ($query) {
                $query->whereRaw('UPPER(TRIM(status)) = ?', ['AKTIF'])
                    ->orWhereNull('status');
            })
            ->get();

        return $users->filter(function (User $user) use ($department) {
            $role = self::normalizeRole($user->role);

            if ($role === self::MAINTENANCE) {
                return true;
            }

            if ($department === self::PREV_MAINT) {
                return $role === self::PREV_MAINT;
            }

            return $role === self::MEKANIK_MAINT;
        })->unique('id')->values();
    }

    public static function canEditWorkOrder(?User $user, WorkOrder $workOrder): bool
    {
        if (self::isAdmin($user) || self::isMaintenanceCs($user)) {
            return true;
        }

        if (self::isMaintenanceStaff($user)) {
            $userDept = self::scopedDepartment($user);
            $woDept = self::workOrderDepartment($workOrder);

            return $userDept !== null && $userDept === $woDept;
        }

        if (self::isProduksi($user)) {
            $userName = trim((string) ($user?->name ?? ''));
            $creator = trim((string) ($workOrder->dibuat_oleh ?? ''));

            return $userName !== '' && $creator !== ''
                && strcasecmp($userName, $creator) === 0;
        }

        return false;
    }

    public static function assertCanEditWorkOrder(?User $user, WorkOrder $workOrder): void
    {
        if (!self::canEditWorkOrder($user, $workOrder)) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit Work Order ini.');
        }
    }

    public static function canDeleteWorkOrder(?User $user, WorkOrder $workOrder): bool
    {
        if (self::isAdmin($user) || self::isMaintenanceCs($user)) {
            return true;
        }

        if (self::isMaintenanceStaff($user)) {
            $userDept = self::scopedDepartment($user);
            $woDept = self::workOrderDepartment($workOrder);

            return $userDept !== null && $userDept === $woDept;
        }

        return false;
    }

    public static function assertCanDeleteWorkOrder(?User $user, WorkOrder $workOrder): void
    {
        if (!self::canDeleteWorkOrder($user, $workOrder)) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus Work Order ini.');
        }
    }

    public static function canEditBarang(?User $user, Barang $barang): bool
    {
        return self::canAccessBarang($user, $barang);
    }

    public static function assertCanEditBarang(?User $user, Barang $barang): void
    {
        if (!self::canEditBarang($user, $barang)) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit inventory ini.');
        }
    }

    public static function canDeleteBarang(?User $user, Barang $barang): bool
    {
        return self::canAccessBarang($user, $barang);
    }

    public static function assertCanDeleteBarang(?User $user, Barang $barang): void
    {
        if (!self::canDeleteBarang($user, $barang)) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus inventory ini.');
        }
    }

    public static function resolveAssignedDepartment(array $data): array
    {
        if (empty($data['assigned_department']) && !empty($data['tujuan'])) {
            $data['assigned_department'] = self::fromTujuan($data['tujuan']);
        }

        if (!empty($data['assigned_department']) && empty($data['tujuan'])) {
            $data['tujuan'] = self::tujuanFromDepartment($data['assigned_department']);
        }

        return $data;
    }
}
