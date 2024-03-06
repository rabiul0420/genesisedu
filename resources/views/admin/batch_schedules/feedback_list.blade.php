@extends('admin.layouts.app')

@section('content')
    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <i class="fa fa-home"></i><a href="{{ url('/') }}">Home</a><i class="fa fa-angle-right"></i>
            </li>
        </ul>
    </div>

    @if (Session::has('message'))
        <div class="alert alert-success" role="alert">
            <p> {{ Session::get('message') }}</p>
        </div>
    @endif

    <style>
        .feedback-table thead tr th {
            font-weight: bold;
            text-align: center;
        }

        .feedback-table tbody tr td {
            text-align: left;
        }

    </style>


    <div class="row">
        <div class="col-md-12">
            <!-- BEGIN EXAMPLE TABLE PORTLET-->
            <div class="portlet">
                <div class="portlet-body row sc_search">
                    <table class="table table-striped table-bordered table-hover feedback-table datatable">
                        <thead>
                            <tr>
                                <th>Date & Time</th>
                                <th>Video ID</th>
                                <th style="max-width: 30%;">Video Name</th>
                                <th>Teacher</th>
                                <th>Batch Name</th>
                                <th>Doctor Name</th>
                                <th>BMDC</th>
                                <th>Feedback</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
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
                        url: "/admin/doctors-feedback-list",

                    },
                    columns: [{
                            data: 'datetime',
                            name: 'dcv.updated_at'
                        },
                        {
                            data: 'id',
                            name: 'dcv.id'
                        },
                        {
                            data: 'lecture_video',
                            name: 'lv.name'
                        },
                        {
                            data: 'mentor',
                            name: 't.name'
                        },
                        {
                            data: 'batch_name',
                            name: 'b.name'
                        },
                        {
                            data: 'doctor_name',
                            name: 'd.name'
                        },
                        {
                            data: 'doctor_bmdc',
                            name: 'd.bmdc_no'
                        },
                        {
                            data: 'feedback',
                            name: 'dcv.feedback'
                        },
                        {
                            data: 'action',
                            searchable: false
                        }
                    ],
                    "order": [
                        [0, "desc"]
                    ]
                });

                $('#btnFiterSubmitSearch').click(function() {
                    $('.datatable').DataTable().draw(true);
                });
            })
        </script>
    @endsection
