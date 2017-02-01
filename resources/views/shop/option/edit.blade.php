@extends('layouts.backend-with-sidemenu')

@section('breadcrumbs')
<li>
  <a href="#">Settings</a>
</li>
<li>
  <a href="{{ url($shop_url.'/options') }}">Options</a>
</li>
@if(! $option->id )
<li>Create new option</li>
@else
<li>Edit option</li>
@endif
@endsection

@section('content')

<div class="row">
	<div class="col-md-12">
    @if(! $option->id )
		<form class="form-horizontal form-row-seperated" action="{{ url($shop_url.'/options') }}" method="post">
    @else
    <form class="form-horizontal form-row-seperated" action="{{ url($shop_url.'/options/'.$option->id) }}" method="POST" >
      <input name="_method" type="hidden" value="PUT">
    @endif
			{{ csrf_field() }}
			@include('shared.error_noti')
			<div class="portlet">
				<div class="portlet-title">
            <div class="caption">
              <i class="fa fa-tag"></i>Option
            </div>
            <div class="actions btn-set">
                <a href="{{ url($shop_url . '/options') }}" class="btn default"><i class="fa fa-angle-left"></i> Back</a>
                <button type="submit" class="btn green"><i class="fa fa-check"></i> Save</button>
            </div>
        </div>
			</div>
			<div class="portlet-body">
				<div class="form-body">
					<div class="form-group">
             <label class="col-md-2 control-label">Name:<span class="required"> * </span></label>
              <div class="col-md-10">
                <input type="text" placeholder="" name="name" class="form-control" value="{!! $option->name !!}">
              </div>
          </div>
          <div class="form-group">
             <label class="col-md-2 control-label">Option type:<span class="required"> * </span></label>
              <div class="col-md-10">
                <input type="text" placeholder="" name="option_type" class="form-control" value="{!! $option->option_type !!}">
              </div>
          </div>
          <div class="form-group">
             <label class="col-md-2 control-label">Ordering:<span class="required"> * </span></label>
              <div class="col-md-10">
                <input type="number" placeholder="" name="ordering" class="form-control" value="{!! $option->ordering !!}">
              </div>
          </div>
				</div>
				<div class="form-actions fluid">
          <div class="row">
            <div class="col-md-offset-2 col-md-10">
              <button type="submit" class="btn green">Save</button>
              <a href="{{ url($shop_url . '/options') }}" class="btn default">Cancel</a>
            </div>
          </div>
        </div>
			</div>
		</form>
	</div>
</div>

@endsection