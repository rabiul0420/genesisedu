
@extends('admin.layouts.app')

@section('content')
    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <i class="fa fa-home"></i><a href="{{ url('/') }}">Home</a><i class="fa fa-angle-right"></i>
            </li>
            <li>Payment List</li>
        </ul>
    </div>

    @if(Session::has('message'))
        <div class="alert alert-success" role="alert">
            <p> {{ Session::get('message') }}</p>
        </div>
    @endif

    <div class="row">
        <div class="col-md-12">
            <div class="portlet">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-globe"></i>Payment List
                        {{-- <a class="btn btn-info btn-xs" style="margin-left:30px"  href="">Installment Payment</a>
                        <a class="btn btn-info btn-xs" href="">No Payment</a> --}}

                    </div>
                </div>

                <div class="portlet-body">
                    <div class="row sc_search">
                        @include('admin.components.year_course_session')
    
                        {{-- <div class="batch">
                        </div> --}}

                        <div class="form-group col-md-2 batches">
                            <label>Batch </span></label>
                            <div class="controls">
                                @php  $batches->prepend('--Select Batch--', ''); @endphp
                                {!! Form::select('batch_id',$batches, '' ,['class'=>'form-control batch2','required'=>'required','id'=>'batch_id']) !!}<i></i>
                            </div>
                        </div>
                    </div>
                </div>  

                <div class="portlet-body">
                    <div class="row sc_search">
                        <div class="form-group col-md-3">
                            <h5>Start Date <span class="text-danger"></span></h5>
                            <div class="controls">
                                <input type="text"  size="20" class="form-control" id="from"  name="start_date">
                            </div>
                        </div>
                        <div class="form-group col-md-3">
                            <h5>End Date <span class="text-danger"></span></h5>
                            <div class="controls">
                                <input type="text"  size="20" class="form-control" id="to" name="end_date">
                            </div>
                        </div>
                    </div>
                        <div class="text-center" style="margin-left: 15px;">
                            <button type="text" id="btnFiterSubmitSearch" class="btn btn-info px-2">Search</button>
                            @can('Excel Download')
                                <button type="text" id="excel-download" class="btn btn-info">Excel Download</button>
                            @endcan
                            @can('Excel Download')
                                <button type="text" id="paymentTotal" class="btn btn-primary">Total Amount</button>
                            @endcan
                        </div>


                    <!-- Modal -->
                    <div class="modal fade" id="payment_note" tabindex="-1" role="dialog" aria-labelledby="cashPament" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Payment Note</h5>
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


                    <table class="table table-striped table-bordered table-hover userstable datatable">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Date</th>
                            <th>Doctor Name</th>
                            <th>Batch</th>
                            <th>Reg No</th>
                            <th>Course Name</th>
                            <th>Faculty</th>
                            <th>Subject</th>
                            <th>BCPS Subject</th>
                            <th>Mobile Number</th>
                            <th>Trans ID</th>                           
                            <th>Paid Amount</th>
                            <th>Payment Status</th>
                            <th>Payment Verified</th>
                            @can('Payment List')
                            <th style="width: 90px">Payment verification</th>                         
                            <th>Note</th>
                            @endcan
                        </tr>
                        </thead>

                    </table>
                </div>
            </div>
        </div>
    </div>

    

    
        {{-- <h1>Note box</h1>
        
        <form  method="post" action="" id="note_box">
            <textarea rows="4" cols="50" name="note_box" form="note_box">
            Enter text here...</textarea> <br>
          <input class="btn btn success" type="submit">
        </form>
         --}}
    


@endsection

@section('js')

    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/css/select2.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/js/select2.js" type="text/javascript"></script>
    <script src="{{ asset('assets/scripts/jquery-ui.min.js') }}"></script>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.js"></script>

    <script type="text/javascript">
        const paymentTotal = document.getElementById("paymentTotal");

        paymentTotal.addEventListener("click", () => {
            axios
                .get('/admin/payment-total', {
                    params: {
                        year: document.getElementById('year')?.value,
                        batch_id: document.getElementById('batch_id')?.value,
                        session_id: document.getElementById('session_id')?.value,
                        start_date: document.getElementById('from')?.value,
                        end_date: document.getElementById('to')?.value,
                    }
                })
                .then((response) => {
                    paymentTotal.innerHTML = `Toal : <b>${response.data}</b> TK`;

                    setTimeout(() => {
                        paymentTotal.innerHTML = "Total Amount";
                    }, 10000);
                });
        });



        // DO NOT REMOVE : GLOBAL FUNCTIONS!
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            var table = $('.datatable').DataTable({
                responsive: true,
                processing: true,
                serverSide: true,
                ajax: {
                    url: "/admin/payment-list",
                    type: 'GET',
                    data: function (d) {
                        d.year = $('#year').val();
                        d.session_id = $('#session_id').val();
                        d.batch_id = $('#batch_id').val();
                        d.start_date = $('#from').val();
                        d.end_date = $('#to').val();

                        console.log(d.year)
                    }
                },
                "pageLength": 25,
                columns: [
                    {data: 'id',name:'d1.id'},
                    {data: 'created_at',name:'d1.created_at'},
                    {data: 'doctor_name',name:'d3.name'},
                    {data: 'batch_name',name:'d4.name'},
                    {data: 'reg_no',name:'d2.reg_no'},
                    {data: 'course_name',name:'d10.name'},
                    {data: 'faculty', name: 'd6.name'},
                    {data: 'subject', name: 'd7.name'},
                    {data: 'bcsp_name', name: 'bs.name'},
                    {data: 'mobile_number',name:'d3.mobile_number'},
                    {data: 'trans_id',name:'d1.trans_id'},
                    {data: 'amount',name:'d1.amount'},
                    {data: 'payment_status',name:'d2.payment_status'},
                    {data: 'user_name',name:'d8.name'},
                    
                    @can('Payment List')
                        {data: 'payment_verification' , render: function( data, type, row ) {
                            return '' +
                                '<div class="payment_verification" style="display: flex">'+
                                '<button class="btn btn-sm '
                                        + ( String(data).toLowerCase() == 'yes' ? 'btn-success':'' )
                                        + '" style="margin-right: 8px" data-value="Yes" data-id="'+row.id+'">Yes</button>' +
                                '<button class="btn btn-sm '
                                        + ( String(data).toLowerCase() == 'no' ? 'btn-danger':'' )
                                        + '"  data-value="No" data-id="'+row.id+'">No</button>' +

                                '</div>';
                        },searchable: false, sortable:false },

                        {data: 'note_box' , render: function( data, type, row ) {
                            return '' +
                                '<div  style="display: flex">'+

                                '<button data-id="'+row.id+'" id="'+row.doctor_id+'" class="payment_note btn btn-sm btn-info ' + '" style="margin-right: 8px">View</button>' +

                                '</div>';
                        },searchable: false, sortable:false},
                    @endcan

                ]
            })

            table.on('draw', function ( ){

                $('.payment_verification .btn').on( 'click', function ( ){
                    const value = $( this ).data('value');
                    const id = $( this ).data('id');
                    
                    showAleart( note => {
                        $('[data-id="' + id + '"]').attr( 'disabled', true );
                        $.ajax({
                            type: "POST",
                            url: '/admin/payment-varification',
                            dataType: 'JSON',
                            data: { value, id, note },
                            success: function( data ) {
                                if( data.changed ) {
                                    table.draw( 'page' );
                                }else {
                                    $('[data-id="' + id + '"]').attr( 'disabled', false );
                                }
                            }
                        });

                    })

                });

            });


            function showAleart(callback){

                $.confirm({
                    title: 'Type a note!',
                    content: '' +
                    '<form action="" class="formName">' +
                    '<div class="form-group">' +
                    '<input type="text" class="payment-note form-control" required />' +
                    '</div>' +
                    '</form>',
                    buttons: {
                        formSubmit: {
                            text: 'Submit',
                            btnClass: 'btn-blue',
                            action: function () {
                                var note = this.$content.find('.payment-note').val();
                                if(!note){
                                    $.alert('Please type your note.');
                                    return false;
                                }

                                if(typeof callback  =='function'){
                                    callback(note)
                                }
                            }
                        },
                        cancel: function () {
                            //close
                        },
                    },
                    onContentReady: function () {
                        // bind to events
                        var jc = this;
                        this.$content.find('form').on('submit', function (e) {
                            // if the user submits the form by pressing enter in the field.
                            e.preventDefault();
                            jc.$$formSubmit.trigger('click'); // reference the button and click it
                        });
                    }
                });
            
            }

            $("body").on("change", "[id='session_id']", function(){
                var session_id = $(this).val();
                var course_id = $('#course_id').val();
                var year =$('#year').val();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: '/admin/batch-search-payment',
                    dataType: 'HTML',
                    data: {session_id: session_id, year: year, course_id:course_id},
                    success: function( data ) {
                        $('.batches').html(data);
                        $('#batch_id').select2();
                    }
                });
            });

            // $("body").on("change", "[id='year']", function(){
            //     var year =$(this).val();
            //     $.ajax({
            //         headers: {
            //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            //         },
            //         type: "POST",
            //         url: '/admin/session-search-payment',
            //         dataType: 'HTML',
            //         data: {year: year},
            //         success: function( data ) {
            //             $('.sessions').html(data);
            //         }
            //     });
            // });

            $('#btnFiterSubmitSearch').click(function(){
                $('.datatable').DataTable().draw(true);
            });

            $('#excel-download').click(function(){
                var year = $('[name="year"]').val();
                var session = $('[name="session_id"]').val();
                var batch = $('[name="batch_id"]').val();
                var paras = year+'_'+session+'_'+batch;
                if(year && session && batch){
                    window.location.href = "/admin/payment-excel/"+paras;
                }else {
                     alert('Please select Year, Session and Batch');
                }
            });

            $('.batch2').select2();
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

            $("body").on( "click", ".payment_note", function() {
                var payment_id = $(this).data('id');
                $('.modal-body').load('/admin/payment-note',{payment_id: payment_id,_token: '{{csrf_token()}}'},function(){
                    $('#payment_note').modal({show:true});
                    $('#payment_note').modal('');

                });
            });

            $("body").on( "click", ".payment", function() {
                var doctor_course_id = $(this).attr('id');
                $('.modal-body').load('/admin/doctors-courses-payemnt-details',{doctor_course_id: doctor_course_id,_token: '{{csrf_token()}}'},function(){
                    $('#pament').modal({show:true});

                });
            });

    </script>

@endsection