@extends('layouts.app')

@section('content')


<div class="container text-center">

    @if (session('message'))
    <div class="alert {{ session('class') }} py-2 mt-3">
        {{ session('message') }} 
    </div>
    @endif

    <form action="{{url( '/password-recovery-submit') }}" method="POST">
        {{ csrf_field() }}
        {{-- <input type="hidden" name="_token" value="{{ csrf_token() }}" /><br> --}}
        <label for="sent">Please Type Your Registered Mobile Number .</label><br><br>
        <input class="form-control mx-auto" type="text" id="sent" name="phone_number" placeholder="01700000000" style="max-width: 360px;" required pattern="[0-9]{11}"> <br>
        <input type="submit" value="Submit" class="btn btn-info">
        
    </form>
</div>
@endsection
