<?php namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller ;
use Illuminate\Http\Request;

use App\Company;
use App\Customer ;


class CustomerController extends Controller{
	public function listing(Request $request , $shop_url){
	 	return Customer::all()->pluck('name') ;
	}

	public function index(Request $request , $shop_url){
		$company = Company::byUrl($shop_url)->first() ;
		$customers = Customer::byCompanyId($company->id)->paginate(15);
		return view('shop.customer.index')->with('shop_url',$shop_url)->with('customers',$customers);
	}

	public function create($shop_url){
		$customer = new Customer() ;
   	return view('shop.customer.edit')->with('shop_url',$shop_url)->with('customer',$customer) ;
  }


  public function store(Request $request , $shop_url){
  	$this->validate($request, [
        'name' => 'required|max:100',
    ]);

  	$company = Company::byUrl($shop_url)->first() ;

  	$customer = new Customer();
  	$customer->name = $request->input('name');
  	$customer->description = $request->input('description');
  	$customer->phone_no = $request->input('phone_no');
  	$customer->company_id = $company->id ;
  	$customer->save();

  	return redirect($shop_url.'/customers')->with('status', 'Success create new customer!');
  }

  public function edit(Request $request , $shop_url , $id){
  	$customer = Customer::findOrFail($id);
  	return view('shop.customer.edit')->with('shop_url',$shop_url)->with('customer',$customer);
  }

  public function update(Request $request , $shop_url , $id){
  	$this->validate($request, [
        'name' => 'required|max:100',
    ]);

  	$customer = Customer::findOrFail($id);

  	$customer->name = $request->input('name');
  	$customer->description = $request->input('description');
  	$customer->phone_no = $request->input('phone_no');
  	$customer->save();

  	return redirect($shop_url.'/customers')->with('status', 'Success update customer - ' . $customer->name );
  }

}