<?php namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller ;
use Illuminate\Http\Request;

use App\Customer ;


class CustomerController extends Controller{
	public function listing(Request $request , $shop_url){
	 	return Customer::all()->pluck('name') ;
	}

}