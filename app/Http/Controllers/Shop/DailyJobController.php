<?php namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller ;
use Illuminate\Http\Request;

use App\Employee ;
use App\DailyJob ;
use App\Company ;
use App\Option ;
use App\Customer ;

use Carbon\Carbon ;

class DailyJobController extends Controller{
	public function index(Request $request , $shop_url){
		$selected_date = $request->input('date',Carbon::today()->toDateString());
		return view('shop.daily_job.index')->with('shop_url',$shop_url)->with('selected_date',$selected_date) ;
	}

	public function create($shop_url){
		$daily_job = new DailyJob() ;
		$daily_job->task_at = Carbon::now('Asia/Bangkok');
		//$daily_job->task_at->setTimezone('UTC');
		$company = Company::byUrl($shop_url)->first() ;
		$employee_list = Employee::byCompanyId($company->id)->orderBy('name','asc')->pluck('name','id');
		$task_list = Option::byCompanyId($company->id)->orderBy('ordering','asc')->pluck('name','id');
   	return view('shop.daily_job.edit')->with('shop_url',$shop_url)->with('daily_job',$daily_job)->with('employee_list',$employee_list)->with('task_list',$task_list) ;
  }

  public function store(Request $request , $shop_url){

  	$this->validate($request, [
        'employee_id' => 'required',
        'task_id' => 'required' ,
        'amount' => 'required'
    ]);

  	/*
  	$all = $request->all() ;
  	print_r($all) ; die() ;
  	*/

  	$company = Company::byUrl($shop_url)->first() ;

  	$daily_job = new DailyJob();
  	$daily_job->employee_id = $request->input('employee_id');

  	$date = Carbon::createFromFormat('Y-m-d H:i', $request->input('task_at'),'Asia/Bangkok');
		$date->setTimezone('UTC');

  	$daily_job->task_at =  $date->toDateTimeString() ;

  	$daily_job->task_id = $request->input('task_id');
  	$daily_job->description = $request->input('description');
  	$daily_job->amount = $request->input('amount');
  	$daily_job->company_id = $company->id ;
  	$daily_job->is_loyal_customer = (bool)$request->input('is_loyal_customer',false);

  	$customer_name = $request->input('customer_name');
  	if(trim($customer_name)){
  		$customer = Customer::byName($customer_name)->byCompanyId($company->id)->first();
	  	if(is_null($customer)){
	  		$customer = new Customer() ;
	  		$customer->name = $customer_name ;
	  		$customer->description = '' ;
	  		$customer->company_id = $company->id ;
	  		$customer->save() ;
	  	}
	  	$daily_job->customer_id = $customer->id ;
  	}

  	$daily_job->save();

  	return redirect($shop_url.'/daily-jobs')->with('status', 'Success create new job!');
  }
}