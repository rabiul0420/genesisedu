<div id="{{$time_slot->id}}_time_slot_container">

    @foreach ( $time_slot->schedule_details as $detail )
        @if( $detail instanceof \App\ScheduleDetail )

            @php
                $feedback_or_solve_class = $detail->lectures[ 0 ] ?? new \App\ScheduleDetail( );
                $feedback_or_solve_class_disabled = $detail->feedback_or_solve_class_disabled( );
                $link_disabled = $detail->is_link_disabled(  );
                $rating_disabled = $detail->feedback_disabled(  );
            @endphp

            <div class="row" style="margin-bottom: 15px">

                <div style="display: flex; flex-direction: column; margin-bottom: 5px;">
                    <div>
                        <div class="badge bg-info" style="font-weight: bold">{{ $detail->type  }}</div>
                        <div class="text-info">
                            
                            {{ $detail->type == 'Class' ? ( $detail->video->description ?? '' )
                            : ( $detail->type == 'Exam' ? ( $detail->exam->description ?? '' ):'' ) }}
                            
                        </div> 
                    </div>
{{--                    <div>--}}
{{--                        <div class="badge bg-success" style="font-weight: bold; margin-top: 10px">Mentor</div>--}}
{{--                        <div class="text-success">--}}
{{--                            <div>{{ $detail->mentor->name ?? '' }}</div>--}}
{{--                            <div style="font-size: 12px;color: #34d95a">{{ $detail->mentor->designation ?? '' }}</div>--}}
{{--                        </div>--}}
{{--                    </div>--}}
                </div>

                @if( $feedback_or_solve_class->id )
                    <div  style="display: flex; flex-direction: column;margin-bottom: 5px; margin-top: 18px">
                        <div>
                            <div class="badge bg-info" style="font-weight: bold">{{ $detail->type == 'Class' ? 'Feedback ': 'Solve ' }} Class</div>
                            <div class="text-info">
                                {{ $feedback_or_solve_class->video->description ?? '' }}
                            </div>
                        </div>
{{--                        <div>--}}
{{--                            <div class="badge bg-success" style="font-weight: bold; margin-top: 10px">Mentor</div>--}}
{{--                            <div class="text-success">--}}
{{--                                <div>{{ $feedback_or_solve_class->mentor->name ?? '' }}</div>--}}
{{--                                <div style="font-size: 12px;color: #34d95a">{{ $feedback_or_solve_class->mentor->designation ?? '' }}</div>--}}
{{--                            </div>--}}
{{--                        </div>--}}
                    </div>
                @endif

            </div>
        @endif
    @endforeach

</div>