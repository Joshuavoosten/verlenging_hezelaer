<?php

namespace App\Models;

use DB;
use Illuminate\Database\Eloquent\Model;

class Price extends Model
{
    protected $table = 'prices';

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
}
