<?php namespace Znck\Trust\Traits;

/**
 * Class PermissionHasRelations.
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\Znck\Trust\Contracts\Role[] roles
 * @property-read \Illuminate\Database\Eloquent\Collection users
 */
trait Permission
{
    public static function bootPermission()
    {
        self::created(function () {
            trust()->permissions(true);
        });

        self::updated(function () {
            trust()->permissions(true);
        });

        self::deleted(function () {
            trust()->permissions(true);
        });
    }
    /**
     * Permission belongs to many roles.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(config('trust.models.role'))->withTimestamps();
    }

    /**
     * Permission belongs to many users.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(config('trust.models.user') ?? config('auth.providers.users.model'))->withTimestamps();
    }
}
