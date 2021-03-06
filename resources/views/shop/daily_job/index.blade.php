@extends('layouts.backend-with-sidemenu')

@section('page-level-styles')
	<link href="/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css" rel="stylesheet" type="text/css" />
	<link href="/assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet" type="text/css" />
@endsection

@section('breadcrumbs')
<li>
    <a href="#">{{ trans('breadcrumb.shops') }}</a>
</li>
<li>{{ trans('breadcrumb.daily-task') }}</li>
@endsection

@section('content')

<div class="portlet light portlet-fit portlet-datatable bordered">
	<div class="portlet-title">
		<div class="caption">
      <i class="icon-list font-green"></i>
      <span class="caption-subject font-green sbold uppercase"> {{ trans('breadcrumb.daily-task') }} </span>
    </div>
    <div class="actions">
       <a href="{{ url($shop_url . '/daily-jobs/create?task_at='.$selected_date) }}" class="btn btn-transparent blue btn-outline btn-circle btn-sm">{{ trans('daily-task.create-new') }}</a>
    </div>
	</div>
	<div class="portlet-body">
		<div class="row">
			<div class="col-md-4 col-md-offset-4">
				<input type="text" value="{!! $selected_date !!}" size="16" name="date" readonly="true" class="form-control form-control-inline input-medium date-picker">
			</div>
		</div>
		<br/>
			<div class="table-responsive">
				<table class="table table-striped table-bordered table-hover ">
					<thead>
						<tr>
						@foreach($queue_employees as $queue_employee)
							<th style="width:100px;"><a href="{{ url($shop_url . '/daily-jobs/' . $queue_employee->id) }}">{!! $queue_employee->employee->name !!}</a>&nbsp;&nbsp;&nbsp;<a href="{{ url($shop_url . '/daily-jobs/create?task_at='.$selected_date.'&employee_id='.$queue_employee->employee->id) }}"><i class="icon-plus icons"></i></a></th>
						@endforeach
						</tr>
					</thead>
					<tbody>
						@foreach($rows as $row)
						<tr>
							@foreach($employee_queue_ids as $employee_queue_id)
								@if(isset($row[$employee_queue_id]))
									<td class="task-col @if($row[$employee_queue_id]->is_loyal_customer) success @endif @if($row[$employee_queue_id]->amount < 0) warning @endif @if(in_array($row[$employee_queue_id]->task_id,$ot_task)) info @endif" data-id="{{ $row[$employee_queue_id]->id }}">
										<table width="100%" >
											<tr>
												<td width="50%">{!! $row[$employee_queue_id]->task->name !!}</td>
												<td width="50%" class="text-right">{!! $row[$employee_queue_id]->amount !!}</td>
											</tr>
											@if($row[$employee_queue_id]->description)
											<tr>
												<td colspan="2"><small>{{ $row[$employee_queue_id]->description }}</small></td>
											</tr>
											@endif
										</table>
									</td>
								@else
									<td>&nbsp;</td>
								@endif
							@endforeach
						</tr>
						@endforeach
					</tbody>
					<tfoot>
						<tr>
							@foreach($employee_queue_ids as $employee_queue_id)
								@if(isset($summary[$employee_queue_id]))
									<td>
										<table width="100%">
											<tr>
												<td width="50%">รวม</td>
												<td width="50%" class="text-right">{!! $summary[$employee_queue_id] !!}</td>
											</tr>
										</table>
									</td>
								@else
									<td>&nbsp;</td>
								@endif
							@endforeach
						</tr>
					</tfoot>
				</table>
			</div>
	</div>
</div>

@endsection

@section('page-level-js')

<!-- BEGIN PAGE LEVEL PLUGINS -->
<script src="/assets/global/plugins/moment.min.js" type="text/javascript"></script>
<script src="/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
<!-- END PAGE LEVEL PLUGINS -->

<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="/assets/pages/scripts/page-daily-jobs.js" type="text/javascript"></script>
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script type="text/javascript">
  var shop_url = '{!! $shop_url !!}' ;

</script>
<!-- END PAGE LEVEL SCRIPTS -->

@endsection