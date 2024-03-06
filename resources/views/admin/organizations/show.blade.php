@extends('admin.layouts.app')

@section('content')

    <h3 class="page-title">
        Organization
    </h3>
    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <i class="fa fa-home"></i><a href="{{ url('/admin') }}">Home</a><i class="fa fa-angle-right"></i>
            </li>
            <li>Organization</li>
        </ul>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="portlet">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-reorder"></i>Details of <b>{{ $organization->name }}</b> Organization
                    </div>
                </div>
                <div class="portlet-body">

                    <div class="form-group">
                        <label for="email">Name: {{ $organization->name }}</label>
                    </div>

                    <div class="form-group">
                        <label for="name">Domain: {{ $organization->domain }}</label>
                    </div>

                    <div class="form-group">
                        <label for="email">Total Employee: {{ $organization->organizationuser->count() }} <a href="{{ url('admin/organization-users/'.$organization->id) }}">All Users</a></label>
                    </div>




                </div>
            </div>


        </div>
    </div>

@endsection













