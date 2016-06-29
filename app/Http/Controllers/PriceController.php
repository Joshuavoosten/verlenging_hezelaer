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

class PriceController extends Controller
{
    public function index()
    {
        // Rates

        $aRates = DB::table('prices')->distinct()->orderby('rate')->pluck('rate', 'rate');
        $aRates = [null => null] + $aRates;

        // Profile Codes

        $aProfileCodes = DB::table('price_codes')->distinct()->orderby('code')->pluck('code', 'code');
        $aProfileCodes = [null => null] + $aProfileCodes;

        // Date Modified

        $sDateModified = DB::table('prices')
            ->max('created_at')
        ;

        $sDateModified = date(Auth::user()->date_format.' H:i', strtotime($sDateModified));

        // View

        return view('content.prices.index', [
            'aProfileCodes' => $aProfileCodes,
            'aRates' => $aRates,
            'sDateModified' => $sDateModified,
            'success' => Session::get('success')
        ]);
    }

    public function json()
    {
        $oDB = DB::table('prices AS p')
            ->select(
                'p.id',
                'p.date_start',
                'p.date_end',
                'p.rate',
                'p.codes',
                'p.price',
                'p.created_at',
                'p.updated_at'
            )
            ->join('price_codes AS pc', 'pc.price_id', '=', 'p.id')
        ;

        if (Input::get('rate')) {
            $oDB->where('p.rate', '=', Input::get('rate'));
        }

        if (Input::get('code')) {
            $oDB->where('pc.code', '=', Input::get('code'));
        }

        if (Input::get('search')) {
            $oDB->whereRaw('MATCH(p.rate) AGAINST(? IN BOOLEAN MODE)', [Input::get('search')]);
            $oDB->orWhere('pc.code', '=', Input::get('search'));
        }

        $total = $oDB->count(DB::Raw('DISTINCT p.id'));

        $oDB->groupBy('p.id');

        if (Input::get('sort') && Input::get('order')) {
            switch(Input::get('sort')) {
                case 'date_start':
                    $oDB->orderBy('p.date_start', Input::get('order'));
                    break;
                case 'date_end':
                    $oDB->orderBy('p.date_end', Input::get('order'));
                    break;
                case 'rate':
                    $oDB->orderBy('p.rate', Input::get('order'));
                    break;
                case 'price':
                    $oDB->orderBy('p.price', Input::get('order'));
                    break;
                case 'created_at':
                    $oDB->orderBy('p.created_at', Input::get('order'));
                    break;
                case 'updated_at':
                    $oDB->orderBy('p.updated_at', Input::get('order'));
                    break;
            }
        } else {
            $oDB->orderBy('p.date_start');
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
                    'date_start' => ($o->date_start ? date(Auth::user()->date_format, strtotime($o->date_start)) : ''),
                    'date_end' => ($o->date_end ? date(Auth::user()->date_format, strtotime($o->date_end)) : ''),
                    'rate' => $o->rate,
                    'codes' => str_replace(' ', ',', $o->codes),
                    'price' => number_format($o->price,2,',','.'),
                    'created_at' => ($o->created_at ? date(Auth::user()->date_format.' H:i', strtotime($o->created_at)) : ''),
                    'updated_at' => ($o->updated_at ? date(Auth::user()->date_format.' H:i', strtotime($o->updated_at)) : ''),
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
                if (strpos($entry, 'huidige_prijzen') === false) {
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
     * @return $aPrices
     */
    private function getPricesFromCsv($sFilename) {
        $aPrices = [];

        $aColumnIndex = [];

        $aRequiredColumns = [
            'startdatum',
            'einddatum',
            'tarief',
            'profielen',
            'prijs'
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
                    $aPrices[] = [
                        'date_start' => $data[$aColumnIndex['startdatum']],
                        'date_end' => $data[$aColumnIndex['einddatum']],
                        'rate' => $data[$aColumnIndex['tarief']],
                        'codes' => $data[$aColumnIndex['profielen']],
                        'price' => $data[$aColumnIndex['prijs']],
                        'created_at' => date('Y-m-d H:i:s')
                    ];
                }
                $i++;
            }
            fclose($handle);
        }

        return $aPrices;
    }

    /**
     * @param array $aPrices
     * @return boolean true
     */
    private function importPrices($aPrices) {
        ini_set('memory_limit', -1);
        ignore_user_abort(true);
        set_time_limit(0);
        
        // Prices

        DB::table('prices')->truncate();

        DB::connection()->disableQueryLog();

        DB::transaction(function() use($aPrices) {
            foreach ($aPrices as $aPrice) {
                DB::table('prices')->insert($aPrice);
            }
        });

        DB::connection()->enableQueryLog();

        // Profile Codes
        DB::table('price_codes')->truncate();

        // Convert column prices.codes to price_codes table.
        Artisan::call('prices:codes');

        return true;
    }

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
                $aPrices = $this->getPricesFromCsv(Input::get('file'));

                if (count($aPrices) > 0) {
                    $this->importPrices($aPrices);

                    return Redirect::to('/prices')
                       ->with('success',  __('The prices has been imported.'))
                    ;
                }
            }
        }

        // View

        return View::make('content.prices.import', [
            'aFiles' => $aFiles,
        ])->withErrors($aErrors);
    }
}
