<?php

namespace App\Jobs;

use App;
use App\Jobs\Job;
use App\Models\Campaign as ModelCampaign;
use App\Models\CampaignCustomer as ModelCampaignCustomer;
use Config;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Mail;

class CampaignCustomersInviteEmail extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $oCampaign;
    protected $oCampaignCustomer;

    /**
     * Create a new job instance.
     *
     * @param object $oCampaign
     * @param object $oCampaignCustomer
     * @return void
     */
    public function __construct(ModelCampaign $oCampaign, ModelCampaignCustomer $oCampaignCustomer)
    {
        $this->oCampaign = $oCampaign;
        $this->oCampaignCustomer = $oCampaignCustomer;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Campaign

        $oCampaign = $this->oCampaign;

        // Campaign -> Customer
        $oCampaignCustomer = $this->oCampaignCustomer;

        // Debug CLI

        if (App::runningInConsole()) {
            echo sprintf('Job: Campaign (%s) %s - Customer (%s) %s : %s', $oCampaign->campaign_id, $oCampaign->name, $oCampaignCustomer->id, $oCampaignCustomer->client_name, $oCampaignCustomer->client_code) . "\n"; 
        }

        // Mail

        $from_address = Config::get('mail.from.address');
        $from_name = Config::get('mail.from.name');

        // $to_address = $oCampaignCustomer->email_commercieel;
        // $to_address = 'david@floro.nl'; // @todo
        // $to_address = 'test@shifft.com';
        $to_address = 'bjorn@hezelaer.nl';
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

        Mail::send('emails.test', [], function($m) use ($from_address, $from_name, $to_address, $to_name, $aMergeVars, $sTemplate, $subject) {
            $headers = $m->getHeaders();
            $headers->addTextHeader('X-MC-MergeVars', json_encode($aMergeVars));
            $headers->addTextHeader('X-MC-Template', $sTemplate);
            $m->to($to_address, $to_name)->subject($subject);
        });

        // Campaign

        $oCampaign->status = ModelCampaign::STATUS_SENT;
        $oCampaign->save();

        // Campaign -> Customer

        $oCampaignCustomer->status = ModelCampaignCustomer::STATUS_INVITE_EMAIL_SENT;
        $oCampaignCustomer->save();
    }

}
