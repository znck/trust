<?php namespace Znck\Trust\Observers;

use Illuminate\Database\Eloquent\Model;

class UserObserver
{
    public function permissionsAdded(Model $user)
    {
        trust()->clearUserCache($user);
    }

    public function permissionsRemoved(Model $user)
    {
        trust()->clearUserCache($user);
    }

    public function rolesAdded(Model $user)
    {
        trust()->clearUserCache($user);
    }

    public function rolesRemoved(Model $user)
    {
        trust()->clearUserCache($user);
    }
}
