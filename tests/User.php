<?php
use Illuminate\Database\Eloquent\Model;

/**
 * This file belongs to Trust.
 *
 * Author: Rahul Kadyan, <hi@znck.me>
 */
class User extends Model implements \Znck\Trust\Contracts\Trust\HasRole
{
    protected $table = 'users';
    use \Znck\Trust\RoleTrait;
}