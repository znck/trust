<?php
/**
 * This file belongs to Trust
 *
 * Author: Rahul Kadyan, <hi@znck.me>
 */
namespace Znck\Trust\Contracts\Trust;


/**
 * Class User
 *
 * @package Sereno
 */
interface HasRole
{
    /**
     * Checks if the user has a Role by its name.
     *
     * @param string $name Role name.
     *
     * @return bool
     */
    public function hasRole($name);

    /**
     * Check if user has a permission by its name.
     *
     * @param string $permission Permission string.
     *
     * @return bool
     */
    public function can($permission);

    /**
     * Checks role(s) and permission(s).
     *
     * @param string|array $roles       Array of roles or comma separated string
     * @param string|array $permissions Array of permissions or comma separated string.
     * @param array        $options     validate_all (true|false) or return_type (boolean|array|both)
     *
     * @return array|bool
     * @throws \InvalidArgumentException
     */
    public function ability($roles, $permissions, $options = []);
}