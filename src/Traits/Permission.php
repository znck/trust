<?php

namespace Znck\Trust\Traits;

/**
 * Class PermissionHasRelations.
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\Znck\Trust\Contracts\Role[] roles
 * @property-read \Illuminate\Database\Eloquent\Collection users
 *
 * @method \Illuminate\Database\Eloquent\Relations\BelongsToMany belongsToMany(string $related)
 */
trait Permission
{
    /**
     * Permission belongs to many roles.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(config('znck.trust.models.role'))->withTimestamps();
    }

    /**
     * Permission belongs to many users.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(config('znck.trust.models.user', config('auth.providers.users.model')))->withTimestamps();
    }
}
