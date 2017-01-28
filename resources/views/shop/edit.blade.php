@extends('layouts.backend')

@section('breadcrumbs')
<li>
    <a href="#">Shops</a>
</li>
<li>Create new shop</li>
@endsection


@section('content')

<div class="row">
	<div class="col-md-12">
		<div class="portlet-body form">
			<form class="form-horizontal" action="{{ url('shops') }}" method="post">
				{{ csrf_field() }}
				<div class="form-body">
					@include('shared.error_noti')
					<div class="form-group">
            <label class="col-md-3 control-label">Name</label>
            <div class="col-md-4">
              <input type="text" placeholder="Enter text" name="name" class="form-control">
              <span class="help-block"> New shop name. </span>
            </div>
	        </div>
				</div>
				<div class="form-actions fluid">
          <div class="row">
            <div class="col-md-offset-3 col-md-9">
              <button class="btn green" type="submit">Save</button>
              <a class="btn default" href="{{ url('') }}">Cancel</a>
            </div>
          </div>
        </div>
			</form>
		</div>
	</div>
</div>

@endsection