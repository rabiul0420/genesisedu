@extends('admin.layouts.app')

@section('content')


    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <i class="fa fa-home"></i>
                <a href="{{ url('admin') }}">Home</a>
                <i class="fa fa-angle-right"></i>
            </li>
            <li>
                User Account List okkk
            </li>
        </ul>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="portlet">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-globe"></i>User Account List
                    </div>
                </div>
                <div class="portlet-body">
                    <iframe src="<?php echo $iframeUrl ?>" frameborder="0" width="100%" height="800" allowtransparency></iframe>
                </div>
            </div>


        </div>
    </div>


@endsection

