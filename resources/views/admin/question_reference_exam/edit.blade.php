@extends('admin.layouts.app')

@section('content')

    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <i class="fa fa-home"></i><a href="{{ url('/admin') }}">Home</a></i>
            </li>
            <?php
            $urls='';
            foreach($breadcrumb as $key=>$value){ $urls .= $value.'/';
                echo '<li> <i class="fa fa-angle-right"></i> <a href="'.url('/').substr_replace($urls, "", -1).'"> '.$value.' </a></li>';
            }
            ?>
        </ul>
    </div>

    @if(Session::has('message'))
        <div class="alert {{ (Session::get('class'))?Session::get('class'):'alert-success' }}" role="alert">
            <p> {{ Session::get('message') }}</p>
        </div>
    @endif

    <div class="row">
        <div class="col-md-12">
            <div class="portlet">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-reorder"></i><?php echo $module_name;?> Edit
                    </div>
                </div>
                <div class="portlet-body form">
                    <!-- BEGIN FORM-->
                    {!! Form::open(['action'=>['Admin\QuestionReferenceExamController@update',$question_reference_exam->id],'method'=>'PUT','files'=>true,'class'=>'form-horizontal']) !!}
                    <div class="form-body">

                        <div class="institutes">
                            <div class="form-group">
                                <label class="col-md-3 control-label">Institute (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                                <div class="col-md-3">
                                    <div class="input-icon right">
                                        @php  $reference_institutes->prepend('Select Institute', ''); @endphp
                                        {!! Form::select('institute_id',$reference_institutes, isset($question_reference_exam->institute->name) ? $question_reference_exam->institute_id :'' ,['class'=>'form-control','required'=>'required','id'=>'institute_id']) !!}<i></i>

                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="courses">
                            <div class="form-group">
                                <label class="col-md-3 control-label">Course (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                                <div class="col-md-3">
                                    <div class="input-icon right">
                                        @php  $reference_courses->prepend('Select Course', ''); @endphp
                                        {!! Form::select('course_id',$reference_courses, isset($question_reference_exam->course->id) ? $question_reference_exam->course_id :'' ,['class'=>'form-control','required'=>'required','id'=>'course_id']) !!}<i></i>

                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="faculties">
                            @if(isset($question_reference_exam->course->type) && $question_reference_exam->course->type == 1)
                                <div class="form-group">
                                    <label class="col-md-3 control-label">Faculty (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                                    <div class="col-md-3">
                                        <div class="input-icon right">
                                            @php  $reference_faculties->prepend('Select Faculty', ''); @endphp
                                            {!! Form::select('faculty_id',$reference_faculties, isset($question_reference_exam->faculty->id) ? $question_reference_exam->faculty_id :'' ,['class'=>'form-control','required'=>'required','id'=>'faculty_id']) !!}<i></i>

                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="subjects">
                            @if(isset($question_reference_exam->course->type) && $question_reference_exam->course->type == 0)
                                <div class="form-group">
                                    <label class="col-md-3 control-label">Subject (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                                    <div class="col-md-3">
                                        <div class="input-icon right">
                                            @php  $reference_subjects->prepend('Select Subject', ''); @endphp
                                            {!! Form::select('subject_id',$reference_subjects, isset($question_reference_exam->subject->id) ? $question_reference_exam->subject_id :'' ,['class'=>'form-control','required'=>'required','id'=>'subject_id']) !!}<i></i>

                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="sessions">
                            @if(isset($question_reference_exam->course->type) && $question_reference_exam->course->type == 0)
                                <div class="form-group">
                                    <label class="col-md-3 control-label">Session (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                                    <div class="col-md-3">
                                        <div class="input-icon right">
                                            @php  $reference_sessions->prepend('Select Session', ''); @endphp
                                            {!! Form::select('session_id',$reference_sessions, isset($question_reference_exam->session->id) ? $question_reference_exam->session_id :'' ,['class'=>'form-control','required'=>'required','id'=>'session_id']) !!}<i></i>

                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        {{--<div class="exam_types">
                            <div class="form-group">
                                <label class="col-md-3 control-label">Exam Type (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                                <div class="col-md-3">
                                    <div class="input-icon right">
                                        @php  $exam_types->prepend('Select Exam Type', ''); @endphp
                                        {!! Form::select('exam_type_id',$exam_types, isset($question_reference_exam->exam_type->id) ? $question_reference_exam->exam_type_id :'' ,['class'=>'form-control','required'=>'required','id'=>'exam_type_id']) !!}<i></i>

                                    </div>
                                </div>
                            </div>
                        </div>--}}

                        <div class="years">
                            <div class="form-group">
                                <label class="col-md-3 control-label">Year (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                                <div class="col-md-3">
                                    <div class="input-icon right">
                                        {!! Form::select('year',$years, $question_reference_exam->year ? $question_reference_exam->year :'' ,['class'=>'form-control','required'=>'required','id'=>'year']) !!}<i></i>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-3 control-label">Select Status  (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>)</label>
                        <div class="col-md-3">
                            {!! Form::select('status', ['1' => 'Active','0' => 'InActive'], old('status'),['class'=>'form-control']) !!}<i></i>
                        </div>
                    </div>

                    <div class="form-actions">
                        <div class="row">
                            <div class="col-md-offset-3 col-md-9">
                                <button type="submit" class="btn btn-info">{{ $submit_value }}</button>
                                <a href="{{ url('admin/question-reference-exam') }}" class="btn btn-default">Cancel</a>
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

            $("body").on( "change", "[name='institute_id']", function() {
                var institute_id = $(this).val();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: '/admin/institute-changed-in-question-reference-exam',
                    dataType: 'HTML',
                    data: {institute_id : institute_id},
                    success: function( data ) {
                        $('.courses').html('');
                        $('.faculties').html('');
                        $('.subjects').html('');
                        $('.sessions').html('');
                        $('.courses').html(data);
                    }
                });
            })

            $("body").on( "change", "[name='course_id']", function() {
                var course_id = $(this).val();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: '/admin/course-changed-in-question-reference-exam',
                    dataType: 'HTML',
                    data: {course_id: course_id},
                    success: function( data ) {
                        var data = JSON.parse(data);
                        $('.faculties').html('');
                        $('.subjects').html('');
                        $('.sessions').html('');
                        $('.faculties').html(data['reference_faculties']);
                        $('.subjects').html(data['reference_subjects']);
                        $('.sessions').html(data['reference_sessions']);
                        $('.select2').select2({ });

                    }
                });
            })


            $("body").on( "click", "#checkbox", function() {
                if($("#checkbox").is(':checked') ){
                    $(".disciplines .select2 > option").prop("selected","selected");
                    $(".disciplines .select2").trigger("change");
                }else{
                    $(".disciplines .select2 > option").removeAttr("selected");
                    $(".disciplines .select2").trigger("change");
                }
            });

            $("body").on( "click", "#checkbox_faculty", function() {
                if($("#checkbox_faculty").is(':checked') ){
                    $(".faculties .select2 > option").prop("selected","selected");
                    $(".faculties .select2").trigger("change");
                }else{
                    $(".faculties .select2 > option").removeAttr("selected");
                    $(".faculties .select2").trigger("change");
                }
            });

            $('.select2').select2({ });


        })
    </script>

@endsection
