@extends('admin.layouts.app')

@section('content')

    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <i class="fa fa-home"></i><a href="{{ url('/admin') }}">Home</a><i class="fa fa-angle-right"></i>
            </li>
            <li>branch edit</li>
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
                        <i class="fa fa-reorder"></i>
                    </div>
                </div>
                <div class="portlet-body form">
                    <!-- BEGIN FORM-->
                    {!! Form::open(['action'=>['Admin\BranchesController@update',$branches->id],'method'=>'PUT','files'=>true,'class'=>'form-horizontal']) !!}

                    
                    <div class="form-body">
                        <div class="form-group">
                            <label class="col-md-1 control-label">Name (<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>) </label>
                            <div class="col-md-11">
                                <div class="input-icon right">
                                    <div class="col-md-3">
                                    <input type="text" name="name" required value="{{ $branches->name }}" class="form-control">
                                    </div> 
                                </div>
                            </div>
                        </div>
                    <div class="form-body">
                        <div class="form-group">
                            <label class="col-md-1 control-label">Select Status (<i class="fa fa-asterisk ipd-star"style="font-size:9px;"></i>)</label>
                            <div class="col-md-3">
                                {!! Form::select('status', ['1' => 'Active','0' => 'InActive'], $branches->status,['class'=>'form-control','required'=>'required']) !!}<i></i>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="form-actions">
                    <div class="row">
                        <div class="col-md-offset-1 col-md-9">
                            <button type="submit" class="btn btn-info">Submit</button>
                            <button type="Cancel" class="btn btn-info">Cancel</button>
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

@section('js')

@endsection