@extends('admin.layouts.app')

@section('content')

    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <i class="fa fa-home"></i><a href="{{ url('/admin') }}">Home</a><i class="fa fa-angle-right"></i>
            </li>
            <li>Administrator Edit</li>
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
                        <i class="fa fa-reorder"></i>Administrator Edit
                    </div>
                </div>
                <div class="portlet-body form">
                    <!-- BEGIN FORM-->
                        {!! Form::open(['action'=>['Admin\AdministratorController@update',$user->id],'method'=>'PUT','files'=>true,'class'=>'form-horizontal']) !!}
                        <div class="form-body">

                            <div class="form-group">
                                <label class="col-md-1 control-label">Name (<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>) </label>
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                                        <input type="name" name="name" value="{{ $user->name }}" class="form-control" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label class="col-md-1 control-label">Email Address (<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>) </label>
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                                        <input type="email" name="email" value="{{ $user->email }}" class="form-control" placeholder="Email Address" required>
                                    </div>
                                </div>
                            </div>

                            @php
                                $phone_prefix = substr($user->phone_number,0,1);
                                $phone_prefix = ($phone_prefix==1)?$phone_prefix:'88';
                                $phone_number = ($phone_prefix==1)?substr($user->phone_number,1):substr($user->phone_number,2);
                            @endphp

                            <div class="form-group">
                                <label class="col-md-1 control-label">Phone Number (<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>) </label>
                                <div class="col-md-1">
                                    <div class="input-icon right">
                                        {!! Form::select('phone_prefix', ['1' => '1','88' => '88'], $phone_prefix ,['class'=>'form-control','required'=>'required']) !!}
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="input-icon right">
                                        <input type="text" name="phone_number" required value="{{ $phone_number }}" class="form-control" minlength="11" maxlength="11">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-1 control-label">Select Status (<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>) </label>
                                <div class="col-md-4">
                                    {!! Form::select('status', ['1' => 'Active','0' => 'InActive'], $user->status ,['class'=>'form-control']) !!}<i></i>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-1 control-label">Role (<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>) </label>
                                <div class="col-md-4">
                                {!! Form::select('roles[]', $roles, old('roles') ? old('roles') : $user->roles()->pluck('name', 'name'), [ 'id' => 'user_role', 'class' => 'form-control select2', 'multiple' => 'multiple', 'required' => 'required']) !!}
                                </div>
                            </div>


                            <div class="form-group">
                                <label class="col-md-1 control-label">Password</label>
                                <div class="col-md-4">
                                    <div class="input-icon right">
                                        <input type="text" name="password" value="{{ $user->security ?? '' }}" class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-actions">
                            <div class="row">
                                <div class="col-md-offset-1 col-md-9">
                                    <button type="submit" class="btn btn-info">Submit</button>
                                    <a href="{{ url('admin/administrator') }}" class="btn btn-default">Cancel</a>
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

    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/css/select2.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/js/select2.js" type="text/javascript"></script>

    <script>
        $(document).ready(function() {
            $('.select2').select2();
        })
    </script>




@endsection
