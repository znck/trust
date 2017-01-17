<?php namespace Znck\Trust\Observers;

class RoleObserver
{
    public function created(Model $role) {
        trust()->cache($role);
    }

    public function updated(Model $role) {
        trust()->cache($role);
    }

    public function deleted(Model $role) {
        trust()->deleteCache($role);
    }

    public function permissionsAdded(Model $role) {
        trust()->cache($role);
    }

    public function permissionsRemoved(Model $role) {
        trust()->cache($role);
    }
}
