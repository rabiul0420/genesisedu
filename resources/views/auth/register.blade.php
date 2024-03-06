@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Register</div>

                <div class="panel-body">
                    @if(Session::has('message'))
                        <div class="alert {{ (Session::get('class'))?Session::get('class'):'alert-success' }}" role="alert">
                            <p> {{ Session::get('message') }}</p>   
                        </div>
                    @endif
                    <form class="form-horizontal" method="POST" action="{{ route('register-post') }}">
                        {{ csrf_field() }}

                        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                            <label for="name" class="col-md-4 control-label">Name  (<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>)</label>

                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}" required autofocus>

                                @if ($errors->has('name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="email" class="col-md-4 control-label">E-Mail Address (<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>)</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required  placeholder="e.g : example@gmail.com">

                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('mobile_number') ? ' has-error' : '' }}">
                            <label for="name" class="col-md-4 control-label">Mobile (<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>)</label>

                            <div class="col-md-6">
                                <input id="mobile_number" type="text" class="form-control" name="mobile_number" value="{{ old('mobile_number') }}" required autofocus maxlength="11" minlength="11" placeholder="e.g : 01555555555">

                                @if ($errors->has('mobile_number'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('mobile_number') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('bmdc_no') ? ' has-error' : '' }}">
                            <label for="name" class="col-md-4 control-label">Medical College (<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>)</label>

                            <div class="col-md-6">
                                @php  $medical_colleges->prepend('Select Medical College', ''); @endphp
                                {!! Form::select('medical_college_id',$medical_colleges, '' ,['class'=>'form-control','required'=>'required','autofocus'=>'autofocus','id'=>'medical_college_id']) !!}<i></i>
                                                
                                @if ($errors->has('medical_college_id'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('medical_college_id') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('bmdc_no') ? ' has-error' : '' }}">
                            <label for="name" class="col-md-4 control-label">BMDC No. (<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>)</label>
                            <label for="number">+44</label>
                            <input id='number' onchange="this.value = '+44' + this.value" type="text">
                            <div class="col-md-6">
                                <input id="bmdc_no" type="text" class="form-control" name="bmdc_no" value="{{ old('bmdc_no') }}" required autofocus placeholder="e.g: A12345">

                                @if ($errors->has('bmdc_no'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('bmdc_no') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            <label for="password" class="col-md-4 control-label">Password (<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>)</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control" name="password" required>

                                @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="password-confirm" class="col-md-4 control-label">Confirm Password (<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>)</label>

                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    Register
                                </button>
                                
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>