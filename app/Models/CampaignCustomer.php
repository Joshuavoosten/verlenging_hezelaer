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

    protected $table = 'campaign_customers';

    /**
     * Connection Address Formatter
     *
     * @return string
     */
    public function cadrFormatter() {
        return implode(' ', [$this->cadr_street, $this->cadr_nr.' '.$this->cadr_nr_conn, $this->cadr_zip, $this->cadr_city]);
    }

    /**
     * Invoice Address Formatter
     *
     * @return string
     */
    public function fadrFormatter() {
        return implode(' ', [$this->fadr_street, $this->fadr_nr.' '.$this->fadr_nr_conn, $this->fadr_zip, $this->fadr_city]);
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
