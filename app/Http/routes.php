<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/

Route::get('/scripts/i18n.js', ['uses' => 'ScriptController@i18n']);
Route::get('/scripts/globals.js', ['uses' => 'ScriptController@globals']);

Route::group(['middleware' => 'web'], function () {
    Route::get('/', ['middleware' => 'auth', 'uses' => 'HomeController@index']);
    Route::get('/logout', ['middleware' => 'auth', 'uses' => 'AuthController@logout']);
    Route::match(['get', 'post'], '/login', ['middleware' => 'guest', 'uses' => 'AuthController@login']);
    Route::match(['get', 'post'], '/my-account', ['middleware' => 'auth', 'uses' => 'AccountController@edit', 'as' => 'account.edit']);

    // Campaigns
    Route::get('/campaigns', ['middleware' => 'auth', 'uses' => 'CampaignController@index']);
    Route::get('/campaigns/json/planned', ['middleware' => 'auth', 'uses' => 'CampaignController@jsonPlanned']);
    Route::get('/campaigns/json/sent', ['middleware' => 'auth', 'uses' => 'CampaignController@jsonSent']);
    Route::match(['get', 'post'], '/campaigns/add', ['middleware' => 'auth', 'uses' => 'CampaignController@add']);
    Route::match(['get', 'post'], '/campaigns/edit/{id}', ['middleware' => 'auth', 'uses' => 'CampaignController@edit', 'as' => 'campaigns.edit']);
    Route::delete('/campaigns/delete/{id}', ['middleware' => 'auth', 'uses' => 'CampaignController@delete']);
    Route::get('/campaigns/csv/{id}', ['middleware' => 'auth', 'uses' => 'CampaignController@csv']);
    Route::match(['get', 'post'], '/campaigns/details/{id}', ['middleware' => 'auth', 'uses' => 'Campaign\DetailsController@index']);
    Route::get('/campaigns/details/json/customers_without_saving/{id}', ['middleware' => 'auth', 'uses' => 'Campaign\DetailsController@JsonCustomersWithoutSaving']);
    Route::get('/campaigns/details/json/customers_with_savings/{id}', ['middleware' => 'auth', 'uses' => 'Campaign\DetailsController@JsonCustomersWithSavings']);
    Route::get('/campaigns/details/json/customers_with_current_offer/{id}', ['middleware' => 'auth', 'uses' => 'Campaign\DetailsController@JsonCustomersWithCurrentOffer']);

    // Deal
    Route::post('/deals/active/{id}', ['middleware' => 'auth', 'uses' => 'DealController@active']);
    Route::match(['get', 'post'], '/verleng/{token}', ['uses' => 'DealController@extend']);

    // Prices
    Route::get('/prices', ['middleware' => 'auth', 'uses' => 'PricesController@index']);
    Route::get('/prices/json', ['middleware' => 'auth', 'uses' => 'PricesController@json']);

    // Users
    Route::get('/users', ['middleware' => 'auth', 'uses' => 'UserController@index']);
    Route::get('/users/json', ['middleware' => 'auth', 'uses' => 'UserController@json']);
    Route::match(['get', 'post'], '/users/add', ['middleware' => 'auth', 'uses' => 'UserController@add']);
    Route::match(['get', 'post'], '/users/edit/{id}', ['middleware' => 'auth', 'uses' => 'UserController@edit', 'as' => 'users.edit']);
    Route::delete('/users/delete/{id}', ['middleware' => 'auth', 'uses' => 'UserController@delete']);
    Route::match(['get', 'post'], '/change-password', ['middleware' => 'auth', 'uses' => 'UserController@changePassword']);

    // I18n
    Route::get('/i18n', ['middleware' => 'auth', 'uses' => 'I18nController@index']);
    Route::get('/i18n/json', ['middleware' => 'auth', 'uses' => 'I18nController@json']);
    Route::match(['get', 'post'], '/i18n/add', ['middleware' => 'auth', 'uses' => 'I18nController@add']);
    Route::match(['get', 'post'], '/i18n/edit/{id}', ['middleware' => 'auth', 'uses' => 'I18nController@edit', 'as' => 'i18n.edit']);
    Route::delete('/i18n/delete/{id}', ['middleware' => 'auth', 'uses' => 'I18nController@delete']);

    // Test
    Route::get('/test', ['middleware' => 'auth', 'uses' => 'TestController@index']);
});
