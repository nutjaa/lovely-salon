@extends('layouts.backend-with-sidemenu')

@section('breadcrumbs')
<li>
    <a href="#">{{ trans('breadcrumb.shops') }}</a>
</li>
<li>{{ trans('breadcrumb.monthly-salary') }}</li>
@endsection

@section('content')

<div class="portlet light portlet-fit portlet-datatable bordered">
	<div class="portlet-title">
		<div class="caption">
      <i class="icon-list font-green"></i>
      <span class="caption-subject font-green sbold uppercase"> {{ trans('breadcrumb.monthly-salary') }} </span>
    </div>
    <div class="actions">
    </div>
	</div>
	<div class="portlet-body">
		<form method="GET" class="form-horizontal" role="form" action="{{ url($shop_url . '/monthly-salary') }}">
			<div class="row">
				<div class="col-md-4 col-md-offset-4">
					{{ Form::select('date_range_id', $date_ranges , $date_range_id , ['class' => 'form-control' ,'name' => 'date_range_id']) }}
				</div>
			</div>
		</form>
		<br/>
		<h3 class="text-center">ตารางสรุปเงินเดือน ช่างซอย </h3>
		<div class="table-responsive">
			<table class="table table-striped table-bordered table-hover ">
				<thead>
					<tr>
						<th>ชื่อพนัก</th>
						<th>ยอด/เงินเดือน</th>
						<th>20%</th>
						<th>&nbsp;</th>
						@if($second_period)
						<th>สาย</th>
						<th>ค่าปรับ</th>
						@endif
						<th>เบิก</th>
						<th>กู้</th>
						<th>รับสุทธิ</th>
					</tr>
				</thead>
				<tbody>
					@foreach($results as $result)
					<tr>
						<td>{{ $result['employee']->name }}</td>
						<td class="text-right">{{ number_format($result['salary']) }}</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						@if($second_period)
						<td class="text-right">{{ number_format($result['late']) }}</td>
						<td class="text-right">{{ number_format($result['fine']) }}</td>
						@endif
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td class="text-right">{{ number_format($result['total_receive']) }}</td>
					</tr>
					@endforeach
				</tbody>
				<tfoot>
					<tr>
						<td>รวม</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						@if($second_period)
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						@endif
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td class="text-right">{{ number_format($grand_total) }}</td>
					</tr>
				</tfoot>
			</table>
		</div>
		<br/>
		<h3 class="text-center">ตารางสรุปเงินเดือนพนักงาน  ผู้ช่วยช่าง</h3>
		<div class="table-responsive">
			<table class="table table-striped table-bordered table-hover ">
				<thead>
					<tr>
						<th>ชื่อพนัก</th>
						<th>เงินเดือน</th>
						<th>ครึ่งเดือน</th>

						@if($second_period)
						<th>OT</th>
						<th>เปอร์เซ็นต์</th>
						<th>สาย</th>
						<th>ค่าปรับ</th>
						@endif
						<th>เบิก</th>
						<th>กู้</th>
						<th>รับสุทธิ</th>
					</tr>
				</thead>
				<tbody>
					@foreach($results2 as $result)
					<tr>
						<td>{{ $result['employee']->name }}</td>
						<td class="text-right">{{ number_format($result['employee']->base_salary) }}</td>
						<td class="text-right">{{ number_format($result['salary']) }}</td>

						@if($second_period)
						<td class="text-right">{{ number_format($result['ot']) }}</td>
						<td class="text-right">{{ number_format($result['summary_percent']) }}</td>
						<td class="text-right">{{ number_format($result['late']) }}</td>
						<td class="text-right">{{ number_format($result['fine']) }}</td>
						@endif
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td class="text-right">{{ number_format($result['total_receive']) }}</td>
					</tr>
					@endforeach
				</tbody>
				<tfoot>
					<tr>
						<td>รวม</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>

						@if($second_period)
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						@endif
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td class="text-right">{{ number_format($grand_total2) }}</td>
					</tr>
				</tfoot>
			</table>
		</div>
	</div>
</div>

@endsection


@section('page-level-js')

<!-- BEGIN PAGE LEVEL PLUGINS -->
<script src="/assets/global/plugins/moment.min.js" type="text/javascript"></script>
<!-- END PAGE LEVEL PLUGINS -->

<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="/assets/pages/scripts/page-monthly-salary.js" type="text/javascript"></script>
<script type="text/javascript">
  var shop_url = '{!! $shop_url !!}' ;

</script>
<!-- END PAGE LEVEL SCRIPTS -->

@endsection