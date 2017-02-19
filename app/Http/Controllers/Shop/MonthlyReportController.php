<?php namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller ;
use Illuminate\Http\Request;

use App\DateRange ;
use App\Option ;
use App\Employee;
use App\DailyJob;
use App\TaskPercent ;

use Carbon\Carbon ;

Use DB;

class MonthlyReportController extends Controller{
	public function all1(Request $request , $shop_url){
		$date_ranges = DateRange::orderBy('start_date','desc')->pluck('name','id');
		$date_range_id = $request->input('date_range_id',0);
		if($date_range_id == 0){
			foreach ($date_ranges as $key => $value) {
				$date_range_id = $key ;
				break ;
			}
			return redirect($shop_url.'/monthly-all-employee1?date_range_id=' . $date_range_id);
		}

		$date_range = DateRange::find($date_range_id);

		$task = Option::byOptionType('employee1_task_monthly')->first();
    $task_list = Option::whereIn('id',explode(',', $task->name))->orderBy('ordering','asc')->get() ;

    $employees = Employee::byType1()->orderBy('name','asc')->get();
    $employee_ids = array();
    foreach($employees as $employee){
      $employee_ids[] = $employee->id ;
    }

    $daily_jobs = DailyJob::where('task_at','>',$date_range->start_date)->where('task_at','<',$date_range->end_date)->whereIn('employee_id',$employee_ids)->whereIn('task_id',explode(',', $task->name))->orderBy('task_at','ASC')->orderBy('id', 'ASC')->get();

    $results = array();
    foreach($employees as $employee){
      $data = array() ;
      $data['employee'] = $employee ;
      $data['data'] = array() ;
      $data['summary_count'] = 0 ;
      $data['summary_amount'] = 0 ;
      $data['summary_percent'] = 0 ;
      foreach($task_list as $task){
        $data['data'][$task->id] = array('count' => 0 , 'amount' => 0 , 'percent' => 0);
      }

      foreach($daily_jobs as $daily_job){
        if($daily_job->employee_id != $employee->id){
          continue ;
        }
        $data['data'][$daily_job->task_id]['count']++ ;
        $data['data'][$daily_job->task_id]['amount'] += $daily_job->amount ;
        $data['summary_amount'] += $daily_job->amount ;
        $data['summary_count']++ ;
      }

      #calculate percent ;
      foreach($data['data'] as $task_id => &$value){
      	$task_percent = TaskPercent::byTask($task_id)->first();
      	if(is_null($task_percent)){
      		$value['percent'] = 0 ;
      	}else{
      		$value['percent'] += $value['amount'] * $task_percent->percent / 100 ;
      	}
      	$data['summary_percent'] += $value['percent'];
      }


      $results[] = $data ;
    }


    $summary_by_task = array() ;
    $summary_by_task['all'] = 0 ;
    foreach($task_list as $task){
      $summary_by_task[$task->id] = 0;
    }

    foreach($results as $result){
    	foreach($result['data'] as $task_id => $value){
      	$summary_by_task[$task_id] += $value['percent'];
      	$summary_by_task['all'] += $value['percent'];
    	}
    }


		return view('shop.monthly-summary.all1')->with('shop_url',$shop_url)->with('date_ranges',$date_ranges)->with('date_range_id',$date_range_id)->with('task_list',$task_list)->with('results',$results)->with('summary_by_task',$summary_by_task);
	}

  public function single1(Request $request , $shop_url){
    $date_ranges = DateRange::orderBy('start_date','desc')->pluck('name','id');
    $date_range_id = $request->input('date_range_id',0);
    $employee_id = $request->input('employee_id',0);
    if($date_range_id == 0){
      foreach ($date_ranges as $key => $value) {
        $date_range_id = $key ;
        break ;
      }
      return redirect($shop_url.'/monthly-single-employee1?date_range_id=' . $date_range_id);
    }

    $employees = Employee::byType1()->orderBy('name','asc')->pluck('name','id');

    if($employee_id == 0){
      foreach($employees as $key => $employee){
        $employee_id = $key ;
        break;
      }
      return redirect($shop_url.'/monthly-single-employee1?date_range_id=' . $date_range_id . '&employee_id='.$employee_id);
    }

    $task = Option::byOptionType('employee1_task_monthly')->first();
    $task_list = Option::whereIn('id',explode(',', $task->name))->orderBy('ordering','asc')->get() ;

    $date_range = DateRange::find($date_range_id);
    $start_day = (int)$date_range->start_day_format ;
    $end_day = (int)$date_range->end_day_format ;

    $daily_jobs = DailyJob::where('task_at','>',$date_range->start_date)->where('task_at','<',$date_range->end_date)->byEmployee($employee_id)->whereIn('task_id',explode(',', $task->name))->orderBy('task_at','ASC')->orderBy('id', 'ASC')->get();

    $results = [] ;
    for($i = $start_day ; $i <= $end_day ; $i++){
      $data = [] ;
      $data['day'] = $i ;
      $data['summary_count'] = 0 ;
      $data['summary_amount'] = 0 ;
      $data['data'] = [];
      foreach($task_list as $task){
        $data['data'][$task->id] = array('count' => 0 , 'amount' => 0 );
      }

      foreach($daily_jobs as $daily_job){
        echo (int)$daily_job->task_at_day_format . '---' . $i  . '|';
        if((int)$daily_job->task_at_day_format != $i){
          continue ;
        }



        $data['data'][$daily_job->task_id]['count']++ ;
        $data['data'][$daily_job->task_id]['amount'] += $daily_job->amount ;
        $data['summary_amount'] += $daily_job->amount ;
        $data['summary_count']++ ;
      }
      $results[] = $data ;
    }

    $summary_by_task = array() ;
    $summary_by_task['all_amount'] = 0 ;
    $summary_by_task['all_count'] = 0 ;
    foreach($task_list as $task){
      $summary_by_task[$task->id] = ['count' => 0 , 'amount' => 0] ;
    }

    foreach($results as $result){
      foreach($result['data'] as $task_id => $value){
        $summary_by_task[$task_id]['amount'] += $value['amount'];
        $summary_by_task[$task_id]['count'] += $value['count'];

        $summary_by_task['all_amount'] += $value['amount'];
        $summary_by_task['all_count'] += $value['count'];
      }
    }

    return view('shop.monthly-summary.single1')->with('shop_url',$shop_url)->with('date_ranges',$date_ranges)->with('date_range_id',$date_range_id)->with('employees',$employees)->with('employee_id',$employee_id)->with('task_list',$task_list)->with('results',$results)->with('summary_by_task',$summary_by_task);

  }
}