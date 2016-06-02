<?php

namespace App\Http\Controllers;

use App\Models\Campaign as ModelCampaign;
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

    public function extend(Request $request, $token) {
        // Deal

        $oDeal = new ModelDeal();

        $oDeal = ModelDeal::where('token', '=', $token)->firstOrFail();

        // @todo check status

        // The form has been requested.

        if ($oDeal->status == ModelDeal::STATUS_INVITE_EMAIL_SENT) {
            $oDeal->status = ModelDeal::STATUS_FORM_REQUESTED;
        }

        $oDeal->save();

        // Campaign

        $oCampaign = new ModelCampaign();

        $oCampaign = $oCampaign->find($oDeal->campaign_id);

        $aErrors = [];

        // Post Request

        if ($request->isMethod('post')) {
            // @todo
        }

        return view('content.deals.extend', [
            'oCampaign' => $oCampaign,
            'oDeal' => $oDeal,
            'token' => $token
        ]);
    }

}
