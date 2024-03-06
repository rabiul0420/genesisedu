@extends('admin.layouts.app')

@section('content')

    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <i class="fa fa-home"></i><a href="{{ url('/') }}">Home</a><i class="fa fa-angle-right"></i>
            </li>
            <li> Add {{ $question_type_name }} in {{ $exam->name }}</li>
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
                        <i class="fa fa-globe"></i> Add {{ $question_type_name }} in {{ $exam->name }}
                        <input type="hidden" name="exam_id" value="{{ $exam->id }}"/>
                        <input type="hidden" name="exam_question_id" value="{{ $exam_question->id }}"/>
                        <input type="hidden" name="question_type" value="{{ $question_type }}"/>
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="row sc_search">
                        <div class="form-group col-md-3">
                            <h5>Subject <span class="text-danger"></span></h5>
                            <div class="controls">
                                @php  $subjects->prepend('Select Subject', ''); @endphp
                                {!! Form::select('subject_id',$subjects, '' ,['class'=>'form-control select2','required'=>'required','id'=>'subject_id']) !!}<i></i>
                            </div>
                        </div>
                        <div class="chapters">
                            <div class="form-group col-md-3">
                                <h5>Chapter <span class="text-danger"></span></h5>
                                <div class="controls">
                                    @php  $chapters->prepend('Select Chapter', ''); @endphp
                                    {!! Form::select('chapter_id',$chapters, '' ,['class'=>'form-control select2','required'=>'required','id'=>'chapter_id']) !!}<i></i>
                                </div>
                            </div>
                        </div>
                        <div class="topics">
                            <div class="form-group col-md-3">
                                <h5>Topic <span class="text-danger"></span></h5>
                                <div class="controls">
                                    @php  $topics->prepend('Select Topic', ''); @endphp
                                    {!! Form::select('topic_id',$topics, '' ,['class'=>'form-control select2','required'=>'required','id'=>'topic_id']) !!}<i></i>
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-md-3">
                            <h5>Question Source <span class="text-danger"></span></h5>
                            <div class="controls">
                                @php  $references->prepend('Select Source', ''); @endphp
                                {!! Form::select('reference_id',$references, '' ,['class'=>'form-control select2','required'=>'required','id'=>'reference_id']) !!}<i></i>
                            </div>
                        </div>
                    </div>
                    <div class="row sc_search2">

                        <div class="source_institute">
                            <div class="form-group col-md-3">
                                <h5>Source Institute <span class="text-danger"></span></h5>
                                <div class="controls">
                                    {{-- @php  $source_institutes->prepend('Select Institute', ''); @endphp --}}
                                    {!! Form::select('source_institute_id',$source_institutes, '' ,['class'=>'form-control select2','required'=>'required','id'=>'source_institute_id','multiple']) !!}<i></i>
                                </div>
                            </div>
                        </div>

                        <div class="source_course">
                            <div class="form-group col-md-3">
                                <h5>Source Course <span class="text-danger"></span></h5>
                                <div class="controls">
                                    @php  $source_courses->prepend('Select Course', ''); @endphp
                                    {!! Form::select('source_course_id',$source_courses, '' ,['class'=>'form-control','id'=>'source_course_id']) !!}<i></i>
                                </div>
                            </div>
                        </div>

                        <div class="source_faculty">
                            <div class="form-group col-md-3">
                                <h5>Source Faculty <span class="text-danger"></span></h5>
                                <div class="controls">
                                    @php  $source_faculties->prepend('Select Faculty', ''); @endphp
                                    {!! Form::select('source_faculty_id',$source_faculties, '' ,['class'=>'form-control','id'=>'source_faculty_id']) !!}<i></i>
                                </div>
                            </div>
                        </div>

                        <div class="source_subject">
                            <div class="form-group col-md-3">
                                <h5>Source Subject <span class="text-danger"></span></h5>
                                <div class="controls">
                                    @php  $source_subjects->prepend('Select Subject', ''); @endphp
                                    {!! Form::select('source_subject_id',$source_subjects, '' ,['class'=>'form-control','id'=>'source_subject_id']) !!}<i></i>
                                </div>
                            </div>
                        </div>


                    </div>
                    <div class="row sc_search3">

                        <div class="source_session">
                            <div class="form-group col-md-3">
                                <h5>Source Session <span class="text-danger"></span></h5>
                                <div class="controls">
                                    @php  $source_sessions->prepend('Select Session', ''); @endphp
                                    {!! Form::select('source_session_id',$source_sessions, '' ,['class'=>'form-control','id'=>'source_session_id']) !!}<i></i>
                                </div>
                            </div>
                        </div>

                        <div class="year">
                            <div class="form-group col-md-3">
                                <h5>Year <span class="text-danger"></span></h5>
                                <div class="controls">
                                    {!! Form::select('year',$years, '' ,['class'=>'form-control','required'=>'required','id'=>'year']) !!}<i></i>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="text-center" style="margin-left: 15px;">
                        <button type="text" id="btnsearch" class="btn btn-info">Search</button>
                    </div>
                    <table id="table_1" class="table table-striped table-bordered table-hover datatable">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Question</th>
                            <th>Question Sources</th>
                            <th>Topic</th>
                            <th>Chapter</th>
                            <th>Subject</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>

                        
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

<!-- Button trigger modal -->

  
  <!-- Modal -->
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

  <!-- Button trigger modal -->

  <style>
      .question_edit{
    display: block !important; /* I added this to see the modal, you don't need this */
}

/* Important part */
.question_edit_dialog{
    overflow-y: initial !important
}
.question_edit_body{
    height: 40vh;
    overflow-y: auto;
}
  </style>

  <!-- Modal -->
  <div class="modal fade" id="question_edit" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog question_edit_dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Edit History</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body question_edit_body">
          ...
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal -->
  <div class="modal fade" id="question_answer" tabindex="-1" aria-labelledby="question_answer_header" aria-hidden="true">
    <div class="modal-dialog question_answer_dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="question_answer_header">Question Stamps</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body question_answer_body">
          ...
        </div>
        <!-- <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div> -->
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

            $(document).on('change', '[name="question_id"]', function() {
        
                var operation = "";
                if(this.checked == true) {
                    operation = "insert";      
                }
                else if(this.checked == false)
                {
                    operation = "delete";
                }
                var question_id = $(this).val();
                var question_type = $('[name="question_type"]').val();
                var exam_id = $('[name="exam_id"]').val();
                var exam_question_id = $('[name="exam_question_id"]').val();
                if(exam_question_id)
                {

                    $.ajax({
                        type: "POST",
                        url: '/admin/update-exam-question',
                        dataType: 'HTML',
                        data: {question_id : question_id, exam_question_id : exam_question_id, question_type : question_type, operation : operation },
                        success: function( data ) { 
                                var data = JSON.parse(data);
                                $("#label_"+data['question_id']).html(data['message']);
                                if(data['question_add_status'] == "completed")
                                {
                                    window.location.href = "/admin/exam-questions/"+data['exam_id'];
                                }
                        }
                    });

                }
                
                
            });


            $("#table_1").on("mouseover", 'td' , function () {
                
                $(this).css('cursor','pointer');                        
                
            });

            $("#table_1").on("click", 'td' , function () {
                
                var question_id = $(this).closest('tr').find('td').first().html();
                
                if(!isNaN(question_id) && $(this).index() != ( $(this).closest('tr').children('td').length - 1 ) )
                {
                    $('#question_answer .modal-body').load('/admin/get-question-details',{question_id : question_id, _token: '{{ csrf_token() }}'},function(){
                        $('#question_answer').modal({show:true});
                    });
                }                                
                
            });

            // $('.doctor2').select2({
            //     minimumInputLength: 3,
            //     placeholder: "Please type doctor's name or bmdc no",
            //     escapeMarkup: function (markup) { return markup; },
            //     language: {
            //         noResults: function () {
            //             return "No Doctors found, for add new doctor please <a target='_blank' href='{{ url('admin/doctors/create') }}'>Click here</a>";
            //         }
            //     },
            //     ajax: {
            //         url: '/admin/search-chapter-list',
            //         dataType: 'json',
            //         type: "GET",
            //         quietMillis: 50,
            //         data: function (term) {
            //             return {
            //                 term: term
            //             };
            //         },
            //         processResults: function (data) {
            //             return {
            //                 results: $.map(data, function (item) {
            //                     console.log(item.id);
            //                     $('.select2-selection__rendered').attr('data-id' , item.id);
            //                     return { id:item.id , text: item.name_bmdc };
            //                 })
            //             };
            //         }
            //     }
            // });

            
            $("body").on( "change", "[name='subject_id']", function() {
                var subject_id = $(this).val();
                $.ajax({
                    type: "POST",
                    url: '/admin/search-chapter-list',
                    dataType: 'HTML',
                    data: {subject_id : subject_id },
                    success: function( data ) {
                         $('.chapters').html(data);
                         $('#chapter_id').select2();
                    }
                });
            })
            $("body").on( "change", "[name='chapter_id']", function() {
                var chapter_id = $(this).val();
                var subject_id = $('#subject_id').val();
                $.ajax({
                    type: "POST",
                    url: '/admin/search-topic-list',
                    dataType: 'HTML',
                    data: {chapter_id : chapter_id, subject_id : subject_id },
                    success: function( data ) {
                         $('.topics').html(data); 
                         $('#topic_id').select2();
                    }
                });
            })

            $("body").on("change","[id='source_institute_id']",function(){
                var source_institute_id = $(this).val();
                console.log(source_institute_id)
                $.ajax({
                    type: "POST",
                    url: '/admin/search-source-course',
                    dataType: 'HTML',
                    data: {source_institute_id : source_institute_id },
                    success: function( data ) {
                         $('.source_course').html(data); 
                         $('#source_course_id').select2();
                    }
                });

            });

            $("body").on("change","[id='source_course_id']",function(){
                var source_course_id = $(this).val();
                $.ajax({
                    type: "POST",
                    url: '/admin/search-source-faculty',
                    dataType: 'HTML',
                    data: {source_course_id : source_course_id },
                    success: function( data ) {
                         $('.source_faculty').html(data); 
                         $('#source_faculty_id').select2();
                    }
                });

            });

            $("body").on("change","[id='source_course_id']",function(){
                var source_course_id = $(this).val();
                $.ajax({
                    type: "POST",
                    url: '/admin/search-source-subject',
                    dataType: 'HTML',
                    data: {source_course_id : source_course_id },
                    success: function( data ) {
                         $('.source_subject').html(data); 
                         $('#source_subject_id').select2();
                    }
                });

            });

            var table = $('.datatable').DataTable({
                responsive: true,
                processing: true,
                serverSide: true,
                ajax: {
                    url: "/admin/question-list",
                    type: 'GET',
                    data: function (d) {
                        d.subject_id = $('#subject_id').val();
                        d.chapter_id = $('#chapter_id').val();
                        d.topic_id = $('#topic_id').val();
                        d.reference_id = $('#reference_id').val();
                        d.source_institute_id = $('#source_institute_id').val();
                        d.source_course_id = $('#source_course_id').val();
                        d.source_faculty_id = $('#source_faculty_id').val();
                        d.source_subject_id = $('#source_subject_id').val();
                        d.source_session_id = $('#source_session_id').val();
                        d.year = $('#year').val();
                        d.question_type = $('[name="question_type"]').val();
                        d.exam_id = $('[name="exam_id"]').val();
                    }
                },
                "pageLength": 10,
                columns: [
                    {data: 'id',name:'d1.id'},
                    {data: 'question_title',name:'d1.question_title'},
                    {data: 'references'},
                    {data: 'topic_name',name:'d2.topic_name'},
                    {data: 'chapter_name',name:'d3.chapter_name'},
                    {data: 'subject_name',name:'d4.subject_name'},
                    {data: 'action',searchable: false},
                ]
            })
            $('#btnsearch').click(function(){
                $('.datatable').DataTable().draw(true);
            });

            $("body").on("click",".btn_view",function(){
                var question_id = $(this).attr('id');
                $('.modal-body').load('/admin/question-view',{question_id: question_id,_token: '{{csrf_token()}}'},function(){
                    $('#question').modal({show:true});
                });
            });

            $("body").on("click",".btn_log",function(){
                var question_id = $(this).attr('id');
                $('.modal-body').load('/admin/question-edit-log',{question_id : question_id, _token: '{{ csrf_token() }}'},function(){
                    $('#question_edit').modal({show:true});
                });
            });

        })
    </script>

@endsection