<?php namespace Znck\Trust\Traits;

use Illuminate\Database\Eloquent\Collection;
use \Znck\Trust\Contracts\Role as RoleContract;
use \Znck\Trust\Contracts\Permission as PermissionContract;
use Znck\Trust\Trust;
use Znck\Trust\Observers\UserObserver;

trait Permissible
{
    use HasPermission, PermissionsHelper;

    /**
     * Add event observer.
     *
     * @return void
     */
    public static function bootPermissible()
    {
        self::observe(UserObserver::class);
    }

    /**
     * Assign role to the user.
     *
     * @param  int|string|RoleContract|Collection $roles List of roles.
     *
     * @return $this
     */
    public function assignRole($roles)
    {
        $ids = $this->getRoleIds($roles);
        $this->roles()->attach($ids);
        $this->fireModelEvent('rolesAdded');

        return $this;
    }

    /**
     * Revoke role from the user.
     *
     * @param  int|string|RoleContract|Collection $roles List of roles.
     *
     * @return $this
     */
    public function revokeRole($roles)
    {
        $ids = $this->getRoleIds($roles);
        $this->roles()->detach($ids);
        $this->fireModelEvent('rolesRemoved');

        return $this;
    }

    /**
     * Grant explicit permission to the user.
     *
     * @param  int|string|PermissionContract|Collection $permissions List of permissions.
     *
     * @return $this
     */
    public function grantPermission($permissions)
    {
        $ids = $this->getPermissionIds($permissions);
        $this->permissions()->attach($ids);
        $this->fireModelEvent('permissionsAdded');

        return $this;
    }

    /**
     * Revoke explicit permission from the user.
     *
     * @param  int|string|PermissionContract|Collection $permissions List of permissions.
     *
     * @return $this
     */
    public function revokePermission($permissions)
    {
        // TODO: Add support to revoke permissions from roles.

        $ids = $this->getPermissionIds($permissions);
        $this->permissions()->detach($ids);
        $this->fireModelEvent('permissionsRemoved');

        return $this;
    }

    /**
     * Fetch role ids from given roles.
     *
     * @param  int|string|Role|Collection $roles List of roles
     *
     * @return array List of model keys
     */
    protected function getRoleIds($roles): array
    {
        if ($roles instanceof RoleContract) {
            $roles = $roles->getKey();
        }

        if ($roles instanceof Collection) {
            $model = app(RoleContract::class);

            $roles = $roles->pluck($model->getKeyName())->toArray();
        }

        // TODO: Add support for UUID keys.

        if (is_string(array_first((array) $roles))) {
            $model = app(RoleContract::class);

            $roles = $model->whereIn('slug', (array) $roles)->get()->pluck($model->getKeyName())->toArray();
        }

        return (array) $roles;
    }

    /**
     * Clear cached permissions.
     *
     * @return void
     */
    public function refreshPermissions()
    {
        trust()->clearUserCache($this);

        $this->setRelations([]);
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
            ['permissionAdded', 'permissionRemoved', 'rolesAdded', 'rolesRemoved']
        );
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
