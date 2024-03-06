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
                        <i class="fa fa-globe"></i>{{ $title }}
                        @can('Batch Add')
                        <a href="{{ action('Admin\BatchController@create') }}"> <i class="fa fa-plus"></i> </a>  
                        @endcan
                      
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="row sc_search">
 
                        @include('admin.components.year_course_session')
    
                        <div class="batch">
                        </div>
                    </div>
                    <div class="text-center" style="margin-left: 15px;">
                        <button type="text" id="btnFiterSubmitSearch" class="btn btn-info">Search</button>
                    </div>
                    <table class="table table-striped table-bordered table-hover datatable">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Year</th>
                            <th>Batch Name</th>
                            <th>Sessions</th>
                            <th>Seat Capacity</th>
                            <th>Institute</th>
                            <th>Course</th>
                            <th>Branch</th>
                            <th>Batch Type</th>
                            <th>Admission Fee For</th>
                            <th>Admission Fee</th>
                            <th>Lecture Sheet Fee</th>
                            <th>Discount From Regular</th>
                            <th>Discount From Exam</th>
                            <th>Payment Times</th>
                            <th>Minimum Pay (%)</th>
                            <th>Discount</th>
                            <th>Lecture Sheet Size</th>
                            <th>Is Emi</th>
                            <th>Status</th>
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
            // $("body").on( "change", "[name='course_id']", function() {
            //     var course_id = $(this).val();
            //     var year = $('#year').val();

            //     $.ajax({
            //         type: "POST",
            //         url: '/admin/search-session',
            //         dataType: 'HTML',
            //         data: {course_id : course_id, year : year },
            //         success: function( data ) {
            //              $('#session_id').html(data); 
            //         }
            //     });
            // })


            $("body").on( "change", "[name='session_id']", function() {
                var session_id = $(this).val();
                var course_id = $('#course_id').val();
                var year = $('#year').val();

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
                    url: "/admin/batch-list",
                    type: 'GET',
                    data: function (d) {
                        d.year = $('#year').val();
                        d.session_id = $('#session_id').val();
                        d.course_id = $('#course_id').val();
                        d.batch_id = $('#batch_id').val();
                    }
                },
                
                columns: [
                    {data: 'id',name:'b.id'},                     
                    {data: 'year',name:'b.year'},
                    {data: 'batch_name',name:'b.name'},
                    {data: 'session_name',name:'b.session_id'},
                    {data: 'capacity',name:'b.capacity'},
                    {data: 'institute_name',name:'i.name'},
                    {data: 'course_name',name:'c.name'},
                    {data: 'branch_name',name:'br.name'},
                    {data: 'batch_type',name:'b.batch_type'},
                    {data: 'fee_type',name:'b.fee_type'},
                    {data: 'admission_fee',name:'b.admission_fee'},
                    {data: 'lecture_sheet_fee',name:'b.lecture_sheet_fee'},
                    {data: 'discount_from_regular',name:'b.discount_from_regular'},
                    {data: 'discount_from_exam',name:'b.discount_from_exam'},
                    {data: 'payment_times',name:'b.payment_times'},
                    {data: 'minimum_payment',name:'b.minimum_payment'},
                    {data: 'discount_fee',name:'b.discount_fee'},
                    {data: 'package_name',name:'ccp.name'},
                    {data: 'is_emi',name:'b.is_emi'},
                    {data: 'status',name:'b.status'},
                    {data: 'action',searchable: false},
                ]
            })
        })

        $('.batch2').select2();
        $('.session2').select2();
        $('.year2').select2();
        $('#btnFiterSubmitSearch').click(function(){
            $('.datatable').DataTable().draw(true);
        });
    </script>

@endsection
