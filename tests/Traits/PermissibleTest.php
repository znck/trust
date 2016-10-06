<?php

namespace Znck\Tests\Trust\Traits;

use Illuminate\Support\Debug\Dumper;
use Znck\Tests\Trust\TestCase;
use Znck\Trust\Models\Permission;
use Znck\Trust\Models\Role;

class PermissibleTest extends TestCase
{
    public function test_it_can_check_permission()
    {
        $user = $this->createUser();
        $this->assertFalse($user->hasPermissionTo('create-post'));

        $permission = Permission::create(['name' => 'Create post', 'slug' => 'create-post']);
        $user->permissions()->attach($permission);
        $user->refreshPermissions();
        // Query permission with name (slug).
        $this->assertTrue($user->hasPermissionTo('create-post'), 'user cannot create post');
        // Query permission with Permission object.
        $this->assertTrue($user->hasPermissionTo($permission), 'user cannot create post (object)');

        // Detach Permission.
        $user->permissions()->detach($permission);
        $user->refreshPermissions();
        $this->assertCount(0, $user->getPermissions());
        $user->refreshPermissions();

        // Verify Permission.
        $user->hasPermissionTo($permission);
        $this->assertFalse($user->hasPermissionTo(null));
    }

    public function test_it_can_check_permission_through_role()
    {
        $user = $this->createUser();
        $this->assertFalse($user->hasPermissionTo('create-post'));

        $permission = Permission::create(['name' => 'Create post', 'slug' => 'create-post']);
        $role = Role::create(['name' => 'Author', 'slug' => 'author']);
        $role->permissions()->attach($permission);
        $user->roles()->attach($role);

        $user->refreshPermissions();

        $this->assertTrue($user->hasPermissionTo('create-post'), 'user cannot create post');
    }
}
