<?php

namespace Znck\Trust\Contracts;

interface Permissible
{
    /**
     * Checks if the user has Permission.
     *
     * @param string|Permission $permission
     *
     * @return bool
     */
    public function hasPermissionTo($permission);

    public function refreshPermissions();
}
