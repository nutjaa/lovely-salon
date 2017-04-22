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

class DailySummaryController extends Controller{
	public function index(Request $request , $shop_url){
		$selected_date = $request->input('date',Carbon::today()->toDateString());
    #DB::enableQueryLog();
    $employee_types = ['ช่างซอย','ผู้ช่วยช่าง'] ;
    $employee_type = $request->input('employee_type',0) ;

    if($employee_type == 0){
      $task = Option::byOptionType('employee1_task_list')->first();
    }else{
      $task = Option::byOptionType('employee2_task_list')->first();
    }

    $task_list = Option::whereIn('id',explode(',', $task->name))->orderBy('ordering','asc')->get() ;

    if($employee_type == 0){
      $employees = Employee::byType1()->orderBy('name','asc')->get();
    }else{
      $employees = Employee::byType2()->orderBy('name','asc')->get();
    }
    $employee_ids = array();
    foreach($employees as $employee){
      $employee_ids[] = $employee->id ;
    }
    $daily_jobs = DailyJob::byTaskDate($selected_date)->byHasAmount()->whereIn('employee_id',$employee_ids)->orderBy('task_at','ASC')->orderBy('id', 'ASC')->get();

    $results = array();
    foreach($employees as $employee){
      $data = array() ;
      $data['employee'] = $employee ;
      $data['data'] = array() ;
      $data['summary'] = 0 ;
      foreach($task_list as $task){
        $data['data'][$task->id] = array('count' => 0 , 'amount' => 0);
      }

      foreach($daily_jobs as $daily_job){
        if($daily_job->employee_id != $employee->id){
          continue ;
        }
        if(!isset($data['data'][$daily_job->task_id])){
          continue;
        }
        $data['data'][$daily_job->task_id]['count']++ ;
        $data['data'][$daily_job->task_id]['amount'] += $daily_job->amount ;
        $data['summary'] += $daily_job->amount ;
      }
      $results[] = $data ;
    }

    $summary_by_task = array() ;
    $summary_by_task['all'] = 0 ;
    foreach($task_list as $task){
      $summary_by_task[$task->id] = 0;
    }

    foreach($daily_jobs as $daily_job){
      if(!isset($summary_by_task[$daily_job->task_id])){
        continue;
      }
      $summary_by_task[$daily_job->task_id] += $daily_job->amount;
      $summary_by_task['all'] += $daily_job->amount;
    }

    return view('shop.daily_summary.index')->with('shop_url',$shop_url)->with('selected_date',$selected_date)->with('task_list',$task_list)->with('results',$results)->with('summary_by_task',$summary_by_task)->with('employee_types',$employee_types)->with('employee_type',$employee_type);
  }
}