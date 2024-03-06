<div>
	<table class="table table-striped table-bordered table-hover datatable">
		<thead>
		<tr>
			<th>Payment Date</th>
			<th>Got Discount</th>
			<th>Paid Amount</th>
			<th>Trx ID</th>
			<th>Note</th>
		</tr>
		</thead>
		<tbody>
		@foreach($doctor_course_payments as $payemnt)
		<tr>
			<td>{{(isset($payemnt->created_at) ? $payemnt->created_at : '')}}</td>
			<td>{{(isset($discounts) ? $discounts->amount .' tk' : '')}}</td>
			<td>{{(isset($payemnt->amount) ? $payemnt->amount : '')}} tk</td>
			<td>{{(isset($payemnt->trans_id) ? $payemnt->trans_id : '')}}</td>
			<td>{{(isset($payemnt->note) ? $payemnt->note : '')}}</td>
		</tr>
		@endforeach
		</tbody>
	</table>
</div>

<div>
	<div>

		<h5> Lecture Sheet Collection Point : <b>{{ (!$doctor_course->include_lecture_sheet)?'Admitted Without Lecture Sheets':(($doctor_course->include_lecture_sheet && $doctor_course->delivery_status == '1') ? "Courier Address" : (($doctor_course->include_lecture_sheet && $doctor_course->delivery_status == '0') ? 'GENESIS Office Collection' : ''))}}</b> </h5>

		<p> {{ isset($doctor_course->doctor->name)?'Name : '.$doctor_course->doctor->name:'' }}<br>
			{{ isset($doctor_course->doctor->mobile_number)?'Mobile : '.$doctor_course->doctor->mobile_number:'' }}<br>
			{!! isset($doctor_course->batch->name)?'<br>Batch : '.$doctor_course->batch->name:'' !!}
			{!! isset($doctor_course->course->name)?'<br>Course : '.$doctor_course->course->name:'' !!}
			{!! isset($doctor_course->faculty->name)?'<br>Faculty : '.$doctor_course->faculty->name:''  !!}
			{!! isset($doctor_course->subject->name)?'<br>Discipline : '.$doctor_course->subject->name:'' !!}
			{!! isset($doctor_course->reg_no)?'<br>Reg No : '.$doctor_course->reg_no:'' !!}
		</p>
		<p>
			{{ $doctor_course->courier_address }}
			{!! isset($doctor_course->courier_upazila->name)?'<br>'.$doctor_course->courier_upazila->name:'' !!}
			{!! isset($doctor_course->courier_district->name)?'<br>'.$doctor_course->courier_district->name:'' !!}
			{!! isset($doctor_course->courier_division->name)?'<br>'.$doctor_course->courier_division->name:'' !!}
		</p>

	</div>
	<div>
		<a class="btn btn-success" href="{{ url('/admin/print-courier-address/'.$doctor_course->id) }}" target="_blank">Print</a>
	</div>


</div>