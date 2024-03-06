
<div>
	<div>

		
		<p> {{ isset($doctor_course->doctor->name)?'Name : '.$doctor_course->doctor->name:'' }}<br>
			{{ isset($doctor_course->doctor->mobile_number)?'Mobile : '.$doctor_course->doctor->mobile_number:'' }}
			{!! isset($doctor_course->batch->name)?'<br>Batch : '.$doctor_course->batch->name:'' !!}
			{!! isset($doctor_course->course->name)?'<br>Course : '.$doctor_course->course->name:'' !!}
			{!! isset($doctor_course->faculty->name)?'<br>Faculty : '.$doctor_course->faculty->name:''  !!}
			{!! isset($doctor_course->subject->name)?'<br>Discipline : '.$doctor_course->subject->name:'' !!}
			@if(isset($doctor_course->bcps_subject->name))
			<br> Bcps Discipline:	{{ $doctor_course->bcps_subject->name }}
    		@endif
			{!! isset($doctor_course->reg_no)?'<br>Reg No : '.$doctor_course->reg_no:'' !!}
		</p>
		<h5> Lecture Sheet Collection Point : <b>{{ (!$doctor_course->include_lecture_sheet)?'Admitted Without Lecture Sheets':(($doctor_course->include_lecture_sheet && $doctor_course->delivery_status == '1') ? "Courier Address" : (($doctor_course->include_lecture_sheet && $doctor_course->delivery_status == '0') ? 'GENESIS Office Collection' : ''))}}</b> </h5>
		<p>
			{{ $doctor_course->courier_address }}
			{!! isset($doctor_course->courier_upazila->name)?'<br>'.$doctor_course->courier_upazila->name:'' !!}
			{!! isset($doctor_course->courier_district->name)?'<br>'.$doctor_course->courier_district->name:'' !!}
			{!! isset($doctor_course->courier_division->name)?'<br>'.$doctor_course->courier_division->name:'' !!}
		</p>
		
	</div>
	<div>
		<a class="btn btn-success" href="{{ url('/admin/print-course-details/'.$doctor_course->id) }}" target="_blank">Print</a>
	</div>


</div>