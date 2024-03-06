@extends('admin.layouts.app')

@section('content')

    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <i class="fa fa-home"></i><a href="{{ url('/') }}">Home</a><i class="fa fa-angle-right"></i>
            </li>
            <li>Available Batches</li>
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
                        <i class="fa fa-globe"></i>Available Batches
                        @can('Available Batch Add')
                        <a href="{{ action('Admin\AvailableBatchesController@create') }}"> <i class="fa fa-plus"></i> </a>
                        @endcan
                    </div>
                </div>
                <div class="portlet-body">

                    <div class="row sc_search">
                        @include('admin.components.year_course_session')

                        <div class="form-group col-md-2">
                            <label class="col-md-3 control-label">Status</label>
                            <div class="controls">
                                <select name="status" id="status" class="form-control">
                                    <option value="">All</option>
                                    <option value="1">Active</option>
                                    <option value="0">InActive</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="text-center" style="margin-left: 15px;">
                        <button type="text" id="btnFiterSubmitSearch" class="btn btn-info">Filter</button>
                    </div>

                    <table class="table table-striped table-bordered table-hover datatable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Course Name</th>
                                <th>Displaying Batch Name</th>
                                <th>Main Batch</th>
                                <th>Starting from</th>
                                <th>Days</th>
                                <th>Time</th>
                                {{-- <td>Details</td> --}}
                                <th>Status</th>
                                <th>Action</th>
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
                    url: "/admin/available-batches-list",
                    type: 'GET',
                    data: function (d) {
                        d.status = $('#status').val();
                        d.year = $('#year').val();
                        d.course_id = $('#course_id').val();
                        d.session_id = $('#session_id').val();                       
                    }
                },
                "pageLength": 25,
                columns: [
                    {data: 'id',name:'d1.id'},
                    {data: 'course_name',name:'d1.course_name'},
                    {data: 'batch_name',name:'d1.batch_name'},
                    {data: 'main_batch_name',name:'d2.name '},
                    {data: 'start_date',name:'d1.start_date'},
                    {data: 'days',name:'d1.days'},
                    {data: 'time',name:'d1.time'},
                    // {data: 'details',name:'d1.details'},
                    {data: 'status', searchable: false},
                    {data: 'action',searchable: false},
                ]
            })
            $('#btnFiterSubmitSearch').click(function(){
                $('.datatable').DataTable().draw(true);
            });
        })
    </script>

@endsection