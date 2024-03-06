@extends('admin.layouts.app')

@section('content')

    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <i class="fa fa-home"></i><a href="{{ url('/') }}">Home</a><i class="fa fa-angle-right"></i>
            </li>
            <li>Discount List</li>
        </ul>
    </div>

    @if(Session::has('message'))
        <div class="alert {{ Session::get('class') ?? 'alert-success' }} " role="alert">
            <p> {{ Session::get('message') }}</p>
        </div>
    @endif

    <div class="modal fade" id="edit_history" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="edit_history">Edit History</h5>
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
                        <!-- BEGIN EXAMPLE TABLE PORTLET-->
            <div class="portlet">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-globe"></i>Discount List
                        <a href="{{ action('Admin\DiscountController@create') }}"> <i class="fa fa-plus"></i> </a>
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="row sc_search">
                        <div class="form-group col-md-2">
                            <h5>Start Date <span class="text-danger"></span></h5>
                            <div class="controls">
                                <input type="text" autocomplete="off" class="form-control" id="date_from"  name="start_date">
                            </div>
                        </div>
                        <div class="form-group col-md-2">
                            <h5>End Date <span class="text-danger"></span></h5>
                            <div class="controls">
                                <input type="text" autocomplete="off" class="form-control" id="date_to" name="end_date">
                            </div>
                        </div>
                    </div>
                    <div class="text-center" style="margin-left: 15px;">
                        <button type="text" id="btnFiterSubmitSearch" class="btn btn-info">Search</button>
                        <button type="text" id="excel-download" class="btn btn-info">Excel Download</button>
                    </div>
                    <table class="table table-striped table-bordered table-hover datatable">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Doctor Name</th>
                            <th>BMDC No</th>
                            <th>Discount For</th> 
                            <th>Batch Name</th> 
                            <th>Discount Code</th>
                            <th>Amount</th>
                            <th>Code Duration <small>(Hour)</small></th> 
                            <th>Used</th>
                            <th>Reference</th>
                            <th>Created By</th>
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
<script src="{{ asset('assets/scripts/jquery-ui.min.js') }}"></script>
<script type="text/javascript">
    // DO NOT REMOVE : GLOBAL FUNCTIONS!
    $(document).ready(function() {
        $('.datatable').DataTable({
            order : [[0, 'DESC']],
            responsive: true,
            processing: true,
            serverSide: true,
            ajax: {
                url: "/admin/discount-list",
                type: 'GET',
                data: function (d) {
                    d.date_from = $('#date_from').val();
                    d.date_to = $('#date_to').val();
                }
            },
            "pageLength": 25,
            columns: [
                {data: 'id', name:'discounts.id'},
                {data: 'doctor_name', name:'doctors.name'},
                {data: 'bmdc_no', name:'doctors.bmdc_no'},
                {data: 'phone', name:'discounts.phone'},
                {data: 'batch_name', name:'batches.name'},
                {data: 'discount_code', name:'discounts.discount_code'},
                {data: 'amount', name:'discounts.amount'},
                {data: 'code_duration', name:'discounts.code_duration'},
                {data: 'used', name:'discounts.used'},
                {data: 'reference', name:'discounts.reference'},
                {data: 'user_name', name:'users.name'},
                {data: 'status', name:'discounts.status'},
                {data: 'action', searchable: false},
            ]
        })
    })

    $("body").on( "click", ".edit_history_btn", function() {
        var discount_id = $(this).data('id');
        $('.modal-body').load('/admin/discount-edit-history',{discount_id: discount_id,_token: '{{csrf_token()}}'},function(){
            $('#edit_history').modal({show:true});

        });
    });

    $('#btnFiterSubmitSearch').click(function(){
        $('.datatable').DataTable().draw(true);
    });

    $('#excel-download').click(function(){
        var date_from = $('#date_from').val();
        var date_to = $('#date_to').val();

        if(date_from && date_to){
            window.location.href = `/admin/discount-excel-download?date_from=${date_from}&date_to=${date_to}`;
        }else {
            alert('Please select start and end date');
        }
    });

    $("#date_from").datepicker({
        defaultDate: "+0d",
        changeMonth: true,
        dateFormat: 'yy-mm-dd',
        numberOfMonths: 1,
        onClose: function( selectedDate ) {
            if(selectedDate) {
                $("#date_to" ).datepicker( "option", "minDate", selectedDate);
            }
        }
    });

    $("#date_from").datepicker("option", "minDate", `{{ $start_date }}`);
    $("#date_from").datepicker("option", "maxDate", `{{ $end_date }}`);

    $("#date_to").datepicker({
        defaultDate: "+1d",
        changeMonth: true,
        dateFormat: 'yy-mm-dd',
        startDate: `{{ $start_date }}`,
        endDate: `{{ $end_date }}`,
        numberOfMonths: 1,
        onClose: function( selectedDate ) {
            if(selectedDate) {
                $( "#date_from" ).datepicker( "option", "maxDate", selectedDate );
            }
        }
    });

    $("#date_to").datepicker("option", "minDate", `{{ $start_date }}`);
    $("#date_to").datepicker("option", "maxDate", `{{ $end_date }}`);

</script>

@endsection
