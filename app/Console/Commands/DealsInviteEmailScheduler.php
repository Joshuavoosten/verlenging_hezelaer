<?php

namespace App\Console\Commands;

use App;
use App\Models\Campaign as ModelCampaign;
use App\Models\Deal as ModelDeal;
use App\Jobs\DealsInviteEmail;
use DB;
use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;

class DealsInviteEmailScheduler extends Command
{
    use DispatchesJobs;
    
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deals:invite_email_scheduler';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deals Invite Email Scheduler';

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
                'd.id AS deal_id'
            )
            ->join('deals AS d', 'd.campaign_id', '=', 'c.id')
            ->where('c.status', '=', ModelCampaign::STATUS_PLANNED)
            ->where('c.scheduled', '=', 1)
            ->whereRaw('c.scheduled_at <= NOW()')
            ->where('d.status', '=', ModelDeal::STATUS_INVITE_EMAIL_SCHEDULED)
            ->where('d.active', '=', 1)
        ;

        if ($oDB->count() == 0) {
            echo 'There are no campaigns planned or deals scheduled.';
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

            // Deal

            $oDeal = new ModelDeal();
            $oDeal = $oDeal->find($o->deal_id);

            $oDeal->status = ModelDeal::STATUS_INVITE_EMAIL_QUEUED;
            $oDeal->save();

            // Job

            // Debug CLI
            if (App::runningInConsole()) {
                echo sprintf('Scheduler: Campaign (%s) %s - Deal (%s) %s <%s>', $o->campaign_id, $oCampaign->name, $o->deal_id, $oDeal->client_name, $oDeal->email_commercieel) . "\n"; 
            }

            $oJob = (new DealsInviteEmail($oCampaign, $oDeal))->onQueue('deals_invite_email');

            $this->dispatch($oJob);
        }
    }

}