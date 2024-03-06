@extends('admin.layouts.app')

@section('content')
    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <i class="fa fa-home"></i><a href="{{ url('/') }}">Home</a><i class="fa fa-angle-right"></i>
            </li>
            <li>Question List</li>
        </ul>
    </div>

    @if (Session::has('message'))
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
                        <i class="fa fa-globe"></i>SBA Question List
                        <a href="{{ action('Admin\SbaController@create') }}"> <i class="fa fa-plus"></i> </a>
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="row sc_search">
                        <div class="form-group col-md-3">
                            <h5>Subject <span class="text-danger"></span></h5>
                            <div class="controls">
                                @php  $subjects->prepend('Select Subject', ''); @endphp
                                {!! Form::select('subject_id', $subjects, '', ['class' => 'form-control select2', 'required' => 'required', 'id' => 'subject_id']) !!}<i></i>
                            </div>
                        </div>
                        <div class="form-group col-md-3">
                            <h5>Chapter <span class="text-danger"></span></h5>
                            <div class="controls">
                                @php  $chapters->prepend('Select Chapter', ''); @endphp
                                {!! Form::select('chapter_id', $chapters, '', ['class' => 'form-control select2', 'required' => 'required', 'id' => 'chapter_id']) !!}<i></i>
                            </div>
                        </div>
                        <div class="form-group col-md-3">
                            <h5>Topic <span class="text-danger"></span></h5>
                            <div class="controls">
                                @php  $topics->prepend('Select Topic', ''); @endphp
                                {!! Form::select('topic_id', $topics, '', ['class' => 'form-control select2', 'required' => 'required', 'id' => 'topic_id']) !!}<i></i>
                            </div>
                        </div>
                        <div class="form-group col-md-3">
                            <h5>Question Source <span class="text-danger"></span></h5>
                            <div class="controls">
                                @php  $references->prepend('Select Source', ''); @endphp
                                {!! Form::select('reference_id', $references, '', ['class' => 'form-control select2', 'required' => 'required', 'id' => 'reference_id']) !!}<i></i>
                            </div>
                        </div>
                    </div>
                    <div class="row sc_search2">

                        <div class="source_institute">
                            <div class="form-group col-md-3">
                                <h5>Source Institute <span class="text-danger"></span></h5>
                                <div class="controls">
                                    @php  $source_institutes->prepend('Select Institute', ''); @endphp
                                    {!! Form::select('source_institute_id', $source_institutes, '', ['class' => 'form-control select2', 'required' => 'required', 'id' => 'source_institute_id']) !!}<i></i>
                                </div>
                            </div>
                        </div>

                        <div class="source_course">
                            <div class="form-group col-md-3">
                                <h5>Source Course <span class="text-danger"></span></h5>
                                <div class="controls">
                                    @php  $source_courses->prepend('Select Course', ''); @endphp
                                    {!! Form::select('source_course_id', $source_courses, '', ['class' => 'form-control select2', 'id' => 'source_course_id']) !!}<i></i>
                                </div>
                            </div>
                        </div>

                        <div class="source_faculty">
                            <div class="form-group col-md-3">
                                <h5>Source Faculty <span class="text-danger"></span></h5>
                                <div class="controls">
                                    @php  $source_faculties->prepend('Select Faculty', ''); @endphp
                                    {!! Form::select('source_faculty_id', $source_faculties, '', ['class' => 'form-control select2', 'id' => 'source_faculty_id']) !!}<i></i>
                                </div>
                            </div>
                        </div>

                        <div class="source_subject">
                            <div class="form-group col-md-3">
                                <h5>Source Subject <span class="text-danger"></span></h5>
                                <div class="controls">
                                    @php  $source_subjects->prepend('Select Subject', ''); @endphp
                                    {!! Form::select('source_subject_id', $source_subjects, '', ['class' => 'form-control select2', 'id' => 'source_subject_id']) !!}<i></i>
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
                                    {!! Form::select('source_session_id', $source_sessions, '', ['class' => 'form-control select2', 'id' => 'source_session_id']) !!}<i></i>
                                </div>
                            </div>
                        </div>

                        <div class="year">
                            <div class="form-group col-md-3">
                                <h5>Year <span class="text-danger"></span></h5>
                                <div class="controls">
                                    {!! Form::select('year', $years, '', ['class' => 'form-control select2', 'required' => 'required', 'id' => 'year']) !!}<i></i>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="text-center" style="margin-left: 15px;">
                        <button type="text" id="btnsearch" class="btn btn-info">Search</button>
                        @foreach (json_decode($print_allows->value, true) as $print)
                            @if (Auth::user()->id == $print)
                                <button type="text" target="_blank" id="print" class="btn btn-info">Print</button>
                            @endif
                        @endforeach
                    </div>
                    <table class="table table-striped table-bordered table-hover datatable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Question</th>
                                <th>Question Sources</th>
                                <th>Topic</th>
                                <th>Chapter</th>
                                <th>Subject</th>
                                <th width="100">Actions</th>
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

    <div class="modal fade" id="question_edit" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Edit History</h5>
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
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            var table = $('.datatable').DataTable({
                responsive: true,
                processing: true,
                serverSide: true,
                ajax: {
                    url: "/admin/sba-list",
                    type: 'GET',
                    data: function(d) {
                        d.subject_id = $('#subject_id').val();
                        d.chapter_id = $('#chapter_id').val();
                        d.topic_id = $('#topic_id').val();
                        d.references = $('#reference_id').val();
                        d.source_institute_id = $('#source_institute_id').val();
                        d.source_course_id = $('#source_course_id').val();
                        d.source_faculty_id = $('#source_faculty_id').val();
                        d.source_subject_id = $('#source_subject_id').val();
                        d.source_session_id = $('#source_session_id').val();
                        d.year = $('#year').val();
                    }
                },
                "pageLength": 25,
                columns: [{
                        data: 'id',
                        name: 'd1.id'
                    },
                    {
                        data: 'question_title',
                        name: 'd1.question_title'
                    },
                    {
                        data: 'references'
                    },
                    {
                        data: 'topic_name',
                        name: 'd2.topic_name'
                    },
                    {
                        data: 'chapter_name',
                        name: 'd3.chapter_name'
                    },
                    {
                        data: 'subject_name',
                        name: 'd4.subject_name'
                    },
                    {
                        data: 'action',
                        searchable: false
                    },
                ]
            })
            $('#btnsearch').click(function() {
                $('.datatable').DataTable().draw(true);
            });

            $(".select2").select2({
                allowClear: true,
                tags: true,
                tokenSeparators: [',']
            });

            $("body").on("click", ".btn_view", function() {
                var question_id = $(this).attr('id');
                $('.modal-body').load('/admin/sba-question-view', {
                    question_id: question_id,
                    _token: '{{ csrf_token() }}'
                }, function() {
                    $('#question').modal({
                        show: true
                    });
                });
            });

            $("body").on("click", ".btn_log", function() {
                var question_id = $(this).attr('id');
                $('.modal-body').load('/admin/sba-question-edit-log', {
                    question_id: question_id,
                    _token: '{{ csrf_token() }}'
                }, function() {
                    $('#question_edit').modal({
                        show: true
                    });
                });
            });

            $("#print").click(function() {

                let chapterID = $('[name="chapter_id"]').val();

                let topicID = $('[name="topic_id"]').val();

                let referenceID = $('[name="reference_id"]').val();

                let params = `?chapter=${chapterID}&topic=${topicID}&question_type=SBA&type=2`;

                let reference_params = `?reference=${referenceID}&question_type=SBA&type=2`

                if (chapterID && topicID) {
                    window.location.href = "/admin/quetion-print" + params;
                } else {
                    if (referenceID) {
                        window.location.href = "/admin/quetion-print" + reference_params;
                    } else {
                        alert('Please select Chapter and Topic Or Question Source');
                    }
                }
            });
        })
    </script>
@endsection
