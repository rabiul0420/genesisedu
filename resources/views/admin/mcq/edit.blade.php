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
                        <i class="fa fa-reorder"></i>{{ $title }}
                    </div>
                </div>
                <div class="portlet-body form">
                    <!-- BEGIN FORM-->
                    {!! Form::open(['action'=>['Admin\McqController@update', $questions->id], 'method'=>'PUT', 'files'=>true, 'class'=>'form-horizontal']) !!}
                    <div class="form-body">

                        <div class="form-group">
                            <label class="col-md-2 control-label">MCQ Question Title (<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>) </label>
                            <div class="col-md-6">
                                <div class="input-icon right">
                                    <textarea name="question_title" required class="form-control">{{ $questions->question_title }}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-2 control-label">MCQ Answer (A) (<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>) </label>
                            <div class="col-md-5">
                                <div class="input-icon right">
                                    @php
                                        $qa = \App\Question_ans::select('id','answer', 'sl_no', 'correct_ans')->where('question_id',$questions->id)->where('sl_no', 'A')->first();
                                        echo "<input class=form-control type='text' name='question_a' required value='".$qa->answer."'>";
                                        echo "<input type='hidden' name='qa_id' value='".$qa->id."'>";
                                    @endphp
                                </div>
                            </div>
                            <div class="col-md-1">

                                @if ($qa->correct_ans=='T')
                                    <label class='radio-inline'><input type='radio' name='answer_a' value='T' checked > T </label> @else
                                    <label class='radio-inline'><input type='radio' name='answer_a' value='T' > T </label>
                                @endif
                                @if ($qa->correct_ans=='F')
                                    <label class='radio-inline'><input type='radio' name='answer_a' value='F' checked > F </label> @else
                                    <label class='radio-inline'><input type='radio' name='answer_a' value='F' > F </label>
                                @endif

                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-2 control-label">MCQ Answer (B) (<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>) </label>
                            <div class="col-md-5">
                                <div class="input-icon right">
                                    @php
                                        $qb = \App\Question_ans::select('id','answer', 'sl_no', 'correct_ans')->where('question_id',$questions->id)->where('sl_no', 'B')->first();
                                        echo "<input class=form-control type='text' name='question_b' required value='".$qb->answer."'>";
                                        echo "<input type='hidden' name='qb_id' value='".$qb->id."'>";
                                    @endphp
                                </div>
                            </div>
                            <div class="col-md-1">
                                @if ($qb->correct_ans=='T')
                                    <label class='radio-inline'><input type='radio' name='answer_b' value='T' checked > T </label> @else
                                    <label class='radio-inline'><input type='radio' name='answer_b' value='T' > T </label>
                                @endif
                                @if ($qb->correct_ans=='F')
                                    <label class='radio-inline'><input type='radio' name='answer_b' value='F' checked > F </label> @else
                                    <label class='radio-inline'><input type='radio' name='answer_b' value='F' > F </label>
                                @endif
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-2 control-label">MCQ Answer (C) (<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>) </label>
                            <div class="col-md-5">
                                <div class="input-icon right">
                                    @php
                                        $qc = \App\Question_ans::select('id','answer', 'sl_no', 'correct_ans')->where('question_id',$questions->id)->where('sl_no', 'C')->first();
                                        echo "<input class=form-control type='text' name='question_c' required value='".$qc->answer."'>";
                                        echo "<input type='hidden' name='qc_id' value='".$qc->id."'>";
                                    @endphp
                                </div>
                            </div>
                            <div class="col-md-1">
                                @if ($qc->correct_ans=='T')
                                    <label class='radio-inline'><input type='radio' name='answer_c' value='T' checked > T </label> @else
                                    <label class='radio-inline'><input type='radio' name='answer_c' value='T' > T </label>
                                @endif
                                @if ($qc->correct_ans=='F')
                                    <label class='radio-inline'><input type='radio' name='answer_c' value='F' checked > F </label> @else
                                    <label class='radio-inline'><input type='radio' name='answer_c' value='F' > F </label>
                                @endif
                            </div>
                        </div>
                         <div class="form-group">
                            <label class="col-md-2 control-label">MCQ Answer (D) (<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>) </label>
                            <div class="col-md-5">
                                <div class="input-icon right">
                                    @php
                                        $qd = \App\Question_ans::select('id','answer', 'sl_no', 'correct_ans')->where('question_id',$questions->id)->where('sl_no', 'D')->first();
                                        echo "<input class=form-control type='text' name='question_d' required value='".$qd->answer."'>";
                                        echo "<input type='hidden' name='qd_id' value='".$qd->id."'>";
                                    @endphp
                                </div>
                            </div>
                            <div class="col-md-1">
                                @if ($qd->correct_ans=='T')
                                    <label class='radio-inline'><input type='radio' name='answer_d' value='T' checked > T </label> @else
                                    <label class='radio-inline'><input type='radio' name='answer_d' value='T' > T </label>
                                @endif
                                @if ($qd->correct_ans=='F')
                                    <label class='radio-inline'><input type='radio' name='answer_d' value='F' checked > F </label> @else
                                    <label class='radio-inline'><input type='radio' name='answer_d' value='F' > F </label>
                                @endif
                            </div>
                        </div>
                         <div class="form-group">
                            <label class="col-md-2 control-label">MCQ Answer (E) (<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>) </label>
                            <div class="col-md-5">
                                <div class="input-icon right">
                                    @php
                                        $qe = \App\Question_ans::select('id','answer', 'sl_no', 'correct_ans')->where('question_id',$questions->id)->where('sl_no', 'E')->first();
                                        echo "<input class=form-control type='text' name='question_e' required value='".$qe->answer."'>";
                                        echo "<input type='hidden' name='qe_id' value='".$qe->id."'>";
                                    @endphp
                                </div>
                            </div>
                            <div class="col-md-1">
                                @if ($qe->correct_ans=='T')
                                    <label class='radio-inline'><input type='radio' name='answer_e' value='T' checked > T </label> @else
                                    <label class='radio-inline'><input type='radio' name='answer_e' value='T' > T </label>
                                @endif
                                @if ($qe->correct_ans=='F')
                                    <label class='radio-inline'><input type='radio' name='answer_e' value='F' checked > F </label> @else
                                    <label class='radio-inline'><input type='radio' name='answer_e' value='F' > F </label>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-2 control-label">Discussion</label>
                            <div class="col-md-6">
                                <div class="input-icon right">
                                    <textarea name="discussion" class="form-control">{{ $questions->discussion }}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-2 control-label">Reference</label>
                            <div class="col-md-6">
                                <div class="input-icon right">
                                    <textarea name="reference" class="form-control">{{ $questions->reference }}</textarea>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="form-actions">
                        <div class="row">
                            <div class="col-md-offset-2 col-md-9">
                                <button type="submit" class="btn btn-info">Submit</button>
                                <a href="{{ url('admin/mcq') }}" class="btn btn-default">Cancel</a>
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