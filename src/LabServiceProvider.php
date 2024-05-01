<?php

namespace Takielias\Lab;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Takielias\Lab\Commands\InstallLAB;

class LabServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot(): void
    {
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'lab');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'lab');
        // $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        // $this->loadRoutesFrom(__DIR__.'/routes.php');

        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }

        $this->loadViewsFrom(__DIR__ . '/../resources/views/alert', 'lab-alert');

        // Register a new custom directive called @alert
        Blade::directive('alert', function ($expression) {
            return "<?php echo view('lab-alert::alert')->render(); ?>";
        });

    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/lab.php', 'lab');

        // Register the service the package provides.
        $this->app->singleton('lab', function ($app) {
            return new Lab();
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['lab'];
    }

    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole(): void
    {
        // Publishing the configuration file.
        $this->publishes([
            __DIR__ . '/../config/lab.php' => config_path('lab.php'),
        ], 'lab.config');

        // Publishing the views.
        /*$this->publishes([
            __DIR__.'/../resources/views' => base_path('resources/views/vendor/lab'),
        ], 'lab.views');*/

        // Publishing assets.
        /*$this->publishes([
            __DIR__.'/../resources/assets' => public_path('vendor/lab'),
        ], 'lab.assets');*/

        // Publishing the translation files.
        /*$this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/lab'),
        ], 'lab.lang');*/

        // Registering package commands.
        $this->commands([
            InstallLAB::class
        ]);
    }
}
