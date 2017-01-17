<?php

return [
    'models' => [
        // 'user' => 'App\User',
        'role'       => Znck\Trust\Models\Role::class,
        'permission' => Znck\Trust\Models\Permission::class,
    ],
    'permissions' => 'trust/permissions.php',
    'roles'       => 'trust/roles.php',
];
