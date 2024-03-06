@extends('admin.layouts.app')

@section('content')

    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <i class="fa fa-home"></i>
                <a href="{{ url('/') }}">Home</a>
                <i class="fa fa-angle-right"></i>
            </li>
            <li>
                Photos Edit
            </li>
        </ul>
    </div>

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
                        <i class="fa fa-reorder"></i>Photos Edit
                    </div>
                </div>
                <div class="portlet-body form">
                    <!-- BEGIN FORM-->
                        {!! Form::open(['action'=>['Admin\PhotosController@update',$photos->id],'method'=>'PUT','files'=>true,'class'=>'form-horizontal']) !!}
                        <div class="form-body">
                            
                            <div class="form-group">
                                <label class="col-md-2 control-label">Old Photo (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                                <div class="col-md-2">
                                    <div class="input-icon right">
                                        <img src="{{asset($photos->image)}}" alt=" Not Found So Upload at now" style="width: 100%; border: 1px solid #22222222">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                            <label class="col-md-2 control-label">Doctors Photo (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                            <div class="col-md-2">
                                <div class="input-icon right">
                                    <input type="file" name="image" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <p class="text-danger h5">Size must be 960px (width) X 560px (height)</p>
                            </div>
                        </div> 

                        </div>
                        <div class="form-actions">
                            <div class="row">
                                <div class="col-md-offset-1 col-md-9">
                                    <button type="submit" class="btn btn-info">Update</button>
                                    <a href="{{ url('admin/photos') }}" class="btn btn-default">Cancel</a>
                                </div>
                            </div>
                        </div>
                       {!! Form::close() !!}
                    <!-- END FORM-->
                </div>
            </div>

        </div>
    </div>



@endsection

@section('js')

    <script type="text/javascript">
        // DO NOT REMOVE : GLOBAL FUNCTIONS!
        $(document).ready(function() {

               // $('.select2').select2();

        })
    </script>




@endsection