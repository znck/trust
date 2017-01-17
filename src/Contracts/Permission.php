<?php

namespace Znck\Trust\Contracts;

/**
 * Interface Permission.
 *
 * @property string $name
 * @property string $slug
 * @property string $description
 */
interface Permission
{
    /**
     * Permission belongs to many roles.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles();

    /**
     * Permission belongs to many users.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users();
}
