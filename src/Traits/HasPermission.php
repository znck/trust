<?php

namespace Znck\Trust\Traits;

use Illuminate\Support\Collection;
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
    private $cached_roles;

    private $cached_permissions;

    private $role_slugs;

    private $permission_slugs;

    public function refreshPermissions()
    {
        $this->cached_roles = $this->cached_permissions = $this->permission_slugs = null;
    }

    /**
     * @param RoleContract|string $role
     *
     * @return bool
     */
    public function assumesRoleOf($role)
    {
        if ($role instanceof RoleContract) {
            $role = $role->slug;
        } elseif (!is_string($role)) {
            return false;
        }

        if ($this->getRoleNames()->has($role)) {
            event(new RoleUsed($this, $role));

            return true;
        }

        return false;
    }

    /**
     * @param string|PermissionContract $permission
     *
     * @return bool
     */
    public function hasPermissionTo($permission)
    {
        if ($permission instanceof PermissionContract) {
            $permission = $permission->slug;
        } elseif (!is_string($permission)) {
            return false;
        }

        if ($this->getPermissionNames()->has($permission)) {
            event(new PermissionUsed($this, $permission));

            return true;
        }

        return false;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function getPermissionNames()
    {
        if (!is_null($this->permission_slugs)) {
            return $this->permission_slugs;
        }

        return $this->permission_slugs = cache()
            ->rememberForever(Trust::PERMISSION_KEY.':'.$this->getKey(), function () {
                return $this->getPermissions()->pluck('id', 'slug');
            });
    }

    public function getRoleNames()
    {
        if (!is_null($this->role_slugs)) {
            return $this->role_slugs;
        }

        return $this->role_slugs = cache()
            ->rememberForever(Trust::ROLE_KEY.':'.$this->getKey(), function () {
                return $this->getRoles()->pluck('id', 'slug');
            });
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection|\Znck\Trust\Contracts\Role[]
     */
    public function getPermissions()
    {
        if (!is_null($this->cached_permissions)) {
            return $this->cached_permissions;
        }

        $this->load('permissions');

        /** @var \Illuminate\Support\Collection $names */
        $names = $this->getRoles()->reduce(
            function (Collection $result, RoleContract $role) {
                /* @noinspection PhpUndefinedFieldInspection */
                return $result->merge($role->permissions);
            },
            $this->permissions ?? new Collection()
        )->pluck('slug', 'id');

        return $this->cached_permissions = trust()->permissions()->filter(
            function ($permission) use ($names) {
                return $names->has($permission->id);
            }
        );
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection|\Znck\Trust\Contracts\Role[]
     */
    public function getRoles()
    {
        if (!is_null($this->cached_roles)) {
            return $this->cached_roles;
        }

        $this->load('roles');

        /** @var \Illuminate\Support\Collection $names */
        /* @noinspection PhpUndefinedFieldInspection */
        $names = $this->roles->pluck('slug', 'id');

        return $this->cached_roles = trust()->roles()->filter(
            function ($role) use ($names) {
                return $names->has($role->id);
            }
        );
    }
}
