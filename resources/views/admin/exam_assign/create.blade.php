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
                        <i class="fa fa-reorder"></i><?php echo $module_name;?> Create
                    </div>
                </div>

                <div class="portlet-body form">
                    <!-- BEGIN FORM-->
                    {!! Form::open(['action'=>['Admin\ExamAssignController@store'],'files'=>true,'class'=>'form-horizontal']) !!}
                    <div class="form-body">

                        <div class="exams">
                            <div class="form-group">
                                <label class="col-md-3 control-label">Exam (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                                <div class="col-md-3">
                                    <div class="input-icon right">
                                        @php  $exams->prepend('Select Exam', ''); @endphp
                                        {!! Form::select('exam_id',$exams, old('exam_id')?old('exam_id'):'' ,['class'=>'form-control select2','required'=>'required','id'=>'exam_id']) !!}<i></i>

                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="institutes">
                            <div class="form-group">
                                <label class="col-md-3 control-label">Institute (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                                <div class="col-md-3">
                                    <div class="input-icon right">
                                        @php  $institutes->prepend('Select Institute', ''); @endphp
                                        {!! Form::select('institute_id',$institutes, old('institute_id')?old('institute_id'):'' ,['class'=>'form-control','required'=>'required','id'=>'institute_id']) !!}<i></i>

                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="courses">

                        </div>

                        <div class="faculties">

                        </div>

                        <div class="disciplines">

                        </div>

                    </div>

                    <div class="form-group">
                        <label class="col-md-3 control-label">Select Status (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>)</label>
                        <div class="col-md-3">
                            {!! Form::select('status', ['1' => 'Active','0' => 'InActive'], old('status'),['class'=>'form-control']) !!}<i></i>
                        </div>
                    </div>

                    <div class="form-actions">
                        <div class="row">
                            <div class="col-md-offset-3 col-md-9">
                                <button type="submit" class="btn btn-info">{{ $submit_value }}</button>
                                <a href="{{ url('admin/exam-assign') }}" class="btn btn-default">Cancel</a>
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
                    url: '/admin/institute-courses',
                    dataType: 'HTML',
                    data: {institute_id : institute_id},
                    success: function( data ) {
                        $('.courses').html('');
                        $('.faculties').html('');
                        $('.disciplines').html('');
                        $('.topics').html('');
                        $('.courses').html(data);
                    }
                });
            })

            $("body").on( "change", "[name='course_id']", function() {
                var institute_id = $("[name='institute_id']").val();
                var course_id = $(this).val();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: '/admin/course-changed-in-lecture-videos',
                    dataType: 'HTML',
                    data: {institute_id:institute_id,course_id: course_id},
                    success: function( data ) {
                        var data = JSON.parse(data);
                        $('.faculties').html('');
                        $('.disciplines').html('');
                        $('.topics').html('');
                        $('.faculties').html(data['faculties']);
                        $('.disciplines').html(data['subjects']);
                        $('.topics').html(data['topics']);
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
                    url: '/admin/faculty-changed-in-lecture-videos',
                    dataType: 'HTML',
                    data: {institute_id:institute_id,course_id:course_id,faculty_id: faculty_id},
                    success: function( data ) {
                        var data = JSON.parse(data);
                        $('.disciplines').html('');
                        $('.disciplines').html(data['subjects']);
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

