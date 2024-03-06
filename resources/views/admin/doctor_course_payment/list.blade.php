@extends('admin.layouts.app')

@section('content')

    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <i class="fa fa-home"></i><a href="{{ url('/') }}">Home</a><i class="fa fa-angle-right"></i>
            </li>
            <li>Doctors List</li>
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
                        <i class="fa fa-globe"></i>Doctors List
                        @can('Doctor Course Payment Add')
                            <a href="{{url('admin/doctor-course-payment/create')}}"> <i class="fa fa-plus"></i> </a>
                        @endcan
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="row">
                        <div class="form-group col-md-6">
                            @can('Manual Payment Total Amount')
                                <h4><b>Total Manual Payment : {{ $total_manual_payment ?? '0'}} BDT</b></h4>
                            @endcan                            
                        </div>
                    </div>
                    <table class="table table-striped table-bordered table-hover userstable datatable">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tranx ID</th>
                            <th>Amount</th>
                            <th>Payment Serial</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        @foreach($doctor_course->payments as $payment)
                            <tr>
                                <td>{{ $payment->id }}</td>
                                <td>{{ $payment->trans_id }}</td>
                                <td>{{ $payment->amount }}</td>
                                <td>{{ $payment->payment_serial }}</td>
                                <td>
                                    <a></a>
                                </td>
                            </tr>
                        @endforeach

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

        })
    </script>

@endsection