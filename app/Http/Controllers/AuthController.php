<?php

namespace App\Http\Controllers;

use View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Input;

class AuthController extends Controller
{
    protected $redirectTo = '/';

    public function login(Request $request)
    {
        $request->flash();

        $aErrors = [];

        if ($request->isMethod('post')) {
            $oValidator = \Validator::make(Input::all(), [
                'email' => 'required',
                'password' => 'required',
            ]);

            if ($oValidator->fails()) {
                $aErrors = $oValidator->errors();
            }
            if (count($aErrors) == 0) {
                if (!Auth::attempt([
                    'email' => Input::get('email'),
                    'password' => Input::get('password'),
                ])) {
                    $aErrors['email'] = __('These credentials do not match our records.');
                } else {
                    return redirect('/');
                }
            }
        }

        return view('login')->withErrors($aErrors);
    }

    public function logout()
    {
        Auth::logout();

        return redirect('/login');
    }
}
