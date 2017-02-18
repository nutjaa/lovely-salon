@extends('layouts.backend-with-sidemenu')

@section('page-level-styles')
  <link href="/assets/global/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" rel="stylesheet" type="text/css" />
  <link href="/assets/global/plugins/typeahead/typeahead.css" rel="stylesheet" type="text/css" />
@endsection

@section('breadcrumbs')
<li>
  <a href="#">{{ trans('breadcrumb.shops') }}</a>
</li>
<li>
  <a href="{{ url($shop_url . '/daily-jobs?date='.$daily_job->task_at->toDateString()) }}">{{ trans('breadcrumb.daily-task') }}</a>
</li>
@if(! $daily_job->id )
<li>{{ trans('daily-task.create-new') }}</li>
@else
<li>{{ trans('daily-task.edit') }}</li>
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
              <i class="icon-list font-green"></i>{{ trans('breadcrumb.daily-task') }}
            </div>
            <div class="actions btn-set">
              @if($daily_job->id)
                <button id="btn-delete-daily-job" class="btn red-mint btn-large" data-toggle="confirmation" data-original-title="Are you sure ?"
                  title="" data-placement="bottom" type="button">{{ trans('daily-task.delete') }}</button>
              @endif
                <a href="{{ url($shop_url . '/daily-jobs?date='.$daily_job->task_at->toDateString()) }}" class="btn default"><i class="fa fa-angle-left"></i> {{ trans('daily-task.cancel') }}</a>
                <button type="submit" class="btn green"><i class="fa fa-check"></i> {{ trans('daily-task.save') }}</button>
            </div>
        </div>

			</div>
			<div class="portlet-body">
				<div class="form-body">
					<div class="form-group">
             <label class="col-md-2 control-label">{{ trans('daily-task.employee') }}:<span class="required"> * </span></label>
              <div class="col-md-10">
                {{ Form::select('employee_id', $employee_list , $daily_job->employee_id , ['class' => 'form-control']) }}
              </div>
          </div>
          <div class="form-group">
            <label class="col-md-2 control-label">{{ trans('daily-task.date') }}:<span class="required"> * </span></label>
            <div class="col-md-4">
              <div class="input-group date form_datetime">
                <input type="text" name="task_at" size="16" readonly class="form-control" value="{!! $daily_job->task_at_format !!}">
                <span class="input-group-btn">
                  <button class="btn default date-set" type="button">
                    <i class="fa fa-calendar"></i>
                  </button>
                </span>
              </div>
            </div>
          </div>
          <div class="form-group">
             <label class="col-md-2 control-label">{{ trans('daily-task.task') }}:<span class="required"> * </span></label>
              <div class="col-md-10">
                {{ Form::select('task_id', $task_list , $daily_job->task_id , ['class' => 'form-control']) }}
              </div>
          </div>
          <div class="form-group">
             <label class="col-md-2 control-label">{{ trans('daily-task.description') }}:</label>
              <div class="col-md-10">
                <textarea class="form-control" name="description">{!! $daily_job->description !!}</textarea>
              </div>
          </div>
          <div class="form-group">
             <label class="col-md-2 control-label">{{ trans('daily-task.amount') }}:<span class="required"> * </span></label>
              <div class="col-md-10">
                {{ Form::number('amount' , $daily_job->amount , ['class' => 'form-control']) }}
              </div>
          </div>
          <div class="form-group">
             <label class="col-md-2 control-label">{{ trans('daily-task.royal-customer') }}:</label>
            <div class="col-md-10">
              {{Form::checkbox('is_loyal_customer', 1 , $daily_job->is_loyal_customer)}}
            </div>
          </div>
          <div class="form-group">
            <label class="col-md-2 control-label">{{ trans('daily-task.customer') }}:</label>
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
              <button type="submit" class="btn green">{{ trans('daily-task.save') }}</button>
              <a href="{{ url($shop_url . '/daily-jobs?date='.$daily_job->task_at->toDateString()) }}" class="btn default">{{ trans('daily-task.cancel') }}</a>
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
<script src="/assets/global/plugins/bootstrap-confirmation/bootstrap-confirmation.min.js" type="text/javascript"></script>
<!-- END PAGE LEVEL PLUGINS -->

<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script type="text/javascript">
  var shop_url = '{!! $shop_url !!}' ;
  var daily_job_id = '{{ $daily_job->id }}' ;
</script>
<script src="/assets/pages/scripts/page-daily-job.js" type="text/javascript"></script>
<script src="/assets/global/plugins/typeahead/handlebars.min.js" type="text/javascript"></script>
<script src="/assets/global/plugins/typeahead/typeahead.bundle.min.js" type="text/javascript"></script>
<!-- END PAGE LEVEL SCRIPTS -->

@endsection