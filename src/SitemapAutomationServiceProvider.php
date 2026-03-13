<?php

namespace Dev1kochiCrypto\SitemapKit;

use Illuminate\Support\ServiceProvider;
use Dev1kochiCrypto\SitemapKit\Observers\SitemapObserver;
use Dev1kochiCrypto\SitemapKit\Console\Commands\SitemapGenerateCommand;

class SitemapAutomationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/sitemap_automation.php', 'sitemap_automation'
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Publish config
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/sitemap_automation.php' => config_path('sitemap_automation.php'),
            ], 'sitemap-config');

            $this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/sitemap-automation'),
            ], 'sitemap-kit-views');

            $this->commands([
                SitemapGenerateCommand::class,
            ]);
        }

        // Register observers
        $models = config('sitemap_automation.models', []);
        foreach ($models as $model) {
            if (class_exists($model)) {
                $model::observe(SitemapObserver::class);
            }
        }

        // Load views
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'sitemap-automation');

        // Load routes
        $middleware = config('sitemap_automation.middleware', []);
        
        \Illuminate\Support\Facades\Route::middleware($middleware)
            ->group(function () {
                $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
            });
    }
}
