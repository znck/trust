<?php namespace Znck\Tests\Trust\Traits;

use GrahamCampbell\TestBench\AbstractPackageTestCase;
use Znck\Tests\Trust\Models\User;
use Znck\Tests\Trust\TestCase;
use Znck\Trust\Models\Permission;
use Znck\Trust\Models\Role;
use Znck\Trust\TrustServiceProvider;

class PermissibleTest extends TestCase
{
    public function test_it_can_check_permission()
    {
        $user = $this->createUser();
        $this->assertFalse($user->checkPermission('post.create'));

        $permission = Permission::create(['name' => 'Create post', 'slug' => 'post.create']);
        $user->attachPermission($permission);
        // Re-attach permission.
        $this->assertFalse($user->attachPermission($permission));
        // Query permission with name (slug).
        $this->assertTrue($user->checkPermission('post.create'));
        // Query permission with Permission object.
        $this->assertTrue($user->checkPermission($permission));
        // Detach Permission.
        $user->detachPermission($permission);
        $this->assertCount(0, $user->permissions);
        $user->attachPermission($permission);
        $this->assertCount(1, $user->permissions);
        $user->detachAllPermissions();
        $this->assertCount(0, $user->permissions);
        // Query null permission.
        $this->expectException(\InvalidArgumentException::class);
        $user->checkPermission(null);
    }

    public function test_it_can_check_permission_with_list()
    {
        $user = $this->createUser();
        $this->assertFalse($user->checkPermission('post.create|post.update'));
        $this->assertFalse($user->checkPermission('post.create,post.update'));
        $this->assertFalse($user->checkPermission(['post.create', 'post.update']));

        $permission1 = Permission::create(['name' => 'Create post', 'slug' => 'post.create']);
        $user->attachPermission($permission1);
        $permission2 = Permission::create(['name' => 'Update post', 'slug' => 'post.update']);
        $user->attachPermission($permission2);
        $this->assertTrue($user->checkPermission('post.create|post.update'));
        $this->assertTrue($user->checkPermission('post.create,post.update'));
        $this->assertTrue($user->checkPermission(['post.create', 'post.update']));
        $this->assertTrue($user->checkPermission(['post.create', $permission2]));
        $this->assertTrue($user->checkPermission([$permission1, $permission2]));
        $this->assertTrue($user->checkPermission(Permission::all()));
    }

    public function test_it_can_check_role()
    {
        $user = $this->createUser();
        $this->assertFalse($user->checkRole('admin'));

        $permission = Permission::create(['name' => 'Create post', 'slug' => 'post.create']);
        $role = Role::create(['name' => 'Administrator', 'slug' => 'admin']);
        $role->attachPermission($permission);
        // Re-attach permission.
        $this->assertFalse($role->attachPermission($permission));
        $user->assignRole($role);
        // Re-attach role.
        $this->assertFalse($user->assignRole($role));
        // Query permission with name (slug).
        $this->assertTrue($user->checkRole('admin'));
        // Query permission with Permission object.
        $this->assertTrue($user->checkRole($role));
        // Detach Permission.
        $user->removeRole($role);
        $this->assertCount(0, $user->permissions);
        $user->assignRole($role);
        $this->assertCount(1, $user->permissions);
        // Query null permission.
        $this->expectException(\InvalidArgumentException::class);
        $user->checkRole(null);
    }

    public function test_it_can_check_role_with_list()
    {
        $user = $this->createUser();
        $this->assertFalse($user->checkRole('admin|author'));
        $this->assertFalse($user->checkRole('admin,author'));
        $this->assertFalse($user->checkRole(['admin', 'author']));

        $permission1 = Permission::create(['name' => 'Create post', 'slug' => 'post.create']);
        $admin = Role::create(['name' => 'Administrator', 'slug' => 'admin']);
        $admin->attachPermission($permission1);
        $permission2 = Permission::create(['name' => 'Update post', 'slug' => 'post.update']);
        $author = Role::create(['name' => 'Author', 'slug' => 'author']);
        $author->attachPermission($permission2);

        $this->assertTrue($user->assignRole($admin));
        $this->assertTrue($user->assignRole($author));

        $this->assertTrue($user->checkRole('admin|author'));
        $this->assertTrue($user->checkRole('admin,author'));
        $this->assertTrue($user->checkRole(['admin', 'author']));
        $this->assertTrue($user->checkRole(['admin', $author]));
        $this->assertTrue($user->checkRole([$admin, $author]));
        $this->assertTrue($user->checkRole(Role::all()));

        $this->assertTrue($user->checkPermission('post.create|post.update'));
        $this->assertTrue($user->checkPermission('post.create,post.update'));
        $this->assertTrue($user->checkPermission(['post.create', 'post.update']));
        $this->assertTrue($user->checkPermission(['post.create', $permission2]));
        $this->assertTrue($user->checkPermission([$permission1, $permission2]));
        $this->assertTrue($user->checkPermission(Permission::all()));
    }

    public function test_it_cannot_load_permissions_for_invalid_config()
    {
        $user = $this->createUser();
        config(['znck.trust.models.permission' => static::class]);
        $this->expectException(\InvalidArgumentException::class);
        $user->permissions;
    }
}
