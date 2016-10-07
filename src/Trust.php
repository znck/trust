<?php namespace Znck\Trust;

use Illuminate\Contracts\Cache\Repository;
use Znck\Trust\Contracts\Permission;
use Znck\Trust\Contracts\Role;

class Trust
{
    const PERMISSION_KEY = 'znck.trust.permissions';

    const ROLE_KEY = 'znck.trust.roles';

    public static $runMigrations = true;

    /**
     * @var Contracts\Permissible
     */
    public $user;

    /**
     * @var \Illuminate\Contracts\Cache\Repository
     */
    protected $cache;


    public function __construct(Repository $cache)
    {
        $this->cache = $cache;
    }


    /**
     * @param bool $forget
     *
     * @return \Illuminate\Database\Eloquent\Collection|Permission[]|null
     */
    public function permissions(bool $forget = false)
    {
        if ($forget === true) {
            return $this->cache->forget(self::PERMISSION_KEY);
        }

        return $this->cache->rememberForever(
            self::PERMISSION_KEY,
            function () {
                return app(Permission::class)->with('roles')->get();
            }
        );
    }


    /**
     * @param bool $forget
     *
     * @return \Illuminate\Database\Eloquent\Collection|Role[]|null
     */
    public function roles(bool $forget = false)
    {
        if ($forget === true) {
            return $this->cache->forget(self::ROLE_KEY);
        }

        return $this->cache->rememberForever(
            self::ROLE_KEY,
            function () {
                return app(Role::class)->with('permissions')->get();
            }
        );
    }

    public function to($permission)
    {
        return $this->getUser()->hasPermissionTo($permission);
    }

    /**
     * @return Contracts\Permissible
     */
    public function getUser(): Contracts\Permissible
    {
        return $this->user ?? auth()->user();
    }

    /**
     * @param Contracts\Permissible $user
     */
    public function setUser(Contracts\Permissible $user)
    {
        $this->user = $user;
    }
}
