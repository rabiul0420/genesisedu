@extends('layouts.app')

@section('content')
    <style>
        .page-breadcrumb {
            display: inline-block;
            float: left;
            padding: 8px;
            margin: 0;
            list-style: none;
        }

        .page-breadcrumb>li {
            display: inline-block;
        }

        .page-breadcrumb>li>a,
        .page-breadcrumb>li>span {
            color: #666;
            font-size: 14px;
            text-shadow: none;
        }

        .page-breadcrumb>li>i {
            color: #999;
            font-size: 14px;
            text-shadow: none;
        }

        .page-breadcrumb>li>i[class^="icon-"],
        .page-breadcrumb>li>i[class*="icon-"] {
            color: gray;
        }

        .bg {
            background: #a6ecc5;
            color: #0f77b7;
        }

    </style>

    <div class="container">


        <div class="row">

            @include('side_bar')


            {{-- $updated_schedules --}}

            <div class="col-md-9">
                <div class="panel panel-default pt-2">
                    <div class="panel_box w-100 bg-white rounded shadow-sm">
                        <div class="header text-center py-3">
                            <h2 class="h2 brand_color">{{ $doctor_course->batch->name }}</h2>
                            
                        </div>
                    </div>

                    <div class="panel-body">
                        @if (session('status'))
                            <div class="alert alert-success">
                                {{ session('status') }}
                            </div>
                        @endif


                        <div class="col-md-12 p-0">
                            <div class="portlet">
                                <div class="portlet-header"  style="text-align:center;font-weight:700;font-size:20px;background-color:#0f77b7;color:white;">
                                    <span >Schedule</span>
                                </div>
                                <div class="portlet-body">
                                    <style>
                                        .action-button
                                        {
                                            margin: 5px;
                                        }
                                        .front-panel th
                                        {
                                            border: 1px solid black;
                                            text-align: center;
                                            background-color:#428bca;
                                            color:white;

                                        }
                                        .front-panel td
                                        {
                                            border: 1px solid black;
                                            text-align: center;

                                        }
                                    </style>
                                    <table class="table table-bordered table-hover datatable front-panel">
                                        <thead>
                                            <th><span>&nbsp;</span><br><span>Date</span><br><span>&nbsp;</span></th>
                                            @php $max_slots = $doctor_course->max_slots(); $ma = $max_slots; @endphp
                                            @for($ma;$ma>0;$ma--)
                                            <th><span>Room</span><br><span>Start Time - End Time</span><br><span>Program</span></th>
                                            @endfor
                                        </thead>
                                        <tbody>
                                            <style>
                                                .custom-video-link
                                                {
                                                    margin: 7px;
                                                    padding: 7px;
                                                    border: 3px solid magenta;
                                                    border-radius: 15px;

                                                }

                                                .custom-live-link
                                                {
                                                    margin: 7px;
                                                    padding: 7px;
                                                    border: 3px solid blue;
                                                    border-radius: 15px;

                                                }

                                                .custom-exam-link
                                                {
                                                    margin: 7px;
                                                    padding: 7px;
                                                    border: 3px solid blueviolet;
                                                    border-radius: 15px;

                                                }

                                                .custom-view-result-link , .custom-view-result-link:hover, .custom-view-result-link:visited
                                                {
                                                    margin: 7px;
                                                    padding: 7px;
                                                    border: 3px solid green;
                                                    border-radius: 15px;
                                                    background-color:green;
                                                    color:white;
                                                    text-decoration: none;
                                                    cursor: pointer;

                                                }
                                            </style>
                                            @foreach($doctor_course->array_custom_slot_list() as $k=>$array_slot)
                                                @php $m = 0; $date = explode("-",$k) ;@endphp
                                                <tr>
                                                    <td><b>{{ $date[2].'-'.$date[1].'-'.$date[0] }}</b></td>
                                                    @foreach($array_slot as $l=>$slot)
                                                    <td>                                                    
                                                    <span>{{ $slot->room_name() }}</span><br><span><b>{{ $slot->time_span() }}</b></span><br><br>
                                                    @if(isset($slot->program))
                                                    <span>{{ $slot->program->name ?? '' }}</span><br>
                                                    @if(in_array('Recorded', $slot->program->media_types()))
                                                    @foreach($slot->program->lecture_videos() as $lecture_video)
                                                    <span><a class="custom-video-link" href="{{ url('doctor-course-schedule-lecture-video/'.$lecture_video->id.'/'.$doctor_course->id) }}">{{ $lecture_video->name ?? '' }} <i class="fa fa-play-circle-o" style="font-size:larger;"></i></a></span><br>  
                                                    @endforeach
                                                    @endif
                                                    @if(in_array('Online', $slot->program->media_types()))
                                                    @foreach($slot->program->exams() as $exam)
                                                    @if($doctor_course->completed_exam($exam))
                                                    <span><a class="custom-view-result-link" href="{{ url('course-exam-result/'.$doctor_course->id.'/'.$exam->id) }}" target="_blank">View Exam Result <i class="fa fa-graduation-cap" style="font-size:larger;"></i></a></span><br>  
                                                    @else
                                                    <span><a class="custom-exam-link" href="{{ url('doctor-course-exam/'.$doctor_course->id.'/'.$exam->id) }}">{{ $exam->name ?? '' }} <i class="fa fa-graduation-cap" style="font-size:larger;"></i></a></span><br>
                                                    @endif
                                                    @endforeach
                                                    @endif
                                                    @if(in_array('Live', $slot->program->media_types()) && $slot->slot->room->live_link )
                                                    <span ><a class="custom-live-link" href="{{ $slot->slot->room->live_link }}">{{ $slot->slot->room->name ?? '' }} <span class="fa-stack fa-1x"> <i class="fa fa-television fa-stack-2x"></i> <i class="fa fa-users fa-stack-1x"></i></span></a></span><br>
                                                    @endif
                                                    @if(in_array('Offline', $slot->program->media_types()))                                                    
                                                    @endif                                                    
                                                    @endif  
                                                    </td>
                                                    @php $m++ @endphp
                                                    @endforeach

                                                    @if($m < $max_slots )
                                                    @for($m;$m < $max_slots;$m++)
                                                    <td></td>
                                                    @endfor
                                                    @endif
                                                </tr>
                                            @endforeach

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>


        </div>


    </div>


@endsection

@section('js')
    <script type="text/javascript">
        $(document).ready(function() {

        })
    </script>
@endsection
