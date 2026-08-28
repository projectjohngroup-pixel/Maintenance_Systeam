<?php

namespace Tests\Unit;

use App\Models\SystemGuard\MonitorConfig;
use App\Models\SystemGuard\MonitorResult;
use App\Services\SystemGuard\MonitoringService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MonitoringServiceTest extends TestCase
{
    use RefreshDatabase;

    private MonitoringService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new class extends MonitoringService {
            public array $dnsResults = [];
            public array $connectionResults = [];
            public array $httpResults = [];

            public function setDnsResult(string $domain, array $result): void
            {
                $this->dnsResults[$domain] = $result;
            }

            public function setConnectionResult(string $host, array $result): void
            {
                $this->connectionResults[$host] = $result;
            }

            public function setHttpResult(string $url, array $result): void
            {
                $this->httpResults[$url] = $result;
            }

            public function checkDns(MonitorConfig $config): array
            {
                $domain = $config->domain;

                if (isset($this->dnsResults[$domain])) {
                    return $this->dnsResults[$domain];
                }

                return parent::checkDns($config);
            }

            public function checkConnection(MonitorConfig $config): array
            {
                $parsed = parse_url($config->target_url);
                $host = $parsed['host'] ?? '';

                if (isset($this->connectionResults[$host])) {
                    return $this->connectionResults[$host];
                }

                return parent::checkConnection($config);
            }

            public function checkHttp(MonitorConfig $config): array
            {
                if (isset($this->httpResults[$config->target_url])) {
                    return $this->httpResults[$config->target_url];
                }

                return parent::checkHttp($config);
            }
        };
    }

    private function createConfig(array $overrides = []): MonitorConfig
    {
        return MonitorConfig::create(array_merge([
            'name'       => 'Test Target',
            'target_url' => 'https://test.example.com',
            'type'       => 'http',
            'is_active'  => true,
            'timeout_seconds' => 5,
            'expected_status_code' => 200,
            'response_time_threshold_ms' => 5000,
            'max_retries' => 3,
            'retry_delay_seconds' => 5,
            'recovery_actions' => ['retry_connection'],
            'auto_recovery_enabled' => false,
        ], $overrides));
    }

    private function setupHealthyMocks(string $url = 'https://test.example.com'): void
    {
        $this->service->setDnsResult('test.example.com', [
            'resolved'   => true,
            'records'    => [['ip' => '1.2.3.4']],
            'error'      => null,
            'error_type' => null,
        ]);

        $this->service->setConnectionResult('test.example.com', [
            'connected'  => true,
            'error'      => null,
            'error_type' => null,
        ]);

        $this->service->setHttpResult($url, [
            'status_code'      => 200,
            'response_time_ms' => 150,
            'successful'       => true,
            'error'            => null,
            'error_type'       => null,
            'body'             => 'OK',
            'headers'          => [],
        ]);
    }

    // ==========================================================
    // NORMAL CONDITIONS
    // ==========================================================

    public function test_http_200_returns_healthy(): void
    {
        $config = $this->createConfig();
        $this->setupHealthyMocks();

        $result = $this->service->check($config);

        $this->assertTrue($result->isHealthy());
        $this->assertEquals('ONLINE', $result->status);
        $this->assertEquals(200, $result->http_status_code);
        $this->assertTrue($result->dns_resolved);
        $this->assertTrue($result->connection_successful);
        $this->assertTrue($result->http_successful);
        $this->assertNull($result->error_type);
    }

    public function test_http_302_returns_healthy(): void
    {
        $config = $this->createConfig();
        $this->setupHealthyMocks();

        $this->service->setHttpResult($config->target_url, [
            'status_code'      => 302,
            'response_time_ms' => 200,
            'successful'       => true,
            'error'            => null,
            'error_type'       => null,
            'body'             => 'Redirect',
            'headers'          => ['Location' => '/new'],
        ]);

        $result = $this->service->check($config);

        $this->assertTrue($result->isHealthy());
        $this->assertEquals('ONLINE', $result->status);
        $this->assertEquals(302, $result->http_status_code);
    }

    // ==========================================================
    // HTTP FAILURES
    // ==========================================================

    public function test_http_404_returns_gangguan(): void
    {
        $config = $this->createConfig();
        $this->setupHealthyMocks();

        $this->service->setHttpResult($config->target_url, [
            'status_code'      => 404,
            'response_time_ms' => 100,
            'successful'       => false,
            'error'            => 'HTTP 404 Client Error',
            'error_type'       => 'HTTP_4XX',
            'body'             => 'Not Found',
            'headers'          => [],
        ]);

        $result = $this->service->check($config);

        $this->assertFalse($result->isHealthy());
        $this->assertEquals('GANGGUAN', $result->status);
        $this->assertEquals('HTTP', $result->category);
        $this->assertEquals('HTTP_4XX', $result->error_type);
        $this->assertEquals(404, $result->http_status_code);
    }

    public function test_http_500_returns_gangguan(): void
    {
        $config = $this->createConfig();
        $this->setupHealthyMocks();

        $this->service->setHttpResult($config->target_url, [
            'status_code'      => 500,
            'response_time_ms' => 100,
            'successful'       => false,
            'error'            => 'HTTP 500 Server Error',
            'error_type'       => 'HTTP_5XX',
            'body'             => 'Server Error',
            'headers'          => [],
        ]);

        $result = $this->service->check($config);

        $this->assertFalse($result->isHealthy());
        $this->assertEquals('GANGGUAN', $result->status);
        $this->assertEquals('HTTP_5XX', $result->error_type);
        $this->assertEquals('HIGH', $result->severity);
    }

    public function test_http_timeout_returns_gangguan(): void
    {
        $config = $this->createConfig();
        $this->setupHealthyMocks();

        $this->service->setHttpResult($config->target_url, [
            'status_code'      => null,
            'response_time_ms' => null,
            'successful'       => false,
            'error'            => 'HTTP request failed: Operation timed out',
            'error_type'       => 'TIMEOUT',
            'body'             => null,
            'headers'          => null,
        ]);

        $result = $this->service->check($config);

        $this->assertFalse($result->isHealthy());
        $this->assertEquals('GANGGUAN', $result->status);
        $this->assertEquals('TIMEOUT', $result->error_type);
    }

    // ==========================================================
    // DNS FAILURES
    // ==========================================================

    public function test_dns_nxdomain_returns_gangguan(): void
    {
        $config = $this->createConfig([
            'target_url' => 'https://oxygen-jeff-decrease-conclusion.trycloudflare.com',
        ]);

        $this->service->setDnsResult('oxygen-jeff-decrease-conclusion.trycloudflare.com', [
            'resolved'   => false,
            'error'      => 'DNS probe finished: NXDOMAIN for oxygen-jeff-decrease-conclusion.trycloudflare.com',
            'error_type' => 'DNS_NXDOMAIN',
        ]);

        $result = $this->service->check($config);

        $this->assertFalse($result->isHealthy());
        $this->assertEquals('GANGGUAN', $result->status);
        $this->assertEquals('DNS', $result->category);
        $this->assertEquals('DNS_NXDOMAIN', $result->error_type);
        $this->assertEquals('HIGH', $result->severity);
        $this->assertFalse($result->dns_resolved);
        $this->assertStringContainsString('NXDOMAIN', $result->error_message);
    }

    public function test_dns_error_returns_gangguan(): void
    {
        $config = $this->createConfig([
            'target_url' => 'https://invalid.domain.test',
        ]);

        $this->service->setDnsResult('invalid.domain.test', [
            'resolved'   => false,
            'error'      => 'DNS resolution failed: server failure',
            'error_type' => 'DNS_ERROR',
        ]);

        $result = $this->service->check($config);

        $this->assertFalse($result->isHealthy());
        $this->assertEquals('GANGGUAN', $result->status);
        $this->assertEquals('DNS_ERROR', $result->error_type);
        $this->assertEquals('HIGH', $result->severity);
    }

    // ==========================================================
    // CONNECTION FAILURES
    // ==========================================================

    public function test_connection_refused_returns_gangguan(): void
    {
        $config = $this->createConfig();

        $this->service->setDnsResult('test.example.com', [
            'resolved'   => true,
            'records'    => [['ip' => '1.2.3.4']],
            'error'      => null,
            'error_type' => null,
        ]);

        $this->service->setConnectionResult('test.example.com', [
            'connected'  => false,
            'error'      => 'Connection failed: Connection refused (errno: 111)',
            'error_type' => 'CONNECTION_REFUSED',
        ]);

        $result = $this->service->check($config);

        $this->assertFalse($result->isHealthy());
        $this->assertEquals('GANGGUAN', $result->status);
        $this->assertEquals('CONNECTION', $result->category);
        $this->assertEquals('CONNECTION_REFUSED', $result->error_type);
        $this->assertEquals('HIGH', $result->severity);
    }

    public function test_connection_timeout_returns_gangguan(): void
    {
        $config = $this->createConfig();

        $this->service->setDnsResult('test.example.com', [
            'resolved'   => true,
            'records'    => [['ip' => '1.2.3.4']],
            'error'      => null,
            'error_type' => null,
        ]);

        $this->service->setConnectionResult('test.example.com', [
            'connected'  => false,
            'error'      => 'Connection failed: Connection timed out (errno: 110)',
            'error_type' => 'TIMEOUT',
        ]);

        $result = $this->service->check($config);

        $this->assertFalse($result->isHealthy());
        $this->assertEquals('GANGGUAN', $result->status);
        $this->assertEquals('TIMEOUT', $result->error_type);
        $this->assertEquals('MEDIUM', $result->severity);
    }

    // ==========================================================
    // PERFORMANCE
    // ==========================================================

    public function test_slow_response_returns_waspada(): void
    {
        $config = $this->createConfig([
            'response_time_threshold_ms' => 3000,
        ]);

        $this->service->setDnsResult('test.example.com', [
            'resolved'   => true,
            'records'    => [['ip' => '1.2.3.4']],
            'error'      => null,
            'error_type' => null,
        ]);

        $this->service->setConnectionResult('test.example.com', [
            'connected'  => true,
            'error'      => null,
            'error_type' => null,
        ]);

        $this->service->setHttpResult($config->target_url, [
            'status_code'      => 200,
            'response_time_ms' => 6000,
            'successful'       => true,
            'error'            => null,
            'error_type'       => null,
            'body'             => 'Slow OK',
            'headers'          => [],
        ]);

        $result = $this->service->check($config);

        $this->assertEquals('WASPADA', $result->status);
        $this->assertEquals('PERFORMANCE', $result->category);
        $this->assertGreaterThan(3000, $result->response_time_ms);
    }

    // ==========================================================
    // MODEL INTEGRATION
    // ==========================================================

    public function test_check_saves_result_to_database(): void
    {
        $config = $this->createConfig();
        $this->setupHealthyMocks();

        $result = $this->service->check($config);

        $this->assertDatabaseHas('monitor_results', [
            'monitor_config_id' => $config->id,
            'status'            => 'ONLINE',
            'http_status_code'  => 200,
        ]);

        $this->assertInstanceOf(MonitorResult::class, $result);
    }

    public function test_check_all_processes_all_active_configs(): void
    {
        $config1 = $this->createConfig(['name' => 'Target 1', 'target_url' => 'https://one.example.com']);
        $config2 = $this->createConfig(['name' => 'Target 2', 'target_url' => 'https://two.example.com']);
        $this->createConfig(['name' => 'Inactive', 'target_url' => 'https://inactive.example.com', 'is_active' => false]);

        $this->service->setDnsResult('one.example.com', [
            'resolved'   => true,
            'records'    => [['ip' => '1.2.3.4']],
            'error'      => null,
            'error_type' => null,
        ]);

        $this->service->setConnectionResult('one.example.com', [
            'connected'  => true,
            'error'      => null,
            'error_type' => null,
        ]);

        $this->service->setHttpResult('https://one.example.com', [
            'status_code'      => 200,
            'response_time_ms' => 100,
            'successful'       => true,
            'error'            => null,
            'error_type'       => null,
            'body'             => 'OK',
            'headers'          => [],
        ]);

        $this->service->setDnsResult('two.example.com', [
            'resolved'   => false,
            'error'      => 'NXDOMAIN',
            'error_type' => 'DNS_NXDOMAIN',
        ]);

        $results = $this->service->checkAll();

        $this->assertCount(2, $results);

        $statuses = array_map(fn ($r) => $r['result']->status, $results);

        $this->assertContains('ONLINE', $statuses);
        $this->assertContains('GANGGUAN', $statuses);
    }
}
