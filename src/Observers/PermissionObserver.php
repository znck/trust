<?php namespace Znck\Trust\Observers;

use Illuminate\Database\Eloquent\Model;

class PermissionObserver
{
    public function updated(Model $permission) {
        trust()->clearPermissionCache($permission);
    }

    public function deleted(Model $permission) {
        trust()->clearPermissionCache($permission);
    }
}
