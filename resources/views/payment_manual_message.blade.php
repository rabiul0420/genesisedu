@extends('layouts.app')


@section('css')
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">

    <style>
        .class-details {
            color: gray;
            border: none;
            background: none;
            outline: none;
        }

        .class-details:hover {
            color: green;
        }

    </style>

@endsection
@section('content')

    <div class="container">
        <div class="row">
            @include('side_bar')
            <div class="col-md-9 col-md-offset-0">

                <div class="row panel panel-default">
                <div class="panel-body mt-3 rounded shadow-sm border bg-white ">

                    <div class="offset-md-1 py-4">
                        <div class="my-1">                                    
                        Thank you for your payment. We will confirm your payment within 3 hours with confirmation SMS.

                            
                        </div>
                    </div>

                    <div class="py-4">
                        <div style="text-align:center;">                                    
                        <a class="btn btn-sm btn-primary" href="{{ url('my-courses') }}">My Courses</a>
                        <a class="btn btn-sm btn-info" href="{{ url('payment-details') }}">Pay Course Fee</a>                            
                        </div>
                    </div>
                                        
                </div>
            </div>
        </div>    
    </div>

@endsection

@section('js')
 
@endsection
