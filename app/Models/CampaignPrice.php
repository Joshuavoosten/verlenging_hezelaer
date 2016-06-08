<?php

namespace App\Models;

use App;
use Illuminate\Database\Eloquent\Model;

class CampaignPrice extends Model
{
    const TYPE_GAS = 1;
    const TYPE_ELEKTRICITY = 2;

    const CALCULATION_GAS_1_RATE = 1;
    const CALCULATION_ENERGY_2_RATES = 2;
    const CALCULATION_ENERGY_3_RATES = 3;

    protected $table = 'campaign_prices';

    /**
     * @return int self::TYPE_ELEKTRICITY | self::TYPE_GAS
     */
    public function determineType() {
        if ($this->price_normal > 0 && $this->price_low > 0) {
            return self::TYPE_ELEKTRICITY;
        } else {
            return self::TYPE_GAS;
        }
    }

    /**
     * @return mixed int | Internal Server Error
     */
    public function determineCalculation() {
        $iCalculation = 0;

        if ($this->price_normal > 0) $iCalculation++;
        if ($this->price_low > 0) $iCalculation++;
        if ($this->price_enkel > 0) $iCalculation++;

        switch ($iCalculation) {
            case 1: return self::CALCULATION_GAS_1_RATE;
            case 2: return self::CALCULATION_ENERGY_2_RATES;
            case 3: return self::CALCULATION_ENERGY_3_RATES;
            default:
                App::abort(500, 'Campaign Price Calculation Unknown.');
        }
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
