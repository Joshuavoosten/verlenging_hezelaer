<?php

namespace App\Http\Controllers;

use App\Models\I18n as ModelI18n;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Session;
use Validator;
use View;

class I18nController extends Controller
{
    public function index()
    {
        $aLanguages = DB::table('languages')->lists('name', 'id');

        return view('content.i18n.index', [
            'success' => Session::get('success'),
            'aLanguages' => $aLanguages,
        ]);
    }

    public function json()
    {
        $oDB = DB::table('i18n')
            ->select(
                'id',
                'source_string',
                'destination_string',
                'created_at',
                'updated_at'
            )
        ;

        if (Input::get('source_language')) {
            $oDB->where('source_language', '=', Input::get('source_language'));
        }

        if (Input::get('destination_language')) {
            $oDB->where('destination_language', '=', Input::get('destination_language'));
        }

        if (Input::get('search')) {
            $oDB->whereRaw('MATCH(source_string,destination_string) AGAINST(? IN BOOLEAN MODE)', [Input::get('search')]);
        }

        if (Input::get('sort') && Input::get('order')) {
            $oDB->orderBy(Input::get('sort'), Input::get('order'));
        } else {
            $oDB->orderBy('source_string', 'ASC');
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
                    'source_string' => $o->source_string,
                    'destination_string' => $o->destination_string,
                    'created_at' => date('d-m-Y', strtotime($o->created_at)),
                    'updated_at' => date('d-m-Y', strtotime($o->updated_at)),
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

        $aLanguages = DB::table('languages')->lists('name', 'id');

        $aErrors = [];

        if ($request->isMethod('post')) {
            $aMessages = [
                'source_language.required' => sprintf(__('%s is required.'), __('Source Language')),
                'destination_language.required' => sprintf(__('%s is required.'), __('Destination Language')),
                'source_string.required' => sprintf(__('%s is required.'), __('Source String')),
                'destination_string.required' => sprintf(__('%s is required.'), __('Destination String')),
            ];

            $oValidator = Validator::make(Input::all(), [
                'source_language' => 'required',
                'destination_language' => 'required',
                'source_string' => 'required',
                'destination_string' => 'required',
            ], $aMessages);

            if ($oValidator->fails()) {
                $aErrors = $oValidator->errors();
            }

            if (count($aErrors) == 0) {
                $oI18n = new ModelI18n();

                $oI18n->source_language = Input::get('source_language');
                $oI18n->destination_language = Input::get('destination_language');
                $oI18n->source_string = Input::get('source_string');
                $oI18n->destination_string = Input::get('destination_string');

                $oI18n->save();

                return Redirect::to('/i18n')
                   ->with('success', sprintf(
                           __('The %s "%s" has been added.'),
                           __('i18n'),
                           '<em>'.e(Input::get('source_string')).'</em>'
                       )
                   )
               ;
            }
        }

        return View::make('content.i18n.add', [
            'aLanguages' => $aLanguages,
        ])->withErrors($aErrors);
    }

    public function edit(Request $request, $id)
    {
        $oI18n = new ModelI18n();

        $oI18n = $oI18n->findOrFail($id);

        $aLanguages = DB::table('languages')->lists('name', 'id');

        $aErrors = [];

        if ($request->isMethod('post')) {
            $aRules = [
                'source_language' => 'required',
                'destination_language' => 'required',
                'source_string' => 'required',
                'destination_string' => 'required',
            ];

            if (Input::get('name') != $oI18n->name) {
                $aRules['name'] = 'required|unique:i18n';
            }

            $aMessages = [
                'source_language.required' => sprintf(__('%s is required.'), __('Source Language')),
                'destination_language.required' => sprintf(__('%s is required.'), __('Destination Language')),
                'source_string.required' => sprintf(__('%s is required.'), __('Source String')),
                'destination_string.required' => sprintf(__('%s is required.'), __('Destination String')),
            ];

            $oValidator = Validator::make(Input::all(), $aRules, $aMessages);

            if ($oValidator->fails()) {
                $aErrors = $oValidator->errors();
            }

            if (count($aErrors) == 0) {
                $oI18n->source_language = Input::get('source_language');
                $oI18n->destination_language = Input::get('destination_language');
                $oI18n->source_string = Input::get('source_string');
                $oI18n->destination_string = Input::get('destination_string');

                $oI18n->save();

                return Redirect::to('/i18n')
                   ->with('success', sprintf(
                           __('The %s "%s" has been saved.'),
                           __('i18n'),
                           '<em>'.e(Input::get('source_string')).'</em>'
                       )
                   )
               ;
            }
        }

        return View::make('content.i18n.edit', [
            'oI18n' => $oI18n,
            'aLanguages' => $aLanguages,
        ])->withErrors($aErrors);
    }

    public function delete(Request $request, $id)
    {
        $oI18n = new ModelI18n();

        $oI18n = $oI18n->findOrFail($id);

        $aResponse = [
            'status' => 'OK',
            'alert' => sprintf(
                __('The %s "%s" has been deleted.'),
                __('I18n'),
                '<em>'.e($oI18n->name).'</em>'
            ),
        ];

        $oI18n->delete();

        return response()->json($aResponse);
    }
}
