<?php

namespace App\Http\Controllers;

use App\Models\Language as ModelLanguage;
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
        $oDB = DB::table('users AS u')
            ->select(
                'u.id',
                'u.name',
                'u.gender',
                'u.email',
                'u.admin',
                'u.created_at',
                'u.updated_at',
                'l.flag AS language'
            )
            ->join('languages AS l', 'l.id', '=', 'u.language_id')
        ;

        if (Input::get('search')) {
            $oDB->whereRaw('MATCH(u.name,u.email) AGAINST(? IN BOOLEAN MODE)', [Input::get('search')]);
        }

        if (Input::get('sort') && Input::get('order')) {
            switch(Input::get('sort')) {
                case 'name':
                    $oDB->orderBy('u.name', Input::get('order'));
                    break;
                case 'gender':
                    $oDB->orderBy('u.gender', Input::get('order'));
                    break;
                case 'email':
                    $oDB->orderBy('u.email', Input::get('order'));
                    break;
                case 'created_at':
                    $oDB->orderBy('u.created_at', Input::get('order'));
                    break;
                case 'updated_at':
                    $oDB->orderBy('u.updated_at', Input::get('order'));
                    break;
            }
        } else {
            $oDB->orderBy('u.name');
        }

        $total = $oDB->count();

        $offset = Input::get('offset', 0);

        $limit = Input::get('limit', 25);

        $oDB->skip($offset)->take($limit);

        $a = $oDB->get();

        $aRows = [];

        if (count($a) > 0) {
            foreach ($a as $o) {
                $sGender = null;

                switch ($o->gender) {
                    case ModelUser::GENDER_UNKNOWN:
                        $sGender = __('Unknown');
                        break;
                    case ModelUser::GENDER_MALE:
                        $sGender = __('Male');
                        break;
                    case ModelUser::GENDER_FEMALE:
                        $sGender = __('Female');
                        break;
                }

                $aRows[] = [
                    'id' => $o->id,
                    'name' => $o->name,
                    'gender' => $sGender,
                    'email' => $o->email,
                    'admin' => $o->admin,
                    'created_at' => ($o->created_at ? date(Auth::user()->date_format.' H:i', strtotime($o->created_at)) : ''),
                    'updated_at' => ($o->updated_at ? date(Auth::user()->date_format.' H:i', strtotime($o->updated_at)) : ''),
                    'language' => $o->language
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

        $aGenders = [
            ModelUser::GENDER_UNKNOWN => __('Unknown'),
            ModelUser::GENDER_MALE => __('Male'),
            ModelUser::GENDER_FEMALE => __('Female'),
        ];

        $aLanguages = DB::table('languages')->pluck('name', 'id');

        foreach ($aLanguages as $k => $v) {
            $aLanguages[$k] = __($v);
        }

        if ($request->isMethod('post')) {
            $aMessages = [
                'name.required' => sprintf(__('%s is required.'), __('Name')),
                'gender.required' => sprintf(__('%s is required.'), __('Gender')),
                'email.required' => sprintf(__('%s is required.'), __('Email')),
                'email.email' => sprintf(__('%s is invalid.'), __('Email')),
                'email.unique' => sprintf(__('Email is already registered.'), __('Email')),
                'date_format.required' => sprintf(__('%s is required.'), __('Date Format')),
                'language.required' => sprintf(__('%s is required.'), __('Language')),
            ];

            $oValidator = Validator::make(Input::all(), [
                'name' => 'required',
                'gender' => 'required',
                'email' => 'required|email|unique:users',
                'date_format' => 'required',
                'language' => 'required',
            ], $aMessages);

            if ($oValidator->fails()) {
                $aErrors = $oValidator->errors();
            }

            if (count($aErrors) == 0) {
                $password = Input::get('password');

                $oUser = new ModelUser();

                $oUser->language_id = Input::get('language_id');
                $oUser->name = Input::get('name');
                $oUser->gender = Input::get('gender');
                $oUser->email = Input::get('email');

                if ($password) {
                    $oUser->password = Hash::make($password);
                }

                $oUser->date_format = Input::get('date_format');

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

        return View::make('content.users.add', [
            'aGenders' => $aGenders,
            'aDateFormats' => ModelUser::$aDateFormats,
            'aLanguages' => $aLanguages
        ])->withErrors($aErrors);
    }

    public function edit(Request $request, $id)
    {
        $oUser = new ModelUser();

        $oUser = $oUser->findOrFail($id);

        $aErrors = [];

        $aGenders = [
            ModelUser::GENDER_UNKNOWN => __('Unknown'),
            ModelUser::GENDER_MALE => __('Male'),
            ModelUser::GENDER_FEMALE => __('Female'),
        ];

        $aLanguages = DB::table('languages')->pluck('name', 'id');

        foreach ($aLanguages as $k => $v) {
            $aLanguages[$k] = __($v);
        }

        if ($request->isMethod('post')) {
            $aRules = [
                'name' => 'required',
                'gender' => 'required',
                'email' => 'required|email|unique:users,email,'.$oUser->id,
                'date_format' => 'required',
                'language_id' => 'required',
            ];

            if (Input::get('email') != $oUser->email) {
                $aRules['email'] = 'required|email|unique:users';
            }

            $aMessages = [
                'name.required' => sprintf(__('%s is required.'), __('Name')),
                'gender.required' => sprintf(__('%s is required.'), __('Gender')),
                'email.required' => sprintf(__('%s is required.'), __('Email')),
                'email.email' => sprintf(__('%s is invalid.'), __('Email')),
                'email.unique' => sprintf(__('Email is already registered.'), __('Email')),
                'date_format.required' => sprintf(__('%s is required.'), __('Date Format')),
                'language_id.required' => sprintf(__('%s is required.'), __('Language')),
            ];

            $oValidator = Validator::make(Input::all(), $aRules, $aMessages);

            if ($oValidator->fails()) {
                $aErrors = $oValidator->errors();
            }

            if (count($aErrors) == 0) {
                $password = Input::get('password');

                $oUser->name = Input::get('name');
                $oUser->gender = Input::get('gender');
                $oUser->email = Input::get('email');

                if ($password) {
                    $oUser->password = Hash::make($password);
                }

                $oUser->date_format = Input::get('date_format');

                if ($oUser->language_id != Input::get('language_id')) {
                    $oUser->language_id = Input::get('language_id');
                    $oLanguage = new ModelLanguage();
                    $oLanguage = $oLanguage->find(Input::get('language_id'));
                    if ($oUser->id == Auth::user()->id) {
                        Session::set('locale', $oLanguage->locale);
                    }
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
            'aGenders' => $aGenders,
            'aDateFormats' => ModelUser::$aDateFormats,
            'aLanguages' => $aLanguages,
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
