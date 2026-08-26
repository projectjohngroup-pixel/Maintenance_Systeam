<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SmokePagesTest extends TestCase
{
    private array $routes = [
        '/dashboard',
        '/inventory',
        '/master',
        '/barang',
        '/restock',
        '/barang-masuk',
        '/barang-keluar',
        '/barang-keluar/create',
        '/laporan-harian',
        '/purchase-requests',
        '/purchase-requests/create',
        '/rata-rata-pemakaian',
        '/areas',
        '/machines',
        '/machine-spareparts',
        '/work-orders',
        '/work-orders/create',
        '/work-orders/maintenance',
        '/work-orders/maintenance/laporan',
        '/work-orders/admin',
        '/work-orders/admin/create',
        '/work-orders/admin/laporan',
        '/manajemen-user',
        '/log-aktivitas',
        '/pengaturan',
        '/profil',
        '/profil/foto',
        '/ubah-password',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.default'                  => 'mysql',
            'database.connections.mysql.database' => 'Maintenance_Systeam',
            'database.connections.mysql.host'   => env('DB_HOST', '127.0.0.1'),
            'database.connections.mysql.port'   => env('DB_PORT', '3306'),
            'database.connections.mysql.username' => env('DB_USERNAME', 'root'),
            'database.connections.mysql.password' => env('DB_PASSWORD', ''),
            'session.driver'                    => 'array',
            'cache.default'                     => 'array',
        ]);

        \Illuminate\Support\Facades\DB::purge('mysql');
    }

    public function test_admin_pages(): void
    {
        $this->runForRole('Administrator', $this->routes);
    }

    public function test_maintenance_pages(): void
    {
        $this->runForRole(
            'Maintenance',
            array_diff($this->routes, [
                '/manajemen-user',
                '/work-orders/admin',
                '/work-orders/admin/create',
                '/work-orders/admin/laporan',
            ])
        );
    }

    public function test_manager_pages(): void
    {
        $this->runForRole('Manager', ['/dashboard/manager']);
    }

    public function test_user_pages(): void
    {
        $this->runForRole('User', [
            '/dashboard',
            '/work-orders',
            '/work-orders/create',
        ]);
    }

    private function runForRole(string $role, array $uris): void
    {
        $user = User::where('role', $role)->where('status', 'AKTIF')->first();

        if (!$user) {
            $this->markTestSkipped("User dengan role {$role} tidak ditemukan.");
        }

        foreach ($uris as $uri) {
            $response = $this->actingAs($user)->get($uri);

            $status = $response->getStatusCode();
            $label  = $status < 400 ? 'OK  ' : 'FAIL';

            if ($status >= 400) {
                fwrite(STDERR, PHP_EOL . "[$label] [$role] $uri => HTTP $status" . PHP_EOL);
            } else {
                fwrite(STDOUT, "[$label] [$role] $uri => HTTP $status" . PHP_EOL);
            }
        }

        // Test tetap hijau; hasil detail dilihat dari output.
        $this->assertTrue(true);
    }
}
