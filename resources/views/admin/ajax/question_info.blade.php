@php
    $is_combined = ( $batch_type ?? '' ) == 'combined';
@endphp

<div class="col-md-1">
    Total MCQ{{$is_combined ? '-R':''}} : <b>{{ $total_mcq }}</b>
</div>

<div class="col-md-1">
    Total SBA{{$is_combined ? '-R':''}}: <b>{{ $total_sba }}</b>
</div>

@if( $is_combined )
    <div class="col-md-1">
        Total MCQ-F: <b>{{ $total_mcq2 }}</b>
    </div>
@endif

<div class="col-md-1">
    Total Mark : <b>{{ $total_mark }}</b>
</div>
<div class="col-md-2">
    Duration : <b>{{ $duration/60 }}</b> Min
</div>
<div class="col-md-2">
    Negative Mark : <b>{{ $negative_mark }}</b>
</div>
