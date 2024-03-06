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

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table .replies p{
            margin-top: 0;
        }

        table thead tr th {
            border-top: 1px solid grey;
            border-bottom: 1px solid grey;
        }

        table th, table td {
            padding: 10px 8px;
        }

        @page {
            size: A4;
            margin-left: 40px;
            margin-top:20px;
            margin-right:40px;
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

    </style>
</head>

<body>
    <div style="width:1000px; overflow:auto; border-bottom:5px #000000 solid; margin:0 auto; padding-top:5px; border-radius:10px;">
        @php
            $course_name = '';
            $batch_name = '';
            $session_name = '';
            if( $complain_list->count() ){
                $course_name = $complain_list->first()->course_name;
                $batch_name = $complain_list->first()->batch_name;
                $session_name = $complain_list->first()->session_name;
            }

        @endphp


        <div style="width: 100%; text-align: center">
            <h1>Print (Question Box)</h1>
            <h2>Lecture Name: {{ $lecture_name }}</h2>
            <h3>Course: {{ $course_name }}</h3>
            <h3>Batch: {{ $batch_name }}</h3>
            <h3>Session: {{ $session_name }}</h3>
            <h4>Question Collection Duration: {{$start_date}} - {{$end_date}} </h4>
        </div>

        <table>
            <thead>
                <tr>
                    <th style="text-align: left">Doctor Name</th>
                    <th>Conversation</th>
                </tr>
            </thead>

{{--
    'da.id as doctor_ask_id',
    'd.name as doctor_name',
    'c.name as course_name',
    'b.name as batch_name',
    's.name as session_name',
    'lv.name as lecture_video',
    'da.has_feedback as has_feedback',
    'dc.year as year'
--}}

            <tbody>
                @foreach( $complain_list as $complain )
                <tr>
                    <td style="vertical-align: top; border-bottom: 1px solid #E5E5E5">{{$complain->doctor_name}}</td>
                    <td style="vertical-align: top; border-bottom: 1px solid #E5E5E5">
                        <div class="replies">
                            @if( $complain->replies && $complain->replies->count())
                                @foreach( $complain->replies  as $reply)
                                    <p style="margin-top: 0">{!! $reply->message !!}</p>
                                @endforeach
                            @else
                                <div style="color: #AAA; font-style: italic; font-size: 12px">No Messages Found</div>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>
