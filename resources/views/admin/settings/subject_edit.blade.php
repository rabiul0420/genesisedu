@extends('admin.layouts.app')

@section('content')

    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <i class="fa fa-home"></i><a href="{{ url('/admin') }}">Home</a><i class="fa fa-angle-right"></i>
            </li>
            <li>{{ $title }}</li>
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
                        <i class="fa fa-reorder"></i>{{ $title }}
                    </div>
                </div>
                <div class="portlet-body form">
                    <!-- BEGIN FORM-->

                    {!! Form::open(['action'=>['Admin\SubjectsController@update', $subject->id],'method'=>'PUT','files'=>true,'class'=>'form-horizontal']) !!}
                    <div class="form-body">

                        <div class="form-group">
                            <label class="col-md-3 control-label">Name (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                            <div class="col-md-3">
                                <div class="input-icon right">
                                    <input type="text" name="name" value="{{ $subject->name }}" required class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">OMR code (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                            <div class="col-md-3">
                                <div class="input-icon right">
                                    <input type="text" name="subject_omr_code" required value="{{ $subject->subject_omr_code }}" maxlength="1" minlength="1" class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">Institute (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                            <div class="col-md-3">
                                @php $institute->prepend('Select Institute', ''); @endphp
                                {!! Form::select('institute_id', $institute, $subject->institute_id, ['class'=>'form-control','required'=>'required']) !!}<i></i>

                            </div>
                        </div>

                        <div class="form-group">
                            <div class="course">
                            <label class="col-md-3 control-label">Course (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                            <div class="col-md-3">
                                @php $course->prepend('Select Course', ''); @endphp
                                {!! Form::select('course_id', $course, $subject->course_id, ['class'=>'form-control','required'=>'required']) !!}<i></i>
                            </div>
                            </div>
                        </div>
 


                        {{--@if($subject->faculty_id)--}}
                            <div class="form-group">
                                <div class="faculty">
                                    <label class="col-md-3 control-label">Faculty</label>
                                    <div class="col-md-3">
                                        @php $faculty->prepend('Select Faculty', ''); @endphp
                                        {!! Form::select('faculty_id', $faculty, $subject->faculty_id, ['class'=>'form-control']) !!}<i></i>
                                    </div>
                                </div>
                            </div>
                        {{--@endif--}}

                        <div class="form-group">
                            <label class="col-md-3 control-label">Select Status (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                            <div class="col-md-3">
                                {!! Form::select('status', ['1' => 'Active','0' => 'InActive'], $subject->status,['class'=>'form-control','required'=>'required']) !!}<i></i>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">Show in Combined (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                            <div class="col-md-3">
                                {!! Form::select('show_in_combined',['No','Yes'], old('show_in_combined', $subject->show_in_combined),[ 'class'=>'form-control' ]) !!}<i></i>
                            </div>
                        </div>



                    </div>
                    <div class="form-actions">
                        <div class="row">
                            <div class="col-md-offset-3 col-md-9">
                                <button type="submit" class="btn btn-info">Submit</button>

                                <a href="{{ url('admin/subjects') }}" class="btn btn-default">Cancel</a>

                            </div>
                        </div>
                    </div>
                {!! Form::close() !!}
                <!-- END FORM-->

                </div>
            </div>

        </div>
    </div>



@endsection

@section('js')

    <script type="text/javascript">
        // DO NOT REMOVE : GLOBAL FUNCTIONS!
        $(document).ready(function() {

            $("body").on( "change", "[name='institute_id']", function() {
                var institute_id = $(this).val();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: '/admin/institute-course',
                    dataType: 'HTML',
                    data: {institute_id : institute_id},
                    success: function( data ) {
                        $('.course').html(data);
                        $('.faculty').html('');
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
                    url: '/admin/course-faculty',
                    dataType: 'HTML',
                    data: {course_id: course_id},
                    success: function( data ) {
                        $('.faculty').html(data);
                    }
                });
            })

        })
    </script>




@endsection