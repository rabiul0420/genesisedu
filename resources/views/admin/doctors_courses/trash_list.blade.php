@extends('admin.layouts.app')

@section('content')

    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <i class="fa fa-home"></i><a href="{{ url('/') }}">Home</a><i class="fa fa-angle-right"></i>
            </li>
            <li>Doctors List</li>
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
                        <i class="fa fa-globe"></i>Doctors List
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
                        <div class="form-group col-md-4">
                            <h5>Year <span class="text-danger"></span></h5>
                            <div class="controls">
                                {!! Form::select('year',$years, '' ,['class'=>'form-control','required'=>'required','id'=>'year']) !!}<i></i>
                            </div>
                        </div>
                        <div class="form-group col-md-4">
                            <h5>Session <span class="text-danger"></span></h5>
                            <div class="controls">
                                @php  $sessions->prepend('Select Session', ''); @endphp
                                {!! Form::select('session_id',$sessions, '' ,['class'=>'form-control','required'=>'required','id'=>'session_id']) !!}<i></i>
                            </div>
                        </div>
                        <div class="form-group col-md-4">
                            <h5>Batch <span class="text-danger"></span></h5>
                            <div class="controls">
                                @php  $batches->prepend('Select Batch', ''); @endphp
                                {!! Form::select('batch_id',$batches, '' ,['class'=>'form-control batch2','required'=>'required','id'=>'batch_id']) !!}<i></i>
                            </div>
                        </div>
                    </div>
                        <div class="text-center" style="margin-left: 15px;">
                            <button type="text" id="btnFiterSubmitSearch" class="btn btn-info">Search</button>
                        </div>

                    <table class="table table-striped table-bordered table-hover userstable datatable">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>BMDC No</th>
                            <th>Password</th>
                            <th>Doctor Name</th>
                            <th>Mobile Number</th>
                            <th>Registration No</th>
                            <th>Institute</th>
                            <th>Course</th>
                            <th>Faculty</th>
                            <th>Discipline</th>
                            <th>Batch</th>
                            <th>Year</th>
                            <th>Session</th>
                            <th>Branch</th>
                            <th>Admission Time(GMT)</th>
                            <th>Payment Status</th>
                            <th>Status</th>
                            <th>Trash by</th>
                            <th>Actions</th>
                        </tr>
                        </thead>

                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('js')

    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/css/select2.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/js/select2.js" type="text/javascript"></script>

    <script type="text/javascript">
        // DO NOT REMOVE : GLOBAL FUNCTIONS!
        $(document).ready(function() {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var table = $('.datatable').DataTable({
                order : [[0, 'DESC']],
                responsive: true,
                processing: true,
                serverSide: true,
                ajax: {
                    url: "/admin/doctors-courses-trash-list",
                    type: 'GET',
                    data: function (d) {
                        d.year = $('#year').val();
                        d.session_id = $('#session_id').val();
                        d.batch_id = $('#batch_id').val();
                    }
                },
                "pageLength": 25,
                columns: [
                    {data: 'id',name:'d1.id'},
                    {data: 'bmdc_no',name:'d2.bmdc_no'},
                    {data: 'main_password',name:'d2.main_password'},
                    {data: 'doctor_name',name:'d2.name'},
                    {data: 'mobile_number',name:'d2.mobile_number'},
                    {data: 'reg_no',name:'d1.reg_no'},
                    {data: 'institute_name',name:'d3.name'},
                    {data: 'course_name',name:'d4.name'},
                    {data: 'faculty_name',name:'d5.name'},
                    {data: 'subject_name',name:'d6.name'},
                    {data: 'batche_name',name:'d7.name'},
                    {data: 'year',name:'d1.year'},
                    {data: 'session_name',name:'d8.name'},
                    {data: 'branch_name',name:'d10.name'},
                    {data: 'admission_time'},
                    {data: 'payment_status',name: 'd1.payment_status'},
                    {data: 'status',name: 'd1.status'},
                    {data: 'trash_by_name',name: 'd11.name'},
                    {data: 'action', sortable: false, searchable: false},
                ]

            })

            $('#btnFiterSubmitSearch').click(function(){
                $('.datatable').DataTable().draw(true);
            });

            $('.batch2').select2();

        })
    </script>

@endsection
