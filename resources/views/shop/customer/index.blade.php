@extends('layouts.backend-with-sidemenu')



@section('breadcrumbs')
<li>
    <a href="#">Shops</a>
</li>
<li>Customers</li>
@endsection

@section('content')

<div class="portlet light portlet-fit portlet-datatable bordered">
	<div class="portlet-title">
		<div class="caption">
      <i class="icon-settings font-green"></i>
      <span class="caption-subject font-green sbold uppercase"> Customer Listing </span>
    </div>
    <div class="actions">
      <a href="{{ url($shop_url . '/customers/create') }}" class="btn btn-transparent blue btn-outline btn-circle btn-sm">Create new customer</a>
    </div>
	</div>
	<div class="portlet-body">
		<div class="dataTables_wrapper no-footer">

			<div class="row">
				<div class="col-md-6 col-sm-12">

				</div>
				<div class="col-md-6 col-sm-12">
					<div id="sample_1_filter" class="dataTables_filter"><label>Search:<input type="search" class="form-control input-sm input-small input-inline" placeholder="" aria-controls="sample_1"></label></div>
				</div>
			</div>

			<div class="table-responsive">
				<table class="table table-striped table-bordered table-hover">
					<thead>
						<tr>
							<th>#</th>
							<th>Name</th>
							<th>Tel No.</th>
							<th>Actions</th>
						</tr>
					</thead>
					<tbody>
						@foreach($customers as $customer)
						<tr>
							<td>{!! $customer->id !!}</td>
							<td>{!! $customer->name !!}</td>
							<td>{!! $customer->phone_no !!}</td>
							<td>
								<a class="btn btn-sm btn-default btn-editable" href="{{ url($shop_url . '/customers/' . $customer->id . '/edit' ) }}"><i class="fa fa-pencil"></i> Edit</a>

							</td>
						</tr>
						@endforeach
					</tbody>
				</table>
				{{ $customers->links() }}
			</div>
		</div>
	</div>
</div>

@endsection