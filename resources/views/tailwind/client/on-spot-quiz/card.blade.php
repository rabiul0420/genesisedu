<div class="flex items-center gap-3">
    <div class="shrink-0 grow-0 flex justify-start items-center gap-2">
        <div class="text-sky-600 bg-sky-200/50 px-1.5 py-1 rounded flex justify-center items-center gap-1">
            <span>Mark: {{ $quiz->quiz_property->full_mark ?? "" }}</span>
        </div>
        <div class="text-green-600 bg-green-200/50 px-1.5 py-1 rounded flex justify-center items-center gap-1">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>{{ $quiz->quiz_property->duration ?? '' }} min</span>
        </div>
    </div>
</div>
<div class="flex items-center gap-2 py-2">
    <div class="shrink-0 grow-0 text-gray-500 text-sm">Quiz:</div>
    <div class="shrink grow font-semibold text-base text-sky-600 text-left">{{ $quiz->title ?? '' }}</div>
</div>
<div class="flex flex-wrap items-center justify-start font-semibold text-base text-gray-600 gap-2">
    <div class="py-1 px-4 bg-gray-600/20 text-gray-600 rounded text-left flex gap-2">
        <span>{{ $quiz->quiz_property->title ?? '' }}</span>
    </div>
</div>