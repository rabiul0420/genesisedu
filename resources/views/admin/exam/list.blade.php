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
    <style>
        .empty-exam td {
            background-color:#FA9 !important;
            border-right-color: #ffc3b8 !important;

        }
    </style>

    <div class="row">
        <div class="col-md-12">
                        <!-- BEGIN EXAMPLE TABLE PORTLET-->
            <div class="portlet">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-globe"></i>{{ $title }}
                        @can('Exam')
                            <a href="{{url('admin/exam/create')}}"> <i class="fa fa-plus"></i> </a>
                        @endcan
                    </div>
                </div>
                <div class="portlet-body">
                    <table class="table table-striped table-bordered table-hover datatable">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Exam Name</th>
                            <th>Institute</th>
                            <th>Course</th>
                            <th>Class</th>
                            <th>Session/Year</th>
                            <th>MCQ/SBA</th>
                            <th>Total Mark/Time(In Minutes)</th>
                            <th>SIF only?</th>
                            <th>Status</th>
                            <th width="200">Actions</th>
                        </tr>
                        </thead>
                        
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('js')

    {{-- <script type="text/javascript">
        // DO NOT REMOVE : GLOBAL FUNCTIONS!
        $(document).ready(function() {
            $('.datatable').DataTable({
                responsive: true,
                "ordering": false,
                "columnDefs": [
                    { "searchable": false, "targets": 5 },
                    { "orderable": false, "targets": 5 }
                ]
            })
        })
    </script> --}}

    <script type="text/javascript">
        // DO NOT REMOVE : GLOBAL FUNCTIONS!
        $(document).ready(function() {
            $('.datatable').DataTable({
                order : [[0, 'DESC']],
                responsive: true,
                processing: true,
                serverSide: true,
                ajax: {
                    url: "/admin/admin-exam-list",
                    type: 'GET',
                },
                "pageLength": 25,
                columns: [
                    {data: 'id',name:'d1.id'},
                    {data: 'exam_name',name:'d1.name'},
                    {data: 'institutes_name',name:'d2.name'},
                    {data: 'course_name',name:'d3.name'},
                    {data: 'class_name',name:'d4.name'},
                    {data: 'session',name:'d5.name'},
                    {data: 'mcq_number',name:'d6.mcq_number'},
                    {data: 'full_mark',name:'d6.full_mark'},
                    {data: 'sif_only',name:'d1.sif_only'},
                    {data: 'status', searchable: false},
                    {data: 'action',searchable: false},
                ]
            })
        })
    </script>

@endsection
