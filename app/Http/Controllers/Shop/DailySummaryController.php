<?php namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller ;
use Illuminate\Http\Request;

use App\Employee ;
use App\DailyJob ;
use App\Company ;
use App\Option ;
use App\Customer ;

use Carbon\Carbon ;

use DB;
use Excel;

class DailySummaryController extends Controller{

  private function dailySummaryProcess($selected_date , $employee_type){
    if($employee_type == 0){
      $task = Option::byOptionType('employee1_task_list')->first();
    }else{
      $task = Option::byOptionType('employee2_task_list')->first();
    }

    $task_list = Option::whereIn('id',explode(',', $task->name))->orderBy('ordering','asc')->get() ;

    if($employee_type == 0){
      $employees = Employee::byType1()->orderBy('name','asc')->get();
    }else{
      $employees = Employee::byType2()->orderBy('name','asc')->get();
    }
    $employee_ids = array();
    foreach($employees as $employee){
      $employee_ids[] = $employee->id ;
    }
    $daily_jobs = DailyJob::byTaskDate($selected_date)->byHasAmount()->whereIn('employee_id',$employee_ids)->orderBy('task_at','ASC')->orderBy('id', 'ASC')->get();

    $results = array();
    foreach($employees as $employee){
      $data = array() ;
      $data['employee'] = $employee ;
      $data['data'] = array() ;
      $data['summary'] = 0 ;
      foreach($task_list as $task){
        $data['data'][$task->id] = array('count' => 0 , 'amount' => 0);
      }

      foreach($daily_jobs as $daily_job){
        if($daily_job->employee_id != $employee->id){
          continue ;
        }
        if(!isset($data['data'][$daily_job->task_id])){
          continue;
        }
        $data['data'][$daily_job->task_id]['count']++ ;
        $data['data'][$daily_job->task_id]['amount'] += $daily_job->amount ;
        $data['summary'] += $daily_job->amount ;
      }
      $results[] = $data ;
    }

    $summary_by_task = array() ;
    $summary_by_task['all'] = 0 ;
    foreach($task_list as $task){
      $summary_by_task[$task->id] = 0;
    }

    foreach($daily_jobs as $daily_job){
      if(!isset($summary_by_task[$daily_job->task_id])){
        continue;
      }
      $summary_by_task[$daily_job->task_id] += $daily_job->amount;
      $summary_by_task['all'] += $daily_job->amount;
    }

    return [
      'task_list' => $task_list ,
      'results' => $results ,
      'summary_by_task' => $summary_by_task
    ];
  }

	public function index(Request $request , $shop_url){
		$selected_date = $request->input('date',Carbon::today()->toDateString());
    #DB::enableQueryLog();
    $employee_types = ['ช่างซอย','ผู้ช่วยช่าง'] ;
    $employee_type = $request->input('employee_type',0) ;

    $data = $this->dailySummaryProcess($selected_date , $employee_type);

    return view('shop.daily_summary.index')->with('shop_url',$shop_url)->with('selected_date',$selected_date)->with('task_list', $data['task_list'])->with('results',$data['results'])->with('summary_by_task',$data['summary_by_task'])->with('employee_types',$employee_types)->with('employee_type',$employee_type);
  }

  public function export(Request $request , $shop_url ){
    $selected_date = $request->input('date',Carbon::today()->toDateString());
    $employee_type = $request->input('employee_type',0) ;

    $data = $this->dailySummaryProcess($selected_date , $employee_type);

    #print_r($data); die();

    Excel::create('daily-summary-'.$selected_date.'-'.$employee_type, function($excel) use ($data,$employee_type){
      $excel->sheet('export', function($sheet) use ($data,$employee_type) {
        $all_cols = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X'];
        if($employee_type == 1){
          $all_cols = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R'];
        }
        $sheet->setAutoSize(true);
        $sheet->setOrientation('landscape');
        #$sheet->setBorder('A1', 'thin');
        $sheet->setOrientation('landscape');
        #$sheet->setBorder('X1', 'thin');
        $sheet->setWidth(array(
          'A'  =>  10,'B'  => 6,'C' => 6,'D' => 6,'E' => 6 , 'F' => 6 , 'G' => 6 , 'H' => 6 , 'I' => 6 , 'J' => 6 , 'K' => 6 , 'L' => 6,'M'=>6,'N'=>6,'O'=>6,'P'=>6,'Q'=>6,'R'=>6,'S'=>6,'T'=>6,'U'=>6,'V'=>6,'W'=>6,'X'=>6
        ));

        $merge_row1 = ['B1:C1','D1:E1','F1:G1','H1:I1','J1:K1','L1:M1','N1:O1','P1:Q1','R1:S1','T1:U1','V1:W1'];
        if($employee_type == 1){
          $merge_row1 = ['B1:C1','D1:E1','F1:G1','H1:I1','J1:K1','L1:M1','N1:O1','P1:Q1'];
        }
        foreach($merge_row1 as $merge){
          #$sheet->setBorder($merge, 'thin');
          $sheet->mergeCells($merge);
        }



        // First row
        $row1 = [] ;
        $row1[] = 'รายการ';
        foreach($data['task_list'] as $task){
          $row1[] = $task->name ;
          $row1[] = '';
        }
        $row1[] = 'รวม';

        $sheet->row(1,$row1);
        $sheet->row(1, function($row) {
          // call cell manipulation methods
          //$row->setBackground('#A9A9A9');
        });

        $sheet->cells('A1:X1', function($cells) {
          $cells->setFontWeight('bold');
        });

        // Second row
        $row2 = [] ;
        $row2[] = 'พนักงาน';
        foreach($data['task_list'] as $task){
          $row2[] = '() ครั้ง';
          $row2[] = '() บาท';
        }
        $sheet->row(2,$row2);

        $sheet->cells('A2', function($cells) {
          $cells->setFontWeight('bold');
        });

        // data
        $row_index = 3 ;
        foreach($data['results'] as $result){
          $temp = [] ;
          $temp[] = $result['employee']->name ;

          foreach($data['task_list'] as $task){
            if($result['data'][$task->id]['count']){
              $temp[] = $result['data'][$task->id]['count'];
              $temp[] = $result['data'][$task->id]['amount'];
            }else{
              $temp[] = '';
              $temp[] = '';
            }
          }
          $temp[] = $result['summary'] ;
          $sheet->row($row_index,$temp);
          $row_index++;
        }
        // Last row
        $merge_row1 = ['B'.$row_index.':C'.$row_index ,'D'.$row_index.':E'.$row_index,'F'.$row_index.':G'.$row_index,'H'.$row_index.':I'.$row_index,'J'.$row_index.':K'.$row_index,'L'.$row_index.':M'.$row_index,'N'.$row_index.':O'.$row_index,'P'.$row_index.':Q'.$row_index ] ;
        foreach($merge_row1 as $merge){
          $sheet->mergeCells($merge);
        }

        $row_last = [] ;
        $row_last[] = 'รวมเป็นเงิน' ;
        foreach($data['task_list'] as $task){

          $row_last[] = $data['summary_by_task'][$task->id] ;
          $row_last[] = '';
        }
        $row_last[] = $data['summary_by_task']['all'];
        $sheet->row($row_index,$row_last);

        $sheet->cells('A'.$row_index, function($cells) {
          $cells->setFontWeight('bold');
        });

        $sheet->cells('B'.$row_index.':X'.$row_index, function($cells) {
          $cells->setAlignment('right');
        });

        for($i = 1 ; $i <= $row_index ; $i++){
          $sheet->setHeight($i, 20);
          foreach($all_cols as $col){
            $sheet->setBorder($col.$i, 'thin');
          }
        }
      });
    })->export('xls');
  }
}