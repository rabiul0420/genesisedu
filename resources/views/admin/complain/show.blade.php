@extends('admin.layouts.app')

@section('content')

    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <i class="fa fa-home"></i><a href="{{ url('/') }}">Home</a><i class="fa fa-angle-right"></i>
            </li>
            <li>Doctor Profile</li>
        </ul>
    </div>

    @if(Session::has('message'))
        <div class="alert alert-success" role="alert">
            <p> {{ Session::get('message') }}</p>
        </div>
    @endif

    <div class="row">
        <div class="col-md-12">
            <!-- BEGIN EXAMPLE TABLE PORTLET-->
            <div class="portlet">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-globe"></i>Profile of <b>{{ $doctor->name }}</b>
                    </div>
                </div>


                <div class="portlet-body">

                    <div class="col-md-2" style="border-right: 2px #eeedf2 solid;">
                        <img src="{{asset($doctor->photo)}}" width="120" height="120" style="border-radius: 60px 60px 0px 0px;">
                    </div>

                    <div class="col-md-10">
                        <h4 style="color:Orange;"><b>{{ $doctor->name }}</b></h4>
                        BMDC No. : {{ $doctor->bmdc_no }} <br>
                        Email : {{ $doctor->email }} <br>
                        Phone : {{ $doctor->mobile_number }} <br>
                        Password : {{ $doctor->main_password }}
                    </div>

                    <div class="col-md-12">
                        <hr>
                    </div>

                    <table class="table table-striped table-bordered table-hover datatable">
                        <thead>
                        <tr>
                            <th>SL</th>
                            <th>Course Reg. No.</th>
                            <th>Year</th>
                            <th>Session</th>
                            <th>Course</th>
                            <th>Discipline</th>
                            <th>Batch</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>

                        @php
                            $temp_name = \App\DoctorsCourses::select('*')->where('doctor_id', $doctor->id)->get();
                            foreach ($temp_name as $k=>$value){
                        @endphp
                        <tr>
                            <td>{{ $k+1 }}</td>
                            <td>{{ $value['reg_no'] }}</td>
                            <td>{{ $value['year'] }}</td>
                            <td>
                                @php
                                    //Session Name
                                    $temp_name = \App\Sessions::select('*')->where('id', $value->session_id)->get();
                                    foreach ($temp_name as $session){
                                       echo $session->name;
                                    }
                                @endphp
                            </td>
                            <td>
                                @php
                                    //Course Name
                                    $temp_name = \App\Courses::select('*')->where('id', $value->course_id)->get();
                                    foreach ($temp_name as $course){
                                       echo $course->name;
                                    }
                                @endphp
                            </td>
                            <td>
                                @php
                                    //Discipline Name
                                    $temp_name = \App\Subjects::select('*')->where('id', $value->subject_id)->get();
                                    foreach ($temp_name as $subject){
                                       echo $subject->name;
                                    }
                                @endphp
                            </td>
                            <td>
                                @php
                                    //Batch Name
                                    $temp_name = \App\Batches::select('*')->where('id', $value->batch_id)->get();
                                    foreach ($temp_name as $batch){
                                       echo $batch->name;
                                    }
                                @endphp
                            </td>

                            <td>{{ ($doctor->status==1)?'Active':'InActive' }}</td>
                            <td>
                                <a href="{{ url('admin/view-course-result/'.$value->id) }}" class="btn btn-xs btn-primary">Result</a>
                            </td>
                        </tr>
                        @php } @endphp

                        </tbody>

                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('js')

    <script type="text/javascript">

        $(document).ready(function() {
               ////////////
        })
    </script>

@endsection
