<?php

namespace Znck\Trust\Traits;

use Znck\Trust\Contracts\Permission as PermissionInterface;

/**
 * Class RoleHasRelations.
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|PermissionInterface[] permissions
 * @property-read \Illuminate\Database\Eloquent\Collection users
 *
 * @method \Illuminate\Database\Eloquent\Relations\BelongsToMany belongsToMany(string $related)
 */
trait Role
{
    /**
     * Role belongs to many permissions.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function permissions()
    {
        return $this
            ->belongsToMany(config('znck.trust.models.permission'))
            ->withTimestamps();
    }

    /**
     * Role belongs to many users.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this
            ->belongsToMany(config('znck.trust.models.user', config('auth.providers.users.model')))
            ->withTimestamps();
    }

    /**
     * Attach permission to a role.
     *
     * @param string|int|PermissionInterface $permission
     *
     * @return bool
     */
    public function attachPermission($permission)
    {
        if (!$this->permissions()->get()->contains($permission)) {
            $this->permissions()->attach($permission);
            unset($this->relations['permissions']);

            return true;
        }

        return false;
    }

    /**
     * Detach permission from a role.
     *
     * @param int|string|PermissionInterface $permission
     *
     * @return int
     */
    public function detachPermission($permission)
    {
        $return = $this->permissions()->detach($permission);
        unset($this->relations['permissions']);

        return $return;
    }

    /**
     * Detach all permissions.
     *
     * @return int
     */
    public function detachAllPermissions()
    {
        $return = $this->permissions()->detach();
        unset($this->relations['permissions']);

        return $return;
    }
}
