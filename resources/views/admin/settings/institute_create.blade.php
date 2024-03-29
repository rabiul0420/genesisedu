@extends('admin.layouts.app')

@section('content')


    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <i class="fa fa-home"></i>
                <a href="{{ url('/') }}">Home</a>
                <i class="fa fa-angle-right"></i>
            </li>
            <?php
            $urls='';
            foreach($breadcrumb as $key=>$value){ $urls .= $value.'/';
                echo ' <li> <i class="fa fa-angle-right"> </i> <a href="'.url('/').substr_replace($urls, "", -1).' "> '.$value.' </a></li> ';
            }
            ?>
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
                        <i class="fa fa-reorder"></i><?php echo $module_name;?> Create
                    </div>
                </div>
                <div class="portlet-body form">
                    <!-- BEGIN FORM-->
                    {!! Form::open(['action'=>'Admin\InstitutesController@store','files'=>true,'class'=>'form-horizontal']) !!}
                    <div class="form-body">

                                <div class="form-group">
                                    <label class="col-md-3 control-label">Name (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                                    <div class="col-md-3">
                                        <div class="input-icon right">
                                            <input type="text" name="name" required value="{{ old('name') }}" class="form-control">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-md-3 control-label">Has Faculty? (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                                    <div class="col-md-3" id="id_div_doctors_gender">
                                        <label class="radio-inline">
                                            <input type="radio" required name="type" value="1" {{  old('type') === "1" ? "checked" : '' }}> Yes
                                        </label>
                                        <label class="radio-inline">
                                            <input type="radio" required name="type" value="0" {{  old('type') === "0" ? "checked" : '' }}> No
                                        </label>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-md-3 control-label">Reference Code (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                                    <div class="col-md-3">
                                        <div class="input-icon right">
                                            <input type="text" name="reference_code" required value="{{ old('reference_code') }}" class="form-control">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-md-3 control-label">Select Status (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                                    <div class="col-md-3">
                                        {!! Form::select('status', ['1' => 'Active','0' => 'InActive'], old('status'),['class'=>'form-control']) !!}<i></i>
                                    </div>
                                </div>

                    </div>
                    <div class="form-actions">
                        <div class="row">
                            <div class="col-md-offset-3 col-md-9">
                                <button type="submit" class="btn btn-info"><?php echo $submit_value;?></button>
                                <a href="{{ url('admin/institutes') }}" class="btn btn-default">Cancel</a>
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
    <!-- END PAGE CONTENT-->
 

@endsection

@section('js')

    <script type="text/javascript">
        $(document).ready(function() {
           // $('.select2').select2();
        })
    </script>


@endsection