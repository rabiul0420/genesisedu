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
                    <table class="table table-striped table-bordered table-hover datatable">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Doctor Name</th>
                            <th>BMDC No</th>
                            <th>Discount For</th> 
                            <th>Batch Name</th> 
                            <th>Amount</th>
                            <th>Discount Code</th>
                            <th>Code Duration <small>(Hour)</small></th> 
                            <th>Used</th>
                            <th>Reference</th>
                            <th>Created By</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        {{-- <tbody>

                        @foreach ($discounts as $discount)
                            <tr>  
                                <td>{{ $discount->id ?? ''}}</td>
                                <td>{{ $discount->name ?? ''}}</td>
                                <td>{{ $discount->bmdc_no ?? ''}}</td>
                                <td>{{ $discount->phone ?? ''}}</td>
                                <td>{{ $discount->batch_name ?? ''}}</td>
                                <td class='{{ (strtotime("now") - strtotime($discount->created_at ) < ($discount->code_duration * 3600)) ? 'bg-warning' : 'bg-danger' }} {{ $discount->used == 1 ? 'bg-success' :'' }}'>{{$discount->discount_code}}</td>
                                <td>{{ $discount->amount }}</td>
                                <td>{{ $discount->code_duration}}</td> 
                                <td>{{ ($discount->used == 0)? 'No':'Yes' }}</td>
                                <td>{{ $discount->reference ?? ''}}</td>
                                <td>{{ $discount->user_name}}</td>
                                
                                <td>{{($discount->status == 1)? 'Active':'Inactive'}}</td>
                                
                                <td>
                                    <a href="{{ url('admin/discount/'.$discount->id.'/edit') }}" class="btn btn-xs btn-primary">Edit</a>

                                    

                                    <button type="button" class="btn btn-xs btn-primary edit_history_btn" data-toggle="modal" data-target="#edit_history_{{ $discount->id }}" data-id="{{ $discount->id }}">Edit History</button>
                                </td>
                            </tr>
                        @endforeach
                        </tbody> --}}
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('js')

    <script type="text/javascript">
        // DO NOT REMOVE : GLOBAL FUNCTIONS!

        $("body").on( "click", ".edit_history_btn", function() {
                var discount_id = $(this).data('id');
                $('.modal-body').load('/admin/discount-edit-history',{discount_id: discount_id,_token: '{{csrf_token()}}'},function(){
                    $('#edit_history').modal({show:true});

                });
            });



            var table = $('.datatable').DataTable({
                order : [[0, 'DESC']],
                responsive: true,
                processing: true,
                serverSide: true,
                ajax: {
                    url: "/admin/discount-list",
                    type: 'GET',
                },
                "pageLength": 25,
                columns: [
                    {data: 'id',name:'discounts.id'},
                    {data: 'name',name:'discounts.name'},
                    {data: 'bmdc_no',name:'discounts.bmdc_no'},
                    {data: 'phone',name:'discounts.phone'},
                    {data: 'batch_name',name:'discounts.batch_name'},
                    {data: 'amount',name:'discounts.amount'},
                    {data: 'discount_code',name:'discounts.bmdc_no'},
                    {data: 'code_duration',name:'discounts.code_duration'},
                    {data: 'used',name:'discounts.used'},
                    {data: 'reference',name:'discounts.reference'},
                    {data: 'user_name',name:'discounts.user_name'},
                    {data: 'status',name:'discounts.status'},
                    {data: 'action',searchable: false},
                   
                ]
            })



        // var myModal = new bootstrap.Modal(document.getElementById('exampleModal'))
        // myModal.show()

    </script>

@endsection
