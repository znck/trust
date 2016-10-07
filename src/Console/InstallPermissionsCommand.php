<?php namespace Zero\Console\Commands\Installation;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Znck\Trust\Models\Permission;

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
        $permissions = $this->file->getRequire(app_path(config('trust.permissions')));

        $create = 0;
        $update = 0;
        foreach ($permissions as $slug => $attributes) {
            $permission = $this->findPermission($slug);
            if ($permission) {
                if ($this->option('force')) {
                    ++$update;
                    $permission->update($attributes + compact('slug'));
                }
            } else {
                ++$create;
                Permission::create($attributes + compact('slug'));
            }
        }
        $total = $create + $update;
        $this->line("Installed ${total} permissions. <info>(${create} new permissions)</info>");
    }

    /**
     * @param string $slug
     *
     * @return Permission
     */
    protected function findPermission(string $slug) {
        return Permission::where('slug', $slug)->first();
    }
}
