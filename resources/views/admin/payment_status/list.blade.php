@extends('admin.layouts.app')

@section('content')

    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <i class="fa fa-home"></i><a href="{{ url('/') }}">Home</a><i class="fa fa-angle-right"></i>
            </li>
            <li>Manual Payment Number List</li>
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
                        <i class="fa fa-globe"></i>Manual Payment Number Create
                        <a href="{{ action('Admin\SiteSetupController@create') }}"> <i class="fa fa-plus"></i> </a>
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="row" style="margin: auto">
                    <table class="table table-striped table-bordered table-hover datatable">
                        <thead>
                        <tr>
                            <th>SL</th>
                            <th>Course Name</th>
                            <th>Bkash Number</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($setup_lists as $k => $payment_status)
                            <tr>
                                <td>{{ ++$k }}</td>
                                <td>{{ $payment_status->course->name ?? ''}}</td>
                                <td>{{ $payment_status->bkash_number ?? ''}}</td>
                                <td>{{ $payment_status->value ?? ''}}</td>
                                <td>
                                    @can('Payment Status Edit')
                                    <a href="{{ url('admin/payment-status/'.$payment_status->id) }}" class="btn btn-xs btn-primary">Edit</a>
                                    @endcan
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