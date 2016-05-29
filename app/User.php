<?php

namespace App;

use DB;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    const GENDER_UNKNOWN = 1;
    const GENDER_FEMALE = 2;
    const GENDER_MALE = 3;

    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public static $aDateFormats = [
        'Y-m-d' => 'YYYY-MM-DD',
        'd-m-Y' => 'MM-DD-YYYY'
    ];
}
