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
                        <i class="fa fa-reorder"></i>{{ $module_name }} Create
                    </div>
                </div>

                <div class="portlet-body form">
                    <!-- BEGIN FORM-->
                    {!! Form::open(['action'=>['Admin\ProgramController@store'],'files'=>true,'class'=>'form-horizontal']) !!}
                    <div class="form-body">

                        <div class="form-group">
                            <label class="col-md-3 control-label">Name (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                            <div class="col-md-6">
                                <div class="input-icon right">
                                    <input type="text" name="name" required value="{{ old('name') }}" class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">Nick Name</label>
                            <div class="col-md-6">
                                <div class="input-icon right">
                                    <input type="text" name="nickname" value="{{ old('nickname') }}" class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label class="col-md-3 control-label">Program Type (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                            <div class="col-md-6" id="id_div_program_type">
                                @foreach($program_types as $key=>$value)
                                <label class="radio-inline">
                                    <input type="radio" name="program_type_id" required value="{{ $key }}" {{  ($key == old('program_type_id')) ? 'checked' : '' }}  > {{ $value }}
                                </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="institute">
                            <div class="form-group">
                                <label class="col-md-3 control-label">Institute (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                                <div class="col-md-3">
                                    <div class="input-icon right">
                                        @php $institutes->prepend('Select Institute', ''); @endphp
                                        {!! Form::select('institute_id',$institutes, old('institute_id'), ['class'=>'form-control','required'=>'required','id'=>'institute_id']) !!}<i></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="course">
                            <div class="form-group">
                                <label class="col-md-3 control-label">Course (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                                <div class="col-md-3">
                                    <div class="input-icon right">
                                        <div class="input-icon right">
                                            @php $courses->prepend('Select Course', ''); @endphp
                                            {!! Form::select('course_id',$courses, old('course_id'), ['class'=>'form-control','required'=>'required','id'=>'course_id']) !!}<i></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="year">
                            <div class="form-group">
                                <label class="col-md-3 control-label">Year (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                                <div class="col-md-3">
                                    <div class="input-icon right">
                                        @php $years->prepend('Select Year', ''); @endphp
                                        {!! Form::select('year',$years,old('year'),['class'=>'form-control','required'=>'required','id'=>'year']) !!}<i></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="session">
                            <div class="form-group">
                                <label class="col-md-3 control-label">Session (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                                <div class="col-md-3">
                                    <div class="input-icon right">
                                        @php $sessions->prepend('Select Session', ''); @endphp
                                        {!! Form::select('session_id',$sessions,old('session_id'),['class'=>'form-control','required'=>'required','id'=>'session_id']) !!}<i></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">Status (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>)</label>
                            <div class="col-md-6">
                                {!! Form::select('status', ['1' => 'Active','0' => 'InActive'], old('status'),['class'=>'form-control','required'=>'required']) !!}<i></i>
                            </div>
                        </div>                        

                    </div>                    

                    <div class="form-actions">
                        <div class="row">
                            <div class="col-md-offset-3 col-md-9">
                                <button type="submit" class="btn btn-info">{{ $submit_value }}</button>
                                <a href="{{ url('admin/program') }}" class="btn btn-default">Cancel</a>
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
                var view_name = 'program_course';
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: '/admin/institute-change-in-program',
                    dataType: 'HTML',
                    data: {institute_id : institute_id , view_name:view_name},
                    success: function( data ) {
                        var data = JSON.parse(data);
                        $('.course').html('');
                        $('.year').html('');
                        $('.session').html('');
                        $('.course').html(data['course']);
                    }
                });
            });

            $("body").on( "change", "[name='course_id']", function() {
                var course_id = $("[name='course_id']").val();
                var view_name = 'program_year';
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: '/admin/course-change-in-program',
                    dataType: 'HTML',
                    data: {course_id: course_id, view_name:view_name},
                    success: function( data ) {
                        var data = JSON.parse(data);
                        $('.year').html('');
                        $('.session').html('');
                        $('.year').html(data['year']);
                    }
                });
            });

            $("body").on( "change", "[name='year']", function() {
                var course_id = $("[name='course_id']").val();
                var year = $("[name='year']").val();
                var view_name = 'program_session';
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: '/admin/year-change-in-program',
                    dataType: 'HTML',
                    data: {course_id: course_id,year:year, view_name:view_name},
                    success: function( data ) {
                        var data = JSON.parse(data);
                        $('.session').html('');
                        $('.session').html(data['session']);
                    }
                });
            });


        })
    </script>


@endsection

