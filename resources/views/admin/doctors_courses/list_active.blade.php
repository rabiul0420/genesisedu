@extends('admin.layouts.app')

@section('content')

    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <i class="fa fa-home"></i><a href="{{ url('/') }}">Home</a><i class="fa fa-angle-right"></i>
            </li>
            <li>Active doctors courses list</li>
        </ul>
    </div>

    @if(Session::has('message'))
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
                        <i class="fa fa-globe"></i>Active Doctors Courses List
                        {{--@can('Doctor Course Add')
                            <a href="{{url('admin/doctors-courses/create')}}"> <i class="fa fa-plus"></i> </a>
                        @endcan--}}

                        @can('Doctor Add')
                            <a 
                                href="{{ url('admin/doctors') }}?vip=true&ref=course"
                                class="btn btn-info btn-xs"
                                style="margin-left: 32px;"
                            > 
                                VIP Doctors
                            </a>
                        @endcan
                    </div>
                </div>
                <div>
                    <?php
                    //echo '<pre>';
                    //print_r($doctors);
                    ?>
                </div>

                <div class="portlet-body">
                    <div class="row sc_search">
                        <div class="form-group col-md-2">
                            <label>Year <span class="text-danger"></span></label>
                            <div class="controls">
                                {!! Form::select('year',$years, '' ,['class'=>'form-control year','required'=>'required','id'=>'year']) !!}<i></i>
                            </div>
                        </div>

                        <div class="course">
                            <div class="form-group col-md-2">
                                <label>Course <span class="text-danger"></span></label>
                                <div class="controls">
                                    @php  $courses->prepend(' -- Select Course --', ''); @endphp
                                    {!! Form::select('course_id',$courses, '' ,['class'=>'form-control select2 course_id','required'=>'required','id'=>'course_id']) !!}<i></i>
                                </div>
                            </div>
                        </div>

                        <div class="session">
                            <div class="form-group col-md-2">
                                <label>Session <span class="text-danger"></span></label>
                                <div class="controls">
                                    @php  $sessions->prepend(' --Select Session --', ''); @endphp
                                    {!! Form::select('session_id',$sessions, '' ,['class'=>'form-control','required'=>'required','id'=>'session_id']) !!}<i></i>
                                </div>
                            </div>
                        </div>
                        <div class="batch">
                            <div class="form-group col-md-2">
                                <label>Batch <span class="text-danger"></span></label>
                                <div class="controls">
                                    @php  $batches->prepend(' --Select Batch --', ''); @endphp
                                    {!! Form::select('batch_id',$batches, '' ,['class'=>'form-control select2','required'=>'required','id'=>'batch_id']) !!}<i></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="portlet-body">
                    <div class="row sc_search">
                        
                        <div class="form-group col-md-2">
                            <label>Start Date <span class="text-danger"></span></label>
                            <div class="controls">
                                <input type="text"  size="20" class="form-control" id="from"  name="start_date">
                            </div>
                        </div>
                        <div class="form-group col-md-2">
                            <label>End Date <span class="text-danger"></span></label>
                            <div class="controls">
                                <input type="text"  size="20" class="form-control" id="to" name="end_date">
                            </div>
                        </div>
                        <div class="form-group col-md-2">
                            <label>Lecture Sheet Status</label>
                            <div class="controls">
                                <select class="form-control" name="lecture_sheet_status">
                                    <option selected value="all" >All</option>
                                    <option value="Not_Delivered">Not Delivered</option>
                                    <option value="In_Progress">In Progress</option>
                                    <option value="Completed">Completed</option>
                                  </select>
                            </div>
                        </div>

                        <div class="subject">
                            <div class="form-group col-md-2">
                                <label>Discipline <span class="text-danger"></span></label>
                                <div class="controls">
                                    @php  $subjects->prepend(' --Select Discipline --', ''); @endphp
                                    {!! Form::select('subject_id',$subjects, '' ,['class'=>'form-control','required'=>'required','id'=>'subject_id']) !!}<i></i>
                                </div>
                            </div>
                        </div>

                        <div class="payment_completed_by_id">
                            <div class="form-group col-md-2">
                                <label>Administrators <span class="text-danger"></span></label>
                                <div class="controls">
                                    <select id="payment_completed_by_id" class="form-control select2">
                                        <option value=""> -- Select Administrator -- </option>
                                        @foreach ($administrators as $administrator )
                                        <option value="{{ $administrator->id }}">{{ $administrator->name }} - {{ $administrator->phone_number }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="payment_status">
                            <div class="form-group col-md-2">
                                <label>Payment <span class="text-danger"></span></label>
                                <div class="controls">
                                    @php   $batchespayment->prepend(' -- Select Payment Status --'); @endphp
                                    {!! Form::select('payment_status', $batchespayment, '' ,['class'=>'form-control','required'=>'required','id'=>'payment_status']) !!}<i></i>
                                </div>
                            </div>
                        </div>
           
                    </div>
                        <div class="text-center" style="margin-left: 15px;">
                            <button type="button" id="btnFiterSubmitSearch" class="btn btn-info">Search</button>
                            
                            @can('Excel Download')
                                <button type="button" id="excel-download" class="btn btn-info">Excel Download</button>
                            @endcan

                            <button type="button" id="resultLink" class="btn btn-primary">Result</button>
                        </div>

                    <table class="table table-striped table-bordered table-hover userstable datatable">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>BMDC No</th>
                            <!-- <th>Password</th> -->
                            <th>Doctor Name</th>
                            <th>Phone</th>
                            <th>Registration No</th>
                            <th>Exam Roll</th>
                            <th>Institute</th>
                            <th>Course</th>
                            <th>Faculty</th>
                            <th>Discipline</th>
                            <th>BCPS Discipline</th>
                            <th>Batch</th>
                            <th>Year</th>
                            <th>Session</th>
                            <th>Branch</th>
                            <th>Admission Time</th>
                            <th>Payment Status</th>
                            <th>Payment Completed By</th>
                            {{-- <th>Batch Shifted</th> --}}
                            <th>Actions</th>
                        </tr>
                        </thead>

                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="pament" tabindex="-1" role="dialog" aria-labelledby="cashPament" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <label class="modal-title" id="exampleModalLabel">Payment</label>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->

    <!-- Modal print doctor hisory-->
    <div class="modal fade" id="print_doctor" tabindex="-1" role="dialog" aria-labelledby="cashPament" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <label class="modal-title" id="exampleModalLabel">Doctor Addmission Details</label>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <!--End Modal print doctor hisory-->

    <div class="modal fade" id="batch_shifted_details" tabindex="-1" role="dialog" aria-labelledby="cashPament" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <label class="modal-title" id="exampleModalLabel">Batch Shifted</label>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>



@endsection

@section('js')

    <script>
        const resultLink = document.getElementById('resultLink');

        resultLink.onclick = () => {
            if(!document.getElementById('batch_id').value) {
                return alert('Select Batch')
            }

            return window.open(`/admin/results/${document.getElementById('batch_id').value}`, "_blank");
        }
    </script>

    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/css/select2.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/js/select2.js" type="text/javascript"></script>
    <script src="{{ asset('assets/scripts/jquery-ui.min.js') }}"></script>
    <script src="https://github.com/mgalante/jquery.redirect/blob/master/jquery.redirect.js" type="text/javascript"></script>

    <script type="text/javascript">
        // DO NOT REMOVE : GLOBAL FUNCTIONS!
        $(document).ready(function() {
            
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            // $("body").on( "change", "[name='course_id'],[name='year']", function() {
            //     var course_id = $('#course_id').val();
            //     var year = $('.year').val();
                
            //     $.ajax({
            //         type: "POST",
            //         url: '/admin/search-session',
            //         dataType: 'HTML',
            //         data: {course_id : course_id, year: year },
            //         success: function( data ) {
            //              $('.session').html(data); 
            //         }
            //     });

            //     // if($(this).attr('name')=='course_id'){
            //     //     $.ajax({
            //     //         type: "POST",
            //     //         url: '/admin/search-subject',
            //     //         dataType: 'HTML',
            //     //         data: {course_id : course_id },
            //     //         success: function( data ) {
            //     //             $('.subject').html(data); 
            //     //             $('#subject_id').select2(); 
            //     //         }
            //     //     });
            //     // }

            // })



            $("body").on( "change", "[name='year']", function() {
            var year = $('.year').val();
            $.ajax({
                type: "GET",
                url: '/admin/class-course-search',
                dataType: 'HTML',
                data: { year: year },
                success: function( data ) {
                     $('.course').html(data); 
                     $('#course_id').select2(); 
                }
            });
        })


        $("body").on( "change", "[name='course_id']", function() {
            var course_id = $(this).val();
            var year = $('.year').val();
            $.ajax({
                type: "GET",
                url: '/admin/class-search-session',
                dataType: 'HTML',
                data: {course_id : course_id, year: year },
                success: function( data ) {
                     $('.session').html(data); 
                     $('#session_id').html(data); 
                }
            });
        })

 
        $("body").on( "change", "[name='session_id']", function() {
            var session_id = $(this).val();
            var course_id = $('#course_id').val();
            // alert(course_id)
            var year = $('.year').val();

            $.ajax({
                type: "POST",
                url: '/admin/search-batch',
                dataType: 'HTML',
                data: {session_id : session_id, course_id : course_id, year : year },
                success: function( data ) {
                        $('.batch').html(data);
                        $('#batch_id').select2();
                }
            });
        })

            var table = $('.datatable').DataTable({
                order : [[0, 'DESC']],
                responsive: true,
                processing: true,
                serverSide: true,
                ajax: {
                    url: "/admin/doctor-course-active-list-ajax",
                    type: 'GET',
                    data: function (d) {
                        d.year = $('#year').val();
                        d.session_id = $('#session_id').val();
                        d.course_id = $('#course_id').val();
                        d.batch_id = $('#batch_id').val();
                        d.start_date = $('#from').val();
                        d.end_date = $('#to').val();
                        d.lecture_sheet_status = $('[name="lecture_sheet_status"]').val();
                        d.subject_id = $('#subject_id').val();
                        d.payment_status = $('#payment_status').val();
                        d.payment_completed_by_id = $('#payment_completed_by_id').val();
                        console.log(d.lecture_sheet_status)
                        console.log(d.start_date)
                        console.log(d.end_date)

                    },
                },
                "pageLength": 25,
                columns: [
                    {data: 'id',name:'d1.id'},
                    {data: 'bmdc_no',name:'d2.bmdc_no'},
                    // {data: 'main_password',name:'d2.main_password'},
                    {data: 'doctor_name',name:'d2.name'},
                    {data: 'mobile_number',name:'d2.mobile_number'},
                    {data: 'reg_no',name:'d1.reg_no'},
                    {data: 'roll',name:'d1.roll'},
                    {data: 'institute_name',name:'d3.name'},
                    {data: 'course_name',name:'d4.name'},
                    {data: 'faculty_name',name:'d5.name'},
                    {data: 'subject_name',name:'d6.name'},
                    {data: 'bcps_subject_name',name:'bs.name'},
                    {data: 'batch_name',name:'d7.name'},
                    {data: 'year',name:'d1.year'},
                    {data: 'session_name',name:'d8.name'},
                    {data: 'branch_name',name:'d10.name'},
                    {data: 'admission_time'},
                    {data: 'payment_status',name: 'd1.payment_status'},
                    {data: 'payment_completed_by'},
                    // {data: 'batch_shifted',name: 'd1.batch_shifted'},
                    //{data: 'status',name: 'd1.status'},
                    {data: 'action', searchable: false, sortable: false},
                                    
                ],
            });

            $('#btnFiterSubmitSearch').click(function(){
                $('.datatable').DataTable().draw(true);
            });
            
            $('.select2').select2();

            $("#excel-download").click(function(){

                var year = $('[name="year"]').val();
                var course_id = $('[name="course_id"]').val();
                var session_id = $('[name="session_id"]').val();
                var batch_id = $('[name="batch_id"]').val();
                var subject_id = $('[name="subject_id"]').val();

                var params = JSON.stringify({
                        'year' : year,
                        'course_id' : course_id,
                        'session_id' : session_id,
                        'subject_id' : subject_id,
                        'batch_id' : batch_id,
                    });

                window.location.href = "/admin/batch-excel-download/"+params;

            });

            $("body").on( "click", ".payment", function() {
                var doctor_course_id = $(this).attr('id');
                $('.modal-body').load('/admin/doctors-courses-payemnt-details',{doctor_course_id: doctor_course_id,_token: '{{csrf_token()}}'},function(){
                    $('#pament').modal({show:true});

                });
            });

            $("body").on( "click", ".print_doctor", function() {
                var doctor_course_id = $(this).attr('id');
                $('.modal-body').load('/admin/doctors-courses-details',{doctor_course_id: doctor_course_id,_token: '{{csrf_token()}}'},function(){
                    $('#print_doctor').modal({show:true});

                });
            });

            $("body").on( "click", ".batch_shifted", function() {
                var doctor_course_id = $(this).attr('id');
                $('.modal-body').load('/admin/doctors-courses-batch-shifted-details',{doctor_course_id: doctor_course_id,_token: '{{csrf_token()}}'},function(){
                    $('#batch_shifted_details').modal({show:true});

                });
            });

            $( "#from" ).datepicker({
                defaultDate: "+0d",
                changeMonth: true,
                dateFormat: 'yy-mm-dd',
                numberOfMonths: 1,
                onClose: function( selectedDate ) {
                    $( "#to" ).datepicker( "option", "minDate", selectedDate );
                }
            });
            $( "#to" ).datepicker({
                defaultDate: "+1d",
                changeMonth: true,
                dateFormat: 'yy-mm-dd',
                numberOfMonths: 1,
                onClose: function( selectedDate ) {
                    $( "#from" ).datepicker( "option", "maxDate", selectedDate );
                }
            });

        })



    </script>

@endsection