<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class DailyJob extends Model{
	protected $table = 'daily_jobs';
	protected $dates = [
    'created_at',
    'updated_at',
    'task_at'
  ];

	/***** RELATION ***/
	public function customer(){
		return $this->belongTo('App\Customer','company_id');
	}

	public function employee(){
		return $this->belongTo('App\Employee','employee_id');
	}

	public function task(){
		return $this->belongTo('App\Option','task_id');
	}
}