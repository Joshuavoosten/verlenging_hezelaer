<?php

namespace App\Models;

use DB;
use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    const TYPE_GAS = 1;
    const TYPE_ELEKTRICITY = 2;

    const STATUS_PLANNED = 1;
    const STATUS_SENT = 2;

    protected $table = 'campaigns';

    public static function countPlanned() {
        return DB::table('campaigns')->where('status', '=', self::STATUS_PLANNED)->count();
    }

    public static function countSent() {
        return DB::table('campaigns')->where('status', '=', self::STATUS_SENT)->count();
    }

    /**
     * @param string $sNewTermOffer
     * @return string
     */
    public static function newTermOfferFormatter($sNewTermOffer) {
        switch ($sNewTermOffer) {
            case '+1 weeks':
                return sprintf(__('%s week'), 1);
            case '+2 weeks':
                return sprintf(__('%s weeks'), 2);
            case '+3 weeks':
                return sprintf(__('%s weeks'), 3);
            default:
                return $sNewTermOffer;
        }
    }

    public function countCustomers() {
        return DB::table('deals')->where('campaign_id', '=', $this->id)->count();
    }

    /**
     * @return bool true | false
     */
    public function isGas() {
        return ($this->type == self::TYPE_GAS ? true : false);
    }

    /**
     * @return bool true | false
     */
    public function isElektricity() {
        return ($this->type == self::TYPE_ELEKTRICITY ? true : false);
    }
}
