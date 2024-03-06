<div>
	<table class="table table-striped table-bordered table-hover datatable">
		<thead>
		<tr>
			<th>Sl</th>
			<th>Note</th>
			<th>Verified</th>
			<th>Verified By</th>
			<th>Date</th>
		</tr>
		</thead>
		<tbody>
				@foreach ($payment_verificationss as $k=>$payment_verification)
					<tr>
						<td>{{ ++$k }}</td>
						<td>{{ $payment_verification->note }}</td>
						<td>{{ $payment_verification->verified }}</td>
						<td>{{ $payment_verification->doctor->name}}</td>
						<td>{{ $payment_verification->created_at->format('d M Y - g:i A')}}</td>
					</tr>
				@endforeach
		</tbody>
	</table>
	<div>
		<a class="btn btn-success" href="{{ url('/admin/print-payment-list/'.$payment_id) }}" target="_blank">Print</a>
	</div>
</div>

@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/js/select2.js" type="text/javascript"></script>
    <script src="{{ asset('assets/scripts/jquery-ui.min.js') }}"></script>
	
	<script type="text/javascript">	

			$(document).ready(function() {
							$.ajaxSetup({
								headers: {
									'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
								}
							});

				$('#print').click(function(){
					var name = $('[name="name"]').val();
					var course = $('[name="course_id"]').val();
					var session = $('[name="session_id"]').val();
					var batch = $('[name="batch_id"]').val();
			
					var params = '?name='+name +'&course_id=' + course +'&session_id='+session+'&batch_id='+batch;

					

						var pw = window.open( "/admin/payment-varification-print"+params, '_blank',
							"toolbar=yes,scrollbars=yes,resizable=yes,top=50,left=50,width=1000,height=800" );
						pw.print( );
				});
			})

	</script>

@endsection