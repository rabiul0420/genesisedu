@extends('admin.layouts.app')

@section('content')

    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <i class="fa fa-home"></i><a href="{{ url('/') }}">Home</a><i class="fa fa-angle-right"></i>
            </li>
            <li>Discount Request List</li>
        </ul>
    </div>

    @if(Session::has('message'))
        <div class="alert {{ Session::get('class') ?? 'alert-success' }} " role="alert">
            <p> {{ Session::get('message') }}</p>
        </div>
    @endif


    <div class="row">
        <div class="col-md-12">
                        <!-- BEGIN EXAMPLE TABLE PORTLET-->
            <div class="portlet">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-globe"></i>Discount Request List
                        <a href="{{ action('Admin\DiscountRequestController@create') }}"> <i class="fa fa-plus"></i> </a>
                    </div>
                </div>
                <div class="portlet-body">
                    <table class="table table-striped table-bordered table-hover datatable">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>BMDC No</th>
                            <th>Mobile No</th> 
                            <th>Previous Batch</th>
                            <th>Batch Reg No</th>
                            <th>Requested Batch</th>
                            <th>Requested Time</th>
                            <th>Doctor Requested Note</th>
                            <th>Response Note</th> 
                            <th>Response</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>

                        @foreach ($discount_requests as $discount_request)
                            <tr>  
                                <td>{{ $discount_request->id ?? ''}}</td>
                                <td>{{ $discount_request->doctor->name ?? ''}}</td>
                                <td>{{ $discount_request->doctor->bmdc_no ?? ''}}</td>
                                <td>{{ $discount_request->doctor->mobile_number ?? ''}}</td>
                                <td>{{ $discount_request->previous_batch_name ?? ''}}</td>
                                <td>{{ $discount_request->previous_reg_no ?? '' }}</td>
                                <td>{{ $discount_request->doctor_course->batch->name ??'' }}</td>
                                <td>{{ $discount_request->created_at ??'' }}</td>
                                <td>{{ $discount_request->note ?? ' ' }}</td>
                                <td>{{ $discount_request->admin_note ?? ' ' }}</td>
                                <td ><a type="button" class="btn btn-sm btn-primary discount_request" 
                                    data-value ={{ $discount_request->status }} {{ $discount_request->status == 1 ? 'disabled' : '' }}
                                    data-id ={{ $discount_request->id }}>{{ $discount_request->status == 1 ? 'Yes' : 'No' }}</a>
                                </td>
                                <td>
                                    <a href="{{ url('admin/get-discount/'. $discount_request->doctor->id ) }}" class="btn btn-sm btn-success get_discount" >
                                        Provide Discount
                                    </a>
                                    </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('js')
    {{-- <script src="{{ asset('assets/scripts/jquery-ui.min.js') }}"></script> --}}
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

            var table = $('.datatable').DataTable({
                responsive: true,
                processing: true,
            })

                $('body').on( 'click','.discount_request', function ( ){
                        var id = $( this ).data('id');
                        var value = $( this ).data('value');
                        showAleart( note => {
                            $('[data-id="' + id + '"]').attr( 'disabled', true );
                            $.ajax({
                                type: "POST",
                                url: '/admin/discount-request-feedback',
                                dataType: 'JSON',
                                data: {value, id , note },
                                success: ( data ) =>{
                                    if( data.changed ) {
                                        $( this ).html(data.discount.status == 1 ? 'Yes' : 'No')
                                    }else {
                                        $('[data-id="' + id + '"]').attr( 'disabled', false );
                                    }
                                }
                            });

                        })

                });

            function showAleart(callback){

                $.confirm({
                    title: 'Type a note!',
                    content: '' + '<form action="" class="formName">' + 
                    '<div class="form-group">' +
                    '<input type="text" class="discount-note form-control" required />' +
                    '</div>' +
                    '</form>',
                    buttons: {
                        formSubmit: {
                            text: 'Submit',
                            btnClass: 'btn-blue',
                            action: function () {
                                var note = this.$content.find('.discount-note').val();
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
        })

    </script>

@endsection
