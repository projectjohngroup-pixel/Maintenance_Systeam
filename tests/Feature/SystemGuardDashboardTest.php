<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class SystemGuardDashboardTest extends TestCase
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

    public function test_admin_can_access_system_guard_dashboard(): void
    {
        $user = User::where('role', 'Administrator')->first();

        if (!$user) {
            $this->markTestSkipped('Admin user not found');
        }

        $response = $this->actingAs($user)->get('/system-guard');

        $response->assertStatus(200);
        $response->assertSee('System Guard');
        $response->assertSee('24/7');
        $response->assertSee('LIVE');
    }

    public function test_system_guard_api_poll_returns_json(): void
    {
        $user = User::where('role', 'Administrator')->first();

        if (!$user) {
            $this->markTestSkipped('Admin user not found');
        }

        $response = $this->actingAs($user)->get('/system-guard/api/poll');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/json');

        $data = $response->json();
        $this->assertArrayHasKey('timestamp', $data);
        $this->assertArrayHasKey('summary', $data);
        $this->assertArrayHasKey('targets', $data);
        $this->assertArrayHasKey('liveFeed', $data);
        $this->assertArrayHasKey('recovery', $data);
    }

    public function test_system_guard_api_status_returns_json(): void
    {
        $user = User::where('role', 'Administrator')->first();

        if (!$user) {
            $this->markTestSkipped('Admin user not found');
        }

        $response = $this->actingAs($user)->get('/system-guard/api/status');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/json');
    }

    public function test_dashboard_handles_empty_data(): void
    {
        $user = User::where('role', 'Administrator')->first();

        if (!$user) {
            $this->markTestSkipped('Admin user not found');
        }

        $response = $this->actingAs($user)->get('/system-guard');

        $response->assertStatus(200);
        $response->assertSee('Belum ada');
    }

    public function test_period_filter_24h(): void
    {
        $user = User::where('role', 'Administrator')->first();

        if (!$user) {
            $this->markTestSkipped('Admin user not found');
        }

        $response = $this->actingAs($user)->get('/system-guard?period=24h');

        $response->assertStatus(200);
        $response->assertSee('24 Jam');
    }

    public function test_period_filter_30d(): void
    {
        $user = User::where('role', 'Administrator')->first();

        if (!$user) {
            $this->markTestSkipped('Admin user not found');
        }

        $response = $this->actingAs($user)->get('/system-guard?period=30d');

        $response->assertStatus(200);
        $response->assertSee('30 Hari');
    }

    public function test_maintenance_user_can_access_system_guard(): void
    {
        $user = User::where('role', 'Maintenance')->first();

        if (!$user) {
            $this->markTestSkipped('Maintenance user not found');
        }

        $response = $this->actingAs($user)->get('/system-guard');

        $response->assertStatus(200);
    }

    public function test_incident_detail_page_without_data(): void
    {
        $user = User::where('role', 'Administrator')->first();

        if (!$user) {
            $this->markTestSkipped('Admin user not found');
        }

        $response = $this->actingAs($user)->get('/system-guard/incident/NONEXISTENT');

        $response->assertStatus(404);
    }
}
