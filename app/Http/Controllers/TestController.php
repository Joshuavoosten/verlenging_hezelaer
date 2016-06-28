<?php

namespace App\Http\Controllers;

use Config;
use Session;
use \App\Models\Price AS ModelPrice;
use \App\Models\Campaign as ModelCampaign;
use \App\Models\CampaignCustomer as ModelCampaignCustomer;
use Mail;

class TestController extends Controller
{
    public function index()
    {
        // Campaign

        $oCampaign = new ModelCampaign();
        $oCampaign = $oCampaign->find(1);

        // Campaign -> Customer
        $oCampaignCustomer = new ModelCampaignCustomer();
        $oCampaignCustomer = $oCampaignCustomer->find(6);

        // Mail

        $from_address = Config::get('mail.from.address');
        $from_name = Config::get('mail.from.name');

        // $to_address = $oDeal->email_commercieel;
        $to_address = 'david@floro.nl'; // @todo
        $to_name = $oCampaignCustomer->client_name;

        $subject = 'Verleng nu uw leveringsovereenkomst(en)';

        $aMergeVars = [
            'client_name' => $oCampaignCustomer->client_name,
            'client_code' => $oCampaignCustomer->client_code,
            'aanhef_commercieel' => $oCampaignCustomer->aanhef_commercieel,
            'besparing' => $oCampaignCustomer->estimate_saving_3_year,
            'accountmanager' => $oCampaignCustomer->accountmanager,
            'token' => $oCampaignCustomer->token
        ];

        if ($oCampaignCustomer->estimate_saving_3_year < ModelCampaignCustomer::HAS_SAVING_PRICE) {
            $sTemplate = 'Contractverlenging zonder besparing';
        } else {
            $sTemplate = 'Contractverlenging met besparing';
        }

        /*
        Mail::send('emails.test', [], function($m) use ($from_address, $from_name, $to_address, $to_name, $aMergeVars, $sTemplate, $subject) {
            $headers = $m->getHeaders();
            $headers->addTextHeader('X-MC-MergeVars', json_encode($aMergeVars));
            $headers->addTextHeader('X-MC-Template', $sTemplate);
            $m->to($to_address, $to_name)->subject($subject);
        });
        */

        // Campaign

        $oCampaign->status = ModelCampaign::STATUS_SENT;
        $oCampaign->save();

        // Campaign -> Customer

        $oCampaignCustomer->status = ModelCampaignCustomer::STATUS_INVITE_EMAIL_SENT;
        $oCampaignCustomer->save();

        echo 'done';
    }
}
