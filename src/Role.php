<?php namespace Znck\Trust;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;

/**
 * Class Role
 *
 * @package Sereno\Models\User
 * @property-read \Illuminate\Database\Eloquent\Collection|Permission[]                     $permissions
 * @property-read \Illuminate\Database\Eloquent\Collection[]                                $users
 * @property integer                                                                        $id
 * @property string                                                                         $name
 * @method static Builder|Role whereId($value)
 * @method static Builder|Role whereName($value)
 */
class Role extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'roles';

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
     * Whether timestamps are needed or not
     *
     * @type bool
     */
    public $timestamps = false;

    /**
     * Permission owned by the role
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function permissions()
    {
        return $this->belongsToMany('Znck\Trust\Permission');
    }

    /**
     * Users with this role attached to them
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(Config::get('trust::users_model', 'User'));
    }
}
