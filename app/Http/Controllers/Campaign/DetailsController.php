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
         $aErrors = [];

         $oCampaign = new ModelCampaign();

         $oCampaign = $oCampaign->find($id);

         if (!$oCampaign) {
             App::abort(404, 'Campaign Not Found.');
         }

         if ($oCampaign->scheduled) {
             $iScheduleDay = date('d', strtotime($oCampaign->scheduled_at));
             $iScheduleMonth = date('m', strtotime($oCampaign->scheduled_at));
             $iScheduleYear = date('Y', strtotime($oCampaign->scheduled_at));
             $sScheduleHour = date('H', strtotime($oCampaign->scheduled_at));
             $sScheduleMinute = date('i', strtotime($oCampaign->scheduled_at));
         } else {
             $iScheduleDay = date('d') + 1;
             $iScheduleMonth = date('m');
             $iScheduleYear = date('Y');
             $sScheduleHour = "03";
             $sScheduleMinute = "00";
         }

         // Days

         $aDays = [];

         for ($i = 1; $i <= 31; $i++){
             $val = ($i < 10) ? '0'.$i : $i;
             $aDays[$val] = $val;
         }

         // Hours

         for ($i = 1; $i <= 23; $i++){
             $val = ($i < 10) ? '0'.$i : $i;
             $aHours[$val] = $val;
         }

         // Minutes
         $aMinutes = [
             "00" => "00",
             "15" => "15",
             "30" => "30",
             "45" => "45"
         ];

         if ($request->isMethod('post')) {
             $aMessages = [
                 'schedule.required' => sprintf(__('%s is required.'), __('Schedule')),
                 'day.required' => sprintf(__('%s is required.'), __('Day')),
                 'month.required' => sprintf(__('%s is required.'), __('Month')),
                 'year.required' => sprintf(__('%s is required.'), __('Year')),
                 'hour.required' => sprintf(__('%s is required.'), __('Hour')),
                 'minute.required' => sprintf(__('%s is required.'), __('Minute')),
             ];

             $oValidator = Validator::make(Input::all(), [
                 'schedule' => 'required',
                 'day' => 'required',
                 'month' => 'required',
                 'year' => 'required',
                 'hour' => 'required',
                 'minute' => 'required',
             ], $aMessages);

             if ($oValidator->fails()) {
                 $aErrors = $oValidator->errors();
             }

             if (count($aErrors) == 0) {

                 if (Input::get('schedule') == 'now') {
                     $sScheduledAt = date('Y-m-d H:i', strtotime('+15 minutes'));
                 }

                 if (Input::get('schedule') == 'planned') {
                     $sScheduledAt = date('Y-m-d H:i:s', strtotime(Input::get('year').'-'.Input::get('month').'-'.Input::get('day').' '.Input::get('hour').':'.Input::get('minute')));
                 }

                 $oCampaign->scheduled = 1;
                 $oCampaign->scheduled_at = $sScheduledAt;

                 $oCampaign->save();

                 // Add deal to queue
                 DB::table('deals')->where('campaign_id', $oCampaign->id)->update(['queue' => 1]);

                 return Redirect::to('/campaigns/details/'.$oCampaign->id)
                    ->with('success', sprintf(
                            __('The %s "%s" has been scheduled.'),
                            __('campaign'),
                            '<em>'.e($oCampaign->name).'</em>'
                        )
                    )
                ;
             }
         }

         return View::make('content.campaigns.details', [
             'oCampaign' => $oCampaign,
             'iScheduleDay' => $iScheduleDay,
             'iScheduleMonth' => $iScheduleMonth,
             'iScheduleYear' => $iScheduleYear,
             'sScheduleHour' => $sScheduleHour,
             'sScheduleMinute' => $sScheduleMinute,
             'aDays' => $aDays,
             'aHours' => $aHours,
             'aMinutes' => $aMinutes
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