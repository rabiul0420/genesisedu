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
                            <a href="{{url('admin/batches-schedules/create')}}"> <i class="fa fa-plus"></i> </a>
                        @endcan
                    </div>
                </div>
                <div>
                <div class="portlet-body">
                    <div class="row sc_search">
                        <div class="form-group col-md-2">
                            <h5>Year <span class="text-danger"></span></h5>
                            <div class="controls">
                                {!! Form::select('year',$years, '' ,['class'=>'form-control year','required'=>'required','id'=>'year']) !!}<i></i>
                            </div>
                        </div>
                        <div class="course">
                            <div class="form-group col-md-2">
                                <h5>Course <span class="text-danger"></span></h5>
                                <div class="controls">
                                    @php  $courses->prepend('Select Course', ''); @endphp
                                    {!! Form::select('course_id',$courses, '' ,['class'=>'form-control batch2 course_id','required'=>'required','id'=>'course_id']) !!}<i></i>
                                </div>
                            </div>
                        </div>
                        <div class="session">
                            <div class="form-group col-md-2">
                                <h5>Session <span class="text-danger"></span></h5>
                                <div class="controls">
                                    @php  $sessions->prepend('Select Session', ''); @endphp
                                    {!! Form::select('session_id',$sessions, '' ,['class'=>'form-control','required'=>'required','id'=>'session_id']) !!}<i></i>
                                </div>
                            </div>
                        </div>
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
                            <th>Schedule Type</th>
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
    <script src="{{ asset('assets/scripts/jquery-ui.min.js') }}"></script>

    <script type="text/javascript">

        // DO NOT REMOVE : GLOBAL FUNCTIONS!
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            }); 


            $("body").on( "change", "[name='course_id']", function() {
                var course_id = $(this).val();
                var year = $('.year').val();

                $.ajax({
                    type: "POST",
                    url: '/admin/session-searching',
                    dataType: 'HTML',
                    data: {course_id : course_id },
                    success: function( data ) {
                         $('.session').html(data); 
                    }
                });
            })

             $("body").on( "change", "[name='session_id']", function() {
                var session_id = $(this).val();
                var course_id = $('.course_id').val();
                var year = $('.year').val();

                $.ajax({
                    type: "POST",
                    url: '/admin/batch-searching',
                    dataType: 'HTML',
                    data: {session_id : session_id, course_id : course_id, year : year },
                    success: function( data ) {
                         $('.batch').html(data); 
                    }
                });
            })


            $('.datatable').DataTable({
                responsive: true,
                processing: true,
                serverSide: true,
                ajax: {
                    url: "/admin/batch-schedule-list",
                    type: 'GET',
                    data: function (d) {
                        d.year = $('#year').val();
                        d.course_id = $('#course_id').val();
                        d.session_id = $('#session_id').val();
                        d.batch_id = $('#batch_id').val();
     
                    }
                },
                "pageLength": 20,
                columns: [
                    {data: 'id',name:'d1.id'},
                    {data: 'batch_schedule',name:'d1.name'},
                    {data: 'year',name:'d1.year'},
                    {data: 'session_name',name:'d5.name'},
                    {data: 'course_name',name:'d2.name'},
                    {data: 'batch_name',name:'d4.name'},
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