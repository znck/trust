<?php

namespace Znck\Trust\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Container\Container;
use Znck\Trust\Contracts\Permission;
use Znck\Trust\Contracts\Role;

class InstallRolesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trust:roles {--force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install roles.';

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
        // Install Permissions First.
        $this->call('trust:permissions');

        $roles = $this->app->make('trust.roles');

        $permissions = $this->getPermissions(['id', 'slug']);

        $create = $update = 0;

        collect($roles)->each(function ($attributes) use ($permissions, &$create, &$update) {
            $role = $this->findRole($attributes['slug']);

            if (!$role) {
                $role = $this->create((array) $attributes);

                ++$create;
            } elseif ($this->option('force')) {
                $role->update((array) $attributes);

                ++$update;
            } else {
                return;
            }

            $role->permissions()->sync(
                collect($attributes['permissions'] ?? [])->sort(function (string $a, string $b) {
                    if ($a[0] === $b[0]) {
                        return 0;
                    }

                    return $b[0] === '!' ? -1 : 1;
                })->reduce(function (Collection $ids, string $slug) use ($permissions) {
                    if (hash_equals('*', $slug)) {
                        return $permissions->pluck('id');
                    }

                    if (count($permissions) === count($ids)) {
                        return $ids;
                    }

                    if ($negate = ($slug[0] === '!')) {
                        $slug = substr($slug, 1);
                    }

                    $matched = $permissions->filter(function ($permission) use ($slug) {
                        return Str::is($slug, $permission->slug);
                    });

                    if ($negate === true) {
                        $matched = $matched->keyBy('slug');

                        return $ids->filter(function ($id) use ($matched) {
                            return !$matched->has($id->slug);
                        });
                    }

                    return $ids->merge($matched)->unique();
                }, collect())->pluck('id')
            );
        });

        $total = $create + $update;

        $this->line("Installed ${total} roles. <info>(${create} new roles, ${update} roles synced)</info>");
    }

    protected function getPermissions(array $columns)
    {
        return $this->app->make(Permission::class)->all($columns);
    }

    protected function findRole(string $slug)
    {
        return $this->app->make(Role::class)->whereSlug($slug)->first();
    }

    protected function create(array $attributes)
    {
        return $this->app->make(Role::class)->create($attributes);
    }
}
