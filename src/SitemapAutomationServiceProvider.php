<?php

namespace MightyWarnersKochi\SitemapKit;

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use MightyWarnersKochi\SitemapKit\Console\Commands\SitemapGenerateCommand;
use MightyWarnersKochi\SitemapKit\Http\Middleware\RecordMissingUrls;
use MightyWarnersKochi\SitemapKit\Http\Middleware\ResolveUrlRedirects;
use MightyWarnersKochi\SitemapKit\Observers\SitemapObserver;
use MightyWarnersKochi\SitemapKit\Observers\UrlRedirectObserver;
use MightyWarnersKochi\SitemapKit\Support\RedirectConfiguration;

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
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

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

        $modelsWithConfig = config('sitemap_automation.models', []);
        foreach ($modelsWithConfig as $key => $value) {
            $model = is_numeric($key) ? $value : $key;
            if (is_string($model) && class_exists($model)) {
                $model::observe(SitemapObserver::class);
            }
        }

        if (config('sitemap_automation.redirects.enabled', true)) {
            foreach (array_keys(RedirectConfiguration::mergedModels()) as $modelClass) {
                if (is_string($modelClass) && class_exists($modelClass)) {
                    $modelClass::observe(UrlRedirectObserver::class);
                }
            }
        }

        if (
            config('sitemap_automation.redirects.enabled', true)
            && config('sitemap_automation.redirects.register_global_middleware', true)
        ) {
            $kernel = $this->app->make(Kernel::class);
            if (method_exists($kernel, 'prependMiddleware')) {
                $kernel->prependMiddleware(ResolveUrlRedirects::class);
            }
        }

        if (config('sitemap_automation.not_found_logging.enabled', true)) {
            $kernel = $this->app->make(Kernel::class);
            if (method_exists($kernel, 'pushMiddleware')) {
                $kernel->pushMiddleware(RecordMissingUrls::class);
            }
        }

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'sitemap-automation');

        $middleware = config('sitemap_automation.middleware', ['web']);

        Route::middleware($middleware)->group(function () {
            $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        });
    }
}
