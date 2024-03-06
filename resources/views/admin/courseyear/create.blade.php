@extends('admin.layouts.app')

@section('content')


    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <i class="fa fa-home"></i>
                <a href="{{ url('/') }}">Home</a>
                <i class="fa fa-angle-right"></i>
            </li>
            <li>admin</li><i class="fa fa-angle-right"></i>
            <li>course year</li><i class="fa fa-angle-right"></i>
           <li> create</li>
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
                        <i class="fa fa-reorder"></i>Course Year Create
                    </div>
                </div>
                <div class="portlet-body form">
                    <!-- BEGIN FORM-->
                    {!! Form::open(['action'=>'Admin\CourseYearController@store','files'=>true,'class'=>'form-horizontal']) !!}
                    <div class="form-body">
                       
                        <div class="form-group">
                            <label class="col-md-3 control-label">Course (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                            <div class="col-md-3">
                                @php  $course->prepend('Select Course', ''); @endphp
                                {!! Form::select('course_id',$course, old('course_id'),['class'=>'form-control','required'=>'required']) !!}<i></i>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">Year (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                            <div class="col-md-3">
                                <div class="input-icon right">
                                    <select name="year" class="form-control">
                                        <option value="">Select year</option>
                                        <option {{ old('year')==(date('Y')+1) ? 'selected' : '' }} value="{{ date('Y')+1 }}">{{ date('Y')+1 }}</option>
                                        <option {{ old('year')==(date('Y')) ? 'selected' : '' }} value="{{ date('Y') }}">{{ date('Y') }}</option>
                                        <option {{ old('year')==(date('Y')-1) ? 'selected' : '' }} value="{{ date('Y')-1 }}">{{ date('Y')-1 }}</option>
                                        <option {{ old('year')==(date('Y')-2) ? 'selected' : '' }} value="{{ date('Y')-2 }}">{{ date('Y')-2 }}</option>
                                        <option {{ old('year')==(date('Y')-3) ? 'selected' : '' }} value="{{ date('Y')-3 }}">{{ date('Y')-3 }}</option>
                                        
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">Session (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                            <div class="col-md-3">
                                <div class="input-icon right">
                                    @php $sessions->prepend( 'Select sessions', '' ) @endphp
                                    {!! Form::select( 'session_id[]',$sessions, '', ['class'=>'form-control  select2 ','data-placeholder' => 'Select Faculty','multiple' => 'multiple',] ) !!}<i></i>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">Select Status (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                            <div class="col-md-3">
                                {!! Form::select('status', ['1' => 'Active','0' => 'InActive'], old('status'),['class'=>'form-control']) !!}<i></i>
                            </div>
                        </div>
                        
                    </div>
                    <div class="form-actions">
                        <div class="form-actions">
                            <div class="row">
                                <div class="col-md-offset-3 col-md-9">
                                    <button type="submit" class="btn btn-info">Submit</button>
                                    <a href="{{ url('admin/course-year') }}" class="btn btn-default">Cancel</a>
                                </div>
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
        // DO NOT REMOVE : GLOBAL FUNCTIONS!
        $(document).ready(function() {

            
            $('.select2').select2();
            
          
        
        })
    </script>

@endsection