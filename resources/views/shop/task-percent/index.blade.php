@extends('layouts.backend-with-sidemenu')


@section('breadcrumbs')
<li>
    <a href="#">Shops</a>
</li>
<li>Tasks Percent</li>
@endsection

@section('content')
<form class="form-horizontal form-row-seperated" action="{{ url($shop_url.'/task-percent') }}" method="POST" >
{{ csrf_field() }}
<div class="portlet light portlet-fit portlet-datatable bordered">
	<div class="portlet-title">
		<div class="caption">
      <i class="icon-settings font-green"></i>
      <span class="caption-subject font-green sbold uppercase"> Task percent configuration </span>
    </div>
    <div class="actions">
    	<button type="submit" class="btn btn-transparent blue btn-outline btn-sm">Save</button>
    </div>
	</div>
	<div class="portlet-body">
		<div class="dataTables_wrapper no-footer">


			<div class="table-responsive">
				<table class="table table-striped table-bordered table-hover">
					<thead>
						<tr>
							<th>Task</th>
							<th>Percent</th>
						</tr>
					</thead>
					<tbody>
						@foreach($tasks as $task)
						<tr>
							<td>{!! $task->name !!}</td>
							<td><input class="form-control" name="task_percents[{{ $task->id}}][percent]" value="{{ isset($task_percents[$task->id])?$task_percents[$task->id]:0 }}" /></td>
						</tr>
						@endforeach
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
</form>

@endsection