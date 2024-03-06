@extends('admin.layouts.app')

@section('content')

    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <i class="fa fa-home"></i><a href="{{ url('/') }}">Home</a><i class="fa fa-angle-right"></i>
            </li>
            <li> Add Lecture Videos in {{ $program->name }}</li>
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
                        <i class="fa fa-globe"></i> Add Lecture Videos in {{ $program->name }}
                        <input type="hidden" name="program_id" value="{{ $program->id }}"/>
                        <input type="hidden" name="program_content_id" value="{{ $program_content->id }}"/>
                        @can('Topic')
                        <a href="{{url('admin/program')}}" class="btn btn-xs btn-primary">Topics</a>
                        @endcan
                        @can('Program')
                        <a href="{{url('admin/program')}}" class="btn btn-xs btn-primary">Programs</a>
                        @endcan
                        @can('Program Content')
                        <a href="{{url('admin/program-content/'.$program->id)}}" class="btn btn-xs btn-info">Program Contents</a>
                        @endcan
                        @can('Program Content')
                        <a href="{{url('admin/program-lecture-video-list/'.$program->id)}}" class="btn btn-xs btn-success">Program Lecture Videos</a>
                        @endcan
                    </div>
                </div>
                <div class="portlet-body">

                    <div class="row">
                        <div class="form-group col-md-3">
                            <h5>Topic <span class="text-danger"></span></h5>
                            <div class="controls">
                                @php  $topics->prepend('Select Topic', ''); @endphp
                                {!! Form::select('topic_id',$topics, $topic_content->topic->id ,['class'=>'form-control select2','required'=>'required','disabled'=>'disabled','id'=>'topic_id']) !!}<i></i>
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
                            <th>Topic</th>
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

            $(document).on('change', '[name="topic_content_id"]', function() {
        
                var operation = "";
                if(this.checked == true)
                {
                    operation = "insert";      
                }
                else if(this.checked == false)
                {
                    operation = "delete";
                }

                var topic_content_id = $(this).val();
                var program_id = $('[name="program_id"]').val();
                var program_content_id = $('[name="program_content_id"]').val();               

                $.ajax({
                    type: "POST",
                    url: '/admin/program-lecture-video-update',
                    dataType: 'HTML',
                    data: {topic_content_id : topic_content_id, program_id : program_id, program_content_id:program_content_id, operation : operation },
                    success: function( data ) { 
                        var data = JSON.parse(data);
                        $("#label_"+data['topic_content_id']).html(data['message']);
                                                            
                        if(data['status'] == "completed" || data['status'] == "data_already_exist" )
                        {
                            window.location.href = "/admin/program-lecture-video-list/"+data['program_id'];
                        }
                        
                    }
                });   
                
                
            });


            $("#table_1").on("mouseover", 'td' , function () {
                
                $(this).css('cursor','pointer');                        
                
            });

            $("#table_1").on("click", 'td' , function () {
                
                var mentor_id = $(this).closest('tr').find('td').first().html();
                
                if(!isNaN(mentor_id) && $(this).index() != ( $(this).closest('tr').children('td').length - 1 ) )
                {
                    $('#question_answer .modal-body').load('/admin/get-question-details',{mentor_id : mentor_id, _token: '{{ csrf_token() }}'},function(){
                        $('#question_answer').modal({show:true});
                    });
                }                                
                
            });

            var table = $('.datatable').DataTable({
                responsive: true,
                processing: true,
                serverSide: true,
                ajax: {
                    url: "/admin/program-lecture-video-edit-list",
                    type: 'GET',
                    data: function (d) {
                        d.program_id = $('[name="program_id"]').val();
                        d.topic_id = $('[name="topic_id"]').val();
                        d.program_content_id = $('[name="program_content_id"]').val();
                    }
                },
                "pageLength": 10,
                columns: [
                    {data: 'id',name:'d1.id'},
                    {data: 'name',name:'d1.name'},
                    {data: 'topic_name',name:'d3.name'},
                    {data: 'action',searchable: false},
                ]
            })
            $('#btnsearch').click(function(){
                $('.datatable').DataTable().draw(true);
            });

            $("body").on("click",".btn_view",function(){
                var mentor_id = $(this).attr('id');
                $('.modal-body').load('/admin/question-view',{mentor_id: mentor_id,_token: '{{csrf_token()}}'},function(){
                    $('#question').modal({show:true});
                });
            });

            $("body").on("click",".btn_log",function(){
                var mentor_id = $(this).attr('id');
                $('.modal-body').load('/admin/question-edit-log',{mentor_id : mentor_id, _token: '{{ csrf_token() }}'},function(){
                    $('#question_edit').modal({show:true});
                });
            });

        })
    </script>

@endsection