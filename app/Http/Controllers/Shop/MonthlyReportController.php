<?php namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller ;
use Illuminate\Http\Request;

use App\DateRange ;
use App\Option ;
use App\Employee;
use App\DailyJob;
use App\TaskPercent ;
use App\Company;

use Carbon\Carbon ;

Use DB;
use Excel;

class MonthlyReportController extends Controller{

  private function processAll1($date_range_id){
    $date_range = DateRange::find($date_range_id);

    $task = Option::byOptionType('employee1_task_monthly')->first();
    $task_list = Option::whereIn('id',explode(',', $task->name))->orderBy('ordering','asc')->get() ;

    $employees = Employee::byType1()->orderBy('name','asc')->get();
    $employee_ids = array();
    foreach($employees as $employee){
      $employee_ids[] = $employee->id ;
    }

    $daily_jobs = DailyJob::where('task_at','>',$date_range->start_date)->where('task_at','<',$date_range->end_date)->whereIn('employee_id',$employee_ids)->whereIn('task_id',explode(',', $task->name))->orderBy('task_at','ASC')->orderBy('id', 'ASC')->get();

    $results = array();
    foreach($employees as $employee){
      $data = array() ;
      $data['employee'] = $employee ;
      $data['data'] = array() ;
      $data['summary_count'] = 0 ;
      $data['summary_amount'] = 0 ;
      $data['summary_percent'] = 0 ;
      foreach($task_list as $task){
        $data['data'][$task->id] = array('count' => 0 , 'amount' => 0 , 'percent' => 0);
      }

      foreach($daily_jobs as $daily_job){
        if($daily_job->employee_id != $employee->id){
          continue ;
        }
        $data['data'][$daily_job->task_id]['count']++ ;
        $data['data'][$daily_job->task_id]['amount'] += $daily_job->amount ;
        $data['summary_amount'] += $daily_job->amount ;
        $data['summary_count']++ ;
      }

      #calculate percent ;
      foreach($data['data'] as $task_id => &$value){
        $task_percent = TaskPercent::byTask($task_id)->first();
        if(is_null($task_percent)){
          $value['percent'] = 0 ;
        }else{
          $value['percent'] += $value['amount'] * $task_percent->percent / 100 ;
        }
        $data['summary_percent'] += $value['percent'];
      }


      $results[] = $data ;
    }


    $summary_by_task = array() ;
    $summary_by_task['all'] = 0 ;
    foreach($task_list as $task){
      $summary_by_task[$task->id] = 0;
    }

    foreach($results as $result){
      foreach($result['data'] as $task_id => $value){
        $summary_by_task[$task_id] += $value['percent'];
        $summary_by_task['all'] += $value['percent'];
      }
    }

    return [
      'task_list' => $task_list ,
      'results' => $results ,
      'summary_by_task' => $summary_by_task
    ];
  }

  public function all1(Request $request , $shop_url){
    $date_ranges = DateRange::orderBy('start_date','desc')->pluck('name','id');
    $date_range_id = $request->input('date_range_id',0);
    if($date_range_id == 0){
      foreach ($date_ranges as $key => $value) {
        $date_range_id = $key ;
        break ;
      }
      return redirect($shop_url.'/monthly-all-employee1?date_range_id=' . $date_range_id);
    }

    $data = $this->processAll1($date_range_id);


    return view('shop.monthly-summary.all1')->with('shop_url',$shop_url)->with('date_ranges',$date_ranges)->with('date_range_id',$date_range_id)->with('task_list',$data['task_list'])->with('results',$data['results'])->with('summary_by_task',$data['summary_by_task']);
  }

  public function  all1Export(Request $request , $shop_url){
    $date_range_id = $request->input('date_range_id',0);
    $data = $this->processAll1($date_range_id);
    $date_range = DateRange::find($date_range_id);
    $data['date_range'] = $date_range ;
    Excel::create('monthly-all1-' . $date_range->start_date->format('Y-m-d') . '-' . $date_range->end_date->format('Y-m-d'), function($excel) use ($data){
      $excel->sheet('export', function($sheet) use ($data) {
        $all_cols = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF','AG','AH'];
        $sheet->setOrientation('landscape');
        $sheet->setWidth(array(
          'A'=>9,'B'=>6,'C'=>6,'D'=>6,'E'=>6, 'F' => 6 , 'G' => 6 , 'H' => 6 , 'I' => 6 , 'J' => 6 , 'K' => 6 , 'L' => 6,'M'=>6,'N'=>6,'O'=>6,'P'=>6,'Q'=>6,'R'=>6,'S'=>6,'T'=>6,'U'=>6,'V'=>6,'W'=>6,'X'=>6,'Y'=>6,'Z'=>6,'AA'=>6,'AB'=>6,'AC'=>6,'AD'=>6,'AE'=>6,'AF'=>6,'AG'=>6,'AH'=>6
        ));
        $sheet->cell('A1', function($cell) {
          $cell->setValue('ใบรายงานพนักงาน(ช่างซอย)ประจำเดือน');
          $cell->setFontWeight('bold');
        });
        $sheet->cell('A2', function($cell) use ($data) {
          $cell->setValue($data['date_range']->name);
          $cell->setFontWeight('bold');
        });
        $sheet->mergeCells('A1:E1');
        $sheet->mergeCells('A2:E2');

        // header
        $header = [] ;
        $header[] = 'ฃื่อพนัก' ;
        foreach($data['task_list'] as $task ){
          $header[] = $task->name ;
          $header[] = '';
          $header[] = '';
        }
        $header[] = 'รวมเป็นเงิน';
        $sheet->row(4,$header);
        $merge_cells = ['B4:D4','E4:G4','H4:J4','K4:M4','N4:P4','Q4:S4','T4:V4','W4:Y4','Z4:AB4','AC4:AE4','AF4:AH4'];
        foreach($merge_cells as $merge_cell){
          $sheet->mergeCells($merge_cell);
        }

        $sheet->cells('B4:AH4', function($cells) {
          $cells->setAlignment('center');
          $cells->setFontWeight('bold');
        });

        $sheet->cell('A4', function($cell) {
          $cell->setFontWeight('bold');
        });

        // sub header
        $subheader = [] ;
        $subheader[] = 'งาน';
        foreach($data['task_list'] as $task ){
          $subheader[] = 'ครั้ง' ;
          $subheader[] = 'บาท';
          $subheader[] = '%' ;
        }
        $subheader[] = 'ครั้ง' ;
        $subheader[] = 'บาท';
        $subheader[] = '%' ;
        $sheet->row(5,$subheader);

        $sheet->cells('B5:AH5', function($cells) {
          $cells->setAlignment('center');
          $cells->setFontWeight('bold');
        });

        $sheet->cell('A5', function($cell) {
          $cell->setFontWeight('bold');
        });

        // data
        $row_index = 6 ;
        foreach($data['results'] as $result){
          $row_data = [] ;
          $row_data[] = $result['employee']->name ;
          foreach($data['task_list'] as $task){
            $row_data[] =$result['data'][$task->id]['count'] ;
            $row_data[] =$result['data'][$task->id]['amount'] ;
            $row_data[] =$result['data'][$task->id]['percent'] ;
          }
          $row_data[] = $result['summary_count'] ;
          $row_data[] = $result['summary_amount'] ;
          $row_data[] = $result['summary_percent'] ;
          $sheet->row($row_index,$row_data);
          $row_index++ ;
        }

         // Last row
        $merge_cells = ['B'.$row_index.':D'.$row_index,'E'.$row_index.':G'.$row_index,'H'.$row_index.':J'.$row_index,'K'.$row_index.':M'.$row_index,'N'.$row_index.':P'.$row_index,'Q'.$row_index.':S'.$row_index,'T'.$row_index.':V'.$row_index,'W'.$row_index.':Y'.$row_index,'Z'.$row_index.':AB'.$row_index,'AC'.$row_index.':AE'.$row_index,'AF'.$row_index.':AH'.$row_index];
        foreach($merge_cells as $merge_cell){
          $sheet->mergeCells($merge_cell);
        }

        $last_row = [] ;
        $last_row[] = 'รวม' ;
        foreach($data['task_list'] as $task ){
          $last_row[] = $data['summary_by_task'][$task->id];
          $last_row[] = '' ;
          $last_row[] = '' ;
        }
        $last_row[] = $data['summary_by_task']['all'];
        $last_row[] = '' ;
        $last_row[] = '' ;

        $sheet->row($row_index,$last_row);

        $sheet->cells('B'.$row_index.':AH'.$row_index, function($cells) {
          $cells->setAlignment('right');
          $cells->setFontWeight('bold');
        });

        $sheet->cell('A'.$row_index, function($cell) {
          $cell->setFontWeight('bold');
        });

        $sheet->setHeight(1, 20);
        $sheet->setHeight(2, 20);
        for($i = 4 ; $i <= $row_index ; $i++){
          $sheet->setHeight($i, 20);
          foreach($all_cols as $col){
            $sheet->setBorder($col.$i, 'thin');
          }
        }

      })->export('xls');
    });
  }

  private function processSingle1($employee_id,$date_range_id){
    $task = Option::byOptionType('employee1_task_monthly')->first();
    $task_list = Option::whereIn('id',explode(',', $task->name))->orderBy('ordering','asc')->get() ;

    $date_range = DateRange::find($date_range_id);
    $start_day = (int)$date_range->start_day_format ;
    $end_day = (int)$date_range->end_day_format ;

    $daily_jobs = DailyJob::where('task_at','>',$date_range->start_date)->where('task_at','<',$date_range->end_date)->byEmployee($employee_id)->whereIn('task_id',explode(',', $task->name))->orderBy('task_at','ASC')->orderBy('id', 'ASC')->get();

    $results = [] ;
    for($i = $start_day ; $i <= $end_day ; $i++){
      $data = [] ;
      $data['day'] = $i ;
      $data['summary_count'] = 0 ;
      $data['summary_amount'] = 0 ;
      $data['data'] = [];
      foreach($task_list as $task){
        $data['data'][$task->id] = array('count' => 0 , 'amount' => 0 );
      }

      foreach($daily_jobs as $daily_job){
        if((int)$daily_job->task_at_day_format != $i){
          continue ;
        }

        $data['data'][$daily_job->task_id]['count']++ ;
        $data['data'][$daily_job->task_id]['amount'] += $daily_job->amount ;
        $data['summary_amount'] += $daily_job->amount ;
        $data['summary_count']++ ;
      }
      $results[] = $data ;
    }

    $summary_by_task = array() ;
    $summary_by_task['all_amount'] = 0 ;
    $summary_by_task['all_count'] = 0 ;
    foreach($task_list as $task){
      $summary_by_task[$task->id] = ['count' => 0 , 'amount' => 0] ;
    }

    foreach($results as $result){
      foreach($result['data'] as $task_id => $value){
        $summary_by_task[$task_id]['amount'] += $value['amount'];
        $summary_by_task[$task_id]['count'] += $value['count'];

        $summary_by_task['all_amount'] += $value['amount'];
        $summary_by_task['all_count'] += $value['count'];
      }
    }

    return [
      'task_list' => $task_list ,
      'results' => $results ,
      'summary_by_task' => $summary_by_task
    ];
  }

  public function single1(Request $request , $shop_url){
    $date_ranges = DateRange::orderBy('start_date','desc')->pluck('name','id');
    $date_range_id = $request->input('date_range_id',0);
    $employee_id = $request->input('employee_id',0);
    if($date_range_id == 0){
      foreach ($date_ranges as $key => $value) {
        $date_range_id = $key ;
        break ;
      }
      return redirect($shop_url.'/monthly-single-employee1?date_range_id=' . $date_range_id);
    }

    $employees = Employee::byType1()->orderBy('name','asc')->pluck('name','id');

    if($employee_id == 0){
      foreach($employees as $key => $employee){
        $employee_id = $key ;
        break;
      }
      return redirect($shop_url.'/monthly-single-employee1?date_range_id=' . $date_range_id . '&employee_id='.$employee_id);
    }

    $data = $this->processSingle1($employee_id , $date_range_id);

    return view('shop.monthly-summary.single1')->with('shop_url',$shop_url)->with('date_ranges',$date_ranges)->with('date_range_id',$date_range_id)->with('employees',$employees)->with('employee_id',$employee_id)->with('task_list',$data['task_list'])->with('results',$data['results'])->with('summary_by_task',$data['summary_by_task']);

  }

  public function single1Export(Request $request , $shop_url){
    $date_range_id = $request->input('date_range_id',0);
    $employee_id = $request->input('employee_id',0);
    $date_range = DateRange::find($date_range_id);
    $employee = Employee::find($employee_id);

    $data = $this->processSingle1($employee_id,$date_range_id);
    $data['date_range'] = $date_range ;
    $data['employee'] = $employee ;

    Excel::create('monthly-single1-'.$employee->name.'-' . $date_range->start_date->format('Y-m-d') . '-' . $date_range->end_date->format('Y-m-d'), function($excel) use ($data){
        $excel->sheet('export', function($sheet) use ($data) {
        $all_cols = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W'];
        $sheet->setOrientation('landscape');
        $sheet->setWidth(array(
          'A'=>6,'B'=>6,'C'=>6,'D'=>6,'E'=>6, 'F' => 6 , 'G' => 6 , 'H' => 6 , 'I' => 6 , 'J' => 6 , 'K' => 6 , 'L' => 6,'M'=>6,'N'=>6,'O'=>6,'P'=>6,'Q'=>6,'R'=>6,'S'=>6,'T'=>6,'U'=>6,'V'=>6,'W'=>6
        ));
        $sheet->cell('A1', function($cell) {
          $cell->setValue('ใบรายงานพนักงาน(ช่างซอย)ประจำเดือน');
          $cell->setFontWeight('bold');
        });
        $sheet->cell('F1', function($cell) use ($data) {
          $cell->setValue($data['employee']->name);
          $cell->setFontWeight('bold');
        });
        $sheet->cell('A2', function($cell) use ($data) {
          $cell->setValue($data['date_range']->name);
          $cell->setFontWeight('bold');
        });
        $sheet->mergeCells('A1:E1');
        $sheet->mergeCells('A2:E2');

        // header
        $header = [] ;
        $header[] = 'วันที่' ;
        foreach($data['task_list'] as $task ){
          $header[] = $task->name ;
          $header[] = '';
        }
        $header[] = 'รวม';
        $header[] = '';
        $sheet->row(4,$header);
        $merge_cells = ['B4:C4','D4:E4','F4:G4','H4:I4','J4:K4','L4:M4','N4:O4','P4:Q4','R4:S4','T4:U4','V4:W4'];
        foreach($merge_cells as $merge_cell){
          $sheet->mergeCells($merge_cell);
        }

        $sheet->cells('B4:W4', function($cells) {
          $cells->setAlignment('center');
          $cells->setFontWeight('bold');
        });

        $sheet->cell('A4', function($cell) {
          $cell->setFontWeight('bold');
        });


        // data
        $row_index = 5 ;
        foreach($data['results'] as $result){
          $row_data = [] ;
          $row_data[] = $result['day'] ;
          foreach($data['task_list'] as $task){
            $row_data[] =$result['data'][$task->id]['count'] ;
            $row_data[] =$result['data'][$task->id]['amount'] ;
          }
          $row_data[] = $result['summary_count'] ;
          $row_data[] = $result['summary_amount'] ;
          $sheet->row($row_index,$row_data);
          $row_index++ ;
        }

         // Last row

        $last_row = [] ;
        $last_row[] = 'รวม' ;
        foreach($data['task_list'] as $task ){
          $last_row[] = $data['summary_by_task'][$task->id]['count'];
          $last_row[] = $data['summary_by_task'][$task->id]['amount'];
        }
        $last_row[] = $data['summary_by_task']['all_count'];
        $last_row[] = $data['summary_by_task']['all_amount'];

        $sheet->row($row_index,$last_row);

        $sheet->cells('B'.$row_index.':W'.$row_index, function($cells) {
          $cells->setAlignment('right');
          $cells->setFontWeight('bold');
        });

        $sheet->cell('A'.$row_index, function($cell) {
          $cell->setFontWeight('bold');
        });

        $sheet->setHeight(1, 20);
        $sheet->setHeight(2, 20);
        for($i = 4 ; $i <= $row_index ; $i++){
          $sheet->setHeight($i, 20);
          foreach($all_cols as $col){
            $sheet->setBorder($col.$i, 'thin');
          }
        }

      })->export('xls');
    });
  }

  private function processAll2($monthly_select_id){
    $task = Option::byOptionType('employee2_task_monthly')->first();
    $task_list = Option::whereIn('id',explode(',', $task->name))->orderBy('ordering','asc')->get() ;

    $employees = Employee::byType2()->orderBy('name','asc')->get();
    $employee_ids = array();
    foreach($employees as $employee){
      $employee_ids[] = $employee->id ;
    }

    $start_date = Carbon::createFromFormat('Y-m-d H',$monthly_select_id . ' 0');
    $end_date = Carbon::createFromFormat('Y-m-d H',$monthly_select_id . ' 0');
    $end_date->addMonth();

    $start_date->setTimezone('Asia/Bangkok');
    $end_date->setTimezone('Asia/Bangkok');

    $daily_jobs = DailyJob::where('task_at','>',$start_date)->where('task_at','<',$end_date)->whereIn('employee_id',$employee_ids)->whereIn('task_id',explode(',', $task->name))->orderBy('task_at','ASC')->orderBy('id', 'ASC')->get();

    $results = array();
    foreach($employees as $employee){
      $data = array() ;
      $data['employee'] = $employee ;
      $data['data'] = array() ;
      $data['summary_count'] = 0 ;
      $data['summary_amount'] = 0 ;
      $data['summary_percent'] = 0 ;
      foreach($task_list as $task){
        $data['data'][$task->id] = array('count' => 0 , 'amount' => 0 , 'percent' => 0);
      }

      foreach($daily_jobs as $daily_job){
        if($daily_job->employee_id != $employee->id){
          continue ;
        }
        $data['data'][$daily_job->task_id]['count']++ ;
        $data['data'][$daily_job->task_id]['amount'] += $daily_job->amount ;
        $data['summary_amount'] += $daily_job->amount ;
        $data['summary_count']++ ;
      }

      #calculate percent ;
      foreach($data['data'] as $task_id => &$value){
        $value['percent'] += $value['amount'] * 10 / 100 ;
        $data['summary_percent'] += $value['percent'];
      }


      $results[] = $data ;
    }


    $summary_by_task = array() ;
    $summary_by_task['all'] = 0 ;
    foreach($task_list as $task){
      $summary_by_task[$task->id] = 0;
    }

    foreach($results as $result){
      foreach($result['data'] as $task_id => $value){
        $summary_by_task[$task_id] += $value['percent'];
        $summary_by_task['all'] += $value['percent'];
      }
    }

    return [
      'task_list' => $task_list ,
      'results' => $results ,
      'summary_by_task' => $summary_by_task
    ];
  }

  public function all2(Request $request , $shop_url){
    $monthly_select_id = $request->input('monthly_select_id',0);
    $date = Carbon::now();
    $monthly_selector = [] ;
    while($date->format('Y') >= 2017 ){
      $monthly_selector[$date->format('Y-m-01')] = $date->format('F Y') ;
      $date->subMonth() ;
    }

    if($monthly_select_id == 0){
      foreach ($monthly_selector as $key => $value) {
        $monthly_select_id = $key ;
        break ;
      }
      return redirect($shop_url.'/monthly-all-employee2?monthly_select_id=' . $monthly_select_id);
    }


    $data = $this->processAll2($monthly_select_id);

    return view('shop.monthly-summary.all2')->with('shop_url',$shop_url)->with('monthly_selector',$monthly_selector)->with('monthly_select_id',$monthly_select_id)->with('task_list',$data['task_list'])->with('results',$data['results'])->with('summary_by_task',$data['summary_by_task']);
  }

  public function all12Export(Request $request , $shop_url){
    $monthly_select_id = $request->input('monthly_select_id',0);
    $data = $this->processAll2($monthly_select_id);

    Excel::create('monthly-all2-' . $monthly_select_id  , function($excel) use ($data,$monthly_select_id){
      $excel->sheet('export', function($sheet) use ($data,$monthly_select_id) {
        $all_cols = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y'];
        $sheet->setOrientation('landscape');
        $sheet->setWidth(array(
          'A'=>9,'B'=>6,'C'=>6,'D'=>6,'E'=>6, 'F' => 6 , 'G' => 6 , 'H' => 6 , 'I' => 6 , 'J' => 6 , 'K' => 6 , 'L' => 6,'M'=>6,'N'=>6,'O'=>6,'P'=>6,'Q'=>6,'R'=>6,'S'=>6,'T'=>6,'U'=>6,'V'=>6,'W'=>6,'X'=>6,'Y'=>6
        ));
        $sheet->cell('A1', function($cell) {
          $cell->setValue('ใบรายงานพนักงาน(ช่างสระไดร์)ประจำเดือน');
          $cell->setFontWeight('bold');
        });
        $sheet->cell('A2', function($cell) use ($monthly_select_id) {
          $cell->setValue($monthly_select_id);
          $cell->setFontWeight('bold');
        });
        $sheet->mergeCells('A1:E1');
        $sheet->mergeCells('A2:E2');

        // header
        $header = [] ;
        $header[] = 'ฃื่อพนัก' ;
        foreach($data['task_list'] as $task ){
          $header[] = $task->name ;
          $header[] = '';
          $header[] = '';
        }
        $header[] = 'รวมเป็นเงิน';
        $sheet->row(4,$header);
        $merge_cells = ['B4:D4','E4:G4','H4:J4','K4:M4','N4:P4','Q4:S4','T4:V4','W4:Y4'];
        foreach($merge_cells as $merge_cell){
          $sheet->mergeCells($merge_cell);
        }

        $sheet->cells('B4:Y4', function($cells) {
          $cells->setAlignment('center');
          $cells->setFontWeight('bold');
        });

        $sheet->cell('A4', function($cell) {
          $cell->setFontWeight('bold');
        });

        // sub header
        $subheader = [] ;
        $subheader[] = 'งาน';
        foreach($data['task_list'] as $task ){
          $subheader[] = 'ครั้ง' ;
          $subheader[] = 'บาท';
          $subheader[] = '%' ;
        }
        $subheader[] = 'ครั้ง' ;
        $subheader[] = 'บาท';
        $subheader[] = '%' ;
        $sheet->row(5,$subheader);

        $sheet->cells('B5:Y5', function($cells) {
          $cells->setAlignment('center');
          $cells->setFontWeight('bold');
        });

        $sheet->cell('A5', function($cell) {
          $cell->setFontWeight('bold');
        });

        // data
        $row_index = 6 ;
        foreach($data['results'] as $result){
          $row_data = [] ;
          $row_data[] = $result['employee']->name ;
          foreach($data['task_list'] as $task){
            $row_data[] =$result['data'][$task->id]['count'] ;
            $row_data[] =$result['data'][$task->id]['amount'] ;
            $row_data[] =$result['data'][$task->id]['percent'] ;
          }
          $row_data[] = $result['summary_count'] ;
          $row_data[] = $result['summary_amount'] ;
          $row_data[] = $result['summary_percent'] ;
          $sheet->row($row_index,$row_data);
          $row_index++ ;
        }

         // Last row
        $merge_cells = ['B'.$row_index.':D'.$row_index,'E'.$row_index.':G'.$row_index,'H'.$row_index.':J'.$row_index,'K'.$row_index.':M'.$row_index,'N'.$row_index.':P'.$row_index,'Q'.$row_index.':S'.$row_index,'T'.$row_index.':V'.$row_index,'W'.$row_index.':Y'.$row_index];
        foreach($merge_cells as $merge_cell){
          $sheet->mergeCells($merge_cell);
        }

        $last_row = [] ;
        $last_row[] = 'รวม' ;
        foreach($data['task_list'] as $task ){
          $last_row[] = $data['summary_by_task'][$task->id];
          $last_row[] = '' ;
          $last_row[] = '' ;
        }
        $last_row[] = $data['summary_by_task']['all'];
        $last_row[] = '' ;
        $last_row[] = '' ;

        $sheet->row($row_index,$last_row);

        $sheet->cells('B'.$row_index.':Y'.$row_index, function($cells) {
          $cells->setAlignment('right');
          $cells->setFontWeight('bold');
        });

        $sheet->cell('A'.$row_index, function($cell) {
          $cell->setFontWeight('bold');
        });

        $sheet->setHeight(1, 20);
        $sheet->setHeight(2, 20);
        for($i = 4 ; $i <= $row_index ; $i++){
          $sheet->setHeight($i, 20);
          foreach($all_cols as $col){
            $sheet->setBorder($col.$i, 'thin');
          }
        }

      })->export('xls');
    });
  }

  private function processSingle2($employee_id,$monthly_select_id){
    $task = Option::byOptionType('employee2_task_monthly')->first();
    $task_list = Option::whereIn('id',explode(',', $task->name))->orderBy('ordering','asc')->get() ;


    $start_date = Carbon::createFromFormat('Y-m-d H',$monthly_select_id . ' 0');
    $end_date = Carbon::createFromFormat('Y-m-d H',$monthly_select_id . ' 0');
    $end_date->addMonth();

    $daily_jobs = DailyJob::where('task_at','>',$start_date)->where('task_at','<',$end_date)->byEmployee($employee_id)->whereIn('task_id',explode(',', $task->name))->orderBy('task_at','ASC')->orderBy('id', 'ASC')->get();

    $start_day = $start_date->format('d');
    $end_day = $end_date->subDay()->format('d');

    $results = [] ;
    for($i = $start_day ; $i <= $end_day ; $i++){
      $data = [] ;
      $data['day'] = $i ;
      $data['summary_count'] = 0 ;
      $data['summary_amount'] = 0 ;
      $data['data'] = [];
      foreach($task_list as $task){
        $data['data'][$task->id] = array('count' => 0 , 'amount' => 0 );
      }

      foreach($daily_jobs as $daily_job){
        if((int)$daily_job->task_at_day_format != $i){
          continue ;
        }

        $data['data'][$daily_job->task_id]['count']++ ;
        $data['data'][$daily_job->task_id]['amount'] += $daily_job->amount ;
        $data['summary_amount'] += $daily_job->amount ;
        $data['summary_count']++ ;
      }
      $results[] = $data ;
    }

    $summary_by_task = array() ;
    $summary_by_task['all_amount'] = 0 ;
    $summary_by_task['all_count'] = 0 ;
    foreach($task_list as $task){
      $summary_by_task[$task->id] = ['count' => 0 , 'amount' => 0] ;
    }

    foreach($results as $result){
      foreach($result['data'] as $task_id => $value){
        $summary_by_task[$task_id]['amount'] += $value['amount'];
        $summary_by_task[$task_id]['count'] += $value['count'];

        $summary_by_task['all_amount'] += $value['amount'];
        $summary_by_task['all_count'] += $value['count'];
      }
    }

    return [
      'task_list' => $task_list ,
      'results' => $results ,
      'summary_by_task' => $summary_by_task
    ];
  }

  public function single2(Request $request , $shop_url){
    $monthly_select_id = $request->input('monthly_select_id',0);
    $employee_id = $request->input('employee_id',0);
    $date = Carbon::now();
    $monthly_selector = [] ;
    while($date->format('Y') >= 2017 ){
      $monthly_selector[$date->format('Y-m-01')] = $date->format('F Y') ;
      $date->subMonth() ;
    }

    if($monthly_select_id == 0){
      foreach ($monthly_selector as $key => $value) {
        $monthly_select_id = $key ;
        break ;
      }
      return redirect($shop_url.'/monthly-single-employee2?monthly_select_id=' . $monthly_select_id);
    }

    $employees = Employee::byType2()->orderBy('name','asc')->pluck('name','id');

    if($employee_id == 0){
      foreach($employees as $key => $employee){
        $employee_id = $key ;
        break;
      }
      return redirect($shop_url.'/monthly-single-employee2?monthly_select_id=' . $monthly_select_id . '&employee_id='.$employee_id);
    }

    $data = $this->processSingle2($employee_id,$monthly_select_id);

    return view('shop.monthly-summary.single2')->with('shop_url',$shop_url)->with('monthly_selector',$monthly_selector)->with('monthly_select_id',$monthly_select_id)->with('employees',$employees)->with('employee_id',$employee_id)->with('task_list',$data['task_list'])->with('results',$data['results'])->with('summary_by_task',$data['summary_by_task']);
  }

  public function single2Export(Request $request , $shop_url){
    $monthly_select_id = $request->input('monthly_select_id',0);
    $employee_id = $request->input('employee_id',0);
    $employee = Employee::find($employee_id);

    $data = $this->processSingle2($employee_id,$monthly_select_id);
    $data['employee'] = $employee ;
    $data['monthly_select_id'] = $monthly_select_id ;

    Excel::create('monthly-single2-'.$employee->name.'-' . $monthly_select_id, function($excel) use ($data){
        $excel->sheet('export', function($sheet) use ($data) {
        $all_cols = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q'];
        $sheet->setOrientation('landscape');
        $sheet->setWidth(array(
          'A'=>6,'B'=>6,'C'=>6,'D'=>6,'E'=>6, 'F' => 6 , 'G' => 6 , 'H' => 6 , 'I' => 6 , 'J' => 6 , 'K' => 6 , 'L' => 6,'M'=>6,'N'=>6,'O'=>6,'P'=>6,'Q'=>6
        ));
        $sheet->cell('A1', function($cell) {
          $cell->setValue('ใบรายงานพนักงาน(ช่างสระไดร์)ประจำเดือน');
          $cell->setFontWeight('bold');
        });
        $sheet->cell('F1', function($cell) use ($data) {
          $cell->setValue($data['employee']->name);
          $cell->setFontWeight('bold');
        });
        $sheet->cell('A2', function($cell) use ($data) {
          $cell->setValue($data['monthly_select_id']);
          $cell->setFontWeight('bold');
        });
        $sheet->mergeCells('A1:E1');
        $sheet->mergeCells('A2:E2');

        // header
        $header = [] ;
        $header[] = 'วันที่' ;
        foreach($data['task_list'] as $task ){
          $header[] = $task->name ;
          $header[] = '';
        }
        $header[] = 'รวม';
        $header[] = '';
        $sheet->row(4,$header);
        $merge_cells = ['B4:C4','D4:E4','F4:G4','H4:I4','J4:K4','L4:M4','N4:O4','P4:Q4'];
        foreach($merge_cells as $merge_cell){
          $sheet->mergeCells($merge_cell);
        }

        $sheet->cells('B4:Q4', function($cells) {
          $cells->setAlignment('center');
          $cells->setFontWeight('bold');
        });

        $sheet->cell('A4', function($cell) {
          $cell->setFontWeight('bold');
        });


        // data
        $row_index = 5 ;
        foreach($data['results'] as $result){
          $row_data = [] ;
          $row_data[] = $result['day'] ;
          foreach($data['task_list'] as $task){
            $row_data[] =$result['data'][$task->id]['count'] ;
            $row_data[] =$result['data'][$task->id]['amount'] ;
          }
          $row_data[] = $result['summary_count'] ;
          $row_data[] = $result['summary_amount'] ;
          $sheet->row($row_index,$row_data);
          $row_index++ ;
        }

         // Last row

        $last_row = [] ;
        $last_row[] = 'รวม' ;
        foreach($data['task_list'] as $task ){
          $last_row[] = $data['summary_by_task'][$task->id]['count'];
          $last_row[] = $data['summary_by_task'][$task->id]['amount'];
        }
        $last_row[] = $data['summary_by_task']['all_count'];
        $last_row[] = $data['summary_by_task']['all_amount'];

        $sheet->row($row_index,$last_row);

        $sheet->cells('B'.$row_index.':Q'.$row_index, function($cells) {
          $cells->setAlignment('right');
          $cells->setFontWeight('bold');
        });

        $sheet->cell('A'.$row_index, function($cell) {
          $cell->setFontWeight('bold');
        });

        $sheet->setHeight(1, 20);
        $sheet->setHeight(2, 20);
        for($i = 4 ; $i <= $row_index ; $i++){
          $sheet->setHeight($i, 20);
          foreach($all_cols as $col){
            $sheet->setBorder($col.$i, 'thin');
          }
        }

      })->export('xls');
    });
  }

  public function salary(Request $request , $shop_url){
    $company = Company::byUrl($shop_url)->first() ;

    $date_ranges = DateRange::orderBy('start_date','desc')->pluck('name','id');
    $date_range_id = $request->input('date_range_id',0);

    if($date_range_id == 0){
      foreach ($date_ranges as $key => $value) {
        $date_range_id = $key ;
        break ;
      }
      return redirect($shop_url.'/monthly-salary?date_range_id=' . $date_range_id);
    }
    $date_range = DateRange::find($date_range_id);

    $task = Option::byOptionType('employee1_task_monthly')->first();
    $task_list = Option::whereIn('id',explode(',', $task->name))->orderBy('ordering','asc')->get() ;

    $employees = Employee::byType1()->orderBy('name','asc')->get();
    $employee_ids = array();
    foreach($employees as $employee){
      $employee_ids[] = $employee->id ;
    }

    $daily_jobs = DailyJob::where('task_at','>',$date_range->start_date)->where('task_at','<',$date_range->end_date)->whereIn('employee_id',$employee_ids)->whereIn('task_id',explode(',', $task->name))->orderBy('task_at','ASC')->orderBy('id', 'ASC')->get();

    $second_period = false ;
    if($date_range->start_date->format('d') == 15){
      $second_period = true ;
    }

    $daily_fine_jobs = [] ;
    $daily_late_jobs = [] ;
    if($second_period){
      $task_fine = Option::byOptionType('task_fine')->first();
      $new_start_date = $date_range->end_date->copy()->subMonth() ;
      $daily_fine_jobs = DailyJob::where('task_at','>',$new_start_date)->where('task_at','<',$date_range->end_date)->whereIn('employee_id',$employee_ids)->whereIn('task_id',explode(',', $task_fine->name))->orderBy('task_at','ASC')->orderBy('id', 'ASC')->get();

      $task_late = Option::byOptionType('late_task_list')->first();
      $daily_late_jobs = DailyJob::where('task_at','>',$new_start_date)->where('task_at','<',$date_range->end_date)->whereIn('employee_id',$employee_ids)->whereIn('task_id',explode(',', $task_late->name))->orderBy('task_at','ASC')->orderBy('id', 'ASC')->get();

    }




    $results = array();
    $grand_total = 0 ;
    foreach($employees as $employee){
      $data = array() ;
      $data['employee'] = $employee ;

      $data['summary_amount'] = 0 ;
      $data['summary_percent'] = 0 ;
      $data['fine'] = 0 ;
      $data['late'] = 0 ;
      foreach($task_list as $task){
        $data['data'][$task->id] = array('amount' => 0 , 'percent' => 0);
      }

      foreach($daily_jobs as $daily_job){
        if($daily_job->employee_id != $employee->id){
          continue ;
        }
        $data['data'][$daily_job->task_id]['amount'] += $daily_job->amount ;
        $data['summary_amount'] += $daily_job->amount ;
      }

      foreach($daily_fine_jobs as $daily_job){
        if($daily_job->employee_id != $employee->id){
          continue ;
        }
        $data['fine'] += $daily_job->amount ;
      }

      foreach($daily_late_jobs as $daily_job){
        if($daily_job->employee_id != $employee->id){
          continue ;
        }
        $data['late'] += $daily_job->amount ;
      }

      #calculate percent ;
      foreach($data['data'] as $task_id => &$value){
        $task_percent = TaskPercent::byTask($task_id)->first();
        if(is_null($task_percent)){
          $value['percent'] = 0 ;
        }else{
          $value['percent'] += $value['amount'] * $task_percent->percent / 100 ;
        }
        $data['summary_percent'] += $value['percent'];
      }
      $data['salary'] = ( $employee->base_salary / 2 ) +  $data['summary_percent'] ;
      if($second_period)
        $data['total_receive'] = $data['salary'] + $data['fine'] + $data['late'];
      else
        $data['total_receive'] = $data['salary'] ;
      $grand_total += $data['total_receive'];

      $results[] = $data ;
    }

    ######### EMPLOYEE TYPE 2

    $employees2 = Employee::byType2()->orderBy('name','asc')->get();
    $results2 = array();

    $employee2_ids = array();
    foreach($employees2 as $employee){
      $employee2_ids[] = $employee->id ;
    }

    $task_list = Option::whereIn('id',explode(',', $task->name))->orderBy('ordering','asc')->get() ;

    $daily_fine2_jobs = [] ;
    $daily_percent = [] ;
    if($second_period){
      $task_fine = Option::byOptionType('task_fine')->first();
      $new_start_date = $date_range->end_date->copy()->subMonth() ;
      $daily_fine2_jobs = DailyJob::where('task_at','>',$new_start_date)->where('task_at','<',$date_range->end_date)->whereIn('employee_id',$employee2_ids)->whereIn('task_id',explode(',', $task_fine->name))->orderBy('task_at','ASC')->orderBy('id', 'ASC')->get();

      $task_employee2_monthly = Option::byOptionType('employee2_task_monthly')->first();

      $daily_percent = DailyJob::where('task_at','>',$new_start_date)->where('task_at','<',$date_range->end_date)->whereIn('employee_id',$employee2_ids)->whereIn('task_id',explode(',', $task_employee2_monthly->name))->orderBy('task_at','ASC')->orderBy('id', 'ASC')->get();

      $task_ot = Option::byOptionType('ot_task_list')->first();
      $daily_ot_jobs = DailyJob::where('task_at','>',$new_start_date)->where('task_at','<',$date_range->end_date)->whereIn('employee_id',$employee2_ids)->whereIn('task_id',explode(',', $task_ot->name))->orderBy('task_at','ASC')->orderBy('id', 'ASC')->get();

      $task_late = Option::byOptionType('late_task_list')->first();
      $daily_late_jobs = DailyJob::where('task_at','>',$new_start_date)->where('task_at','<',$date_range->end_date)->whereIn('employee_id',$employee2_ids)->whereIn('task_id',explode(',', $task_late->name))->orderBy('task_at','ASC')->orderBy('id', 'ASC')->get();
    }

    $grand_total2 = 0 ;
    foreach($employees2 as $employee){
      $data = array() ;
      $data['employee'] = $employee ;
      $data['salary'] = ( $employee->base_salary / 2 ) ;
      $data['fine'] = 0 ;
      $data['summary_amount'] = 0 ;
      $data['summary_percent'] = 0 ;
      $data['ot'] = 0 ;
      $data['late'] = 0 ;
      if($second_period){
        foreach($daily_fine2_jobs as $daily_job){
          if($daily_job->employee_id != $employee->id){
            continue ;
          }
          $data['fine'] += $daily_job->amount ;
        }

        foreach($daily_ot_jobs as $daily_job){
          if($daily_job->employee_id != $employee->id){
            continue ;
          }
          $data['ot'] += $daily_job->amount ;
        }

        foreach($daily_late_jobs as $daily_job){
          if($daily_job->employee_id != $employee->id){
            continue ;
          }
          $data['late'] += $daily_job->amount ;
        }
      }

      if($second_period){
        foreach($daily_percent as $daily_job){
          if($daily_job->employee_id != $employee->id){
            continue ;
          }
          $data['summary_amount'] += $daily_job->amount ;
        }
        $data['summary_percent'] = $data['summary_amount'] * 10 / 100 ;
      }


      $data['total_receive'] = $data['salary'];
      if($second_period){
        $data['total_receive'] += $data['fine'] + $data['ot'] + $data['late'] + $data['summary_percent'] ;
      }
      $grand_total2 += $data['total_receive'];

      $results2[] = $data ;
    }

    return view('shop.monthly-summary.salary')->with('shop_url',$shop_url)->with('date_ranges',$date_ranges)->with('date_range_id',$date_range_id)->with('results',$results)->with('grand_total',$grand_total)->with('results2',$results2)->with('grand_total2',$grand_total2)->with('second_period',$second_period);
  }
}