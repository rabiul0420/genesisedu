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
                    {!! Form::open(['url'=>['successfull-personal-detail-submit'],'method'=>'post','files'=>true,'class'=>'form-horizontal']) !!}
                    <div class="form-body">

                        <div class="panel panel-default pt-2">
                            <div class="panel_box w-100 bg-white rounded shadow-sm">
                                <div class="header text-center py-3">
                                    <h2 class="h2 brand_color">{{ 'Personal Details' }}</h2>
                                </div>
                            </div>
                            <div class="panel-body mt-3 rounded shadow-sm border bg-white ">
                                <div class="offset-md-1 py-4">
                                    <div class="institutes my-1">
                                        <div class="form-group">
                                            <label style="background: #f5f5f0;padding: 9px 5px;margin-bottom: 10px;" class="col-md-3 control-label">Name (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                                            <div class="col-md-6">
                                                <div class="input-icon right">
                                                    <input type="text" name="name" id="" class="form-control" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="my-1">
                                        <div class="form-group">
                                            <label style="background: #f5f5f0;padding: 9px 5px;margin-bottom: 10px;" class="col-md-3 control-label">BMDC No (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                                            <div class="col-md-6">
                                                <div class="input-icon right">
                                                    <div class="position-relative w-100 prefix">
                                                        <span class="position-absolute border-bottom text-dark border-info" style="padding: 9px 13px;left: 0px; background-color:#17a2b8">A</span>
                                                    </div>
                                                    <input style="padding: 0px 40px;" type="number"  name="bmdc_no" id="" class="form-control" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="my-1">
                                        <div class="form-group">
                                            <label style="background: #f5f5f0;padding: 9px 5px;margin-bottom: 10px;" class="col-md-3 control-label">Medical College</label>
                                            <div class="col-md-6">
                                                <select class="form-select select2" required name="medical_college_id" class="form-control">
                                                    <option value="" selected>Select Medical College</option>
                                                    @foreach ($medical_college as $key=>$value)
                                                        <option value="{{ $key }}">{{ $value }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="my-1">
                                        <div class="form-group">
                                            <label style="background: #f5f5f0;padding: 9px 5px;margin-bottom: 10px;" class="col-md-3 control-label">Mobile Number(<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                                            <div class="col-md-6">
                                                <div class="input-icon right">
                                                    <div class="position-relative w-100 prefix">
                                                        <span class="position-absolute border-bottom text-dark border-info bg-warning" style="padding: 9px 13px;left: 0px;">+88</span>
                                                    </div>
                                                    <input type="number" style="padding: 5px 0px 0px 60px;" name="mobile_number" maxlength="11" id="sent"  oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"  id="" class="form-control" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="my-1">
                                        <div class="form-group">
                                            <label style="background: #f5f5f0;padding: 9px 5px;margin-bottom: 10px;" class="col-md-3 control-label">FCPS Part-1 Discipline</label>
                                            <div class="col-md-6">
                                                <select class="form-select form-control discipline2" name="discipline">
                                                    <option value="1">Select FCPS Part-1 Discipline</option>
                                                    @foreach ($subjects as $key=>$value)
                                                        <option value="{{ $key }}">{{ $value }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="my-1">
                                        <div class="form-group">
                                            <label style="background: #f5f5f0;padding: 9px 5px;margin-bottom: 10px;" class="col-md-3 control-label">Address</label>
                                            <div class="col-md-6">
                                                <div class="input-icon right">
                                                    <textarea name="address" id="" class="form-control" cols="50" rows="5"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                    <div class="my-1">
                                        <div class="form-group">
                                            <label class="col-md-3 control-label">Session</label>
                                            <div class="col-md-6">
                                                <div class="input-icon right">
                                                    <input type="text" id="" value="July-2021" class="form-control" readonly>
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
                                        <button id="submit" type="submit" class="btn btn-info" >Next</button>
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

<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/css/select2.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/js/select2.js" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function(){
            $('.select2').select2({});
            $('.discipline2').select2({});
        })
    </script>


@endsection
