<?php namespace Zero\Console\Commands\Installation;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Znck\Trust\Models\Permission;
use Znck\Trust\Models\Role;

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

    public function __construct(Filesystem $file) {
        parent::__construct();
        $this->file = $file;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle() {
        $roles = $this->file->getRequire(app_path(config('trust.permissions')));;

        $this->call('trust:permissions');
        $all = Permission::all(['id', 'slug']);
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
                $role = Role::create($attributes + compact('slug'));
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

    /**
     * @param string $slug
     *
     * @return Role
     */
    protected function findRole(string $slug) {
        return Role::where('slug', $slug)->first();
    }
}
