<?php

namespace App\Http\Controllers;

use Artisan;
use Auth;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Session;
use Validator;
use View;
use Storage;

class ContractController extends Controller
{
    public function index()
    {
        // Date Modified

        $sDateModified = DB::table('contractgegevens')
            ->max('created_at')
        ;

        $sDateModified = date(Auth::user()->date_format.' H:i', strtotime($sDateModified));

        // View

        return view('content.contracts.index', [
            'sDateModified' => $sDateModified,
            'success' => Session::get('success')
        ]);
    }

    public function json()
    {
        $oDB = DB::table('contractgegevens')
            ->select(
                'id',
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
                'auto_renewal',
                'accountmanager',
                'klantsegment',
                'category1',
                'category2',
                'category3',
                'consument',
                'price_normal',
                'price_low'
            )
        ;

        $total = $oDB->count(DB::Raw('DISTINCT id'));

        if (Input::get('sort') && Input::get('order')) {
            $oDB->orderBy(Input::get('sort') , Input::get('order'));
        } else {
            $oDB->orderBy('client_name');
        }

        $offset = Input::get('offset', 0);

        $limit = Input::get('limit', 25);

        $oDB->skip($offset)->take($limit);

        $a = $oDB->get();

        $aRows = [];

        if (count($a) > 0) {
            foreach ($a as $o) {
                $aRows[] = [
                    'id' => $o->id,
                    'client_name' => $o->client_name,
                    'client_code' => $o->client_code,
                    'ean' => $o->ean,
                    'code' => $o->code,
                    'super_contract_number' => $o->super_contract_number,
                    'syu_normal' => ($o->syu_normal != 0 ? round($o->syu_normal) : null),
                    'syu_low' => ($o->syu_low != 0 ? round($o->syu_low) : null),
                    'end_agreement' => ($o->end_agreement ? date(Auth::user()->date_format, strtotime($o->end_agreement)) : ''),
                    'email_commercieel' => $o->email_commercieel,
                    'telnr_commercieel' => $o->telnr_commercieel,
                    'aanhef_commercieel' => $o->aanhef_commercieel,
                    'fadr_street' => $o->fadr_street,
                    'fadr_nr' => $o->fadr_nr,
                    'fadr_nr_conn' => $o->fadr_nr_conn,
                    'fadr_zip' => $o->fadr_zip,
                    'fadr_city' => $o->fadr_city,
                    'cadr_street' => $o->cadr_street,
                    'cadr_nr' => $o->cadr_nr,
                    'cadr_nr_conn' => $o->cadr_nr_conn,
                    'cadr_zip' => $o->cadr_zip,
                    'cadr_city' => $o->cadr_city,
                    'vastrecht' => number_format($o->vastrecht,5,',','.'),
                    'auto_renewal' => $o->auto_renewal,
                    'accountmanager' => $o->accountmanager,
                    'klantsegment' => $o->klantsegment,
                    'category1' => $o->category1,
                    'category2' => $o->category2,
                    'category3' => $o->category3,
                    'consument' => $o->consument,
                    'price_normal' => ($o->price_normal != '0.0000' ? number_format($o->price_normal,4,',','.') : null),
                    'price_low' => ($o->price_low != '0.0000' ? number_format($o->price_low,4,',','.') : null)
                ];
            }
        }

        // Response

        $aResponse = [
            'total' => $total,
            'rows' => $aRows,
        ];

        return response()->json($aResponse);
    }

    /**
     * @return $aFiles
     */
    private function getFilesFromStorage() {
        $aFiles = [];

        $sDirectory = storage_path('ftp');

        $aEntries = [];

        if ($handle = opendir($sDirectory)) {
            while (false !== ($entry = readdir($handle))) {
                if ($entry == '.' || $entry == '..') {
                    continue;
                }
                if (strpos($entry, 'active_contracts') === false) {
                    continue;
                }
                if (pathinfo($entry, PATHINFO_EXTENSION) != 'csv') {
                    continue;
                }
                $aEntries[] = [
                    'filename' => $entry,
                    'filemtime' => filemtime($sDirectory.'/'.$entry)
                ];
            }
        }

        if (count($aEntries) > 0) {
            uasort($aEntries, function($a, $b) {
                return $b['filemtime'] - $a['filemtime'];
            });
        }

        if (count($aEntries) > 0) {
            foreach($aEntries as $aEntry) {
                $sEntry = $aEntry['filename'];
                $aFiles[$sEntry] = $sEntry;
            }
        }

        return $aFiles;
    }

    /**
     * @param $sFilename
     * @return $aContracts
     */
    private function getContractsFromCsv($sFilename) {
        $aContracts = [];

        $aColumnIndex = [];

        $aRequiredColumns = [
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
            'auto_renewal',
            'accountmanager',
            'klantsegment',
            'category1',
            'category2',
            'category3',
            'consument',
            'price_normal',
            'price_low'
        ];

        $sDirectory = storage_path('ftp');

        if (($handle = fopen($sDirectory.'/'.$sFilename, "r")) !== false) {
            $i = 0;
            while (($data = fgetcsv($handle, 1000, ";")) !== false) {
                if ($i == 0) {
                    // Column Index
                    foreach ($data as $k => $v) {
                        if (in_array($v, $aRequiredColumns)) {
                            $aColumnIndex[$v] = $k;
                        }
                    }
                    if (count($aColumnIndex) != count($aRequiredColumns)) {
                        App::abort(500, 'Required Columns');
                    }
                } else {
                    $aContracts[] = [
                        'client_name' => $data[$aColumnIndex['client_name']],
                        'client_code' => $data[$aColumnIndex['client_code']],
                        'ean' => $data[$aColumnIndex['ean']],
                        'code' => $data[$aColumnIndex['code']],
                        'super_contract_number' => $data[$aColumnIndex['super_contract_number']],
                        'syu_normal' => $data[$aColumnIndex['syu_normal']],
                        'syu_low' => $data[$aColumnIndex['syu_low']],
                        'end_agreement' => $data[$aColumnIndex['end_agreement']],
                        'email_commercieel' => $data[$aColumnIndex['email_commercieel']],
                        'telnr_commercieel' => $data[$aColumnIndex['telnr_commercieel']],
                        'aanhef_commercieel' => $data[$aColumnIndex['aanhef_commercieel']],
                        'fadr_street' => $data[$aColumnIndex['fadr_street']],
                        'fadr_nr' => $data[$aColumnIndex['fadr_nr']],
                        'fadr_nr_conn' => $data[$aColumnIndex['fadr_nr_conn']],
                        'fadr_zip' => $data[$aColumnIndex['fadr_zip']],
                        'fadr_city' => $data[$aColumnIndex['fadr_city']],
                        'cadr_street' => $data[$aColumnIndex['cadr_street']],
                        'cadr_nr' => $data[$aColumnIndex['cadr_nr']],
                        'cadr_nr_conn' => $data[$aColumnIndex['cadr_nr_conn']],
                        'cadr_zip' => $data[$aColumnIndex['cadr_zip']],
                        'cadr_city' => $data[$aColumnIndex['cadr_city']],
                        'vastrecht' => $data[$aColumnIndex['vastrecht']],
                        'auto_renewal' => $data[$aColumnIndex['auto_renewal']],
                        'accountmanager' => $data[$aColumnIndex['accountmanager']],
                        'klantsegment' => $data[$aColumnIndex['klantsegment']],
                        'category1' => $data[$aColumnIndex['category1']],
                        'category2' => $data[$aColumnIndex['category2']],
                        'category3' => $data[$aColumnIndex['category3']],
                        'consument' => $data[$aColumnIndex['consument']],
                        'price_normal' => $data[$aColumnIndex['price_normal']],
                        'price_low' => $data[$aColumnIndex['price_low']],
                        'created_at' => date('Y-m-d H:i:s')
                    ];
                }
                $i++;
            }
            fclose($handle);
        }

        return $aContracts;
    }

    /**
     * @param array $aContracts
     * @return boolean true
     */
    private function importContracts($aContracts) {
        ini_set('memory_limit', -1);
        ignore_user_abort(true);
        set_time_limit(0);

        // Contracts

        DB::table('contractgegevens')->truncate();

        DB::connection()->disableQueryLog();

        DB::transaction(function() use($aContracts) {
            foreach ($aContracts as $aContract) {
                DB::table('contractgegevens')->insert($aContract);
            }
        });

        DB::connection()->enableQueryLog();

        // Convert column contractgegevens.code "EContinu" to "E3A".
        Artisan::call('contractgegevens:code');

        return true;
    }

    /**
     * @param object $request
     */
    public function import(Request $request) {
        $aErrors = [];

        $aFiles = $this->getFilesFromStorage();

        if ($request->isMethod('post')) {

            $aMessages = [
                'file.required' => sprintf(__('%s is required.'), __('File'))
            ];

            $oValidator = Validator::make(Input::all(), [
                'file' => 'required'
            ], $aMessages);

            if ($oValidator->fails()) {
                $aErrors = $oValidator->errors();
            }

            if (count($aErrors) == 0) {
                $aContracts = $this->getContractsFromCsv(Input::get('file'));

                if (count($aContracts) > 0) {
                    $this->importContracts($aContracts);

                    return Redirect::to('/contracts')
                       ->with('success',  __('The contracts has been imported.'))
                    ;
                }
            }
        }

        // View

        return View::make('content.contracts.import', [
            'aFiles' => $aFiles,
        ])->withErrors($aErrors);
    }
}
