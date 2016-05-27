<?php

namespace App\Http\Controllers\Campaign;

use App\Http\Controllers\Controller;
use App\Models\Campaign as ModelCampaign;
use App\Models\Deal as ModelDeal;
use App\Models\Price as ModelPrice;
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

class DetailsController extends Controller
{
    public function index(Request $request, $id)
    {
         $oCampaign = new ModelCampaign();

         $oCampaign = $oCampaign->find($id);

         if (!$oCampaign) {
             App::abort(404, 'Campaign Not Found.');
         }

         return View::make('content.campaigns.details', [
             'oCampaign' => $oCampaign
         ]);
    }

    public function jsonCustomersWithoutSaving(Request $request, $id) {
        $oDB = DB::table('deals')
            ->select(
                'id',
                'client_name',
                'client_code',
                'code',
                'end_agreement',
                'aanhef_commercieel'
            )
            ->where('campaign_id', '=', $id)
            ->where(function($query)
            {
                $query->where('estimate_saving_1_year', '<=', 0)
                      ->where('estimate_saving_2_year', '<=', 0)
                      ->where('estimate_saving_3_year', '<=', 0)
                ;
            })
        ;

        if (Input::get('search')) {
            $oDB->whereRaw('MATCH(client_name,client_code,code,aanhef_commercieel) AGAINST(? IN BOOLEAN MODE)', [Input::get('search')]);
        }

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
                    'client_code' => $o->client_code,
                    'code' => $o->code,
                    'end_agreement' => ($o->end_agreement ? date('j-n-Y', strtotime($o->end_agreement)) : ''),
                    'aanhef_commercieel' => $o->aanhef_commercieel
                ];
            }
        }

        $aResponse = [
            'total' => $total,
            'rows' => $aRows,
        ];

        return response()->json($aResponse);
    }

    public function jsonCustomersWithSavings(Request $request, $id) {
        $oDB = DB::table('deals')
            ->select(
                'id',
                'client_name',
                'client_code',
                'code',
                'end_agreement',
                'aanhef_commercieel'
            )
            ->where('campaign_id', '=', $id)
            ->where(function($query)
            {
                $query->where('estimate_saving_1_year', '>', 0)
                      ->orWhere('estimate_saving_2_year', '>', 0)
                      ->orWhere('estimate_saving_3_year', '>', 0)
                ;
            })
        ;

        if (Input::get('search')) {
            $oDB->whereRaw('MATCH(client_name,client_code,code,aanhef_commercieel) AGAINST(? IN BOOLEAN MODE)', [Input::get('search')]);
        }

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
                    'client_code' => $o->client_code,
                    'code' => $o->code,
                    'end_agreement' => ($o->end_agreement ? date('j-n-Y', strtotime($o->end_agreement)) : ''),
                    'aanhef_commercieel' => $o->aanhef_commercieel
                ];
            }
        }

        $aResponse = [
            'total' => $total,
            'rows' => $aRows,
        ];

        return response()->json($aResponse);
    }

    public function jsonCustomersWithCurrentOffer(Request $request, $id) {
        
    }

}