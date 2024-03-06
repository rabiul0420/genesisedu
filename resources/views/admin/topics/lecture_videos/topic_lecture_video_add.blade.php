@extends('admin.layouts.app')

@section('content')

    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <i class="fa fa-home"></i><a href="{{ url('/') }}">Home</a><i class="fa fa-angle-right"></i>
            </li>
            <li> Add Lecture Videos in {{ $topic->name }}</li>
        </ul>
    </div>

    @if(Session::has('message'))
        <div class="alert alert-success" role="alert">
            <p> {{ Session::get('message') }}</p>
        </div>
    @endif


    <style>
        
        input[type=checkbox][disabled] {
            outline: 5px solid #31b0d5;
            outline-offset: -20px;
        }

        .title-content
        {
            padding: 20px;
            font-size: 27px;
            text-align: center;
            font-weight:700;
            color:blueviolet;
        }

    </style>

    <div class="row title-content">
        TOPIC LECTURE VIDEO ADD
    </div>

    <div class="row">
        <div class="col-md-12">
            <!-- BEGIN EXAMPLE TABLE PORTLET-->
            <div class="portlet">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-globe"></i> Add Lecture Videos in {{ $topic->name }}
                        <input type="hidden" name="topic_id" value="{{ $topic->id }}"/>
                        @can('Topic')
                        <a href="{{url('admin/topics')}}" class="btn btn-xs btn-primary">Topics</a>
                        @endcan
                        @can('Topic Content')
                        <a href="{{url('admin/topics-contents/'.$topic->id)}}" class="btn btn-xs btn-info">Topic Contents</a>
                        @endcan
                        @can('Topic Content')
                        <a href="{{url('admin/topic-lecture-video-list/'.$topic->id)}}" class="btn btn-xs btn-success">Topic Lecture Videos</a>
                        @endcan
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
                    <table id="table_1" class="table table-striped table-bordered table-hover datatable">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Lecture Video</th>
                            <th>Institute</th>
                            <th>Course</th>
                            <th>Year</th>
                            <th>Session</th>
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

  <div class="modal fade" id="question" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Question</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          ...
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

@endsection

@section('js')

<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />

    <script type="text/javascript">

        // DO NOT REMOVE : GLOBAL FUNCTIONS!
        $(document).ready(function() {

            $(".select2").select2({
                // minimumInputLength: 3,
                allowClear : true,
                tags : true,
                tokenSeparators : [',']
            });

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $(document).on('change', '[name="lecture_video_id"]', function() {
        
                var operation = "";
                if(this.checked == true)
                {
                    operation = "insert";      
                }
                else if(this.checked == false)
                {
                    operation = "delete";
                }

                var lecture_video_id = $(this).val();
                var topic_id = $('[name="topic_id"]').val();                

                $.ajax({
                    type: "POST",
                    url: '/admin/topic-lecture-video-save',
                    dataType: 'HTML',
                    data: {lecture_video_id : lecture_video_id, topic_id : topic_id, operation : operation },
                    success: function( data ) { 
                        var data = JSON.parse(data);
                        $("#label_"+data['lecture_video_id']).html(data['message']);
                        
                        if(data['status'] == "insert_success")
                        {
                            $("#label_m_"+data['lecture_video_id']).removeClass("btn btn-info").addClass("btn btn-danger");
                            $("#label_m_"+data['lecture_video_id']).html("Delete From Topic");
                        }
                        if(data['status'] == "delete_success")
                        {
                            $("#label_m_"+data['lecture_video_id']).removeClass("btn btn-danger").addClass("btn btn-info");
                            $("#label_m_"+data['lecture_video_id']).html("Add to Topic");
                        }
                                                            
                        if(data['status'] == "completed" || data['status'] == "data_already_exist" )
                        {
                            window.location.href = "/admin/topic-lecture-video-list/"+data['topic_id'];
                        }
                        
                    }
                });   
                
                
            });


            $("#table_1").on("mouseover", 'td' , function () {
                
                $(this).css('cursor','pointer');                        
                
            });

            $("#table_1").on("click", 'td' , function () {
                
                var lecture_video_id = $(this).closest('tr').find('td').first().html();
                
                if(!isNaN(lecture_video_id) && $(this).index() != ( $(this).closest('tr').children('td').length - 1 ) )
                {
                    $('#question_answer .modal-body').load('/admin/get-question-details',{lecture_video_id : lecture_video_id, _token: '{{ csrf_token() }}'},function(){
                        $('#question_answer').modal({show:true});
                    });
                }                                
                
            });

            var table = $('.datatable').DataTable({
                responsive: true,
                processing: true,
                serverSide: true,
                ajax: {
                    url: "/admin/topic-lecture-video-add-list",
                    type: 'GET',
                    data: function (d) {
                        d.topic_id = $('[name="topic_id"]').val();
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
                    {data: 'institute.name',name:'institute.name',defaultContent: ''},
                    {data: 'course.name',name:'course.name',defaultContent: ''},
                    {data: 'year',name:'year',defaultContent: ''},
                    {data: 'session.name',name:'session.name',defaultContent: ''},
                    {data: 'action',searchable: false},
                ]
            })
            $('#btnsearch').click(function(){
                $('.datatable').DataTable().draw(true);
            });

            $("body").on("click",".btn_view",function(){
                var lecture_video_id = $(this).attr('id');
                $('.modal-body').load('/admin/question-view',{lecture_video_id: lecture_video_id,_token: '{{csrf_token()}}'},function(){
                    $('#question').modal({show:true});
                });
            });

            $("body").on("click",".btn_log",function(){
                var lecture_video_id = $(this).attr('id');
                $('.modal-body').load('/admin/question-edit-log',{lecture_video_id : lecture_video_id, _token: '{{ csrf_token() }}'},function(){
                    $('#question_edit').modal({show:true});
                });
            });

            $("body").on( "change", "[name='institute_id']", function() {
                var institute_id = $(this).val();
                var view_name = 'topic_course_search';
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: '/admin/institute-change-in-topic',
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
                var view_name = 'topic_year_search';
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: '/admin/course-change-in-topic',
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
                var view_name = 'topic_session_search';
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: '/admin/year-change-in-topic',
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