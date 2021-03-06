<?php

namespace App\Http\Controllers;

use App\Models\Campaign as ModelCampaign;
use App\Models\CampaignPrice as ModelCampaignPrice;
use App\Models\CampaignCustomer as ModelCampaignCustomer;
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
    /**
     * Fetch all email adresses with multiple client_code's => holdings.
     *
     * @return array $aHoldings
     */
    private function getHoldings() {
        $a = DB::table('contractgegevens')
            ->select('client_code','email_commercieel')
            ->get()
        ;

        $b = [];

        foreach ($a as $o) {
            if (!$o->email_commercieel) {
                continue;
            }
            $email_commercieel = strtolower($o->email_commercieel);
            if (!array_key_exists($email_commercieel, $b)) {
                $b[$email_commercieel] = [];
            }
            if (!in_array($o->client_code, $b[$email_commercieel])) {
                $b[$email_commercieel][] = $o->client_code;
            }
        }

        $aHoldings = [];

        foreach ($b as $k => $v) {
            if (count($v) > 1) {
                $aHoldings[$k] = $v;
            }
        }

        return $aHoldings;
    }

    public function index()
    {
        $iCountPlanned = ModelCampaign::countPlanned();
        $iCountSent = ModelCampaign::countSent();

        return view('content.campaigns.index', [
            'iCountPlanned' => $iCountPlanned,
            'iCountSent' => $iCountSent,
            'success' => Session::get('success')
        ]);
    }

    public function jsonPlanned()
    {
        $oDB = DB::table('campaigns')
            ->select(
                'id',
                'name',
                'current_segment',
                'current_profile_codes',
                'current_agreement',
                'current_expiration_date',
                'scheduled_at',
                'created_at',
                'updated_at',
                DB::RAW('(SELECT COUNT(id) FROM campaign_customers AS cc WHERE cc.campaign_id = campaigns.id) AS count_customers') 
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
                // Current Expiration Date

                if ($o->current_agreement == 'Flexibel contract') {
                    switch (Auth::user()->date_format) {
                        case 'Y-m-d':
                            $current_expiration_date = '3000-01-01';
                            break;
                        case 'd-m-Y':
                            $current_expiration_date = '01-01-3000';
                            break;
                    }
                } else {
                    $current_expiration_date = date(Auth::user()->date_format, strtotime($o->current_expiration_date));
                }

                $aRows[] = [
                    'id' => $o->id,
                    'name' => $o->name,
                    'current_segment' => $o->current_segment,
                    'current_agreement' => $o->current_agreement,
                    'current_expiration_date' => $current_expiration_date,
                    'count_customers' => $o->count_customers,
                    'scheduled_at' => ($o->scheduled_at ? date(Auth::user()->date_format.' H:i', strtotime($o->scheduled_at)) : ''),
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

    public function jsonSent()
    {
        $oDB = DB::table('campaigns')
            ->select(
                'id',
                'name',
                'current_segment',
                'current_agreement',
                'current_expiration_date',
                'created_at',
                'updated_at',
                DB::RAW('(SELECT COUNT(id) FROM campaign_customers AS cc WHERE cc.campaign_id = campaigns.id) AS count_customers') 
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
                // Current Expiration Date

                if ($o->current_agreement == 'Flexibel contract') {
                    switch (Auth::user()->date_format) {
                        case 'Y-m-d':
                            $current_expiration_date = '3000-01-01';
                            break;
                        case 'd-m-Y':
                            $current_expiration_date = '01-01-3000';
                            break;
                    }
                } else {
                    $current_expiration_date = date(Auth::user()->date_format, strtotime($o->current_expiration_date));
                }

                $aRows[] = [
                    'id' => $o->id,
                    'name' => $o->name,
                    'current_segment' => $o->current_segment,
                    'current_agreement' => $o->current_agreement,
                    'current_expiration_date' => $current_expiration_date,
                    'count_customers' => $o->count_customers,
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

    public function add(Request $request)
    {
        $request->flash();

        $aData = [];

        $aErrors = [];

        // Labels
        $aCurrentLabels = [
            'Hezelaer Energy' => 'Hezelaer Energy',
            // 'VvE Energie Select' => 'VvE Energie Select' 
        ];

        // Auto renewal
        $aCurrentAutoRenewals = [
            null => '',
            'Y' => __('Yes'),
            'N' => __('No') 
        ];

        // Holding
        $aCurrentHoldings = [
            null => '',
            'Y' => __('Yes'),
            'N' => __('No') 
        ];

        // Segments (current)
        $aCurrentSegments = [
            'Zakelijk' => __('Zakelijk'),
            'Consument' => __('Consument')
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
            'S' => __('Yes, selection'),
            'N' => __('No')
        ];

        $aCurrentAgents = DB::table('contractgegevens')->distinct()
            ->whereNotNull('category2')
            ->where('category2', '!=', '')
            ->where('category2', '!=', 'NA')
            ->orderby('category2')
            ->pluck('category2', 'category2')
        ;

        $aData['current_agents'] = $aCurrentAgents;

        // Agreements (current)
        $aCurrentAgreements = [
            'Vast contract' => 'Vast contract',
            // 'Flexibel contract' => 'Flexibel contract', // Ingangsdatum op basis van 1-1-3000 ?
        ];

        // Expiration Date

        $aCurrentExpirationDate = [
            null => ''
        ];

        for ($i = date('Y') + 1; $i < date('Y') + 5; $i++) {
            $aCurrentExpirationDate['01-01-'.$i] = __('t/m').' '.'1-1-'.$i;
        }

        // Agreements (new)
        $aNewAgreements = [
            'Vast contract' => 'Vast contract',
            'Flexibel contract' => 'Flexibel contract',
        ];

        // Term Offers (new)
        $aNewTermOffers = [
            '+1 week' => '1 week',
            '+2 week' => '2 weken',
            '+3 week' => '3 weken'
        ];

        // Percentages (new)
        $aNewPercentages = [
            10 => '10 %',
            15 => '15 %',
            20 => '20 %',
            25 => '25 %',
        ];

        if ($request->isMethod('post')) {
            // Under An Agent

            $aData['current_agents'] = [];

            if (Input::get('current_under_an_agent') == 'S') {
                $aData['current_agents'] = Input::get('current_agents');
            }

            $aMessages = [
                'name.required' => sprintf(__('%s is required.'), __('Name')),
                'current_label.required' => sprintf(__('%s is required.'), __('Label')),
                'current_segment.required' => sprintf(__('%s is required.'), __('Segment')),
                'current_agreement.required' => sprintf(__('%s is required.'), __('Agreement')),
                'new_agreement.required' => sprintf(__('%s is required.'), __('Agreement')),
                'new_term_offer.required' => sprintf(__('%s is required.'), __('Term Offer')),
                'new_percentage.required' => sprintf(__('%s is required.'), __('Percentage')),
            ];

            $oValidator = Validator::make(Input::all(), [
                'name' => 'required',
                'current_label' => 'required',
                'current_segment' => 'required',
                'current_agreement' => 'required',
                'new_agreement' => 'required',
                'new_term_offer' => 'required',
                'new_percentage' => 'required',
            ], $aMessages);

            if ($oValidator->fails()) {
                $aErrors = $oValidator->errors();
            }

            if (count($aErrors) == 0) {
                if (Input::get('current_agreement') == 'Vast contract'){
                    if (!Input::get('current_expiration_date')){
                        $oValidator->getMessageBag()->add('current_expiration_date', sprintf(__('%s is required.'), __('Expiration Date')));
                        $aErrors = $oValidator->errors();
                    }
                }
            }

            if (count($aErrors) == 0) {

                // Current Expiration Date

                if (Input::get('current_agreement') == 'Vast contract'){
                    $sCurrentExpirationDate = Input::get('current_expiration_date');
                }

                if (Input::get('current_agreement') == 'Flexibel contract'){
                    $sCurrentExpirationDate = date('Y-01-01', strtotime('+1 YEAR'));
                }

                // Deals

                $oDB = DB::table('contractgegevens')
                    ->select(
                        'client_name',
                        'client_code',
                        'ean',
                        'code',
                        'super_contract_number',
                        'syu_normal',
                        'syu_low',
                        'end_agreement',
                        'email_factuur',
                        'email_meter',
                        'email_commercieel',
                        'telnr_commercieel',
                        'aanhef_commercieel',
                        'fadr_street',
                        'fadr_nr',
                        'fadr_nr_conn',
                        'fadr_zip',
                        'fadr_city',
                        'cadr_street',
                        'cadr_nr',
                        'cadr_nr_conn',
                        'cadr_zip',
                        'cadr_city',
                        'vastrecht',
                        'auto_renewal',
                        'iban',
                        'accountmanager',
                        'klantsegment',
                        'category1',
                        'category2',
                        'category3',
                        'consument',
                        'price_normal',
                        'price_low'
                    )
                ;

                // Label

                if (Input::get('current_label') == 'Hezelaer Energy') {
                    $oDB->whereIn('category1', ['1. Hezelaer', '1.Hezelaer']);
                }

                if (Input::get('current_label') == 'VvE Energie Select') {
                    $oDB->where('category1', '=', '1. VvE Energie Select');
                }

                // Auto renewal

                if (Input::get('current_auto_renewal') == 'Y') {
                    $oDB->where('auto_renewal', '=', 'TRUE');
                }

                if (Input::get('current_auto_renewal') == 'N') {
                    $oDB->where('auto_renewal', '=', 'FALSE');
                }

                // Segment

                if (Input::get('current_segment') == 'Zakelijk') {
                    $oDB->where('consument', '=', 'FALSE');
                }

                if (Input::get('current_segment') == 'Consument') {
                    $oDB->where('consument', '=', 'TRUE');
                }

                // Under an agent

                if (Input::get('current_under_an_agent') == 'Y') {
                    $oDB->where(function($query) {
                        $query->whereNotNull('category2')
                            ->where('category2', '!=', '')
                            ->where('category2', '!=', 'NA')
                        ;
                    });
                }

                if (Input::get('current_under_an_agent') == 'S') {
                    $oDB->whereIn('category2', Input::get('current_agents'));
                }

                if (Input::get('current_under_an_agent') == 'N') {
                    $oDB->where(function($query) {
                        $query->whereNull('category2')
                            ->orWhere('category2', '=', '')
                            ->orWhere('category2', '=', 'NA')
                        ;
                    });
                }

                // In a group

                if (Input::get('current_in_a_group') == 'Y') {
                    $oDB->where(function($query) {
                        $query->whereNotNull('category3')
                            ->where('category3', '!=', '')
                            ->where('category3', '!=', 'NA')
                        ;
                    });
                }

                if (Input::get('current_in_a_group') == 'N') {
                    $oDB->where(function($query) {
                        $query->whereNull('category3')
                            ->orWhere('category3', '=', '')
                            ->orWhere('category3', '=', 'NA')
                        ;
                    });
                }

                // Agreement

                if (Input::get('current_agreement') == 'Vast contract') {
                    $oDB->where('end_agreement', '!=', '3000-01-01');

                    // Expiration Date
                    $oDB->where('end_agreement', '<=', date('Y-m-d', strtotime(Input::get('current_expiration_date'))));
                }

                if (Input::get('current_agreement') == 'Flexibel contract') {
                    $oDB->where('end_agreement', '=', '3000-01-01');
                }

                if ($oDB->count() == 0) {
                    $aErrors['deals'] = __('There are no contracts were found.');
                }
            }

            if (count($aErrors) == 0) {
                // Current Deals
                $aCurrentDeals = $oDB->get();

                // Holding

                $aCurrentHoldings = $this->getHoldings();

                foreach ($aCurrentDeals as $k => $v) {
                    if (Input::get('current_holding') == 'Y') {
                        if (!array_key_exists($v->email_commercieel, $aCurrentHoldings)) {
                            unset($aCurrentDeals[$k]);
                        }
                    }
                    if (Input::get('current_holding') == 'N') {
                        if (array_key_exists($v->email_commercieel, $aCurrentHoldings)) {
                            unset($aCurrentDeals[$k]);
                        }
                    }
                }

                // Profile Codes

                $aProfileCodes = array_pluck($aCurrentDeals, 'code');
                $aProfileCodes = array_unique($aProfileCodes);
                sort($aProfileCodes);

                // Prices

                $iYear = date('Y', strtotime($sCurrentExpirationDate));

                $aPrices = [];

                foreach ($aProfileCodes as $sCode) {
                    $a = ModelPrice::getCampaignPrices($sCode, $sCurrentExpirationDate);

                    if (count($a) == 0) {
                        $aErrors['prices'] = sprintf(__('There are no prices available for profile code \'%s\'.'), $sCode);
                    } elseif(!array_key_exists(1, $a)) {
                        $aErrors['prices'] = sprintf(__('There are no prices available for profile code \'%s\' with year \'%s\'.'), $sCode, ($iYear+1));
                    } elseif(!array_key_exists(2, $a)) {
                        $aErrors['prices'] = sprintf(__('There are no prices available for profile code \'%s\' with year \'%s\'.'), $sCode, ($iYear+2));
                    } elseif(!array_key_exists(3, $a)) {
                        $aErrors['prices'] = sprintf(__('There are no prices available for profile code \'%s\' with year \'%s\'.'), $sCode, ($iYear+3));
                    }
                    else {
                        $aPrices[$sCode] = $a;
                    }
                }
            }

            if (count($aErrors) == 0) {
                // Agents

                $sAgents = null;

                if (Input::get('current_under_an_agent') == 'S') {
                    $sAgents = implode(',', Input::get('current_agents'));
                }

                // Campaign

                $oCampaign = new ModelCampaign();

                $oCampaign->name = Input::get('name');
                $oCampaign->current_label = Input::get('current_label');
                $oCampaign->current_auto_renewal = Input::get('current_auto_renewal');
                $oCampaign->current_holding = Input::get('current_holding');
                $oCampaign->current_segment = Input::get('current_segment');
                $oCampaign->current_in_a_group = Input::get('current_in_a_group');
                $oCampaign->current_under_an_agent = Input::get('current_under_an_agent');
                $oCampaign->current_agents = $sAgents;
                $oCampaign->current_profile_codes = implode(',', $aProfileCodes);
                $oCampaign->current_agreement = Input::get('current_agreement');
                $oCampaign->current_expiration_date = date('Y-m-d', strtotime($sCurrentExpirationDate));
                $oCampaign->new_agreement = Input::get('new_agreement');
                $oCampaign->new_term_offer = Input::get('new_term_offer');
                $oCampaign->new_percentage = Input::get('new_percentage');
                $oCampaign->new_percentage_after_term_offer = Input::get('new_percentage_after_term_offer');

                $oCampaign->save();

                // Campaign -> Prices

                foreach ($aPrices as $code => $v) {
                    foreach($v as $years => $aPrice) {
                        $oCampaignPrice = new ModelCampaignPrice();

                        $oCampaignPrice->campaign_id = $oCampaign->id;
                        $oCampaignPrice->date_start = $aPrice['date_start'];
                        $oCampaignPrice->date_end = $aPrice['date_end'];
                        $oCampaignPrice->years = $years;
                        $oCampaignPrice->rate = $aPrice['rate'];
                        $oCampaignPrice->code = $code;
                        $oCampaignPrice->price_normal = (array_key_exists('normal', $aPrice) ? $aPrice['normal'] : 0);
                        $oCampaignPrice->price_low = (array_key_exists('low', $aPrice) ? $aPrice['low'] : 0);
                        $oCampaignPrice->price_enkel = (array_key_exists('enkel', $aPrice) ? $aPrice['enkel'] : 0);
                        $oCampaignPrice->type = $oCampaignPrice->determineType();
                        $oCampaignPrice->calculation = $oCampaignPrice->determineCalculation();

                        $oCampaignPrice->save();

                        $aCampaignPrices[$code][$years] = [
                            'date_start' => $aPrice['date_start'],
                            'date_end' => $aPrice['date_end'],
                            'years' => $years,
                            'rate' => $aPrice['rate'],
                            'code' => $aPrice['code'],
                            'normal' => $oCampaignPrice->price_normal,
                            'enkel' => $oCampaignPrice->price_enkel,
                            'low' => $oCampaignPrice->price_low,
                            'type' => $oCampaignPrice->type,
                            'calculation' => $oCampaignPrice->calculation,
                            'percentage' => $oCampaign->new_percentage
                        ];
                    }
                }

                // Campaign -> Customers
                $aCampaignCustomers = [];

                // Campaign Customers : Prices
                $aCampaignCustomersPrices = [];

                foreach ($aCurrentDeals as $o) {
                    // Campaign -> Customers

                    $campaign_customer_id = null;

                    if (!in_array($o->client_code, $aCampaignCustomers)) {
                        $oCampaignCustomer = new ModelCampaignCustomer();

                        $oCampaignCustomer->campaign_id = $oCampaign->id;
                        $oCampaignCustomer->client_name = $o->client_name;
                        $oCampaignCustomer->client_code = $o->client_code;
                        $oCampaignCustomer->email_factuur = $o->email_factuur;
                        $oCampaignCustomer->email_meter = $o->email_meter;
                        $oCampaignCustomer->email_commercieel = $o->email_commercieel;
                        $oCampaignCustomer->telnr_commercieel = $o->telnr_commercieel;
                        $oCampaignCustomer->aanhef_commercieel = $o->aanhef_commercieel;
                        $oCampaignCustomer->fadr_street = $o->fadr_street;
                        $oCampaignCustomer->fadr_nr = $o->fadr_nr;
                        $oCampaignCustomer->fadr_nr_conn = $o->fadr_nr_conn;
                        $oCampaignCustomer->fadr_zip = $o->fadr_zip;
                        $oCampaignCustomer->fadr_city = $o->fadr_city;
                        $oCampaignCustomer->auto_renewal = $o->auto_renewal;
                        $oCampaignCustomer->iban = $o->iban;
                        $oCampaignCustomer->accountmanager = $o->accountmanager;
                        $oCampaignCustomer->klantsegment = $o->klantsegment;
                        $oCampaignCustomer->category1 = $o->category1;
                        $oCampaignCustomer->category2 = $o->category2;
                        $oCampaignCustomer->category3 = $o->category3;
                        $oCampaignCustomer->consument = $o->consument;
                        $oCampaignCustomer->token = sha1(openssl_random_pseudo_bytes(32));
                        $oCampaignCustomer->active = 1;
                        $oCampaignCustomer->status = ModelCampaignCustomer::STATUS_PLANNED;

                        $oCampaignCustomer->save();

                        $campaign_customer_id = $oCampaignCustomer->id;

                        $aCampaignCustomers[$campaign_customer_id] = $o->client_code;
                    } else {
                        $campaign_customer_id = array_search($o->client_code, $aCampaignCustomers);
                    }

                    // Deal

                    $oDeal = new ModelDeal;

                    $oDeal->campaign_id = $oCampaign->id;
                    $oDeal->campaign_customer_id = $campaign_customer_id;
                    $oDeal->ean = $o->ean;
                    $oDeal->super_contract_number = $o->super_contract_number;
                    $oDeal->code = $o->code;
                    $oDeal->cadr_street = $o->cadr_street;
                    $oDeal->cadr_nr = $o->cadr_nr;
                    $oDeal->cadr_nr_conn = $o->cadr_nr_conn;
                    $oDeal->cadr_zip = $o->cadr_zip;
                    $oDeal->cadr_city = $o->cadr_city;
                    $oDeal->syu_normal = $o->syu_normal;
                    $oDeal->syu_low = $o->syu_low;
                    $oDeal->end_agreement = $o->end_agreement;
                    $oDeal->vastrecht = $o->vastrecht;
                    $oDeal->new_vastrecht = ModelDeal::NEW_VASTRECHT;
                    $oDeal->price_normal = $o->price_normal;
                    $oDeal->price_low = $o->price_low;
                    $oDeal->estimate_price_1_year = $oDeal->calculateCosts($aCampaignPrices[$o->code][1]);
                    $oDeal->estimate_saving_1_year = $oDeal->calculateSaving($aCampaignPrices[$o->code][1]);
                    $oDeal->estimate_price_2_year = $oDeal->calculateCosts($aCampaignPrices[$o->code][2]);
                    $oDeal->estimate_saving_2_year = $oDeal->calculateSaving($aCampaignPrices[$o->code][1]);
                    $oDeal->estimate_price_3_year = $oDeal->calculateCosts($aCampaignPrices[$o->code][3]);
                    $oDeal->estimate_saving_3_year = $oDeal->calculateSaving($aCampaignPrices[$o->code][3]);
                    $oDeal->type = $aCampaignPrices[$o->code][1]['type'];
                    $oDeal->calculation = $aCampaignPrices[$o->code][1]['calculation'];
                    $oDeal->has_saving = (max([$oDeal->estimate_saving_1_year, $oDeal->estimate_saving_2_year, $oDeal->estimate_saving_3_year]) > 0 ? 1 : 0);

                    $oDeal->save();

                    // Campaign Customers : Prices

                    if (!array_key_exists($campaign_customer_id, $aCampaignCustomersPrices)) {
                        $aCampaignCustomersPrices[$campaign_customer_id] = [
                            'estimate_price_1_year' => 0,
                            'estimate_saving_1_year' => 0,
                            'estimate_price_2_year' => 0,
                            'estimate_saving_2_year' => 0,
                            'estimate_price_3_year' => 0,
                            'estimate_saving_3_year' => 0
                        ];
                    }

                    $aCampaignCustomersPrices[$campaign_customer_id]['estimate_price_1_year'] += $oDeal->estimate_price_1_year;
                    $aCampaignCustomersPrices[$campaign_customer_id]['estimate_saving_1_year'] += $oDeal->estimate_saving_1_year;
                    $aCampaignCustomersPrices[$campaign_customer_id]['estimate_price_2_year'] += $oDeal->estimate_price_2_year;
                    $aCampaignCustomersPrices[$campaign_customer_id]['estimate_saving_2_year'] += $oDeal->estimate_saving_2_year;
                    $aCampaignCustomersPrices[$campaign_customer_id]['estimate_price_3_year'] += $oDeal->estimate_price_3_year;
                    $aCampaignCustomersPrices[$campaign_customer_id]['estimate_saving_3_year'] += $oDeal->estimate_saving_3_year;
                }

                if (count($aCampaignCustomersPrices) > 0) {
                    foreach ($aCampaignCustomersPrices as $campaign_customer_id => $v) {
                        DB::table('campaign_customers')
                            ->where('id', $campaign_customer_id)
                            ->update([
                                'has_saving' => (($v['estimate_saving_3_year'] / 3) > ModelCampaignCustomer::HAS_SAVING_PRICE ? 1 : 0),
                                'estimate_price_1_year' => $v['estimate_price_1_year'],
                                'estimate_saving_1_year' => $v['estimate_saving_1_year'],
                                'estimate_price_2_year' => $v['estimate_price_2_year'],
                                'estimate_saving_2_year' => $v['estimate_saving_2_year'],
                                'estimate_price_3_year' => $v['estimate_price_3_year'],
                                'estimate_saving_3_year' => $v['estimate_saving_3_year'],
                            ])
                        ;
                    }
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

        return View::make('content.campaigns.add', [
            'aData' => $aData,
            'aCurrentLabels' => $aCurrentLabels,
            'aCurrentAutoRenewals' => $aCurrentAutoRenewals,
            'aCurrentHoldings' => $aCurrentHoldings,
            'aCurrentAgents' => $aCurrentAgents,
            'aCurrentSegments' => $aCurrentSegments,
            'aCurrentInAGroup' => $aCurrentInAGroup,
            'aCurrentUnderAnAgent' => $aCurrentUnderAnAgent,
            'aCurrentAgreements' => $aCurrentAgreements,
            'aCurrentExpirationDate' => $aCurrentExpirationDate,
            'aNewAgreements' => $aNewAgreements,
            'aNewTermOffers' => $aNewTermOffers,
            'aNewPercentages' => $aNewPercentages,
        ])->withErrors($aErrors);
    }

    public function edit(Request $request, $id)
    {
        $oCampaign = new ModelCampaign();

        $oCampaign = $oCampaign->findOrFail($id);

        if ($oCampaign->status == ModelCampaign::STATUS_SENT) {
             App::abort(500, 'The status is equal to "sent" therefore the campaign cannot be modified.');
        }

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

        $oCampaign = $oCampaign->findOrFail($id);

        $aResponse = [
            'status' => 'OK',
            'alert' => sprintf(
                __('The %s "%s" has been deleted.'),
                __('campaign'),
                '<em>'.e($oCampaign->name).'</em>'
            ),
        ];

        $oCampaign->delete();

        DB::table('campaign_prices')->where('campaign_id', '=', $id)->delete();

        DB::table('deals')->where('campaign_id', '=', $id)->delete();

        return response()->json($aResponse);
    }

    public function csv(Request $request, $id)
    {
        $a = DB::table('campaign_customers AS cc')
            ->select(
                'cc.campaign_id',
                'cc.client_name',
                'cc.client_code',
                'cc.email_commercieel',
                'cc.telnr_commercieel',
                'cc.aanhef_commercieel',
                'cc.fadr_street',
                'cc.fadr_nr',
                'cc.fadr_nr_conn',
                'cc.fadr_zip',
                'cc.fadr_city',
                'cc.auto_renewal',
                'cc.iban',
                'cc.accountmanager',
                'cc.klantsegment',
                'cc.category1',
                'cc.category2',
                'cc.category3',
                'cc.consument',
                'd.ean',
                'd.code',
                'd.cadr_street',
                'd.cadr_nr',
                'd.cadr_nr_conn',
                'd.cadr_zip',
                'd.cadr_city',
                'd.super_contract_number',
                'd.syu_normal',
                'd.syu_low',
                'd.end_agreement',
                'd.vastrecht',
                'd.price_normal',
                'd.price_low',
                'd.estimate_price_1_year',
                'd.estimate_saving_1_year',
                'd.estimate_price_2_year',
                'd.estimate_saving_2_year',
                'd.estimate_price_3_year',
                'd.estimate_saving_3_year'
            )
            ->join('deals AS d', function($join) {
                $join->on('d.campaign_id', '=', 'cc.campaign_id');
                $join->on('d.campaign_customer_id', '=', 'cc.id');
            })
            ->where('cc.campaign_id', '=', $id)
            ->get()
        ;

        $filename = 'campaign_customer_deals_'.$id.'_'.date('Y_m_d_H_i').'.csv';

        $pathToFile = storage_path('app').'/'.$filename;

        $fp = fopen($pathToFile, 'w');

        $fields = [
            'client_name',
            'client_code',
            'email_factuur',
            'email_meter',
            'email_commercieel',
            'telnr_commercieel',
            'aanhef_commercieel',
            'fadr_street',
            'fadr_nr',
            'fadr_nr_conn',
            'fadr_zip',
            'fadr_city',
            'cadr_street',
            'cadr_nr',
            'cadr_nr_conn',
            'cadr_zip',
            'cadr_city',
            'auto_renewal',
            'iban',
            'accountmanager',
            'klantsegment',
            'category1',
            'category2',
            'category3',
            'consument',
            'ean',
            'code',
            'super_contract_number',
            'syu_normal',
            'syu_low',
            'end_agreement',
            'vastrecht',
            'price_normal',
            'price_low',
            'estimate_price_1_year',
            'estimate_saving_1_year',
            'estimate_price_2_year',
            'estimate_saving_2_year',
            'estimate_price_3_year',
            'estimate_saving_3_year'
        ];

        fputcsv($fp, $fields);

        foreach ($a as $k => $v) {
            $fields = [
                'client_name' => $v->client_name,
                'client_code' => $v->client_code,
                'email_factuur' => $v->email_factuur,
                'email_meter' => $v->email_meter,
                'email_commercieel' => $v->email_commercieel,
                'telnr_commercieel' => $v->telnr_commercieel,
                'aanhef_commercieel' => $v->aanhef_commercieel,
                'fadr_street' => $v->fadr_street,
                'fadr_nr' => $v->fadr_nr,
                'fadr_nr_conn' => $v->fadr_nr_conn,
                'fadr_zip' => $v->fadr_zip,
                'fadr_city' => $v->fadr_city,
                'cadr_street' => $v->cadr_street,
                'cadr_nr' => $v->cadr_nr,
                'cadr_nr_conn' => $v->cadr_nr_conn,
                'cadr_zip' => $v->cadr_zip,
                'cadr_city' => $v->cadr_city,
                'auto_renewal' => $v->auto_renewal,
                'accountmanager' => $v->accountmanager,
                'klantsegment' => $v->klantsegment,
                'category1' => $v->category1,
                'category2' => $v->category2,
                'category3' => $v->category3,
                'consument' => $v->consument,
                'ean' => $v->ean,
                'code' => $v->code,
                'super_contract_number' => $v->super_contract_number,
                'syu_normal' => $v->syu_normal,
                'syu_low' => $v->syu_low,
                'end_agreement' => $v->end_agreement,
                'vastrecht' => $v->vastrecht,
                'price_normal' => $v->price_normal,
                'price_low' => $v->price_low,
                'estimate_price_1_year' => $v->estimate_price_1_year,
                'estimate_saving_1_year' => $v->estimate_saving_1_year,
                'estimate_price_2_year' => $v->estimate_price_2_year,
                'estimate_saving_2_year' => $v->estimate_saving_2_year,
                'estimate_price_3_year' => $v->estimate_price_3_year,
                'estimate_saving_3_year' => $v->estimate_saving_3_year
            ];

            fputcsv($fp, $fields);
        }

        fclose($fp);

        return response()->download($pathToFile)->deleteFileAfterSend(true);
    }
}