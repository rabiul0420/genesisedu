@if($payment_validated == false)
<a href="{{url('admin/doctor-course/payments/'.$doctor_course_manual_payment_list->doctor_course_id)}}" class="btn btn-xs btn-success" target="_blank">Pay Now</a>
@can('Manual Payment Force Validate')
<a href="{{url('admin/doctor-course-manual-payment-validate/'.$doctor_course_manual_payment_list->id)}}" class="btn btn-xs btn-info">Force Validate</a>
@endcan
@endif
