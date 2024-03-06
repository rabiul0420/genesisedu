@extends('admin.layouts.app')

@section('content')

    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <i class="fa fa-home"></i><a href="{{ url('/') }}">Home</a><i class="fa fa-angle-right"></i>
            </li>
            <li>{{$title}}</li>
        </ul>
    </div>

    @if(Session::has('message'))
        <div class="alert alert-success" role="alert">
            <p> {{ Session::get('message') }}</p>
        </div>
    @endif


    <div class="row">
        <div class="col-md-12">
            <div class="portlet">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-globe"></i>{{ $title }}
                    </div>
                </div>
                <div class="portlet-body">
                    <table class="table table-striped table-bordered table-hover userstable datatable">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th width="100">Date & Time</th>
                            <th>Doctor Name</th>
                            <!-- <th>Mobile</th> -->
                            <th>Complain</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($complain_info as $sl=>$complain)
                        <tr>
                            <td>{{ $sl+1 }}</td>
                            <td>{{ $complain->created_at }}</td>
                            <td>{{ $complain->doctorname->name ?? '' }} ({{$complain->doctor_id}})</td>
                            <!-- <th>mm</th> -->
                            <td>{{ $complain->complain }}</td>
                            <td>
                                @if($complain->status==1)
                                <a href="#" class="btn btn-xs btn-primary" style="background-color:red; border:0px;">No Replied</a>
                                @endif
                                @if($complain->status==2)
                                <a href="#" class="btn btn-xs btn-primary" style="background-color:green; border:0px;">Replied</a>
                                @endif
                            </td>
                            <td><a href="{{ url('admin/complain-reply/'.$complain->id) }}" class="btn btn-xs btn-primary">Update</a></td>
                        </tr>
                        </tbody>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection

