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
           Doctor Course Edit
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
      <i class="fa fa-reorder"></i>Doctor Course Edit
     </div>
    </div>
    <div class="portlet-body form">
     <!-- BEGIN FORM-->
     {!! Form::open(['action'=>['Admin\DoctorsCoursesController@update',$doctor_course->id],'method'=>'PUT','files'=>true,'class'=>'form-horizontal']) !!}
     <div class="form-body">

      <div class="panel panel-primary"  style="border-color: #eee; ">
       <div class="panel-heading" style="background-color: #eee; color: black; border-color: #eee; "> Select Doctor Information</div>
       <div class="panel-body">
        <div class="form-group">
         <label class="col-md-1 control-label">Doctor (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
         <div class="col-md-3">

          <div class="input-icon right">
           <select name="doctor_id" required class="form-control doctor2">
            <option value="{{$doctor->id}}" selected="selected">{{$doctor->name.' - '.$doctor->bmdc_no}}</option>
           </select>
          </div>
         </div>


         <div class="form-group">
          <label class="col-md-1 control-label">Refer By</label>
          <div class="col-md-3">
           <input type="text" name="refer_by" value="{{ $doctor->refer_by }}" class="form-control">
          </div>
         </div>



        </div>

        </div>



        <div class="form-group">



       </div>
      </div>

      <div class="panel panel-primary"  style="border-color: #eee; ">
       <div class="panel-heading" style="background-color: #eee; color: black; border-color: #eee; ">Select Doctor Course Information</div>
       <div class="panel-body">

        <div class="years">
         <div class="form-group">
          <label class="col-md-1 control-label">Year (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
          <div class="col-md-3">
           <div class="input-icon right">
            {!! Form::select('year',$years, isset($doctor_course->year) ? $doctor_course->year : '' ,['class'=>'form-control','required'=>'required','id'=>'year']) !!}<i></i>
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
            {!! Form::select('session_id',$sessions, isset($doctor_course->session_id) ? $doctor_course->session_id : '' ,['class'=>'form-control','required'=>'required','id'=>'session_id']) !!}<i></i>
           </div>
          </div>
         </div>
        </div>

        <div class="form-group">
          <label class="col-md-1 control-label">Branch (<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>) </label>
          <div class="col-md-3">
              @php  $branches->prepend('Select Branch', ''); @endphp
              {!! Form::select('branch_id',$branches, isset($doctor_course->branch_id)?$doctor_course->branch_id:'',['class'=>'form-control','required'=>'required']) !!}<i></i>
          </div>
        </div>

        <div class="institutes">
         <div class="form-group">
          <label class="col-md-1 control-label">Institute (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
          <div class="col-md-3">
           <div class="input-icon right">
            @php  $institutes->prepend('Select Institute', ''); @endphp
            {!! Form::select('institute_id',$institutes, isset($doctor_course->institute_id) ? $doctor_course->institute_id : '' ,['class'=>'form-control','required'=>'required','id'=>'institute_id']) !!}<i></i>

           </div>
          </div>
         </div>
        </div>


        <div class="courses">
          <div class="form-group">
            <label class="col-md-1 control-label">Course (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
            <div class="col-md-3">
              @php  $courses->prepend('Select Course', ''); @endphp
              {!! Form::select('course_id',$courses, isset($doctor_course->course_id) ? $doctor_course->course_id : '',['class'=>'form-control','required'=>'required','id'=>'course_id']) !!}<i></i>
            </div>
           <input type="hidden" name="url" value="{{$url}}">
         </div>
        </div>


        <div class="faculties">
          @if($institute_type==1)
             <div class="form-group">
                <label class="col-md-1 control-label">Faculty </label>
                <div class="col-md-3">
                  @php  $faculties->prepend('Select Faculty', ''); @endphp
                  {!! Form::select('faculty_id',$faculties, isset($doctor_course->faculty_id) ? $doctor_course->faculty_id : '' ,['class'=>'form-control','id'=>'faculty_id']) !!}<i></i>
                </div>
              </div>
           @endif
        </div>

        <div class="subjects">
         <div class="form-group">
           <label class="col-md-1 control-label">Discipline</label>
           <div class="col-md-3">
             @php  $subjects->prepend('Select Discipline', ''); @endphp
             {!! Form::select('subject_id',$subjects, isset($doctor_course->subject_id) ? $doctor_course->subject_id : '' ,['class'=>'form-control','id'=>'subject_id']) !!}<i></i>
           </div>
         </div>
        </div>

        <div class="batches">
         <div class="form-group">
           <label class="col-md-1 control-label">Batch (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
           <div class="col-md-3">
             @php  $batches->prepend('Select Batch', ''); @endphp
             {!! Form::select('batch_id',$batches, isset($doctor_course->batch_id) ? $doctor_course->batch_id : '' ,['class'=>'form-control','required'=>'required','id'=>'batch_id']) !!}<i></i>
           </div>
         </div>
        </div>

        <!-- <div class="reg_no"> -->
         <div class="form-group">
           <label class="col-md-1 control-label">Reg No. (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
           <div class="col-md-3">
             <div class="input-group">
               <span id="reg_no_first_part" class="input-group-addon">{{ isset($doctor_course->reg_no_first_part) ? $doctor_course->reg_no_first_part : '' }}</span>
               <input type="hidden" name="reg_no_first_part" required value="{{ isset($doctor_course->reg_no_first_part) ? $doctor_course->reg_no_first_part : '' }}">
               <input type="text" name="reg_no_last_part" required value="{{ isset($doctor_course->reg_no_last_part) ? $doctor_course->reg_no_last_part : ''}} " class="form-control" placeholder="_ _ _" >
               {{--<input type="text" name="reg_no_last_part" value="" class="form-control" placeholder="_ _ _" minlength="3" maxlength="3">--}}
             </div>
             <div><span id="range" class="" style="color:green;font-weight:700;">{{ $range }}</span></div>
           </div>
         </div>

        <!-- </div> -->

       </div>
      </div>

      <div class="form-group">
       <label class="col-md-1 control-label">Select Status (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
       <div class="col-md-3">
        {!! Form::select('status', ['1' => 'Active','0' => 'InActive'], old('status'),['class'=>'form-control','required'=>'required']) !!}<i></i>
       </div>
      </div>

     </div>
     <div class="form-actions">
      <div class="form-group">
       <label class="col-md-1 control-label"></label>
       <div class="col-md-3">
        <button type="submit" class="btn btn-info">Update</button>
        <a href="{{ url('admin/doctors-courses') }}" class="btn btn-default">Cancel</a>
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


    <script type="text/javascript">
        $(document).ready(function() {

            $(function() { 
                $('html, body').animate({ scrollTop: $('.alert').offset().top }, '500');
            });

            $("body").on( "change", "[name='institute_id']", function() {
                var institute_id = $(this).val();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: '/branch-institute-courses',
                    dataType: 'HTML',
                    data: {institute_id : institute_id},
                    success: function( data ) {
                        $('.courses').html('');
                        $('.faculties').html('');
                        $('.subjects').html('');
                        $('.batches').html('');
                        $('.reg_no').html('');
                        $('#reg_no_first_part').text('');
                        $('[name="reg_no_last_part"]').val('');
                        $('#range').text('');
                        $('.courses').html(data);
                    }
                });
            })

            $("body").on( "change", "[name='course_id'],[name='branch_id']", function() {
                var branch_id = $("[name='branch_id']").val();
                var institute_id = $("[name='institute_id']").val();
                var course_id = $("[name='course_id']").val();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: '/admin/'+$("[name='url']").val(),
                    dataType: 'HTML',
                    data: {branch_id:branch_id,institute_id:institute_id,course_id: course_id},
                    success: function( data ) {
                        var data = JSON.parse(data);
                        $('.faculties').html('');
                        $('.subjects').html('');
                        $('.batches').html('');
                        $('.reg_no').html('');
                        $('#reg_no_first_part').text('');
                        $('[name="reg_no_last_part"]').val('');
                        $('#range').text('');
                        $('.faculties').html(data['faculties']);
                        $('.subjects').html(data['subjects']);
                        $('.batches').html(data['batches']);
                        
                    }
                });

            })

            $("body").on( "change", "[name='faculty_id']", function() {
                var institute_id = $("[name='institute_id']").val();
                var course_id = $("[name='course_id']").val();
                var faculty_id = $(this).val();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: '/faculty-subjects',
                    dataType: 'HTML',
                    data: {institute_id:institute_id,course_id:course_id,faculty_id: faculty_id},
                    success: function( data ) {
                        $('.subjects').html(data);
                    }
                });
            })

            $("body").on( "change", "[name='subject_id']", function() {

                var course_id = $('#course_id').val();
                var faculty_id = $('#faculty_id').val();
                var subject_id = $(this).val();

                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: '/courses-faculties-subjects-batches',
                    dataType: 'HTML',
                    data: { course_id : course_id , faculty_id : faculty_id,  subject_id : subject_id },
                    success: function( data ) {
                        //$('.batches').html(data);
                    }
                });
            });

            $("body").on( "change", "[name='batch_id'],[name='session_id'],[name='year']", function() {

                var year = $('#year').val().slice(-2);
                var session_id = $('#session_id').val();
                var course_id = $('#course_id').val();
                var batch_id = $('#batch_id').val();

                if(year && session_id && course_id && batch_id) {
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: "POST",
                        url: '/reg-no',
                        dataType: 'HTML',
                        data: {year: year, session_id: session_id, course_id: course_id, batch_id: batch_id},
                        success: function (data) {
                            var data = JSON.parse(data);
                            $('#reg_no_first_part').text(data['reg_no_first_part']);
                            $('[name="reg_no_first_part"]').val(data['reg_no_first_part']);
                            $('#range').text(data['range']);
                        }
                    });
                }
            });

        })
    </script>


@endsection
