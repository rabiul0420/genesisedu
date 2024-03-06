@extends('admin.layouts.app')

@section('content')
 <div class="row">
        <div class="col-md-12">
            <div class="portlet">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-globe"></i>Sms Log History
                    </div>
                </div>

                <div class="row" style="padding: 5px 15px 0;">
                    <div class="form-group col-md-2">
                        <label>Date From</label>
                        <div class="controls">
                            <input type="text" autocomplete="off" value="" class="form-control input-append date" id="date_from" placeholder="Select Date">
                        </div>
                    </div>

                    <div class="form-group col-md-2">
                        <label>Date To</label>
                        <div class="controls">
                            <input type="text" autocomplete="off" value="" class="form-control input-append date" id="date_to" placeholder="Select Date">
                        </div>
                    </div>

                    <div class="form-group col-md-2">
                        <label>Sms Event</label>
                        <div class="controls">
                            <select class="form-control select2" id="sms_event">
                                <option value=""> -- Select Event -- </option>
                                @foreach ($sms_events as $sms_event)
                                <option value="{{ $sms_event }}">{{ $sms_event }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group col-md-2">
                        <label>Actions</label>
                        <div class="controls">
                            <button type="button" id="btnFiterSubmitSearch" class="btn btn-info">Filter</button>
                        </div>
                    </div>
                </div>

                <div class="portlet-body">
                    <table class="table table-striped table-bordered table-hover datatable">
                        <thead>
                        <tr>
                            <th class="text-center">ID</th>
                            <th class="text-center">Doctor Name</th>
                            <th class="text-center">Number Status</th>
                            <th class="text-center">Bmdc No</th>
                            <th class="text-center">Job ID</th> 
                            <th style="width: 100px">Delivery Status </th>
                            <th class="text-center">Date And Time</th>
                            <th class="text-center">Event</th>
                            <th class="text-center">Sender</th>
                            <th class="text-center">Action</th>
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
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker.min.css" />
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker3.min.css" />
<script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/js/bootstrap-datepicker.min.js"></script>

<script type="text/javascript">
    $(document).ready(function() {
        $('#date_from').datepicker({
            format: 'yyyy-mm-dd',
            startDate: `{{ $start_date }}`,
            endDate: `{{ $end_date }}`,
        }).on('changeDate', function(e){
            $(this).datepicker('hide');
        });

        $('#date_to').datepicker({
            format: 'yyyy-mm-dd',
            startDate: `{{ $start_date }}`,
            endDate: `{{ $end_date }}`,
        }).on('changeDate', function(e){
            $(this).datepicker('hide');
        });

        const table= $('.datatable').DataTable({
            order : [[0, 'DESC']],
            responsive: true,
            processing: true,
            serverSide: true,
            ajax: {
                url: '/admin/sms-log-ajax-list',
                type: 'GET',
                data: function (data) {
                    data.date_from = $('#date_from').val();
                    data.date_to = $('#date_to').val();
                    data.sms_event = $('#sms_event').val();
                }
            },
            "pageLength": 25,
            searchCols: [
                // 'sms_log.id','d1.name','sms_log.mobile_no'//
            ],
            columns: [
                {data: 'id', name:'sms_log.id'},
                {data: 'doctor_name',name:'d1.name'},
                {data: 'doctor_mobile_number', name: 'sms_log.mobile_no' },
                {data: 'doctor_bmdc_no',name:'d1.bmdc_no'},
                {data: 'job_id',name:'sms_log.job_id'},
                {data: 'delivery_status',name:'sms_log.delivery_status'},
                {data: 'created_at',name:'sms_log.created_at'},
                {data: 'event', name:'sms_log.event'},
                {data: 'admin_name', name:'u.name'},
                {data: 'action', name: 'd1.mobile_number'},
            ],
        });

        table.on('draw',function(){
            $("[data-job-id]").each(function(){
                $(this).on("click",function(){
                    const job_id = $(this).data('job-id');
                    const id = $(this).data('id');
                    const THIS = this;

                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: "POST",
                        url: '/admin/update-status',
                        dataType: 'JSON',
                        data: {job_id : job_id,id:id},
                        success: function( data ) {
                            console.log(data)

                            if( data.success ) {
                                table.draw('page');
                                // if( data.log.delivery_status == 'Delivered' ) {
                                //     $(THIS).attr( 'disabled', true );
                                // }
                            }

                        }
                    });
                });
            });
        });
        
        $('.select2').select2();
    
        $('#btnFiterSubmitSearch').click(function(){
            $('.datatable').DataTable().draw(true);
        });

    });
</script>

@endsection
