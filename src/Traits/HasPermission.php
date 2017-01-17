<?php namespace Znck\Trust\Traits;

use Znck\Trust\Contracts\Permission as PermissionContract;
use Znck\Trust\Contracts\Role as RoleContract;
use Znck\Trust\Events\PermissionUsed;
use Znck\Trust\Events\RoleUsed;
use Znck\Trust\Trust;

/**
 * @internal Znck\Trust
 */
trait HasPermission
{
    /**
     * Check if the user has Role.
     *
     * @param  string $role
     * @return bool
     */
    public function canAssumeRole($role) {
        if ($role instanceof RoleContract) {
            $role = $role->slug;
        } elseif (!is_string($role)) {
            return false;
        }

        if ($this->getRoleNames()->contains($role)) {
            event(new RoleUsed($this, $role));

            return true;
        }

        return false;
    }

    /**
     * Checks if the user has Permission.
     *
     * @param  string $permission
     * @return bool
     */
    public function hasPermissionTo($permission) {
        if ($permission instanceof PermissionContract) {
            $permission = $permission->slug;
        } elseif (!is_string($permission)) {
            return false;
        }

        if ($this->getPermissionNames()->contains($permission)) {
            event(new PermissionUsed($this, $permission));

            return true;
        }

        return false;
    }

    /**
     * A collection of permission names (slugs)
     *
     * @return Illuminate\Support\Collection
     */
    public function getPermissionNames() {
        return $this->getPermissions()->keys();
    }

    /**
     * A collection of role names (slugs)
     *
     * @return Illuminate\Support\Collection
     */
    public function getRoleNames() {
        return $this->getRoles()->keys();
    }

    /**
     * A collection of permissions.
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getPermissions() {
        return trust($this)->getPermissions(function () {
            // TODO: Add support for revoking specific permissions.

            return $this->roles->reduce(function ($result, $role) {
                return $result->merge($role->permissions->keyBy('slug'));
            }, $this->permissions->keyBy('slug');
        });
    }

    /**
     * A collection of roles.
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getRoles() {
        return trust($this)->getRoles(function () {
            return $this->roles->keyBy('slug');
        });
    }
}
