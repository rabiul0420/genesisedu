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
                        <i class="fa fa-reorder"></i>Banner Sliders Edit
                    </div>
                </div>
                <div class="portlet-body form">
                    <!-- BEGIN FORM-->
                    <form action="{{ route('banner-sliders.update', $bannerSlider->id) }}" method="POST" class="form-horizontal" enctype="multipart/form-data">
                    {{method_field('PUT')}}
                    {{ csrf_field() }}
                    @include('admin.banner-sliders.form') 
                    <div class="form-actions">
                        <div class="row">
                            <div class="col-md-offset-3 col-md-9">
                                <button type="submit" class="btn btn-info">Update</button>
                                <a href="{{ route('banner-sliders.show', $bannerSlider->id) }}" class="btn btn-default">Cancel</a>
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
                <img width="500" height="350" id="image-preview" src="{{ asset($bannerSlider->image) }}">
            </div>

        </div>
    </div>
    <!-- END PAGE CONTENT-->


@endsection

@section('js')
<script src="https://cdn.ckeditor.com/4.14.0/standard/ckeditor.js"></script>
<script type="">
    $(document).ready(function() {
        CKEDITOR.replace( 'description' );
    })
</script>
@endsection