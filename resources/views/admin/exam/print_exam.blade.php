<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
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
            .print {
                display: none;
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

        .question-table .right{
            vertical-align : top;
            padding-top: 15px;            
            padding-right: 10px;
            text-align: right;
        }

        .question-table .left{
            vertical-align : top; 
            text-align: left;
        }

        .question-table-heading .right{
            vertical-align : top;            
            padding-right: 10px;
            text-align: right;
        }

        .question-table-heading .center{
            vertical-align : top; 
            text-align: center;
        }

        .question-table-heading .left{
            vertical-align : top; 
            text-align: left;
        } 

        

    </style>
    
</head>

<body>
<div style="width:1000px; overflow:auto; margin:0 auto; padding-top:5px; border-radius:10px;">
<table id="" cellpadding="3" style="vertical-align: middle;float:right;">
    <tr class="print">
        <td align="right" colspan="3">
            <button type="button" onclick="window.print()">
                <img src=" {{ asset('print.png') }} " width="20" height="20" alt="" title="Print">
            </button>
        </td>
    </tr>
</table>

</div>

<div style="width:1000px; overflow:auto; border-bottom:5px #000000 solid; margin:0 auto; padding-top:5px; border-radius:10px;">
<table class="question-table-heading" width="100%" cellspacing="0" cellpadding="0" style="border-collapse: collapse; font-size: 16px;font-family:Calibri, Arial, Verdana, Geneva, Tahoma, sans-serif;">
    
    <tr>        
        <td width="20%" class="left"></td>
        <td width="60%" class="center" style="font-family:Cooper; font-size:30px;">GENESIS</td>       
        <td width="20%" class="right"></td>                       
    </tr>
    <tr>        
        <td width="20%" class="left"></td>
        <td width="60%" class="center">(Post Graduation Medical orientation Centre)</td>       
        <td width="20%" class="right"></td>                       
    </tr>
    <tr>        
        <td width="20%" class="left"></td>
        <td width="60%" class="center"><b>Exam : {{ $exam->name }}</b></td>       
        <td width="20%" class="right"></td>                       
    </tr>
    <tr>        
        <td width="20%" class="left"></td>
        <td width="60%" class="center"><b>@php if ($exam->question_type->sba_number) {$qn=$exam->question_type->mcq_number+1; echo "Question ". $qn ." to End is Based on Single Answers";} @endphp</td>       
        <td width="20%" class="right"></td>                       
    </tr>
    
      
</table>
<table class="question-table-heading" width="100%" cellspacing="0" cellpadding="0" style="border-collapse: collapse; font-size: 16px;font-family:Calibri, Arial, Verdana, Geneva, Tahoma, sans-serif;">
    <tr>
        <td width="7%" class="left"><b>Year</b></td>
        <td width="23%" class="left"><b> : {{ $exam->year }}</b></td>
        <td width="51%" class="center" style="font-family:Cooper; font-size:30px;"></td>
        <td width="7%" class="left"><b>Time</b></td>
        <td width="10%" class="left"><b> : {{ $exam->question_type->duration ?  $exam->question_type->duration/60 . " Minutes" : "" }}</b></td>        
    </tr>
    <tr>
        <td width="7%" class="left"><b>Session</b></td>
        <td width="23%" class="left"><b> : {{ $exam->topic->session->name ?? '' }}</b></td>
        <td width="51%" class="center" style="font-family:Cooper; font-size:30px;"></td>
        <td width="7%" class="left"><b>Full Mark</b></td>
        <td width="10%" class="left"><b> : {{ $exam->question_type->full_mark ?? "" }}</b></td>
    </tr>
    <tr>
        <td width="7%" class="left"><b>Course</b></td>
        <td width="23%" class="left"><b> : {{ $exam->course->name }}</b></td>
        <td width="51%" class="center" style="font-family:Cooper; font-size:30px;"></td>
        <td width="7%" class="left"><b>Date</b></td>
        <td width="10%" class="left"><b> : {{ Date('d-m-Y') }}</b></td>
    </tr>
</table>
<br>
</div>

<div style="width:1000px; overflow:auto; border:0px #000000 solid; margin:0 auto; margin-top:0px; ">
<table class="question-table" width="100%" cellspacing="0" cellpadding="0" style="border-collapse: collapse; font-size: 16px;font-family:Calibri, Arial, Verdana, Geneva, Tahoma, sans-serif;">
    @php $k = 0; @endphp
    @if(isset($questions) && count($questions))
    @foreach($questions as $question)
    <tr>
        @if(isset($question['a']))
        <td width="5%" class="right"><b>{{ ++$k.' .' }}</b></td>
        <td width="45%" class="left">
            <b>{!! $question['a']->question_title !!}</b>
            @foreach($question['a']->question_answers as $answer)
            {!! $answer->answer !!}
            @endforeach 
        </td>
        @endif
        @if(isset($question['b']))
        <td width="5%" class="right"><b>{{ ++$k.' .' }}</b></td>
        <td width="45%" class="left">        
            <b>{!! $question['b']->question_title !!}</b>
            @foreach($question['b']->question_answers as $answer)
            {!! $answer->answer !!}
            @endforeach        
        </td>
        @endif               
    </tr>     
    @endforeach
    @endif  
</table>      
</div>

@php //echo $questions; @endphp

</body>
</html>
