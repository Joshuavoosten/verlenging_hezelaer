<?php

namespace App\Models;

use App;
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
                'p.date_end',
                DB::raw('(YEAR(date_end) - YEAR(date_start)) AS years')
            )
            ->join('price_codes AS pc', 'pc.price_id', '=', 'p.id')
            ->where('pc.code', '=', $code)
            ->where('p.date_start', '=', date('Y-m-d', strtotime($date_end)))
            ->orderby('p.date_start', 'ASC')
            ->orderby('p.date_end', 'ASC')
        ;

        $a = $oDB->get();

        foreach ($a as $o) {
            $r[$o->years]['date_start'] = $o->date_start;
            $r[$o->years]['date_end'] = $o->date_end;
            $r[$o->years]['rate'] = $o->rate;
            $r[$o->years]['code'] = $code;
            
            if (strpos($o->rate, 'gas') !== false) {
                $r[$o->years]['normal'] = $o->price;
                break;
            }
            if (strpos($o->rate, 'laag') !== false) {
                $r[$o->years]['low'] = $o->price;
            }
            if (strpos($o->rate, 'hoog') !== false) {
                $r[$o->years]['normal'] = $o->price;
            }
            if (strpos($o->rate, 'enkel') !== false) {
                $r[$o->years]['enkel'] = $o->price;
            }
        }

        return $r;
    }

}
