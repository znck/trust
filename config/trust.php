<?php

return [
    'models' => [
        // 'user' => 'App\User',
        'role'       => Znck\Trust\Models\Role::class,
        'permission' => Znck\Trust\Models\Permission::class,
    ],
    'actions' => [
        'create' => 'Create',
        'read'   => 'Read',
        'update' => 'Update',
        'delete' => 'Delete',
    ],
    'permissions' => 'trust/permissions.php',
    'roles'       => 'trust/roles.php',
];
