<?php

namespace App\Http\Controllers;

use App\Models\Deal as ModelDeal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class DealController extends Controller
{
    public function active(Request $request, $id)
    {
        $oDeal = new ModelDeal();

        $oDeal = $oDeal->find($id);

        if (!$oDeal) {
            App::abort(404, 'Deal Not Found.');
        }

        if (!in_array($oDeal->status, [ModelDeal::STATUS_PLANNED, ModelDeal::STATUS_INVITE_EMAIL_SCHEDULED])) {
            App::abort(500, 'The status is not equal to "planned" or "scheduled" therefore the active state cannot be modified.');
        }

        if (Input::get('active') == 0) {
            $oDeal->status = ModelDeal::STATUS_PLANNED;
        }

        $oDeal->active = Input::get('active');

        $oDeal->save();
    }
}
