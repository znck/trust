<?php namespace Znck\Trust\Traits;

use Illuminate\Support\Collection;
use Znck\Trust\Jobs\EvictCachedRolePermissions;
use Znck\Trust\Observers\RoleObserver;
use Znck\Trust\Contracts\Permission as PermissionContract;

/**
 * Class RoleHasRelations.
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\Znck\Trust\Contracts\Permission[] permissions
 * @property-read \Illuminate\Database\Eloquent\Collection users
 */
trait Role
{
    public static function bootRole() {
        self::observe(RoleObserver::class);
    }

    protected function collectPermissions($permission) {
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

        return null;
    }

    /**
     * @param string|PermissionContract|Collection|array $permission
     */
    public function addPermission($permission) {
        if ($permission = $this->collectPermissions($permission)) {
            $this->permissions()->attach($permission);
            dispatch(new EvictCachedRolePermissions($this));
        }

    }

    public function removePermission($permission) {
        if ($permission = $this->collectPermissions($permission)) {
            $this->permissions()->detach($permission);
            dispatch(new EvictCachedRolePermissions($this));
        }
    }

    /**
     * Role belongs to many permissions.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function permissions() {
        return $this->belongsToMany(config('trust.models.permission'))->withTimestamps();
    }

    /**
     * Role belongs to many users.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users() {
        return $this->belongsToMany(config('trust.models.user') ?? config('auth.providers.users.model'))->withTimestamps();
    }
}
