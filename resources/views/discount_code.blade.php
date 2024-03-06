@extends('layouts.app')

@section('content')


<div class="container text-center mt-5">

    <form action="{{url( '') }}" method="get">
         <label class="discount" for="discount_code">Register Mobile Number.</label><br>
         <input type="hidden" name="_token" value="{{ csrf_token() }}" /><br>
         <input class="form-control mx-auto" type="number" id="sent" name="discount_code"style="max-width: 360px;"> <br>
     </form>
 
    <form action="{{url( 'discount-code-submit') }}" method="post">
        <input type="hidden" name="_token" value="{{ csrf_token() }}" /><br>
        <input class="form-control mx-auto" type="number" id="sent" name="discount_code" placeholder="Please Type Your promo code." style="max-width: 360px;"> <br>
        <input type="submit" value="Apply code" class="btn btn-apply">
    </form>

</div>

@endsection
