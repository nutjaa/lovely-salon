<?php namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller ;
use Illuminate\Http\Request;

use Carbon\Carbon ;

class DailyJobController extends Controller{
	public function index(Request $request , $shop_url){
		$selected_date = $request->input('date',Carbon::today()->toDateString());
		return view('shop.daily_job.index')->with('shop_url',$shop_url)->with('selected_date',$selected_date) ;
	}
}