<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/


Route::get('/', 'HomeController@index');
Auth::routes();
Route::get('/logout', 'Auth\LoginController@logout');

Route::group(['middleware' => ['auth']], function () {
	Route::resource('shops', 'CompanyController');
	Route::group([ 'prefix' => '{shop_url}' ], function () {
		Route::get('/', 'Shop\DashboardController@index');
		Route::resource('employees', 'Shop\EmployeeController');
	});
});



