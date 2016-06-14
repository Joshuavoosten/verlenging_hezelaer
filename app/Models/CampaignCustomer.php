<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CampaignCustomer extends Model
{
    const STATUS_PLANNED = 1;
    const STATUS_INVITE_EMAIL_SCHEDULED = 2;
    const STATUS_INVITE_EMAIL_QUEUED = 3;
    const STATUS_INVITE_EMAIL_SENT = 4;
    const STATUS_FORM_REQUESTED = 5;
    const STATUS_FORM_SAVED = 6;

    // If the estimate saving is larger then this amount, the deal is saved with has_saving = 1.
    const HAS_SAVING_PRICE = 50;

    protected $table = 'campaign_customers';

    /**
     * Invoice Address Formatter
     *
     * @return string
     */
    public function fadrFormatter() {
        return implode(' ', [$this->fadr_street, $this->fadr_nr.' '.$this->fadr_nr_conn, $this->fadr_zip, $this->fadr_city]);
    }

    /**
     * Generate an unique reference.
     *
     * @return string $sKenmerk
     */
    public function kenmerkFormatter() {
        $sKenmerk = null;

        $sKenmerk .= 'V';
        $sKenmerk .= date('Ym', strtotime($this->created_at));
        $sKenmerk .= '_';
        $sKenmerk .= str_pad($this->id, 4, 0, STR_PAD_LEFT);

        return $sKenmerk;
    }

    /**
     * Remove Salutation
     * 
     * @param string $sValue
     * @return string $sReturn
     */
    public static function removeSalutation($sValue) {
        $sReturn =  $sValue;

        $sReturn = str_replace('Geachte heer', '', $sReturn);
        $sReturn = str_replace('Geachte mevrouw', '', $sReturn);
        $sReturn = str_replace('Beste', '', $sReturn);

        return $sReturn;
    }

    /**
     * Status Formatter
     *
     * @param string $sStatus
     * @return mixed null | string
     */
    public static function statusFormatter($sStatus) {
        switch ($sStatus) {
            case self::STATUS_PLANNED:
                return null;
            case self::STATUS_INVITE_EMAIL_SCHEDULED:
                return __('Scheduled');
            case self::STATUS_INVITE_EMAIL_QUEUED:
                return __('Queued');
            case self::STATUS_INVITE_EMAIL_SENT:
                return __('Sent');
            case self::STATUS_FORM_REQUESTED:
                return __('Form Requested');
            case self::STATUS_FORM_SAVED:
                return __('Form Saved');
        }
    }
}
