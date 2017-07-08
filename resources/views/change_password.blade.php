@extends('layouts.backend')



@section('breadcrumbs')
<li>{{ trans('breadcrumb.change-password') }}</li>
@endsection

@section('content')
<div class="row">
	<div class="col-md-12">
    <form class="form-horizontal form-row-seperated" action="{{ url('/change-password/'.$user->id) }}" method="POST" >
      <input name="_method" type="hidden" value="PUT">
			{{ csrf_field() }}
			@include('shared.error_noti')
			<div class="portlet">
				<div class="portlet-title">
            <div class="caption">
              <i class="fa fa-user"></i>Change password
            </div>
            <div class="actions btn-set">
                <a href="{{ url('/') }}" class="btn default"><i class="fa fa-angle-left"></i> Back</a>
                <button type="submit" class="btn green"><i class="fa fa-check"></i> Save</button>
            </div>
        </div>
			</div>
			<div class="portlet-body">
				<div class="form-body">
					<div class="form-group">
             <label class="col-md-2 control-label">Old password:<span class="required"> * </span></label>
              <div class="col-md-10">
                <input type="password" placeholder="" name="old_password" class="form-control" value="">
              </div>
          </div>
          <div class="form-group">
             <label class="col-md-2 control-label">New password:<span class="required"> * </span></label>
              <div class="col-md-10">
                <input type="password" placeholder="" name="new_password" class="form-control" value="">
              </div>
          </div>
          <div class="form-group">
             <label class="col-md-2 control-label">Confirm new password:<span class="required"> * </span></label>
              <div class="col-md-10">
                <input type="password" placeholder="" name="confirm_password" class="form-control" value="">
              </div>
          </div>
				</div>
				<div class="form-actions fluid">
          <div class="row">
            <div class="col-md-offset-2 col-md-10">
              <button type="submit" class="btn green">Save</button>
              <a href="{{ url('/') }}" class="btn default">Cancel</a>
            </div>
          </div>
        </div>
			</div>
		</form>
	</div>
</div>
@endsection