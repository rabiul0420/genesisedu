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
                    {!! Form::open(['action'=>['Admin\LectureVideoController@lecture_video_price_save'],'files'=>true,'class'=>'form-horizontal']) !!}
                    <div class="form-body">

                        <div class="form-group">
                            <label class="col-md-3 control-label">Lecture Video (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                            <div class="col-md-6">
                                <div class="input-icon right">
                                    <label class="control-label"  style="font-size: 15px;font-weight:700;">{{ $lecture_video->name }}</label>
                                    <input type="hidden" name="lecture_video_id" value="{{ $lecture_video->id }}" >
                                </div>
                            </div>                            
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">Price Activation Date (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                            <div class="col-md-6">
                                <div class="input-icon right">
                                    <input type="text" id="active_from" name="active_from" value="" class="form-control input-append date" autocomplete="off" required>
                                </div>
                            </div>                            
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">Price (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                            <div class="col-md-6">
                                <div class="input-icon right">
                                    <input type="number" id="price" name="price" value="{{ old('price') ?? '' }}" min="0" class="form-control input-append" required>
                                </div>
                            </div>                            
                        </div>         

                    </div>
                    <div class="form-actions">
                        <div class="row">
                            <div class="col-md-offset-3 col-md-9">
                                <button type="submit" class="btn btn-info">Submit</button>
                                <a href="{{ url('admin/lecture-video') }}" class="btn btn-default">Cancel</a>
                            </div>
                        </div>
                    </div>

                    {!! Form::close() !!}

                </div>
            </div>
            <!-- END EXAMPLE TABLE PORTLET-->
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <!-- BEGIN EXAMPLE TABLE PORTLET-->
            <div class="portlet">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-reorder"></i>Lecture Video Prices
                    </div>
                </div>
                <div class="portlet-body">
                    <style>
                        .active
                        {
                            background-color: blue;
                        }
                    </style>
                    <table class="table table-bordered table-hover">
                        <tr>
                        <th>Sl No</th><th>Lecture Video</th><th>Price Activation Date</th><th>Price</th><th>Currency</th><th>Action</th>
                        </tr>
                        @php $lecture_video_prices = $lecture_video->lecture_video_prices; $row_class="inactive"; @endphp
                        @if(isset($lecture_video_prices))
                        @foreach($lecture_video->lecture_video_prices as $key=>$lecture_video_price)
                        @if($key==$active_index)
                        <tr style="background-color:lightgreen;">
                            <td>{{ $key+1 }}</td><td>{{ $lecture_video_price->lecture_video->name }}</td><td>{{ $lecture_video_price->active_from }}</td><td>{{ $lecture_video_price->price }}</td><td>BDT</td>
                            <td><a class="btn btn-xs btn-info" href="{{ url('admin/lecture-video-price-edit/'.$lecture_video_price->id) }}">Edit</a></td>
                        </tr>
                        @else
                        <tr>
                            <td>{{ $key+1 }}</td><td>{{ $lecture_video_price->lecture_video->name }}</td><td>{{ $lecture_video_price->active_from }}</td><td>{{ $lecture_video_price->price }}</td><td>BDT</td>
                            <td><a class="btn btn-xs btn-info" href="{{ url('admin/lecture-video-price-edit/'.$lecture_video_price->id) }}">Edit</a></td>
                        </tr>
                        @endif
                        @endforeach
                        @endif                        
                    </table>                    
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