<?php

namespace Shahkochaki\TaraService;

use Illuminate\Support\ServiceProvider;

class TaraServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/tara.php', 'tara');

        $this->app->singleton(TaraService::class, function ($app) {
            $config = config('tara');
            return new TaraService(
                $config['branch_code'] ?? null,
                $config['username'] ?? null,
                $config['password'] ?? null,
                $config['base_url'] ?? 'https://stage.tara-club.ir/club/api/v1'
            );
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/tara.php' => config_path('tara.php'),
            ], 'tara-config');
        }
    }
}
