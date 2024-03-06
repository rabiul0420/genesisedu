@extends('layouts.app')

@section('content')
    <div class="container">

        <div class="row">

            @include('side_bar')

            <div class="col-md-9 col-md-offset-0">
                <div class="panel panel-default">
                    <div class="panel-heading" style="background-color:#7fc9f6; color: #FFFFFF;"><h3>OTP Option</h3></div>

                    <div class="panel-body">
                        @if (session('status'))
                            <div class="alert alert-success">
                                {{ session('status') }}
                            </div>
                        @endif


                        
                        <div class="col-md-12">
                            <div class="portlet">
                                <div class="portlet-body">
                                    
                                {{$msg}}<br><br>
                                {!! Form::open(['url'=>['submit-otp'],'method'=>'post','files'=>true,'class'=>'form-horizontal']) !!}
                                    <input type="hidden" name="doctor_id" required value="{{$doc_info->id}}">
                                    <input type="hidden" name="video_id" required value="{{$video_id}}">
                                    <input type="text" name="otp" required minlength="4" placeholder="type OTP">
                                    <input type="submit" value="submit" class="btn btn-xm btn-primary">
                                {!! Form::close() !!}  



                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>

    </div>
@endsection


