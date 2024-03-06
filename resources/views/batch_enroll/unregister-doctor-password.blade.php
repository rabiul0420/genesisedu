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
                    <div class="caption" style="background-color:#90EDB7">
                        <p style="color:#0AC057; font-size: 15px; margin-bottom: 10px;background-color:#90EDB7;">Dear Doctor, This is single step registration process . Please type your name and submit.</p>
                    </div>
                </div>
                <div class="portlet-body form">
                    <form action="{{ url('/password-submit-auto') }}" method="POST" class="form">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" colspan= "3" class="form-control" name="password" id="password" placeholder="Enter Password">
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
