<?php

namespace Znck\Trust\Traits;

use Illuminate\Support\Collection;
use Znck\Trust\Contracts\Permission as PermissionContract;
use Znck\Trust\Contracts\Role as RoleContract;
use Znck\Trust\Trust;

trait Permissible
{
    use HasPermission;

    protected function collectRoles($role)
    {
        if (is_string($role)) {
            $role = app(RoleContract::class)->whereSlug($role)->first();
        }

        if (is_array($role) or $role instanceof Collection) {
            $role = new Collection($role);

            if (is_string($role->first())) {
                return app(RoleContract::class)->whereIn('slug', $role->toArray())->get();
            } elseif ($role->first() instanceof RoleContract) {
                return $role;
            }
        } elseif ($role instanceof RoleContract) {
            return $role;
        }
    }

    protected function collectPermissions($permission)
    {
        if (is_string($permission)) {
            $permission = app(PermissionContract::class)->whereSlug($permission)->first();
        }

        if (is_array($permission) or $permission instanceof Collection) {
            $permission = new Collection($permission);

            if (is_string($permission->first())) {
                return app(PermissionContract::class)->whereIn('slug', $permission->toArray())->get();
            } elseif ($permission->first() instanceof PermissionContract) {
                return $permission;
            }
        } elseif ($permission instanceof PermissionContract) {
            return $permission;
        }
    }

    /**
     * @param string|RoleContract|array|Collection $role
     */
    public function assignRole($role)
    {
        if ($role = $this->collectRoles($role)) {
            $this->roles()->attach($role);

            cache()->forget(Trust::PERMISSION_KEY.':'.$this->getKey());
            cache()->forget(Trust::ROLE_KEY.':'.$this->getKey());
            $this->refreshPermissions();
        }
    }

    /**
     * @param string|RoleContract|array|Collection $role
     */
    public function revokeRole($role)
    {
        if ($role = $this->collectRoles($role)) {
            $this->roles()->detach($role);

            cache()->forget(Trust::PERMISSION_KEY.':'.$this->getKey());
            cache()->forget(Trust::ROLE_KEY.':'.$this->getKey());
            $this->refreshPermissions();
        }
    }

    /**
     * @param string|PermissionContract|array|Collection $permission
     */
    public function givePermission($permission)
    {
        if ($permission = $this->collectPermissions($permission)) {
            $this->permissions()->attach($permission);

            cache()->forget(Trust::PERMISSION_KEY.':'.$this->getKey());
            $this->refreshPermissions();
        }
    }

    /**
     * @param string|PermissionContract|array|Collection $permission
     */
    public function revokePermission($permission)
    {
        if ($permission = $this->collectPermissions($permission)) {
            $this->permissions()->detach($permission);

            cache()->forget(Trust::PERMISSION_KEY.':'.$this->getKey());
            $this->refreshPermissions();
        }
    }

    /**
     * User belongs to many roles.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(config('trust.models.role'))->withTimestamps();
    }

    /**
     * User have many explicit permissions.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function permissions()
    {
        return $this->belongsToMany(config('trust.models.permission'))->withTimestamps();
    }
}
