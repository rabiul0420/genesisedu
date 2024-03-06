@extends('admin.layouts.app')

@section('content')

    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <i class="fa fa-home"></i><a href="{{ url('/') }}">Home</a><i class="fa fa-angle-right"></i>
            </li>
            <li>Discount Request Number List</li>
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
                        <i class="fa fa-globe"></i>Discount Request Number List
                        <a href="{{ action('Admin\DiscountRequestNumberController@create') }}"> <i class="fa fa-plus"></i> </a>
                    </div>
                </div>
                <div class="portlet-body">
                    <table class="table table-striped table-bordered table-hover datatable">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Number</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                            @foreach ($discount_request_numbers as $discount_request_number)
                                <tr>
                                    <td>{{ $discount_request_number->id ?? ' ' }}</td>
                                    <td>{{ $discount_request_number->name ?? ' ' }}</td>
                                    <td>{{ $discount_request_number->mobile_number ?? ' ' }}</td>
                                    <td>{{ $discount_request_number->status == 1 ? 'Active' : 'Inactive'?? ' ' }}</td>
                                    <td>
                                        <a href="{{ url('admin/discount-request-number/'.$discount_request_number->id.'/edit') }}"   class="btn btn-xs btn-primary">Edit</a>
                                        
                                        {!! Form::open(array('route' => array('discount-request-number.destroy', $discount_request_number->id), 'method' => 'delete','style' => 'display:inline')) !!}
                                        <button onclick="return confirm('Are You Sure ?')" class='btn btn-xs btn-danger' type="submit">Delete</button>
                                        {!! Form::close() !!}
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

    <script type="text/javascript">
        // DO NOT REMOVE : GLOBAL FUNCTIONS!
        $(document).ready(function() {
            $('.datatable').DataTable({
                responsive: true,
            })
        })

    </script>

@endsection
