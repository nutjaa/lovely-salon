<?php namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller ;
use Illuminate\Http\Request;

use App\Employee ;
use App\Company ;
use App\Option ;

class EmployeeController extends Controller{
	public function index(Request $request , $shop_url){
		$company = Company::byUrl($shop_url)->first() ;
		$employees = Employee::byCompanyId($company->id)->paginate(15);
		return view('shop.employee.index')->with('shop_url',$shop_url)->with('employees',$employees);
	}

	public function create($shop_url){
		$employee = new Employee() ;
    $tasks = Option::byOptionType('task')->get() ;
   	return view('shop.employee.edit')->with('shop_url',$shop_url)->with('employee',$employee)->with('tasks',$tasks);
  }

  public function store(Request $request , $shop_url){
  	$this->validate($request, [
        'name' => 'required|unique:employees|max:100',
        'position' => 'required' ,
        'base_salary' => 'required|numeric'
    ]);

  	$company = Company::byUrl($shop_url)->first() ;

  	$employee = new Employee();
  	$employee->name = $request->input('name');
  	$employee->description = $request->input('description');
  	$employee->position = $request->input('position');
  	$employee->base_salary = $request->input('base_salary');
  	$employee->company_id = $company->id ;
  	$employee->save();

  	return redirect($shop_url.'/employees')->with('status', 'Success create new employees!');
  }

  public function edit(Request $request , $shop_url , $id){
  	$employee = Employee::findOrFail($id);
    $tasks = Option::byOptionType('task')->get() ;
  	return view('shop.employee.edit')->with('shop_url',$shop_url)->with('employee',$employee)->with('tasks',$tasks);
  }

  public function update(Request $request , $shop_url , $id){
  	$employee = Employee::findOrFail($id);

    $tasks = $request->input('tasks');

  	$employee->name = $request->input('name');
  	$employee->description = $request->input('description');
  	$employee->position = $request->input('position');
  	$employee->base_salary = $request->input('base_salary');
    $employee->tasks = $tasks ;
  	$employee->save();

  	return redirect($shop_url.'/employees')->with('status', 'Success update employee - ' . $employee->name );
  }
}