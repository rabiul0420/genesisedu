@extends('layouts.app')

@section('content')

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
                        <!-- <i class="fa fa-globe"></i>Doctors List -->
                    </div>
                </div>
                <div>
                    <?php
                    //echo '<pre>';
                    //print_r($doctors);
                    ?>
                </div>
                <div class="portlet-body">
                    <table class="table table-striped table-bordered table-hover userstable datatable">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>BMDC No</th>
                            <th>Password</th>
                            <th>Doctor Name</th>
                            <th>Registration No</th>
                            <th>Institute</th>
                            <th>Course</th>
                            <th>Faculty</th>
                            <th>Discipline</th>
                            <th>Batch</th>
                            <th>Year</th>
                            <th>Session</th>
                            <th>Branch</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($doctors_courses as $doctor_course)
                            <tr>
                                <td>{{ $doctor_course->id }}</td>
                                <td>{{ $doctor_course->doctor->bmdc_no }}</td>
                                <td>{{ $doctor_course->doctor->main_password }}</td>
                                <td>{{ $doctor_course->doctor->name }}</td>
                                <td>{{ $doctor_course->reg_no }}</td>
                                <td>{{ isset($doctor_course->institute->name) ? $doctor_course->institute->name : ''  }}</td>
                                <td>{{ isset($doctor_course->course->name) ? $doctor_course->course->name : ''  }}</td>
                                <td>{{ isset($doctor_course->faculty->name) ? $doctor_course->faculty->name : '' }}</td>
                                <td>{{ isset($doctor_course->subject->name) ? $doctor_course->subject->name : '' }}</td>
                                <td>{{ isset($doctor_course->batch->name) ? $doctor_course->batch->name : '' }}</td>
                                <td>{{ isset($doctor_course->year) ? $doctor_course->year : '' }}</td>
                                <td>{{ isset($doctor_course->session->name) ? $doctor_course->session->name : '' }}</td>
                                <td>{{ isset($doctor_course->branch->name) ? $doctor_course->branch->name : '' }}</td>
                                <td>{{ ($doctor_course->status==1)?'Active':'InActive' }}</td>
                                <td>
                                    <a href="{{ url('doctor-course/'.$doctor_course->id.'/edit') }}" class="btn btn-xs btn-primary">Edit</a>
                                    
                                    <!-- {!! Form::open(array('route' => array('doctor-course.destroy', $doctor_course->id), 'method' => 'delete','style' => 'display:inline')) !!}
                                    <button onclick="return confirm('Are You Sure ?')" class='btn btn-xs btn-danger' type="submit">Delete</button>
                                    {!! Form::close() !!} -->
                                    
                                </td>
                            </tr>
                        @endforeach

                        </tbody>

                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('js')

    <script type="text/javascript">
        
    </script>

@endsection
