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

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        table .replies p {
            margin-top: 0;
        }

        table thead tr th {
            border-top: 1px solid grey;
            border-bottom: 1px solid grey;
        }

        table th,
        table td {
            padding: 10px 8px;
        }

        table td {
            overflow-wrap: break-word;
            font-size: 12px
        }

        @page {
            size: A4;
            margin-left: 40px;
            margin-top: 20px;
            margin-right: 40px;
        }

        @media print {

            table {
                width: 100%;
            }

            html,
            body {
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
@php
$institute = isset($course_reg_no->institute->name) ? $course_reg_no->institute->name : '';
@endphp

<body>
    <div class="" style="width:1000px; padding-top:5px; border-radius:10px;">

        <div style="margin:0 auto; text-align: center">
            <div>
                <h1>GENESIS</h1>
                <h4>Medical Post Graduation Orientation Center</h4>
                <p style="font-size:20px;">Doctor Result Sheet</p>
            </div>
            <div style="float: left ;text-align:left">
                <h4 >Doctor Name: <span style="font-weight:400;">{{ $course_reg_no->doctor->name ?? '' }}</span></h4>
                <h4>Phone: <span style="font-weight:400;">{{ $course_reg_no->doctor->mobile_number ?? ' ' }} </span></h4>
                <h4>BMDC: <span style="font-weight:400;">{{ $course_reg_no->doctor->bmdc_no ?? '' }}</span></h4>
                <h4>Course Registration: <span style="font-weight:400;">{{ $course_reg_no->reg_no ?? '' }}</span></h4>
            </div>
            <div style="float: right;text-align:left">
                <h4>Discipline: <span style="font-weight:400;">{{ isset($results[0]->subject->name) ? $results[0]->subject->name : '' }}</span></h4>
                <h4>Batch: <span style="font-weight:400;">{{ isset($results[0]->batch->name) ? $results[0]->batch->name : '' }}</span></h4>
                <h4>Session: <span style="font-weight:400;">{{ isset($results[0]->exam->sessions->name) ? $results[0]->exam->sessions->name : '' }}
                </span></h4>
                <h4>Year: <span style="font-weight:400;">{{ isset($results[0]->exam->year) ? $results[0]->exam->year : '' }}</span></h4>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th style="text-align: left">SL</th>
                    <th style="width: 18%">Exam Name</th>
                    <th>Exam Date</th>
                    <th>Obtained Mark</th>
                    <th >Exam Highest</th>
                    <th>Overall Position</th>
                    <th>Discipline Position</th>
                    <th>Batch Position</th>
                    @if ($institute == 'BSMMU')
                        <th>Candidate Position</th>
                    @endif
                    @if ($institute == 'BCPS')
                        <!-- <th>Pass/Fail</th> -->
                    @endif
                    <th>Wrong Ans</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($results as $index => $result)
                    <tr>
                        <td>{{ $result->exam_id }}</td>
                        <td>{{ isset($result->exam->name) ? $result->exam->name : '' }}</td>
                        <td>{{ isset($result->exam->created_at) ? $result->exam->created_at->format('d M Y') : '' }}
                        </td>
                        <td style="text-align:center">{{ $result->obtained_mark }}</td>
                        <td style="text-align:center">
                            @php
                                //Exam Highest mark
                                $temp_name = \App\Result::select('*')
                                    ->where('exam_id', $result->exam_id)
                                    ->orderBy('obtained_mark', 'desc')
                                    ->take(1)
                                    ->get();
                                foreach ($temp_name as $exam_top) {
                                    echo $exam_top->obtained_mark;
                                }
                            @endphp
                        </td>
                        <td style="text-align:center">
                            {{ $result->overallPosition() }}
                        </td>
                        <td style="text-align:center" >
                            {{ $result->getDisciplinePosition() }}
                        </td>
                        <td style="text-align:center">
                            {{ $result->getBatchPosition() }}
                        </td>
                        @if ($institute == 'BSMMU')
                        <td style="text-align:center">
                            {{ $result->getCandidatePosition() }}
                        </td>
                        @endif
                        <td style="text-align:center">
                            {{ $result->wrong_answers }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>

</html>
