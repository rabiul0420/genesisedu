<div>
	<table class="table table-striped table-bordered table-hover datatable">
		<thead>
		<tr>
			<th>SL</th>
			<th>Updated By</th>
			<th>Updated At</th>
			<th>Time</th>
		</tr>
		</thead>
		<tbody>
		@if( $sba_question_edit_historys->count() ) 

			@foreach($sba_question_edit_historys as $k=>$sba_question_edit_history)
				<tr>
					<td rowspan="">{{(isset($k) ? '' . ++$k : '')}}</td>
					<td rowspan="">{{ $sba_question_edit_history->user->name }}</td>
					<td rowspan="">{{(isset($sba_question_edit_history->updated_at) ? date('d-m-Y', strtotime($sba_question_edit_history->updated_at)) : '')}}</td>
					<td rowspan="">{{(isset($sba_question_edit_history->updated_at) ? date('h:i:s', strtotime($sba_question_edit_history->updated_at)) : '')}}</td>
				</tr>
			@endforeach
		@else
				<tr><td colspan="6">No history</td></tr>
        @endif
		</tbody>
	</table>
</div>