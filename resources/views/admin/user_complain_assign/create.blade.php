
@extends('admin.layouts.app')

@section('content')

    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <i class="fa fa-home"></i><a href="{{ url('/') }}">Home</a><i class="fa fa-angle-right"></i>
            </li>
            <li>User Complain Assign List</li>
        </ul>
    </div>

    @if(Session::has('message'))
        <div class="alert {{ Session::get('class') ?? 'alert-success' }} " role="alert">
            <p> {{ Session::get('message') }}</p>
        </div>
    @endif

    <div class="row">
        <div class="col-md-12">
            <!-- BEGIN EXAMPLE TABLE PORTLET-->
            <div class="portlet">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-reorder"></i>User Complain Assign
                    </div>
                </div>
                
                <div class="portlet-body form">
                    <!-- BEGIN FORM-->
                    {!! Form::open(['action'=>['Admin\UserComplainAssignController@store'],'files'=>true,'class'=>'form-horizontal']) !!}
                    <div class="form-body">
                        
                        <div class="form-group">
                            <label class="col-md-3 control-label">User(<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>)</label>
                            <div class="col-md-4">                                
                                <div class="controls">
                                    <select name="user_id" id="" class="form-control course">
                                        <option value="" selected>--Select User---</option>
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                 </div>                           
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-3 control-label">Course Complain Type(<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>)</label>
                            <div class="col-md-4">                                
                                <div class="controls">
                                    <select name="course_complain_type_ids[]" id="" class="form-control course" multiple>
                                        <option value="" selected>--Select Course Complain---</option>
                                        @foreach ($course_complains as $course_complain)
                                        {{-- {{ $course_complain }} --}}
                                            <option value="{{ $course_complain->id }}">{{ 'Course Name : '. $course_complain->course->name . ' - ' . 'Complain Type : ' .$course_complain->complain_type->name }}</option>
                                        @endforeach
                                    </select>
                                 </div>                           
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="col-md-3 control-label">Select Status (<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>) </label>
                            <div class="col-md-4">
                                {!! Form::select('status', ['1' => 'Active','0' => 'InActive'], old('status'),['class'=>'form-control','required'=>'required']) !!}<i></i>
                            </div>
                        </div> 
                        
                    </div>

                    <div class="form-actions">
                        <div class="row">
                            <div class="col-md-offset-3 col-md-9">
                                <button type="submit" class="btn btn-info">Submit</button>
                                <a href="{{ url('admin/discount') }}" class="btn btn-default">Cancel</a>
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
        $(document).ready(function(){
            $('.course').select2({});    
        });

    </script>

@endsection


