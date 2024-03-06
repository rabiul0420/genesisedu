@extends('admin.layouts.app')

@section('content')

    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <i class="fa fa-home"></i><a href="{{ url('/') }}">Home</a><i class="fa fa-angle-right"></i>
            </li>
            <li> Add Media Types in {{ $module->name }}</li>
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
                        <i class="fa fa-globe"></i> Add Media Types in {{ $module->name }}
                        <input type="hidden" name="module_id" value="{{ $module->id }}"/>
                        <input type="hidden" name="module_content_id" value="{{ $module_content->id }}"/>
                        @can('Module')
                        <a href="{{url('admin/module')}}" class="btn btn-xs btn-primary">Module</a>
                        @endcan
                        @can('Module Content')
                        <a href="{{url('admin/module-content/'.$module->id)}}" class="btn btn-xs btn-info">Module Contents</a>
                        @endcan
                        @can('Module Content')
                        <a href="{{url('admin/module-media-type-list/'.$module->id)}}" class="btn btn-xs btn-success">Module Media Types</a>
                        @endcan
                        
                    </div>
                </div>
                <div class="portlet-body">

                    <div class="row">
                                            
                    </div>
                    <div class="row">
                        
                    </div>
                    <table id="table_1" class="table table-striped table-bordered table-hover datatable">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Media Type</th>
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

            $(document).on('change', '[name="media_type_id"]', function() {
                
                var operation = "";
                if(this.checked == true)
                {
                    operation = "insert";      
                }
                else if(this.checked == false)
                {
                    operation = "delete";
                }

                var media_type_id = $(this).val();
                var module_id = $('[name="module_id"]').val();
                var module_content_id = $('[name="module_content_id"]').val();               

                $.ajax({
                    type: "POST",
                    url: '/admin/module-media-type-update',
                    dataType: 'HTML',
                    data: {media_type_id : media_type_id, module_id : module_id, module_content_id:module_content_id, operation : operation },
                    success: function( data ) { 
                        var data = JSON.parse(data);
                        $("#label_"+data['media_type_id']).html(data['message']);
                                                            
                        if(data['status'] == "completed" || data['status'] == "data_already_exist" )
                        {
                            window.location.href = "/admin/module-media-type-list/"+data['module_id'];
                        }
                        
                    }
                });   
                
                
            });

            $("#table_1").on("mouseover", 'td' , function () {
                
                $(this).css('cursor','pointer');                        
                
            });

            $("#table_1").on("click", 'td' , function () {
                
                var exam_id = $(this).closest('tr').find('td').first().html();
                
                if(!isNaN(exam_id) && $(this).index() != ( $(this).closest('tr').children('td').length - 1 ) )
                {
                    $('#question_answer .modal-body').load('/admin/get-question-details',{exam_id : exam_id, _token: '{{ csrf_token() }}'},function(){
                        $('#question_answer').modal({show:true});
                    });
                }                                
                
            });

            var table = $('.datatable').DataTable({
                responsive: true,
                processing: true,
                serverSide: true,
                ajax: {
                    url: "/admin/module-media-type-edit-list",
                    type: 'GET',
                    data: function (d) {
                        d.module_id = $('[name="module_id"]').val();
                    }
                },
                "pageLength": 10,
                columns: [
                    {data: 'id',name:'d1.id'},
                    {data: 'name',name:'d1.name'},
                    {data: 'action',searchable: false},
                ]
            })
            $('#btnsearch').click(function(){
                $('.datatable').DataTable().draw(true);
            });

            $("body").on("click",".btn_view",function(){
                var exam_id = $(this).attr('id');
                $('.modal-body').load('/admin/question-view',{exam_id: exam_id,_token: '{{csrf_token()}}'},function(){
                    $('#question').modal({show:true});
                });
            });

            $("body").on("click",".btn_log",function(){
                var exam_id = $(this).attr('id');
                $('.modal-body').load('/admin/question-edit-log',{exam_id : exam_id, _token: '{{ csrf_token() }}'},function(){
                    $('#question_edit').modal({show:true});
                });
            });

        })
    </script>

@endsection