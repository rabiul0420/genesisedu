@extends('admin.layouts.app')

@section('content')

    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <i class="fa fa-home"></i><a href="{{ url('/') }}">Home</a><i class="fa fa-angle-right"></i>
            </li>
            <li> Add Batchs in {{ $program->name }}</li>
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

    </style>

    <div class="row">
        <div class="col-md-12">
            <!-- BEGIN EXAMPLE TABLE PORTLET-->
            <div class="portlet">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-globe"></i> Add Batches in {{ $program->name }}
                        <input type="hidden" name="program_id" value="{{ $program->id }}"/>
                        @can('Topic')
                        <a href="{{url('admin/topics')}}" class="btn btn-xs btn-primary">Topics</a>
                        @endcan
                        @can('Program')
                        <a href="{{url('admin/program')}}" class="btn btn-xs btn-primary">Program</a>
                        @endcan
                        @can('Program Content')
                        <a href="{{url('admin/program-content/'.$program->id)}}" class="btn btn-xs btn-info">Program Contents</a>
                        @endcan
                        @can('Program Content')
                        <a href="{{url('admin/program-batch-list/'.$program->id)}}" class="btn btn-xs btn-success">Program Batches</a>
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
                            <th>Batch</th>
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

  <div class="modal fade" id="question" tabindex="-1" aria-labelledby="batchpleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="batchpleModalLabel">Question</h5>
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

            $(document).on('change', '[name="batch_id"]', function() {
                
                var operation = "";
                if(this.checked == true)
                {
                    operation = "insert";      
                }
                else if(this.checked == false)
                {
                    operation = "delete";
                }

                var batch_id = $(this).val();
                var program_id = $('[name="program_id"]').val();                

                $.ajax({
                    type: "POST",
                    url: '/admin/program-batch-save',
                    dataType: 'HTML',
                    data: {batch_id : batch_id, program_id : program_id, operation : operation },
                    success: function( data ) { 
                        var data = JSON.parse(data);
                        $("#label_"+data['batch_id']).html(data['message']);
                        
                        if(data['status'] == "insert_success")
                        {
                            $("#label_m_"+data['batch_id']).removeClass("btn btn-info").addClass("btn btn-danger");
                            $("#label_m_"+data['batch_id']).html("Delete From Topic");
                        }
                        if(data['status'] == "delete_success")
                        {
                            $("#label_m_"+data['batch_id']).removeClass("btn btn-danger").addClass("btn btn-info");
                            $("#label_m_"+data['batch_id']).html("Add to Topic");
                        }
                                                            
                        if(data['status'] == "completed" || data['status'] == "data_already_exist" )
                        {
                            window.location.href = "/admin/program-batch-list/"+data['program_id'];
                        }
                        
                    }
                });   
                
                
            });


            $("#table_1").on("mouseover", 'td' , function () {
                
                $(this).css('cursor','pointer');                        
                
            });

            $("#table_1").on("click", 'td' , function () {
                
                var batch_id = $(this).closest('tr').find('td').first().html();
                
                if(!isNaN(batch_id) && $(this).index() != ( $(this).closest('tr').children('td').length - 1 ) )
                {
                    $('#question_answer .modal-body').load('/admin/get-question-details',{batch_id : batch_id, _token: '{{ csrf_token() }}'},function(){
                        $('#question_answer').modal({show:true});
                    });
                }                                
                
            });

            var table = $('.datatable').DataTable({
                responsive: true,
                processing: true,
                serverSide: true,
                ajax: {
                    url: "/admin/program-batch-add-list",
                    type: 'GET',
                    data: function (d) {
                        d.program_id = $('[name="program_id"]').val();
                        d.institute_id = $('[name="institute_id"]').val();
                        d.course_id = $('[name="course_id"]').val();
                        d.year = $('[name="year"]').val();
                        d.session_id = $('[name="session_id"]').val();
                    }
                },
                "pageLength": 10,
                columns: [
                    {data: 'id',name:'d1.id'},
                    {data: 'name',name:'d1.name'},
                    {data: 'institute_name',name:'d3.name'},
                    {data: 'course_name',name:'d4.name'},
                    {data: 'year',name:'d1.year'},
                    {data: 'session_name',name:'d5.name'},
                    {data: 'action',searchable: false},
                ]
            })
            $('#btnsearch').click(function(){
                $('.datatable').DataTable().draw(true);
            });

            $("body").on("click",".btn_view",function(){
                var batch_id = $(this).attr('id');
                $('.modal-body').load('/admin/question-view',{batch_id: batch_id,_token: '{{csrf_token()}}'},function(){
                    $('#question').modal({show:true});
                });
            });

            $("body").on("click",".btn_log",function(){
                var batch_id = $(this).attr('id');
                $('.modal-body').load('/admin/question-edit-log',{batch_id : batch_id, _token: '{{ csrf_token() }}'},function(){
                    $('#question_edit').modal({show:true});
                });
            });

            $("body").on( "change", "[name='institute_id']", function() {
                var institute_id = $(this).val();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: '/admin/institute-change-in-program-batch',
                    dataType: 'HTML',
                    data: {institute_id : institute_id},
                    success: function( data ) {
                        $('.course').html('');
                        $('.year').html('');
                        $('.session').html('');
                        $('.course').html(data);
                    }
                });
            });

            $("body").on( "change", "[name='course_id']", function() {
                var course_id = $("[name='course_id']").val();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: '/admin/course-change-in-program-batch',
                    dataType: 'HTML',
                    data: {course_id: course_id},
                    success: function( data ) {
                        $('.year').html('');
                        $('.session').html('');
                        $('.year').html(data);
                    }
                });
            });

            $("body").on( "change", "[name='year']", function() {
                var course_id = $("[name='course_id']").val();
                var year = $("[name='year']").val();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: '/admin/year-change-in-program-batch',
                    dataType: 'HTML',
                    data: {course_id: course_id,year:year},
                    success: function( data ) {
                        $('.session').html('');
                        $('.session').html(data);
                    }
                });
            });

        })
    </script>

@endsection