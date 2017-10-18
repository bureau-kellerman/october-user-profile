<?php namespace Kellerman\UserProfile\Models;

use October\Rain\Exception\ApplicationException;
use October\Rain\Database\Model;
use RainLab\User\Models\User;

/**
 * User Group Model
 */
class UserProfileField extends Model
{

    /**
     * @var string The database table used by the model.
     */
    protected $table = 'user_profile_fields';

    /**
     * Validation rules
     */
    public $rules = [
       
    ];

    /**
     * @var array Relations
     */
    public $belongsToOne = [
        'users'       => [User::class],
    ];

    /**
     * @var array The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'label',
        'type'
    ];
 
}
