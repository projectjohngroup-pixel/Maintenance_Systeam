<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class ManagerDetailCheckTest extends TestCase
{
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

        \Illuminate\Support\Facades\DB::purge('mysql');
    }

    private function manager(): ?User
    {
        return User::where('role', 'Manager')
            ->where('status', 'AKTIF')
            ->first();
    }

    public function test_manager_redirected_from_dashboard_to_manager_dashboard(): void
    {
        $user = $this->manager();

        if (!$user) {
            $this->markTestSkipped('User Manager tidak ditemukan.');
        }

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertRedirect(route('dashboard.manager'));
    }

    public function test_manager_can_open_manager_dashboard(): void
    {
        $user = $this->manager();

        if (!$user) {
            $this->markTestSkipped('User Manager tidak ditemukan.');
        }

        $response = $this->actingAs($user)->get('/dashboard/manager');

        $response->assertOk();

        $response->assertSeeText('Dashboard Manager');
    }

    public function test_data_endpoint_all_periods(): void
    {
        $user = $this->manager();

        if (!$user) {
            $this->markTestSkipped('User Manager tidak ditemukan.');
        }

        foreach (['day', 'week', 'month', 'year'] as $period) {

            $response = $this->actingAs($user)
                ->getJson('/dashboard/manager/data?period=' . $period);

            $response->assertStatus(200);

            $json = $response->json();

            /* Struktur utama */
            $this->assertArrayHasKey('meta', $json);
            $this->assertArrayHasKey('kpi', $json);
            $this->assertArrayHasKey('trends', $json);
            $this->assertArrayHasKey('statusCounts', $json);
            $this->assertArrayHasKey('priorities', $json);
            $this->assertArrayHasKey('rankings', $json);
            $this->assertArrayHasKey('completion', $json);
            $this->assertArrayHasKey('delay', $json);
            $this->assertArrayHasKey('machines', $json);
            $this->assertArrayHasKey('inventory', $json);
            $this->assertArrayHasKey('alerts', $json);

            /* Meta periode */
            $this->assertEquals($period, $json['meta']['period']);
            $this->assertNotEmpty($json['meta']['labels']);

            /* KPI */
            foreach ([
                'woMasuk', 'woSelesai', 'woOpen',
                'woProgress', 'woHold', 'woOverdue',
                'emergency', 'urgent',
                'stokAman', 'stokMenipis', 'stokHabis',
                'barangMasukQty', 'barangKeluarQty',
            ] as $kpiKey) {
                $this->assertArrayHasKey(
                    $kpiKey,
                    $json['kpi'],
                    "KPI {$kpiKey} hilang pada periode {$period}"
                );
            }

            /* Trend arrays konsisten dengan labels */
            $count = count($json['meta']['labels']);

            $this->assertCount(
                $count,
                $json['trends']['wo']['masuk']
            );
            $this->assertCount(
                $count,
                $json['trends']['wo']['selesai']
            );
            $this->assertCount(
                $count,
                $json['trends']['inventory']['masuk']
            );
            $this->assertCount(
                $count,
                $json['trends']['inventory']['keluar']
            );

            /* Trend status & prioritas (dipakai chart JS) */
            foreach (['open', 'progress', 'hold', 'selesai'] as $sg) {
                $this->assertArrayHasKey(
                    $sg,
                    $json['trends']['status']['series'],
                    "Series status {$sg} hilang pada periode {$period}"
                );
                $this->assertCount(
                    $count,
                    $json['trends']['status']['series'][$sg]
                );
            }

            foreach (['EMERGENCY', 'URGENT', 'NORMAL'] as $pk) {
                $this->assertArrayHasKey(
                    $pk,
                    $json['trends']['priority']['series'],
                    "Series prioritas {$pk} hilang pada periode {$period}"
                );
                $this->assertCount(
                    $count,
                    $json['trends']['priority']['series'][$pk]
                );
            }

            $this->assertArrayHasKey(
                'values',
                $json['trends']['kerusakanMesin']
            );

            /* Ranking memiliki field percent */
            if (!empty($json['rankings']['department'])) {

                $this->assertArrayHasKey(
                    'percent',
                    $json['rankings']['department'][0]
                );

            }

            /* Inventory stok */
            $this->assertArrayHasKey('total', $json['inventory']['stok']);
            $this->assertArrayHasKey('aman', $json['inventory']['stok']);
            $this->assertArrayHasKey('menipis', $json['inventory']['stok']);
            $this->assertArrayHasKey('habis', $json['inventory']['stok']);
            $this->assertArrayHasKey('rataRata', $json['inventory']['stok']);

            /* Downtime */
            $this->assertArrayHasKey('totalHours', $json['machines']['downtime']);
            $this->assertArrayHasKey('byMesin', $json['machines']['downtime']);
            $this->assertArrayHasKey('byArea', $json['machines']['downtime']);

            /* Delay (dipakai section keterlambatan) */
            foreach (
                ['terlambat', 'belumSelesai', 'terlama',
                 'byArea', 'byMesin', 'byDepartment'] as $dk
            ) {
                $this->assertArrayHasKey(
                    $dk,
                    $json['delay'],
                    "Delay {$dk} hilang pada periode {$period}"
                );
            }

            /* Inventory pelengkap chart & daftar */
            $this->assertArrayHasKey('masukTotal', $json['inventory']);
            $this->assertArrayHasKey('keluarTotal', $json['inventory']);
            $this->assertArrayHasKey('topKeluar', $json['inventory']);
            $this->assertArrayHasKey('kritis', $json['inventory']);

            /* Alerts berbentuk list level + text */
            foreach ($json['alerts'] as $alert) {
                $this->assertArrayHasKey('level', $alert);
                $this->assertArrayHasKey('text', $alert);
            }

            /* Window dipakai tombol Lihat Detail */
            $this->assertArrayHasKey('start', $json['window']);
            $this->assertArrayHasKey('end', $json['window']);

        }
    }

    public function test_manager_detail_endpoint_returns_json(): void
    {
        $user = $this->manager();

        if (!$user) {
            $this->markTestSkipped('User Manager tidak ditemukan.');
        }

        foreach ([
            'total',
            'in-progress',
            'done',
            'emergency',
            'overdue',
            'completion',
            'on-time',
            'open',
            'pending',
            'close',
            'hold',
            'top-machines',
            'top-areas',
            'top-categories',
            'top-departments',
            'delay-reasons',
            'wo-longest',
            'downtime-by-mesin',
            'downtime-by-area',
            'delay-by-area',
            'delay-by-mesin',
            'delay-by-department',
            'inventory-top-keluar',
            'inventory-kritis',
            'inventory-stok',
            'chart-trend',
            'chart-status',
            'chart-priority',
        ] as $metric) {

            $response = $this->actingAs($user)
                ->getJson(
                    '/dashboard/manager/detail?metric=' . $metric
                    . '&start=' . now()->startOfYear()->toDateString()
                    . '&end=' . now()->endOfYear()->toDateString()
                );

            $response->assertStatus(200);

            $json = $response->json();

            $this->assertArrayHasKey(
                'title',
                $json,
                "Metric {$metric} tanpa title"
            );

            $this->assertArrayHasKey('rows', $json);

        }
    }

    public function test_detail_wo_list_drilldown(): void
    {
        $user = $this->manager();

        if (!$user) {
            $this->markTestSkipped('User Manager tidak ditemukan.');
        }

        $response = $this->actingAs($user)
            ->getJson(
                '/dashboard/manager/detail?metric=wo-list'
                . '&field=departemen&value=PRODUKSI'
                . '&start=' . now()->startOfYear()->toDateString()
                . '&end=' . now()->endOfYear()->toDateString()
            );

        $response->assertStatus(200);

        $json = $response->json();

        $this->assertEquals('Daftar Work Order', $json['title']);
    }

    public function test_non_manager_cannot_access_manager_endpoints(): void
    {
        $user = User::whereIn('role', ['User', 'Maintenance'])
            ->where('status', 'AKTIF')
            ->first();

        if (!$user) {
            $this->markTestSkipped('User non-manager tidak ditemukan.');
        }

        $this->actingAs($user)
            ->getJson('/dashboard/manager/data?period=month')
            ->assertStatus(403);

        $this->actingAs($user)
            ->getJson('/dashboard/manager/detail?metric=total&year=' . now()->year)
            ->assertStatus(403);

        $this->actingAs($user)
            ->get('/dashboard/manager')
            ->assertStatus(403);
    }

    public function test_admin_still_has_full_access_and_user_flows_untouched(): void
    {
        $admin = User::whereIn('role', ['Admin', 'Administrator'])
            ->where('status', 'AKTIF')
            ->first();

        if (!$admin) {
            $this->markTestSkipped('User Admin tidak ditemukan.');
        }

        /* Admin boleh membuka dashboard biasa (bukan halaman manager). */
        $this->actingAs($admin)->get('/dashboard')->assertOk();
        $this->actingAs($admin)->get('/work-orders')->assertOk();
    }
}
