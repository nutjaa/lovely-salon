<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon ;

class DailyJob extends Model{
	protected $table = 'daily_jobs';
	protected $dates = [
    'created_at',
    'updated_at',
    'task_at'
  ];

	/***** RELATION ***/
	public function customer(){
		return $this->belongsTo('App\Customer','company_id');
	}

	public function employee(){
		return $this->belongsTo('App\Employee','employee_id');
	}

	public function task(){
		return $this->belongsTo('App\Option','task_id');
	}

	/***** SCOPE ***/
	public function scopeByTaskDate($query,$task_at){
		$date = new Carbon($task_at);
		return $query->where('task_at','>=' , $date->toDateString() )->where('task_at','<',$date->addDay()->toDateString()) ;
	}
	public function scopeByNoAmount($query){
		return $query->where('amount',0);
	}
	public function scopeByHasAmount($query){
		return $query->where('amount','<>',0);
	}
	public function scopeByEmployee($query,$employee_id){
		return $query->where('employee_id',$employee_id);
	}
	public function scopeByCompany($query,$company_id){
		return $query->where('company_id',$company_id);
	}

	/***** ATTRIBUTE ****/
	public function getTaskAtFormatAttribute(){
		$date = Carbon::createFromFormat('Y-m-d H:i:s', $this->task_at  );
		$date->setTimezone('Asia/Bangkok');
		return $date->format('Y-m-d H:i');
  }
}