@extends('admin.layouts.app')

@section('content')
    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <i class="fa fa-home"></i><a href="{{ url('/') }}">Home</a><i class="fa fa-angle-right"></i>
            </li>
            <li>{{ $title }}</li>
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
                        <i class="fa fa-globe"></i>{{ $title }}
                        <a id="exam_id" data-id="{{ $exam->id }}"
                            href="{{ url('admin/upload-result/' . $exam->id) }}">
                            <i class="fa fa-plus"></i> </a>
                        @can('Exam')
                            <a href="{{ url('admin/exam') }}" class="btn btn-xs btn-info">Exams</a>
                        @endcan
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="row sc_search">
                        <div class="form-group col-md-2">
                            <h5>Year <span class="text-danger"></span></h5>
                            <div class="controls">
                                {!! Form::select('year', $years, $exam->year, ['class' => 'form-control year', 'id' => 'year']) !!}<i></i>
                            </div>
                        </div>

                        <div class="course">
                            <div class="form-group col-md-2">
                                <h5>Course <span class="text-danger"></span></h5>
                                <div class="controls">
                                    @php  $courses->prepend('Select Course', ''); @endphp
                                    {!! Form::select('course_id', $courses, $exam->course_id, ['class' => 'form-control course_id', 'id' => 'course_id']) !!}<i></i>
                                </div>
                            </div>
                        </div>

                        <div class="session">
                            <div class="form-group col-md-2">
                                <h5>Session <span class="text-danger"></span></h5>
                                <div class="controls">
                                    @php  $sessions->prepend('Select Session', ''); @endphp
                                    {!! Form::select('session_id', $sessions, $exam->session_id, ['class' => 'form-control session_id', 'id' => 'session_id']) !!}<i></i>
                                </div>
                            </div>
                        </div>

                        <div class="batch">
                            <div class="form-group col-md-2">
                                <h5>Batch <span class="text-danger"></span></h5>
                                <div class="controls">
                                    @php  $batches->prepend('Select Batch', ''); @endphp
                                    {!! Form::select('batch_id', $batches, '', ['class' => 'form-control batch2', 'id' => 'batch_id']) !!}<i></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="text-center" style="margin-left: 15px;">
                        <button type="text" id="btnFiterSubmitSearch" class="btn btn-info">Search</button>
                        <button type="text" id="excel-download" class="btn btn-info">Excel Download</button>
                        <a type="button" target="_blank" id="print" class="btn btn-info">Print</a>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <p><b>Exam Name:</b> {{ $exam->name }}</p>
                            <p><b>Course:</b> {{ $exam->course->name }}</p>
                            <p><b>Year:</b> {{ $exam->year }}</p>
                        </div>
                        <div class="col-md-4">
                            <p><b>Session:</b> {{ $exam->sessions->name }}</p>
                            <p><b>Full Mark:</b> {{ $exam->question_type->full_mark }}</p>
                            <p><b>Highest Mark:</b> {{ $exam->highest_mark }}</p>
                        </div>
                    </div>

                    <table class="table table-striped table-bordered table-hover datatable">
                        <thead>
                            <tr>
                                <th>Reg. No</th>
                                <th>Doctor Name</th>
                                <th>Batch</th>
                                <th>Discipline</th>
                                <th>Obtained Mark</th>
                                <th>Wrong Answer</th>
                                <th>Discipline Position</th>
                                <th>Batch Position</th>
                                @if ($exam->institute_id == 6)
                                    <th>Candidate Position</th>
                                @endif
                                <th>Overall Position</th>

                            </tr>
                        </thead>

                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="{{ asset('assets/scripts/jquery-ui.min.js') }}"></script>
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/css/select2.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/js/select2.js" type="text/javascript"></script>

    <script type="text/javascript">
        var institute_id = '{{ $exam->institute_id }}';

        $(document).ready(function() {

            $('#batch_id').select2();
            // $("body").on( "change", "[name='year']", function() {
            //     var year = $(this).val();
            //     $.ajax({
            //         headers: {
            //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            //         },
            //         type: "POST",
            //         url: '/admin/exam-search-batches',
            //         dataType: 'HTML',
            //         data: {year : year },
            //         success: function( data ) {
            //              $('.batch').html(data); 
            //         }
            //     });
            // });

            $("body").on("change", "[name='course_id'],[name='year']", function() {
                var course_id = $('#course_id').val();
                var year = $('.year').val();

                $.ajax({
                    type: "GET",
                    url: '/admin/course-session-search',
                    dataType: 'HTML',
                    data: {
                        course_id: course_id,
                        year: year
                    },
                    success: function(data) {
                        $('.session').html(data);
                        $('#session_id').select2();
                    }
                });

            })

            $("body").on("change", "[name='session_id']", function() {
                var session_id = $(this).val();
                var year = $('.year').val();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: '/admin/view-result-search-batch',
                    dataType: 'HTML',
                    data: {
                        session_id: session_id,
                        year: year
                    },
                    success: function(data) {
                        $('.batch').html(data);
                        $('#batch_id').select2();
                    }
                });
            })


            const columns = [{
                    data: 'reg_no',
                    name: 'dc.reg_no'
                },
                {
                    data: 'doctor_name',
                    name: 'd.name'
                },
                {
                    data: 'batch_name',
                    name: 'b.name'
                },
                {
                    data: 'discipline_name',
                    name: 's.name'
                },
                {
                    data: 'obtained_mark',
                    name: 'r.obtained_mark'
                },
                {
                    data: 'wrong_answer',
                    name: 'r.wrong_answers'
                },
                {
                    data: 'disciplain_position',
                    searchable: false
                },
                {
                    data: 'batch_position',
                    searchable: false
                },
            ];

            if (institute_id == '6') {
                columns.push({
                    data: 'candidate_position',
                    searchable: false
                });
            }
            columns.push({
                data: 'overall_position',
                searchable: false
            });


            $('.datatable').DataTable({
                responsive: true,
                processing: true,
                serverSide: true,
                ajax: {
                    url: "/admin/exam-batch-list",
                    type: 'GET',
                    data: function(d) {
                        d.year = $('#year').val();
                        d.session_id = $('#session_id').val();
                        d.batch_id = $('#batch_id').val();
                        d.exam_id = $('#exam_id').data('id');
                    }
                },
                "pageLength": 25,
                columns
            });

            $('#btnFiterSubmitSearch').click(function() {
                $('.datatable').DataTable().draw(true);
            });

            $('#excel-download').click(function() {
                var year = $('[name="year"]').val();
                var course = $('[name="course_id"]').val();
                var session = $('[name="session_id"]').val();
                var batch = $('[name="batch_id"]').val();
                var exam_id = $('#exam_id').data('id');

                var params = JSON.stringify({
                    'year': year,
                    'course_id': course,
                    'session_id': session,
                    'batch_id': batch,
                    'exam_id': exam_id,
                });

                window.location.href = "/admin/result-excel/" + params;

                // if(year && session && batch){
                //     window.location.href = `/admin/result-excel/${year}/${course}/${session}/${batch}/${exam_id}`;
                // }else {
                //      alert('Please select Year, Session and Batch');
                // }
            });


            $("#print").click(function() {
                let batchId = $('[name="batch_id"]').val();

                let examId = $('#exam_id').data('id');

                let params = `?exam=${examId}&batch=${batchId}`;

                window.location.href = `/admin/batch-wise-result-print${params}`;
            });

        })
    </script>
@endsection
