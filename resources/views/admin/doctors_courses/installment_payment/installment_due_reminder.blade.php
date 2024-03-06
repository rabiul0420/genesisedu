@extends('admin.layouts.app')

@section('content')


    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <i class="fa fa-home"></i>
                <a href="{{ url('/') }}">Home</a>
                <i class="fa fa-angle-right"></i>
            </li>
            <li>Installment Due Reminder</li>
        </ul>

    </div>

    @if(Session::has('message'))
        <div class="alert {{ (Session::get('class'))?Session::get('class'):'alert-success' }}" role="alert">
            <p> {{ Session::get('message') }}</p>
        </div>
    @endif

    <style>
        
    </style>

    <div class="row">
        <div class="col-md-12">
            <!-- BEGIN EXAMPLE TABLE PORTLET-->
            <div class="portlet">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-reorder"></i>Installment Due Reminder 
                        <a href="{{ url('admin/installment-due-list') }}" class="btn btn-xs btn-info">Installment due list</a>
                    </div>
                </div>
                <div class="portlet-body form">
                    <!-- BEGIN FORM-->
                    
                    <div class="form-body">

                        <div class="panel panel-primary"  style="border-color: #eee; ">
                            <div class="panel-heading" style="background-color: #eee; color: black; border-color: #eee; "> Installment Due Reminder Sms Sent : </div>
                            <div class="panel-body">
                                <div>
                                    @if(isset($doctors_courses) && count($doctors_courses))
                                    <table class="table table-striped table-bordered table-hover userstable datatable dataTable no-footer">
                                        <tr>
                                            <th>Serial No</th><th>Doctor</th><th>Reg No</th><th>Mobile No</th>
                                        </tr>
                                        @foreach($doctors_courses as $k=>$doctor_course)
                                        <tr>
                                            <td>{{ $k + 1 }}</td><td>{{ $doctor_course->doctor->name ?? '' }}</td><td>{{ $doctor_course->reg_no ?? ''}}</td><td>{{ $doctor_course->doctor->mobile_number ?? ''}}</td>
                                        </tr>
                                        @endforeach                                        
                                    </table>
                                    @else
                                    <div>No SMS is sent to doctors.</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>                    
                    <!-- END FORM-->                    
                </div>  
            </div>
            <!-- END EXAMPLE TABLE PORTLET-->
        </div>
    </div>
    <!-- END PAGE CONTENT-->

@endsection

@section('js')

@endsection