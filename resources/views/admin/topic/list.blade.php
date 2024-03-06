@extends('admin.layouts.app')

@section('content')

    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <i class="fa fa-home"></i><a href="{{ url('/') }}">Home</a><i class="fa fa-angle-right"></i>
            </li>
            <li>{{ $title }}</li>
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
                        <i class="fa fa-globe"></i>{{ $title }}
                        @can('Class/Chapter Add')
                            <a href="{{url('admin/topic/create')}}"> <i class="fa fa-plus"></i> </a>
                        @endcan
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="row sc_search">

                        @include('admin.components.year_course_session_others')

                    </div>

                    <div class="text-center" style="margin-left: 15px;">
                        <button type="text" id="btnFiterSubmitSearch" class="btn btn-info">Search</button>
                        @can('Excel Download')
                            <button type="text" id="excel-download" class="btn btn-info">Excel Download</button>
                        @endcan
                    </div>

                    <table class="table table-striped table-bordered table-hover userstable datatable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Class/Chapter Name</th>
                                <th>Institute</th>
                                <th>Year</th>
                                <th>Course</th>
                                <th>Session</th>
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

<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/css/select2.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/js/select2.js" type="text/javascript"></script>

    <script type="text/javascript">
        // DO NOT REMOVE : GLOBAL FUNCTIONS!
        $(document).ready(function() {
            $('.datatable').DataTable({
                order : [[0, 'DESC']],
                responsive: true,
                processing: true,
                serverSide: true,
                ajax: {
                    url: "/admin/class-chapter-list",
                    type: 'GET',
                    data: function (d) {
                        d.year = $('#year').val();
                        d.session_id = $('#session_id').val();
                        d.course_id = $('#course_id').val();
                        d.subject_id = $('#subject_id').val();
                        d.class_id = $('#class_id').val();
                        d.faculty_id = $('#faculty_id').val();
                        d.bcps_subject_id = $('#bcps_subject_id').val();

                    }
                },
                
                "pageLength": 25,
                columns: [
                    {data: 'id',name:'d1.id'},
                    {data: 'chapte_name',name:'d1.name'},
                    {data: 'institutes_name',name:'d2.name'},
                    {data: 'year',name:'d1.year'},
                    {data: 'course_name',name:'d3.name'},
                    {data: 'session_name',name:'d4.name'},
                    {data: 'action',searchable: false},
                ]
            });
            $('#btnFiterSubmitSearch').click(function(){
                $('.datatable').DataTable().draw(true);  
            });
    
        })
    </script>

@endsection
