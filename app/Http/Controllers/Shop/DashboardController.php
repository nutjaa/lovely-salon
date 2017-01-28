<?php namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller ;
use Illuminate\Http\Request;

class DashboardController extends Controller{
	public function index(Request $request , $shop_url){
		return view('shop.dashboard')->with('shop_url',$shop_url);
	}
}