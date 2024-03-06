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
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="row sc_search">
                        <div class="form-group col-md-2">
                            <h5>Year <span class="text-danger"></span></h5>
                            <div class="controls">
                                {!! Form::select('year',$years, '' ,['class'=>'form-control','required'=>'required','id'=>'year']) !!}<i></i>
                            </div>
                        </div>
                        <div class="form-group col-md-2">
                            <h5>Session <span class="text-danger"></span></h5>
                            <div class="controls">
                                @php  $sessions->prepend('Select Session', ''); @endphp
                                {!! Form::select('session_id',$sessions, '' ,['class'=>'form-control','required'=>'required','id'=>'session_id']) !!}<i></i>
                            </div>
                        </div>
                        <div class="form-group col-md-2">
                            <h5>Batch <span class="text-danger"></span></h5>
                            <div class="controls">
                                @php  $batches->prepend('Select Batch', ''); @endphp
                                {!! Form::select('batch_id',$batches, '' ,['class'=>'form-control batch2','required'=>'required','id'=>'batch_id']) !!}<i></i>
                            </div>
                        </div>
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
                        </div>


                    <div class="">
                        <div class="modal fade" id="payment_note" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="payment_note">Note</h5>
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
                    </div>

                    <table class="table table-striped table-bordered table-hover userstable datatable">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Date</th>
                            <th>Doctor Name</th>
                            <th>Batch</th>
                            <th>Reg No</th>
                            <th>Mobile Number</th>
                            <th>Trans ID</th>
                            
                            <th>Paid Amount</th>                       
                           
                          
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
        // DO NOT REMOVE : GLOBAL FUNCTIONS!
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $('.datatable').DataTable({
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
                    }
                },
                "pageLength": 25,
                columns: [
                    {data: 'id',name:'d1.id'},
                    {data: 'created_at',name:'d1.created_at'},
                    {data: 'doctor_name',name:'d3.name'},
                    {data: 'batch_name',name:'d4.name'},
                    {data: 'reg_no',name:'d2.reg_no'},
                    {data: 'mobile_number',name:'d3.mobile_number'},
                    {data: 'trans_id',name:'d1.trans_id'},
                    {data: 'amount',name:'d1.amount'},
                 
                   
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

            




            $('#btnFiterSubmitSearch').click(function(){
                $('.datatable').DataTable().draw(true);
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
                // alert(payment_id);
                $('.modal-body').load('/admin/payment-note',{payment_id: payment_id,_token: '{{csrf_token()}}'},function(){
                    $('#payment_note').modal({show:true});

                });
            });
    </script>

@endsection
