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
    $today = Carbon::today()->addDay();
    //$today = Carbon::create(2017, 1, 31 ,0);
    $lastmonth = Carbon::today()->subMonth() ;
    //$lastmonth = Carbon::create(2017, 1, 1 , 0);
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

    return view('shop.dashboard')->with('shop_url',$shop_url)->with('data',json_encode($data));
  }
}