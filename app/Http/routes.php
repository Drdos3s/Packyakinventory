<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

//Leads to marketing page
Route::get('/', function() {
	return view('marketing');
});

//Main dashboard
Route::get('/dashboard', function () {
    return view('admin_template');
});

Route::get('/dashboard', 'mainItemFeedController@index');
Route::post('/dashboard', 'mainItemFeedController@setupAndSendInventoryUpdate');



//Locations Page routes
Route::get('/locations', function () {
    return view('admin_template');
});

Route::get('/locations', 'locationsPageController@index');

//Purchase order routes
Route::get('/purchaseOrders', function () {
    return view('admin_template');
});

Route::get('/purchaseOrders', 'purchaseOrderController@index');

// Authentication routes...
Route::get('auth/login', 'Auth\AuthController@getLogin');
Route::post('auth/login', 'Auth\AuthController@postLogin');
Route::get('auth/logout', 'Auth\AuthController@getLogout');

// Registration routes...
Route::get('auth/register', 'Auth\AuthController@getRegister');
Route::post('auth/register', 'Auth\AuthController@postRegister');


Route::controllers([
   'password' => 'Auth\PasswordController',
]);

