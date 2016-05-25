<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    const STATUS_PLANNED = 1;
    const STATUS_SENT = 2;

    protected $table = 'campaigns';
}
