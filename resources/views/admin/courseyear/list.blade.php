@extends('admin.layouts.app')

@section('content')

    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <i class="fa fa-home"></i><a href="{{ url('/') }}">Home</a><i class="fa fa-angle-right"></i>
            </li>
           <li>admin</li><i class="fa fa-angle-right"></i>
           <li>course year</li>
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
                        <i class="fa fa-globe"></i>Course Year List
                        @can('Add Course Year')                      
                        <a href="{{url('admin/course-year/create')}}"> <i class="fa fa-plus"></i> </a>
                        @endcan
                    </div>

                </div>
                <div>
                    <div class="caption">

                    </div>
                </div>
                <div class="portlet-body">
                    <table class="table table-striped table-bordered table-hover datatable">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Course</th>                           
                            <th>Year</th> 
                            <th>Sessions</th> 
                            <th>Status</th>                         
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                            @foreach($course_years as $course_year)
                       
                                <tr>
                                    <td>{{ $course_year->id }}</td>
                                    <td>{{ $course_year->course->name }}</td>
                                    <td>{{ $course_year->year }}</td>                                  
                                    <td>
                                        @foreach($course_year->course_year_session as $course_year_session)
                                           {{ $course_year_session->session->name ?? ''}}<br>
                                        @endforeach
                                    </td>                                  
                                
                                                                  
                                    <td>{{$course_year->status==1? 'Active':'Inactive' }}</td>
                                    <td>
                                        @can('Edit Course Year')   
                                        <a href="{{ url('admin/course-year/'.$course_year->id.'/edit') }}" class="btn btn-xs btn-primary">Edit</a>
                                        @endcan
                                        @can('Delete Course Year')
                                        {!! Form::open(array('route' => array('course-year.destroy', $course_year->id), 'method' => 'delete','style' => 'display:inline')) !!} 
                                        <button onclick="return confirm('Are You Sure ?')" class='btn btn-xs btn-danger' type="submit">Delete</button> 
                                        {!! Form::close() !!}   
                                        @endcan                                      
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
        // DO NOT REMOVE : GLOBAL FUNCTIONS!
        $(document).ready(function() {
            $('.datatable').DataTable({
                responsive: true,
                // "columnDefs": [
                //     { "searchable": false, "targets": 5 },
                //     { "orderable": false, "targets": 5 }
                // ]
            })
        })
    </script>

@endsection
