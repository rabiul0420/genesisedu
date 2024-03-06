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
                    <div class="caption" style="background-color:#BFF1D4; padding:15px 10px">
                        <p style="color:black; font-size: 14px; margin-bottom: 10px; text-align:justify;">Dear Doctor,This is single step registration process.Please type your name and submit.</p>
                    </div>
                </div>
                <div class="portlet-body form">
                    <form action="{{ url('/register-name') }}" method="POST" class="form">
                        {{ csrf_field() }}
                        <div class="form-group" style="margin-bottom: 10px;">
                            <label for="name">Name</label>
                            <input type="text" colspan= "3" class="form-control" name="name" id="name" required placeholder="Enter Your Name">
                            <input type="hidden" name="hidden_mobile_number" value="{{ $mobile_number }}">
                            <input type="hidden" name="hidden_schedule_id" value="{{ $schedule_id }}">
                          </div>
                          <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
            </div>
            
    
    <!-- Modal -->
    


@endsection

@section('js')



    <script type="text/javascript">

    </script>


@endsection
