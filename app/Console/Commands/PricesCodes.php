<?php

namespace App\Console\Commands;

use DB;
use Illuminate\Console\Command;

class PricesCodes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'prices:codes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Convert column prices.codes to price_codes table.';

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
        DB::table('price_codes')->truncate();

        $a = DB::table('prices')->pluck('codes', 'id');

        if (!is_array($a)){
            return false;
        }

        if (count($a) == 0) {
            return false;
        }

        foreach ($a as $price_id => $codes) {
            $aCodes = explode(' ', $codes);

            if (!is_array($aCodes)){
                continue;
            }

            if (count($aCodes) == 0) {
                continue;
            }

            $aPriceCodes = [];

            foreach ($aCodes as $code) {
                $aPriceCodes[] = [
                    'price_id' => $price_id,
                    'code' => $code,
                    'created_at' => date('Y-m-d H:i:s')
                ];
            }

            DB::table('price_codes')->insert($aPriceCodes);
        }
    }
}