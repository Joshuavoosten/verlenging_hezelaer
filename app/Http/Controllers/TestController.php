<?php

namespace App\Http\Controllers;

use Session;
use \App\Models\Price AS ModelPrice;

class TestController extends Controller
{
    public function index()
    {
         ModelPrice::getCampaignPrices('E1A', '01-01-2017');
    }
}
