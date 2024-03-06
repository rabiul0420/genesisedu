{{--Mentor Name:--}}
{{--<pre>--}}
{{--    {!!   json_encode( $detail->doctor_class_views ) ?? '' !!}--}}
{{--</pre>--}}

<style>
    .table>:not(:last-child)>:last-child>* {
        /*border-bottom: grey 1px solid;*/
    }

    .table>:not(:last-child)>:last-child> td {
        vertical-align: middle;
    }
</style>

<table class="table table-striped" style="border: 1px solid #ccc">
    <thead>
    <tr>
        <th>Criteria</th>
        @foreach( (array) (\App\DoctorClassView::getProgresses()) as $progress_id => $progress )
            @if( $progress_id > 0 )
                <th style="text-align: center">{{$progress}}</th>
            @endif
        @endforeach
    </tr>
    </thead>
    <tbody>

        @foreach( (array) ( \App\DoctorClassView::getClassCriteriaList( ) ) as $criteria )
            <tr class="criteria-list" data-criteria="{{$criteria}}">
                <td class="title text-left">
                    <div>{{$criteria}}</div>
                </td>

                @foreach( (array) ( \App\DoctorClassView::getProgresses( ) ) as $progress_id => $progress )
                    @if( $progress_id > 0 )
                        <td style="text-align: center">

                            {{ calculate_percent( $detail->rating_calculation['primary'][$criteria][$progress_id] ?? 0 , $detail->rating_count ?? 0 ) }} %
{{--                            {{ $detail->rating_calculation['primary'][$criteria][$progress_id] ?? 0 }}/{{$detail->rating_count ?? 0 }}--}}
                        </td>
                    @endif
                @endforeach
            </tr>
        @endforeach
    </tbody>
</table>

<table class="table table-striped" style="border: 1px solid #ccc;">
    <thead>
    <tr>
        <th>Criteria</th>
        @foreach( (array) (\App\DoctorClassView::getVideoProgresses()) as $progress_id => $progress )
        @if( $progress_id > 0 )
        <th style="text-align: center">{{$progress}}</th>
        @endif
        @endforeach
    </tr>
    </thead>
    <tbody>
        @foreach( (array) ( \App\DoctorClassView::getVideoQualityCriteriaList( ) ) as $criteria )
            <tr class="criteria-list" data-criteria="{{$criteria}}">
                <td class="title text-left">
                    <div>{{$criteria}}</div>
                </td>

                @foreach( (array) ( \App\DoctorClassView::getVideoProgresses( ) ) as $progress_id => $progress )
                    @if( $progress_id > 0 )
                        <td style="text-align: center">
                            {{ calculate_percent( $detail->rating_calculation['primary'][$criteria][$progress_id] ?? 0 , $detail->rating_count ?? 0 ) }} %
{{--                            {{$detail->rating_calculation['video-quality'][$criteria][$progress_id] ?? 0 }}/{{$detail->rating_count ?? 0}}--}}
                        </td>
{{--                        <td style="text-align: center">1/10</td>--}}
                    @endif
                @endforeach
            </tr>
        @endforeach
    </tbody>
</table>