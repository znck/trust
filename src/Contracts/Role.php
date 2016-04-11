<?php

namespace Znck\Trust\Contracts;

/**
 * Interface Role.
 *
 * @property string name
 * @property string slug
 * @property string description
 */
interface Role
{
    /**
     * Role belongs to many permissions.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function permissions();

    /**
     * Role belongs to many users.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users();

    /**
     * Attach permission to a role.
     *
     * @param string|int|Permission $permission
     *
     * @return self
     */
    public function attachPermission($permission);

    /**
     * Detach permission from a role.
     *
     * @param int|string|Permission $permission
     *
     * @return int
     */
    public function detachPermission($permission);

    /**
     * Detach all permissions.
     *
     * @return int
     */
    public function detachAllPermissions();
}
