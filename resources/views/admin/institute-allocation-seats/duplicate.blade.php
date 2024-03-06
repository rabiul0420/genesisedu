@extends('admin.layouts.app')
@section('institute-allocation-seats', 'active')
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
                        <i class="fa fa-reorder"></i>Institute Seat Edit
                    </div>
                </div>
                <div class="portlet-body form">
                    <!-- BEGIN FORM-->
                    <form action="{{ route('institute-allocation-seats.duplicate-save', $instituteAllocationSeat->id) }}" method="POST" class="form-horizontal">
                    {{ csrf_field() }}

                    @include('admin.institute-allocation-seats.form')
                    
                    <div class="form-actions">
                        <div class="row">
                            <div class="col-md-offset-3 col-md-9">
                                <button type="submit" class="btn btn-info">Update</button>
                                <a href="{{ route('institute-allocation-seats.show', $instituteAllocationSeat->id) }}" class="btn btn-default">Cancel</a>
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