<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Form extends Model
{
    protected $table = 'forms';

    const PAYMENT_INVOICE = 1;
    const PAYMENT_AUTOMATIC_COLLECTION = 2;
}
