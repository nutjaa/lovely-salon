@extends('layouts.backend-with-sidemenu')

@section('page-level-styles')
	<link href="/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css" rel="stylesheet" type="text/css" />
@endsection

@section('breadcrumbs')
<li>
    <a href="#">Shops</a>
</li>
<li>Date Ranges</li>
@endsection

@section('content')

<div class="portlet light portlet-fit portlet-datatable bordered">
	<div class="portlet-title">
		<div class="caption">
      <i class="icon-settings font-green"></i>
      <span class="caption-subject font-green sbold uppercase"> Date Range Listing </span>
    </div>
    <div class="actions">
      <a href="{{ url($shop_url . '/date-ranges/create') }}" class="btn btn-transparent blue btn-outline btn-circle btn-sm">Create new date range</a>
    </div>
	</div>
	<div class="portlet-body">
		<div class="dataTables_wrapper no-footer">


			<div class="table-responsive">
				<table class="table table-striped table-bordered table-hover">
					<thead>
						<tr>
							<th>#</th>
							<th>Name</th>
							<th>Start</th>
							<th>End</th>
							<th>Actions</th>
						</tr>
					</thead>
					<tbody>
						@foreach($date_ranges as $date_range)
						<tr>
							<td>{!! $date_range->id !!}</td>
							<td>{!! $date_range->name !!}</td>
							<td>{!! $date_range->startFormat !!}</td>
							<td>{!! $date_range->endFormat !!}</td>
							<td>
								<a class="btn btn-sm btn-default btn-editable" href="{{ url($shop_url . '/date-ranges/' . $date_range->id . '/edit' ) }}"><i class="fa fa-pencil"></i> Edit</a>

							</td>
						</tr>
						@endforeach
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

@endsection