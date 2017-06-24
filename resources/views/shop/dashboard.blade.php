@extends('layouts.backend-with-sidemenu')

@section('page-level-styles')
  <link href="/assets/global/plugins/morris/morris.css" rel="stylesheet" type="text/css" />
@endsection

@section('breadcrumbs')
<li>
    <a href="#">{{ trans('breadcrumb.dashboard') }}</a>
</li>
@endsection

@section('content')

<div class="portlet light portlet-fit portlet-datatable bordered">
  <div class="portlet-title">
    <div class="caption">
      <i class="icon-list font-green"></i>
      <span class="caption-subject font-green sbold uppercase"> {{ trans('breadcrumb.dashboard-daily-income') }} </span>

      {{$start_date}} - {{$to_date}}
    </div>
    <div class="actions">
      <form method="GET" class="form-horizontal" role="form" action="{{ url($shop_url) }}">
        {{ Form::select('monthly_select_id', $monthly_selector , $monthly_select_id , ['class' => 'form-control' ,'name' => 'monthly_select_id']) }}
      </form>
    </div>
  </div>
  <div class="portlet-body">
    <div id="graph"></div>
  </div>
</div>
@endsection



@section('page-level-js')
  <script src="/assets/global/plugins/morris/morris.min.js" type="text/javascript"></script>
  <script src="/assets/global/plugins/morris/raphael-min.js" type="text/javascript"></script>
  <script type="text/javascript">
    var day_data = {!!$data!!};
    Morris.Line({
      element: 'graph',
      data: day_data,
      xkey: 'date',
      ykeys: ['total'],
      labels: ['Total']
    });

    jQuery(document).ready(function() {
      $('select[name="monthly_select_id"]').change(function(e) {
        $(this).parents('form:first').submit();
      });
    });

  </script>
@endsection