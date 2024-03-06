@extends('admin.layouts.app')

@section('content')

    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <i class="fa fa-home"></i><a href="{{ url('/admin') }}">Home</a><i class="fa fa-angle-right"></i>
            </li>
            <li>{{ $title }}</li>
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
                        <i class="fa fa-reorder"></i>{{ $title }}
                    </div>
                </div>
                <div>
                    <?php
                    //echo '<pre>';
                    //print_r($question_type);
                    ?>
                </div>
                <div class="portlet-body form">
                    <!-- BEGIN FORM-->
                    {!! Form::open(['action'=>['Admin\QuestionTypesController@update',$type_info->id],'method'=>'PUT','files'=>true,'class'=>'form-horizontal']) !!}
                    <div class="form-body">

                        <div class="form-group">
                            <label class="col-md-2 control-label">Batch Type(<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>) </label>
                            <div class="col-md-4">
                                <div class="input-icon right">
                                    <select name="batch_type"  class="form-control" id="batch-type">
                                        <option value="-" {{ old('batch_type', $type_info->batch_type) == '' ? 'selected':'' }}>Normal</option>
                                        <option value="combined" {{ old('batch_type', $type_info->batch_type) == 'combined' ? 'selected':'' }}>Combined</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-2 control-label">Question Type Title (<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>) </label>
                            <div class="col-md-4">
                                <div class="input-icon right">
                                    <input type="text" name="title" required value="{{ $type_info->title }}" class="form-control">
                                </div>
                            </div>
                        </div>


                        <div id="mcq_data" style="border-bottom: 1px dashed green; border-top: 1px dashed green; margin-bottom: 20px;padding-top: 20px">
                            @include('admin.question_types.question_type_data', [ 'name' => 'mcq', 'required' => true, 'question_type' => $type_info ])
                        </div>


                        <div id="mcq2_data" style="{{ $type_info->batch_type != 'combined' ? 'display:none':'' }};border-bottom: 1px dashed green; margin-bottom: 20px">
                            @include('admin.question_types.question_type_data', [ 'name' => 'mcq2', 'question_type' => $type_info ])
                        </div>

                        <div id="sba_data" style="border-bottom: 1px dashed green; margin-bottom: 20px">
                            @include('admin.question_types.question_type_data', [ 'name' => 'sba', 'required' => true,'question_type' => $type_info ])
                        </div>


                        <div class="form-group">
                            <label class="col-md-2 control-label">Pass Mark (<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>) </label>
                            <div class="col-md-4">
                                <div class="input-icon right">
                                    <input type="number" name="pass_mark" required value="{{ $type_info->pass_mark }}" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-2 control-label">Duration ( In Minutes ) (<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>) </label>
                            <div class="col-md-4">
                                <div class="input-icon right">
                                    <input type="number" name="duration" required value="{{ $duration }}" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-2 control-label">Paper or Faculty (BCPS) </label>
                            <div class="col-md-4" id="id_div_doctors_gender">
                                <label class="radio-inline"><input type="radio" name="paper_faculty" value="Paper" {{ $type_info->paper_faculty === "Paper" ? "checked" : '' }} > Paper</label>
                                <label class="radio-inline"><input type="radio" name="paper_faculty" value="Faculty" {{ $type_info->paper_faculty === "Faculty" ? "checked" : '' }}> Faculty</label>
                                <label class="radio-inline"><input type="radio" name="paper_faculty" value="None" {{ $type_info->paper_faculty === "None" ? "checked" : '' }}> None</label>
                            </div>
                        </div>

                    </div>
                    <div class="form-actions">
                        <div class="row">
                            <div class="col-md-offset-2 col-md-9">
                                <button type="submit" class="btn btn-info">Submit</button>
                                <a href="{{ url('admin/question-types') }}" class="btn btn-default">Cancel</a>
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

    @include( 'admin.question_types.scripts' )

@endsection
