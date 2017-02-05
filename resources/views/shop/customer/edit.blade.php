@extends('layouts.backend-with-sidemenu')

@section('breadcrumbs')
<li>
  <a href="#">Shops</a>
</li>
<li>
  <a href="{{ url($shop_url.'/customers') }}">Customers</a>
</li>
@if(! $customer->id )
<li>Create new customer</li>
@else
<li>Edit customer</li>
@endif
@endsection

@section('content')

<div class="row">
	<div class="col-md-12">
    @if(! $customer->id )
		<form class="form-horizontal form-row-seperated" action="{{ url($shop_url.'/customers') }}" method="post">
    @else
    <form class="form-horizontal form-row-seperated" action="{{ url($shop_url.'/customers/'.$customer->id) }}" method="POST" >
      <input name="_method" type="hidden" value="PUT">
    @endif
			{{ csrf_field() }}
			@include('shared.error_noti')
			<div class="portlet">
				<div class="portlet-title">
            <div class="caption">
              <i class="fa fa-user"></i>Employee
            </div>
            <div class="actions btn-set">
                <a href="{{ url($shop_url . '/customers') }}" class="btn default"><i class="fa fa-angle-left"></i> Back</a>
                <button type="submit" class="btn green"><i class="fa fa-check"></i> Save</button>
            </div>
        </div>
			</div>
			<div class="portlet-body">
				<div class="form-body">
					<div class="form-group">
             <label class="col-md-2 control-label">Name:<span class="required"> * </span></label>
              <div class="col-md-10">
                <input type="text" placeholder="" name="name" class="form-control" value="{!! $customer->name !!}">
              </div>
          </div>
          <div class="form-group">
             <label class="col-md-2 control-label">Description:</label>
              <div class="col-md-10">
                <textarea class="form-control" name="description">{!! $customer->description !!}</textarea>
              </div>
          </div>
          <div class="form-group">
             <label class="col-md-2 control-label">Tel No.</label>
              <div class="col-md-10">
                <input type="number" placeholder="" name="phone_no" class="form-control" value="{!! $customer->phone_no !!}">
              </div>
          </div>
				</div>
				<div class="form-actions fluid">
          <div class="row">
            <div class="col-md-offset-2 col-md-10">
              <button type="submit" class="btn green">Save</button>
              <a href="{{ url($shop_url . '/customers') }}" class="btn default">Cancel</a>
            </div>
          </div>
        </div>
			</div>
		</form>
	</div>
</div>
@endsection