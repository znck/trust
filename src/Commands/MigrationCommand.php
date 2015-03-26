<?php
/**
 * This file belongs to Trust.
 *
 * Author: Rahul Kadyan, <hi@znck.me>
 */

namespace Znck\Trust\Commands;


use Illuminate\Console\Command;

class MigrationCommand extends Command
{
    /**
     * The console command name.
     *
     * @type string
     */
    protected $name = 'trust:migration';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a migration for user roles and permissions.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        $this->line('');
        $this->info("Tables: roles, user_role, permissions, permission_role");
        $message = "An migration that creates 'roles, 'user_role', 'permissions', 'permission_role'" .
            " tables. The migration file will be created in the database/migrations directory";
        $this->comment($message);
        $this->line('');
        if ($this->confirm("Proceed with the migration creation? [Yes|no]")) {
            $this->line('');
            $this->info("Creating migration...");
            if ($this->create()) {
                $this->info("Migration successfully created!");
            } else {
                $this->error(
                    "Coudn't create migration.\n Check the write permissions" .
                    " within the database/migrations directory."
                );
            }
            $this->line('');
        }
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [];
    }

    private function create()
    {
        $src = __DIR__ . '/../migrations/create.php.stub';
        $databasePath = $this->laravel->basePath() . "/database/migrations/";
        if ($this->laravel->bound('path.database')) {
            $databasePath = $this->laravel->make('path.database') . '/migrations/';
        }
        // build a filename for the migration
        $migration_file = $databasePath . date('Y_m_d_His') . "_create_roles_permissions_tables.php";
        // Check that the source file exists
        if (!file_exists($src)) {
            return false;
        }
        // Check that the destination does NOT exist, before copy our file in place
        if (!file_exists($migration_file)) {
            if (copy($src, $migration_file)) {
                return true;
            }
        }

        return false;
    }
}