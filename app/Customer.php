<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Option ;

class Customer extends Model{
	protected $table = 'customers';

	/***** RELATION ***/
	public function company(){
		return $this->belongsTo('App\Company', 'company_id');
	}

	/***** SCOPE *****/
	public function scopeByCompanyId($query,$company_id){
		return $query->where('company_id',$company_id);
	}
	public function scopeByName($query,$name){
		return $query->where('name',$name);
	}



}