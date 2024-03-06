@extends('admin.layouts.app')

@section('content')

    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <i class="fa fa-home"></i><a href="{{ url('/') }}">Home</a><i class="fa fa-angle-right"></i>
            </li>
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
                        <i class="fa fa-globe"></i>Batch Schedule Trash List
                    </div>
                </div>

                <div class="portlet-body">
                    <table class="table table-striped table-bordered table-hover userstable datatable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Year</th>
                                <th>Session</th>
                                <th>Course</th>
                                <th>Batch</th>
                                <th>Faculty</th>
                                <th>Discipline</th>
                                <th>FCPS Part-1 Discipline</th>
                                <th>Deleted at</th>
                                <th>Actions</th>
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
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

            $('.datatable').DataTable({
                order : [[9, 'DESC']],
                responsive: true,
                processing: true,
                serverSide: true,
                ajax: {
                    url: "/admin/batch-schedules/batch_schedule_trash_list",
                    type: 'GET',
                    data: function (d) {
                        d.year = $('#year').val();
                        d.course_id = $('#course_id').val();
                        d.session_id = $('#session_id').val();
                        d.batch_id = $('#batch_id').val();
     
                    }
                },
                "pageLength": 10,
                columns: [
                    {data: 'id',name:'d1.id'},
                    {data: 'batch_schedule',name:'d1.name'},
                    {data: 'year',name:'d1.year'},
                    {data: 'session_name',name:'d5.name'},
                    {data: 'course_name',name:'d2.name'},
                    {data: 'batch_name',name:'d4.name'},

                    {data: 'faculty_name',name:'f.name'},
                    {data: 'subject_name',name:'s.name'},
                    {data: 'bcps_subject_name',name:'bs.name'},

                    {data: 'deleted_at',name:'d1.deleted_at', render(date){
                        const d  = new Date( date );
                        return `${d.getDate()} ${months[d.getMonth()]}, ${d.getFullYear()} ${d.getHours()}:${d.getMinutes()}`;
                    }},
                    {data: 'action',searchable: false}
                ]
            })
        })
    </script>

@endsection
