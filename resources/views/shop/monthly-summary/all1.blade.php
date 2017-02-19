@extends('layouts.backend-with-sidemenu')

@section('breadcrumbs')
<li>
    <a href="#">{{ trans('breadcrumb.shops') }}</a>
</li>
<li>{{ trans('breadcrumb.monthly-summary-all1') }}</li>
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
		<form method="GET" class="form-horizontal" role="form" action="{{ url($shop_url . '/monthly-all-employee1') }}">
			<div class="row">
				<div class="col-md-4 col-md-offset-4">
					{{ Form::select('date_range_id', $date_ranges , $date_range_id , ['class' => 'form-control' ,'name' => 'date_range_id']) }}
				</div>
			</div>
		</form>
		<br/>
		<div class="table-responsive">
			<table class="table table-striped table-bordered table-hover ">
				<thead>
					<tr>
						<th>ชื่อพนัก</th>
						@foreach($task_list as $task)
						<th colspan="3" class="text-center">{{ $task->name }}</th>
						@endforeach
						<th colspan="3" class="text-center">รวมเป็นเงิน</th>
					</tr>
					<tr>
						<th>งาน</th>
						@foreach($task_list as $task)
							<th class="text-center">ครั้ง</th>
							<th class="text-center">บาท</th>
							<th class="text-center">%</th>
						@endforeach
						<th class="text-center">ครั้ง</th>
						<th class="text-center">บาท</th>
						<th class="text-center">%</th>
					</tr>
				</thead>
				<tbody>
					@foreach($results as $result)
					<tr>
						<td>{{ $result['employee']->name }}</td>
						@foreach($task_list as $task)
							<td class="text-right">{{ $result['data'][$task->id]['count'] }}</td>
							<td class="text-right">{{ $result['data'][$task->id]['amount'] }}</td>
							<td class="text-right">{{ $result['data'][$task->id]['percent'] }}</td>
						@endforeach
							<td class="text-right">{{ $result['summary_count'] }}</td>
							<td class="text-right">{{ $result['summary_amount'] }}</td>
							<td class="text-right">{{ $result['summary_percent'] }}</td>
					</tr>
					@endforeach
				</tbody>
				<tfoot>
					<th class="text-center">รวม</th>
					@foreach($task_list as $task)
						<td class="text-right" colspan="3">{{ $summary_by_task[$task->id] }}</td>
					@endforeach
					<td class="text-right" colspan="3">{{ $summary_by_task['all'] }}</td>
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
<script src="/assets/pages/scripts/page-monthly-all1.js" type="text/javascript"></script>
<script type="text/javascript">
  var shop_url = '{!! $shop_url !!}' ;

</script>
<!-- END PAGE LEVEL SCRIPTS -->

@endsection