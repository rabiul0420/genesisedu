@extends('admin.layouts.app')

@section('content')

    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <i class="fa fa-home"></i><a href="{{ url('/') }}">Home</a><i class="fa fa-angle-right"></i>
            </li>
            <?php
            $urls='';
            foreach($breadcrumb as $key=>$value){ $urls .= $value.'/';
                echo '<li> <i class="fa fa-angle-right"></i> <a href="'.url('/').substr_replace($urls, "", -1).'">'.$value.'</a> </li>';
            }
            ?>
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
                        <i class="fa fa-globe"></i>{{ $module_name }} List
                        @can('Program')
                        <a href="{{url('admin/program/create')}}"> <i class="fa fa-plus"></i> </a>
                        @endcan
                    </div>
                </div>
                <div>
                    <div class="caption">

                    </div>
                </div>
                <div class="portlet-body">
                    <div class="row">
                        <div class="form-group col-md-2">
                            <h5>Institute <span class="text-danger"></span></h5>
                            <div class="controls">
                                @php  $institutes->prepend('Select Institute', ''); @endphp
                                {!! Form::select('institute_id',$institutes, '' ,['class'=>'form-control select2','required'=>'required','id'=>'institute_id']) !!}<i></i>
                            </div>
                        </div>
                        <div class="course">
                            <div class="form-group col-md-2">
                                <h5>Course <span class="text-danger"></span></h5>
                                <div class="controls">
                                    @php  $courses->prepend('Select Course', ''); @endphp
                                    {!! Form::select('course_id',$courses, '' ,['class'=>'form-control select2','required'=>'required','id'=>'course_id']) !!}<i></i>
                                </div>
                            </div>
                        </div>
                        <div class="year">
                            <div class="form-group col-md-2">
                                <h5>Year <span class="text-danger"></span></h5>
                                <div class="controls">
                                    @php  $years->prepend('Select Year', ''); @endphp
                                    {!! Form::select('year',$years, '' ,['class'=>'form-control select2','required'=>'required','id'=>'year']) !!}<i></i>
                                </div>
                            </div>
                        </div>

                        <div class="session">
                            <div class="form-group col-md-2">
                                <h5>Session <span class="text-danger"></span></h5>
                                <div class="controls">
                                    @php  $sessions->prepend('Select Session', ''); @endphp
                                    {!! Form::select('session_id',$sessions, '' ,['class'=>'form-control select2','required'=>'required','id'=>'session_id']) !!}<i></i>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="row">
                        <div class="form-group col-md-3">
                            <button type="text" id="btnsearch" class="btn btn-info">Search</button>
                        </div>
                    </div>
                        
                    <table class="table table-striped table-bordered table-hover datatable">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Institute</th>
                            <th>Course</th>
                            <th>Year</th>
                            <th>Session</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>

                        </tbody>
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
            var table = $('.datatable').DataTable({
                responsive: true,
                processing: true,
                serverSide: true,
                ajax: {
                    url: "/admin/program-list",
                    type: 'GET',
                    data: function (d) {
                        d.institute_id = $('[name="institute_id"]').val();
                        d.course_id = $('[name="course_id"]').val();
                        d.year = $('[name="year"]').val();
                        d.session_id = $('[name="session_id"]').val();                        
                    }
                },
                "pageLength": 10,
                columns: [
                    {data: 'id',name:'id'},
                    {data: 'name',name:'name'},
                    {data: 'program_type.name',name:'program_type.name'},
                    {data: 'institute.name',name:'institute.name',defaultContent: ''},
                    {data: 'course.name',name:'course.name',defaultContent: ''},
                    {data: 'year',name:'year',defaultContent: ''},
                    {data: 'session.name',name:'session.name',defaultContent: ''},
                    {data: 'status',name:'status'},
                    {data: 'action',searchable: false},
                ]
            });

            $('#btnsearch').click(function(){
                $('.datatable').DataTable().draw(true);
            });

            $("body").on( "change", "[name='institute_id']", function() {
                var institute_id = $(this).val();
                var view_name = 'program_course_search';
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: '/admin/institute-change-in-program',
                    dataType: 'HTML',
                    data: {institute_id : institute_id , view_name:view_name},
                    success: function( data ) {
                        var data = JSON.parse(data);
                        $('.course').html('');
                        $('.year').html('');
                        $('.session').html('');
                        $('.course').html(data['course']);
                    }
                });
            });

            $("body").on( "change", "[name='course_id']", function() {
                var course_id = $("[name='course_id']").val();
                var view_name = 'program_year_search';
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: '/admin/course-change-in-program',
                    dataType: 'HTML',
                    data: {course_id: course_id, view_name:view_name},
                    success: function( data ) {
                        var data = JSON.parse(data);
                        $('.year').html('');
                        $('.session').html('');
                        $('.year').html(data['year']);
                    }
                });
            });

            $("body").on( "change", "[name='year']", function() {
                var course_id = $("[name='course_id']").val();
                var year = $("[name='year']").val();
                var view_name = 'program_session_search';
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: '/admin/year-change-in-program',
                    dataType: 'HTML',
                    data: {course_id: course_id,year:year, view_name:view_name},
                    success: function( data ) {
                        var data = JSON.parse(data);
                        $('.session').html('');
                        $('.session').html(data['session']);
                    }
                });
            });
        })
    </script>

@endsection
