<button type="button" onclick="document.getElementById('formContainer{{ $quiz_property->id }}').classList.remove('hidden')" class="block rounded border space-y-2 p-4 bg-white hover:bg-gray-100 transition-all ease-linear">
    <div class="flex items-center gap-3">
        <div class="shrink grow flex justify-start items-center gap-2">
            @if($quiz_property->status == 2 || $quiz_property->quizzes->count())
            <div class="shrink-0 grow-0 text-rose-600 text-xl">
                <i class="fa fa-lock"></i>
            </div>
            @endif
            <div class="shrink-0 grow-0 text-indigo-600 bg-indigo-600/20 px-2 py-1 rounded">
                ID: {{ $quiz_property->id }}
            </div>
        </div>
        <div class="shrink-0 grow-0 flex justify-start items-center gap-2">
            <div class="text-sky-600 bg-sky-600/20 px-1.5 py-1 rounded flex justify-center items-center gap-1">
                <span>Mark: {{ $quiz_property->full_mark ?? "" }}</span>
            </div>
            <div class="text-green-700 bg-green-700/20 px-1.5 py-1 rounded flex justify-center items-center gap-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ $quiz_property->duration ?? '' }} min</span>
            </div>
        </div>
    </div>
    <div class="flex flex-wrap items-center justify-between font-semibold text-base text-gray-600 pt-2">
        <span>{{ $quiz_property->title ?? '' }}</span>
        <span class="py-1 px-4 bg-purple-600/20 text-purple-600 rounded text-left">
            {{ $quiz_property->course->name ?? '' }}
        </span>
    </div>
</button>

@include('tailwind.admin.quiz-properties.form', compact('quiz_property'))