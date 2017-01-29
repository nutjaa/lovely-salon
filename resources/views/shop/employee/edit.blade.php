@extends('layouts.backend-with-sidemenu')

@section('breadcrumbs')
<li>
  <a href="#">Shops</a>
</li>
<li>
  <a href="{{ url($shop_url.'/employees') }}">Employees</a>
</li>
@if(! $employee->id )
<li>Create new employee</li>
@else
<li>Edit employee</li>
@endif
@endsection

@section('content')

<div class="row">
	<div class="col-md-12">
    @if(! $employee->id )
		<form class="form-horizontal form-row-seperated" action="{{ url($shop_url.'/employees') }}" method="post">
    @else
    <form class="form-horizontal form-row-seperated" action="{{ url($shop_url.'/employees/'.$employee->id) }}" method="POST" >
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
                <a href="{{ url($shop_url . '/employees') }}" class="btn default"><i class="fa fa-angle-left"></i> Back</a>
                <button type="submit" class="btn green"><i class="fa fa-check"></i> Save</button>
            </div>
        </div>
			</div>
			<div class="portlet-body">
				<div class="form-body">
					<div class="form-group">
             <label class="col-md-2 control-label">Name:<span class="required"> * </span></label>
              <div class="col-md-10">
                <input type="text" placeholder="" name="name" class="form-control" value="{!! $employee->name !!}">
              </div>
          </div>
          <div class="form-group">
             <label class="col-md-2 control-label">Description:</label>
              <div class="col-md-10">
                <textarea class="form-control" name="description">{!! $employee->description !!}</textarea>
              </div>
          </div>
          <div class="form-group">
             <label class="col-md-2 control-label">Position:<span class="required"> * </span></label>
              <div class="col-md-10">
                <input type="text" placeholder="" name="position" class="form-control" value="{!! $employee->position !!}">
              </div>
          </div>
          <div class="form-group">
             <label class="col-md-2 control-label">Base salary:<span class="required"> * </span></label>
              <div class="col-md-10">
                <input type="number" placeholder="" name="base_salary" class="form-control" value="{!! $employee->base_salary !!}">
              </div>
          </div>
          <div class="form-group">
             <label class="col-md-2 control-label">Tasks:<span class="required"> * </span></label>
              <div class="col-md-10">
                <div class="form-control height-auto">
                  <div class="scroller" style="height:275px;" data-always-visible="1">
                    <ul class="list-unstyled">
                      @foreach($tasks as $task)
                      <li><label><input type="checkbox" name="tasks[]" value="{!! $task->id !!}" @if($employee->hasTask($task->id)) checked="checked" @endif>&nbsp;&nbsp;{!! $task->name !!}</label>
                      @endforeach
                    </ul>
                  </div>
                </div>
              </div>
          </div>
				</div>
				<div class="form-actions fluid">
          <div class="row">
            <div class="col-md-offset-2 col-md-10">
              <button type="submit" class="btn green">Save</button>
              <a href="{{ url($shop_url . '/employees') }}" class="btn default">Cancel</a>
            </div>
          </div>
        </div>
			</div>
		</form>
	</div>
</div>
@endsection