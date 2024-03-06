@extends('admin.layouts.app')

@section('content')

    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <i class="fa fa-home"></i><a href="{{ url('/') }}">Home</a><i class="fa fa-angle-right"></i>
            </li>
          <li> <i class="fa fa-angle-right"></i> <a href="#">SMS</a> </li>
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
                        <i class="fa fa-globe"></i>SMS List
                        @can('Sms')
                        <a href="{{url('admin/sms/create')}}"> <i class="fa fa-plus"></i> </a>
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
                                <th>Date</th>
                                <th>Title</th>
                                <th>SMS For</th>
                                <th>SMS Event</th>
                                <th>Doctor course option</th>
                                <th>Status</th>
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

        function formatDate(date) {
            var d = new Date(date),
                month = (d.getMonth() + 1),
                day = d.getDate(),
                year = d.getFullYear(),
                hour = d.getHours(),
                minute = d.getMinutes(),
                second = d.getSeconds();              

            if(hour>=12)text = "PM";
            else text = "AM";

            hour = hour%12||12;                

            if (month.toString().length < 2)month = '0' + month;
            if (day.toString().length < 2)day = '0' + day;
            if (hour.toString().length < 2)hour = '0' + hour;
            if (minute.toString().length < 2)minute = '0' + minute;
            if (second.toString().length < 2)second = '0' + second;
      
            return [year, month, day].join('-') + " ( " + [hour,minute,second].join(':') + " " + text + " ) ";
        }

        // DO NOT REMOVE : GLOBAL FUNCTIONS!
        $(document).ready(function() {
            $('.datatable').DataTable({
                order : [[0, 'DESC']],
                responsive: true,
                processing: true,
                serverSide: true,
                ajax: {
                    url: "/admin/sms-list",
                    type: 'GET',
                },
                "pageLength": 10,
                columnDefs: [
                    { targets: 1, "data": "create_time", "render" : function ( data, type, row, meta ) { return formatDate(data); } },
                ],
                columns: [
                    {data: 'id',name:'d1.id'},
                    {data: 'create_time',name:'d1.created_at'},
                    {data: 'sms_title',name:'d1.title'},
                    {data: 'sms_type',name:'d1.type'},
                    {data: 'sms_event'},
                    {data: 'doctor_course_option'},
                    {data: 'status', searchable: false},
                    {data: 'action',searchable: false},
                ]
            })
        })
    </script>

@endsection