<?php namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller ;
use Illuminate\Http\Request;

use App\Employee ;
use App\DailyJob ;
use App\Company ;
use App\Option ;
use App\Customer ;

use Carbon\Carbon ;

Use DB;

class DailyJobController extends Controller{
	public function index(Request $request , $shop_url){
    $company = Company::byUrl($shop_url)->first() ;
		$selected_date = $request->input('date',Carbon::today()->toDateString());
    #DB::enableQueryLog();
    $queue_customers = DailyJob::byTaskDate($selected_date)->byNoAmount()->orderBy('task_at','ASC')->orderBy('id', 'ASC')->get();

    #make sure that no duplicate ;
    $employee_queue_ids = array() ;
    $show_queue_employees = array() ;
    foreach($queue_customers as $queue_customer){
      if(!in_array($queue_customer->employee_id, $employee_queue_ids)){
        $employee_queue_ids[] = $queue_customer->employee_id ;
        $show_queue_employees[] = $queue_customer ;
      }
    }

    $daily_jobs = DailyJob::byTaskDate($selected_date)->byHasAmount()->orderBy('task_at','ASC')->orderBy('id', 'ASC')->get();
    $rows = [] ;

    foreach ($daily_jobs as $daily_job) {
      $insert = false ;
      foreach($rows as &$row){
        if(!isset($row[$daily_job->employee_id])){
          $row[$daily_job->employee_id] = $daily_job ;
          $insert = true ;
          break ;
        }
      }
      if($insert === false){
        $rows[] = [
          $daily_job->employee_id => $daily_job
        ];
      }
    }

    $summary = [] ;
    foreach ($daily_jobs as $daily_job) {
      if(!isset($summary[$daily_job->employee_id])){
        $summary[$daily_job->employee_id] = $daily_job->amount ;
      }else{
        $summary[$daily_job->employee_id] += $daily_job->amount ;
      }
    }

    $ot_task = Option::byOptionType('ot_task_list')->byCompany($company->id)->first();
    $ot_task = explode(',',$ot_task->name);

		return view('shop.daily_job.index')->with('shop_url',$shop_url)->with('selected_date',$selected_date)->with('queue_employees',$show_queue_employees)->with('rows',$rows)->with('employee_queue_ids',$employee_queue_ids)->with('summary',$summary)->with('ot_task',$ot_task);
	}

  private function createEmployeeList(Request $request,$shop_url){


    $company = Company::byUrl($shop_url)->first() ;
    $employee_list = Employee::byCompanyId($company->id)->orderBy('name','asc')->pluck('name','id');
    $first = collect(['-1'=>trans('daily-task.select-employee') ]) ;


    return  $first->all() + $employee_list->all() ;
  }

	public function create(Request $request ,$shop_url){
		$daily_job = new DailyJob() ;
		$daily_job->task_at = Carbon::now('Asia/Bangkok');
    $daily_job->amount = 0 ;

    $task_at = $request->input('task_at',false);
    $employee_id = $request->input('employee_id');
    if($task_at){
      $daily_job->task_at = Carbon::createFromFormat('Y-m-d',$task_at);
    }
    if($employee_id){
      $daily_job->employee_id = $employee_id ;
    }

    $company = Company::byUrl($shop_url)->first() ;
    $employee_list = $this->createEmployeeList($request,$shop_url);
    if($daily_job->employee_id){
      $total_tasks = DailyJob::byTaskDate($daily_job->task_at)->byEmployee($daily_job->employee_id)->byCompany($company->id)->count() ;
      if($total_tasks){
        $task_list = $daily_job->employee->tasks->pluck('name','id'); ;
      }else{
        $task_list = Option::byCompanyId($company->id)->byOptionType('task')->where('ordering',-1)->orderBy('ordering','asc')->pluck('name','id');
      }

    }else{
      $task_list = Option::byCompanyId($company->id)->byOptionType('task')->orderBy('ordering','asc')->pluck('name','id');
    }

   	return view('shop.daily_job.edit')->with('shop_url',$shop_url)->with('daily_job',$daily_job)->with('employee_list',$employee_list)->with('task_list',$task_list) ;
  }

  public function store(Request $request , $shop_url){


  	$this->validate($request, [
        'employee_id' => 'required',
        'task_id' => 'required'
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
  	$daily_job->amount = $request->input('amount',0);
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

    $save_new = $request->input('save_new');
    if($save_new){
      return redirect($shop_url.'/daily-jobs/create?task_at='.$daily_job->task_at->toDateString().'&employee_id='.$daily_job->employee_id)->with('status', trans('daily-task.success-message') );
    }

  	return redirect($shop_url.'/daily-jobs?date='.$daily_job->task_at->toDateString())->with('status', trans('daily-task.success-message') );
  }

  public function show(Request $request , $shop_url , $id){
    $daily_job = DailyJob::where('id',$id)->first();
    $company = Company::byUrl($shop_url)->first() ;
    $employee_list = $this->createEmployeeList($request,$shop_url);
    $task_list = $daily_job->employee->tasks->pluck('name','id'); ;

    $employee_id = $request->input('employee_id');
    if($employee_id){
      $daily_job->employee_id = $employee_id ;
    }

    return view('shop.daily_job.edit')->with('shop_url',$shop_url)->with('daily_job',$daily_job)->with('employee_list',$employee_list)->with('task_list',$task_list) ;
  }

  public function update(Request $request , $shop_url , $id){
    $daily_job = DailyJob::where('id',$id)->first();

    $this->validate($request, [
        'employee_id' => 'required',
        'task_id' => 'required'
    ]);

    /*
    $all = $request->all() ;
    print_r($all) ; die() ;
    */

    $company = Company::byUrl($shop_url)->first() ;

    $daily_job->employee_id = $request->input('employee_id');

    $date = Carbon::createFromFormat('Y-m-d H:i', $request->input('task_at'),'Asia/Bangkok');
    $date->setTimezone('UTC');

    $daily_job->task_at =  $date->toDateTimeString() ;

    $daily_job->task_id = $request->input('task_id');
    $daily_job->description = $request->input('description');
    $daily_job->amount = $request->input('amount',0);
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
    return redirect($shop_url.'/daily-jobs?date='.$daily_job->task_at->toDateString())->with('status', trans('daily-task.success-message') );
  }

  public function destroy(Request $request , $shop_url , $id){
    DailyJob::destroy($id);
    return response()->json(['success' => true ]);
  }


  public function autoCreateQueueTaskIfNotExists($daily_job){

  }
}