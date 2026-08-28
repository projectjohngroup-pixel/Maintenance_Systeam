<?php

namespace App\Providers;

use App\Services\SystemGuard\InfrastructureMonitor;
use App\Services\SystemGuard\MonitoringService;
use App\Services\SystemGuard\RecoveryEngine;
use App\Services\SystemGuard\RecoveryManager;
use App\Services\SystemGuard\SystemGuard;
use App\Services\SystemGuard\SystemGuardDaemon;
use App\Services\SystemGuard\TunnelRestarter;
use App\Services\SystemGuard\VerificationService;
use Illuminate\Support\ServiceProvider;

class SystemGuardServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(MonitoringService::class);

        $this->app->singleton(InfrastructureMonitor::class);

        $this->app->singleton(TunnelRestarter::class);

        $this->app->singleton(RecoveryManager::class, function ($app) {
            return new RecoveryManager(
                $app->make(TunnelRestarter::class)
            );
        });

        $this->app->singleton(SystemGuardDaemon::class, function ($app) {
            return new SystemGuardDaemon(
                $app->make(InfrastructureMonitor::class),
                $app->make(TunnelRestarter::class),
                $app->make(RecoveryManager::class)
            );
        });

        $this->app->singleton(RecoveryEngine::class, function ($app) {
            return new RecoveryEngine(
                $app->make(MonitoringService::class),
                $app->make(TunnelRestarter::class)
            );
        });

        $this->app->singleton(VerificationService::class, function ($app) {
            return new VerificationService(
                $app->make(MonitoringService::class)
            );
        });

        $this->app->singleton(SystemGuard::class, function ($app) {
            return new SystemGuard(
                $app->make(MonitoringService::class),
                $app->make(RecoveryEngine::class),
                $app->make(VerificationService::class)
            );
        });
    }

    public function boot(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/system-guard.php',
            'system-guard'
        );
    }
}
