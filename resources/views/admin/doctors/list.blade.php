@extends('admin.layouts.app')

@section('content')

    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <i class="fa fa-home"></i><a href="{{ url('/') }}">Home</a><i class="fa fa-angle-right"></i>
            </li>
            <li>
                Doctors List
            </li>
        </ul>
    </div>

    @if(Session::has('message'))
        <div class="alert alert-success" role="alert">
            <p> {{ Session::get('message') }}</p>
        </div>
    @endif




    <div class="modal fade" id="profile_edit_history" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="profile_edit_history">Edit Profile History</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                </div>

                    <div class="modal-body">
                    </div>
                    <div class="portlet-body">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
        </div>
    </div>






    <div class="row">
        <div class="col-md-12">
            <div class="portlet">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-globe"></i>
                        @if(request()->verified == 'yes')
                        <b>Verified</b>
                        @elseif(request()->verified == 'no')
                        <b>Unverified</b>
                        @elseif(request()->vip == true)
                        <b>VIP</b>
                        @endif
                        Doctors List

                        @can('Doctor Add')
                        <a href="{{url('admin/doctors/create')}}"> <i class="fa fa-plus" style="margin-left: 8px;"></i> </a>
                        @endcan

                        @if($vip || $verified)
                        @if(request()->ref == 'doctor')
                        <a 
                            href="{{ url('admin/doctors') }}"
                            class="btn btn-primary btn-xs"
                            style="margin-left: 32px;"
                        > 
                            Go To Doctor List
                        </a>
                        @endif
                        @if(request()->ref == 'course')
                        <a 
                            href="{{ url('admin/doctors-courses') }}"
                            class="btn btn-primary btn-xs"
                            style="margin-left: 32px;"
                        > 
                            Go to Doctor Course List
                        </a>
                        @endif
                        @else
                        <a 
                            href="{{ url('admin/doctors') }}?vip=true&ref=doctor"
                            class="btn btn-info btn-xs"
                            style="margin-left: 32px;"
                        > 
                            VIP Doctors
                        </a>
                        <a 
                            href="{{ url('admin/doctors') }}?verified=yes&ref=doctor"
                            class="btn btn-info btn-xs"
                            style="margin-left: 12px;"
                        > 
                            Verified Doctors
                        </a>
                        <a 
                            href="{{ url('admin/doctors') }}?verified=no&ref=doctor"
                            class="btn btn-info btn-xs"
                            style="margin-left: 12px;"
                        > 
                            Unverified doctors
                        </a>
                        @endif
                    </div>
                </div>
                <div class="portlet-body">
                    @if(!$vip)
                    <div class="row sc_search">
                        <div class="form-group col-md-2">
                            <h5>Start Date <span class="text-danger"></span></h5>
                            <div class="controls">
                                <input type="text"  size="20" class="form-control" id="from"  name="start_date">
                            </div>
                        </div>
                        <div class="form-group col-md-2">
                            <h5>End Date <span class="text-danger"></span></h5>
                            <div class="controls">
                                <input type="text"  size="20" class="form-control" id="to" name="end_date">
                            </div>
                        </div>
                    </div>
                    <div class="text-center" style="margin-left: 15px;">
                        <button type="text" id="btnFiterSubmitSearch" class="btn btn-info">Search</button>
                        <button type="text" id="excel-download" class="btn btn-info">Excel Download</button>
                    </div>
                    @endif

                    <table class="table table-striped table-bordered table-hover userstable datatable">
                        <thead>
                        <tr>
                            <th>ID</th>
                            @if($vip)
                            <th>VIP</th>
                            @endif
                            <th>Name</th>
                            <th>Email</th>
                            <th>BMDC</th>
                            <th>Medical College</th>
                            <th>Total Course</th>
                            <th>Phone</th>
                            <th>Status</th>
                            <th>Password</th>
                            <th>Registration</th>                           
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

    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script> --}}

    <script type="text/javascript">
        // DO NOT REMOVE : GLOBAL FUNCTIONS!
        $(document).ready(function() {
            $('.datatable').DataTable({
                order : [[0, 'DESC']],
                responsive: true,
                processing: true,
                serverSide: true,
                ajax: '/admin/doctors-list',
                ajax: {
                    url: "/admin/doctors-list",
                    type: 'GET',
                    data: function (d) {
                        d.start_date = $('#from').val();
                        d.end_date = $('#to').val();
                        d.vip = `{{ $vip }}`;
                        d.verified = `{{ $verified }}`;
                    }
                },
                "pageLength": 25,
                columns: [
                    {data: 'id',name:'d1.id'},
                    @if($vip)
                    {data: 'vip',name:'d1.vip'},
                    @endif
                    {data: 'name',name:'d1.name'},
                    {data: 'email',name:'d1.email'},
                    {data: 'bmdc_no',name:'d1.bmdc_no'},
                    {data: 'medical_college',name:'d3.name'},
                    {data: 'total_courses', name:'total_courses', searchable: false },
                    {data: 'mobile_number',name: 'd1.mobile_number'},
                    {data: 'status',name: 'd1.status'},
                    {data: 'main_password',name: 'd1.main_password'},
                    {data: 'updated_at',name: 'd1.updated_at'},
                    {data: 'action',searchable: false},
                ]
            })

            $('#btnFiterSubmitSearch').click(function(){
                $('.datatable').DataTable().draw(true);
            });


            $("body").on( "click", ".profile_edit_history_btn", function() {
                var doctor_id = $(this).data('id');
                $('.modal-body').load('/admin/profile-edit-history',{doctor_id: doctor_id,_token: '{{csrf_token()}}'},function(){
                    $('#profile_edit_history').modal({show:true});

                });
            });

            $('#excel-download').click(function(){
                var start_date = $('[name="start_date"]').val();
                var end_date = $('[name="end_date"]').val();
                var verified = `{{ request()->verified ?? '' }}`;
                var paras = start_date+'_'+end_date;
                if(start_date && end_date ){
                    window.location.href = "/admin/doctors-excel/"+paras+"?verified="+verified;
                }else {
                     alert('Please select start and end date');
                }
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

           

        });

        
    </script>

@endsection