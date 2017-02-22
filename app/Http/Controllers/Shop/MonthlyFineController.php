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

class MonthlyFineController extends Controller{
	public function index(Request $request , $shop_url){
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
			return redirect($shop_url.'/monthly-fine?monthly_select_id=' . $monthly_select_id);
		}

		$start_date = Carbon::createFromFormat('Y-m-d H',$monthly_select_id . ' 0');
		$end_date = Carbon::createFromFormat('Y-m-d H',$monthly_select_id . ' 0');
		$end_date->addMonth();

		$start_date->setTimezone('Asia/Bangkok');
		$end_date->setTimezone('Asia/Bangkok');

		$task = Option::byOptionType('task_fine')->first();
    $task_list = Option::whereIn('id',explode(',', $task->name))->orderBy('ordering','asc')->get() ;

		$daily_jobs = DailyJob::where('task_at','>',$start_date)->where('task_at','<',$end_date)->whereIn('task_id',explode(',', $task->name))->orderBy('task_at','ASC')->orderBy('id', 'ASC')->get();

		$start_day = $start_date->format('d');
		$end_day = $end_date->subDay()->format('d');

		$employees = Employee::orderBy('name','asc')->get();

		$results = [] ;
		for($i = $start_day ; $i <= $end_day ; $i++){
			$data = [] ;
			$data['day'] = $i ;
			$data['summary_amount'] = 0 ;

			foreach($employees as $employee){
				$data['data'][$employee->id] = 0 ;
			}

			foreach ($daily_jobs as $daily_job) {
				$daily_job->task_at->setTimezone('Asia/Bangkok');
				if($daily_job->task_at->format('d') != $i){
					continue ;
				}
				$data['data'][$daily_job->employee_id] += $daily_job->amount ;
				$data['summary_amount'] += $daily_job->amount ;
			}

			$results[] = $data ;
		}

		$summary_by_employee = array() ;
    $summary_by_employee['all_amount'] = 0 ;
    foreach($employees as $employee){
      $summary_by_employee[$employee->id] = [ 'amount' => 0] ;
    }

    foreach($results as $result){
      foreach($result['data'] as $employee_id => $value){
        $summary_by_employee[$employee_id]['amount'] += $value ;
        $summary_by_employee['all_amount'] += $value['amount'];
      }
    }



		return view('shop.monthly-summary.fine')->with('shop_url',$shop_url)->with('monthly_selector',$monthly_selector)->with('monthly_select_id',$monthly_select_id)->with('results',$results)->with('employees',$employees)->with('summary_by_employee',$summary_by_employee);
	}
}