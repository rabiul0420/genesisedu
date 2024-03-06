@extends('admin.layouts.app')

@section('content')


 <div class="page-bar">
  <ul class="page-breadcrumb">
   <li>
    <i class="fa fa-home"></i>
    <a href="{{ url('/') }}">Home</a>
    <i class="fa fa-angle-right"></i>
   </li>
   <li>
    Batch Schedule Update
   </li>
  </ul>

 </div>

 @if(Session::has('message'))
  <div class="alert {{ (Session::get('class'))?Session::get('class'):'alert-success' }}" role="alert">
   <p> {{ Session::get('message') }}</p>
  </div>
 @endif

 <div class="row">
  <div class="col-md-12">
   <!-- BEGIN EXAMPLE TABLE PORTLET-->
   <div class="portlet">
    <div class="portlet-title">
     <div class="caption">
      <i class="fa fa-reorder"></i>Batch Schedule Update
     </div>
    </div>
    <div class="portlet-body form">
     <!-- BEGIN FORM-->
     {!! Form::open(['action'=>['Admin\BatchesSchedulesController@update',$schedule_details->id],'method'=>'PUT','files'=>true,'class'=>'form-horizontal']) !!}
     <div class="form-body">

      <div class="panel panel-primary"  style="border-color: #eee; ">
       <div class="panel-heading" style="background-color: #eee; color: black; border-color: #eee; "> Select Batch Schedule Information</div>
       <div class="panel-body">

        <div class="form-group">
         <label class="col-md-1 control-label">Schedule Name (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
         <div class="col-md-3">
          <div class="input-icon right">
           <input type="text" name="name" value="{{ $schedule_details->name?$schedule_details->name:'' }}" class="form-control" >
          </div>
         </div>

         <label class="col-md-1 control-label">Schedule Sub Line </label>
         <div class="col-md-3">
          <div class="input-icon right">
           <input type="text" name="tag_line" value="{{ $schedule_details->tag_line?$schedule_details->tag_line:'' }}" class="form-control" >
          </div>
         </div>

         <label class="col-md-1 control-label">Schedule Address (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
         <div class="col-md-3">
          <div class="input-icon right">
           <input type="text" name="address" value="{{ $schedule_details->address?$schedule_details->address:'' }}" class="form-control" >
          </div>
         </div>

        </div>

        <div class="form-group">
         <label class="control-label col-lg-1">Room Name (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
         <div class="col-lg-3">
          @php  $rooms_types->prepend('Select Room', ''); @endphp
          {!! Form::select('room_id',$rooms_types, $schedule_details->room_id?$schedule_details->room_id:'' ,['class'=>'form-control','required'=>'required']) !!}<i></i>
         </div>

         <label class="col-md-1 control-label">Schedule Contact Details (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>)  </label>
         <div class="col-md-3">
          <div class="input-icon right">
           <input type="text" name="contact_details" value="{{ $schedule_details->contact_details?$schedule_details->contact_details:'' }}" class="form-control" >
          </div>
         </div>

         <label class="col-md-1 control-label">Schedule Type (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
         <div class="col-md-3">
          <div class="input-icon right">
           {!! Form::select('type',$schedule_types, $schedule_details->type?$schedule_details->type:'' ,['class'=>'form-control','required'=>'required']) !!}<i></i>
          </div>
         </div>

        </div>



        <div class="form-group">

         <label class="col-md-1 control-label">Service Package (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
         <div class="col-md-3">
          <div class="input-icon right">
           @php  $service_packages->prepend('Select Service Package', ''); @endphp
           {!! Form::select('service_package_id',$service_packages, $schedule_details->service_package_id?$schedule_details->service_package_id:'' ,['class'=>'form-control','required'=>'required']) !!}<i></i>
          </div>
         </div>

         <label class="col-md-1 control-label">Executive (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
         <div class="col-md-3">
          <div class="input-icon right">
           @php  $executive_list->prepend('Select Executive', ''); @endphp
           {!! Form::select('executive_id',$executive_list, $schedule_details->executive_id?$schedule_details->executive_id:'' ,['class'=>'form-control','required'=>'required']) !!}<i></i>
          </div>
         </div>

         <label class="col-md-1 control-label">Support Staff (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
         <div class="col-md-3">
          <div class="input-icon right">
           @php  $support_stuff_list->prepend('Select Support Stuff', ''); @endphp
           {!! Form::select('support_stuff_id',$support_stuff_list, $schedule_details->support_stuff_id?$schedule_details->support_stuff_id:'' ,['class'=>'form-control','required'=>'required']) !!}<i></i>
          </div>
         </div>

        </div>

       </div>
      </div>

      <div class="panel panel-primary"  style="border-color: #eee; ">
       <div class="panel-heading" style="background-color: #eee; color: black; border-color: #eee; ">Select Batch Schedule Information</div>
       <div class="panel-body">
        <div class="form-group">

         <label class="control-label col-lg-1">Initial Schedule Date (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
         <div class="col-lg-3">
          <input type="text" name="initial_date" id="initial_date" autocomplete="off" onchange="change_wd_value(this.value)" value="{{ $schedule_details->initial_date?date('Y-m-d',date_create_from_format('Y-m-d h:i:s',$schedule_details->initial_date)->getTimestamp()):'' }}" required class="form-control input-append date">
         </div>

         <div class="wd_ids">
          <label class="col-md-1 control-label">Select Batch Days (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
          <div class="col-md-3">
           <div class="input-icon right">
            {!! Form::select('wd_ids[]',$week_days, $wd_ids?$wd_ids:'' ,[ 'id'=>'id_wd_ids','class'=>'form-control select2 ', 'multiple' => 'multiple','required'=>'required']) !!}<i></i>
           </div>
          </div>
         </div>



        </div>

        <div class="form-group">

         <label class="col-md-1 control-label">Select Slot One (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
         <div class="col-md-3">
          <div class="input-icon right">
           @php  $slots_list->prepend('Select Slot', ''); @endphp
           {!! Form::select('slot_type[]',$slots_list, !empty($batch_slots[0])?$batch_slots[0]->slot_type:'' ,['class'=>'form-control','required'=>'required']) !!}<i></i>
          </div>
         </div>

         <label class="col-md-1 control-label">Select Slot Time One (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
         <div class="col-md-3">
          <div class="input-group date">
           <input type="text" class="form-control timepicker" required name="start_time[]" value="{{ !empty($batch_slots[0])?$batch_slots[0]->start_time:'' }}">
           {{--<span class="input-group-addon"><span class="glyphicon glyphicon-time"></span></span>--}}
           <span class="input-group-addon">To</span>
           <input type="text" class="form-control timepicker" required  name="end_time[]" value="{{ !empty($batch_slots[0])?$batch_slots[0]->end_time:'' }}">
           {{--<span class="input-group-addon"><span class="glyphicon glyphicon-time"></span></span>--}}
          </div>
         </div>

        </div>

        <div class="form-group">

         <label class="col-md-1 control-label">Select Slot Two</label>
         <div class="col-md-3">
          <div class="input-icon right">
           @php  $slots_list->prepend('Select Slot', ''); @endphp
           {!! Form::select('slot_type[]',$slots_list, !empty($batch_slots[1])?$batch_slots[1]:'' ,['class'=>'form-control']) !!}<i></i>
          </div>
         </div>

         <label class="col-md-1 control-label">Select Slot Time Two</label>
         <div class="col-md-3">
          <div class="input-group date">
           <input type="text" class="form-control timepicker" name="start_time[]" value="{{ !empty($batch_slots[1])?$batch_slots[1]->start_time:'' }}">
           {{--<span class="input-group-addon"><span class="glyphicon glyphicon-time"></span></span>--}}
           <span class="input-group-addon">To</span>
           <input type="text" class="form-control timepicker" name="end_time[]" value="{{ !empty($batch_slots[1])?$batch_slots[1]->end_time:'' }}">
           {{--<span class="input-group-addon"><span class="glyphicon glyphicon-time"></span></span>--}}
          </div>
         </div>

        </div>

        <div class="form-group">

         <label class="col-md-1 control-label">Select Slot Three</label>
         <div class="col-md-3">
          <div class="input-icon right">
           @php  $slots_list->prepend('Select Slot', ''); @endphp
           {!! Form::select('slot_type[]',$slots_list, !empty($batch_slots[2])?$batch_slots[2]:'' ,['class'=>'form-control']) !!}<i></i>
          </div>
         </div>

         <label class="col-md-1 control-label">Select Slot Time Three</label>
         <div class="col-md-3">
          <div class="input-group date">
           <input type="text" class="form-control timepicker" name="start_time[]" value="{{ !empty($batch_slots[2])?$batch_slots[2]->start_time:'' }}">
           {{--<span class="input-group-addon"><span class="glyphicon glyphicon-time"></span></span>--}}
           <span class="input-group-addon">To</span>
           <input type="text" class="form-control timepicker" name="end_time[]" value="{{ !empty($batch_slots[2])?$batch_slots[2]->end_time:'' }}">
           {{--<span class="input-group-addon"><span class="glyphicon glyphicon-time"></span></span>--}}
          </div>
         </div>

        </div>

        <div class="form-group">

         <label class="col-md-1 control-label">Select Slot Four</label>
         <div class="col-md-3">
          <div class="input-icon right">
           @php  $slots_list->prepend('Select Slot', ''); @endphp
           {!! Form::select('slot_type[]',$slots_list, !empty($batch_slots[3])?$batch_slots[3]:'' ,['class'=>'form-control']) !!}<i></i>
          </div>
         </div>

         <label class="col-md-1 control-label">Select Slot Time Four</label>
         <div class="col-md-3">
          <div class="input-group date">
           <input type="text" class="form-control timepicker" name="start_time[]" value="{{ !empty($batch_slots[3])?$batch_slots[3]->start_time:'' }}">
           {{--<span class="input-group-addon"><span class="glyphicon glyphicon-time"></span></span>--}}
           <span class="input-group-addon">To</span>
           <input type="text" class="form-control timepicker" name="end_time[]" value="{{ !empty($batch_slots[3])?$batch_slots[3]->end_time:'' }}">
           {{--<span class="input-group-addon"><span class="glyphicon glyphicon-time"></span></span>--}}
          </div>
         </div>

        </div>

       </div>
      </div>


      <div class="panel panel-primary"  style="border-color: #eee; ">
       <div class="panel-heading" style="background-color: #eee; color: black; border-color: #eee; ">Select Batch Schedule Course Information</div>
       <div class="panel-body">

        <div class="form-group">
         <label class="col-md-1 control-label">Paper</label>
         <div class="col-md-3">
          <div class="input-icon right">
           {!! Form::select('paper',$papers, $schedule_details->paper?$schedule_details->paper:'' ,['class'=>'form-control']) !!}<i></i>
          </div>
         </div>
        </div>

        <div class="years">
         <div class="form-group">
          <label class="col-md-1 control-label">Year (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
          <div class="col-md-3">
           <div class="input-icon right">
            {!! Form::select('year',$years, $schedule_details->year?$schedule_details->year:'' ,['class'=>'form-control','required'=>'required','id'=>'year']) !!}<i></i>
           </div>
          </div>
         </div>
        </div>

        <div class="sessions">
         <div class="form-group">
          <label class="col-md-1 control-label">Session (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
          <div class="col-md-3">
           <div class="input-icon right">
            @php  $sessions->prepend('Select Session', ''); @endphp
            {!! Form::select('session_id',$sessions, $schedule_details->session_id?$schedule_details->session_id:'' ,['class'=>'form-control','required'=>'required','id'=>'session_id']) !!}<i></i>
           </div>
          </div>
         </div>
        </div>

        <div class="institutes">
         <div class="form-group">
          <label class="col-md-1 control-label">Institute (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
          <div class="col-md-3">
           <div class="input-icon right">
            @php  $institutes->prepend('Select Institute', ''); @endphp
            {!! Form::select('institute_id',$institutes, $schedule_details->institute_id?$schedule_details->institute_id:'' ,['class'=>'form-control','required'=>'required','id'=>'institute_id']) !!}<i></i>
           </div>
          </div>
         </div>
        </div>


        <div class="courses">
         <div class="form-group">
          <label class="col-md-1 control-label">Course (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
          <div class="col-md-3">
           <div class="input-icon right">
            @php  $courses->prepend('Select Course', ''); @endphp
            {!! Form::select('course_id',$courses, $schedule_details->course_id?$schedule_details->course_id:'' ,['class'=>'form-control','required'=>'required','id'=>'course_id']) !!}<i></i>
           </div>
          </div>
         </div>

        </div>

        <div class="batches">
         <div class="form-group">
          <label class="col-md-1 control-label">Batch (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
          <div class="col-md-3">
           <div class="input-icon right">
            @php  $batches->prepend('Select Batch', ''); @endphp
            {!! Form::select('batch_id',$batches, $schedule_details->batch_id?$schedule_details->batch_id:'' ,['class'=>'form-control','required'=>'required','id'=>'batch_id']) !!}<i></i>
           </div>
          </div>
         </div>

        </div>

       </div>
      </div>

      <div class="form-group">
       <label class="col-md-1 control-label">Select Status</label>
       <div class="col-md-3">
        {!! Form::select('status', ['1' => 'Active','0' => 'InActive'], old('status'),['class'=>'form-control']) !!}<i></i>
       </div>
      </div>

     </div>
     <div class="form-actions">
      <div class="row">
       <div class="col-md-offset-1 col-md-9">
        <button type="submit" class="btn btn-info">Submit</button>
        <a href="{{ url('admin/batches-schedules') }}" class="btn btn-default">Cancel</a>
       </div>
      </div>
     </div>
    {!! Form::close() !!}
    <!-- END FORM-->
    </div>
   </div>
   <!-- END EXAMPLE TABLE PORTLET-->



  </div>
 </div>
 <!-- END PAGE CONTENT-->


@endsection

@section('js')
 <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/css/select2.css" />
 <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/js/select2.js" type="text/javascript"></script>
 <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker.min.css" />
 <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker3.min.css" />
 <script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/js/bootstrap-datepicker.min.js"></script>

 <link href="//cdn.rawgit.com/Eonasdan/bootstrap-datetimepicker/e8bddc60e73c1ec2475f827be36e1957af72e2ea/build/css/bootstrap-datetimepicker.css" rel="stylesheet">
 <script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.9.0/moment-with-locales.js"></script>
 <script src="//cdn.rawgit.com/Eonasdan/bootstrap-datetimepicker/e8bddc60e73c1ec2475f827be36e1957af72e2ea/src/js/bootstrap-datetimepicker.js"></script>



 <script type="text/javascript">

  /*function change_wd_value( value_to_set ){
   var given_date = new Date(value_to_set);
   var given_date_weekday = given_date.getDay();
   $("#id_wd_ids").val(given_date_weekday);
   $('#id_wd_ids').trigger('change');
  }*/

  function change_wd_value( value_to_set ){
   var week_days = {0:7, 1:1, 2:2, 3:3, 4:4, 5:5, 6:6, 7:7 };
   var given_date = new Date(value_to_set);
   var given_date_weekday = given_date.getDay();
   $("#id_wd_ids").val(week_days[given_date_weekday]);
   $('#id_wd_ids').trigger('change');
  }


  $(document).ready(function() {

   $('.timepicker').datetimepicker({
    format: 'LT'
   });

   $('#initial_date').datepicker({
    format: 'yyyy-mm-dd',
    todayHighlight: true,
    startDate: '1900-01-01',
    endDate: '2035-01-01',
   }).on('changeDate', function(e){
    $(this).datepicker('hide');
   });

   $("body").on( "change", "[name='institute_id']", function() {
    var institute_id = $(this).val();
    $.ajax({
     headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
     },
     type: "POST",
     url: '/admin/institutes-courses',
     dataType: 'HTML',
     data: { institute_id : institute_id },
     success: function( data ) {
      $('.courses').html(data);
     }
    });
   });

   $("body").on( "change", "[name='course_id']", function() {
    var course_id = $(this).val();
    $.ajax({
     headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
     },
     type: "POST",
     url: '/admin/courses-batches',
     dataType: 'HTML',
     data: { course_id : course_id },
     success: function( data ) {
      $('.batches').html(data);
     }
    });
   });

   /*$("body").on( "change", "[name='faculty_id']", function() {
    var faculty_id = $(this).val();
    $.ajax({
     headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
     },
     type: "POST",
     url: '/admin/faculties-subjects',
     dataType: 'HTML',
     data: { faculty_id : faculty_id },
     success: function( data ) {
      $('.subjects').html(data);
     }
    });
   });

   $("body").on( "change", "[name='subject_id']", function() {

    var course_id = $('#course_id').val();
    var faculty_id = $('#faculty_id').val();
    var subject_id = $(this).val();

    $.ajax({
     headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
     },
     type: "POST",
     url: '/admin/courses-faculties-subjects-batches',
     dataType: 'HTML',
     data: { course_id : course_id , faculty_id : faculty_id,  subject_id : subject_id },
     success: function( data ) {
      $('.batches').html(data);
     }
    });
   });*/

   $("body").on( "change", "[name='batch_id']", function() {

    var year = $('#year').val().slice(-2);
    var session_id = $('#session_id').val();
    var course_id = $('#course_id').val();
    var faculty_id = $('#faculty_id').val();
    var subject_id = $('#subject_id').val();
    var batch_id = $('#batch_id').val();

    $.ajax({
     headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
     },
     type: "POST",
     url: '/admin/reg-no',
     dataType: 'HTML',
     data: { year : year, session_id : session_id, course_id : course_id , faculty_id : faculty_id,  subject_id : subject_id, batch_id : batch_id },
     success: function( data ) {
      $('.reg_no').html(data);
     }
    });
   });

   $('.select2').select2();
  })
 </script>


@endsection