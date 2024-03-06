@extends('admin.layouts.app')

@section('content')
    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <i class="fa fa-home"></i><a href="{{ url('/') }}">Home</a><i class="fa fa-angle-right"></i>
            </li>
            <li>Discipline List</li>
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
                        <i class="fa fa-globe"></i>Discipline List
                        @can('Discipline Add')
                        <a href="{{ action('Admin\SubjectsController@create') }}"> <i class="fa fa-plus"></i> </a>
                        @endcan           
                    </div>
                </div>
                <div class="portlet-body">
                    <table class="table table-striped table-bordered table-hover datatable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Discipline Name</th>
                                <th>OMR Code</th>
                                <th>Institute</th>
                                <th>Course</th>
                                <th>Faculty</th>
                                <th>Show In Combined</th>
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
        // DO NOT REMOVE : GLOBAL FUNCTIONS!
        $(document).ready(function() {
            $('.datatable').DataTable({
                responsive: true,
                processing: true,
                serverSide: true,
                ajax: {
                    url: "/admin/subjects-list",
                    type: 'GET',
                },
                "pageLength": 10,
                columns: [
                    {data: 'id',name:'d1.id'},
                    {data: 'discipline_name',name:'d1.name'},
                    {data: 'omr_code',name:'d1.subject_omr_code'},
                    {data: 'institute_name',name:'d2.name'},
                    {data: 'course_name',name:'d3.name'},
                    {data: 'faculty_name',name:'d4.name'},
                    {data: 'show_combined', searchable: false},
                    {data: 'action',searchable: false},
                ]
            })
        })
    </script>

@endsection
