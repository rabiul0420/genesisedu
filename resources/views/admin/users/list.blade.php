@extends('admin.layouts.app')

@section('content')


                <div class="page-bar">
                    <ul class="page-breadcrumb">
                        <li>
                            <i class="fa fa-home"></i>
                            <a href="{{ url('admin') }}">Home</a>
                            <i class="fa fa-angle-right"></i>
                        </li>
                        <li>
                            User Account List
                        </li>
                    </ul>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-globe"></i>User Account List
                                </div>
                            </div>
                            <div class="portlet-body">
                                <table class="table table-striped table-bordered table-hover userstable datatable">
                                    <thead>
                                    <tr>
                                        <th>User ID</th>
                                        <th>User Name</th>
                                        <th>User Work Email</th>
                                        <th>User Phone</th>
                                        <th>Organization</th>
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
        // DO NOT REMOVE : GLOBAL FUNCTIONS!
        $(document).ready(function() {
            $('.datatable').DataTable({
                responsive: true,
                processing: true,
                serverSide: true,
                ajax: '/admin/users-list',
                "pageLength": 25,
                columns: [
                    {data: 'id',name:'d1.id'},
                    {data: 'autousername',name:'d1.autousername'},
                    {data: 'email',name:'d1.email'},
                    {data: 'phone_number',name:'d1.phone_number'},
                    {data: 'organization',name: 'd3.name'},
                    {data: 'status',name: 'd1.status'},
                    {data: 'action',searchable: false},
                ]
            })
        })
    </script>




@endsection