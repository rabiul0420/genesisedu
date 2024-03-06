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
                Doctor Course Create
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
                        <i class="fa fa-reorder"></i>Doctor Course Create
                    </div>
                </div>
                <div class="portlet-body form">
                    <!-- BEGIN FORM-->
                    {!! Form::open(['action'=>'Admin\DoctorsCoursesController@doctor_course_payment','method'=>'post','class'=>'form-horizontal']) !!}
                    <div class="form-body">

                        <div class="panel panel-primary"  style="border-color: #eee; ">
                            <div class="panel-heading" style="background-color: #eee; color: black; border-color: #eee; "> Doctor Course Payment</div>
                            <div class="panel-body">
                                <input type="hidden" name="doctor_course_id" value="{{ $doctor_course->id }}">
                                <div class="form-group">

                                    <label class="col-md-3 control-label">Transaction ID (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>)  </label>
                                    <div class="col-md-3">
                                        <input type="text" name="trans_id" class="form-control" required>
                                    </div>

                                </div>
                                <div class="form-group">

                                    <label class="col-md-3 control-label">Amount (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>)  </label>
                                    <div class="col-md-3">
                                        <input type="number" name="amount" class="form-control" required value="{{ $amount }}" {{ $readonly }} min="{{ $min }}" max="{{ $max }}">
                                        <span class="text-success"><b> ( Total Course Fee : {{ $doctor_course->course_price }}  Discount : Delivery Charge : {{ ($doctor_course->include_lecture_sheet == '1' && $doctor_course->delivery_status == '1' && $doctor_course->courier_upazila_id == 493) ? "200" : (($doctor_course->include_lecture_sheet && $doctor_course->delivery_status == '1' && $doctor_course->courier_upazila_id != 493) ? '250' : '')}})</b></span>
                                    </div>

                                </div>

                            </div>
                        </div>

                    </div>
                    <div class="form-actions">
                        <div class="form-group">
                            <label class="col-md-3 control-label"></label>
                            <div class="col-md-3">
                                <button onclick="return confirm('Are You Sure ?')" class='btn btn-md btn-primary' type="submit">Submit Payment</button>
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
