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
                    {!! Form::open(['url'=>['genesis-batch-details-submit'],'method'=>'post','files'=>true,'class'=>'form-horizontal']) !!}
                    <div class="form-body">

                        <div class="panel panel-default pt-2">
                            <div class="panel_box w-100 bg-white rounded shadow-sm">
                                <div class="header text-center py-3">
                                    <h2 class="h2 brand_color">{{ 'Genesis Batch Details' }}</h2>
                                </div>
                            </div>
                            <div class="panel-body mt-3 rounded shadow-sm border bg-white ">
                                <div class="offset-md-1 py-4">
                                    <div class="institutes my-1">
                                        <div class="form-group">
                                            <label  style="background: #f5f5f0;padding: 9px 5px;margin-bottom: 10px;" class="col-md-3 control-label">Batch Name (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                                            <div class="col-md-6">
                                                <div class="input-icon right">
                                                    <input type="text" name="batch_name" id="" class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="my-1">
                                        <div class="form-group">
                                            <label style="background: #f5f5f0;padding: 9px 5px;margin-bottom: 10px;" class="col-md-3 control-label">Year</label>
                                            <div class="col-md-6">
                                                <select class="form-select form-control" name="year">
                                                    <option value="">---Select Year---</option>
                                                    <option value="2021">2021</option>
                                                    <option value="2020">2020</option>
                                                    <option value="2019">2019</option>
                                                    <option value="2018">2018</option>
                                                    <option value="2017">2017</option>
                                                    <option value="2016">2016</option>
                                                    <option value="2015">2015</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="my-1">
                                        <div class="form-group">
                                            <label style="background: #f5f5f0;padding: 9px 5px;margin-bottom: 10px;" class="col-md-3 control-label">Coaching Session</label>
                                            <div class="col-md-6">
                                                <select class="form-select" name="session">
                                                    <option value="" selected>---Select---</option>
                                                    <option value="January">January(Jan-July)</option>
                                                    <option value="July">July(July-Jan)</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div class="form-actions my-1" style="float: right">
                                <div class="form-group offset-md-1">
                                    <label  class="col-md-3 control-label"></label>
                                    <div class="col-md-3 d-flex" >
                                        <button style="margin-right: 3px"  style="margin-right: 3px;" id="submit" type="submit" class="btn btn-info" >Next</button>
                                        <a style="background-color:#F1C40F;" href="{{ url('feedback-about-genesis') }}" class="btn btn-md">Skip</a>
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

    </script>


@endsection
