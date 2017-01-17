<?php

namespace Znck\Trust\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Znck\Trust\Trust;

class EvictCachedRolePermissions implements ShouldQueue
{
    use Queueable, SerializesModels;
    /**
     * @var \Znck\Trust\Contracts\Role
     */
    protected $role;

    public function __construct($role)
    {
        $this->role = $role;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->role->users()->chunk(50, function (Collection $users) {
            $users->each(function (Model $user) {
                cache()->forget(Trust::PERMISSION_KEY.':'.$user->getKey());
            });
        });
    }
}
