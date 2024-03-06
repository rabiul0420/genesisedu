@extends('admin.layouts.app')

@section('content')

    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <i class="fa fa-home"></i>
                <a href="{{ url('/') }}">Home</a>
                <i class="fa fa-angle-right"></i>
            </li>
            <li>
               Payment Status Edit
            </li>
        </ul>
    </div>

    @if(Session::has('message'))
        <div class="alert {{ (Session::get('class'))?Session::get('class'):'alert-success' }}" role="alert">
            <p> {{ Session::get('message') }}</p>
        </div>
    @endif

    <div class="row">
        <div class="col-md-12">
            <div class="portlet">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-reorder"></i>Payment Status Edit
                    </div>
                </div>
                <div class="portlet-body form">
                    <!-- BEGIN FORM-->
                    <br>
                        {!! Form::open(['action'=>['Admin\SiteSetupController@update',$site_setup->id],'method'=>'PUT','files'=>true,'class'=>'form-horizontal']) !!}


                            <div class="form-group">
                                <label class="col-md-2 control-label">Bkash Number
                                (<i class="fa fa-asterisk ipd-star"style="font-size:9px;"></i>) </label>
                                <div class="col-md-3">
                                <div class="input-icon right">
                                    <input type="number" id="bkash_number" name="bkash_number" value="{{ $site_setup->bkash_number ?? '' }}" class="form-control">
                                </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-2 control-label">Select Status (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                                <div class="col-md-3 mt-2">
                                    {!! Form::select('value', ['YES' => 'Active','NO' => 'InActive'], $site_setup->value ?? '',['class'=>'form-control','required'=>'required']) !!}<i></i>
                                </div>
                            </div>
                        <div class="form-actions">
                            <div class="row">
                                <div class="col-md-offset-1 col-md-9">
                                    <button type="submit" class="btn btn-info">Update</button>
                                    <a href="{{ url('admin/payment-status')}}" class="btn btn-default">Cancel</a>
                                </div>
                            </div>
                        </div>
                       {!! Form::close() !!}
                    <!-- END FORM-->
                </div>
            </div>

        </div>
    </div>



@endsection
