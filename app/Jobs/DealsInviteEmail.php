<?php

namespace App\Jobs;

use App;
use App\Jobs\Job;
use App\Models\Campaign as ModelCampaign;
use App\Models\Deal as ModelDeal;
use Config;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Mail;

class DealsInviteEmail extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $oCampaign;
    protected $oDeal;

    /**
     * Create a new job instance.
     *
     * @param object $oCampaign
     * @param object $oDeal
     * @return void
     */
    public function __construct(ModelCampaign $oCampaign, ModelDeal $oDeal)
    {
        $this->oCampaign = $oCampaign;
        $this->oDeal = $oDeal;
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

        // Deal
        $oDeal = $this->oDeal;

        // Debug CLI
        if (App::runningInConsole()) {
            echo sprintf('Job: Campaign (%s) %s - Deal (%s) %s <%s>', $oCampaign->id, $oCampaign->name, $oDeal->id, $oDeal->client_name, $oDeal->email_commercieel) . "\n";
        }

        // Mail

        $from_address = Config::get('mail.from.address');
        $from_name = Config::get('mail.from.name');

        // $to_address = $oDeal->email_commercieel;
        $to_address = 'david@floro.nl'; // @todo
        $to_name = $oDeal->client_name;

        $subject = 'Verleng nu uw leveringsovereenkomst(en)';

        $besparing = 0.00; // @todo

        $aMergeVars = [
            'client_name' => $oDeal->client_name,
            'client_code' => $oDeal->client_code,
            'aanhef_commercieel' => $oDeal->aanhef_commercieel,
            'besparing' => $besparing,
            'accountmanager' => $oDeal->accountmanager,
            'token' => $oDeal->token
        ];

        // $sTemplate = 'Contractverlenging met besparing';
        $sTemplate = 'Contractverlenging zonder besparing';

        Mail::send('emails.test', [], function($m) use ($from_address, $from_name, $to_address, $to_name, $aMergeVars, $sTemplate, $subject) {
            $headers = $m->getHeaders();
            $headers->addTextHeader('X-MC-MergeVars', json_encode($aMergeVars));
            $headers->addTextHeader('X-MC-Template', $sTemplate);
            $m->to($to_address, $to_name)->subject($subject);
        });

        // Save

        $oDeal->status = ModelDeal::STATUS_INVITE_EMAIL_SENT;
        $oDeal->save();

        $oCampaign->status = ModelCampaign::STATUS_SENT;
        $oCampaign->save();
    }
}
