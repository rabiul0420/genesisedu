@extends('admin.layouts.app')

@section('content')

    <h3 class="page-title">
        My Profile
    </h3>
    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <i class="fa fa-home"></i><a href="{{ url('/admin') }}">Home</a><i class="fa fa-angle-right"></i>
            </li>
            <li>My Profile</li>
        </ul>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="portlet">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-reorder"></i>My Profile
                    </div>
                </div>
                <div class="portlet-body">


                    <div class="form-group">
                        <label for="email">Email: {{ $user->email }}</label>
                    </div>

                    <div class="form-group">
                        <label for="name">Phone: {{ $user->phone_number }}</label>
                    </div>

                    <div class="form-group">
                        <label for="name">Two Factor: {{ $user->two_factor }}</label>
                    </div>

                   {{-- <div class="form-group">
                        <label for="name">Role: @foreach($user->roles as $key=>$role) {{ (($key)?', ':'').$role->name }}   @endforeach</label>
                    </div>--}}

                    <a class="btn btn-primary" href="{{ url('admin/profile-edit') }}"> Edit</a>

                </div>
            </div>


        </div>
    </div>

@endsection













