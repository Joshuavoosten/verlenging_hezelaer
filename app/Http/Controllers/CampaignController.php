<?php

namespace App\Http\Controllers;

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

class CampaignController extends Controller
{
    public function index()
    {
        $iCountPlanned = ModelCampaign::countPlanned();
        $iCountSent = ModelCampaign::countSent();

        return view('content.campaigns.index', [
            'iCountPlanned' => $iCountPlanned,
            'iCountSent' => $iCountSent,
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
                'updated_at',
                DB::RAW('(SELECT COUNT(id) FROM deals AS d WHERE d.campaign_id = campaigns.id) AS count_customers') 
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
                    'count_customers' => $o->count_customers,
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
                'updated_at',
                DB::RAW('(SELECT COUNT(id) FROM deals AS d WHERE d.campaign_id = campaigns.id) AS count_customers') 
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
                    'count_customers' => $o->count_customers,
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
            'Zakelijk' => 'Zakelijk',
            'Consument' => 'Consument'
        ];

        // In a group (current)
        $aCurrentInAGroup = [
            null => '',
            'Y' => __('Yes'),
            'N' => __('No') 
        ];

        // Under an agent (current)
        $aCurrentUnderAnAgent = [
            null => '',
            'Y' => __('Yes'),
            'N' => __('No') 
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

                // In a group

                $current_in_a_group = null; 

                if (in_array(Input::get('current_in_a_group'), ['Y','N'])) { 
                    $current_in_a_group = (Input::get('current_in_a_group') == 'Y' ? 1 : 0);
                }

                // Under an agent

                $current_under_an_agent = null;

                if (in_array(Input::get('current_under_an_agent'), ['Y','N'])) { 
                    $current_under_an_agent = (Input::get('current_under_an_agent') == 'Y' ? 1 : 0);
                }

                $oCampaign->name = Input::get('name');
                $oCampaign->current_segment = Input::get('current_segment');
                $oCampaign->current_in_a_group = $current_in_a_group;
                $oCampaign->current_under_an_agent = $current_under_an_agent;
                $oCampaign->current_profile_code = Input::get('current_profile_code');
                $oCampaign->current_agreement = Input::get('current_agreement');
                $oCampaign->current_expiration_date = date('Y-m-d', strtotime(Input::get('current_expiration_date')));
                $oCampaign->new_agreement = Input::get('new_agreement');
                $oCampaign->new_term_offer = Input::get('new_term_offer');
                $oCampaign->new_percentage = Input::get('new_percentage');

                // Prices

                $aPrices = ModelPrice::getCampaignPrices(Input::get('current_profile_code'), Input::get('current_expiration_date'));

                if (count($aPrices) == 0) {
                    $aErrors['prices'] = __('There are no prices available.');
                } else {

                    // Prices

                    $oCampaign->price_normal = (array_key_exists('normal', $aPrices) ? $aPrices['normal'] : 0);
                    $oCampaign->price_low = (array_key_exists('low', $aPrices) ? $aPrices['low'] : 0);
                    $oCampaign->price_enkel = (array_key_exists('enkel', $aPrices) ? $aPrices['enkel'] : 0);

                    // Deals

                    $oDB = DB::table('contractgegevens')
                        ->select(
                            'client_name',
                            'client_code',
                            'ean',
                            'street',
                            'number',
                            'nr_conn',
                            'zip',
                            'city',
                            'code',
                            'super_contract_number',
                            'syu_normal',
                            'syu_low',
                            'end_agreement',
                            'email_commercieel',
                            'telnr_commercieel',
                            'aanhef_commercieel',
                            'vastrecht',
                            'accountmanager',
                            'auto_renewal',
                            'segment',
                            'label',
                            'agent',
                            'groep',
                            'price_normal',
                            'price_low',
                            'new_price_enkel',
                            'new_price_normal',
                            'new_price_low',
                            'jaarlijkse_besparing'
                        )
                    ;

                    // Segment

                    if (Input::get('current_segment')) {
                        $oDB->where('segment', '=', Input::get('current_segment'));
                        if (Input::get('current_segment') == 'Zakelijk') {
                            $oDB->orWhereNull('segment');
                            $oDB->orWhere('segment', '=', '');
                        }
                    }

                    // In a group

                    if (Input::get('current_in_a_group') == 'Y') {
                        $oDB->whereNotNull('groep');
                        $oDB->where('groep', '!=', '');
                    }

                    if (Input::get('current_in_a_group') == 'N') {
                        $oDB->whereNull('groep');
                        $oDB->orWhere('groep', '=', '');
                    }

                    // Under an agent

                    if (Input::get('current_under_an_agent') == 'Y') {
                        $oDB->whereNotNull('agent');
                        $oDB->where('agent', '!=', '');
                    }

                    if (Input::get('current_under_an_agent') == 'N') {
                        $oDB->whereNull('agent');
                        $oDB->orWhere('agent', '=', '');
                    }

                    // Profile Code
                    $oDB->where('code', '=', Input::get('current_profile_code'));

                    // Agreement

                    if (Input::get('current_agreement') == 'Zeker & Vast') {
                        $oDB->where('end_agreement', '=', '3000-01-01');
                    }

                    if (Input::get('current_agreement') == 'Onbepaald') {
                        $oDB->where('end_agreement', '!=', '3000-01-01');

                        // Expiration Date
                        $oDB->where('end_agreement', '<=', date('Y-m-d', strtotime(Input::get('current_expiration_date'))));
                    }

                    if ($oDB->count() == 0) {
                        $aErrors['deals'] = __('There are no contracts were found.');
                    } else {
                        // Campaign
                        $oCampaign->save();

                        // Deals

                        $a = $oDB->get();

                        foreach ($a as $o) {
                            $oDeal = new ModelDeal;

                            $oDeal->campaign_id = $oCampaign->id;
                            $oDeal->client_name = $o->client_name;
                            $oDeal->client_code =  $o->client_code;
                            $oDeal->ean = $o->ean;
                            $oDeal->street = $o->street;
                            $oDeal->number = $o->number;
                            $oDeal->nr_conn = $o->nr_conn;
                            $oDeal->zip = $o->zip;
                            $oDeal->city = $o->city;
                            $oDeal->code = $o->code;
                            $oDeal->super_contract_number = $o->super_contract_number;
                            $oDeal->syu_normal = $o->syu_normal;
                            $oDeal->syu_low = $o->syu_low;
                            $oDeal->end_agreement = $o->end_agreement;
                            $oDeal->email_commercieel = $o->email_commercieel;
                            $oDeal->telnr_commercieel = $o->telnr_commercieel;
                            $oDeal->aanhef_commercieel = $o->aanhef_commercieel;
                            $oDeal->vastrecht = $o->vastrecht;
                            $oDeal->accountmanager = $o->accountmanager;
                            $oDeal->auto_renewal = $o->auto_renewal;
                            $oDeal->segment = $o->segment;
                            $oDeal->label = $o->label;
                            $oDeal->agent = $o->agent;
                            $oDeal->groep = $o->groep;
                            $oDeal->price_normal = $o->new_price_normal;
                            $oDeal->price_low = $o->new_price_low;
                            $oDeal->new_price_enkel = null;
                            $oDeal->new_price_normal = null;
                            $oDeal->new_price_low = null;
                            $oDeal->jaarlijkse_besparing = null;

                            $oDeal->save();
                        }

                        return Redirect::to('/campaigns/details/'.$oCampaign->id)
                           ->with('success', sprintf(
                                   __('The %s "%s" has been added.'),
                                   __('campaign'),
                                   '<em>'.e(Input::get('name')).'</em>'
                               )
                           )
                       ;
                    }
                }
            }
        }

        return View::make('content.campaigns.add', [
            'aCurrentSegments' => $aCurrentSegments,
            'aCurrentInAGroup' => $aCurrentInAGroup,
            'aCurrentUnderAnAgent' => $aCurrentUnderAnAgent,
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
        $a = DB::table('deals')
            ->select(
                'client_name',
                'client_code',
                'ean',
                'street',
                'number',
                'nr_conn',
                'zip',
                'city',
                'code',
                'super_contract_number',
                'syu_normal',
                'syu_low',
                'end_agreement',
                'email_commercieel',
                'telnr_commercieel',
                'aanhef_commercieel',
                'vastrecht',
                'accountmanager',
                'auto_renewal',
                'segment',
                'label',
                'agent',
                'groep',
                'price_normal',
                'price_low',
                'new_price_enkel',
                'new_price_normal',
                'new_price_low',
                'jaarlijkse_besparing'
            )
            ->where('campaign_id', '=', $id)
            ->get()
        ;

        $filename = 'campaign_deals_'.$id.'.csv';

        $pathToFile = storage_path('app').'/'.$filename;

        $fp = fopen($pathToFile, 'w');

        $fields = [
            'client_name',
            'client_code',
            'ean',
            'street',
            'number',
            'nr_conn',
            'zip',
            'city',
            'code',
            'super_contract_number',
            'syu_normal',
            'syu_low',
            'end_agreement',
            'email_commercieel',
            'telnr_commercieel',
            'aanhef_commercieel',
            'vastrecht',
            'accountmanager',
            'auto_renewal',
            'segment',
            'label',
            'agent',
            'groep',
            'price_normal',
            'price_low',
            'new_price_enkel',
            'new_price_normal',
            'new_price_low',
            'jaarlijkse_besparing'
        ];

        fputcsv($fp, $fields);

        foreach ($a as $k => $v) {
            $fields = [
                'client_name' => $v->client_name,
                'client_code' => $v->client_code,
                'ean' => $v->ean,
                'street' => $v->street,
                'number' => $v->number,
                'nr_conn' => $v->nr_conn,
                'zip' => $v->zip,
                'city' => $v->city,
                'code' => $v->code,
                'super_contract_number' => $v->super_contract_number,
                'syu_normal' => $v->syu_normal,
                'syu_low' => $v->syu_low,
                'end_agreement' => $v->end_agreement,
                'email_commercieel' => $v->email_commercieel,
                'telnr_commercieel' => $v->telnr_commercieel,
                'aanhef_commercieel' => $v->aanhef_commercieel,
                'vastrecht' => $v->vastrecht,
                'accountmanager' => $v->accountmanager,
                'auto_renewal' => $v->auto_renewal,
                'segment' => $v->segment,
                'label' => $v->label,
                'agent' => $v->agent,
                'groep' => $v->groep,
                'price_normal' => $v->price_normal,
                'price_low' => $v->price_low,
                'new_price_enkel' => $v->new_price_enkel,
                'new_price_normal' => $v->new_price_normal,
                'new_price_low' => $v->new_price_low,
                'jaarlijkse_besparing' => $v->jaarlijkse_besparing,
            ];

            fputcsv($fp, $fields);
        }

        fclose($fp);

        return response()->download($pathToFile)->deleteFileAfterSend(true);
    }

    public function detailsJsonCustomersWithoutSaving(Request $request, $id) {
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

    public function detailsJsonCustomersWithSavings() {
        // @todo
    }

    public function detailsJsonCustomersWithCurrentOffer() {
        // @todo
    }

    public function details(Request $request, $id)
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
}