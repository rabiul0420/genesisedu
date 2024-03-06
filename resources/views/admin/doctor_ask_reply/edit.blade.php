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
                    {!! Form::open(['action'=>['Admin\LectureVideoLinkController@update',$lecture_video_link->id],'method'=>'PUT','files'=>true,'class'=>'form-horizontal']) !!}
                    <div class="form-body">

                        <div class="years">
                            <div class="form-group">
                                <label class="col-md-3 control-label">Year (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                                <div class="col-md-3">
                                    <div class="input-icon right">
                                        {!! Form::select('year',$years, $lecture_video_link->year ? $lecture_video_link->year :'' ,['class'=>'form-control','required'=>'required','id'=>'year']) !!}<i></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="sessions">
                            <div class="form-group">
                                <label class="col-md-3 control-label">Session (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                                <div class="col-md-3">
                                    <div class="input-icon right">
                                        @php  $sessions->prepend('Select Session', ''); @endphp
                                        {!! Form::select('session_id',$sessions, $lecture_video_link->session_id ? $lecture_video_link->session_id :'' ,['class'=>'form-control','required'=>'required','id'=>'session_id']) !!}<i></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">Branch (<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>) </label>
                            <div class="col-md-3">
                                @php  $branches->prepend('Select Branch', ''); @endphp
                                {!! Form::select('branch_id',$branches, $lecture_video_link->branch_id ? $lecture_video_link->branch_id : "" ,['class'=>'form-control','required'=>'required']) !!}<i></i>
                            </div>
                        </div>

                        <div class="institutes">
                            <div class="form-group">
                                <label class="col-md-3 control-label">Institute (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                                <div class="col-md-3">
                                    <div class="input-icon right">
                                        @php  $institutes->prepend('Select Institute', ''); @endphp
                                        {!! Form::select('institute_id',$institutes, $lecture_video_link->institute_id ? $lecture_video_link->institute_id :'' ,['class'=>'form-control','required'=>'required','id'=>'institute_id']) !!}<i></i>

                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="courses">
                            <div class="form-group">
                                <label class="col-md-3 control-label">Course (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                                <div class="col-md-3">
                                    @php  $courses->prepend('Select Course', ''); @endphp
                                    {!! Form::select('course_id',$courses, isset($lecture_video_link->course_id) ? $lecture_video_link->course_id : '',['class'=>'form-control','required'=>'required','id'=>'course_id']) !!}<i></i>
                                </div>
                                <input type="hidden" name="url" value="{{$url}}">
                            </div>
                        </div>                       

                        <div class="batches">
                            <div class="form-group">
                                <label class="col-md-3 control-label">Batch (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                                <div class="col-md-3">
                                    @php  $batches->prepend('Select Batch', ''); @endphp
                                    {!! Form::select('batch_id',$batches, isset($lecture_video_link->batch_id) ? $lecture_video_link->batch_id : '' ,['class'=>'form-control','required'=>'required','id'=>'batch_id']) !!}<i></i>
                                </div>
                            </div>
                        </div>

                        <div class="lecture_videos">
                            <div class="form-group">
                                <label class="col-md-3 control-label">Lecture Video  (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>)</label>
                                <div class="col-md-3">
                                        @php  $lecture_videos->prepend('Select Lecture Video', ''); @endphp
                                        {!! Form::select('lecture_video_id[]', $lecture_videos,  $selected_videos ,['class'=>'form-control select2','id'=>'lecture_video_id','multiple'=>'multiple']) !!}<i></i>
                                </div>
                            </div>
                        </div>

                    </div>
                    

                    <div class="form-group">
                        <label class="col-md-3 control-label">Select Status</label>
                        <div class="col-md-3">
                            {!! Form::select('status', ['1' => 'Active','0' => 'InActive'], old('status'),['class'=>'form-control']) !!}<i></i>
                        </div>
                    </div>

                    <div class="form-actions">
                        <div class="row">
                            <div class="col-md-offset-3 col-md-9">
                                <button type="submit" class="btn btn-info">{{ $submit_value }}</button>
                                <a href="{{ url('admin/lecture-video-link') }}" class="btn btn-default">Cancel</a>
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
                    url: '/admin/institute-courses-for-lectures-topics-batches',
                    dataType: 'HTML',
                    data: {institute_id : institute_id},
                    success: function( data ) {
                        $('.courses').html('');
                        $('.faculties').html('');
                        $('.subjects').html('');
                        $('.topics').html('');
                        $('.lecture_videos').html('');
                        $('.batches').html('');
                        $('.reg_no').html('');
                        $('#reg_no_first_part').text('');
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
                    url: '/admin/lecture-videos',
                    dataType: 'HTML',
                    data: {branch_id:branch_id,institute_id:institute_id,course_id: course_id},
                    success: function( data ) {
                        var data = JSON.parse(data);
                        $('.faculties').html('');
                        $('.subjects').html('');
                        $('.topics').html('');
                        $('.lecture_videos').html('');
                        $('.batches').html('');
                        $('.reg_no').html('');
                        $('#reg_no_first_part').text('');
                        //$('.faculties').html(data['faculties']);
                        //$('.subjects').html(data['subjects']);
                        //$('.topics').html(data['topics']);
                        $('.lecture_videos').html(data['lecture_videos']);
                        $('.batches').html(data['batches']);

                        $('.select2').select2({ });
                        
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
                    url: '/admin/faculty-subjects',
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
                    url: '/admin/courses-faculties-subjects-batches',
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
                        url: '/admin/reg-no',
                        dataType: 'HTML',
                        data: {year: year, session_id: session_id, course_id: course_id, batch_id: batch_id},
                        success: function (data) {
                            $('#reg_no_first_part').text(data);
                            $('[name="reg_no_first_part"]').val(data);
                        }
                    });
                }
            });

            $('.select2').select2({
                //'placeholder':"Select Topic"
            });

        })
    </script>


@endsection
