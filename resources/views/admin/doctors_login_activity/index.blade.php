@extends('admin.layouts.app')

@section('content')

    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <i class="fa fa-home"></i><a href="{{ url('/') }}"> Home </a><i class="fa fa-angle-right"></i>
            </li>
            <li>Doctors Activity List</li>
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
                        <i class="fa fa-globe"></i>Doctors Activity List
                    </div>
                </div>

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

                <div class="form-group col-md-2">
                    <br><br>
                    <button type="text" id="btnFiterSubmitSearch" class="btn btn-info">Search</button>
                </div>
                
                <div class="text-center">
                </div>

                <div class="portlet-body">
                    <table class="table table-striped table-bordered table-hover datatable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Doctors Name</th>
                                <th>BMDC No</th>
                                <th>Mobile Number</th>
                                <th>Email</th>
                                <th>Action</th>
                                <th>Last Used Time</th>
                                <th>Total Usage</th>
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
    <script src="{{ asset('assets/scripts/jquery-ui.min.js') }}"></script>

    <script type="text/javascript">
        // DO NOT REMOVE : GLOBAL FUNCTIONS!
        $(document).ready(function() {
            $('.datatable').DataTable({
                responsive: true,
                processing: true,
                serverSide: true,
                ajax: {
                    url: "/admin/doctors-app-activity-list",
                    type: 'GET',
                    data: function (d) {
                        d.start_date = $('#from').val();
                        d.end_date = $('#to').val();
                        console.log(d.start_date)
                        console.log(d.end_date)
                    }
                },
                pageLength: 25,
                columns: [
                    {data: 'doctor_id',name:'d.id'},
                    {data: 'doctor_name',name:'d.name'},
                    {data: 'bmdc_no',name:'d.bmdc_no'},
                    {data: 'mobile_number',name:'d.mobile_number'},
                    {data: 'email',name:'d.email'},
                    {data: 'action',name:'oat.name'},
                    {data: 'time',name:'oat.created_at'},
                    {data: 'total_usage', searchable: false, sortable: false,},
                ]
            })
        })

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

        $('#btnFiterSubmitSearch').click(function(){
            $('.datatable').DataTable().draw(true);
        });
    </script>

@endsection
