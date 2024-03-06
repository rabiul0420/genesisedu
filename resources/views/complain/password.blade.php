<div  class="my-3">
    <div class="row d-flex justify-content-center align-items-center">
        <div class="col-sm-4">
            <div class="caption">
                @if(isset($doctor))
                <p style="color:#0AC057; font-size: 15px; margin-bottom: 10px;">Dear Doctor,please enter your full name. Also you will get SMS with password within 1 minute . Please collect the password from SMS & input in the password box.</p>
                <div class="py-2">
                    <input class="form-control" id="doc_name" type="text" name="name" placeholder="Full Name"  required>
                </div>
                @else
                <p style="color:#0AC057; font-size: 15px; margin-bottom: 10px;">Dear Doctor, GENESIS found you registered in the website . Please input your Password and submit.</p>
                @endif
            </div>
            <label for="sent">Password</label>
            <div class="relative" style="position: relative;">
                <input type="password" colspan= "3" class="form-control" name="password" id="password" placeholder="Enter Password">
                <i style="position: absolute; top: 10px; right: 5px;" class="fa fa-eye showpwd" onClick="showPwd('password', this)"></i>
            </div>
            <p style="text-align: left;color: red; margin-bottom: 5px;" class="password-wrong-message"></p>
            <p><a type="button" value="Submit" id= "password_submit" class="btn btn-info">Submit</a></p>

           <p> <a href="{{url('/password-send-complain')}}" class="forgot_pass forgot-text btn-link">Forgot password?</a></p>

        </div>
    </div>
</div>
