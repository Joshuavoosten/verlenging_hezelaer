<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use DB;
use Session;
use Validator;
use View;
use App\Models\Campaign as ModelCampaign;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;

class CampaignController extends Controller
{
    public function index()
    {
        $iCountPlannedCampaigns = 0; // @todo
        $iCountSentCampaigns = 0; // @todo

        return view('content.campaigns.index', [
            'iCountPlannedCampaigns' => $iCountPlannedCampaigns,
            'iCountSentCampaigns' => $iCountSentCampaigns,
            'success' => Session::get('success'),
        ]);
    }

    public function json()
    {
        $oDB = DB::table('campaigns')
            ->select(
                'id',
                'name',
                'created_at',
                'updated_at'
            )
        ;

        if (Input::get('search')) {
            $oDB->whereRaw('MATCH(name) AGAINST(? IN BOOLEAN MODE)', [Input::get('search')]);
        }

        if (Input::get('sort') && Input::get('order')) {
            $oDB->orderBy(Input::get('sort'), Input::get('order'));
        } else {
            $oDB->orderBy('name');
        }

        $total = $oDB->count();

        $offset = Input::get('offset', 0);

        $limit = Input::get('limit', 25);

        $oDB->skip($offset)->take($limit);

        $a = $oDB->get();

        $aRows = [];

        if (count($a) > 0) {
            foreach ($a as $o) {
                $aRows[] = [
                    'id' => $o->id,
                    'name' => $o->name,
                    'created_at' => ($o->created_at ? date('d-m-Y H:i', strtotime($o->created_at)) : ''),
                    'updated_at' => ($o->updated_at ? date('d-m-Y H:i', strtotime($o->updated_at)) : ''),
                ];
            }
        }

        $aResponse = [
            'total' => $total,
            'rows' => $aRows,
        ];

        return response()->json($aResponse);
    }

    public function add(Request $request)
    {
        $request->flash();

        $aErrors = [];

        // Segments
        $aSegments = [null, 'Zakelijk'];

        if ($request->isMethod('post')) {
            $aMessages = [
                'name.required' => sprintf(__('%s is required.'), __('Name')),
            ];

            $oValidator = Validator::make(Input::all(), [
                'name' => 'required',
            ], $aMessages);

            if ($oValidator->fails()) {
                $aErrors = $oValidator->errors();
            }

            if (count($aErrors) == 0) {
                $password = Input::get('password');

                $oCampaign = new ModelCampaign();

                $oCampaign->name = Input::get('name');

                $oCampaign->save();

                return Redirect::to('/campaigns')
                   ->with('success', sprintf(
                           __('The %s "%s" has been added.'),
                           __('campaign'),
                           '<em>'.e(Input::get('name')).'</em>'
                       )
                   )
               ;
            }
        }

        return View::make('content.campaigns.add', [
            'aSegments' => $aSegments
        ])->withErrors($aErrors);
    }

    public function edit(Request $request, $id)
    {
        $oCampaign = new ModelCampaign();

        $oCampaign = $oCampaign->findOrFail($id);

        $aErrors = [];

        if ($request->isMethod('post')) {
            $aRules = [
                'name' => 'required'
            ];

            $aMessages = [
                'name.required' => sprintf(__('%s is required.'), __('Name'))
            ];

            $oValidator = Validator::make(Input::all(), $aRules, $aMessages);

            if ($oValidator->fails()) {
                $aErrors = $oValidator->errors();
            }

            if (count($aErrors) == 0) {
                $password = Input::get('password');

                $oCampaign->name = Input::get('name');

                $oCampaign->save();

                return Redirect::to('/campaigns')
                    ->with('success', sprintf(
                            __('The %s "%s" has been saved.'),
                            __('campaign'),
                            '<em>'.e(Input::get('name')).'</em>'
                        )
                    )
                ;
            }
        }

        return View::make('content.campaigns.edit', [
            'oCampaign' => $oCampaign,
        ])->withErrors($aErrors);
    }

    public function delete(Request $request, $id)
    {
        $oCampaign = new ModelCampaign();

        $oCampaign = $Campaign->findOrFail($id);

        $aResponse = [
            'status' => 'OK',
            'alert' => sprintf(
                __('The %s "%s" has been deleted.'),
                __('campaign'),
                '<em>'.e($oCampaign->name).'</em>'
            ),
        ];

        $oCampaign->delete();

        return response()->json($aResponse);
    }

}
