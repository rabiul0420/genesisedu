@extends('admin.layouts.app')

@section('content')


    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <i class="fa fa-home"></i>
                <a href="{{ url('/') }}">Home</a>
                <i class="fa fa-angle-right"></i>
            </li>
            <li>{{$title}}</li>
        </ul>

    </div>

    @if(Session::has('message'))
        <div class="alert {{ (Session::get('class'))?Session::get('class'):'alert-success' }}" role="alert">
            <p> {{ Session::get('message') }}</p>
        </div>
    @endif

    <style>
        
    </style>

    <div class="row">
        <div class="col-md-12">
            <!-- BEGIN EXAMPLE TABLE PORTLET-->
            <div class="portlet">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-reorder"></i>{{$title}}
                    </div>
                    
                </div>
                <div class="portlet-body form">
                    <!-- BEGIN FORM-->
                    {!! Form::open(['action'=>['Admin\LectureVideoController@lecture_video_price_update'],'files'=>true,'class'=>'form-horizontal']) !!}
                    <div class="form-body">

                        <input type="hidden" name="lecture_video_price_id" value="{{ $lecture_video_price->id }}" >

                        <div class="form-group">
                            <label class="col-md-3 control-label">Lecture Video (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                            <div class="col-md-6">
                                <div class="input-icon right">
                                    <label class="control-label"  style="font-size: 15px;font-weight:700;">{{ $lecture_video_price->lecture_video->name }}</label>
                                    <input type="hidden" name="lecture_video_id" value="{{ $lecture_video_price->lecture_video->id }}" >
                                </div>
                            </div>                            
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">Price Activation Date (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                            <div class="col-md-6">
                                <div class="input-icon right">
                                    <input type="text" id="active_from" name="active_from" value="{{ $lecture_video_price->active_from }}" class="form-control input-append date" autocomplete="off" required>
                                </div>
                            </div>                            
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">Price (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                            <div class="col-md-6">
                                <div class="input-icon right">
                                    <input type="number" id="price" name="price" value="{{ $lecture_video_price->price }}" min="0" class="form-control input-append" required>
                                </div>
                            </div>                            
                        </div>         

                    </div>
                    <div class="form-actions">
                        <div class="row">
                            <div class="col-md-offset-3 col-md-9">
                                <button type="submit" class="btn btn-info">Submit</button>
                                <a href="{{ url('admin/lecture-video-price/'.$lecture_video_price->lecture_video->id) }}" class="btn btn-default">Cancel</a>
                            </div>
                        </div>
                    </div>

                    {!! Form::close() !!}

                </div>
            </div>
            <!-- END EXAMPLE TABLE PORTLET-->
        </div>
    </div>

    <!-- END PAGE CONTENT-->

@endsection

@section('js')
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker.min.css" />
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker3.min.css" />
    <script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/js/bootstrap-datepicker.min.js"></script>

    <script type="text/javascript">

        $(document).ready(function() {

            $("body").on( "focus", ".date", function() {
                $(this).datepicker({
                    format: 'yyyy-mm-dd',
                    startDate: '',
                    endDate: '',
                }).on('changeDate', function(e){
                    $(this).datepicker('hide');
                });
            })

        })
    </script>

@endsection