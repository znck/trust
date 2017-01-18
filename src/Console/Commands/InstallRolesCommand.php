<?php

namespace Znck\Trust\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Znck\Trust\Contracts\Role;
use Znck\Trust\Contracts\Permission;

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
     * @var \Illuminate\Filesystem\Filesystem
     */
    private $file;

    public function __construct(Filesystem $file)
    {
        parent::__construct();
        $this->file = $file;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $roles = $this->file->getRequire(base_path(config('trust.roles')));

        $this->call('trust:permissions');
        $all = $this->getPermissions(['id', 'slug']);
        $create = 0;
        $update = 0;
        foreach ($roles as $slug => $attributes) {
            $role = $this->findRole($slug);
            if ($role) {
                if ($this->option('force')) {
                    ++$update;
                    $role->update($attributes + compact('slug'));
                }
            } else {
                ++$create;
                $role = $this->create($attributes + compact('slug'));
            }

            $permissions = array_reduce(
                Arr::get($attributes, 'permissions', []),
                function (Collection $result, string $name) use ($all) {
                    if (hash_equals('*', $name)) {
                        return $all->pluck('id');
                    }

                    if ($all->count() === $result->count()) {
                        return $result;
                    }

                    $filtered = $all->filter(
                        function (Permission $permission) use ($name) {
                            return Str::is($name, $permission->slug);
                        }
                    )->pluck('id');

                    return $result->merge($filtered);
                },
                new Collection()
            );

            $role->permissions()->sync($permissions->toArray());
        }
        $total = $create + $update;
        $this->line("Installed ${total} roles. <info>(${create} new roles, ${update} roles synced)</info>");
    }

    protected function getPermissions()
    {
        return app(Permission::class)->all();
    }

    protected function findRole(string $slug)
    {
        return app(Role::class)->whereSlug($slug)->first();
    }

    protected function create(array $attributes)
    {
        return app(Role::class)->create($attributes);
    }
}
