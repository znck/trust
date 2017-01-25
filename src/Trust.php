<?php

namespace Znck\Trust;

use Illuminate\Auth\AuthManager;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Contracts\Cache\Repository;
use Znck\Trust\Contracts\Permissible;
use Znck\Trust\Contracts\Permission;
use Znck\Trust\Contracts\Role;
use Znck\Trust\Jobs\EvictCachedRolePermissions;

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
     * @var bool
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
     * Laravel bus dispatcher.
     *
     * @var \Illuminate\Contracts\Bus\Dispatcher
     */
    protected $dispatcher;

    /**
     * Laravel auth guard.
     *
     * @var \Illuminate\Contracts\Auth\Guard
     */
    protected $guard;

    /**
     * Determines whether to use cache or not.
     *
     * @var bool
     */
    protected $caching = true;

    /**
     * Storage of in-memory caching of roles and permissions.
     *
     * @var array
     */
    protected $inMemoryCache = [];

    public function __construct(Repository $cache, Dispatcher $dispatcher, AuthManager $guard)
    {
        $this->cache = $cache;
        $this->dispatcher = $dispatcher;
        $this->guard = $guard;
    }

    /**
     * Enable/Disable caching.
     *
     * @param bool $state
     *
     * @return void
     */
    public function useCache(bool $state = null)
    {
        $this->caching = is_null($state) ? true : $state;
    }

    /**
     * Check user has permission.
     *
     * @param string $permission Permission name (slug)
     *
     * @return bool
     */
    public function to($permission)
    {
        return $this->getUser()->hasPermissionTo($permission);
    }

    public function can($permission)
    {
        return $this->getUser()->hasPermissionTo($permission);
    }

    public function hasPermission($permission)
    {
        return $this->getUser()->hasPermissionTo($permission);
    }

    /**
     * Check user has role.
     *
     * @param string $role Role name (slug)
     *
     * @return bool
     */
    public function is($role)
    {
        return $this->getUser()->canAssumeRole($role);
    }

    public function hasRole($role)
    {
        return $this->getUser()->canAssumeRole($role);
    }

    /**
     * Get current user or fetch from guard.
     *
     * @return Permissible
     */
    public function getUser(): Permissible
    {
        return $this->user ?? $this->guard->user();
    }

    /**
     * Set current user.
     *
     * @param Permissible $user
     */
    public function setUser(Permissible $user)
    {
        $this->user = $user;
    }

    /**
     * Get/cache roles for current user.
     *
     * @param callable $callback
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getRoles($callback)
    {
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

    /**
     * Get/cache permissions for current user.
     * *.
     *
     * @param callable $callback
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPermissions($callback)
    {
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

    /**
     * Delete cached permission.
     *
     * @param Permission $permission
     *
     * @return void
     */
    public function clearPermissionCache(Permission $permission)
    {
        $this->dispatcher->dispatch(new EvictCachedRolePermissions($permission));
    }

    /**
     * Delete cached role.
     *
     * @param Role $role
     *
     * @return void
     */
    public function clearRoleCache(Role $role)
    {
        $this->dispatcher->dispatch(new EvictCachedRolePermissions($role));
    }

    /**
     * Delete cached roles and permmissions for the user.
     *
     * @param Permissible $user
     *
     * @return void
     */
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
