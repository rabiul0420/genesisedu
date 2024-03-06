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
                Question Link Edit
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
                        <i class="fa fa-reorder"></i>Question Link Edit
                    </div>
                </div>

                <div class="portlet-body form">
                    <!-- BEGIN FORM-->
                        {!! Form::open(['action'=>['Admin\QuestionlinkController@update',$questionlink->id],'method'=>'PUT','files'=>true,'class'=>'form-horizontal']) !!}
                            <div class="form-group">
                                <label class="col-md-2 control-label">Title 
                                (<i class="fa fa-asterisk ipd-star"style="font-size:9px;"></i>) </label>
                                <div class="col-md-6 mt-2">
                                <div class="input-icon right">
                                <input type="text" id="title" name="title"  value="{{ $questionlink->title ?? '' }}" class="form-control">
                                </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-2 control-label">question_link
                                (<i class="fa fa-asterisk ipd-star"style="font-size:9px;"></i>) </label>
                                <div class="col-md-6">
                                <div class="input-icon right">
                                    <textarea class="form-control" name="question_link" id="questionlink" cols="30" rows="5"  >{{  $questionlink->question_link ?? '' }}</textarea>
                                {{-- <input type="text" id="questionlink" name="question_link" value="{{ $questionlink->question_link ?? '' }}" class="form-control"> --}}
                                
                                 </div>
                                </div>
                            </div>
                        </div>


                        <div class="form-actions">
                            <div class="row">
                                <div class="col-md-offset-1 col-md-9">
                                    <button type="submit" class="btn btn-info">Update</button>
                                    <a href="{{ url('admin/questionlink') }}" class="btn btn-default">Cancel</a>
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
<script src="https://cdn.ckeditor.com/4.14.0/standard/ckeditor.js"></script>
    <script type="text/javascript">
        // DO NOT REMOVE : GLOBAL FUNCTIONS!
        $(document).ready(function() {

               // $('.select2').select2();

        })
        
        CKEDITOR.replace('description');
        </script>

@endsection