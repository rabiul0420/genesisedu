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
                    {!! Form::open(['url'=>['struggling-history-submit'],'method'=>'post','files'=>true,'class'=>'form-horizontal']) !!}
                    <div class="form-body">

                        <div class="panel panel-default pt-2">
                            <div class="panel_box w-100 bg-white rounded shadow-sm">
                                <div class="header text-center py-3">
                                    <h2 class="h2 brand_color">{{ 'Struggling History' }}</h2>
                                </div>
                            </div>
                            <div class="panel-body mt-3 rounded shadow-sm border bg-white ">
                                <div class="offset-md-1 py-4">


                                    <div class="my-4">
                                        <div class="form-group">
                                            <label style="background: #f5f5f0;padding: 9px 5px;margin-bottom: 10px;" class="col-md-4 control-label">Share your struggling history with us</label>
                                            <div class="col-md-8">
                                                <div class="input-icon right">
                                                    <textarea name="struggling_history" style="overflow:auto" id="struggling_history" placeholder="Up to 10 thousand words" class="form-control" cols="6" rows="4"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div class="form-actions my-1" style="float: right">
                                <div class="form-group offset-md-1">
                                    <label class="col-md-3 control-label"></label>
                                    <div class="col-md-3" style="display: flex">
                                        <button id="submit" style=" margin-right: 3px;" type="submit" class="btn btn-info" >Next</button>
                                        <a style="background-color:#F1C40F;" href="{{ url('effective-service') }}" class="btn btn-md">Skip</a>
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

    document.getElementById("struggling_history").addEventListener("keypress", function(evt){
        var words = this.value.split(/\s+/);
        var numWords = words.length;
        var maxWords = 10000;
    
        if(numWords > maxWords){
            evt.preventDefault();
        }
    })

    </script>


@endsection
