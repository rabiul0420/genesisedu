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
            background: white;
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

        #company-name {
            font-weight: bold;
            margin: 0;
            padding: 0;
            font-size: 40px;
        }

        #slogan {
            padding-top: 0;
            margin-top: 0;
            margin-bottom: 30px;
        }

        #headings {
            display: flex; justify-content: center; flex-wrap: wrap;
            margin-bottom: 50px;
        }

        #headings p {
            margin: 0;
            margin-bottom:5px;
        }

        #headings .left {
            padding-right: 40px;
        }

        #headings .right {
            padding-left: 40px;
        }

        #headings .course-item-key {
            width: 120px;
            font-weight: bold;
            display: inline-block;
        }

        #headings .right .course-item-key {
            width: 90px;
        }

    </style>
</head>

<body>
    <div style="width:1000px; overflow:auto; margin:0 auto; padding-top:5px; ">

        @php

        @endphp


        <div style="width: 100%; text-align: center">
            <h1 id="company-name">GENESIS</h1>
            <p id="slogan">Post Graduation Medical Orientation Centre</p>
        </div>
        <div id="headings">
            <div class="left">
                <p>
                    <span class="course-item-key">Batch Name</span>
                    <span>{{ $doctor_course->batch->name ?? '' }}</span>
                </p>
                <p>
                    <span class="course-item-key">Branch Name</span>
                    <span>{{$doctor_course->branch->name ?? ''}}</span>
                </p>

            </div>
            <div class="right">
                <p>
                    <span class="course-item-key">Year</span>
                    <span>{{$doctor_course->year ?? ''}}</span>
                </p>
                <p>
                    <span class="course-item-key">Course</span>
                    <span>{{$doctor_course->course->name ?? ''}}</span>
                </p>
                <p>
                    <span class="course-item-key">Session</span>
                    <span>{{$doctor_course->session->name ?? ''}}</span>
                </p>

                @if( isset( $doctor_course->faculty->name ) && !empty( $doctor_course->faculty->name ) )
                    <p>
                        <span class="course-item-key">Faculty</span>
                        <span>{{ $doctor_course->faculty->name  }}</span>
                    </p>
                @endif

                @if( isset( $doctor_course->subject->name ) && !empty($doctor_course->subject->name) )
                    <p>
                        <span class="course-item-key">Discipline</span>
                        <span>{{ $doctor_course->subject->name  }}</span>
                    </p>
                @endif

                @if( isset( $doctor_course->bcps_subject->name ) && !empty($doctor_course->bcps_subject->name) )
                    <p>
                        <span class="course-item-key">FCPS Part-1 Discipline</span>
                        <span>{{ $doctor_course->bcps_subject->name  }}</span>
                    </p>
                @endif

            </div>
        </div>

        <div id="print-body">
            @foreach ( $scheduleTimeSlots as $time_slot )

                @if( isset( $time_slot->schedule_details ) && $time_slot->schedule_details->count()  )

                    <div class="container-fluid class-or-exam-contents">

                        <div class="row" style="margin-bottom: 25px;" >
                            <div class="col-10 col-md-8 col-lg-5">
                                <div class="col-12"> <span style="font-weight: bold">Date</span> {{  $time_slot->datetime->format('l, d-M-Y') }}</div> {{-- 'l, d-M-Y' --}}
                                <div class="col-12"> <span style="font-weight: bold">Time</span> {{  $time_slot->datetime->format('h:i A') }}</div> {{-- 'l, d-M-Y' --}}
                            </div>
                        </div>

                        @include( 'batch_schedule.print.time-slot-print', compact( 'time_slot' ) )
                        <hr class="mt-4 bottom">
                    </div>

                @endif
            @endforeach
        </div>


    </div>
</body>
</html>
