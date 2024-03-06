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
                        <i class="fa fa-globe"></i>Batch Schedule List
                        @can('Batch Schedule Add')
                            <a href="{{url('admin/batch-schedules/create')}}"> <i class="fa fa-plus"></i> </a>
                        @endcan
                    </div>
                </div>

     
                <div class="portlet-body">
                    <div class="row sc_search">


                        @include('admin.components.schedule_year_course_session')
                     
                        <div class="batch">
                        </div>
                    </div>
                        <div class="text-center" style="margin-left: 15px;">
                            <button type="text" id="btnFiterSubmitSearch" class="btn btn-info">Search</button>
                        </div>

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
                                <th>Schedule Type</th>
                                <th>Actions</th>
                            </tr>
                        </thead>

                    </table>
                </div>

            </div>
        </div>



@endsection

@section('js')

    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/css/select2.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/js/select2.js" type="text/javascript"></script>
    <script src="{{ asset('assets/scripts/jquery-ui.min.js') }}"></script>

    <script type="text/javascript">

        // DO NOT REMOVE : GLOBAL FUNCTIONS!
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            }); 

             $("body").on( "change", "[name='session_id']", function() {
                var course_id = $('[name="course_id"]').val();
                var session_id = $('[name="session_id"]').val();
                var year = $('[name="year"]').val();
        
                $.ajax({
                    type: "POST",
                    url: '/admin/batch-searching',
                    dataType: 'HTML',
                    data: {session_id : session_id, year  : year, course_id : course_id },
                    success: function( data ) {
                         $('.batch').html(data); 
                         $('#batch_id').select2();
                    }
                });
            })


            $('.datatable').DataTable({
                order : [[0, 'DESC']],
                responsive: true,
                processing: true,
                serverSide: true,
                ajax: {
                    url: "/admin/batch-schedules/datatable",
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

                    {data: 'name',name:'d3.name'},
                    {data: 'action',searchable: false}
                ]
            })
            
            $('#btnFiterSubmitSearch').click(function(){
                $('.datatable').DataTable().draw(true);
            });
    
            $('.batch2').select2();
        })



    </script>

@endsection