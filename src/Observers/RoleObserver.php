<?php

namespace Znck\Trust\Observers;

use Illuminate\Database\Eloquent\Model;

class RoleObserver
{
    public function updated(Model $role)
    {
        trust()->clearRoleCache($role);
    }

    public function deleted(Model $role)
    {
        trust()->clearRoleCache($role);
    }

    public function permissionsAdded(Model $role)
    {
        trust()->clearRoleCache($role);
    }

    public function permissionsRemoved(Model $role)
    {
        trust()->clearRoleCache($role);
    }
}
