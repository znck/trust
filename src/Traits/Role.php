<?php

namespace Znck\Trust\Traits;

use Znck\Trust\Observers\RoleObserver;

trait Role
{
    use PermissionsHelper;

    /**
     * Add a observer.
     */
    public static function bootRole()
    {
        self::observe(RoleObserver::class);
    }

    /**
     * Add permissions from role.
     *
     * @param  int|string|Permission|Collection $permissions List of permissions
     *
     * @return $this
     */
    public function addPermission($permissions)
    {
        $ids = $this->getPermissionIds($permissions);
        $this->permissions()->attach($ids);
        $this->fireModelEvent('permissionsAdded');

        return $this;
    }

    /**
     * Remove permissions from role.
     *
     * @param  int|string|Permission|Collection $permissions List of permissions
     *
     * @return $this
     */
    public function removePermission($permissions)
    {
        $ids = $this->getPermissionIds($permissions);
        $this->permissions()->detach($ids);
        $this->fireModelEvent('permissionsRemoved');

        return $this;
    }

    /**
     * Add observable events.
     *
     * @return array
     */
    public function getObservableEvents()
    {
        return array_merge(
            parent::getObservableEvents(),
            ['permissionsAdded', 'permissionsRemoved']
        );
    }

    /**
     * Role belongs to many permissions.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function permissions()
    {
        return $this->belongsToMany(
            config('trust.models.permission')
        )->withTimestamps();
    }

    /**
     * Role belongs to many users.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(
            config('trust.models.user') ?? config('auth.providers.users.model')
        )->withTimestamps();
    }
}
