<?php namespace Znck\Trust\Traits;

trait Permissible
{
    use HasPermission;

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
