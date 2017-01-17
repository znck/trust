<?php

namespace Znck\Tests\Trust\Models;

use Illuminate\Database\Eloquent\Model;
use Znck\Trust\Contracts\Permissible;

/**
 * Class User.
 *
 * @internal Znck\Tests\Trust
 */
class User extends Model implements Permissible
{
    use \Znck\Trust\Traits\Permissible;

    protected $table = 'users';

    protected $fillable = ['name', 'email', 'password'];
}
