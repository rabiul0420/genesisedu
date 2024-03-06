<div>
    <div class="flex items-center gap-2">
        <span class="text-gray-500">Quiz:</span>
        <div>{!! str_ireplace($search, "<mark>$search</mark>", $participant->quiz->title ?? '') !!}</div>
    </div>
    <div class="flex items-center gap-2">
        <span class="text-gray-500">Doctor:</span>
        <div>{!! str_ireplace($search, "<mark>$search</mark>", $participant->doctor->name ?? '') !!}</div>
    </div>
    <div class="flex items-center gap-2">
        <span class="text-gray-500">Phone:</span>
        <div>{!! str_ireplace($search, "<mark>$search</mark>", $participant->doctor->phone ?? '') !!}</div>
    </div>
    <div class="flex items-center gap-2">
        <span class="text-gray-500">BMDC:</span>
        <div>{!! str_ireplace($search, "<mark>$search</mark>", $participant->doctor->bmdc_no ?? '') !!}</div>
    </div>
    <div class="flex items-center gap-2">
        <span class="text-gray-500">Mark:</span>
        <div>{{ $participant->obtained_mark ?? '' }}/{{ $participant->quiz->quiz_property->full_mark ?? '' }}</div>
    </div>
    <div class="flex items-center gap-2">
        <span class="text-gray-500">Coupon:</span>
        <div>{!! str_ireplace($search, "<mark>$search</mark>", $participant->coupon ?? '') !!}</div>
    </div>
    
</div>