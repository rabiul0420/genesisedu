@extends('admin.layouts.app')
@section('banner-sliders', 'active')
@section('content')

    @if(Session::has('message'))
        <div class="alert {{ (Session::get('class'))?Session::get('class'):'alert-success' }}" role="alert">
            <p> {{ Session::get('message') }}</p>
        </div>
    @endif

    <div class="row">
        <div class="col-md-12">
            <div class="portlet">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-reorder"></i>Banner Slider Create
                    </div>
                </div>
                <div class="portlet-body form">
                    <!-- BEGIN FORM-->
                    <form action="{{ route('banner-sliders.store', $bannerSlider->id) }}" method="POST" class="form-horizontal" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    @include('admin.banner-sliders.form') 
                    <div class="form-actions">
                        <div class="row">
                            <div class="col-md-offset-3 col-md-9">
                                <button type="submit" class="btn btn-info">Submit</button>
                                <a href="{{ route('banner-sliders.index') }}" class="btn btn-default">Cancel</a>
                            </div>
                        </div>
                    </div>
                    </form>
                <!-- END FORM-->
                </div>
            </div>
            <!-- END EXAMPLE TABLE PORTLET-->
            <style>
            #image-preview {
                width:500px;
                height:350px;
                border: 1px solid #707070;
                }
            </style>

            <div class="text-center">
                <img width="500" height="350" id="image-preview" src="{{ asset($bannerSlider->image ?? 'images/1000X700.png') }}">
            </div>
        </div>
    </div>
    <!-- END PAGE CONTENT-->
@endsection