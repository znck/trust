<?php namespace Znck\Trust\Contracts;

interface Permissible
{
    /**
     * Checks if the user has Permission.
     *
     * @param  string|Permission $permission
     *
     * @return bool
     */
    public function hasPermissionTo($permission);

    /**
     * Check if the user has Role.
     *
     * @param  string|Role $role
     *
     * @return bool
     */
    public function canAssumeRole($role);
}
