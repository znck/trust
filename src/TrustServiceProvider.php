<?php

namespace Znck\Trust;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Znck\Trust\Console\Commands\InstallPermissionsCommand;
use Znck\Trust\Console\Commands\InstallRolesCommand;
use Znck\Trust\Contracts\Permission;
use Znck\Trust\Contracts\Role;

/**
 * @property \Illuminate\Foundation\Application $app
 */
class TrustServiceProvider extends BaseServiceProvider
{
    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([__DIR__.'/../config/trust.php' => config_path('trust.php')], 'trust-config');
        $this->publishes([__DIR__.'/../trust' => base_path('trust')], 'trust');

        if ($this->app->runningInConsole()) {
            $this->registerMigrations();
        }
    }

    protected function registerMigrations()
    {
        if (Trust::$runMigrations) {
            $this->loadMigrationsFrom(__DIR__.'/../migrations');

            return;
        }

        $this->publishes(
            [
                __DIR__.'/../migrations' => database_path('migrations'),
            ],
            'trust-migrations'
        );
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/trust.php', 'trust');
        $this->app->bind(Role::class, config('trust.models.role'));
        $this->app->bind(Permission::class, config('trust.models.permission'));
        $this->app->singleton(Trust::class);

        $this->app->singleton('command.trust.permissions', InstallPermissionsCommand::class);
        $this->commands('command.trust.permissions');
        $this->app->singleton('command.trust.roles', InstallRolesCommand::class);
        $this->commands('command.trust.roles');
    }

    public function provides()
    {
        return [Role::class, Permission::class, Trust::class, 'command.trust.roles', 'command.trust.permissions'];
    }
}
