@extends('admin.layouts.app')

@section('content')

    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <i class="fa fa-home"></i><a href="{{ url('/') }}">Home</a><i class="fa fa-angle-right"></i>
            </li>
            <li> Lecture Sheets in {{ $topic->name }}</li>
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
        TOPIC LECTURE SHEET LIST
    </div>

    <div class="row">
        <div class="col-md-12">
            <!-- BEGIN EXAMPLE TABLE PORTLET-->
            <div class="portlet">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-globe"></i> Lecture Sheets in {{ $topic->name }}
                        <input type="hidden" name="topic_id" value="{{ $topic->id }}"/>
                        @can('Topic')
                        <a href="{{url('admin/topics')}}" class="btn btn-xs btn-primary">Topics</a>
                        @endcan
                        @can('Topic Content')
                        <a href="{{url('admin/topics-contents/'.$topic->id)}}" class="btn btn-xs btn-info">Topic Contents</a>
                        @endcan
                        @can('Topic Content')
                        <a href="{{url('admin/topic-lecture-sheet-add/'.$topic->id)}}" class="btn btn-xs btn-success">Add Topic Lecture Sheet</a>
                        @endcan
                    </div>
                </div>
                <div class="portlet-body">
                    <table id="table_1" class="table table-striped table-bordered table-hover datatable">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
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
                    url: "/admin/topic-lecture-sheet-ajax-list",
                    type: 'GET',
                    data: function (d) {
                        d.topic_id = $('[name="topic_id"]').val();
                    }
                },
                "pageLength": 10,
                columns: [
                    {data: 'topic_content_id',name:'topic_content_id'},
                    {data: 'name',name:'d1.name'},
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