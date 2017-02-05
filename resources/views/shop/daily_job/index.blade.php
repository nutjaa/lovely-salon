@extends('layouts.backend-with-sidemenu')

@section('page-level-styles')
	<link href="/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css" rel="stylesheet" type="text/css" />
	<link href="/assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet" type="text/css" />
@endsection

@section('breadcrumbs')
<li>
    <a href="#">Shops</a>
</li>
<li>Daily Jobs</li>
@endsection

@section('content')

<div class="portlet light portlet-fit portlet-datatable bordered">
	<div class="portlet-title">
		<div class="caption">
      <i class="icon-settings font-green"></i>
      <span class="caption-subject font-green sbold uppercase"> Daily Jobs </span>
    </div>
    <div class="actions">
       <a href="{{ url($shop_url . '/daily-jobs/create?task_at='.$selected_date) }}" class="btn btn-transparent blue btn-outline btn-circle btn-sm">Create new job</a>
    </div>
	</div>
	<div class="portlet-body">
		<div class="row">
			<div class="col-md-4 col-md-offset-4">
				<input type="text" value="{!! $selected_date !!}" size="16" name="date" class="form-control form-control-inline input-medium date-picker">
			</div>
		</div>
		<br/>
		<div class="dataTables_wrapper no-footer">
			<div class="table-responsive">
				<table class="table table-striped table-bordered table-hover">
					<thead>
						<tr>
						@foreach($queue_employees as $queue_employee)
							<th>{!! $queue_employee->employee->name !!}</th>
						@endforeach
						</tr>
					</thead>
				</table>
			</div>
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
<!-- END PAGE LEVEL SCRIPTS -->

@endsection