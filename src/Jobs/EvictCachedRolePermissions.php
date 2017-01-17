<?php namespace Znck\Trust\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use Znck\Trust\Contracts\Role;
use Znck\Trust\Contracts\Permission;

class EvictCachedRolePermissions implements ShouldQueue
{
    use Queueable, SerializesModels;
    /**
     * @var \Znck\Trust\Contracts\Role
     */
    protected $model;

    public function __construct($model)
    {
        $this->model = $model;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->model instanceof Role) {
            $this->handleRole($this->model);
        } elseif ($this->model instanceof Permission) {
            $this->model->roles()->chunk(100, function ($roles) {
                $roles->each([$this, 'handleRole']);
            });
        }
    }

    /**
     * Clear cache for role.
     *
     * @param  Role $role
     *
     * @return void
     */
    public function handleRole(Role $role)
    {
        $role->users()->chunk(100, function ($users) {
            $users->each(function ($user) {
                trust()->clearUserCache($user);
            });
        });
    }
}
