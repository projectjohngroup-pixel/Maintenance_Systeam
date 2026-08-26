<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class SmokeErrorsTest extends TestCase
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

    public function test_collect_errors(): void
    {
        $this->withoutExceptionHandling();

        config(['app.debug' => true]);

        $user   = User::find(1);
        $uris   = [
            '/barang-keluar',
            '/barang-keluar/create',
            '/laporan-harian',
            '/purchase-requests',
            '/purchase-requests/create',
            '/work-orders/admin',
            '/work-orders/create',
        ];

        foreach ($uris as $uri) {
            try {
                $response = $this->actingAs($user)->get($uri);
                $status   = $response->getStatusCode();
                $content  = (string) $response->getContent();

                if ($status >= 400) {
                    $msg = '';

                    if (preg_match('/<h2[^>]*>(.*?)<\/h2>/s', $content, $m)) {
                        $msg = trim(html_entity_decode(strip_tags($m[1])));
                    }
                    if (!$msg && preg_match('/exception "(.*?)"\s*—?\s*(.{0,200})/s', strip_tags($content), $m)) {
                        $msg = trim($m[1] . ': ' . $m[2]);
                    }
                    if (!$msg) {
                        $msg = mb_substr(trim(strip_tags($content)), 0, 160);
                    }

                    fwrite(STDOUT, "[$status] $uri :: " . str_replace(PHP_EOL, ' ', $msg) . PHP_EOL);
                } else {
                    fwrite(STDOUT, "[200 ] $uri" . PHP_EOL);
                }
            } catch (\Throwable $e) {
                fwrite(STDOUT, "[EXC] $uri :: " . get_class($e) . ': ' . mb_substr($e->getMessage(), 0, 180) . PHP_EOL);
            }
        }

        $this->assertTrue(true);
    }
}
