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

  public function all2(Request $request , $shop_url){
    $monthly_select_id = $request->input('monthly_select_id',0);
    $date = Carbon::now();
    $monthly_selector = [] ;
    while($date->format('Y') >= 2017 ){
      $monthly_selector[$date->format('Y-m-01')] = $date->format('F Y') ;
      $date->subMonth() ;
    }

    if($monthly_select_id == 0){
      foreach ($monthly_selector as $key => $value) {
        $monthly_select_id = $key ;
        break ;
      }
      return redirect($shop_url.'/monthly-all-employee2?monthly_select_id=' . $monthly_select_id);
    }

    $task = Option::byOptionType('employee2_task_monthly')->first();
    $task_list = Option::whereIn('id',explode(',', $task->name))->orderBy('ordering','asc')->get() ;

    $employees = Employee::byType2()->orderBy('name','asc')->get();
    $employee_ids = array();
    foreach($employees as $employee){
      $employee_ids[] = $employee->id ;
    }

    $start_date = Carbon::createFromFormat('Y-m-d H',$monthly_select_id . ' 0');
    $end_date = Carbon::createFromFormat('Y-m-d H',$monthly_select_id . ' 0');
    $end_date->addMonth();

    $start_date->setTimezone('Asia/Bangkok');
    $end_date->setTimezone('Asia/Bangkok');

    $daily_jobs = DailyJob::where('task_at','>',$start_date)->where('task_at','<',$end_date)->whereIn('employee_id',$employee_ids)->whereIn('task_id',explode(',', $task->name))->orderBy('task_at','ASC')->orderBy('id', 'ASC')->get();

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
        $value['percent'] += $value['amount'] * 10 / 100 ;
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


    return view('shop.monthly-summary.all2')->with('shop_url',$shop_url)->with('monthly_selector',$monthly_selector)->with('monthly_select_id',$monthly_select_id)->with('task_list',$task_list)->with('results',$results)->with('summary_by_task',$summary_by_task);
  }

  public function salary(Request $request , $shop_url){
    $date_ranges = DateRange::orderBy('start_date','desc')->pluck('name','id');
    $date_range_id = $request->input('date_range_id',0);

    if($date_range_id == 0){
      foreach ($date_ranges as $key => $value) {
        $date_range_id = $key ;
        break ;
      }
      return redirect($shop_url.'/monthly-salary?date_range_id=' . $date_range_id);
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

    $second_period = false ;
    if($date_range->start_date->format('d') == 15){
      $second_period = true ;
    }

    $daily_fine_jobs = [] ;
    if($second_period){
      $task_fine = Option::byOptionType('task_fine')->first();
      $new_start_date = $date_range->end_date->copy()->subMonth() ;
      $daily_fine_jobs = DailyJob::where('task_at','>',$new_start_date)->where('task_at','<',$date_range->end_date)->whereIn('employee_id',$employee_ids)->whereIn('task_id',explode(',', $task_fine->name))->orderBy('task_at','ASC')->orderBy('id', 'ASC')->get();
    }




    $results = array();
    $grand_total = 0 ;
    foreach($employees as $employee){
      $data = array() ;
      $data['employee'] = $employee ;

      $data['summary_amount'] = 0 ;
      $data['summary_percent'] = 0 ;
      $data['fine'] = 0 ;
      foreach($task_list as $task){
        $data['data'][$task->id] = array('amount' => 0 , 'percent' => 0);
      }

      foreach($daily_jobs as $daily_job){
        if($daily_job->employee_id != $employee->id){
          continue ;
        }
        $data['data'][$daily_job->task_id]['amount'] += $daily_job->amount ;
        $data['summary_amount'] += $daily_job->amount ;
      }

      foreach($daily_fine_jobs as $daily_job){
        if($daily_job->employee_id != $employee->id){
          continue ;
        }
        $data['fine'] += $daily_job->amount ;
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
      $data['salary'] = ( $employee->base_salary / 2 ) +  $data['summary_percent'] ;
      if($second_period)
        $data['total_receive'] = $data['salary'] + $data['fine'];
      else
        $data['total_receive'] = $data['salary'] ;
      $grand_total += $data['total_receive'];

      $results[] = $data ;
    }

    $employees2 = Employee::byType2()->orderBy('name','asc')->get();
    $results2 = array();

    $employee2_ids = array();
    foreach($employees2 as $employee){
      $employee2_ids[] = $employee->id ;
    }

    $daily_fine2_jobs = [] ;
    if($second_period){
      $task_fine = Option::byOptionType('task_fine')->first();
      $new_start_date = $date_range->end_date->copy()->subMonth() ;
      $daily_fine2_jobs = DailyJob::where('task_at','>',$new_start_date)->where('task_at','<',$date_range->end_date)->whereIn('employee_id',$employee2_ids)->whereIn('task_id',explode(',', $task_fine->name))->orderBy('task_at','ASC')->orderBy('id', 'ASC')->get();
    }

    $grand_total2 = 0 ;
    foreach($employees2 as $employee){
      $data = array() ;
      $data['employee'] = $employee ;
      $data['salary'] = ( $employee->base_salary / 2 ) ;
      $data['fine'] = 0 ;

      foreach($daily_fine2_jobs as $daily_job){
        if($daily_job->employee_id != $employee->id){
          continue ;
        }
        $data['fine'] += $daily_job->amount ;
      }


      $data['total_receive'] = $data['salary'];
      if($second_period){
        $data['total_receive'] += $data['fine'] ;
      }
      $grand_total2 += $data['total_receive'];

      $results2[] = $data ;
    }

    return view('shop.monthly-summary.salary')->with('shop_url',$shop_url)->with('date_ranges',$date_ranges)->with('date_range_id',$date_range_id)->with('results',$results)->with('grand_total',$grand_total)->with('results2',$results2)->with('grand_total2',$grand_total2)->with('second_period',$second_period);
  }
}