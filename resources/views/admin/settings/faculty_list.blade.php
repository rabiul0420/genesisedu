@extends('admin.layouts.app')

@section('content')

    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <i class="fa fa-home"></i><a href="{{ url('/') }}">Home</a><i class="fa fa-angle-right"></i>
            </li>
            <li>Faculty List</li>
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
                        <i class="fa fa-globe"></i>Faculty List 
                        @can('Faculty Add')
                        <a href="{{ action('Admin\FacultyController@create') }}"> <i class="fa fa-plus"></i> </a>   
                        @endcan          
                    </div>
                </div>
                <div class="portlet-body">
                    <table class="table table-striped table-bordered table-hover datatable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Institute</th>
                                <th>Course</th>
                                <th>OMR Code</th>
                                <th>Faculty Name</th>
                                <th>Show in Combined</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('js')

    <script type="text/javascript">
        $(document).ready(function() {
            $('.datatable').DataTable({
                // responsive: true,
                // processing: true,
                // serverSide: true,  
                ajax: {
                    url: "/admin/faculty-list",
                    type: 'GET',
                },
                "pageLength": 10,
                columns: [
                    {data: 'id',name:'f.id'},
                    {data: 'institute_name',name:'i.name'},
                    {data: 'course_name',name:'c.name'},
                    {data: 'omr_code',name:'f.faculty_omr_code'},
                    {data: 'faculty_name',name:'f.faculty_name'},
                    {data: 'show_combined',name:'f.show_in_combined'},
                    {data: 'action',searchable: false},
                ]
            })
        })
    </script>

@endsection