<?php

namespace App\Http\Controllers;

use App\Models\CampaignCustomer as ModelCampaignCustomer;
use Auth;
use DB;
use Illuminate\Support\Facades\Input;

class DealController extends Controller
{
    public function index()
    {
        return view('content.deals.index');
    }

    public function json()
    {
        $oDB = DB::table('campaign_customers AS cc')
            ->select(
                'cc.id',
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
                'cc.accountmanager',
                'cc.klantsegment',
                'cc.category1',
                'cc.category2',
                'cc.category3',
                'cc.consument',
                'cc.estimate_price_1_year AS estimate_total_price_1_year',
                'cc.estimate_saving_1_year AS estimate_total_saving_1_year',
                'cc.estimate_price_2_year AS estimate_total_price_2_year',
                'cc.estimate_saving_2_year AS estimate_total_saving_2_year',
                'cc.estimate_price_3_year AS estimate_total_price_3_year',
                'cc.estimate_saving_3_year AS estimate_total_saving_3_year',
                'cc.status',
                'c.name AS campaign',
                'd.id AS deal_id',
                'd.ean',
                'd.code',
                'd.super_contract_number',
                'd.syu_normal',
                'd.syu_low',
                'd.end_agreement',
                'd.cadr_street',
                'd.cadr_nr',
                'd.cadr_nr_conn',
                'd.cadr_zip',
                'd.cadr_city',
                'd.vastrecht',
                'd.new_vastrecht',
                'd.price_normal',
                'd.price_low',
                'd.estimate_price_1_year',
                'd.estimate_saving_1_year',
                'd.estimate_price_2_year',
                'd.estimate_saving_2_year',
                'd.estimate_price_3_year',
                'd.estimate_saving_3_year'
            )
            ->join('campaigns AS c', 'c.id', '=', 'cc.campaign_id')
            ->join('deals AS d', 'd.campaign_customer_id', '=', 'cc.id')
            ->whereIn('cc.status', [
                ModelCampaignCustomer::STATUS_INVITE_EMAIL_SENT,
                ModelCampaignCustomer::STATUS_FORM_REQUESTED,
                ModelCampaignCustomer::STATUS_FORM_SAVED,
            ])
            ->orderBy('cc.client_name')
            ->orderBy('cc.client_code')
            ->orderBy('d.super_contract_number')
            ->orderBy('d.code')
        ;

        if (Input::get('sort') && Input::get('order')) {
            $aSort = [
                'client_name' => 'cc.client_name',
                'client_code' => 'cc.client_code',
                'email_commercieel' => 'cc.email_commercieel',
                'telnr_commercieel' => 'cc.telnr_commercieel',
                'aanhef_commercieel' => 'cc.aanhef_commercieel',
                'fadr_street' => 'cc.fadr_street',
                'fadr_nr' => 'cc.fadr_nr',
                'fadr_nr_conn' => 'cc.fadr_nr_conn',
                'fadr_zip' => 'cc.fadr_zip',
                'fadr_city' => 'cc.fadr_city',
                'auto_renewal' => 'cc.auto_renewal',
                'accountmanager' => 'cc.accountmanager',
                'klantsegment' => 'cc.klantsegment',
                'category1' => 'cc.category1',
                'category2' => 'cc.category2',
                'category3' => 'cc.category3',
                'consument' => 'cc.consument',
                'estimate_total_price_1_year' => 'cc.estimate_price_1_year',
                'estimate_total_saving_1_year' => 'cc.estimate_saving_1_year',
                'estimate_total_price_2_year' => 'cc.estimate_price_2_year',
                'estimate_total_saving_2_year' => 'cc.estimate_saving_2_year',
                'estimate_total_price_3_year' => 'cc.estimate_price_3_year',
                'estimate_total_saving_3_year' => 'cc.estimate_saving_3_year',
                'status' => 'cc.status',
                'campaign' => 'c.name',
                'ean' => 'd.ean',
                'code' => 'd.code',
                'super_contract_number' => 'd.super_contract_number',
                'syu_normal' => 'd.syu_normal',
                'syu_low' => 'd.syu_low',
                'end_agreement' => 'd.end_agreement',
                'cadr_street' => 'd.cadr_street',
                'cadr_nr' => 'd.cadr_nr',
                'cadr_nr_conn' => 'd.cadr_nr_conn',
                'cadr_zip' => 'd.cadr_zip',
                'cadr_city' => 'd.cadr_city',
                'vastrecht' => 'd.vastrecht',
                'new_vastrecht' => 'd.new_vastrecht',
                'price_normal' => 'd.price_normal',
                'price_low' => 'd.price_low',
                'estimate_price_1_year' => 'd.estimate_price_1_year',
                'estimate_saving_1_year' => 'd.estimate_saving_1_year',
                'estimate_price_2_year' => 'd.estimate_price_2_year',
                'estimate_saving_2_year' => 'd.estimate_saving_2_year',
                'estimate_price_3_year' => 'd.estimate_price_3_year',
                'estimate_saving_3_year' => 'd.estimate_saving_3_year'
            ];
            if (array_key_exists(Input::get('sort'), $aSort)) {
                $oDB->orderBy($aSort[Input::get('sort')], Input::get('order'));
            }
        } else {
            $oDB->orderBy('cc.client_name');
        }

        $total = $oDB->count(DB::raw('DISTINCT cc.client_code'));

        $offset = Input::get('offset', 0);

        $limit = Input::get('limit', 25);

        $oDB->skip($offset)->take($limit);

        $a = $oDB->get();

        $aRows = [];

        $aClientCodes = [];

        if (count($a) > 0) {
            $i = 0;
            foreach ($a as $o) {
                $rowstyle = null;

                if ($o->status == ModelCampaignCustomer::STATUS_FORM_SAVED) {
                    $rowstyle = 'success';
                }

                if (!array_key_exists($o->client_code, $aClientCodes)) {
                    $aClientCodes[$o->client_code] = $i;

                    $aRows[$i] = [
                        'id' => $o->id,
                        'campaign' => $o->campaign,
                        'client_name' => $o->client_name,
                        'client_code' => $o->client_code,
                        'email_commercieel' => $o->email_commercieel,
                        'telnr_commercieel' => $o->telnr_commercieel,
                        'aanhef_commercieel' => $o->aanhef_commercieel,
                        'fadr_street' => $o->fadr_street,
                        'fadr_nr' => $o->fadr_nr,
                        'fadr_nr_conn' => $o->fadr_nr_conn,
                        'fadr_zip' => $o->fadr_zip,
                        'fadr_city' => $o->fadr_city,
                        'auto_renewal' => $o->auto_renewal,
                        'accountmanager' => $o->accountmanager,
                        'klantsegment' => $o->klantsegment,
                        'category1' => $o->category1,
                        'category2' => $o->category2,
                        'category3' => $o->category3,
                        'consument' => $o->consument,
                        'estimate_total_price_1_year' => number_format($o->estimate_total_price_1_year,2,',','.'),
                        'estimate_total_saving_1_year' => number_format($o->estimate_total_saving_1_year,2,',','.'),
                        'estimate_total_price_2_year' => number_format($o->estimate_total_price_2_year,2,',','.'),
                        'estimate_total_saving_2_year' => number_format($o->estimate_total_saving_2_year,2,',','.'),
                        'estimate_total_price_3_year' => number_format($o->estimate_total_price_3_year,2,',','.'),
                        'estimate_total_saving_3_year' => number_format($o->estimate_total_saving_3_year,2,',','.'),
                        'status' => $o->status,
                        'status_format' => ModelCampaignCustomer::statusFormatter($o->status),
                        'rowstyle' => $rowstyle
                    ];

                    $i++;
                }

                $j = $aClientCodes[$o->client_code];

                $aRows[$j]['deals'][] = [
                    'deal_id' => $o->deal_id,
                    'ean' => $o->ean,
                    'code' => $o->code,
                    'super_contract_number' => $o->super_contract_number,
                    'syu_normal' => ($o->syu_normal != 0 ? round($o->syu_normal) : ''),
                    'syu_low' => ($o->syu_low != 0 ? round($o->syu_low) : ''),
                    'end_agreement' => ($o->end_agreement ? date(Auth::user()->date_format, strtotime($o->end_agreement)) : ''),
                    'cadr_street' => $o->cadr_street,
                    'cadr_nr' => $o->cadr_nr,
                    'cadr_nr_conn' => $o->cadr_nr_conn,
                    'cadr_zip' => $o->cadr_zip,
                    'cadr_city' => $o->cadr_city,
                    'vastrecht' => number_format($o->vastrecht,5,',','.'),
                    'new_vastrecht' => number_format($o->new_vastrecht,5,',','.'),
                    'price_normal' => ($o->price_normal != '0.0000' ? number_format($o->price_normal,4,',','.') : ''),
                    'price_low' => ($o->price_low != '0.0000' ? number_format($o->price_low,4,',','.') : ''),
                    'estimate_price_1_year' => number_format($o->estimate_price_1_year,2,',','.'),
                    'estimate_saving_1_year' => number_format($o->estimate_price_1_year,2,',','.'),
                    'estimate_price_2_year' => number_format($o->estimate_price_2_year,2,',','.'),
                    'estimate_saving_2_year' => number_format($o->estimate_price_2_year,2,',','.'),
                    'estimate_price_3_year' => number_format($o->estimate_price_3_year,2,',','.'),
                    'estimate_saving_3_year' => number_format($o->estimate_price_3_year,2,',','.')
                ];
            }
        }

        $aResponse = [
            'total' => $total,
            'rows' => $aRows,
        ];

        return response()->json($aResponse);
    }

    public function csv() {
        $a = DB::table('campaign_customers AS cc')
            ->select(
                'cc.id',
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
                'cc.accountmanager',
                'cc.klantsegment',
                'cc.category1',
                'cc.category2',
                'cc.category3',
                'cc.consument',
                'cc.estimate_price_1_year AS estimate_total_price_1_year',
                'cc.estimate_saving_1_year AS estimate_total_saving_1_year',
                'cc.estimate_price_2_year AS estimate_total_price_2_year',
                'cc.estimate_saving_2_year AS estimate_total_saving_2_year',
                'cc.estimate_price_3_year AS estimate_total_price_3_year',
                'cc.estimate_saving_3_year AS estimate_total_saving_3_year',
                'cc.status',
                'c.name AS campaign',
                'd.ean',
                'd.code',
                'd.super_contract_number',
                'd.syu_normal',
                'd.syu_low',
                'd.end_agreement',
                'd.cadr_street',
                'd.cadr_nr',
                'd.cadr_nr_conn',
                'd.cadr_zip',
                'd.cadr_city',
                'd.vastrecht',
                'd.new_vastrecht',
                'd.price_normal',
                'd.price_low',
                'd.estimate_price_1_year',
                'd.estimate_saving_1_year',
                'd.estimate_price_2_year',
                'd.estimate_saving_2_year',
                'd.estimate_price_3_year',
                'd.estimate_saving_3_year'
            )
            ->join('campaigns AS c', 'c.id', '=', 'cc.campaign_id')
            ->join('deals AS d', 'd.campaign_customer_id', '=', 'cc.id')
            ->whereIn('cc.status', [
                ModelCampaignCustomer::STATUS_INVITE_EMAIL_SENT,
                ModelCampaignCustomer::STATUS_FORM_REQUESTED,
                ModelCampaignCustomer::STATUS_FORM_SAVED,
            ])
            ->get()
        ;

        $filename = 'deals_'.date('Y_m_d_H_i').'.csv';

        $pathToFile = storage_path('app').'/'.$filename;

        $fp = fopen($pathToFile, 'w');

        $fields = [
            'client_name',
            'client_code',
            'ean',
            'code',
            'super_contract_number',
            'syu_normal',
            'syu_low',
            'end_agreement',
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
            'new_vastrecht',
            'auto_renewal',
            'accountmanager',
            'klantsegment',
            'category1',
            'category2',
            'category3',
            'consument',
            'price_normal' => number_format($o->price_normal,4,',','.'),
            'price_low' => number_format($o->price_low,4,',','.')
        ];

        fputcsv($fp, $fields);

        foreach ($a as $k => $v) {
            $fields = [
                'client_name' => $v->client_name,
                'client_code' => $v->client_code,
                'ean' => $v->ean,
                'code' => $v->code,
                'super_contract_number' => $v->super_contract_number,
                'syu_normal' => round($v->syu_normal),
                'syu_low' => round($v->syu_low),
                'end_agreement' => ($v->end_agreement ? date(Auth::user()->date_format, strtotime($v->end_agreement)) : ''),
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
                'vastrecht' => number_format($v->vastrecht,5,',','.'),
                'new_vastrecht' => number_format($v->new_vastrecht,5,',','.'),
                'auto_renewal' => $v->auto_renewal,
                'accountmanager' => $v->accountmanager,
                'klantsegment' => $v->klantsegment,
                'category1' => $v->category1,
                'category2' => $v->category2,
                'category3' => $v->category3,
                'consument' => $v->consument,
                'price_normal' => number_format($v->price_normal,4,',','.'),
                'price_low' => number_format($v->price_low,4,',','.')
            ];

            fputcsv($fp, $fields);
        }

        fclose($fp);

        return response()->download($pathToFile)->deleteFileAfterSend(true);
    }
}
