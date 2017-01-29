<?php namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller ;
use Illuminate\Http\Request;

use App\Option ;
use App\Company ;

class OptionController extends Controller{
	public function index(Request $request , $shop_url){
		$company = Company::byUrl($shop_url)->first() ;
		$options = Option::byCompanyId($company->id)->paginate(15);
		return view('shop.option.index')->with('shop_url',$shop_url)->with('options',$options);
	}

	public function create($shop_url){
		$option = new Option() ;
   	return view('shop.option.edit')->with('shop_url',$shop_url)->with('option',$option);
  }

  public function store(Request $request , $shop_url){
  	$company = Company::byUrl($shop_url)->first() ;

  	$this->validate($request, [
        'name' => 'required|unique:options,name,NULL,_id,company_id,'.$company->id.'|max:100',
        'option_type' => 'required' ,
    ]);



  	$option = new Option();
  	$option->name = $request->input('name');
  	$option->option_type = $request->input('option_type');
  	$option->company_id = $company->id ;
  	$option->hidden = false ;
  	$option->save();

  	return redirect($shop_url.'/options')->with('status', 'Success create new option!');
  }

  public function edit(Request $request , $shop_url , $id){
  	$option = Option::findOrFail($id);
  	return view('shop.option.edit')->with('shop_url',$shop_url)->with('option',$option);
  }

  public function update(Request $request , $shop_url , $id){
  	$this->validate($request, [
        'name' => 'required|max:100',
        'option_type' => 'required' ,
    ]);

  	$option = Option::findOrFail($id);

  	$option->name = $request->input('name');
  	$option->option_type = $request->input('option_type');
  	$option->save();

  	return redirect($shop_url.'/options')->with('status', 'Success update option - ' . $option->name );
  }
}