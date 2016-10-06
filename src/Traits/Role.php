<?php namespace Znck\Trust\Traits;


/**
 * Class RoleHasRelations.
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\Znck\Trust\Contracts\Permission[] permissions
 * @property-read \Illuminate\Database\Eloquent\Collection users
 */
trait Role
{
    public static function bootRole()
    {
        self::created(
            function () {
                trust()->roles(true);
            }
        );

        self::updated(
            function () {
                trust()->roles(true);
            }
        );

        self::deleted(
            function () {
                trust()->roles(true);
            }
        );
    }

    /**
     * Role belongs to many permissions.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function permissions()
    {
        return $this->belongsToMany(config('trust.models.permission'))->withTimestamps();
    }

    /**
     * Role belongs to many users.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(config('trust.models.user') ?? config('auth.providers.users.model'))->withTimestamps();
    }
}
