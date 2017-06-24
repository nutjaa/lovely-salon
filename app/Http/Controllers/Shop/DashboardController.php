<?php namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller ;
use Illuminate\Http\Request;

use App\DailyJob ;
use App\Company;

use Carbon\Carbon;
use DB;

class DashboardController extends Controller{
  public function index(Request $request , $shop_url){
    $company = Company::byUrl($shop_url)->first() ;

    $monthly_select_id = $request->input('monthly_select_id',0);
    $date = Carbon::now();
    $monthly_selector = [] ;
    $monthly_selector[0] = 'Last month';
    while($date->format('Y') >= 2017 ){
      $monthly_selector[$date->format('Y-m-01')] = $date->format('F Y') ;
      $date->subMonth() ;
    }

    if($monthly_select_id == 0){
      $today = Carbon::today()->addDay();
      $lastmonth = Carbon::today()->subMonth() ;
    }else{
      $lastmonth = Carbon::createFromFormat('Y-m-d H',$monthly_select_id . ' 0');
      $today = Carbon::createFromFormat('Y-m-d H',$monthly_select_id . ' 0');
      $today->addMonth();
    }


    $start_date = $lastmonth->copy();
    $rows = DB::table('daily_jobs')
    ->select(DB::raw('DATE(task_at) as d'), DB::raw('sum(amount) as total'))
    //->where(DB::raw('task_at >= \''.$today->subMonth(6) . '\' AND task_at <= \'' . $today->addMonth() .'\''))
    ->where('task_at','>=',$lastmonth)
    ->where('task_at','<',$today)
    ->groupBy(DB::raw('DATE(task_at)') )
    ->get();
    $data = [] ;
    while ($lastmonth < $today) {
      $t = [] ;
      $t['date'] = $lastmonth->toDateString() ;
      foreach($rows as $row){
        if($row->d == $t['date']){
          $t['total'] = $row->total;
        }
      }
      if(!isset($t['total'])){
        $t['total'] = 0 ;
      }
      $data[] = $t ;
      $lastmonth->addDay();
    }

    return view('shop.dashboard')->with('shop_url',$shop_url)->with('data',json_encode($data))->with('start_date',$start_date)->with('to_date',$today)->with('monthly_selector',$monthly_selector)->with('monthly_select_id',$monthly_select_id);
  }
}