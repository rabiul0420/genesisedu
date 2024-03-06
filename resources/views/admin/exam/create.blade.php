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
                {{$title}}
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
            <!-- BEGIN EXAMPLE TABLE PORTLET-->
            <div class="portlet">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-reorder"></i>{{$title}}
                    </div>
                </div>
                <div class="portlet-body form">
                    <!-- BEGIN FORM-->
                    {!! Form::open(['action'=>['Admin\ExamController@store'],'files'=>true,'class'=>'form-horizontal']) !!}
                    <div class="form-body">

                        <div class="form-group">
                            <label class="col-md-1 control-label">Exam Name (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                            <div class="col-md-11">
                                <div class="input-icon right">
                                    <input type="text" name="name" required value="{{ old('name') }}" class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-1 control-label">Nick Name </label>
                            <div class="col-md-11">
                                <div class="input-icon right">
                                    <input type="text" name="nickname" value="{{ old('nickname') }}" class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-1 control-label">Exam Type (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                            <div class="col-md-5">
                                @php  $exam_type->prepend('Select Exam Type', ''); @endphp
                                {!! Form::select('exam_type_id',$exam_type, old('exam_type_id'),['class'=>'form-control','required'=>'required']) !!}<i></i>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-1 control-label">Paper</label>
                            <div class="col-md-5">
                                {!! Form::select('paper',$papers, old('paper')?old('paper'):'' ,['class'=>'form-control']) !!}<i></i>
                            </div>
                        </div>



                        <div class="row">
                            <div class="col-md-6">
                                <div class="years">
                                    <div class="form-group">
                                        <label class="col-md-2 control-label">Year (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                                        <div class="col-md-5">
                                            <div class="input-icon right">
                                                {!! Form::select('year',$years, old('year')?old('year'):'' ,['class'=>'form-control','required'=>'required','id'=>'year']) !!}<i></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @if( $has_class_id_column )

                                    <div id="institutes">
                                        {!! $institutes_view ?? '' !!}
                                    </div>

                                    <div id="courses">
                                        {!! $courses_view ?? '' !!}
                                    </div>


                                    <div id="sessions">
                                        {!! $sessions_view ?? '' !!}
                                    </div>


                                    <div class="form-group">
                                        <label class="col-md-2 control-label">Class (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>)
                                        </label>
                                        <div class="col-md-10">
                                            <div class="input-icon right">
                                                <select name="class" class="form-control class-selection" required></select>
                                            </div>
                                        </div>
                                    </div>

                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-1 control-label">SIF Only ? (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                            <div class="col-md-1">
                                {!! Form::select('sif_only', ['No' => 'No','Yes' => 'Yes'], old('sif_only'),['class'=>'form-control','required'=>'required']) !!}<i></i>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-1 control-label">Question Type (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                            <div class="col-md-3">
                                @php  $question_type->prepend('Select Question Type', ''); @endphp
                                {!! Form::select('question_type_id',$question_type, old('question_type_id')?old('question_type_id'):'' ,['class'=>'form-control','required'=>'required']) !!}<i></i>
                            </div>


                        </div>

                        <div class="form-group">
                            <label class="col-md-1 control-label">Information Details (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                            <div class="col-md-11">
                                <div class="input-icon right">
                                    <textarea name="description" class="form-control"  style="min-height: 200px; min-width: 100%; max-width: 100%" required></textarea>
                                </div>
                            </div>
                        </div>

                       <div class="form-group">
                            <label class="col-md-1 control-label">Exam Details </label>
                            <div class="col-md-11">
                                <textarea name="exam_details" class="form-control">{{ old('exam_details') }}</textarea>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-1 control-label" >Status (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                                <div class="col-md-5">
                                    {!! Form::select('status', ['0' => 'InActive','1' => 'Active'], old('status'),['class'=>'form-control','required'=>'required']) !!}<i></i>
                                    <span class="text-danger">*** Default is inActive . Do not change it !!!</span><br>
                                    <span class="text-danger">*** Status will be auto Active when all questions are added to exam</span>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-1 control-label" ></label>
                                <div class="col-md-5">
                                    <label for="roll">
                                        <input type="checkbox" value="1" name="roll" id="roll">
                                        Collect Institue Roll
                                    </label>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="form-actions">
                        <div class="row">
                            <div class="col-md-offset-1 col-md-9">
                                <button type="submit" class="btn btn-info">Submit</button>
                                <a href="{{ url('admin/exam') }}" class="btn btn-default">Cancel</a>
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




    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker.min.css" />
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker3.min.css" />
    <script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/js/bootstrap-datepicker.min.js"></script>


    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/css/select2.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/js/select2.js" type="text/javascript"></script>

    <script type="text/javascript">
        const action = 'create';
    </script>

    @include('admin.exam.script');

@endsection
