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

class AccountController extends Controller
{
    public function edit(Request $request)
    {
        $oUser = Auth::user();

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
                $oUser->date_format = Input::get('date_format');

                if ($password) {
                    $oUser->password = Hash::make($password);
                }

                if ($oUser->language_id != Input::get('language_id')) {
                    $oUser->language_id = Input::get('language_id');
                    $oLanguage = new ModelLanguage();
                    $oLanguage = $oLanguage->find(Input::get('language_id'));
                    Session::set('locale', $oLanguage->locale);
                }

                $oUser->save();

                return Redirect::to('/my-account')
                    ->with('success', sprintf(
                            __('The %s "%s" has been saved.'),
                            __('user'),
                            '<em>'.e(Input::get('name')).'</em>'
                        )
                    )
                ;
            }
        }

        return View::make('content.account.edit', [
            'aDateFormats' => ModelUser::$aDateFormats,
            'aGenders' => $aGenders,
            'aLanguages' => $aLanguages,
            'oUser' => $oUser,
        ])->withErrors($aErrors);
    }
}
