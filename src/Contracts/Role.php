<?php

namespace Znck\Trust\Contracts;

/**
 * Interface Role.
 *
 * @property string name
 * @property string slug
 * @property string description
 */
interface Role
{
    /**
     * Role belongs to many permissions.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function permissions();

    /**
     * Role belongs to many users.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users();
}
