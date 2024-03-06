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
                                    
                                    @if ($otp_status==1)
                                        @foreach($video_info as $vlink) 
                                        Click on Video Name to view : 

<span class="btn btn-sm btn-primary" data-toggle='modal' data-target='#myModal_{{$vlink->id}}'>{{$vlink->name}}</span>
<div class='modal fade' id='myModal_{{$vlink->id}}' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' >
    <div class='modal-dialog' role='document' style="width: 100%;">
        <div class='modal-content'>
            <div class='modal-header'>

                <h4 class='modal-title' id='myModalLabel'>{{ $vlink->name }}</h4>
                
                @if($vlink->password)
             
                <h4><b>Video Password:</b> {{ $vlink->password }}</h4>
                @endif

            </div>
            <div class='modal-body'>
                <div class="col-md-6">
                    <iframe width='100%' height='400' src='{{$vlink->lecture_address}}' frameborder='0' allow='accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture' allowfullscreen></iframe>
                </div>
                <div class="col-md-6">
                    <iframe width='100%' height='500' src="pdf/{{$vlink->pdf_file}}"></iframe>
                </div>
                
            </div>
            <div class='modal-footer'>
                <button type='button' class='btn btn-sm bg-red' data-dismiss='modal'>Close</button>
            </div>
        </div>
    </div>
</div>


                                        
                                        @endforeach
                                    @elseif ($otp_status==0)
                                        <font color="red">You have Used OTP for this Video</font>
                                    @else
                                    <font color="red">Incorrect OTP! Try Again</font>
                                    {!! Form::open(['url'=>['submit-otp'],'method'=>'post','files'=>true,'class'=>'form-horizontal']) !!}
                                        <input type="hidden" name="doctor_id" required value="{{$doctor_id}}">
                                        <input type="hidden" name="video_id" required value="{{$video_id}}">
                                        <input type="text" name="otp" required minlength="4" placeholder="type OTP">
                                        <input type="submit" value="submit" class="btn btn-xm btn-primary">
                                    {!! Form::close() !!} 
                                    @endif



                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>

    </div>
@endsection


