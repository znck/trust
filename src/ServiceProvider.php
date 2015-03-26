<?php
/**
 * This file belongs to Trust.
 *
 * Author: Rahul Kadyan, <hi@znck.me>
 */

namespace Znck\Trust;


use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Znck\Trust\Commands\MigrationCommand;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {
        $this->setupConfig();
    }

    /**
     * Setup the config.
     *
     * @return void
     */
    protected function setupConfig()
    {
        $source = realpath(__DIR__ . '/config/trust.php');
        $this->publishes([$source => config_path('trust.php')]);
        $this->mergeConfigFrom($source, 'znck.trust');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $router = $this->app['router'];
        $router->middleware('role', 'Znck\Trust\Http\Middleware\NeedsRole');
        $router->middleware('permission', 'Znck\Trust\Http\Middleware\NeedsPermission');

        $this->app->singleton('command.trust.migrate', function () {
            return new MigrationCommand;
        });

        $this->commands('command.trust.migrate');
    }
}