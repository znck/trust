<?php namespace Znck\Trust\Traits;

use Znck\Trust\Observers\PermissionObserver;

trait Permission
{
    /**
     * Add Permission Observers
     *
     * @return void
     */
    public static function bootPermission() {
        self::observe(PermissionObserver::class);
    }

    /**
     * Permission belongs to many roles.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles() {
        return $this->belongsToMany(
            config('trust.models.role')
        )->withTimestamps();
    }

    /**
     * Permission belongs to many users.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users() {
        return $this->belongsToMany(
            config('trust.models.user') ?? config('auth.providers.users.model')
        )->withTimestamps();
    }

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName() {
        return 'slug';
    }
}
