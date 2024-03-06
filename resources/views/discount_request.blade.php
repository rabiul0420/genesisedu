@extends('layouts.app')
@section('content')

<div class="container">

    <div class="row">

        @include('side_bar')

        <div class="col-md-9 col-md-offset-0">
        
        @if(Session::has('message'))
            <div class="alert {{ (Session::get('class'))?Session::get('class'):'alert-success' }}" role="alert">
                <p> {{ Session::get('message') }}</p>
            </div>
        @endif
        <!-- BEGIN EXAMPLE TABLE PORTLET-->
        <div class="portlet">
            <div class="portlet-title">
                <div class="caption">
                    <!-- <i class="fa fa-reorder"></i>Doctor Course Create -->
                </div>
            </div>
            <div class="portlet-body form">
                <!-- BEGIN FORM-->
                {!! Form::open(['url'=>['discount-request-submit'],'method'=>'post','class'=>'form-horizontal']) !!}
                {{ csrf_field() }}   

                <div class="form-body">

                    <div class="panel panel-default pt-2">
                        <div class="panel_box w-100 bg-white rounded shadow-sm">
                            <div class="header text-center py-3">
                                <h2 class="h2 brand_color">{{ 'Discount Request' }}</h2>
                            </div>
                        </div>
                        <div class="panel-body mt-3 rounded shadow-sm border bg-white ">
                            <div class="offset-md-1 py-4">
                                <div class="my-1">
                                    <div class="form-group">
                                        <label class="col-md-3 control-label my-2">Previous Batch Name (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                                        <div class="col-md-3">
                                            <div class="input-icon right">
                                                <input type="text" name="previous_batch_name" class="form-control" >
                                                <input type="hidden" name="course_id" class="form-control" value="{{ $course_id }}">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-md-3 control-label my-2">Previous Reg No (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                                        <div class="col-md-3">
                                            <div class="input-icon right">
                                                <input type="text" name="previous_reg_no" class="form-control" >
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-md-3 control-label my-2">Your Note </label>
                                        <div class="col-md-3">
                                            <div class="input-icon right">
                                                <input type="text" name="note" class="form-control" placeholder="Write Something..." >
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-action">
                                <div class="form-group offset-md-1">
                                    <label class="col-md-3 control-label"></label>
                                    <div class="col-md-3">
                                        <button id="submit" type="submit" class="btn btn-info" >Submit</button>
                                        <a class="btn btn-primary" href="{{ url('payment/'.$course_id) }}">Cancel</a>
                                    </div>
                                    
                                </div>
                            </div>      
                                
                        </div>
                        
                    </div>

                </div>
               
            {!! Form::close() !!}
            <!-- END FORM-->
            </div>
        </div>
        <!-- END EXAMPLE TABLE PORTLET-->


        </div>
    </div>  

@endsection

@section('js')
<script src="https://unpkg.com/@popperjs/core@2"></script>
<script src="https://unpkg.com/tippy.js@6"></script>
<script src="script.js"></script>




@endsection