@extends('layouts.app')

@section('content')


    <div class="container text-center">

        @if(Session::has('message'))
        <div class="alert {{ (Session::get('class'))?Session::get('class'):'alert-success' }}" role="alert">
            <p> {{ Session::get('message') }}</p>
        </div>
        @endif

        <form  id="mobile_number_form" class="my-3">
            
            <div class="row d-flex justify-content-center align-items-center">
                <div class="col-sm-4">
                    <label for="sent">Please type your mobile number to make a complain.</label>
                    <div class="position-relative w-100 prefix">
                        <span class="position-absolute border-bottom text-dark border-info bg-warning" style="padding:9px 9px; left: 0px;">+88</span>
                    </div>

                    <input class="form-control" style="padding-left: 50px;" id="mobile_number" type="number" maxlength="11" id="sent"  oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"  name="phone_number" placeholder="01700000000"  required pattern="[0-9]{11}">
                    <input type="submit" value="Next" id= "btn" class="btn btn-info">
                </div>

                
            </div>
        </form>
        <br>

        <div class="password-place">

        </div>
    </div>
@endsection
@section('js')
    <script type="text/javasctipt"></script>
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

        $(document).ready(function() {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                }
            });

            $("#btn").on("click", function(e) {
                e.preventDefault();
               var phone_number = $('#mobile_number').val();

               $.ajax({
                   type:"POST",
                   url:'/submit-phone-number',
                   dataType: 'HTML',
                   data : {phone_number},
                   success: function(data){
                    $('.password-place').html(data);
                        
                        $("#password_submit").on("click", function(e) {
                            var password = $('#password').val();
                            var phone_number = $('#mobile_number').val();
                            const base_url = "{{ url('') }}";
                            $.ajax({
                                type:"POST",
                                url:'/password-submit-complain',
                                dataType: 'JSON',
                                data : {password,phone_number},
                                success: function(data){
                                    console.log(data);
                                     $('.password-wrong-message').html(data.message);
                                     
                                     if(data.success == true){
                                         console.log(data.success == true)
                                        window.location = base_url + '/' + 'complain-related';
                                     }
                                }

                            })
                            
                        })
                   }

               })
              
            })
        })
    </script>
    
@endsection