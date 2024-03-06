@extends('layouts.app')

@section('content')
    <div class="container">

        <div class="row">

            @include('side_bar')

            <div class="col-md-9 col-md-offset-0">

                @if(Session::has('message'))
                    <div class="alert {{ (Session::get('class'))?Session::get('class'):'alert-success' }}" role="alert">
                        <p> {{ Session::get('message') }}</p>
                    </div>
            @endif
            <!-- BEGIN EXAMPLE TABLE PORTLET-->
                <div class="portlet">
                    <div class="portlet-title">
                        <div class="caption">
                            <!-- <i class="fa fa-reorder"></i>Doctor Course Create -->
                        </div>
                    </div>
                    <div class="portlet-body form">
                        <!-- BEGIN FORM-->
                        {!! Form::open( [ 'url' => [ 'update-doctor-course-discipline' ], 'method'=>'post', 'files'=>true, 'class'=>'form-horizontal' ]) !!}

                        <div class="form-body">


                            <div class="panel panel-primary"  style="border-color: #eee; ">
                                <div class="panel-heading" style="background-color:#7fc9f6; color: #FFFFFF; border-color: #ffffff; "><h3>Edit Discipline</h3></div>
                                <div class="panel-body">
                                    <div class="institutes">
                                        <div class="form-group">
                                            <label class="col-md-3 control-label">Institute (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                                            <div class="col-md-3">
                                                <div class="input-icon right">
                                                    <input type="hidden" name="doctor_course_id" value="{{ $doctor_course->id }}">
                                                    <input type="hidden" name="institute_id" value="{{ isset($doctor_course->institute_id) ? $doctor_course->institute_id : '' }}">
                                                    <input type="text" class="form-control" name="institute_name" value="{{ isset($doctor_course->institute_id) ? $doctor_course->institute->name : '' }}" readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="courses">
                                        <div class="form-group">
                                            <label class="col-md-3 control-label">Course (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                                            <div class="col-md-3">
                                                <input type="hidden" name="course_id" value="{{ isset($doctor_course->course_id) ? $doctor_course->course_id : '' }}">
                                                <input type="text" class="form-control" name="course_name" value="{{ isset($doctor_course->course_id) ? $doctor_course->course->name : '' }}" readonly>
                                            </div>
                                        </div>
                                    </div>

                                    <br>Faculty: {{ $doctor_course->faculty->name ?? ' '  }}<br>
                                    Discipline: {{ $doctor_course->subject->name ?? ' ' }}<br>

                                    @if( isset( $doctor_course->bcps_subject->name  ) )
                                        FCPS Part Discipline: {{ $doctor_course->bcps_subject->name ?? ' '  }}<br>
                                    @endif
                                    <br>

                                    <div class="faculties">
                                        @if($institute_type==1)
                                            <div class="form-group">
                                                <label class="col-md-3 control-label">{{$is_combined ? 'Residency ':''}}Faculty </label>
                                                <div class="col-md-3">
                                                    @php  $faculties->prepend('Select Faculty', ''); @endphp
                                                    {!! Form::select('faculty_id',$faculties, isset($doctor_course->faculty_id) ? $doctor_course->faculty_id : '' ,['class'=>'form-control','disabled'=>'disabled','id'=>'faculty_id']) !!}<i></i>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    <br>

                                    <div class="subjects">
                                        <div class="form-group">
                                            <label class="col-md-3 control-label">{{$is_combined ? 'FCPS Part-1 ':''}}Discipline </label>
                                            <div class="col-md-3">
                                                @php  $subjects->prepend('Select Discipline', ''); @endphp
                                                {!! Form::select('subject_id',$subjects, isset($doctor_course->subject_id) ? $doctor_course->subject_id : '' ,['class'=>'form-control','disabled'=>'disabled','id'=>'subject_id']) !!}<i></i>
                                            </div>
                                        </div>
                                    </div>
                                    <br>

                                    @if( $is_combined )
                                        <div class="bcps-subjects">
                                            <div class="form-group">
                                                <label class="col-md-3 control-label">FCPS Part-1 Discipline</label>
                                                <div class="col-md-3">
                                                    @php  $subjects->prepend('Select FCPS Part-1 Discipline', ''); @endphp
                                                    {!! Form::select('bcps_subject_id',$bcps_subjects, ($doctor_course->bcps_subject_id ?? '') ,['class'=>'form-control','disabled'=>'disabled','id'=>'bcps_subject_id']) !!}<i></i>
                                                </div>
                                            </div>
                                        </div>
                                    @endif


                                </div>
                            </div>

                        </div>
                        <div class="form-actions">
                            <div class="form-group">
                                <label class="col-md-3 control-label"></label>
                                <div class="col-md-3">
                                    <button id="submit" type="submit" class="btn btn-info" >Submit</button>
                                    <a href="{{ url('my-courses') }}" class="btn btn-default">Cancel</a>
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
                    url: '/institute-courses',
                    dataType: 'HTML',
                    data: {institute_id : institute_id},
                    success: function( data ) {
                        $('.courses').html('');
                        $('.faculties').html('');
                        $('.subjects').html('');
                        $('.batches').html('');
                        $('.batch_details').html('');
                        $('.reg_no').html('');
                        $('#reg_no_first_part').text('');
                        $('[name="reg_no_first_part"]').val('');
                        $('[name="reg_no_last_part"]').val('');
                        $('#range').html('');
                        $('#message').html('');
                        $('.session-faculty').html('');
                        $('.session-subject').html('');
                        $('.courses').html(data);
                    }
                });
            });

            $("body").on( "change", "[name='course_id']", function() {
                var course_id = $("[name='course_id']").val();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: '/'+$("[name='url']").val(),
                    dataType: 'HTML',
                    data: {course_id: course_id},
                    success: function( data ) {
                        $('.faculties').html('');
                        $('.subjects').html('');
                        //$('.batches').html('');
                        $('.reg_no').html('');
                        $('#reg_no_first_part').text('');
                        $('[name="reg_no_first_part"]').val('');
                        $('[name="reg_no_last_part"]').val('');
                        $('#range').html('');
                        $('#message').html('');
                        $('.session-faculty').html('');
                        $('.session-subject').html('');
                        $('.session-faculty').html(data);

                    }
                });
            });

            $("body").on( "change", "[name='faculty_id']", function() {
                var faculty_id = $(this).val();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: '/faculty-subjects',
                    dataType: 'HTML',
                    data: {faculty_id: faculty_id, institute_id:'{{ $doctor_course->institute_id ?? '' }}' },
                    success: function( data ) {
                        $('.subjects').html(data);
                    }
                });
            });

            $("body").on( "change", "[name='branch_id'],[name='course_id'],[name='subject_id']", function() {

                var institute_id = $("[name='institute_id']").val();
                var course_id = $("[name='course_id']").val();
                var subject_id = $("[name='subject_id']").val();
                var branch_id = $("[name='branch_id']").val();

                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: '/courses-branches-subjects-batches',
                    dataType: 'HTML',
                    data: { institute_id : institute_id, course_id : course_id , branch_id : branch_id,  subject_id : subject_id },
                    success: function( data ) {
                        var data = JSON.parse(data);
                        $('.batch_details').html('');
                        $('.batches').html(data['batches']);
                    }
                });

            });

            $("body").on( "change", "[name='batch_id'],[name='session_id'],[name='year']", function() {

                var year = $('#year').val();
                var session_id = $('#session_id').val();
                var course_id = $('#course_id').val();
                var batch_id = $('#batch_id').val();
                var batch_id = $('#batch_id').val();
                var second_ajax_call = true;

                if(year && session_id && course_id && batch_id) {
                    second_ajax_call = false;
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
                            $('.batch_details').html('');
                            $('.batch_details').html(data['batch_details']);
                            $('#range').html('');
                            $('#message').html('');
                            $('#reg_no_first_part').text(data['reg_no_first_part']);
                            $('[name="reg_no_first_part"]').val(data['reg_no_first_part']);
                            $('[name="reg_no_last_part"]').val(data['reg_no_last_part']);
                            $('#range').html(data['range']);
                            $('#message').html(data['message']);
                            $('#submit').prop( "disabled", false );
                            if(data['message'] !== null && data['message'] !== '')$('#submit').prop( "disabled", true );

                        }
                    });

                }

                if(second_ajax_call && course_id && batch_id) {
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: "POST",
                        url: '/batch-details',
                        dataType: 'HTML',
                        data: {course_id: course_id, batch_id: batch_id},
                        success: function (data) {
                            var data = JSON.parse(data);
                            $('.batch_details').html('');
                            $('.batch_details').html(data['batch_details']);

                        }
                    });
                }
            });

        })
    </script>


@endsection
