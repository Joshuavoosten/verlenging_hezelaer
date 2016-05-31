<?php

namespace App\Models;

use DB;
use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    const STATUS_PLANNED = 1;
    const STATUS_SENT = 2;

    protected $table = 'campaigns';

    public static function countPlanned() {
        return DB::table('campaigns')->where('status', '=', self::STATUS_PLANNED)->count();
    }

    public static function countSent() {
        return DB::table('campaigns')->where('status', '=', self::STATUS_SENT)->count();
    }

    public function countCustomers() {
        return DB::table('deals')->where('campaign_id', '=', $this->id)->count();
    }

    public function isElektricity() {
        return ($this->price_normal > 0 && $this->price_low > 0);
    }

    public function isGas() {
        return ! ($this->price_normal > 0 && $this->price_low > 0);
    }
}
