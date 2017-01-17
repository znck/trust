<?php namespace Znck\Trust;

use Illuminate\Contracts\Cache\Repository;
use Znck\Trust\Contracts\Permission;
use Znck\Trust\Contracts\Permissible;
use Znck\Trust\Contracts\Role;

class Trust
{
    /**
     * A cache key prefix to prevent collision.
     *
     * @var string
     */
    const CACHE_KEY = 'znck.trust.cache.';

    /**
     * Determines whether to run migrations or publish them.
     *
     * @var boolean
     */
    public static $runMigrations = true;

    /**
     * Active user.
     *
     * @var Permissible
     */
    public $user;

    /**
     * Instance of Laravel Cache.
     *
     * @var \Illuminate\Contracts\Cache\Repository
     */
    protected $cache;

    /**
     * Determines whether to use cache or not.
     *
     * @var boolean
     */
    protected $caching;

    /**
     * Storage of in-memory caching of roles and permissions.
     *
     * @var array
     */
    protected $inMemoryCache = [];


    public function __construct(Repository $cache)
    {
        $this->cache = $cache;
        $this->caching = app()->environment() === 'production';
    }

    public function useCache(bool $state = true) {
        $this->caching = $state;
    }

    public function to($permission)
    {
        return $this->getUser()->hasPermissionTo($permission);
    }

    public function is($role)
    {
        return $this->getUser()->canAssumeRole($role);
    }

    public function getUser(): Permissible
    {
        return $this->user ?? $this->guard->user();
    }

    public function setUser(Permissible $user)
    {
        $this->user = $user;
    }

    public function getRoles($callback) {
        $key = $this->getUser()->getKey();

        if (isset($this->inMemoryCache[$key])) {
            if (isset($this->inMemoryCache[$key]['roles'])) {
                return $this->inMemoryCache[$key]['roles'];
            }
        } else {
            $this->inMemoryCache[$key] = [];
        }

        if ($this->caching !== true) {
            $this->inMemoryCache[$key]['roles'] = call_user_func($callback);
        }

        return $this->inMemoryCache[$key]['roles'] = $this->cache->rememberForever(static::CACHE_KEY.'roles.'.$key, $callback);
    }

    public function getPermissions($callback) {
        $key = $this->getUser()->getKey();

        if (isset($this->inMemoryCache[$key])) {
            if (isset($this->inMemoryCache[$key]['permissions'])) {
                return $this->inMemoryCache[$key]['permissions'];
            }
        } else {
            $this->inMemoryCache[$key] = [];
        }

        if ($this->caching !== true) {
            $this->inMemoryCache[$key]['permissions'] = call_user_func($callback);
        }

        return $this->inMemoryCache[$key]['permissions'] = $this->cache->rememberForever(static::CACHE_KEY.'permissions.'.$key, $callback);
    }

    public function clearPermissionCache(Permission $permission)
    {
        $permission->roles->each([$this, 'clearRoleCache']);
    }

    public function clearRoleCache(Role $role)
    {
        $role->user->each([$this, 'clearUserCache']);
    }

    public function clearUserCache(Permissible $user)
    {
        $key = $user->getKey();

        if (isset($this->inMemoryCache[$key])) {
            unset($this->inMemoryCache[$key]);
        }

        $this->cache->forget(static::CACHE_KEY.'roles.'.$key);
        $this->cache->forget(static::CACHE_KEY.'permissions.'.$key);
    }
}
