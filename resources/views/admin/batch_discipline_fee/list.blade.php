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
                        @can('Batch Discipline Fee Add')
                        <a href="{{ action('Admin\BatchDisciplineFeeController@create') }}"> <i class="fa fa-plus"></i> </a>
                        @endcan              
                    </div>
                </div>
                <div class="portlet-body">
                    <table class="table table-striped table-bordered table-hover datatable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Batch Name</th>
                                <th>Discipline</th>
                                <th>Admission Fee</th>
                                <th>Lecture Sheet Fee</th>
                                <th>Discount From Regular</th>
                                <th>Discount From Exam</th>
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
                order : [[0, 'DESC']],
                responsive: true,
                processing: true,
                serverSide: true,
                ajax: {
                    url: "/admin/batch-discipline-list",
                    type: 'GET',
                },
                "pageLength": 10,
                columns: [
                    {data: 'id',name:'d1.id'},
                    {data: 'batch_name',name:'d2.name'},
                    {data: 'discipline_name',name:'d3.name'},
                    {data: 'admission_fee',name:'d1.admission_fee'},
                    {data: 'lecture_sheet_fee',name:'d1.lecture_sheet_fee'},
                    {data: 'discount_from_regular',name:'d1.discount_from_regular'},
                    {data: 'discount_from_exam',name:'d1.discount_from_exam'},
                    {data: 'action',searchable: false},
                ]
            })
        })
    </script>

@endsection
