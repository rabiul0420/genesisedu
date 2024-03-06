<div>
	<table class="table table-striped table-bordered table-hover datatable">
		<thead>
		<tr>
			<th>SL</th>
			<th>Updated By</th>
			<th>Question</th>
			<th>Updated At</th>
			<th>Time</th>
		</tr>
		</thead>
		<tbody>
		@if( $log_histories->count() ) 

			@foreach($log_histories as $k=>$log_history)
			<?php $detail = json_decode($log_history->details,true);?>
				<tr>
					<td rowspan="">{{(isset($k) ? 'Edit-' . ++$k : '')}}</td>
					<td rowspan="">{{ $log_history->user->name }}</td>
					<td>
						<p class="text-left">Question And Answer: {!! $detail['question_and_answers'] ?? ' ' !!}</p>
						<p class="text-left">Reference: {!! $detail['reference'] ?? ' ' !!}</p>
						<p class="text-left">Discussion: {!! $detail['discussion'] ?? ' ' !!}</p>
					</td>
					<td rowspan="">{{(isset($log_history->updated_at) ? date('d-m-Y', strtotime($log_history->updated_at)) : '')}}</td>
					<td rowspan="">{{(isset($log_history->updated_at) ? date('h:i:s', strtotime($log_history->updated_at)) : '')}}</td>
				</tr>
			@endforeach
		@else
				<tr><td colspan="6">No history</td></tr>
        @endif
		</tbody>
	</table>
</div>
