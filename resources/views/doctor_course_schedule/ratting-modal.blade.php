<div>
    <div class="badge bg-info" style="margin-bottom: 10px; text-align: center; display: block; padding: 12px 15px; font-size: 15px; white-space: normal ">{{ $details->video->name ?? '' }}</div>

    <div class="badge bg-success">Mentor</div>
    <div class="text-info">{{ $details->mentor->name ?? 'dd' }}</div>
    <div class="text-success" style="font-size: 11px">{{ $details->mentor->designation ?? 'dd' }}</div>

</div>

<style>
    .table>:not(:last-child)>:last-child>* {
        border-bottom: grey 1px solid;
    }

    .table>:not(:last-child)>:last-child> td {
        vertical-align: middle;
    }
</style>


<table class="table table-striped" style="margin-top: 15px">
    <thead>
    <tr style="border-top: 1px solid grey;border-bottom: 1px solid grey">
        <th>Criteria</th>
        @foreach( $progresses as $progress_id => $progress )

            @if( $progress_id > 0 )
                <th style="text-align: center">{{$progress}}</th>
            @endif

        @endforeach
    </tr>
    </thead>
    <tbody>

        @foreach( $feedback_criteria_list as $criteria )
            <tr class="criteria-list" data-criteria="{{$criteria}}">
                <td class="title">
                    <div>{{$criteria}}</div>
                </td>

{{--                @php $data = $feedbacks->where( 'criteria', $criteria )->first( ) @endphp--}}
                @php $ratings = $doctor_class_view->getRatings('primary') @endphp

                @foreach( $progresses as $progress_id => $progress )
                    @if( $progress_id > 0 )
                        <td style="text-align: center; vertical-align: middle" >
                            <input required type="radio" name="progress[primary][{{$criteria}}]" value="{{$progress_id}}"
                                    {{ ( $ratings->get( $criteria ) ?? '' ) == $progress_id ? 'checked':'' }}
                                    {{ ($ratings->has( $criteria ) ?? false ) ? 'disabled' : '' }}
                            >
                        </td>
                    @endif
                @endforeach
            </tr>
        @endforeach

    </tbody>

</table>


<table class="table table-striped">
    <thead>
    <tr>
        <th>Criteria</th>
        @foreach( $video_progresses as $progress_id => $progress )
            @if( $progress_id > 0 )
                <th style="text-align: center">{{$progress}}</th>
            @endif
        @endforeach
    </tr>
    </thead>
    <tbody>

        @foreach( $video_quality_criteria_list as $criteria )
            <tr class="criteria-list" data-criteria="{{$criteria}}">
                <td class="title">
                    <div>{{$criteria}}</div>
                </td>

{{--                @php $data = $feedbacks->where( 'criteria', $criteria )->first( ) @endphp--}}

                @php $ratings = $doctor_class_view->getRatings('video-quality') @endphp

                @foreach( $video_progresses as $progress_id => $progress )
                    @if( $progress_id > 0 )
                        <td style="text-align: center; vertical-align: middle" >
                            <input required type="radio" name="progress[video-quality][{{$criteria}}]" value="{{$progress_id}}"
                                    {{ ( $ratings->get( $criteria ) ?? '' ) == $progress_id ? 'checked':'' }}
                                    {{ ($ratings->has( $criteria ) ?? false ) ? 'disabled' : '' }}

                            >
                        </td>
                    @endif
                @endforeach
            </tr>
        @endforeach
    </tbody>
</table>
{{--s--}}

<div style="margin-bottom: 15px">
    <label style="font-size: 12px" for="feedback-text">Your Feedback</label>
    <textarea name="feedback"
              id="feedback-text"
              class="form-control" {{ strlen((string)$doctor_class_view->feedback) > 0 ? 'disabled':''}}
              placeholder="Write a feedback">{{$doctor_class_view->feedback}}</textarea>
</div>