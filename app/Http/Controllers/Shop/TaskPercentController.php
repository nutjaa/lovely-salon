<?php namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller ;
use Illuminate\Http\Request;

use App\Option ;
use App\Company ;
use App\TaskPercent ;

use Carbon\Carbon;

class TaskPercentController extends Controller{
	public function index(Request $request , $shop_url){
		$company = Company::byUrl($shop_url)->first() ;
		$tasks = Option::byOptionType('task')->byCompanyId($company->id)->orderBy('ordering','asc')->get() ;

		$task_percents = TaskPercent::pluck('percent','option_id') ;


		return view('shop.task-percent.index')->with('shop_url',$shop_url)->with('tasks',$tasks)->with('task_percents',$task_percents);
	}

	public function store(Request $request , $shop_url){
		$task_percents = $request->input('task_percents');
		foreach($task_percents as $option_id => $percent){
			$task_percent = TaskPercent::byTask($option_id)->first();
			if(is_null($task_percent)){
				$task_percent = new TaskPercent();
			}
			$task_percent->option_id = $option_id ;
			$task_percent->percent = (float)$percent['percent'];
			$task_percent->save() ;
		}

		return redirect($shop_url.'/task-percent')->with('status', 'Success update task percent' );
	}
}
