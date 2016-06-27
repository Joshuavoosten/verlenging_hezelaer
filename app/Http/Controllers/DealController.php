<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Support\Facades\Input;

class DealController extends Controller
{
    public function index()
    {
        return view('content.deals.index');
    }

    public function json()
    {
        $oDB = DB::table('campaign_customers')
            ->select(
                'id',
                'client_name',
                'email_commercieel'
            )
            ->where('status', '>=', 4)
        ;

        /*
        if (Input::get('search')) {
            $oDB->whereRaw('MATCH(name) AGAINST(? IN BOOLEAN MODE)', [Input::get('search')]);
        }
        */

        if (Input::get('sort') && Input::get('order')) {
            $oDB->orderBy(Input::get('sort'), Input::get('order'));
        } else {
            $oDB->orderBy('client_name');
        }

        $total = $oDB->count();

        $offset = Input::get('offset', 0);

        $limit = Input::get('limit', 25);

        $oDB->skip($offset)->take($limit);

        $a = $oDB->get();

        $aRows = [];

        if (count($a) > 0) {
            foreach ($a as $o) {
                $aRows[] = [
                    'id' => $o->id,
                    'client_name' => $o->client_name,
                    'email_commercieel' => $o->email_commercieel
                ];
            }
        }

        $aResponse = [
            'total' => $total,
            'rows' => $aRows,
        ];

        return response()->json($aResponse);
    }
}
