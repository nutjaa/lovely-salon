<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Option ;

class Employee extends Model{
	protected $table = 'employees';

	/***** RELATION ***/
	public function company(){
		return $this->belongsTo('App\Company', 'company_id');
	}

	/***** SCOPE *****/
	public function scopeByCompanyId($query,$company_id){
		return $query->where('company_id',$company_id);
	}

	/***** ATTRIBUTE *****/
	public function setTasksAttribute($value){
		$this->attributes['tasks'] =  json_encode($value);
	}

	public function getTasksAttribute($value){
		$tasks = json_decode($value,true);
		if(!$tasks){
			return [] ;
		}
		return Option::whereIn('id',$tasks)->get();
	}

	/****** CUSTOM FUNCTION ********/
	public function hasTask($task_id){
		$tasks = $this->tasks ;
		if($tasks == null){
			return false;
		}
		foreach($tasks as $task){
			if($task->id === $task_id){
				return true ;
			}
		}
		return false ;
	}

}