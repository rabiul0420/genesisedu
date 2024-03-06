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
                {{ $title }}
            </li>
        </ul>

    </div>

    @if( Session::has('message') )
        <div class="alert {{ (Session::get('class'))?Session::get('class'):'alert-success' }}" role="alert">
            <p> {{ Session::get('message') }}</p>
        </div>
    @endif

    <div class="row">
        <div class="col-md-12">
            <!-- BEGIN EXAMPLE TABLE PORTLET-->
            <div class="portlet">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-reorder"></i>{{ $title }}
                    </div>
                </div>
                <div class="portlet-body form">
                    <!-- BEGIN FORM-->
                    {!! Form::open(['action'=>['Admin\TopicController@store'],'files'=>true,'class'=>'form-horizontal']) !!}
                    <div class="form-body">

                        <div class="form-group">
                            <label class="col-md-3 control-label">Class/Chapter Name (<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>) </label>
                            <div class="col-md-4">
                                <div class="input-icon right">
                                    <input type="text" name="name" required value="{{ old('name') }}" class="form-control">
                                </div>
                            </div>
                        </div>



                        <div class="institutes">

                            <div class="form-group">
                                <label class="col-md-3 control-label">Institute (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                                <div class="col-md-3">
                                    <div class="input-icon right">

                                        @php
                                            $institutes = \App\Institutes::active()->pluck( 'name', 'id' );
                                            $institutes->prepend('Select Institute', '');
                                        @endphp
                                        {!! Form::select('institute_id',$institutes,
                                                old('institute_id', $topic->institute_id ?? '' ),
                                                ['class'=>'form-control','required'=>'required','id'=>'institute_id'])
                                        !!}<i></i>

                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="courses">
                            <div class="form-group">
                                <label class="col-md-3 control-label">Courses (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                                <div class="col-md-3">
                                    <div class="input-icon right">

                                        @php
                                            $institutes = \App\Courses::active()->pluck( 'name', 'id' );
                                            $institutes->prepend('Select Institute', '');
                                        @endphp
                                        {!! Form::select('institute_id',$institutes,
                                                old('institute_id', $topic->institute_id ?? '' ),
                                                ['class'=>'form-control','required'=>'required','id'=>'institute_id'])
                                        !!}<i></i>

                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="faculties">

                        </div>

                        <div class="subjects">

                        </div>

                        <div class="topics">

                        </div>

                        <div class="batches">

                        </div>



{{--                        <div class="form-group">--}}
{{--                            <label class="col-md-3 control-label">Course (<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>) </label>--}}
{{--                            <div class="col-md-4">--}}
{{--                                @php  $courses->prepend('Select Course', ''); @endphp--}}
{{--                                {!! Form::select('course_id',$courses, old('course_id'),['class'=>'form-control','required'=>'required']) !!}<i></i>--}}
{{--                            </div>--}}
{{--                        </div>--}}



                    </div>
                    <div class="form-actions">
                        <div class="row">
                            <div class="col-md-offset-3 col-md-9">
                                <button type="submit" class="btn btn-info">Submit</button>
                                <a href="{{ url('admin/topic') }}" class="btn btn-default">Cancel</a>
                            </div>
                        </div>
                    </div>
                {!! Form::close() !!}
                <!-- END FORM-->
                </div>
            </div>
            <!-- END EXAMPLE TABLE PORTLET-->



        </div>
    </div>
    <!-- END PAGE CONTENT-->


@endsection

@section('js')


@endsection
