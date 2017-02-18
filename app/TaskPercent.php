<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class TaskPercent extends Model{

    protected $table = 'task_percents';

    /****** SCOPE ******/
    public function scopeByTask($query,$option_id){
        return $query->where('option_id',$option_id);
    }

}