@extends('layouts.backend-with-sidemenu')

@section('page-level-styles')
  <link href="/assets/global/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" rel="stylesheet" type="text/css" />
  <link href="/assets/global/plugins/typeahead/typeahead.css" rel="stylesheet" type="text/css" />
@endsection

@section('breadcrumbs')
<li>
  <a href="#">Shops</a>
</li>
<li>
  <a href="{{ url($shop_url.'/daily-jobs') }}">Daily Jobs</a>
</li>
@if(! $daily_job->id )
<li>Create new daily job</li>
@else
<li>Edit daily job</li>
@endif
@endsection

@section('content')
<div class="row">
	<div class="col-md-12">
    @if(! $daily_job->id )
		<form class="form-horizontal form-row-seperated" action="{{ url($shop_url.'/daily-jobs') }}" method="post">
    @else
    <form class="form-horizontal form-row-seperated" action="{{ url($shop_url.'/daily-jobs/'.$daily_job->id) }}" method="POST" >
      <input name="_method" type="hidden" value="PUT">
    @endif
    	{{ csrf_field() }}
			@include('shared.error_noti')
			<div class="portlet">
				<div class="portlet-title">
            <div class="caption">
              <i class="fa fa-user"></i>Job
            </div>
            <div class="actions btn-set">
                <a href="{{ url($shop_url . '/daily-jobs?date='.$daily_job->task_at->toDateString()) }}" class="btn default"><i class="fa fa-angle-left"></i> Back</a>
                <button type="submit" class="btn green"><i class="fa fa-check"></i> Save</button>
            </div>
        </div>

			</div>
			<div class="portlet-body">
				<div class="form-body">
					<div class="form-group">
             <label class="col-md-2 control-label">Employee:<span class="required"> * </span></label>
              <div class="col-md-10">
                {{ Form::select('employee_id', $employee_list , $daily_job->employee_id , ['class' => 'form-control']) }}
              </div>
          </div>
          <div class="form-group">
            <label class="col-md-2 control-label">Date:<span class="required"> * </span></label>
            <div class="col-md-4">
              <div class="input-group date form_datetime">
                <input type="text" name="task_at" size="16" readonly class="form-control" value="{!! $daily_job->task_at->format('Y-m-d H:i') !!}">
                <span class="input-group-btn">
                  <button class="btn default date-set" type="button">
                    <i class="fa fa-calendar"></i>
                  </button>
                </span>
              </div>
            </div>
          </div>
          <div class="form-group">
             <label class="col-md-2 control-label">Task:<span class="required"> * </span></label>
              <div class="col-md-10">
                {{ Form::select('task_id', $task_list , $daily_job->task_id , ['class' => 'form-control']) }}
              </div>
          </div>
          <div class="form-group">
             <label class="col-md-2 control-label">Description:</label>
              <div class="col-md-10">
                <textarea class="form-control" name="description">{!! $daily_job->description !!}</textarea>
              </div>
          </div>
          <div class="form-group">
             <label class="col-md-2 control-label">Amount:<span class="required"> * </span></label>
              <div class="col-md-10">
                {{ Form::number('amount' , $daily_job->amount , ['class' => 'form-control']) }}
              </div>
          </div>
          <div class="form-group">
             <label class="col-md-2 control-label">Royal Customer:</label>
            <div class="col-md-10">
              {{Form::checkbox('is_loyal_customer', $daily_job->is_loyal_customer)}}
            </div>
          </div>
          <div class="form-group">
            <label class="col-md-2 control-label">Customer:</label>
            <div class="col-sm-4">
              <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-search"></i>
                </span>
                <input type="text" id="customer_name" name="customer_name" class="form-control" />
              </div>
            </div>
          </div>
        </div>
        <div class="form-actions fluid">
          <div class="row">
            <div class="col-md-offset-2 col-md-10">
              <button type="submit" class="btn green">Save</button>
              <a href="{{ url($shop_url . '/daily-jobs?date='.$daily_job->task_at->toDateString()) }}" class="btn default">Cancel</a>
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
<script src="/assets/pages/scripts/page-daily-job.js" type="text/javascript"></script>
<script src="/assets/global/plugins/typeahead/handlebars.min.js" type="text/javascript"></script>
<script src="/assets/global/plugins/typeahead/typeahead.bundle.min.js" type="text/javascript"></script>
<!-- END PAGE LEVEL SCRIPTS -->

@endsection