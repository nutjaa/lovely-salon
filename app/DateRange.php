<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon ;

class DateRange extends Model{

	protected $table = 'date_ranges';
	protected $dates = [
    'created_at',
    'updated_at',
    'start_date',
    'end_date'
  ];

  /****** SCOPE ******/
  public function scopeByCompany($query,$company_id){
		return $query->where('company_id',$company_id);
	}

	/***** ATTRIBUTE ****/
	public function getStartFormatAttribute(){
		if(!is_null($this->start_date)){
			$date = Carbon::createFromFormat('Y-m-d H:i:s', $this->start_date);
		}else{
			$date = Carbon::now();
		}

		$date->setTimezone('Asia/Bangkok');
		return $date->format('Y-m-d H:i');
  }
  public function getEndFormatAttribute(){
		if(!is_null($this->end_date)){
			$date = Carbon::createFromFormat('Y-m-d H:i:s', $this->end_date);
		}else{
			$date = Carbon::now();
		}
		$date->setTimezone('Asia/Bangkok');
		return   $date->format('Y-m-d H:i');
  }

  public function getStartDayFormatAttribute(){
		if(!is_null($this->start_date)){
			$date = Carbon::createFromFormat('Y-m-d H:i:s', $this->start_date);
		}else{
			$date = Carbon::now();
		}

		$date->setTimezone('Asia/Bangkok');
		return $date->format('d');
  }
  public function getEndDayFormatAttribute(){
		if(!is_null($this->end_date)){
			$date = Carbon::createFromFormat('Y-m-d H:i:s', $this->end_date->subDay());
		}else{
			$date = Carbon::now();
		}
		$date->setTimezone('Asia/Bangkok');
		return   $date->format('d');
  }

}