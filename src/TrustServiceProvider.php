<?php

namespace Znck\Trust;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Znck\Trust\Console\Commands\InstallPermissionsCommand;
use Znck\Trust\Console\Commands\InstallRolesCommand;
use Znck\Trust\Contracts\Permission;
use Znck\Trust\Contracts\Role;
use Illuminate\Filesystem\Filesystem;

use Znck\Trust\Services\PermissionFinder;

/**
 * @property \Illuminate\Foundation\Application $app
 */
class TrustServiceProvider extends BaseServiceProvider
{
    /**
     * List of trust models.
     *
     * @var array
     */
    protected $models = [];

    /**
     * List of available permissions.
     *
     * @var array
     */
    protected $permissions = [];

    /**
     * List of default roles.
     *
     * @var array
     */
    protected $roles = [];

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

    /**
     * Register migrations.
     *
     * @return void
     */
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
        $this->loadConfig();
        $this->bindInterfaces();
        $this->registerTrust();

        $this->registerPermissionFinder();
        $this->registerRoleFinder();

        $this->registerPermissionsCommand();
        $this->registerRolesCommand();
    }

    /**
     * Find roles.
     */
    public function registerRoleFinder()
    {
        $this->app->singleton('trust.roles', function () {
            $filename = base_path(config('trust.roles'));
            $filesystem = $this->getFilesystem();

            $roles = collect($this->roles);

            if ($filesystem->exists($filename)) {
                $roles = $roles->merge(collect($filesystem->getRequire($filename)));
            }

            return $roles->map(function ($role, $slug) {
                return $role + compact('slug');
            });
        });
    }

    /**
     * Find permissions.
     */
    public function registerPermissionFinder()
    {
        $this->app->singleton('trust.permissions', function () {
            $filename = base_path(config('trust.permissions'));
            $filesystem = $this->getFilesystem();

            $permissions = collect([]);

            if ($filesystem->exists($filename)) {
                $permissions = $permissions->merge(new PermissionFinder($filesystem->getRequire($filename)));
            }

            if (count($this->models)) {
                $permissions = collect($this->models)->reduce(function ($permissions, $class) {
                    list($class, $extras) = is_array($class) ? $class : [$class, []];

                    return $permissions->merge(new PermissionFinder($class, $extras));
                }, $permissions);
            }

            if (count($this->permissions)) {
                $permissions = $permissions->merge(new PermissionFinder($this->permissions));
            }

            return $permissions;
        });
    }

    /**
     * Laravel Local Filesystem
     *
     * @return \Illuminate\Filesystem\Filesystem
     */
    public function getFilesystem()
    {
        return $this->app->make(Filesystem::class);
    }
    /**
     * Register Trust instance.
     */
    public function registerTrust()
    {
        $this->app->singleton(Trust::class);
    }
    /**
     * Load configurations.
     */
    public function loadConfig()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/trust.php', 'trust');
    }
    /**
     * Bind Role & Permission interface to concrete types.
     */
    public function bindInterfaces()
    {
        $this->app->bind(Role::class, config('trust.models.role'));
        $this->app->bind(Permission::class, config('trust.models.permission'));
    }

    /**
     * Register Permissions Command.
     *
     * @return void
     */
    public function registerPermissionsCommand()
    {
        $this->app->singleton('command.trust.permissions', InstallPermissionsCommand::class);
        $this->commands('command.trust.permissions');
    }

    /**
     * Register Roles Command.
     *
     * @return void
     */
    public function registerRolesCommand()
    {
        $this->app->singleton('command.trust.roles', InstallRolesCommand::class);
        $this->commands('command.trust.roles');
    }

    public function provides()
    {
        return [
            Role::class,
            Permission::class,
            Trust::class,
            'command.trust.roles',
            'command.trust.permissions',
            'trust.roles',
            'trust.permissions',
        ];
    }
}
