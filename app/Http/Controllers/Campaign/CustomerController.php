<?php

namespace App\Http\Controllers\Campaign;

use App;
use App\Http\Controllers\Controller;
use App\Models\Deal as ModelDeal;
use App\Models\Campaign as ModelCampaign;
use App\Models\CampaignCustomer as ModelCampaignCustomer;
use App\Models\Form as ModelForm;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Validator;

class CustomerController extends Controller
{
    // Show Profile Codes
    const SHOW_PROFILE_CODES = false;

    public function jsonEstimateSaving(Request $request, $token) {
        // Campaign -> Customer

        $oCampaignCustomer = new ModelCampaignCustomer();

        $oCampaignCustomer = ModelCampaignCustomer::where('token', '=', $token)->first();

        if (!$oCampaignCustomer) {
            App::abort('500', 'Token Error');
        }

        if (!Input::get('years')) {
            App::abort('500', 'Parameter Error');
        }

        // Estimate Saving

        $fEstimateSaving = 0;

        switch (Input::get('years')) {
            case 1:
                $fEstimateSaving = $oCampaignCustomer->estimate_saving_1_year / 1;
                break;
            case 2:
                $fEstimateSaving = $oCampaignCustomer->estimate_saving_2_year / 2;
                break;
            case 3:
                $fEstimateSaving = $oCampaignCustomer->estimate_saving_3_year / 3;
                break;
        }

        // Renewable Resource

        if (Input::get('renewable_resource')) {

            // Total Annual Consumption
 
            $oDB = DB::table('deals')
                ->select(DB::raw('sum(syu_normal + syu_low) AS total_annual_consumption'))
                ->where('campaign_customer_id', '=', $oCampaignCustomer->id)
                ->where('type', '=', ModelDeal::TYPE_ELEKTRICITY)
            ;

            $o = $oDB->first();

            $fTotalAnnualConsumption = $o->total_annual_consumption;

            switch (Input::get('renewable_resource')) {
                case 1:
                    // 100% produced by dutch windmills: Total annual consumption * 0,0030 EUR/kWh
                    $fEstimateSaving += ($fTotalAnnualConsumption * 0.0030);
                    break;
                case 3:
                    // Not renewable: Total annual consumption * -0,0005 EUR/kWh
                    $fEstimateSaving -= ($fTotalAnnualConsumption * 0.0005);
                    break;
            }
        }

        if ($fEstimateSaving < 0) {
            $fEstimateSaving = 0;
        }

        $aResponse = [
            'estimate_saving' => $fEstimateSaving,
            'estimate_saving_format' => number_format($fEstimateSaving,2,',','.')
        ];

        return response()->json($aResponse);
    }

    public function extend(Request $request, $token) {
        // Default Contract Period
        $iYears = 3;

        // Campaign -> Customer

        $oCampaignCustomer = new ModelCampaignCustomer();

        $oCampaignCustomer = ModelCampaignCustomer::where('token', '=', $token)->first();

        if (!$oCampaignCustomer) {
            return view('content.campaigns.customer.extend.token_error');
        }

        // Check if form already has been saved.

        if ($oCampaignCustomer->status == ModelCampaignCustomer::STATUS_FORM_SAVED) {
            return view('content.campaigns.customer.extend.status_form_saved', [
                'oCampaignCustomer' => $oCampaignCustomer
            ]);
        }

        // Check if the form has been requested for the first time.

        if ($oCampaignCustomer->status == ModelCampaignCustomer::STATUS_INVITE_EMAIL_SENT) {
            $oCampaignCustomer->status = ModelCampaignCustomer::STATUS_FORM_REQUESTED;
            $oCampaignCustomer->save();
        }

        // Campaign

        $oCampaign = new ModelCampaign();

        $oCampaign = $oCampaign->find($oCampaignCustomer->campaign_id);

        // Check if the campaign has expired.
        $bExpired = (time() > strtotime($oCampaign->new_term_offer, strtotime($oCampaign->scheduled_at)) ? true : false);

        // Has Elektricity or Gas

        $hasElektricity = false;
        $hasGas = false;

        // Deals

        $aDeals = [];

        $oDB = DB::table('deals')
            ->select(
                'id'
            )
            ->where('campaign_customer_id', '=', $oCampaignCustomer->id)
        ;

        $a = $oDB->get();

        foreach ($a as $o) {
            $oDeal = new ModelDeal();
            $oDeal = $oDeal->find($o->id);
            $aDeals[] = $oDeal;

            if ($oDeal->type == ModelDeal::TYPE_ELEKTRICITY) {
                $hasElektricity = true;
            }

            if ($oDeal->type == ModelDeal::TYPE_GAS) {
                $hasGas = true;
            }
        }

        uasort($aDeals, function($a, $b) {
            return $b->cadrChecksum() - $a->cadrChecksum();
        });

        // Profile Codes

        $aProfileCodes = array_pluck($aDeals, 'code');

        // Campaign Prices

        $aCampaignPrices = [];

        $oDB = DB::table('campaign_prices')
            ->select(
                'id',
                'date_start',
                'date_end',
                'years',
                'rate',
                'code',
                'price_normal',
                'price_low',
                'price_enkel',
                'type',
                'calculation'
            )
            ->where('campaign_id', '=', $oCampaign->id)
            ->whereIn('code', $aProfileCodes)
        ;

        $a = $oDB->get();

        foreach ($a as $o) {
            $aCampaignPrices[$o->code][$o->years] = $o;
        }

        // Data
        $aData = [
            'form_end_agreement' => 3,
            'form_renewable_resource' => 2, // 100% opgewekt door windmolens
            'form_email_billing' => $oCampaignCustomer->email_factuur,
            'form_email_contract_extension' => $oCampaignCustomer->email_commercieel,
            'form_email_meter_readings' => $oCampaignCustomer->email_meter,
            'form_payment' => null,
            'form_fadr_street' => $oCampaignCustomer->fadr_street,
            'form_fadr_nr' => $oCampaignCustomer->fadr_nr,
            'form_fadr_nr_conn' => $oCampaignCustomer->fadr_nr_conn,
            'form_fadr_zip' => $oCampaignCustomer->fadr_zip,
            'form_fadr_city' => $oCampaignCustomer->fadr_city,
            'form_iban' => $oCampaignCustomer->iban,
            'form_terms_and_conditions_1' => null,
            'form_terms_and_conditions_2' => null,
            'form_terms_and_conditions_3' => null,
            'form_sign_name' => null,
            'form_sign_on_behalf_of' => null,
            'form_sign_function' => null,
            'form_permission' => null
        ];

        // Errors
        $aErrors = [];

        // Post Request

        if ($request->isMethod('post')) {
            $aData = array_merge($aData, Input::all());

            // Validation

            $aMessages = [
                'form_end_agreement.required' => 'Looptijd is verplicht.',
                'form_renewable_resource.required' => 'Hernieuwbare bron is verplicht.',
                'form_email_billing.required' => 'E-mail adres voor facturatie is verplicht.',
                'form_email_billing.email' => 'E-mail adres voor facturatie is onjuist.',
                'form_email_billing.required' => 'E-mail adres voor contractverlenging is verplicht.',
                'form_email_billing.email' => 'E-mail adres voor contractverlenging is onjuist.',
                'form_email_billing.required' => 'E-mail adres voor opgave meterstanden is verplicht.',
                'form_email_billing.email' => 'E-mail adres voor opgave meterstanden is onjuist.',
                'form_payment.email' => 'Betalingsmethode is verplicht.',
                'form_fadr_street.required' => 'Straat is verplicht.',
                'form_fadr_nr.required' => 'Huisnummer is verplicht.',
                'form_fadr_zip' => 'Postcode is verplicht.',
                'form_fadr_city' => 'Stad is verplicht.',
                'form_iban' => 'IBAN is verplicht.',
                'form_terms_and_conditions_1' => 'Dit is een verplicht veld.',
                'form_terms_and_conditions_2' => 'Dit is een verplicht veld.',
                'form_terms_and_conditions_3' => 'Dit is een verplicht veld.',
                'form_sign_name' => 'Naam is verplicht.',
                'form_sign_on_behalf_of' => 'Namens is verplicht.',
                'form_sign_function' => 'Functie is verplicht.',
                'form_permission' => 'Dit is een verplicht veld.'
            ];

            $aRules = [
               'form_end_agreement' => 'required',
               'form_email_billing' => 'required|email',
               'form_email_contract_extension' => 'required|email',
               'form_email_meter_readings' => 'required|email',
               'form_payment' => 'required',
               'form_fadr_street' => 'required',
               'form_fadr_nr' => 'required',
               'form_fadr_zip' => 'required',
               'form_fadr_city' => 'required',
               'form_terms_and_conditions_1' => 'required',
               'form_terms_and_conditions_2' => 'required',
               'form_terms_and_conditions_3' => 'required',
               'form_sign_name' => 'required',
               'form_sign_on_behalf_of' => 'required',
               'form_sign_function' => 'required',
               'form_permission' => 'required'
            ];

            if ($hasElektricity) {
                $aRules = array_merge($aRules, [
                    'form_renewable_resource' => 'required'
                ]);
            }

            if (Input::get('form_payment') == ModelForm::PAYMENT_AUTOMATIC_COLLECTION) {
                $aRules = array_merge($aRules, [
                    'form_iban' => 'required'
                ]);
            }

            $oValidator = Validator::make(Input::all(), $aRules, $aMessages);

            if ($oValidator->fails()) {
                $aErrors = $oValidator->errors();
            }

            if (Input::get('form_iban')) {
                if (!validateIban(Input::get('form_iban'))) {
                    $oValidator->getMessageBag()->add('form_iban', 'IBAN is onjuist');
                }
            }

            // Process

            if (count($aErrors) == 0) {
                $oForm = new ModelForm();

                $oForm->campaign_customer_id = $oCampaignCustomer->id;
                $oForm->end_agreement = Input::get('form_end_agreement');
                $oForm->renewable_resource = Input::get('form_renewable_resource');
                $oForm->email_billing = Input::get('form_email_billing');
                $oForm->email_contract_extension = Input::get('form_email_contract_extension');
                $oForm->email_meter_readings = Input::get('form_email_meter_readings');
                $oForm->payment = Input::get('form_payment');
                $oForm->fadr_street = Input::get('form_fadr_street');
                $oForm->fadr_nr = Input::get('form_fadr_nr');
                $oForm->fadr_nr_conn = Input::get('form_fadr_nr_conn');
                $oForm->fadr_zip = Input::get('form_fadr_zip');
                $oForm->fadr_city = Input::get('form_fadr_city');
                $oForm->iban = Input::get('form_iban');
                $oForm->sign_name = Input::get('form_sign_name');
                $oForm->sign_on_behalf_of = Input::get('form_sign_on_behalf_of');
                $oForm->sign_function = Input::get('form_sign_function');
                $oForm->created_at = date('Y-m-d H:i:s');

                $oForm->save();

                // Campaign -> Customer

                $oCampaignCustomer->status = ModelCampaignCustomer::STATUS_FORM_SAVED;

                $oCampaignCustomer->save();

                return view('content.campaigns.customer.extend.status_form_saved', [
                    'oCampaignCustomer' => $oCampaignCustomer
                ]);
            }
        }

        return view('content.campaigns.customer.extend', [
            'aData' => $aData,
            'aDeals' => $aDeals,
            'aCampaignPrices' => $aCampaignPrices,
            'bDisplayProfileCodes' => self::SHOW_PROFILE_CODES,
            'hasElektricity' => $hasElektricity,
            'hasGas' => $hasGas,
            'iYears' => $iYears,
            'oCampaign' => $oCampaign,
            'oCampaignCustomer' => $oCampaignCustomer,
            'token' => $token
        ])->withErrors($aErrors);
    }

    public function active(Request $request, $id)
    {
        $oCampaignCustomer = new ModelCampaignCustomer();

        $oCampaignCustomer = $oCampaignCustomer->find($id);

        if (!$oCampaignCustomer) {
            App::abort(404, 'Campaign Customer Not Found.');
        }

        // Check if the status is planned or scheduled.

        if (!in_array($oCampaignCustomer->status, [ModelCampaignCustomer::STATUS_PLANNED, ModelCampaignCustomer::STATUS_INVITE_EMAIL_SCHEDULED])) {
            App::abort(500, 'The status is not equal to "planned" or "scheduled" therefore the active state cannot be modified.');
        }

        if (Input::get('active') == 0) {
            $oCampaignCustomer->status = ModelCampaignCustomer::STATUS_PLANNED;
        }

        $oCampaignCustomer->active = Input::get('active');

        $oCampaignCustomer->save();
    }

    public function toggle(Request $request, $campaign_id)
    {
        DB::table('campaign_customers')
            ->where('campaign_id', $campaign_id)
            ->where('has_saving', Input::get('has_saving'))
            ->where('status', '=', ModelCampaignCustomer::STATUS_PLANNED)
            ->update([
                'active' => Input::get('active')
            ])
        ;
    }
}