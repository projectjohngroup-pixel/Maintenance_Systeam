<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class DashboardRenderCheckTest extends TestCase
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

    public function test_maintenance_dashboard_shows_real_numbers(): void
    {
        $user = User::where('role', 'Maintenance')->first();

        $content = (string) $this->actingAs($user)->get('/dashboard')->getContent();

        preg_match_all('/kpi-value[^>]*>\s*([^<]+?)\s*</', $content, $m);

        fwrite(STDOUT, 'KPI VALUES: ' . implode(' | ', $m[1]) . PHP_EOL);

        $this->assertStringContainsString('1', implode($m[1]));
    }
}
