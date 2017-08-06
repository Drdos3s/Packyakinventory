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

//Leads to marketing page--->
Route::get('/', function() {
	return view('marketing');
});

//Main dashboard--->
Route::get('/dashboard', 'mainItemFeedController@index');

//Retrieve item data
Route::get('/dashboard/retrieve', 'mainItemFeedController@createAndUpdateItems');

Route::post('/dashboard', 'mainItemFeedController@setupAndSendInventoryUpdate');
//Route::post('/dashboard', 'mainItemFeedController@setupAndSendUnitPriceUpdate');

Route::post('/dashboard/deleteVariation', 'mainItemFeedController@deleteItemVariation');






//Locations Page routes--->
Route::get('/locations', function () {
    return view('admin_template');
});

Route::get('/locations', 'locationsPageController@index');

//playing with laravel resource routes
/*Contains:
index
create
store
show
edit
update
destroy*/
Route::resource('vendors', 'vendorPageController');

//Purchase order routes--->
Route::get('/purchaseOrders', function () {
    return view('admin_template');
});

Route::get('/purchaseOrders', 'purchaseOrderController@index');
Route::post('/purchaseOrders', 'purchaseOrderController@ajaxRoute');


// Authentication routes...
Route::get('auth/login', 'Auth\AuthController@getLogin');
Route::post('auth/login', 'Auth\AuthController@postLogin');
Route::get('auth/logout', 'Auth\AuthController@getLogout');

// Registration routes...
Route::get('auth/register', 'Auth\AuthController@getRegister');
Route::post('auth/register', 'Auth\AuthController@postRegister');

// Square Permissions routes...
Route::get('auth/permission', 'Auth\PermissionsController@getPermission');

//User Profile Routes...
Route::resource('users', 'userController');

/*Contains:
index
create
store
show
edit
update
destroy*/

Route::controllers([
   'password' => 'Auth\PasswordController',
]);

