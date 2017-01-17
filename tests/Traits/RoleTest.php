<?php

namespace Znck\Tests\Trust\Traits;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Znck\Tests\Trust\TestCase;
use Znck\Trust\Models\Permission;
use Znck\Trust\Models\Role;

class RoleTest extends TestCase
{
    public function test_it_has_relations()
    {
        $role = Role::create(['name' => 'Foo', 'slug' => 'foo']);
        $this->assertTrue($role->users instanceof Collection);
        $this->assertTrue($role->users() instanceof BelongsToMany);
        $this->assertTrue($role->permissions instanceof Collection);
        $this->assertTrue($role->permissions() instanceof BelongsToMany);
    }
}
