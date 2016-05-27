<?php

namespace App\Models;

use App;
use DB;
use Illuminate\Database\Eloquent\Model;

class Price extends Model
{
    protected $table = 'prices';

    const CALCULATION_GAS_1_RATE = 1;
    const CALCULATION_ENERGY_2_RATES = 2;
    const CALCULATION_ENERGY_3_RATES = 3;

    /**
     * @param string $code
     * @param string $date_end
     * @return array $r
     */
    public static function getCampaignPrices($code, $date_end) {
        $r = [];

        $oDB = DB::table('prices AS p')
            ->select(
                'p.rate',
                'p.price',
                'p.date_start',
                'p.date_end'
            )
            ->join('price_codes AS pc', 'pc.price_id', '=', 'p.id')
            ->where('pc.code', '=', $code)
            ->where('p.date_end', '<=', date('Y-m-d', strtotime($date_end)))
            ->orderby('p.date_end', 'DESC')
            ->orderby('p.date_start', 'DESC')
            ->limit(3)
        ;

        $a = $oDB->get();

        foreach ($a as $o) {
            if (strpos($o->rate, 'gas') !== false) {
                $r['normal'] = $o->price;
                break;
            }
            if (strpos($o->rate, 'laag') !== false) {
                $r['low'] = $o->price;
            }
            if (strpos($o->rate, 'hoog') !== false) {
                $r['normal'] = $o->price;
            }
            if (strpos($o->rate, 'enkel') !== false) {
                $r['enkel'] = $o->price;
            }
        }

        return $r;
    }

    /**
     * @param array $aPrices
     * @return mixed int | Internal Server Error
     */
    public static function getCalculation($aPrices) {
        switch (count($aPrices)) {
            case 1: return self::CALCULATION_GAS_1_RATE;
            case 2: return self::CALCULATION_ENERGY_2_RATES;
            case 3: return self::CALCULATION_ENERGY_3_RATES;
            default:
                App::abort(500, 'Price Calculation Unknown.');
        }
    }

}
