<?php

namespace App\Models;

use DB;
use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    const STATUS_PLANNED = 1;
    const STATUS_SENT = 2;

    protected $table = 'campaigns';

    public static function countPlanned() {
        return DB::table('campaigns')->where('status', '=', self::STATUS_PLANNED)->count();
    }

    public static function countSent() {
        return DB::table('campaigns')->where('status', '=', self::STATUS_SENT)->count();
    }

    /**
     * Convert textual datetime to human readable string.
     *
     * @param string $sNewTermOffer
     * @return string
     */
    public static function newTermOfferFormatter($sNewTermOffer) {
        switch ($sNewTermOffer) {
            case '+1 week':
                return sprintf(__('%s week'), 1);
            case '+2 week':
                return sprintf(__('%s weeks'), 2);
            case '+3 week':
                return sprintf(__('%s weeks'), 3);
            default:
                return $sNewTermOffer;
        }
    }

    /**
     * Fetch client code(s) from customers with a current offer in other campaigns that has not expired, ignoring the current campaign.
     *
     * @param int $iCampaignId
     * @return array $aClientCodes
     */
    public static function getCustomersWithCurrentOffer($iCampaignId) {
        $aClientCodes = [];

        // Campaign

        $oCampaign = new Campaign;
        $oCampaign = $oCampaign->find($iCampaignId);

        // Client Codes

        $oDB = DB::table('campaigns AS c')
            ->select(
                DB::raw('DISTINCT cc.client_code'),
                DB::raw('DATE_ADD(GREATEST(c.scheduled_at, c.created_at), INTERVAL '.strtoupper($oCampaign->new_term_offer).') AS date_expired')
            )
            ->join('campaign_customers AS cc', 'cc.campaign_id', '=', 'c.id')
            ->where('c.id', '!=', $iCampaignId)
            ->where('cc.active', '=', 1)
            ->having('date_expired', '>=', date('Y-m-d 00:00:00'))
        ;

        $aClientCodes = $oDB->pluck('client_code');

        return $aClientCodes;
    }

    public function countCustomers() {
        return DB::table('campaign_customers')->where('campaign_id', '=', $this->id)->count();
    }
}
