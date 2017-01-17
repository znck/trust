<?php

/*
|--------------------------------------------------------------------------
| Trust Roles
|--------------------------------------------------------------------------
|
| This file is where you may define your roles.
|
*/

return [
    'admin' => [
        'name'        => 'Administrator',
        'description' => 'These users would have every available permission.',
        'permissions' => ['*'],
    ],
    /*
    'moderator' => [
        'name' => 'Moderator',
        'description' => 'These users can control ...',
        'permissions' => [ 'delete_comment', 'block_user' ],
    ]
     */
];