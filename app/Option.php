<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
	protected $table = 'options';

	/***** RELATION ***/

	/***** SCOPE *****/
	public function scopeByOptionType($query,$option_type){
		return $query->where('option_type',$option_type);
	}

	public function scopeByName($query,$name){
		return $query->where('name',$name);
	}

	public function scopeByAvailable($query){
		return $query->where('hidden',false)->whereNull('deleted_at ');
	}

	public function scopeByCompanyId($query,$company_id){
		return $query->where('company_id',$company_id);
	}
}
