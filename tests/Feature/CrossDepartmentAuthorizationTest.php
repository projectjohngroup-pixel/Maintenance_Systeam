<?php

namespace Tests\Feature;

use App\Models\Inventory\Barang;
use App\Models\User;
use App\Models\WorkOrder\WorkOrder;
use App\Support\DepartmentAccess;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class CrossDepartmentAuthorizationTest extends TestCase
{
    private int $mekanikUserId = 0;
    private int $prevUserId = 0;
    private int $maintenanceUserId = 0;
    private int $adminUserId = 0;
    private int $mekanikWoId = 0;
    private int $prevWoId = 0;
    private int $mekanikBarangId = 0;
    private int $prevBarangId = 0;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.default'                    => 'mysql',
            'database.connections.mysql.database' => 'Maintenance_Systeam',
            'database.connections.mysql.host'     => env('DB_HOST', '127.0.0.1'),
            'database.connections.mysql.port'     => env('DB_PORT', '3306'),
            'database.connections.mysql.username' => env('DB_USERNAME', 'root'),
            'database.connections.mysql.password' => env('DB_PASSWORD', ''),
            'session.driver'                      => 'array',
            'cache.default'                       => 'array',
        ]);

        DB::purge('mysql');

        $this->setupTestData();
    }

    private function setupTestData(): void
    {
        $this->adminUserId = User::where('role', 'Administrator')->where('status', 'AKTIF')->first()?->id ?? 0;

        $this->maintenanceUserId = User::where('role', 'Maintenance')->where('status', 'AKTIF')->first()?->id ?? 0;

        $this->mekanikUserId = (int) DB::table('users')->insertGetId([
            'name' => 'Test Mekanik User',
            'email' => 'test_mekanik_' . time() . '@test.com',
            'password' => bcrypt('password'),
            'role' => 'Mekanik / Maintenance',
            'status' => 'AKTIF',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->prevUserId = (int) DB::table('users')->insertGetId([
            'name' => 'Test Prev-Maint User',
            'email' => 'test_prev_' . time() . '@test.com',
            'password' => bcrypt('password'),
            'role' => 'Prev-Maint',
            'status' => 'AKTIF',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->mekanikWoId = (int) DB::table('work_orders')->insertGetId([
            'no_wo' => 'WO-TEST-MEK-' . time(),
            'tanggal_kerusakan' => now()->format('Y-m-d'),
            'jam_kerusakan' => now()->format('H:i'),
            'departemen' => 'PRODUKSI',
            'tujuan' => DepartmentAccess::TUJUAN_MEKANIK,
            'assigned_department' => DepartmentAccess::MEKANIK_MAINT,
            'priority' => 'NORMAL',
            'status' => 'OPEN',
            'job' => 'Test Job Mekanik',
            'deskripsi' => 'Test deskripsi mekanik',
            'dibuat_oleh' => 'Test Mekanik User',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->prevWoId = (int) DB::table('work_orders')->insertGetId([
            'no_wo' => 'WO-TEST-PREV-' . time(),
            'tanggal_kerusakan' => now()->format('Y-m-d'),
            'jam_kerusakan' => now()->format('H:i'),
            'departemen' => 'PRODUKSI',
            'tujuan' => DepartmentAccess::TUJUAN_PREV,
            'assigned_department' => DepartmentAccess::PREV_MAINT,
            'priority' => 'NORMAL',
            'status' => 'OPEN',
            'job' => 'Test Job Prev-Maint',
            'deskripsi' => 'Test deskripsi prev-maint',
            'dibuat_oleh' => 'Test Prev-Maint User',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->mekanikBarangId = (int) DB::table('barangs')->insertGetId([
            'kode_barang' => 'BMK-' . time(),
            'nama_spesifikasi' => 'Test Barang Mekanik',
            'stok' => 10,
            'stok_minimum' => 2,
            'department' => DepartmentAccess::MEKANIK_MAINT,
            'status' => 'AKTIF',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->prevBarangId = (int) DB::table('barangs')->insertGetId([
            'kode_barang' => 'BPV-' . time(),
            'nama_spesifikasi' => 'Test Barang Prev-Maint',
            'stok' => 10,
            'stok_minimum' => 2,
            'department' => DepartmentAccess::PREV_MAINT,
            'status' => 'AKTIF',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    protected function tearDown(): void
    {
        if ($this->mekanikBarangId) {
            DB::table('barangs')->where('id', $this->mekanikBarangId)->delete();
        }
        if ($this->prevBarangId) {
            DB::table('barangs')->where('id', $this->prevBarangId)->delete();
        }
        if ($this->mekanikWoId) {
            DB::table('work_orders')->where('id', $this->mekanikWoId)->delete();
        }
        if ($this->prevWoId) {
            DB::table('work_orders')->where('id', $this->prevWoId)->delete();
        }
        if ($this->mekanikUserId) {
            DB::table('users')->where('id', $this->mekanikUserId)->delete();
        }
        if ($this->prevUserId) {
            DB::table('users')->where('id', $this->prevUserId)->delete();
        }

        parent::tearDown();
    }

    private function mekanikUser(): User
    {
        return User::findOrFail($this->mekanikUserId);
    }

    private function prevUser(): User
    {
        return User::findOrFail($this->prevUserId);
    }

    private function maintenanceUser(): User
    {
        return User::findOrFail($this->maintenanceUserId);
    }

    private function adminUser(): User
    {
        return User::findOrFail($this->adminUserId);
    }

    private function mekanikWo(): WorkOrder
    {
        return WorkOrder::findOrFail($this->mekanikWoId);
    }

    private function prevWo(): WorkOrder
    {
        return WorkOrder::findOrFail($this->prevWoId);
    }

    private function mekanikBarang(): Barang
    {
        return Barang::findOrFail($this->mekanikBarangId);
    }

    private function prevBarang(): Barang
    {
        return Barang::findOrFail($this->prevBarangId);
    }

    /*
    |--------------------------------------------------------------------------
    | WORK ORDER EDIT AUTHORIZATION
    |--------------------------------------------------------------------------
    */

    public function test_mekanik_can_edit_own_department_wo(): void
    {
        $this->assertTrue(
            DepartmentAccess::canEditWorkOrder($this->mekanikUser(), $this->mekanikWo())
        );
    }

    public function test_mekanik_cannot_edit_prev_wo(): void
    {
        $this->assertFalse(
            DepartmentAccess::canEditWorkOrder($this->mekanikUser(), $this->prevWo())
        );
    }

    public function test_prev_can_edit_own_department_wo(): void
    {
        $this->assertTrue(
            DepartmentAccess::canEditWorkOrder($this->prevUser(), $this->prevWo())
        );
    }

    public function test_prev_cannot_edit_mekanik_wo(): void
    {
        $this->assertFalse(
            DepartmentAccess::canEditWorkOrder($this->prevUser(), $this->mekanikWo())
        );
    }

    public function test_maintenance_cs_can_edit_both_departments(): void
    {
        $user = $this->maintenanceUser();

        $this->assertTrue(DepartmentAccess::canEditWorkOrder($user, $this->mekanikWo()));
        $this->assertTrue(DepartmentAccess::canEditWorkOrder($user, $this->prevWo()));
    }

    public function test_admin_can_edit_both_departments(): void
    {
        $user = $this->adminUser();

        $this->assertTrue(DepartmentAccess::canEditWorkOrder($user, $this->mekanikWo()));
        $this->assertTrue(DepartmentAccess::canEditWorkOrder($user, $this->prevWo()));
    }

    /*
    |--------------------------------------------------------------------------
    | WORK ORDER DELETE AUTHORIZATION
    |--------------------------------------------------------------------------
    */

    public function test_mekanik_can_delete_own_department_wo(): void
    {
        $this->assertTrue(
            DepartmentAccess::canDeleteWorkOrder($this->mekanikUser(), $this->mekanikWo())
        );
    }

    public function test_mekanik_cannot_delete_prev_wo(): void
    {
        $this->assertFalse(
            DepartmentAccess::canDeleteWorkOrder($this->mekanikUser(), $this->prevWo())
        );
    }

    public function test_prev_can_delete_own_department_wo(): void
    {
        $this->assertTrue(
            DepartmentAccess::canDeleteWorkOrder($this->prevUser(), $this->prevWo())
        );
    }

    public function test_prev_cannot_delete_mekanik_wo(): void
    {
        $this->assertFalse(
            DepartmentAccess::canDeleteWorkOrder($this->prevUser(), $this->mekanikWo())
        );
    }

    public function test_maintenance_cs_can_delete_both_departments(): void
    {
        $user = $this->maintenanceUser();

        $this->assertTrue(DepartmentAccess::canDeleteWorkOrder($user, $this->mekanikWo()));
        $this->assertTrue(DepartmentAccess::canDeleteWorkOrder($user, $this->prevWo()));
    }

    public function test_admin_can_delete_both_departments(): void
    {
        $user = $this->adminUser();

        $this->assertTrue(DepartmentAccess::canDeleteWorkOrder($user, $this->mekanikWo()));
        $this->assertTrue(DepartmentAccess::canDeleteWorkOrder($user, $this->prevWo()));
    }

    /*
    |--------------------------------------------------------------------------
    | INVENTORY EDIT/DELETE AUTHORIZATION
    |--------------------------------------------------------------------------
    */

    public function test_mekanik_can_edit_own_department_barang(): void
    {
        $this->assertTrue(
            DepartmentAccess::canEditBarang($this->mekanikUser(), $this->mekanikBarang())
        );
    }

    public function test_mekanik_cannot_edit_prev_barang(): void
    {
        $this->assertFalse(
            DepartmentAccess::canEditBarang($this->mekanikUser(), $this->prevBarang())
        );
    }

    public function test_prev_can_edit_own_department_barang(): void
    {
        $this->assertTrue(
            DepartmentAccess::canEditBarang($this->prevUser(), $this->prevBarang())
        );
    }

    public function test_prev_cannot_edit_mekanik_barang(): void
    {
        $this->assertFalse(
            DepartmentAccess::canEditBarang($this->prevUser(), $this->mekanikBarang())
        );
    }

    public function test_mekanik_cannot_delete_prev_barang(): void
    {
        $this->assertFalse(
            DepartmentAccess::canDeleteBarang($this->mekanikUser(), $this->prevBarang())
        );
    }

    public function test_prev_cannot_delete_mekanik_barang(): void
    {
        $this->assertFalse(
            DepartmentAccess::canDeleteBarang($this->prevUser(), $this->mekanikBarang())
        );
    }

    public function test_maintenance_cs_can_edit_both_departments_barang(): void
    {
        $user = $this->maintenanceUser();

        $this->assertTrue(DepartmentAccess::canEditBarang($user, $this->mekanikBarang()));
        $this->assertTrue(DepartmentAccess::canEditBarang($user, $this->prevBarang()));
    }

    public function test_admin_can_edit_both_departments_barang(): void
    {
        $user = $this->adminUser();

        $this->assertTrue(DepartmentAccess::canEditBarang($user, $this->mekanikBarang()));
        $this->assertTrue(DepartmentAccess::canEditBarang($user, $this->prevBarang()));
    }

    /*
    |--------------------------------------------------------------------------
    | HTTP ROUTE TESTS - WO EDIT
    |--------------------------------------------------------------------------
    */

    public function test_http_mekanik_can_access_edit_for_own_wo(): void
    {
        $response = $this->actingAs($this->mekanikUser())->get(
            route('work-orders.maintenance.edit', $this->mekanikWoId)
        );

        $response->assertStatus(200);
    }

    public function test_http_prev_can_access_edit_for_own_wo(): void
    {
        $response = $this->actingAs($this->prevUser())->get(
            route('work-orders.maintenance.edit', $this->prevWoId)
        );

        $response->assertStatus(200);
    }

    public function test_http_mekanik_cannot_access_edit_for_prev_wo(): void
    {
        $response = $this->actingAs($this->mekanikUser())->get(
            route('work-orders.maintenance.edit', $this->prevWoId)
        );

        $response->assertStatus(403);
    }

    public function test_http_prev_cannot_access_edit_for_mekanik_wo(): void
    {
        $response = $this->actingAs($this->prevUser())->get(
            route('work-orders.maintenance.edit', $this->mekanikWoId)
        );

        $response->assertStatus(403);
    }

    public function test_http_maintenance_cs_can_access_edit_for_both(): void
    {
        $user = $this->maintenanceUser();

        $this->actingAs($user)->get(
            route('work-orders.maintenance.edit', $this->mekanikWoId)
        )->assertStatus(200);

        $this->actingAs($user)->get(
            route('work-orders.maintenance.edit', $this->prevWoId)
        )->assertStatus(200);
    }

    /*
    |--------------------------------------------------------------------------
    | HTTP ROUTE TESTS - WO DELETE
    |--------------------------------------------------------------------------
    */

    public function test_http_mekanik_cannot_delete_prev_wo(): void
    {
        $response = $this->actingAs($this->mekanikUser())->delete(
            route('work-orders.maintenance.destroy', $this->prevWoId)
        );

        $response->assertStatus(403);
    }

    public function test_http_prev_cannot_delete_mekanik_wo(): void
    {
        $response = $this->actingAs($this->prevUser())->delete(
            route('work-orders.maintenance.destroy', $this->mekanikWoId)
        );

        $response->assertStatus(403);
    }

    public function test_http_maintenance_cs_can_delete_own_wo(): void
    {
        $response = $this->actingAs($this->maintenanceUser())->delete(
            route('work-orders.maintenance.destroy', $this->mekanikWoId)
        );

        $response->assertStatus(302);
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPED DEPARTMENT TEST
    |--------------------------------------------------------------------------
    */

    public function test_mekanik_scoped_department(): void
    {
        $this->assertEquals(
            DepartmentAccess::MEKANIK_MAINT,
            DepartmentAccess::scopedDepartment($this->mekanikUser())
        );
    }

    public function test_prev_scoped_department(): void
    {
        $this->assertEquals(
            DepartmentAccess::PREV_MAINT,
            DepartmentAccess::scopedDepartment($this->prevUser())
        );
    }

    public function test_maintenance_cs_null_scoped_department(): void
    {
        $this->assertNull(
            DepartmentAccess::scopedDepartment($this->maintenanceUser())
        );
    }

    public function test_maintenance_cs_can_view_all_departments(): void
    {
        $this->assertTrue(
            DepartmentAccess::canViewAllDepartments($this->maintenanceUser())
        );
    }
}
