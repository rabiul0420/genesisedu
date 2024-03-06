@extends('admin.layouts.print')

@section('content')

    @if( Session::has( 'message' ) )
        <div class="alert {{ (Session::get('class'))?Session::get('class'):'alert-success' }}" role="alert">
            <p> {!! Session::get('message') !!}</p>
        </div>
    @endif

    <div class="row">

        <div class="portlet-body" id="printableArea">
           
            <style>
                table.result-data th {
                    text-align: center;
                    background-color: #EEE;
                }
                table.result-data th, table.result-data td {
                    padding: 15px 3px;
                }

                .italic {
                    font-style: italic;
                }

            </style>

            <table class="result-data" border="0" width=650px" align="center" cellpadding="0" cellspacing="0">
                <tr>
                    <td colspan="2"><span style="font-family:Verdana; font-size:40px; font-weight:bold;">GENESIS</span></td>
                </tr>

                <tr>
                    <td colspan="2"><span style="font-size:18px;"><u><b>Post Graduation Medical Orientation Centre</b></u></span></td>
                </tr>

                <tr>
                    <td width="50%"><b>Batch Name:</b> {{ $batch_schedule->batch->name ?? '' }}</td>
                    <td width="50%"><b>Year:</b> {{ $batch_schedule->year ?? '' }}</td>
                </tr>
                
                <tr>
                    <td width="50%"><b>Course:</b> {{ $batch_schedule->course->name ?? '' }}</td>
                    <td width="50%"><b>Session:</b> {{ $batch_schedule->session->name ?? '' }}</td>
                </tr>

                <tr>
                    @if( isset( $batch_schedule->faculty->name ) && !empty( $batch_schedule->faculty->name ) )
                        <td>
                            <span><b>Faculty: </b></span>
                            <span>{{ $batch_schedule->faculty->name }}</span>
                        </td>
                    @endif

                    @if( isset( $batch_schedule->subject->name ) && !empty($batch_schedule->subject->name) )
                        <td>
                            <span><b>Discipline: </b></span>
                            <span>{{ $batch_schedule->subject->name }}</span>
                        </td>
                    @endif

                    @if( isset( $batch_schedule->bcps_subject->name ) && !empty($batch_schedule->bcps_subject->name) )
                        <td>
                            <span><b>FCPS Part-1 Discipline: </b></span>
                            <span>{{ $batch_schedule->bcps_subject->name }}</span>
                        </td>
                    @endif
                </tr>
            </table> <br>

            <table class="result-data" border="1" width="650px" align="center" cellpadding="0" cellspacing="0">
                <thead>
                    <tr>
                        <th width="15%">Date</th>
                        <th width="10%">
                            <div>Exam</div>
                            <div>(Duration)</div>
                        </th>
                        <th width="25%">Solve Class</th>
                        <th width="25%">Lecture</th>
                        <th width="25%">Feedback Class</th>
                    </tr>
                </thead>

                <tbody>
                    @if($batch_schedule->time_slots instanceof \Illuminate\Support\Collection)
                        @foreach($batch_schedule->time_slots as $slot)

                            @php 
                                $contents = [];
                                
                                $exams = $slot->schedule_details->where('type', 'Exam')->values()->all();
                                $classes = $slot->schedule_details
                                    ->where('type', 'Class')
                                    ->where('parent_id', 0)->values()->all();

                                $classLength = count( $classes );
                                
                                // array_walk( $classes, function( &$item ){
                                //     $item = $item->toArray();
                                // });

                                
                                
                                $loopLength = count( $exams ) > count( $classes ) ? count( $exams ) : count( $classes );
                                
                                
                                //echo $loopLength;
                                

                                for( $i = 0; $i < $loopLength; $i++  ) {
                                    
                                    $contents[] = [
                                        'exam' => $exams[$i]->exam ?? null,
                                        'solve_class' => $exams[$i]->lectures[0]->video ?? null,
                                        'lecture' => $classes[$i]->video ?? null,
                                        'feedback_class' => $classes[$i]->lectures[0]->video ?? null,
                                    ];
                                    
                                }
                                // echo   '<pre>' . print_r( $contents , true   ). '</pre>';
                            
                            @endphp

                        @foreach( $contents as $ind => $content  )
                                    @php 
                                        $exam = $content['exam'] ?? null; 
                                        $lecture =  $content[ 'lecture' ] ?? null;
                                        $solve_class =  $content[ 'solve_class' ] ?? null;
                                        $feedback_class =  $content[ 'feedback_class' ] ?? null;
                                    @endphp

                                    <tr>
                                        @if( $ind == 0 )
                                            <td rowspan="{{ $loopLength }}">{{$slot->datetime->format('d - M Y l h:i a')}}</td>
                                        @endif


                                        @if( $ind == 0 )
                                            <td {!! $classLength > 1 && $ind == 0  ? 'rowspan="'.$classLength.'"':'' !!}>
                                                {{-- Exam --}}
                                                @if( $exam )
                                                    @php 
                                                        $duration = $exam->question_type->duration ?? 0;
                                                        
                                                        $startTime = $slot->datetime;
                                                        $endTime = $slot->datetime->addSeconds($duration)

                                                    @endphp
                                                    <div>
                                                        {{ $exam->name ?? 'NO EXAM' }}
                                                    </div>
                                                    <div>
                                                        ({{ $startTime->format('h:i a') }} - {{ $endTime->format('h:i a') }})
                                                    </div>
                                                @else
                                                    <div class="text-muted italic">NO EXAM</div>
                                                @endif

                                            </td>
                                        @endif

                                        @if( $ind == 0 )
                                            <td {!! $classLength > 1 && $ind == 0  ? 'rowspan="'.$classLength.'"':'' !!}>
                                                {{-- Solve Class --}}
                                                <div>{!! $solve_class->name ?? '<span class="text-muted italic">NO CLASS</span>' !!}</div>
                                            </td>
                                        @endif


                                        <td>
                                            {{-- Lecture --}}
                                            <div>{!! $lecture->name ?? '<span class="text-muted italic">NO CLASS</span>' !!}</div>
                                        </td>


                                        @if( $ind == 0 )
                                            <td {!! $classLength > 1 && $ind == 0  ? 'rowspan="'.$classLength.'"':'' !!}>
                                                {{-- Feedback Class --}}
                                                <div>{!! $feedback_class->name ?? '<span class="text-muted italic">NO CLASS</span>' !!}</div>
                                            </td>
                                        @endif


                                    </tr>
                                @endforeach
                        @endforeach
                    @endif
                </tbody>

            </table>
            <table class="result-data" border="0" width="500px" align="center" cellpadding="2" cellspacing="0">
                <tr>
                    <td colspan="2">
                        <font face="Verdana" size="2">N.B: Schedule can be changed in any emergency/unavoidable reason.</font><br>
                        <a href="http://genesisedu.info/" style="font-size:16px;">For Result Please Visit: www.genesisedu.info</a>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div style="border:1px #000000 solid; padding:3px; text-align:center; border-radius:10px">
                            <p><b>Address:</b> {{ $batch_schedule->address ?? '' }} <br>
                                <b>Room:</b> {{ $batch_schedule->room->room_name ?? '' }} <br>
                                <b>Contact:</b> {{ $batch_schedule->contact_details ?? '' }}</p>
                        </div>
                    </td>
                </tr>
            </table>
        </div>


    </div>

@endsection

@section('styles')

@endsection
@section('js')

<script>
    function printDiv(divName) {
     var printContents = document.getElementById(divName).innerHTML;
     var originalContents = document.body.innerHTML;

     document.body.innerHTML = printContents;

     window.print();

     document.body.innerHTML = originalContents;
}
</script>

@endsection