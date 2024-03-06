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
            <div class="portlet">
                <div class="portlet-title">
                    <div class="caption">
                        <!-- <i class="fa fa-reorder"></i>Doctor Course Create -->
                    </div>
                </div>
                <div class="portlet-body form">
                    <!-- BEGIN FORM-->
                    {!! Form::open(['url'=>['effective-service-submit'],'method'=>'post','files'=>true,'class'=>'form-horizontal','enctype'=>'multipart/form-data']) !!}
                    <div class="form-body">

                        <div class="panel panel-default pt-2">
                            <div class="panel_box w-100 bg-white rounded shadow-sm">
                                <div class="header text-center py-3">
                                    <h2 class="h2 brand_color"></h2>
                                </div>
                            </div>
                            <div class="panel-body mt-3 rounded shadow-sm border bg-white ">
                                <div class="offset-md-1 py-4">


                                    <div class="my-4">
                                        <div class="form-group">
                                            <label style="background: #f5f5f0;padding: 9px 5px;margin-bottom: 10px;" class="col-md-6 control-label">Most effective service of GENESIS for you</label>
                                            <div class="col-md-6">
                                                <div class="input-icon right">
                                                    <textarea name="effective_service" id="effective_service" style="overflow: auto" placeholder="Up to 25 words " class="form-control" cols="50" rows="5"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="my-4">
                                        <div class="form-group">
                                            <label style="background: #f5f5f0;padding: 9px 5px;margin-bottom: 10px;" class="col-md-6 control-label">Which service need to improve</label>
                                            <div class="col-md-6">
                                                <div class="input-icon right">
                                                    <textarea name="service_improve" id="service_improve" style="overflow: auto" placeholder="Up to 25 words " class="form-control" cols="50" rows="5"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="my-1">
                                        <div class="form-group">
                                            <label style="background: #f5f5f0;padding: 9px 5px;margin-bottom: 10px;" class="col-md-6 control-label">Your overall impression out of 100</label>
                                            <div class="col-md-6">
                                                <select class="form-select" name="overall_value">
                                                    <option value="" selected>---Select---</option>
                                                    <option value="0">0</option>
                                                    <option value="10">10</option>
                                                    <option value="20">20</option>
                                                    <option value="30">30</option>
                                                    <option value="40">40</option>
                                                    <option value="50">50</option>
                                                    <option value="60">60</option>
                                                    <option value="70">70</option>
                                                    <option value="80">80</option>
                                                    <option value="90">90</option>
                                                    <option value="100">100</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="my-4">
                                        <div class="form-group">
                                            <label style="background: #f5f5f0;padding: 9px 5px;margin-bottom: 10px;" class="col-md-6 control-label">Add Your Image</label>
                                            <div class="col-md-6">
                                                <div class="input-icon right">
                                                    <input type="file" class="form-control" name="image">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div class="form-actions my-1" style="float: right">
                                <div class="form-group offset-md-1">
                                    <label class="col-md-3 control-label"></label>
                                    <div class="col-md-3">
                                        <button id="submit" type="submit" class="btn btn-info" >Submit</button>
                                        {{-- <a href="{{ url('my-profile') }}" class="btn btn-outline-info btn-default">Cancel</a> --}}
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
    </div>
    <!-- END PAGE CONTENT-->




@endsection

@section('js')



    <script type="text/javascript">
        document.getElementById("effective_service").addEventListener("keypress", function(evt){   
        var words = this.value.split(/\s+/);
        var numWords = words.length;
        var maxWords = 25; 
        
        if(numWords > maxWords){
            evt.preventDefault();
        }
        });

        document.getElementById("service_improve").addEventListener("keypress", function(evt){
        var words = this.value.split(/\s+/);
        var numWords = words.length;
        var maxWords = 25; 
        
        if(numWords > maxWords){
            evt.preventDefault();
        }
        });
    </script>


@endsection
