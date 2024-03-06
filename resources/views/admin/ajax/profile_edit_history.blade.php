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
		@if( $profile_edit_history->count() ) 
			@foreach($profile_edit_history as $k=>$profile_histoey)
				<tr>
					<td rowspan="4">{{(isset($k) ? ++$k : '')}}</td>
					<td rowspan="4">{{$profile_histoey->user->name ??  ''}}</td>
					<td rowspan="4">{{(isset($profile_histoey->updated_at) ? date('d-m-Y', strtotime($profile_histoey->updated_at)) : '')}}</td>
					<td rowspan="4">{{(isset($profile_histoey->updated_at) ? date('h:i:s', strtotime($profile_histoey->updated_at)) : '')}}</td>

					<td class="{{ $profile_histoey->bmdc_no ?  'text-danger': ''}}">BMDC</td>
					<td>
						@if($profile_histoey->bmdc_no)
							{{$profile_histoey->bmdc_no}}
						@else
							<span class="text-muted">No Change</span>
						@endif
					</td>
				</tr>
				<tr>

					<td class="{{ $profile_histoey->email ?  'text-danger': ''}}">Email</td>
					<td>
						@if($profile_histoey->email)
							{{$profile_histoey->email}}
						@else
							<span class="text-muted">No Change</span>
						@endif

					</td>

				</tr>
				<tr>
					<td class="{{ $profile_histoey->mobile_number ?  'text-danger': ''}}">Mobile</td>
				
					<td> 
						@if($profile_histoey->mobile_number)
							{{$profile_histoey->mobile_number}}
						@else
							<span class="text-muted">No Change</span>
						@endif

					</td>
				</tr>
				<tr>
					<td class="{{ $profile_histoey->password ?  'text-danger': ''}}">Password</td>
				
					<td> 
						@if($profile_histoey->password)
							{{$profile_histoey->password}}
						@else
							<span class="text-muted">No Change</span>
						@endif

					</td>
				</tr>
			
				@endforeach
			@else
				<tr><td colspan="6">No Profile Histoey</td></tr>
			@endif
		</tbody>
	</table>
</div>
