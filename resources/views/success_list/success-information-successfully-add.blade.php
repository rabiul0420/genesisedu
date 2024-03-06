@extends('layouts.app')

@section('content')
<div class="container">

    <div class="container">

        <div class="row">

            <div class="col-md-9 col-md-offset-0">

            @if(Session::has('message'))
                <div  style="margin-top: 25px;" class="alert {{ (Session::get('class'))?Session::get('class'):'alert-success' }}" role="alert">
                    <p> {{ Session::get('message') }}</p>
                </div>
            @endif
            <!-- BEGIN EXAMPLE TABLE PORTLET-->


            </div>
        </div>
    </div>
    <!-- END PAGE CONTENT-->




@endsection

@section('js')



@endsection
