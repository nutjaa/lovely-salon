<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class CompanyUser extends Model{
	protected $table = 'company_users';

	public function user(){
		return $this->belongsTo('App\User','user_id');
	}

	public function company(){
		return $this->belongsTo('App\Company','company_id');
	}
}