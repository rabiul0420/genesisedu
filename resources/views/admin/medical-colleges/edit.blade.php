@extends('admin.layouts.app')
@section('medical-colleges', 'active')
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
                        <i class="fa fa-reorder"></i>Medical College Edit
                    </div>
                </div>
                <div class="portlet-body form">
                    <!-- BEGIN FORM-->
                    <form action="{{ route('medical-colleges.update', $medicalCollege->id) }}" method="POST" class="form-horizontal">
                    {{method_field('PUT')}}
                    {{ csrf_field() }}
                    @include('admin.medical-colleges.form')
                    <input type="hidden" name="created_by" value="{{ $medicalCollege->created_by ?? Auth::id() }}">
                    <input type="hidden" name="updated_by" value="{{ Auth::id() }}">
                    <div class="form-actions">
                        <div class="row">
                            <div class="col-md-offset-3 col-md-9">
                                <button type="submit" class="btn btn-info">Update</button>
                                <a href="{{ route('medical-colleges.show', $medicalCollege->id) }}" class="btn btn-default">Cancel</a>
                            </div>
                        </div>
                    </div>
                    </form>
                <!-- END FORM-->
                </div>
            </div>
            <!-- END EXAMPLE TABLE PORTLET-->



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