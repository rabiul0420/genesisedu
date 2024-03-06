@extends('layouts.apps')

@section('content')
<div class="container">
    <div class="row align-items-center justify-content-center py-5">
        <div class="col" style="max-width: 600px;">
            <div class="py-3 text-center h4">Enter E-Mail Address</div>

            <div class="row justify-content-center">
                <div class="col-12">
                @if (session('status'))
                <div class="alert alert-success py-2">
                    {{ session('status') }} Check email <b>Inbox</b> or <b>Spam</b>
                </div>
                @endif
                </div>

                <div class="col-12">
                <form class="form-horizontal" method="POST" action="{{ route('password.email') }}">
                    {{ csrf_field() }}
                    <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                        <div class="col">
                            <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}"
                                required>

                            @if ($errors->has('email'))
                            <div class="alert alert-danger mt-2 py-2">
                                {{ $errors->first('email') }}
                            </div>
                            @endif
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col py-3 text-center">
                            <button type="submit" class="btn btn-info">
                                Send Link into E-Mail
                            </button>
                        </div>
                    </div>
                </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection