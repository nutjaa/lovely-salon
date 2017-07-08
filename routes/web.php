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

	Route::get('/profile','ProfileController@show');
	Route::put('/profile/{id}','ProfileController@update');

	Route::get('/change-password','PasswordController@show');
	Route::put('/change-password/{id}','PasswordController@update');

	Route::group([ 'prefix' => '{shop_url}' ], function () {
		Route::get('/', 'Shop\DashboardController@index');
		Route::resource('employees', 'Shop\EmployeeController');
		Route::resource('options', 'Shop\OptionController');
		Route::resource('daily-jobs','Shop\DailyJobController');
		Route::get('daily-summary/export','Shop\DailySummaryController@export');
		Route::resource('daily-summary','Shop\DailySummaryController');
		Route::get('customers/listing','Shop\CustomerController@listing');
		Route::resource('customers', 'Shop\CustomerController');
		Route::resource('date-ranges', 'Shop\DateRangeController');
		Route::get('task-percent','Shop\TaskPercentController@index');
		Route::post('task-percent','Shop\TaskPercentController@store');

		Route::get('monthly-all-employee1','Shop\MonthlyReportController@all1');
		Route::get('monthly-all-employee1/export','Shop\MonthlyReportController@all1Export');
		Route::get('monthly-single-employee1','Shop\MonthlyReportController@single1');
		Route::get('monthly-single-employee1/export','Shop\MonthlyReportController@single1Export');
		Route::get('monthly-all-employee2','Shop\MonthlyReportController@all2');
		Route::get('monthly-all-employee2/export','Shop\MonthlyReportController@all12Export');
		Route::get('monthly-single-employee2','Shop\MonthlyReportController@single2');
		Route::get('monthly-single-employee2/export','Shop\MonthlyReportController@single2Export');

		Route::get('monthly-salary','Shop\MonthlyReportController@salary');
		Route::get('monthly-salary/export','Shop\MonthlyReportController@salaryExport');
		Route::get('monthly-fine','Shop\MonthlyFineController@index');
	});
});



