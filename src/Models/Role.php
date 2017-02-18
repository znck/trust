<?php

namespace Znck\Trust\Models;

use Illuminate\Database\Eloquent\Model;
use Znck\Trust\Contracts\Role as RoleContract;
use Znck\Trust\Traits\Role as RoleTrait;

class Role extends Model implements RoleContract
{
    use RoleTrait;

    protected $fillable = ['name', 'slug', 'description'];
}
