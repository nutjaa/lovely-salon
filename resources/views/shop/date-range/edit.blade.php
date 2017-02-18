@extends('layouts.backend-with-sidemenu')

@section('breadcrumbs')
<li>
  <a href="#">Shops</a>
</li>
<li>
  <a href="{{ url($shop_url.'/date-ranges') }}">Date ranges</a>
</li>
@if(! $date_range->id )
<li>Create new date range</li>
@else
<li>Edit date range</li>
@endif
@endsection

@section('page-level-styles')
  <link href="/assets/global/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" rel="stylesheet" type="text/css" />
@endsection

@section('content')

<div class="row">
	<div class="col-md-12">
    @if(! $date_range->id )
		<form class="form-horizontal form-row-seperated" action="{{ url($shop_url.'/date-ranges') }}" method="post">
    @else
    <form class="form-horizontal form-row-seperated" action="{{ url($shop_url.'/date-ranges/'.$date_range->id) }}" method="POST" >
      <input name="_method" type="hidden" value="PUT">
    @endif
			{{ csrf_field() }}
			@include('shared.error_noti')
			<div class="portlet">
				<div class="portlet-title">
            <div class="caption">
              <i class="fa fa-user"></i>Date range
            </div>
            <div class="actions btn-set">
                <a href="{{ url($shop_url . '/date-ranges') }}" class="btn default"><i class="fa fa-angle-left"></i> Back</a>
                <button type="submit" class="btn green"><i class="fa fa-check"></i> Save</button>
            </div>
        </div>
			</div>
			<div class="portlet-body">
				<div class="form-body">
					<div class="form-group">
             <label class="col-md-2 control-label">Name:<span class="required"> * </span></label>
              <div class="col-md-10">
                <input type="text" placeholder="" name="name" class="form-control" value="{!! $date_range->name !!}">
              </div>
          </div>
          <div class="form-group">
             <label class="col-md-2 control-label">Start:<span class="required"> * </span></label>
              <div class="col-md-10">
                <input type="text" placeholder="" name="start_date" class="form-control form_datetime" value="{!! $date_range->startFormat !!}">
              </div>
          </div>
          <div class="form-group">
             <label class="col-md-2 control-label">End:<span class="required"> * </span></label>
              <div class="col-md-10">
                <input type="text" placeholder="" name="end_date" class="form-control form_datetime" value="{!! $date_range->endFormat !!}">
              </div>
          </div>
				</div>
				<div class="form-actions fluid">
          <div class="row">
            <div class="col-md-offset-2 col-md-10">
              <button type="submit" class="btn green">Save</button>
              <a href="{{ url($shop_url . '/date-ranges') }}" class="btn default">Cancel</a>
            </div>
          </div>
        </div>
			</div>
		</form>
	</div>
</div>
@endsection

@section('page-level-js')

<!-- BEGIN PAGE LEVEL PLUGINS -->
<script src="/assets/global/plugins/moment.min.js" type="text/javascript"></script>
<script src="/assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
<!-- END PAGE LEVEL PLUGINS -->

<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script type="text/javascript">
  var shop_url = '{!! $shop_url !!}' ;
</script>
<script src="/assets/pages/scripts/date-range.js" type="text/javascript"></script>
<!-- END PAGE LEVEL SCRIPTS -->

@endsection