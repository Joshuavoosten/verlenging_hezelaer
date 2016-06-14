<?php

namespace App\Console\Commands;

use App;
use App\Models\Campaign as ModelCampaign;
use App\Models\CampaignCustomer as ModelCampaignCustomer;
use App\Jobs\CampaignCustomersInviteEmail;
use DB;
use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;

class CampaignCustomersInviteEmailScheduler extends Command
{
    use DispatchesJobs;
    
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'campaign_customers:invite_email_scheduler';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Campaign Customers Invite Email Scheduler';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $oDB = DB::table('campaigns AS c')
            ->select(
                'c.id AS campaign_id',
                'cc.id AS campaign_customer_id'
            )
            ->join('campaign_customers AS cc', 'cc.campaign_id', '=', 'c.id')
            ->where('c.status', '=', ModelCampaign::STATUS_PLANNED)
            ->where('c.scheduled', '=', 1)
            ->whereRaw('c.scheduled_at <= NOW()')
            ->where('cc.status', '=', ModelCampaignCustomer::STATUS_INVITE_EMAIL_SCHEDULED)
            ->where('cc.active', '=', 1)
        ;

        if ($oDB->count() == 0) {
            echo 'There are no campaigns planned or customers scheduled.';
            return false;
        }

        $a = $oDB->get();

        $campaign_id = null;

        foreach ($a as $o) {
            // Campaign

            if ($campaign_id != $o->campaign_id) {
                $oCampaign = new ModelCampaign();
                $oCampaign = $oCampaign->find($o->campaign_id);
            }

            $campaign_id = $o->campaign_id;

            // Campaign -> Customer

            $oCampaignCustomer = new ModelCampaignCustomer();
            $oCampaignCustomer = $oCampaignCustomer->find($o->campaign_customer_id);

            $oCampaignCustomer->status = ModelCampaignCustomer::STATUS_INVITE_EMAIL_QUEUED;
            $oCampaignCustomer->save();

            // Debug CLI

            if (App::runningInConsole()) {
                echo sprintf('Scheduler: Campaign (%s) %s - Customer (%s) %s : %s', $o->campaign_id, $oCampaign->name, $o->campaign_customer_id, $oCampaignCustomer->client_name, $oCampaignCustomer->client_code) . "\n"; 
            }

            // Job

            $oJob = (new CampaignCustomersInviteEmail($oCampaign, $oCampaignCustomer))->onQueue('campaign_customers_invite_email');

            $this->dispatch($oJob);
        }
    }

}