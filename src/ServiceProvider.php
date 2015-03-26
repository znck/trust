<?php
/**
 * This file belongs to Trust.
 *
 * Author: Rahul Kadyan, <hi@znck.me>
 */

namespace Znck\Trust;


use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Znck\Commands\MigrationCommand;

class ServiceProvider extends BaseServiceProvider
{
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
    }
}