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
            Manual Payment Status Create
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
        <!-- BEGIN EXAMPLE TABLE PORTLET-->
        <div class="portlet">
            <div class="portlet-body">
                <!-- BEGIN FORM-->
                {!!
                Form::open(['action'=>'Admin\SiteSetupController@store','files'=>true,'class'=>'form-horizontal'])
                !!}

                    <div class="form-group">
                        <label class="col-md-2 control-label">Course Type
                        (<i class="fa fa-asterisk ipd-star"style="font-size:9px;"></i>) </label>
                        <div class="col-md-3">
                        <div class="input-icon right">
                            <select name="course_id" id="course_id" class="form-control"> 
                                <option value="">Select courses</option>
                                @foreach ($courses as $key=>$value )
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                         
                        </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label">Bkash Number
                        (<i class="fa fa-asterisk ipd-star"style="font-size:9px;"></i>) </label>
                        <div class="col-md-3">
                        <div class="input-icon right">
                            <input type="number" id="bkash_number" name="bkash_number" value="{{ old('bkash_number')?old('bkash_number'):'' }}" class="form-control">
                        </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-2 control-label">Select Status (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                        <div class="col-md-3">
                            {!! Form::select('value', ['YES' => 'Active','NO' => 'InActive'], old('value'),['class'=>'form-control','required'=>'required']) !!}<i></i>
                        </div>
                    </div>
                </div>


                <div class="form-actions">
                    <div class="row">
                        <div class="col-md-offset-1 col-md-9">
                            <button type="submit" class="btn btn-info">Submit</button>
                            <a href="{{ url('admin/payment_status') }}" class="btn btn-default">Cancel</a>
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







