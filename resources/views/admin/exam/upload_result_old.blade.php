@extends('admin.layouts.app')

@section('content')
    
    
    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <i class="fa fa-home"></i>
                <a href="{{ url('/') }}">Home</a>
                <i class="fa fa-angle-right"></i>
            </li>
            <li>
                {{$title}}
            </li>
        </ul>

    </div>

    @if(Session::has('message'))
        <div class="alert {{ (Session::get('class'))?Session::get('class'):'alert-success' }}" role="alert">
            <p> {{ Session::get('message') }}</p>
        </div>
    @endif

    <div class="row">
        <div class="col-md-12">
            <!-- BEGIN EXAMPLE TABLE PORTLET-->
            <div class="portlet">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-reorder"></i> Exam : <b>{{ $exam_info->name }}</b>
                    </div>
                </div>
                <div class="portlet-body form">
                    <!-- BEGIN FORM-->
                    {!! Form::open(['url'=>'admin/result-submit','files'=>true,'class'=>'form-horizontal']) !!}
                    <div class="form-body">


                        <input type="hidden" name="exam_id" value="{{ Request::segment(3) }}">

                        <div class="form-group" >
                            <label class="col-md-2 control-label">Select Doctor's Answer</label>
                            <div class="col-md-3">
                                <div class="input-icon right">
                                    <input class="form-control" type="file" name="result">
                                </div>
                            </div>
                        </div>

                        <div class="form-group" >
                            <label class="col-md-2 control-label">Select Correct Answer</label>
                            <div class="col-md-3">
                                <div class="input-icon right">
                                    <input class="form-control" type="file" name="answer">
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="form-actions">
                        <div class="row">
                            <div class="col-md-offset-2 col-md-9">
                                <button type="submit" class="btn btn-info">Submit</button>
                                <a href="{{ url('admin/exam') }}" class="btn btn-default">Cancel</a>
                            </div>
                        </div>
                    </div>
                {!! Form::close() !!}
                <!-- END FORM-->
                </div>
            </div>
            <!-- END EXAMPLE TABLE PORTLET-->

        </div>
    </div>

    <!-- END PAGE CONTENT-->


@endsection

@section('js')
    
    

    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker.min.css" />
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker3.min.css" />
    <script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/js/bootstrap-datepicker.min.js"></script>

    
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/css/select2.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/js/select2.js" type="text/javascript"></script>
    
    <script type="text/javascript">
        $(document).ready(function() {

            $('#datepicker').datepicker({
                format: 'yyyy-mm-dd',
                startDate: '1900-01-01',
                endDate: '2030-12-30',
            }).on('changeDate', function(e){
                $(this).datepicker('hide');
            });


            $("body").on( "change", "[name='question_type']", function() {
                var question_type = $(this).val();
                $.ajax({

                    
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: '/admin/question-type',
                    dataType: 'HTML',
                    data: {question_type : question_type},
                    success: function( data ) {
                        $('.question_type').html(data);
                        
                    }
                });
            })

            $("body").on( "change", "[name='institute_id']", function() {
                var institute_id = $(this).val();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: '/admin/institute-course',
                    dataType: 'HTML',
                    data: {institute_id : institute_id},
                    success: function( data ) {
                        $('.course').html(data);
                        $('.faculty').html('');
                        $('.subject').html('');
                    }
                });
            })



            $("body").on( "change", "[name='course_id']", function() {

                var course_id = $(this).val();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: '/admin/'+$("[name='url']").val(),
                    dataType: 'HTML',
                    data: {course_id: course_id},
                    success: function( data ) {
                        $('.faculty').html(data);
                    }
                });
            })

            $("body").on( "change", "[name='faculty_id']", function() {
                var faculty_id = $(this).val();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: '/admin/faculty-subject',
                    dataType: 'HTML',
                    data: {faculty_id: faculty_id},
                    success: function( data ) {
                        $('.subject').html(data);
                    }
                });
            })

            $("body").on( "change", "[name='question_type_id']", function() {
                var question_type_id = $(this).val();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: '/admin/question-type-mcq-sba',
                    dataType: 'HTML',
                    data: {question_type_id: question_type_id},
                    success: function( data ) {
                        $('.mcq-sba').html(data);
                        $('.mcqs2').select2({
                            minimumInputLength: 3,
                            escapeMarkup: function (markup) { return markup; },
                            language: {
                                noResults: function () {
                                    return "No MCQ question found, for add new MCQ question please <a target='_blank' href='{{ url('admin/mcq/create') }}'>Click here</a>";
                                }
                            },
                            ajax: {
                                url: '/admin/search-questions',
                                dataType: 'json',
                                type: "GET",
                                quietMillis: 50,
                                data: function (term) {
                                    return {
                                        term: term,
                                        type: 2
                                    };
                                },
                                processResults: function (data) {
                                    return {
                                        results: $.map(data, function (item) {
                                            return { id:item.id , text: item.question_title };
                                        })
                                    };
                                }
                            }
                        }).trigger('change');
                        $('.sbas2').select2({
                            minimumInputLength: 3,
                            escapeMarkup: function (markup) { return markup; },
                            language: {
                                noResults: function () {
                                    return "No SBA question found, for add new SBA question please <a target='_blank' href='{{ url('admin/sba/create') }}'>Click here</a>";
                                }
                            },
                            ajax: {
                                url: '/admin/search-questions',
                                dataType: 'json',
                                type: "GET",
                                quietMillis: 50,
                                data: function (term) {
                                    return {
                                        term: term,
                                        type: 1
                                    };
                                },
                                processResults: function (data) {
                                    return {
                                        results: $.map(data, function (item) {
                                            return { id:item.id , text: item.question_title };
                                        })
                                    };
                                }
                            }
                        }).trigger('change');
                    }
                });
            })

            $("body").on('select2:close','.mcqs2', function() {
                let select = $(this)
                $(this).next('span.select2').find('ul').html(function() {
                    let selected_mcq = select.select2('data').length;
                    let total_mcq = $('[name="mcq_count"]').val();
                    let moreq = total_mcq-selected_mcq;
                    if(moreq){
                        $('.mcq_count').text('Add more '+moreq+' Questions');
                        $('[name="mcq_full"]').attr('required',true);
                    }else{
                        $('.mcq_count').text('');
                        $('[name="mcq_full"]').removeAttr('required');
                    }


                })
            })

            $("body").on('select2:close','.sbas2', function() {
                let select = $(this)
                $(this).next('span.select2').find('ul').html(function() {
                    let selected_sba = select.select2('data').length;
                    let total_sba = $('[name="mcq_count"]').val();
                    let moreq =total_sba-selected_sba;
                    if(moreq){
                        $('.sba_count').text('Add more '+moreq+' Questions');
                        $('[name="sba_full"]').attr('required',true);
                    }else{
                        $('.sba_count').text('');
                        $('[name="sba_full"]').removeAttr('required');
                    }

                })
            })

            
            $('.topic2').select2();



        })
    </script>


@endsection