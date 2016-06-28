<?php

namespace App\Console\Commands;

use Artisan;
use DB;
use Illuminate\Console\Command;

class PricesImport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'prices:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import prices from the latest CSV.';

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
     * @return string $sFilename
     */
    private function getFileFromStorage() {
        $sFilename = null;

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
            $sFilename = current($aEntries)['filename'];
        }

        return $sFilename;
    }

    /**
     * @param $sFilename
     * @return $aPrices
     */
    private function getPricesFromCsv($sFilename) {
        $aPrices = [];
        $aColumnIndex = [];
        $aRequiredColumns = ['startdatum', 'einddatum', 'tarief', 'profielen', 'prijs'];
        $aPrices = [];
        $sDirectory = storage_path('ftp');

        if (($handle = fopen($sDirectory.'/'.$sFilename, "r")) !== false) {
            $i = 0;
            while (($data = fgetcsv($handle, 1000, ",")) !== false) {
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
        // Prices

        DB::table('prices')->truncate();

        DB::table('prices')->insert($aPrices);

        // Profile Codes

        DB::table('price_codes')->truncate();

        Artisan::call('prices:codes');

        return true;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $sFilename = $this->getFileFromStorage();

        if (!$sFilename) {
            return false;
        }

        $aPrices = $this->getPricesFromCsv($sFilename);

        if (count($aPrices) == 0) {
            return false;
        }

        $this->importPrices($aPrices);

        return true;
    }
}