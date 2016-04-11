<?php

namespace Znck\Tests\Trust\Traits;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Znck\Tests\Trust\TestCase;
use Znck\Trust\Models\Permission;

class PermissionTest extends TestCase
{
    public function test_it_has_relations()
    {
        $permission = Permission::create(['name' => 'Bar', 'slug' => 'bar']);
        $this->assertTrue($permission->users() instanceof BelongsToMany);
        $this->assertTrue($permission->roles() instanceof BelongsToMany);
        $this->assertTrue($permission->users instanceof Collection);
        $this->assertTrue($permission->roles instanceof Collection);
    }
}
