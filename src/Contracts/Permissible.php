<?php

namespace Znck\Trust\Contracts;

interface Permissible
{
    /**
     * Checks if the user has Permission(s).
     *
     * `$permissions` can have following format:
     *      - permission name; eg: 'user.create'
     *      - permission query
     *          - any one: 'user.create|user.delete'
     *          - all: 'user.create,user.delete'
     *      - an array of permission names; eg: ['user.create', 'user.delete']
     *      - an array of permission objects (objects implementing \Znck\Trust\Contracts\Permission)
     *      - a collection of permission objects (objects implementing \Znck\Trust\Contracts\Permission)
     *
     * @param $permissions
     *
     * @return bool
     */
    public function checkPermission($permissions);

    /**
     * Checks if the user has Role(s).
     *
     * `$roles` can have following format:
     *      - role name; eg: 'user'
     *      - role query
     *          - any one: 'user|admin'
     *          - all: 'user,admin'
     *      - an array of role names; eg: ['user', 'admin']
     *      - an array of role objects (objects implementing \Znck\Trust\Contracts\Role)
     *      - a collection of role objects (objects implementing \Znck\Trust\Contracts\Role)
     *
     * @param $roles
     *
     * @return bool
     */
    public function checkRole($roles);
}
