<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>GENESIS : SIF</title>
    <link href="{{ asset('assets/css/print/print.css') }}" rel="stylesheet">
</head>
<body>


<table cellpadding="3" style="vertical-align: middle;">
    <tr class="print">
        <td align="right" colspan="3">
            <button type="button" onclick="window.print()">
                <img src=" {{ asset('print.png') }} " width="20" height="20" alt="" title="Print">
            </button>
        </td>
    </tr>

</table>

<table class="result-data" border="0" width="900px" align="center" cellpadding="0" cellspacing="0">
    <div style="float:left; width:600px; border:0px #000000 solid; text-align:center;">
        <span style="font-family:Cooper; font-size:30px;">GENESIS</span><br>
        <span style="font-family:Verdana; font-size:16px;">(Post Graduation Medical orientation Centre)<br><b>Exam : {{ $exam->name }}</b><br></span>
    </div>
    <div style="float:left; width:600px; border:0px #000000 solid; text-align:center; ">
        <p>Course: {{ $exam->course->name ?? '' }} @if($doctor_course->subject != null) Discipline: {{ $doctor_course->subject->name ?? '' }}@endif @if($doctor_course->faculty != null) Faculty: {{ $doctor_course->faculty->name ?? '' }}@endif</p>
        <p>Batch: {{ $doctor_course->batch->name ?? '' }}</p>
        <p>Year: {{ $doctor_course->batch->year ?? '' }} Session: {{ $exam->sessions->name ?? '' }}</p>
    </div>
</table>
<table class="result-data" width="900px;" style="border:none"  align="center" cellpadding="3" cellspacing="0"  border="1" style="border-collapse: collapse; font-size: 15px;">
    <thead></thead>
    @php $i=1; @endphp
    <tr align="left" style="border:none" >
        @foreach($exam->exam_questions as $exam_question)
            @php $i=0; @endphp
            <div id="question">
                @if(isset($exam_question->question->question_title))
                    <tr style="border:none">
                        <td colspan="4" style="border:none">
                            <h4 class='modal-title' id='myModalLabel'>{!!
                                '('.($exam->exam_questions->search($exam_question) + 1).' of
                                '.$exam->exam_questions->count().' )
                                '.$exam_question->question->question_title !!}</h4>
                        </td>
                    </tr>

                    @if($exam_question->question->type == "1")
                        @foreach($exam_question->question->question_answers as $k=>$answer)
                            {{-- @if($k<session('stamp')) --}}
                                <tr style="border:none">
                                    <td style="border:none">
                                        {!! isset($answer->answer)? $answer->answer:'' !!}
                                    </td>

                                </tr>
                            {{-- @endif --}}
                        @endforeach
                    @else
                        @foreach($exam_question->question->question_answers as $k=>$answer)
                            {{-- @if($k<session('stamp')) --}}
                                <tr style="border:none">
                                    <td style="border:none">
                                        {!! isset($answer->answer)? $answer->answer:'' !!}
                                    </td>
                                </tr>
                            {{-- @endif --}}
                        @endforeach
                    @endif
                @endif
            </div>
        @endforeach
    </tr>

    <tbody></tbody>

</table>

</body>
</html>

