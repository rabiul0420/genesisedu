@extends('admin.layouts.app')

@section('content')

    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <i class="fa fa-home"></i><a href="{{ url('/') }}">Home</a><i class="fa fa-angle-right"></i>
            </li>
            <li> Add Discipline in {{ $module->name }}</li>
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
                        <i class="fa fa-globe"></i> Add Discipline in {{ $module->name }}
                        <input type="hidden" name="module_id" value="{{ $module->id }}"/>
                        @can('Module')
                        <a href="{{url('admin/module')}}" class="btn btn-xs btn-primary">Module</a>
                        @endcan
                        @can('Module Content')
                        <a href="{{url('admin/module-content/'.$module->id)}}" class="btn btn-xs btn-info">Module Contents</a>
                        @endcan
                        @can('Module Content')
                        <a href="{{url('admin/module-discipline-list/'.$module->id)}}" class="btn btn-xs btn-success">Module Discipline</a>
                        @endcan
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="row">
                        <div class="form-group col-md-2">
                            <h5>Institute <span class="text-danger"></span></h5>
                            <div class="controls">
                                @php  $institutes->prepend('Select Institute', ''); @endphp
                                {!! Form::select('institute_id',$institutes, $module->institute_id ,['class'=>'form-control select2','required'=>'required','id'=>'institute_id']) !!}<i></i>
                            </div>
                        </div>
                        <div class="course">
                            <div class="form-group col-md-2">
                                <h5>Course <span class="text-danger"></span></h5>
                                <div class="controls">
                                    @php  $courses->prepend('Select Course', ''); @endphp
                                    {!! Form::select('course_id',$courses, $module->course_id ,['class'=>'form-control select2','required'=>'required','id'=>'course_id']) !!}<i></i>
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
                            <th>Discipline</th>
                            <th>Institute</th>
                            <th>Course</th>
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

  <div class="modal fade" id="question" tabindex="-1" aria-labelledby="disciplinepleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="disciplinepleModalLabel">Question</h5>
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

            $(document).on('change', '[name="discipline_id"]', function() {
                
                var operation = "";
                if(this.checked == true)
                {
                    operation = "insert";      
                }
                else if(this.checked == false)
                {
                    operation = "delete";
                }

                var discipline_id = $(this).val();
                var module_id = $('[name="module_id"]').val();                

                $.ajax({
                    type: "POST",
                    url: '/admin/module-discipline-save',
                    dataType: 'HTML',
                    data: {discipline_id : discipline_id, module_id : module_id, operation : operation },
                    success: function( data ) { 
                        var data = JSON.parse(data);
                        $("#label_"+data['discipline_id']).html(data['message']);
                        
                        if(data['status'] == "insert_success")
                        {
                            $("#label_m_"+data['discipline_id']).removeClass("btn btn-info").addClass("btn btn-danger");
                            $("#label_m_"+data['discipline_id']).html("Delete From Topic");
                        }
                        if(data['status'] == "delete_success")
                        {
                            $("#label_m_"+data['discipline_id']).removeClass("btn btn-danger").addClass("btn btn-info");
                            $("#label_m_"+data['discipline_id']).html("Add to Topic");
                        }
                                                            
                        if(data['status'] == "completed" || data['status'] == "data_already_exist" )
                        {
                            window.location.href = "/admin/module-discipline-list/"+data['module_id'];
                        }
                        
                    }
                });   
                
                
            });


            $("#table_1").on("mouseover", 'td' , function () {
                
                $(this).css('cursor','pointer');                        
                
            });

            $("#table_1").on("click", 'td' , function () {
                
                var discipline_id = $(this).closest('tr').find('td').first().html();
                
                if(!isNaN(discipline_id) && $(this).index() != ( $(this).closest('tr').children('td').length - 1 ) )
                {
                    $('#question_answer .modal-body').load('/admin/get-question-details',{discipline_id : discipline_id, _token: '{{ csrf_token() }}'},function(){
                        $('#question_answer').modal({show:true});
                    });
                }                                
                
            });

            var table = $('.datatable').DataTable({
                responsive: true,
                processing: true,
                serverSide: true,
                ajax: {
                    url: "/admin/module-discipline-add-list",
                    type: 'GET',
                    data: function (d) {
                        d.module_id = $('[name="module_id"]').val();
                        d.institute_id = $('[name="institute_id"]').val();
                        d.course_id = $('[name="course_id"]').val();
                    }
                },
                "pageLength": 10,
                columns: [
                    {data: 'id',name:'d1.id'},
                    {data: 'name',name:'d1.name'},
                    {data: 'institute_name',name:'d3.name'},
                    {data: 'course_name',name:'d4.name'},
                    {data: 'action',searchable: false},
                ]
            })
            $('#btnsearch').click(function(){
                $('.datatable').DataTable().draw(true);
            });

            $("body").on("click",".btn_view",function(){
                var discipline_id = $(this).attr('id');
                $('.modal-body').load('/admin/question-view',{discipline_id: discipline_id,_token: '{{csrf_token()}}'},function(){
                    $('#question').modal({show:true});
                });
            });

            $("body").on("click",".btn_log",function(){
                var discipline_id = $(this).attr('id');
                $('.modal-body').load('/admin/question-edit-log',{discipline_id : discipline_id, _token: '{{ csrf_token() }}'},function(){
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
                    url: '/admin/institute-change-in-module-discipline',
                    dataType: 'HTML',
                    data: {institute_id : institute_id},
                    success: function( data ) {
                        $('.course').html('');
                        $('.course').html(data);
                    }
                });
            });

        })
    </script>

@endsection