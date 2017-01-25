<?php

namespace Znck\Trust\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Container\Container;
use Znck\Trust\Contracts\Permission;

class InstallPermissionsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trust:permissions {--force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install permissions.';

    /**
     * Laravel Container (IoC Binder)
     *
     * @var \Illuminate\Container\Container
     */
    protected $app;

    public function __construct(Container $app)
    {
        parent::__construct();

        $this->app = $app;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $permissions = $this->app->make('trust.permissions');

        $create = $update = 0;

        collect($permissions)->each(function ($attributes) use (&$create, &$update) {
            $permission = $this->findPermission($attributes['slug']);

            if (! $permission) {
                $this->create((array) $attributes);

                ++$create;
            } elseif ($this->option('force')) {
                $permission->update((array) $attributes);

                ++$update;
            }
        });

        $total = $create + $update;
        $this->line("Installed ${total} permissions. <info>(${create} new permissions)</info>");
    }

    /**
     * @param string $slug
     *
     * @return Permission
     */
    protected function findPermission(string $slug)
    {
        return $this->app->make(Permission::class)->whereSlug($slug)->first();
    }

    protected function create(array $attributes)
    {
        return $this->app->make(Permission::class)->create($attributes);
    }
}
