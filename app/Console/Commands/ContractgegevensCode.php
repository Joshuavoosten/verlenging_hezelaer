<?php

namespace App\Console\Commands;

use DB;
use Illuminate\Console\Command;

class ContractgegevensCode extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'contractgegevens:code';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Convert column contractgegevens.code "EContinu" to "E3A".';

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
        DB::table('contractgegevens')
           ->where('code', '=', 'EContinu')
           ->update(['status' => 'E3A'])
       ;
    }
}