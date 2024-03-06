<div>
	<table class="table table-striped table-bordered table-hover datatable">
		<thead>
		<tr>
			<th>SL</th>
			<th>Updated By</th>
			<th>Updated At</th>
			<th>Time</th>
			<th colspan=2 class="text-center">Modification</th>
		</tr>
		</thead>
		<tbody>
		@if( $discount_history->count() ) 

			@foreach($discount_history as $k=>$history)
				<tr>
					<td rowspan="3">{{(isset($k) ? 'Edit-' . ++$k : '')}}</td>
					<td rowspan="3">{{(isset($history->updated_by) ? $history->user->name : '')}}</td>
					<td rowspan="3">{{(isset($history->updated_at) ? date('d-m-Y', strtotime($history->updated_at)) : '')}}</td>
					<td rowspan="3">{{(isset($history->updated_at) ? date('h:i:s', strtotime($history->updated_at)) : '')}}</td>

					<td class="{{ $history->amount_change ?  'text-danger': ''}}">Amount</td>
					<td>
						@if($history->amount_change)
							{{$history->amount_change}}
						@else
							<span class="text-muted">No Change</span>
						@endif
					</td>
				</tr>
				<tr>

					<td class="{{ $history->status_change ?  'text-danger': ''}}">Status</td>
					<td>
						@if($history->status_change)
							{{$history->status_change}}
						@else
							<span class="text-muted">No Change</span>
						@endif

					</td>

				</tr>
				<tr>
					<td class="{{ $history->duration_change ?  'text-danger': ''}}">Duration</td>
				
					<td> 
						@if($history->duration_change)
							{{$history->duration_change}}
						@else
							<span class="text-muted">No Change</span>
						@endif

					</td>
				</tr>

				@endforeach
			@else
				<tr><td colspan="6">No history</td></tr>
			@endif
		</tbody>
	</table>
</div>
