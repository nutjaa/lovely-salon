@extends('layouts.backend-with-sidemenu')

@section('breadcrumbs')
<li>
    <a href="#">{{ trans('breadcrumb.shops') }}</a>
</li>
<li>{{ trans('breadcrumb.monthly-summary-single1') }}</li>
@endsection

@section('content')

<div class="portlet light portlet-fit portlet-datatable bordered">
	<div class="portlet-title">
		<div class="caption">
      <i class="icon-list font-green"></i>
      <span class="caption-subject font-green sbold uppercase"> {{ trans('breadcrumb.monthly-summary-all1') }} </span>
    </div>
    <div class="actions">
    </div>
	</div>
	<div class="portlet-body">
		<form method="GET" class="form-horizontal" role="form" action="{{ url($shop_url . '/monthly-single-employee1') }}">
			<div class="row">
				<div class="col-md-4 col-md-offset-2">
					{{ Form::select('date_range_id', $date_ranges , $date_range_id , ['class' => 'form-control' ,'name' => 'date_range_id']) }}
				</div>
				<div class="col-md-4">
				{{ Form::select('employee_id', $employees , $employee_id , ['class' => 'form-control' ,'name' => 'employee_id']) }}
				</div>
			</div>
		</form>
		<br/>
		<div class="table-responsive">
			<table class="table table-striped table-bordered table-hover ">
				<thead>
					<tr>
						<th>วันที่</th>
						@foreach($task_list as $task)
						<th colspan="2" class="text-center">{{ $task->name }}</th>
						@endforeach
						<th colspan="2" class="text-center">รวม</th>
					</tr>
				</thead>
				<tbody>
					@foreach($results as $result)
					<tr>
						<td>{{ $result['day'] }}</td>
						@foreach($task_list as $task)
							<td class="text-right">{{ $result['data'][$task->id]['count'] }}</td>
							<td class="text-right">{{ $result['data'][$task->id]['amount'] }}</td>
						@endforeach
							<td class="text-right">{{ $result['summary_count'] }}</td>
							<td class="text-right">{{ $result['summary_amount'] }}</td>
					</tr>
					@endforeach
				</tbody>
				<tfoot>
					<tr>
						<th class="text-center">รวม</th>
						@foreach($task_list as $task)
							<td class="text-right" >{{ $summary_by_task[$task->id]['count'] }}</td>
							<td class="text-right" >{{ $summary_by_task[$task->id]['amount'] }}</td>
						@endforeach
						<td class="text-right"  >{{ $summary_by_task['all_count'] }}</td>
						<td class="text-right"  >{{ $summary_by_task['all_amount'] }}</td>
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
<!-- END PAGE LEVEL PLUGINS -->

<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="/assets/pages/scripts/page-monthly-single1.js" type="text/javascript"></script>
<script type="text/javascript">
  var shop_url = '{!! $shop_url !!}' ;

</script>
<!-- END PAGE LEVEL SCRIPTS -->

@endsection