@extends('layouts.app')
@section('content')

<div class="container">

    <div class="row">

        @include('side_bar')

        <div class="col-md-9 col-md-offset-0">

        <!-- BEGIN EXAMPLE TABLE PORTLET-->
        <div class="portlet">
            <div class="portlet-title">
                <div class="caption">
                    <!-- <i class="fa fa-reorder"></i>Doctor Course Create -->
                </div>
            </div>
            <div class="portlet-body form">
                
                <!-- BEGIN FORM-->
                {!! Form::open(['url'=>['/apply-promo-code'],'method'=>'post','class'=>'form-horizontal']) !!}
                {{ csrf_field() }}   

                <div class="form-body">

                    <div class="panel panel-default pt-2">
                        <div class="panel_box w-100 bg-white rounded shadow-sm">
                            <div class="header text-center py-3">
                                <h2 class="h2 brand_color">{{ 'Promo Code' }}</h2>
                            </div>
                            @if(Session::has('status'))
                                <div class="alert {{ (Session::get('class'))?Session::get('class'):'alert-success' }}" role="alert">
                                    <p> {{ Session::get('status') }}</p>
                                </div>
                            @endif
                        </div>
                        
                        <div class="panel-body mt-3 rounded shadow-sm border bg-white ">
                            <div class="offset-md-1 py-4">
                                <div class="my-1">
                                    <div class="form-group">
                                        <label class="col-md-3 control-label my-2">Doctor (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                                        <div class="col-md-6">
                                            <div class="input-icon right">
                                                <input type="hidden" name="doctor_course_id" value="{{ $doctor_course->id }}" >
                                                <span style="font-size:15px;font-weight:700;">{{ $doctor_course->doctor->name.'-'.$doctor_course->reg_no }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-md-3 control-label my-2">Course (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                                        <div class="col-md-6">
                                            <div class="input-icon right">
                                                <span style="font-size:15px;font-weight:700;">{{ $doctor_course->course->name }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-md-3 control-label my-2">Batch (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                                        <div class="col-md-6">
                                            <div class="input-icon right">
                                                <span style="font-size:15px;font-weight:700;">{{ $doctor_course->batch->name }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-md-3 control-label my-2">Promo Code  (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                                        <div class="col-md-6">
                                            <div class="input-icon right">
                                                <input type="text" name="discount_code" class="form-control" placeholder="Enter Promo code..." value="" required>
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
                                        @if($doctor_course->batch->payment_times > 1 && $doctor_course->payment_option != "single")                                        
                                            <a class="btn btn-primary" href="{{ url('installment-payment/'.$doctor_course->id) }}">Cancel</a>                                        
                                        @else
                                            <a class="btn btn-primary" href="{{ url('payment/'.$doctor_course->id) }}">Cancel</a>
                                        @endif
                                        
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