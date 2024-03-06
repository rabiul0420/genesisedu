@extends('admin.layouts.app')

@section('content')


    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <i class="fa fa-home"></i>
                <a href="{{ url('/') }}">Home</a>
                <i class="fa fa-angle-right"></i>
            </li>
            <li> <i class="fa fa-angle-right"> </i> <a href="#">Sms</a></li>
        </ul>

    </div>

    @if(Session::has('message'))
        <div class="alert {{ (Session::get('class'))?Session::get('class'):'alert-success' }}" role="alert">
            <p> {{ Session::get('message') }}</p>
        </div>
    @endif

    <div class="row">
        <div class="col-md-12">
            <!-- BEGIN EXAMPLE TABLE PORTLET-->
            <div class="portlet">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-reorder"></i>Sms Send Status
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="row" style="padding:20px;">
                        <h3><b>SMS Send status</b></h3>
                        <table class="table table-striped table-bordered table-hover">
                        <tr><th>Mobile Number</th><th>Send Status</th></tr>
                        @foreach($sms_send_statuses as $sms_send_status)
                            @if(strpos($sms_send_status['status'],'Ok:') !== false)
                            <tr style="color:white;background-color:green;" ><td>{{ $sms_send_status['mobile_number'] }}</td><td>{{ $sms_send_status['status'] }}</td></tr>
                            @else
                            <tr style="color:white;background-color:red;" ><td>{{ $sms_send_status['mobile_number'] }}</td><td>{{ $sms_send_status['status'] }}</td></tr>
                            @endif                        
                        @endforeach
                        </table>


                    </div>
                </div>
            </div>
            <!-- END EXAMPLE TABLE PORTLET-->



        </div>
    </div>
    <!-- END PAGE CONTENT-->


@endsection

