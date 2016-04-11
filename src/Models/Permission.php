<?php

namespace Znck\Trust\Models;

use Illuminate\Database\Eloquent\Model;
use Znck\Trust\Contracts\Permission as PermissionContract;
use Znck\Trust\Traits\Permission as PermissionTrait;

class Permission extends Model implements PermissionContract
{
    use PermissionTrait;

    protected $fillable = ['name', 'slug', 'description'];
}
