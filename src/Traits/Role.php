<?php namespace Znck\Trust\Traits;

use Illuminate\Support\Collection;
use Znck\Trust\Jobs\EvictCachedRolePermissions;
use Znck\Trust\Observers\RoleObserver;
use Znck\Trust\Contracts\Permission as PermissionContract;

trait Role
{
    /**
     * Add a observer.
     */
    public static function bootRole() {
        self::observe(RoleObserver::class);
    }

    /**
     * Add permissions from role.
     *
     * @param  int|string|Permission|Collection $permissions List of permissions
     * @return $this
     */
    public function addPermission($permissions) {
        $ids = $this->getPermissionIds($permissions);
        $this->permissions()->attach($ids);
        $this->fireModelEvent('permissionsAdded');

        return $this;
    }

    /**
     * Remove permissions from role.
     *
     * @param  int|string|Permission|Collection $permissions List of permissions
     * @return $this
     */
    public function removePermission($permissions) {
        $ids = $this->getPermissionIds($permissions)
        $this->permissions()->detach($ids);
        $this->fireModelEvent('permissionsRemoved');

        return $this;
    }

    /**
     * Fetch permission ids from given permissions.
     *
     * @param  int|string|Permission|Collection $permissions List of permissions
     * @return array List of model keys
     */
    private function getPermissionIds($permissions): array {
        if ($permissions instanceof Model) {
            $permissions = $permissions->getKey();
        }

        if ($permissions instanceof Collection) {
            $model = app(PermissionContract::class);

            $permissions = $permissions->pluck($model->getKeyName())->toArray();
        }

        // TODO: Add support for UUID keys.

        if (is_string(array_first((array) $permissions))) {
            $model = app(PermissionContract::class);

            $permissions = $model->whereIn('slug', (array) $permissions)->get()->pluck($model->getKeyName())->toArray();
        }

        return (array) $permissions;
    }

    /**
     * Add observable events.
     *
     * @return array
     */
    public function getObservableEvents() {
        return array_merge(
            parent::getObservableEvents(),
            ['permissionAdded', 'permissionRemoved']
        );
    }

    /**
     * Role belongs to many permissions.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function permissions() {
        return $this->belongsToMany(
            config('trust.models.permission')
        )->withTimestamps();
    }

    /**
     * Role belongs to many users.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users() {
        return $this->belongsToMany(
            config('trust.models.user') ?? config('auth.providers.users.model')
        )->withTimestamps();
    }
}
