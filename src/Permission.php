<?php namespace Znck\Trust;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;

/**
 * Class Permission
 * 
 * Eloquent ORM for permissions table
 *
 * @package Sereno\Models\User
 * @property-read \Illuminate\Database\Eloquent\Collection|Role[] $roles
 * @property integer $id
 * @property string $name
 * @method static Builder|Permission whereId($value)
 * @method static Builder|Permission whereName($value)
 */
class Permission extends Model
{
    /**
     * Whether timestamps are needed or not
     *
     * @type bool
     */
    public $timestamps = false;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'permissions';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name'];
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['id'];

    /**
     * Roles which contain this permission
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany('Znck\Trust\Role');
    }
}
