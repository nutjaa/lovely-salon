<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model{
	protected $table = 'employees';

	/***** RELATION ***/
	public function company(){
		return $this->belongTo('App\Company', 'company_id');
	}

	/***** SCOPE *****/
	public function scopeByCompanyId($query,$company_id){
		return $query->where('company_id',$company_id);
	}

}