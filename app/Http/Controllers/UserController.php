<?php

namespace App\Http\Controllers;

use App\User as ModelUser;
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

class UserController extends Controller
{
    public function index()
    {
        return view('content.users.index', [
            'success' => Session::get('success'),
        ]);
    }

    public function json()
    {
        $oDB = DB::table('users')
            ->select(
                'id',
                'name',
                'email',
                'admin',
                'created_at',
                'updated_at'
            )
        ;

        if (Input::get('search')) {
            $oDB->whereRaw('MATCH(name,email) AGAINST(? IN BOOLEAN MODE)', [Input::get('search')]);
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
                    'email' => $o->email,
                    'admin' => $o->admin,
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

        if ($request->isMethod('post')) {
            $aMessages = [
                'name.required' => sprintf(__('%s is required.'), __('Name')),
                'email.required' => sprintf(__('%s is required.'), __('Email')),
                'email.email' => sprintf(__('%s is invalid.'), __('Email')),
            ];

            $oValidator = Validator::make(Input::all(), [
                'name' => 'required',
                'email' => 'required|email|unique:users',
            ], $aMessages);

            if ($oValidator->fails()) {
                $aErrors = $oValidator->errors();
            }

            if (count($aErrors) == 0) {
                $password = Input::get('password');

                $oUser = new ModelUser();

                $oUser->name = Input::get('name');
                $oUser->email = Input::get('email');

                if ($password) {
                    $oUser->password = Hash::make($password);
                }

                $oUser->save();

                return Redirect::to('/users')
                   ->with('success', sprintf(
                           __('The %s "%s" has been added.'),
                           __('user'),
                           '<em>'.e(Input::get('name')).'</em>'
                       )
                   )
               ;
            }
        }

        return View::make('content.users.add')->withErrors($aErrors);
    }

    public function edit(Request $request, $id)
    {
        $oUser = new ModelUser();

        $oUser = $oUser->findOrFail($id);

        $aErrors = [];

        if ($request->isMethod('post')) {
            $aRules = [
                'name' => 'required',
                'email' => 'required|email',
            ];

            if (Input::get('email') != $oUser->email) {
                $aRules['email'] = 'required|email|unique:users';
            }

            $aMessages = [
                'name.required' => sprintf(__('%s is required.'), __('Name')),
                'email.required' => sprintf(__('%s is required.'), __('Email')),
                'email.email' => sprintf(__('%s is invalid.'), __('Email')),
            ];

            $oValidator = Validator::make(Input::all(), $aRules, $aMessages);

            if ($oValidator->fails()) {
                $aErrors = $oValidator->errors();
            }

            if (count($aErrors) == 0) {
                $password = Input::get('password');

                $oUser->name = Input::get('name');
                $oUser->email = Input::get('email');

                if ($password) {
                    $oUser->password = Hash::make($password);
                }

                $oUser->save();

                return Redirect::to('/users')
                    ->with('success', sprintf(
                            __('The %s "%s" has been saved.'),
                            __('user'),
                            '<em>'.e(Input::get('name')).'</em>'
                        )
                    )
                ;
            }
        }

        return View::make('content.users.edit', [
            'oUser' => $oUser,
        ])->withErrors($aErrors);
    }

    public function delete(Request $request, $id)
    {
        $oUser = new ModelUser();

        $oUser = $oUser->findOrFail($id);

        if ($oUser->admin == 1) {
            App::abort(401, 'Unauthorized.');
        }

        $aResponse = [
            'status' => 'OK',
            'alert' => sprintf(
                __('The %s "%s" has been deleted.'),
                __('user'),
                '<em>'.e($oUser->name).'</em>'
            ),
        ];

        $oUser->delete();

        return response()->json($aResponse);
    }

    public function changePassword(Request $request)
    {
        $request->flash();

        $aErrors = [];

        $oUser = Auth::user();

        if ($request->isMethod('post')) {
            $aMessages = [
                'current_password.required' => sprintf(__('%s is required.'), __('Current Password')),
                'new_password.required' => sprintf(__('%s is required.'), __('New Password')),
                'new_password_repeat.required' => sprintf(__('%s is required.'), __('New Password (repeat)')),
            ];

            $oValidator = Validator::make(Input::all(), [
                'current_password' => 'required',
                'new_password' => 'required|same:new_password_repeat',
                'new_password_repeat' => 'required',
            ], $aMessages);

            if ($oValidator->fails()) {
                $aErrors = $oValidator->errors();
            }

            if (count($aErrors) == 0
            && !Hash::check(Input::get('current_password'), $oUser->password)) {
                $oValidator->getMessageBag()->add('current_password', __('These credentials do not match our records.'));
                $aErrors = $oValidator->errors();
            }

            if (count($aErrors) == 0) {
                $password = Input::get('new_password');

                $oUser->password = Hash::make($password);

                $oUser->save();

                return Redirect::to('/change-password')
                    ->with('success', __('The password has been changed.'))
                ;
            }
        }

        return View::make('content.users.change_password', [
            'success' => Session::get('success'),
        ])->withErrors($aErrors);
    }
}
