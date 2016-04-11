<?php namespace Znck\Trust\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use Znck\Trust\Contracts\Permission as PermissionInterface;
use Znck\Trust\Contracts\Role as RoleInterface;

/**
 * Class Permissible
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|Permission[] permissions
 * @property-read \Illuminate\Database\Eloquent\Collection|RoleInterface[] roles
 *
 * @method \Illuminate\Database\Eloquent\Relations\BelongsToMany belongsToMany(string $related)
 */
trait Permissible
{
    /**
     * @var \Illuminate\Database\Eloquent\Collection
     */
    private $cached_permissions;

    /**
     * User belongs to many roles.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this
            ->belongsToMany(config('znck.trust.models.role'))
            ->withTimestamps();
    }

    /**
     * User have many explicit permissions.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function userPermissions()
    {
        return $this
            ->belongsToMany(config('znck.trust.models.permission'))
            ->withTimestamps();
    }

    /**
     * Collect permission through roles.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function rolePermissions()
    {
        $permission = app(config('znck.trust.models.permission'));
        if (! $permission instanceof Model or ! $permission instanceof PermissionInterface) {
            throw new InvalidArgumentException('[trust.models.permission] should be instance of '.Model::class.' and it should implement '.PermissionInterface::class);
        }
        return $permission
            ->select(['permissions.*', 'permission_role.created_at as pivot_created_at', 'permission_role.updated_at as pivot_updated_at'])
            ->join('permission_role', 'permission_role.permission_id', '=', 'permissions.id')
            ->join('roles', 'roles.id', '=', 'permission_role.role_id')
            ->whereIn('roles.id', $this->roles->pluck('id'))
            ->orWhere('roles.level', '<', $this->level())
            ->groupBy(['permissions.id', 'pivot_created_at', 'pivot_updated_at']);
    }

    /**
     * List of all permissions.
     *
     * @return \Illuminate\Database\Eloquent\Collection|Permission[]
     */
    public function getPermissionsAttribute()
    {
        if (! $this->cached_permissions) {
            $this->cached_permissions = $this->rolePermissions()->get()->merge($this->userPermissions()->get());
        }
        return $this->cached_permissions;
    }

    /**
     * Get role level of a user.
     *
     * @return int
     */
    public function level()
    {
        if ($role = $this->roles->sortByDesc('level')->first()) {
            return $role->level;
        }
        return 0;
    }

    public function checkPermission($permissions)
    {
        $permissions = $this->parsePermissions($permissions);
        if (hash_equals('any', $permissions['query'])) {
            return $this->canAny($permissions['items']);
        }

        return $this->canAll($permissions['items']);
    }

    protected function canAny(array $permissions)
    {
        return ! $this->filterCollection($permissions, $this->permissions)->isEmpty();
    }

    protected function canAll(array $permissions)
    {
        return count($permissions) === $this->filterCollection($permissions, $this->permissions)->count();
    }

    /**
     * @param $permissions
     *
     * @return array
     */
    protected function parsePermissions($permissions)
    {
        if (is_string($permissions)) {
            return $this->parseRolePermissionQuery($permissions);
        }

        if (is_array($permissions)) {
            return [
                'items' => array_map(function ($val) {
                    if ($val instanceof PermissionInterface) {
                        return $val->slug;
                    }
                    if (is_string($val)) {
                        return $val;
                    }
                    throw new InvalidArgumentException('Permission should be string or instance of '.Permission::class);
                }, $permissions),
                'query' => 'all'
            ];
        }

        if ($permissions instanceof PermissionInterface) {
            return [
                'items' => [$permissions->slug],
                'query' => 'all'
            ];
        }

        if ($permissions instanceof Collection) {
            return [
                'items' => $permissions->pluck('slug')->toArray(),
                'query' => 'all'
            ];
        }

        throw new InvalidArgumentException('Invalid permission format.');
    }
    
    public function checkRole($roles)
    {
        $roles = $this->parseRoles($roles);
        if (hash_equals('any', $roles['query'])) {
            return $this->hasAny($roles['items']);
        }
        return $this->hasAll($roles['items']);
    }

    protected function hasAny(array $roles)
    {
        return ! $this->filterCollection($roles, $this->roles)->isEmpty();
    }

    protected function hasAll(array $roles)
    {
        return count($roles) === $this->filterCollection($roles, $this->roles)->count();
    }
    
    public function parseRoles($roles)
    {
        if (is_string($roles)) {
            return $this->parseRolePermissionQuery($roles);
        }


        if (is_array($roles)) {
            return [
                'items' => array_map(function ($val) {
                    if ($val instanceof RoleInterface) {
                        return $val->slug;
                    }
                    if (is_string($val)) {
                        return $val;
                    }
                    throw new InvalidArgumentException('Role should be string or instance of '.RoleInterface::class);
                }, $roles),
                'query' => 'all'
            ];
        }

        if ($roles instanceof RoleInterface) {
            return [
                'items' => [$roles->slug],
                'query' => 'all'
            ];
        }

        if ($roles instanceof Collection) {
            return [
                'items' => $roles->pluck('slug')->toArray(),
                'query' => 'all'
            ];
        }

        throw new InvalidArgumentException('Invalid permission format.');
    }

    protected function parseRolePermissionQuery(string $query)
    {
        if (str_contains($query, '|')) {
            return [
                'items' => explode('|', $query),
                'query' => 'any',
            ];
        }

        if (str_contains($query, ',')) {
            return [
                'items' => explode(',', $query),
                'query' => 'all'
            ];
        }

        return [
            'items' => [$query],
            'query' => 'all'
        ];
    }

    /**
     * @param array $choose
     * @param Collection $source
     *
     * @return Collection
     */
    protected function filterCollection(array $choose, Collection $source)
    {
        $choose = array_flip($choose);

        return $source->filter(
            function ($item) use ($choose) {
                return array_key_exists($item->slug, $choose);
            }
        );
    }

    /**
     * Assign role to user.
     *
     * @param string|int|RoleInterface $role
     *
     * @return bool
     */
    public function assignRole($role)
    {
        if (! $this->roles->contains($role)) {
            $this->roles()->attach($role);
            unset($this->relations['roles']);
            $this->cached_permissions = null;
            return true;
        }

        return false;
    }

    /**
     * Remove role from user.
     *
     * @param string|int|RoleInterface $role
     *
     * @return int
     */
    public function removeRole($role)
    {
        $return = $this->roles()->detach($role);
        $this->load('roles');
        $this->cached_permissions = null;
        return $return;
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
        if (! $this->permissions->contains($permission)) {
            $this->userPermissions()->attach($permission);
            $this->cached_permissions = null;
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
        $this->cached_permissions = null;
        return $this->userPermissions()->detach($permission);
    }

    /**
     * Detach all permissions.
     *
     * @return int
     */
    public function detachAllPermissions()
    {
        $this->cached_permissions = null;
        return $this->userPermissions()->detach();
    }
}