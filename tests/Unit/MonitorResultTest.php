<?php

namespace Tests\Unit;

use App\Models\SystemGuard\MonitorConfig;
use App\Models\SystemGuard\MonitorResult;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MonitorResultTest extends TestCase
{
    use RefreshDatabase;

    private function createConfig(): MonitorConfig
    {
        return MonitorConfig::create([
            'name'       => 'Test Target',
            'target_url' => 'https://example.com',
            'type'       => 'http',
            'is_active'  => true,
        ]);
    }

    public function test_create_monitor_result(): void
    {
        $config = $this->createConfig();

        $result = MonitorResult::create([
            'monitor_config_id' => $config->id,
            'status'            => 'ONLINE',
            'category'          => 'normal',
            'dns_resolved'      => true,
            'connection_successful' => true,
            'http_successful'   => true,
            'http_status_code'  => 200,
            'response_time_ms'  => 820,
            'severity'          => 'normal',
        ]);

        $this->assertDatabaseHas('monitor_results', [
            'status'       => 'ONLINE',
            'http_status_code' => 200,
        ]);
    }

    public function test_is_healthy_returns_true_for_online(): void
    {
        $config = $this->createConfig();

        $result = MonitorResult::create([
            'monitor_config_id' => $config->id,
            'status'            => 'ONLINE',
            'category'          => 'normal',
            'dns_resolved'      => true,
            'connection_successful' => true,
            'http_successful'   => true,
        ]);

        $this->assertTrue($result->isHealthy());
    }

    public function test_is_healthy_returns_false_for_gangguan(): void
    {
        $config = $this->createConfig();

        $result = MonitorResult::create([
            'monitor_config_id' => $config->id,
            'status'            => 'GANGGUAN',
            'category'          => 'DNS',
            'dns_resolved'      => false,
            'connection_successful' => false,
            'http_successful'   => false,
            'error_type'        => 'DNS_NXDOMAIN',
        ]);

        $this->assertFalse($result->isHealthy());
    }

    public function test_is_dns_failure_for_nxdomain(): void
    {
        $config = $this->createConfig();

        $result = MonitorResult::create([
            'monitor_config_id' => $config->id,
            'status'            => 'GANGGUAN',
            'category'          => 'DNS',
            'dns_resolved'      => false,
            'connection_successful' => false,
            'http_successful'   => false,
            'error_type'        => 'DNS_NXDOMAIN',
            'error_message'     => 'DNS probe finished: NXDOMAIN',
        ]);

        $this->assertTrue($result->isDnsFailure());
    }

    public function test_is_http_failure_for_4xx(): void
    {
        $config = $this->createConfig();

        $result = MonitorResult::create([
            'monitor_config_id' => $config->id,
            'status'            => 'GANGGUAN',
            'category'          => 'HTTP',
            'dns_resolved'      => true,
            'connection_successful' => true,
            'http_successful'   => false,
            'http_status_code'  => 404,
            'error_type'        => 'HTTP_4XX',
        ]);

        $this->assertTrue($result->isHttpFailure());
    }

    public function test_is_http_failure_for_5xx(): void
    {
        $config = $this->createConfig();

        $result = MonitorResult::create([
            'monitor_config_id' => $config->id,
            'status'            => 'GANGGUAN',
            'category'          => 'HTTP',
            'dns_resolved'      => true,
            'connection_successful' => true,
            'http_successful'   => false,
            'http_status_code'  => 500,
            'error_type'        => 'HTTP_5XX',
        ]);

        $this->assertTrue($result->isHttpFailure());
    }

    public function test_is_timeout(): void
    {
        $config = $this->createConfig();

        $result = MonitorResult::create([
            'monitor_config_id' => $config->id,
            'status'            => 'GANGGUAN',
            'category'          => 'TIMEOUT',
            'dns_resolved'      => true,
            'connection_successful' => false,
            'http_successful'   => false,
            'error_type'        => 'TIMEOUT',
        ]);

        $this->assertTrue($result->isTimeout());
    }

    public function test_is_connection_failure(): void
    {
        $config = $this->createConfig();

        $result = MonitorResult::create([
            'monitor_config_id' => $config->id,
            'status'            => 'GANGGUAN',
            'category'          => 'CONNECTION',
            'dns_resolved'      => true,
            'connection_successful' => false,
            'http_successful'   => false,
            'error_type'        => 'CONNECTION_REFUSED',
        ]);

        $this->assertTrue($result->isConnectionFailure());
    }

    public function test_response_time_label_in_ms(): void
    {
        $config = $this->createConfig();

        $result = MonitorResult::create([
            'monitor_config_id' => $config->id,
            'status'            => 'ONLINE',
            'category'          => 'normal',
            'dns_resolved'      => true,
            'connection_successful' => true,
            'http_successful'   => true,
            'response_time_ms'  => 500,
        ]);

        $this->assertEquals('500 ms', $result->responseTimeLabel());
    }

    public function test_response_time_label_in_seconds(): void
    {
        $config = $this->createConfig();

        $result = MonitorResult::create([
            'monitor_config_id' => $config->id,
            'status'            => 'WASPADA',
            'category'          => 'PERFORMANCE',
            'dns_resolved'      => true,
            'connection_successful' => true,
            'http_successful'   => true,
            'response_time_ms'  => 6500,
        ]);

        $this->assertEquals('6.5 s', $result->responseTimeLabel());
    }

    public function test_response_time_label_na_when_null(): void
    {
        $config = $this->createConfig();

        $result = MonitorResult::create([
            'monitor_config_id' => $config->id,
            'status'            => 'GANGGUAN',
            'category'          => 'DNS',
            'dns_resolved'      => false,
            'connection_successful' => false,
            'http_successful'   => false,
        ]);

        $this->assertEquals('N/A', $result->responseTimeLabel());
    }

    public function test_scope_healthy(): void
    {
        $config = $this->createConfig();

        MonitorResult::create([
            'monitor_config_id' => $config->id,
            'status'            => 'ONLINE',
            'category'          => 'normal',
            'dns_resolved'      => true,
            'connection_successful' => true,
            'http_successful'   => true,
        ]);

        MonitorResult::create([
            'monitor_config_id' => $config->id,
            'status'            => 'GANGGUAN',
            'category'          => 'DNS',
            'dns_resolved'      => false,
            'connection_successful' => false,
            'http_successful'   => false,
        ]);

        $this->assertCount(1, MonitorResult::healthy()->get());
        $this->assertCount(1, MonitorResult::unhealthy()->get());
    }
}
