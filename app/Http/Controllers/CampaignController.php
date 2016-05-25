<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use DB;
use Session;
use Validator;
use View;
use App\Models\Campaign as ModelCampaign;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;

class CampaignController extends Controller
{
    public function index()
    {
        $iCountPlannedCampaigns = DB::table('campaigns')->where('status', '=', ModelCampaign::STATUS_PLANNED)->count();
        $iCountSentCampaigns = DB::table('campaigns')->where('status', '=', ModelCampaign::STATUS_SENT)->count();

        return view('content.campaigns.index', [
            'iCountPlannedCampaigns' => $iCountPlannedCampaigns,
            'iCountSentCampaigns' => $iCountSentCampaigns,
            'success' => Session::get('success'),
        ]);
    }

    public function jsonPlanned()
    {
        $oDB = DB::table('campaigns')
            ->select(
                'id',
                'name',
                'current_segment',
                'current_profile_code',
                'current_agreement',
                'current_expiration_date',
                'planned_at',
                'created_at',
                'updated_at'
            )
            ->where('status', '=', ModelCampaign::STATUS_PLANNED)
        ;

        if (Input::get('search')) {
            $oDB->whereRaw('MATCH(name) AGAINST(? IN BOOLEAN MODE)', [Input::get('search')]);
        }

        if (Input::get('sort') && Input::get('order')) {
            $oDB->orderBy(Input::get('sort'), Input::get('order'));
        } else {
            $oDB->orderBy('name');
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
                    'name' => $o->name,
                    'current_segment' => $o->current_segment,
                    'current_profile_code' => $o->current_profile_code,
                    'current_agreement' => $o->current_agreement,
                    'current_expiration_date' => ($o->current_expiration_date ? date('d-m-Y', strtotime($o->current_expiration_date)) : ''),
                    'count_customers' => 0,
                    'planned_at' => ($o->planned_at ? date('d-m-Y H:i', strtotime($o->planned_at)) : ''),
                    'created_at' => ($o->created_at ? date('d-m-Y H:i', strtotime($o->created_at)) : ''),
                    'updated_at' => ($o->updated_at ? date('d-m-Y H:i', strtotime($o->updated_at)) : ''),
                ];
            }
        }

        $aResponse = [
            'total' => $total,
            'rows' => $aRows,
        ];

        return response()->json($aResponse);
    }

    public function jsonSent()
    {
        $oDB = DB::table('campaigns')
            ->select(
                'id',
                'name',
                'current_segment',
                'current_profile_code',
                'current_agreement',
                'current_expiration_date',
                'created_at',
                'updated_at'
            )
            ->where('status', '=', ModelCampaign::STATUS_SENT)
        ;

        if (Input::get('search')) {
            $oDB->whereRaw('MATCH(name) AGAINST(? IN BOOLEAN MODE)', [Input::get('search')]);
        }

        if (Input::get('sort') && Input::get('order')) {
            $oDB->orderBy(Input::get('sort'), Input::get('order'));
        } else {
            $oDB->orderBy('name');
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
                    'name' => $o->name,
                    'current_segment' => $o->current_segment,
                    'current_profile_code' => $o->current_profile_code,
                    'current_agreement' => $o->current_agreement,
                    'current_expiration_date' => ($o->current_expiration_date ? date('d-m-Y', strtotime($o->current_expiration_date)) : ''),
                    'count_customers' => 0,
                    'created_at' => ($o->created_at ? date('d-m-Y H:i', strtotime($o->created_at)) : ''),
                    'updated_at' => ($o->updated_at ? date('d-m-Y H:i', strtotime($o->updated_at)) : ''),
                ];
            }
        }

        $aResponse = [
            'total' => $total,
            'rows' => $aRows,
        ];

        return response()->json($aResponse);
    }

    public function add(Request $request)
    {
        $request->flash();

        $aErrors = [];

        // Segments (current)
        $aCurrentSegments = [
            'Zakelijk' => 'Zakelijk'
        ];

        // Profile Codes (current)
        $aCurrentProfileCodes = DB::table('contractgegevens')->distinct()->orderby('code')->pluck('code', 'code');

        // Agreements (current)
        $aCurrentAgreements = [
            'Zeker & Vast' => 'Zeker & Vast',
            'Onbepaald' => 'Onbepaald'
        ];

        // Expiration Date

        $aCurrentExpirationDate = [];

        for ($i = date('Y') + 1; $i < date('Y') + 5; $i++) {
            $aCurrentExpirationDate['01-01-'.$i] = __('t/m').' '.'1-1-'.$i;
        }

        // Agreements (new)
        $aNewAgreements = [
            'Zeker & Vast' => 'Zeker & Vast',
            'Onbepaald' => 'Onbepaald'
        ];

        // Term Offers (new)
        $aNewTermOffers = [
            '+1 weeks' => '1 week',
            '+2 weeks' => '2 weken',
            '+3 weeks' => '3 weken'
        ];

        // Percentages (new)
        $aNewPercentages = [
            15 => '15 %',
            20 => '20 %',
            25 => '25 %',
        ];

        if ($request->isMethod('post')) {
            $aMessages = [
                'name.required' => sprintf(__('%s is required.'), __('Name')),
                'current_segment.required' => sprintf(__('%s is required.'), __('Segment')),
                'current_profile_code.required' => sprintf(__('%s is required.'), __('Profile Code')),
                'current_agreement.required' => sprintf(__('%s is required.'), __('Agreement')),
                'current_expiration_date.required' => sprintf(__('%s is required.'), __('Expiration Date')),
                'new_agreement.required' => sprintf(__('%s is required.'), __('Agreement')),
                'new_term_offer.required' => sprintf(__('%s is required.'), __('Term Offer')),
                'new_percentage.required' => sprintf(__('%s is required.'), __('Percentage')),
            ];

            $oValidator = Validator::make(Input::all(), [
                'name' => 'required',
                'current_segment' => 'required',
                'current_profile_code' => 'required',
                'current_agreement' => 'required',
                'current_expiration_date' => 'required',
                'new_agreement' => 'required',
                'new_term_offer' => 'required',
                'new_percentage' => 'required',
            ], $aMessages);

            if ($oValidator->fails()) {
                $aErrors = $oValidator->errors();
            }

            if (count($aErrors) == 0) {
                $password = Input::get('password');

                // Campaign

                $oCampaign = new ModelCampaign();

                $oCampaign->name = Input::get('name');
                $oCampaign->current_segment = Input::get('current_segment');
                $oCampaign->current_profile_code = Input::get('current_profile_code');
                $oCampaign->current_agreement = Input::get('current_agreement');
                $oCampaign->current_expiration_date = date('Y-m-d', strtotime(Input::get('current_expiration_date')));
                $oCampaign->new_agreement = Input::get('new_agreement');
                $oCampaign->new_term_offer = Input::get('new_term_offer');
                $oCampaign->new_percentage = Input::get('new_percentage');

                $oCampaign->save();

                // Deals

                

                return Redirect::to('/campaigns')
                   ->with('success', sprintf(
                           __('The %s "%s" has been added.'),
                           __('campaign'),
                           '<em>'.e(Input::get('name')).'</em>'
                       )
                   )
               ;
            }
        }

        return View::make('content.campaigns.add', [
            'aCurrentSegments' => $aCurrentSegments,
            'aCurrentAgreements' => $aCurrentAgreements,
            'aCurrentProfileCodes' => $aCurrentProfileCodes,
            'aCurrentExpirationDate' => $aCurrentExpirationDate,
            'aNewAgreements' => $aNewAgreements,
            'aNewTermOffers' => $aNewTermOffers,
            'aNewPercentages' => $aNewPercentages
        ])->withErrors($aErrors);
    }

    public function edit(Request $request, $id)
    {
        $oCampaign = new ModelCampaign();

        $oCampaign = $oCampaign->findOrFail($id);

        $aErrors = [];

        if ($request->isMethod('post')) {
            $aRules = [
                'name' => 'required'
            ];

            $aMessages = [
                'name.required' => sprintf(__('%s is required.'), __('Name'))
            ];

            $oValidator = Validator::make(Input::all(), $aRules, $aMessages);

            if ($oValidator->fails()) {
                $aErrors = $oValidator->errors();
            }

            if (count($aErrors) == 0) {
                $password = Input::get('password');

                $oCampaign->name = Input::get('name');

                $oCampaign->save();

                return Redirect::to('/campaigns')
                    ->with('success', sprintf(
                            __('The %s "%s" has been saved.'),
                            __('campaign'),
                            '<em>'.e(Input::get('name')).'</em>'
                        )
                    )
                ;
            }
        }

        return View::make('content.campaigns.edit', [
            'oCampaign' => $oCampaign,
        ])->withErrors($aErrors);
    }

    public function delete(Request $request, $id)
    {
        $oCampaign = new ModelCampaign();

        $oCampaign = $Campaign->findOrFail($id);

        $aResponse = [
            'status' => 'OK',
            'alert' => sprintf(
                __('The %s "%s" has been deleted.'),
                __('campaign'),
                '<em>'.e($oCampaign->name).'</em>'
            ),
        ];

        $oCampaign->delete();

        return response()->json($aResponse);
    }

    public function csv(Request $request, $id)
    {
        // @todo
    }

    public function details(Request $request, $id)
    {
         $oCampaign = new ModelCampaign();

         $oCampaign = $oCampaign->find($id);

         if (!$oCampaign) {
             App::abort(404, 'Campaign Not Found.');
         }

         $iCountCustomers = 0; // @todo

         return View::make('content.campaigns.details', [
             'oCampaign' => $oCampaign,
             'iCountCustomers' => $iCountCustomers
         ]);
    }

}