@extends('layouts.backend-with-sidemenu')

@section('breadcrumbs')
<li>
    <a href="#">{{ trans('breadcrumb.shops') }}</a>
</li>
<li>{{ trans('breadcrumb.monthly-fine') }}</li>
@endsection

@section('content')

<div class="portlet light portlet-fit portlet-datatable bordered">
	<div class="portlet-title">
		<div class="caption">
      <i class="icon-list font-green"></i>
      <span class="caption-subject font-green sbold uppercase"> {{ trans('breadcrumb.monthly-fine') }} </span>
    </div>
    <div class="actions">
    </div>
	</div>
	<div class="portlet-body">
		<form method="GET" class="form-horizontal" role="form" action="{{ url($shop_url . '/monthly-fine') }}">
			<div class="row">
				<div class="col-md-4 col-md-offset-4">
					{{ Form::select('monthly_select_id', $monthly_selector , $monthly_select_id , ['class' => 'form-control' ,'name' => 'monthly_select_id']) }}
				</div>
			</div>
		</form>
		<br/>

		<div class="table-responsive">
			<table class="table table-striped table-bordered table-hover ">
				<thead>
					<tr>
						<th>วันที่</th>
						@foreach($employees as $employee)
						<th>{{ $employee->name }}</th>
						@endforeach
						<th>รวม</th>
					</tr>
				</thead>
				<tbody>
					@foreach($results as $result)
					<tr>
						<td>{{ $result['day'] }}</td>
						@foreach($employees as $employee)
							<td class="text-right">{{ $result['data'][$employee->id] }}</td>
						@endforeach
							<td class="text-right">{{ $result['summary_amount'] }}</td>
					</tr>
					@endforeach
				</tbody>
				<tfoot>
					<tr>
						<th >รวม</th>
						@foreach($employees as $employee)
							<td class="text-right" >{{ $summary_by_employee[$employee->id]['amount'] }}</td>
						@endforeach
						<td class="text-right"  >{{ $summary_by_employee['all_amount'] }}</td>
					</tr>
				</tfoot>
			</table>
		</div>
	</div>
</div>

@endsection