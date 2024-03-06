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
                <div class="portlet-body form">
                    <!-- BEGIN FORM-->

                    @if( $duplicate )
                        {!! Form::open(['action'=>['Admin\ExamController@store'],'files'=>true,'class'=>'form-horizontal']) !!}
                    @else
                        {!! Form::open(['action'=>['Admin\ExamController@update', $exam->id],'method'=>'PUT','files'=>true,'class'=>'form-horizontal']) !!}
                    @endif
                    <div class="form-body">
 
                        <div class="form-group">
                            <label class="col-md-1 control-label">Exam Date (<i class="fa fa-asterisk ipd-star"
                                    style="font-size:11px;"></i>) </label>
                            <div class="col-md-3">
                                <div class="input-icon right">
                                    <input type="text" name="exam_date" autocomplete="off" value="{{ $exam->exam_date }}"
                                        required class="form-control input-append date" id="datepicker">
                                </div>
                            </div>
                            <label class="col-md-1 control-label">Year (<i class="fa fa-asterisk ipd-star"
                                    style="font-size:11px;"></i>) </label>
                            <div class="col-md-3">
                                {!! Form::select('year',$years, $exam->year
                                ,['class'=>'form-control','required'=>'required','id'=>'year']) !!}<i></i>
                            </div>
                            <label class="col-md-1 control-label">Session (<i class="fa fa-asterisk ipd-star"
                                    style="font-size:11px;"></i>) </label>
                            <div class="col-md-3">
                                @php $sessions->prepend('Select Session', ''); @endphp
                                {!! Form::select('session_id',$sessions, $exam->session_id
                                ,['class'=>'form-control','required'=>'required']) !!}<i></i>
                            </div>
                        </div>


                        <div class="form-group">
                            <label class="col-md-1 control-label">Teacher</label>
                            <div class="col-md-3">
                                @php $teacher->prepend('Select Teacher', ''); @endphp
                                {!! Form::select('teacher_id',$teacher, $exam->teacher_id ,['class'=>'form-control'])
                                !!}<i></i>
                            </div>
                            <label class="col-md-1 control-label">Paper</label>
                            <div class="col-md-3">
                                {!! Form::select('paper',$papers, $exam->paper ,['class'=>'form-control']) !!}<i></i>
                            </div>
                            <label class="col-md-1 control-label">Exam Type (<i class="fa fa-asterisk ipd-star"
                                    style="font-size:11px;"></i>) </label>
                            <div class="col-md-3">
                                @php $exam_type->prepend('Select Exam Type', ''); @endphp
                                {!! Form::select('exam_type_id',$exam_type,
                                $exam->exam_type_id,['class'=>'form-control','required'=>'required']) !!}<i></i>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-1 control-label">Displaying Exam Name (<i class="fa fa-asterisk ipd-star"
                                    style="font-size:11px;"></i>) </label>
                            <div class="col-md-11">
                                <div class="input-icon right">
                                    <input type="text" name="name" required value="{{ $exam->name }}" class="form-control">
                                </div>
                            </div>
                        </div>


                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Information Details (<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>) </label>
                                    <div class="col-md-10">
                                        <div class="input-icon right">
                                            <textarea name="description" class="form-control"  style="min-height: 200px; min-width: 100%; max-width: 100%" required>{{ $exam->description ?? '' }}</textarea>
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
                                                <select name="class" class="form-control class-selection">
                                                    @if(isset($exam->topic))
                                                        <option value="{{$exam->topic->id}}">{{$exam->topic->name}}</option>
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                @endif
                            </div>
                        </div>

{{--                        <div class="form-group">--}}
{{--                            <label class="col-md-1 control-label">Information Details (<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>) </label>--}}
{{--                            <div class="col-md-3">--}}
{{--                                <div class="input-icon right">--}}
{{--                                    <textarea name="description" class="form-control" cols="10" rows="4" required>{{ $exam->description ?? '' }}</textarea>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </div>--}}

{{--                        <div class="form-group">--}}
{{--                            <label class="col-md-1 control-label">Institute (<i class="fa fa-asterisk ipd-star"--}}
{{--                                    style="font-size:11px;"></i>) </label>--}}
{{--                            <div class="col-md-2">--}}
{{--                                @php $institute->prepend('Select Institute', ''); @endphp--}}
{{--                                {!! Form::select('institute_id',$institute,--}}
{{--                                $exam->institute_id,['class'=>'form-control','required'=>'required']) !!}<i></i>--}}
{{--                            </div>--}}


{{--                            @if( $has_class_id_column )--}}

{{--                                <div class="form-group">--}}
{{--                                    <label class="col-md-3 control-label">Class--}}
{{--                                        (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) --}}
{{--                                    </label>--}}
{{--                                    <div class="col-md-3">--}}
{{--                                        <div class="input-icon right">--}}
{{--                                            <select name="class" class="form-control select2">--}}
{{--                                                <option value="{{$exam->topic->id ?? ''}}" selected="selected">{{$exam->topic->name ?? ''}} </option>--}}
{{--                                            </select>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                </div>--}}

{{--                            @endif--}}


{{--                            <div class="course">--}}
{{--                                <label class="col-md-1 control-label">Course (<i class="fa fa-asterisk ipd-star"--}}
{{--                                        style="font-size:11px;"></i>) </label>--}}
{{--                                <div class="col-md-2">--}}
{{--                                    @php $course->prepend('Select Course', ''); @endphp--}}
{{--                                    {!! Form::select('course_id',$course,--}}
{{--                                    $exam->course_id,['class'=>'form-control','required'=>'required']) !!}<i></i>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                            <div class="faculty">--}}
{{--                                @if($institute_type==1)--}}
{{--                                <label class="col-md-1 control-label">Faculty</label>--}}
{{--                                <div class="col-md-2">--}}
{{--                                    @php $faculty->prepend('Select Faculty', ''); @endphp--}}
{{--                                    {!! Form::select('faculty_id',$faculty, $exam->faculty_id,['class'=>'form-control'])--}}
{{--                                    !!}<i></i>--}}
{{--                                </div>--}}
{{--                                @endif--}}
{{--                            </div>--}}

{{--                        </div>--}}


{{--                        <div class="form-group topics">--}}
{{--                            <label class="col-md-1 control-label">Add Class/Chapter</label>--}}
{{--                            <div class="col-md-8">--}}
{{--                                {!! Form::select('topic_id[]',$topic, $topic_ids,['class'=>'form-control topic2','multiple','required'=>'required']) !!}<i></i>--}}
{{--                            </div>--}}
{{--                        </div>--}}

                        <div class="form-group">
                            <label class="col-md-1 control-label">SIF Only ? (<i class="fa fa-asterisk ipd-star"
                                    style="font-size:11px;"></i>) </label>
                            <div class="col-md-1">
                                {!! Form::select('sif_only', ['Yes' => 'Yes', 'No' => 'No'],
                                $exam->sif_only,['class'=>'form-control','required'=>'required']) !!}<i></i>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-1 control-label">Question Type (<i class="fa fa-asterisk ipd-star"
                                    style="font-size:11px;"></i>) </label>
                            <div class="col-md-3">
                                @php $question_types->prepend('Select Question Type', ''); @endphp
                                {!! Form::select('question_type_id',$question_types, $exam->question_type_id
                                ,['class'=>'form-control','required'=>'required']) !!}<i></i>
                            </div>
                        </div>

                        <div class="mcq-sba">

                            @include( 'admin.ajax.question_type_mcq_sba' )

                            {{--                            @if($question_type->mcq_number )--}}
                            {{--                                <div class="form-group">--}}
                            {{--                                    <label class="col-md-1 control-label">Add MCQs (<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>) </label>--}}
                            {{--                                    <div class="col-md-11">--}}
                            {{--                                        {!! Form::select('mcq_question_id[]',$mcqs, $mcqs_ids,['class'=>'form-control mcqs2','multiple','required'=>'required']) !!}<i></i>--}}
                            {{--                                        <span style="color:red" class="mcq_count"></span>--}}
                            {{--                                        <input type="hidden" name="mcq_count" value="{{ $question_type->mcq_number }}">--}}
                            {{--                                        <input type="text" class="hidden" name="mcq_full">--}}
                            {{--                                    </div>--}}
                            {{--                                </div>--}}
                            {{--                            @endif--}}

                            {{--                            @if($question_type->sba_number)--}}
                            {{--                                <div class="form-group">--}}
                            {{--                                    <label class="col-md-1 control-label">Add SBAs (<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>) </label>--}}
                            {{--                                    <div class="col-md-11">--}}
                            {{--                                        {!! Form::select('sba_question_id[]',$sbas, $sbas_ids,['class'=>'form-control sbas2','multiple','required'=>'required']) !!}<i></i>--}}
                            {{--                                        <span style="color:red" class="sba_count"></span>--}}
                            {{--                                        <input type="hidden" name="sba_count" value="{{ $question_type->sba_number }}">--}}
                            {{--                                        <input type="text" class="hidden" name="sba_full">--}}
                            {{--                                    </div>--}}
                            {{--                                </div>--}}
                            {{--                            @endif--}}

                        </div>

                        <div class="form-group">

                            <label class="col-md-1 control-label">Is Free? (<i class="fa fa-asterisk ipd-star"
                                    style="font-size:11px;"></i>) </label>
                            <div class="col-md-1">
                                {!! Form::select('is_free', ['1' => 'Yes', '0' => 'No'],
                                old('is_free')?old('is_free'):'0',['class'=>'form-control','required'=>'required'])
                                !!}<i></i>
                            </div>
                            <label class="col-md-1 control-label">Status (<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>) </label>
                            <div class="form-group">
                                <label class="col-md-3 control-label">Select Status (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                                <div class="col-md-3">
                                    @if( $exam->status == 2 )
                                        {!! Form::select('status', ['1' => 'Active','2' => 'InActive'], $exam->status,['class'=>'form-control','required'=>'required']) !!}
                                    @else
                                        {!! Form::select('status', ['1' => 'Active','0' => 'InActive'], $exam->status,['class'=>'form-control','required'=>'required']) !!}
                                    @endif
 
                                </div>
                            </div>>
                        </div>

                        <div class="form-group">
                            <label class="col-md-1 control-label">Exam Details </label>
                            <div class="col-md-11">
                                <textarea name="exam_details" class="form-control">{{ $exam->exam_details }}</textarea>
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

        </div>
    </div>



@endsection

@section('js')



<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker.min.css" />
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker3.min.css" />
<script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/js/bootstrap-datepicker.min.js"></script>


<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/css/select2.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/js/select2.js" type="text/javascript"></script>


<script type="text/javascript">
    $(document).ready(function() {
        $('.select2').select2({
            minimumInputLength: 3,
            escapeMarkup: function (markup) { return markup; },
            ajax: {
                url: '/admin/search-classes',
                dataType: 'json',
                type: "GET",
                quietMillis: 50,
                data: function (term) {
                    return {
                        term: term
                    };
                },
                processResults: function (data) {
                    return {
                        results: $.map(data, function (item) {
                            return { id:item.id , text: item.name };
                        })
                    };
                }
            }
        });
    })
</script>



@include('admin.exam.script')


@endsection
