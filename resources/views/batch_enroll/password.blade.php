@extends('layouts.app')

@section('content')
    <div class="container">

        <div class="row">

            <div class="col-md-9 col-md-offset-0">
            
            @if(Session::has('message'))
                <div  style="margin-top: 25px;" class="alert {{ (Session::get('class'))?Session::get('class'):'alert-success' }}" role="alert">
                    <p> {{ Session::get('message') }}</p>
                </div>
            @endif
            <!-- BEGIN EXAMPLE TABLE PORTLET-->
            <div class="portlet" style="margin: 40px">
                <div class="portlet-title">
                    <div class="caption">
                        @if($doctor->bmdc_no == null)
                        <p style="color:#0AC057; font-size: 15px; margin-bottom: 10px;">Dear Doctor, you will get SMS with password within 1 minute . Please collect the password from SMS & input in the password box.</p>
                        @else
                        <p style="color:#0AC057; font-size: 15px; margin-bottom: 10px;">Dear Doctor, GENESIS found you registered in the website . Please input your Password and submit.</p>
                        @endif
                    </div>
                </div>
                <div class="portlet-body form">
  
                    <form action="{{ url('/password-submit-auto') }}" method="POST" class="form">
                        {{ csrf_field() }}

                          <div class="form-group">

                            <label for="password" style="color:#0AC057">Password</label>
                            <div class="relative" style="position: relative;">
                                <input type="password" colspan= "3" class="form-control" name="password" id="password" placeholder="Enter Password">
                                <i style="position: absolute; top: 10px; right: 5px;" class="fa fa-eye showpwd" onClick="showPwd('password', this)">   </i>
                            </div>
                            <input type="hidden" name="hidden_mobile_number" value="{{ $mobile_number }}">
                            <input type="hidden" name="hidden_schedule_id" value="{{ $schedule_id }}">
                        </div>
                        <div class="form-group">
                            <div class="col-sm-6 ">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> Remember Me
                                    </label>
                                </div>
                            </div>
                        </div>
                        <button type="submit" style="margin:10px 0px;" class="btn btn-primary">Submit</button>  
                        <div class="form-group">
                            <div class="col-sm-6">
                                <div class="checkbox">
                                    <a class="btn btn-link" style="padding:0px" href="{{ url('/password-request-from-available-batch/'.$schedule_id) }}">
                                        Forgot Your Password?
                                    </a> 
                                </div>
                            </div>
                        </div> 
                                           
                    </form>
                </div>
            </div>
            
    
    <!-- Modal -->
    
  

@endsection

@section('js')

    <script type="text/javascript">

    </script>

    <script>
        function showPwd(id, el) {
        let x = document.getElementById(id);
        if (x.type === "password") {
            x.type = "text";
            el.className = 'fa fa-eye-slash showpwd';
        } else {
            x.type = "password";
            el.className = 'fa fa-eye showpwd';
        }
        }
    </script>

@endsection
