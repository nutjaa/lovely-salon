@extends('layouts.backend-with-sidemenu')

@section('page-level-styles')
	<link href="/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css" rel="stylesheet" type="text/css" />
	<link href="/assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet" type="text/css" />
@endsection

@section('breadcrumbs')
<li>
    <a href="#">{{ trans('breadcrumb.shops') }}</a>
</li>
<li>{{ trans('breadcrumb.daily-summary') }}</li>
@endsection

@section('content')

<div class="portlet light portlet-fit portlet-datatable bordered">
	<div class="portlet-title">
		<div class="caption">
      <i class="icon-list font-green"></i>
      <span class="caption-subject font-green sbold uppercase"> {{ trans('breadcrumb.daily-summary') }} </span>
    </div>
    <div class="actions">
    </div>
	</div>
	<div class="portlet-body">
		<form method="GET" class="form-horizontal" role="form" action="{{ url($shop_url . '/daily-summary') }}">
		<div class="row">
			<div class="col-md-4 col-md-offset-4">
				<input type="text" value="{!! $selected_date !!}" size="16" name="date" readonly="true" class="form-control form-control-inline input-medium date-picker">
			</div>
			<div class="col-md-2">
				{{ Form::select('employee_type', $employee_types , $employee_type , ['class' => 'form-control' ,'name' => 'employee_type']) }}
			</div>
			<div class="col-md-2">
				<a href="/olympic-salon/daily-summary/export?date={{$selected_date}}&employee_type={{$employee_type}}" class="btn green">Export</a>
			</div>
		</div>
		</form>
		<br/>
		<div class="table-responsive">
			<table class="table table-striped table-bordered table-hover ">
				<thead>
					<tr>
						<th>รายการ</th>
						@foreach($task_list as $task)
							<th colspan="2">{{ $task->name }}</th>
						@endforeach
						<th>รวม</th>
					</tr>
					<tr>
						<th>พนักงาน</th>
						@foreach($task_list as $task)
							<td>() ครั้ง</td>
							<td>() บาท</td>
						@endforeach
						<td>&nbsp;</td>
					</tr>
				</thead>
				<tbody>
					@foreach($results as $result)
					<tr>
						<td>{{ $result['employee']->name }}</td>
						@foreach($task_list as $task)
							@if($result['data'][$task->id]['count'])
							<td class="text-right">{{ $result['data'][$task->id]['count'] }}</td>
							<td class="text-right">{{ $result['data'][$task->id]['amount'] }}</td>
							@else
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							@endif
						@endforeach
							<td class="text-right">{{ $result['summary'] }}</td>
					</tr>
					@endforeach
				</tbody>
				<tfoot>
					<th>รวมเป็นเงิน</th>
					@foreach($task_list as $task)
						<th class="text-right" colspan="2">{{ $summary_by_task[$task->id] }}</th>
					@endforeach
					<th class="text-right">{{ $summary_by_task['all'] }}</th>
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
<script src="/assets/pages/scripts/page-daily-summary.js" type="text/javascript"></script>
<script type="text/javascript">
  var shop_url = '{!! $shop_url !!}' ;

</script>
<!-- END PAGE LEVEL SCRIPTS -->

@endsection