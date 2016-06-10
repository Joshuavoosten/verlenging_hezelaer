<?php

namespace App\Http\Controllers;

use Auth;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Session;
use Validator;
use View;

class PricesController extends Controller
{
    public function index()
    {
        // Rates

        $aRates = DB::table('prices')->distinct()->orderby('rate')->pluck('rate', 'rate');
        $aRates = [null => null] + $aRates;

        // Profile Codes

        $aProfileCodes = DB::table('price_codes')->distinct()->orderby('code')->pluck('code', 'code');
        $aProfileCodes = [null => null] + $aProfileCodes;

        return view('content.prices.index', [
            'aProfileCodes' => $aProfileCodes,
            'aRates' => $aRates
        ]);
    }

    public function json()
    {
        $oDB = DB::table('prices AS p')
            ->select(
                'p.id',
                'p.date_start',
                'p.date_end',
                'p.rate',
                'p.codes',
                'p.price',
                'p.created_at',
                'p.updated_at'
            )
            ->join('price_codes AS pc', 'pc.price_id', '=', 'p.id')
        ;

        if (Input::get('rate')) {
            $oDB->where('p.rate', '=', Input::get('rate'));
        }

        if (Input::get('code')) {
            $oDB->where('pc.code', '=', Input::get('code'));
        }

        if (Input::get('search')) {
            $oDB->whereRaw('MATCH(p.rate) AGAINST(? IN BOOLEAN MODE)', [Input::get('search')]);
            $oDB->orWhere('pc.code', '=', Input::get('search'));
        }

        $total = $oDB->count(DB::Raw('DISTINCT p.id'));

        $oDB->groupBy('p.id');

        if (Input::get('sort') && Input::get('order')) {
            switch(Input::get('sort')) {
                case 'date_start':
                    $oDB->orderBy('p.date_start', Input::get('order'));
                    break;
                case 'date_end':
                    $oDB->orderBy('p.date_end', Input::get('order'));
                    break;
                case 'rate':
                    $oDB->orderBy('p.rate', Input::get('order'));
                    break;
                case 'price':
                    $oDB->orderBy('p.price', Input::get('order'));
                    break;
                case 'created_at':
                    $oDB->orderBy('p.created_at', Input::get('order'));
                    break;
                case 'updated_at':
                    $oDB->orderBy('p.updated_at', Input::get('order'));
                    break;
            }
        } else {
            $oDB->orderBy('p.date_start');
        }

        $offset = Input::get('offset', 0);

        $limit = Input::get('limit', 25);

        $oDB->skip($offset)->take($limit);

        $a = $oDB->get();

        $aRows = [];

        if (count($a) > 0) {
            foreach ($a as $o) {
                $aRows[] = [
                    'id' => $o->id,
                    'date_start' => ($o->date_start ? date(Auth::user()->date_format, strtotime($o->date_start)) : ''),
                    'date_end' => ($o->date_end ? date(Auth::user()->date_format, strtotime($o->date_end)) : ''),
                    'rate' => $o->rate,
                    'codes' => str_replace(' ', ',', $o->codes),
                    'price' => number_format($o->price,2,',','.'),
                    'created_at' => ($o->created_at ? date(Auth::user()->date_format.' H:i', strtotime($o->created_at)) : ''),
                    'updated_at' => ($o->updated_at ? date(Auth::user()->date_format.' H:i', strtotime($o->updated_at)) : ''),
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
