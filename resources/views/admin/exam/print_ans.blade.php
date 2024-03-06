<!DOCTYPE html>
<html>
<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Print Exam</title>

    <style>
        body {
            width: 100%;
            height: 100%;
            margin: 0;
            padding: 0;
            background-color: #FAFAFA;
            font: 12pt "Tahoma";
        }
        * {
            box-sizing: border-box;
            -moz-box-sizing: border-box;
        }
        .page {
            width: 210mm;
            min-height: 297mm;
            padding: 20mm;
            margin: 10mm auto;
            border: 1px #D3D3D3 solid;
            border-radius: 5px;
            background: white;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        }
        .subpage {
            padding: 1cm;
            border: 5px red solid;
            height: 257mm;
            outline: 2cm #FFEAEA solid;
        }

        @page {
            size: A4;
            margin-left: 40px;
            margin-top:20px;
            margin-right:20px;
        }
        @media print {
            html, body {
                width: 210mm;
                height: 297mm;
            }
            .page {
                margin: 0px;
                border: initial;
                border-radius: initial;
                width: initial;
                min-height: initial;
                box-shadow: initial;
                background: initial;
                page-break-after: always;
            }
        }

        p {
            margin-block-start: 2px;
            margin-block-end: 2px;
        }

    </style>
</head>

<body>

<div style="width:1000px; overflow:auto; border-bottom:5px #000000 solid; margin:0 auto; padding-top:5px; border-radius:10px;">

    <div style="float:left; width:200px; border:0px #000000 solid; text-align:center; padding-top:100px;">
        <span style="font-family:Verdana; font-size:15px; font-weight:bold;">Total Mark : {{ $exam->question_type->full_mark ?? '' }}</span><br>
        <span style="font-family:Verdana; font-size:15px; font-weight:bold;">@if (isset($exam->question_type->pass_mark)) {{ "Pass Mark : " . $exam->question_type->pass_mark }} @endif</span>
    </div>
    <div style="float:left; width:600px; border:0px #000000 solid; text-align:center; height:160px; line-height:170%;">
        <span style="font-family:Cooper; font-size:30px;">GENESIS</span><br>
        <span style="font-family:Verdana; font-size:16px;">(Post Graduation Medical orientation Centre)<br><b>Exam : {{ $exam->name ?? '' }}</b><br></span>
        <span style="font-family:Verdana; font-size:16px;">

            Class/Chapter :
            <?php
                $temp_name = \App\Exam_topic::select('*')->where('exam_id', $exam->id)->get();
                foreach ($temp_name as $topic_ids){
                    $temp_name = \App\Topics::select('*')->where('id', $topic_ids->topic_id)->get();
                    foreach ($temp_name as $topics){
                        echo $topics->name.", ";
                    }
                }
                ?>
        </span><br>
        <span style="font-family:Verdana; font-size:16px;">
            @if($exam->question_type->sba_number ?? 0)
            Question {{ ($exam->question_type->mcq_number ?? 0) + 1 }} to End is Based on Single Answers
            @endif
        </span><br>
    </div>
    <div style="float:left; width:200px; border:0px #000000 solid; text-align:center; padding-top:100px;">
        <span style="font-family:Verdana; font-size:15px; font-weight:bold;">Time : {{ $exam->question_type->duration/60 }} Min</span><br>
        <span style="font-family:Verdana; font-size:15px; font-weight:bold;">Date : {{ $exam->exam_date }}</span>
    </div>

</div>

<div style="width:1000px; overflow:auto; border:0px #000000 solid; margin:0 auto; margin-top:0px; padding-top: 8px; font-family:Verdana; font-size:14px; font-weight:normal;">

    @foreach ($exam->exam_questions as $exam_question)
    <div style="float:left; width:500px; border:0px #000000 solid; text-align:left; padding-right:10px; height:auto; margin-bottom: 20px; overflow:auto;">

        <div style="display: flex; align-items: flex-start; gap: 5px;">
            <b>{{ $loop->iteration }}.</b>
            <div>
                {!! $exam_question->question->title !!}
            </div>
        </div>

        <div style="padding-left: 8px;">
            @foreach($exam_question->question->question_answers as $option)
                <div>{!! $option->title !!}</div>
            @endforeach
        </div>
            
        <div>
           <b>{!! $exam_question->question->script !!}</b>
        </div>

        <br>
            
        <div>
            {!! $exam_question->question->discussion !!}
        </div>
        
        <br>
            
        <div>
            {!! $exam_question->question->reference !!}
        </div>
    </div>
    @endforeach
</div>

</body>
</html>
