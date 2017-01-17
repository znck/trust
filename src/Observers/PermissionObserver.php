<?php namespace Znck\Trust\Observers;

use Illuminate\Database\Eloquent\Model;

class PermissionObserver
{
    public function updated(Model $permission) {
        trust()->cacheRelated($permission);
    }

    public function deleted(Model $permission) {
        trust()->removeRelated($permission);
    }
}
