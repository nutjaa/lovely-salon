<?php namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller ;
use Illuminate\Http\Request;

use App\Company ;
use App\DateRange ;

use Carbon\Carbon;

class DateRangeController extends Controller{

	public function index(Request $request , $shop_url){
		$company = Company::byUrl($shop_url)->first() ;
		$date_ranges = DateRange::orderBy('start_date','desc')->get();

		return view('shop.date-range.index')->with('shop_url',$shop_url)->with('date_ranges',$date_ranges);
	}

	public function create(Request $request , $shop_url){
		$date_range = new DateRange() ;
   	return view('shop.date-range.edit')->with('shop_url',$shop_url)->with('date_range',$date_range) ;
	}

	public function store(Request $request , $shop_url){

  	$this->validate($request, [
        'name' => 'required',
        'start_date' => 'required',
        'end_date' => 'required',
    ]);
  	$company = Company::byUrl($shop_url)->first() ;

    $date_range = new DateRange();
    $date_range->name = $request->input('name');
    $date = Carbon::createFromFormat('Y-m-d H:i', $request->input('start_date'),'Asia/Bangkok');
		$date->setTimezone('UTC');
  	$date_range->start_date =  $date->toDateTimeString() ;
  	$date = Carbon::createFromFormat('Y-m-d H:i', $request->input('end_date'),'Asia/Bangkok');
		$date->setTimezone('UTC');
  	$date_range->end_date =  $date->toDateTimeString() ;
  	$date_range->company_id = $company->id ;
  	$date_range->save() ;

  	return redirect($shop_url.'/date-ranges')->with('status', 'Success save new date range');
  }


  public function edit(Request $request , $shop_url , $id){
    $date_range = DateRange::where('id',$id)->first();
    return view('shop.date-range.edit')->with('shop_url',$shop_url)->with('date_range',$date_range) ;
  }

  public function update(Request $request , $shop_url , $id){
    $date_range = DateRange::where('id',$id)->first();

    $this->validate($request, [
        'name' => 'required',
        'start_date' => 'required',
        'end_date' => 'required',
    ]);

    $date_range->name = $request->input('name');
    $date = Carbon::createFromFormat('Y-m-d H:i', $request->input('start_date'),'Asia/Bangkok');
		$date->setTimezone('UTC');
  	$date_range->start_date =  $date->toDateTimeString() ;
  	$date = Carbon::createFromFormat('Y-m-d H:i', $request->input('end_date'),'Asia/Bangkok');
		$date->setTimezone('UTC');
  	$date_range->end_date =  $date->toDateTimeString() ;
  	$date_range->save() ;

  	return redirect($shop_url.'/date-ranges')->with('status', 'Success update date range');
  }
}