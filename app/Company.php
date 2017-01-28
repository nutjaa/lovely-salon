<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Company extends Model{
	protected $table = 'companies';

	public function company_users(){
		return $this->hasMany('App\ComapnyUser', 'company_id');
	}
}