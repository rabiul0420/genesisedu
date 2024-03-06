@extends('admin.layouts.app')

@section('content')

    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <i class="fa fa-home"></i><a href="{{ url('/') }}">Home</a><i class="fa fa-angle-right"></i>
            </li>
            <li> Installment Payment List</li>
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
                        <i class="fa fa-globe"></i> Installment Payment List 
                        <a href="{{ url('admin/sms-to-installment-due-list') }}" class="btn btn-xs btn-info"> Sms to installment due list</a>
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
                                    {!! Form::select('session_id',$sessions, '',['class'=>'form-control select2','required'=>'required','id'=>'session_id']) !!}<i></i>
                                </div>
                            </div>
                        </div>
                        <div class="batch">
                            <div class="form-group col-md-2">
                                <h5>Batch <span class="text-danger"></span></h5>
                                <div class="controls">
                                    @php  $batches->prepend('Select Batch', ''); @endphp
                                    {!! Form::select('batch_id',$batches, '' ,['class'=>'form-control batch2','required'=>'required','id'=>'batch_id']) !!}<i></i>
                                </div>
                            </div>
                        </div>                      

                    </div>
                    <div class="row">
                        <div class="form-group col-md-2">
                            <h5>Start Date <span class="text-danger"></span></h5>
                            <div class="controls">
                                <input type="text"  size="20" class="form-control date" id="from"  name="start_date">
                            </div>
                        </div>

                        <div class="form-group col-md-2">
                            <h5>End Date <span class="text-danger"></span></h5>
                            <div class="controls">
                                <input type="text"  size="20" class="form-control date" id="to" name="end_date">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-3">
                            <button type="text" id="btnsearch" class="btn btn-info">Search</button>
                        </div>
                    </div>
                    <table style="width: 100%;">
                        <table id="table_1" class="table table-striped table-bordered table-hover datatable" style="width: 100%;">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Reg No</th>
                                <th>Mobile Number</th>
                                <th>Institute</th>
                                <th>Course</th>
                                <th>Year</th>
                                <th>Session</th>
                                <th>Batch</th>
                                <th>Faculty</th>
                                <th>Subject</th>
                                <th>BCPS Subject</th>
                                <th>Course Price</th>
                                <th>Next Installment Last Date</th>
                                <th>Installments</th>                                
                                <th>Action</th>
                                
                            </tr>
                            </thead>
                            <tbody>

                            
                            </tbody>
                        </table>
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

    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker.min.css" />
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker3.min.css" />
    <script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/js/bootstrap-datepicker.min.js"></script>


    <script type="text/javascript">

        // DO NOT REMOVE : GLOBAL FUNCTIONS!
        $(document).ready(function() {

            $("body").on( "focus", ".date", function() {
                $(this).datepicker({
                    format: 'yyyy-mm-dd',
                    startDate: '',
                    endDate: '',
                }).on('changeDate', function(e){
                    $(this).datepicker('hide');
                });
            });

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

            $("body").on( "click", ".send-sms", function(e) { e.preventDefault();
                var doctor_course_id = $(this).attr('id');
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: '/admin/sms-to-installment-due-list-from-admin',
                    dataType: 'HTML',
                    data: {doctor_course_id : doctor_course_id},
                    success: function( data ) {
                        var dat = JSON.parse(data); 
                        alert("Successfully sent sms to : " + dat.mobile_number );
                    }
                });
            });

            var table = $('.datatable').DataTable({
                responsive: true,
                processing: true,
                serverSide: true,
                scrollY: 1000,
                scrollX: 500,
                scrollCollapse: true,
                ajax: {
                    url: "/admin/installment-due-ajax-list",
                    type: 'GET',
                    data: function (d) {
                        d.institute_id = $('[name="institute_id"]').val();
                        d.course_id = $('[name="course_id"]').val();
                        d.year = $('[name="year"]').val();
                        d.session_id = $('[name="session_id"]').val();
                        d.batch_id = $('[name="batch_id"]').val();
                        d.start_date = $('[name="start_date"]').val();
                        d.end_date = $('[name="end_date"]').val();
                    }
                },
                "pageLength": 10,
                columns: [
                    // {data: 'id',name:'doctors_courses.id'},
                    // {data: 'doctor_name',name:'d8.name'},
                    // {data: 'reg_no',name:'doctors_courses.reg_no'},
                    // {data: 'phone_number',name:'d8.mobile_number'},
                    // {data: 'institute_name',name:'d2.name'},
                    // {data: 'course_name',name:'d3.name'},
                    // {data: 'year',name:'doctors_courses.year'},
                    // {data: 'session_name',name:'d4.name'},
                    // {data: 'batch_name',name:'d5.name'},
                    // {data: 'faculty_name',name:'d6.name'},
                    // {data: 'subject_name',name:'d7.name'},
                    // {data: 'doctors_courses.bcps_subject_id',name:'doctors_courses.bcps_subject_id'},
                    // {data: 'course_price',name:'doctors_courses.course_price'},
                    // {data: 'next_installment_last_date',name:'next_installment_last_date'},
                    // {data: 'installments',name:'installments'},                    
                    // {data: 'action',searchable: false},
                    {data: 'id',name:'id'},
                    {data: 'doctor.name',name:'doctor.name'},
                    {data: 'reg_no',name:'reg_no'},
                    {data: 'doctor.mobile_number',name:'doctor.mobile_number'},
                    {data: 'institute.name',name:'institute.name'},
                    {data: 'course.name',name:'course.name'},
                    {data: 'year',name:'year'},
                    {data: 'session.name',name:'session.name'},
                    {data: 'batch.name',name:'batch.name'},
                    {data: 'faculty.name',name:'faculty.name',defaultContent: ''},
                    {data: 'subject.name',name:'subject.name',defaultContent: ''},
                    {data: 'bcps_subject.name',name:'bcps_subject.name',defaultContent: ''},
                    {data: 'course_price',name:'course_price'},
                    {data: 'next_installment_last_date',name:'next_installment_last_date',defaultContent: ''},
                    {data: 'installments',name:'installments'},                    
                    {data: 'action',searchable: false},
                ]
            });
            $('#btnsearch').click(function(){
                $('.datatable').DataTable().draw();                
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

            $("body").on( "change", "[name='institute_id']", function() {
                var institute_id = $(this).val();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: '/admin/institute-change-in-installemnt-due-list',
                    dataType: 'HTML',
                    data: {institute_id : institute_id},
                    success: function( data ) {
                        $('.course').html('');
                        $('.year').html('');
                        $('.session').html('');
                        $('.batch').html('');
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
                    url: '/admin/course-change-in-installemnt-due-list',
                    dataType: 'HTML',
                    data: {course_id: course_id},
                    success: function( data ) {
                        $('.year').html('');
                        $('.session').html('');
                        $('.batch').html('');
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
                    url: '/admin/year-change-in-installemnt-due-list',
                    dataType: 'HTML',
                    data: {course_id: course_id,year:year},
                    success: function( data ) {
                        $('.session').html('');
                        $('.batch').html('');
                        $('.session').html(data);
                    }
                });
            });

            $("body").on( "change", "[name='session_id']", function() {
                var course_id = $("[name='course_id']").val();
                var year = $("[name='year']").val();
                var session_id = $("[name='session_id']").val();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: '/admin/session-change-in-installemnt-due-list',
                    dataType: 'HTML',
                    data: {course_id: course_id,year:year,session_id:session_id},
                    success: function( data ) {
                        $('.batch').html('');
                        $('.batch').html(data);
                    }
                });
            });

        })
    </script>

@endsection