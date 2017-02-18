<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Company extends Model{
	protected $table = 'companies';

	/***** RELATION ***/
	public function company_users(){
		return $this->hasMany('App\ComapnyUser', 'company_id');
	}
	public function employees(){
		return $this->hasMany('App\Employee','company_id');
	}

	/**** SCOPE ****/
	public function scopeByUrl($query,$url){
		return $this->where('url',$url);
	}
}