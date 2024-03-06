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
                            <label class="col-md-1 control-label">Exam Date (<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>) </label>
                            <div class="col-md-3">
                                <div class="input-icon right">
                                    <input type="text" name="exam_date" autocomplete="off" value="{{ date('Y-m-d') }}" required class="form-control input-append date" id="datepicker">
                                </div>
                            </div>
                            <label class="col-md-1 control-label">Year (<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>) </label>
                            <div class="col-md-3">
                                {!! Form::select('year',$years, old('year')?old('year'):($exam->year ?? date('Y')) ,['class'=>'form-control','required'=>'required','id'=>'year']) !!}<i></i>
                            </div>
                            <label class="col-md-1 control-label">Session (<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>) </label>
                            <div class="col-md-3">
                                @php  $sessions->prepend('Select Session', ''); @endphp
                                {!! Form::select('session_id',$sessions, old('session_id'),['class'=>'form-control','required'=>'required']) !!}<i></i>
                            </div>
                        </div>


                        <div class="form-group">
                            <label class="col-md-1 control-label">Teacher</label>
                            <div class="col-md-3">
                                @php  $teacher->prepend('Select Teacher', ''); @endphp
                                {!! Form::select('teacher_id',$teacher, old('teacher_id'),['class'=>'form-control']) !!}<i></i>
                            </div>
                            <label class="col-md-1 control-label">Paper</label>
                            <div class="col-md-3">
                                {!! Form::select('paper',$papers, old('paper')?old('paper'):'' ,['class'=>'form-control']) !!}<i></i>
                            </div>
                            <label class="col-md-1 control-label">Exam Type (<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>) </label>
                            <div class="col-md-3">
                                @php  $exam_type->prepend('Select Exam Type', ''); @endphp
                                {!! Form::select('exam_type_id',$exam_type, old('exam_type_id'),['class'=>'form-control','required'=>'required']) !!}<i></i>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-1 control-label">Displaying Exam Name (<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>) </label>
                            <div class="col-md-11">
                                <div class="input-icon right">
                                    <input type="text" name="name" required value="{{ old('name') }}" class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Information Details (<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>) </label>
                                    <div class="col-md-10">
                                        <div class="input-icon right">
                                            <textarea name="description" class="form-control"  style="min-height: 200px; min-width: 100%; max-width: 100%" required></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
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
                                        <label class="col-md-2 control-label">Class
                                            {{--                                    (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) --}}
                                        </label>
                                        <div class="col-md-10">
                                            <div class="input-icon right">
                                                <select name="class" class="form-control class-selection"></select>
                                            </div>
                                        </div>
                                    </div>

                                @endif
                            </div>
                        </div>


{{--                        <div class="form-group">--}}
{{--                            <label class="col-md-1 control-label">Institute (<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>) </label>--}}
{{--                            <div class="col-md-2">--}}
{{--                                @php  $institute->prepend('Select Institute', ''); @endphp--}}
{{--                                {!! Form::select('institute_id',$institute, old('institute_id'),['class'=>'form-control','required'=>'required']) !!}<i></i>--}}
{{--                            </div>--}}
{{--                            <div class="course"></div>--}}
{{--                            <div class="faculty"></div>--}}
{{--                        </div>--}}





                        <div class="form-group">
                            <label class="col-md-1 control-label">SIF Only ? (<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>) </label>
                            <div class="col-md-1">
                                {!! Form::select('sif_only', ['Yes' => 'Yes', 'No' => 'No'], old('sif_only')?old('sif_only'):'Yes',['class'=>'form-control','required'=>'required']) !!}<i></i>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-1 control-label">Question Type (<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>) </label>
                            <div class="col-md-3">
                                @php  $question_type->prepend('Select Question Type', ''); @endphp
                                {!! Form::select('question_type_id',$question_type, old('question_type_id')?old('question_type_id'):'' ,['class'=>'form-control','required'=>'required']) !!}<i></i>
                            </div>


                            <div class="question_type_info">

                            </div>


                        </div>



                        <div class="mcq-sba">

                        </div>

                        <div class="form-group">
                            <label class="col-md-1 control-label">Is Free? (<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>) </label>
                            <div class="col-md-1">
                                {!! Form::select('is_free', ['1' => 'Yes', '0' => 'No'], old('is_free')?old('is_free'):'0',['class'=>'form-control','required'=>'required']) !!}<i></i>
                            </div>
                            <label class="col-md-1 control-label">Status (<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>) </label>
                                <div class="col-md-3">
                                    {!! Form::select('status', ['1' => 'Active','0' => 'InActive'], old('status'),['class'=>'form-control','required'=>'required']) !!}<i></i>
                                </div>
                            </div>
                        </div>

                       <div class="form-group">
                            <label class="col-md-1 control-label">Exam Details </label>
                            <div class="col-md-11">
                                <textarea name="exam_details" class="form-control">{{ old('exam_details') }}</textarea>
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

    <script type="text/javascript">

        // $(document).ready(function() {
        //     $('.class-selection').select2({
        //         minimumInputLength: 3,
        //         escapeMarkup: function (markup) { return markup; },
        //         ajax: {
        //
        //             url: '/admin/search-classes',
        //             dataType: 'json',
        //             type: "GET",
        //             quietMillis: 50,
        //
        //             data: function (term) {
        //                 return {
        //                     term: term
        //                 };
        //             },
        //
        //             processResults: function (data) {
        //                 return {
        //                     results: $.map(data, function (item) {
        //                         return { id:item.id , text: item.name };
        //                     })
        //                 };
        //             }
        //
        //         }
        //     });
        // });

    </script>

    @include('admin.exam.script');

@endsection
