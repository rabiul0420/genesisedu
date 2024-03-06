<div class="grid gap-2 md:gap-3 p-2 md:grid-cols-2 max-w-6xl mx-auto ">
    @foreach ($questions as $question)
    <div class="px-4 py-4 rounded border shadow bg-white">
        <div class="grid gap-2">
            <div class="flex items-center gap-4">
                <div class="text-sky-600" id="questionId{{ $question->id }}"></div>
                <label class="grow-0 shrink-0">
                    <input type="checkbox" class="cursor-pointer" {{ in_array($question->id, $selected_question_ids) ? 'checked' : '' }} value="{{ $question->id }}" onclick="selectQuestion(this)">
                </label>
                <div class="grow shrink break-all">
                    ID: {{ $question->id }}
                </div>
                <a 
                    target="_blank"
                    title="Edit Question"
                    href="{{ $question->path_url ?? '' }}/edit" 
                    class="flex justify-center items-center text-sky-600"
                    onclick="
                        this.nextElementSibling.style.display = '';
                        this.style.display = 'none';
                    "
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                    </svg>
                </a>
                <a style="display: none;" class="print-button btn btn-xs btn-info" href="{{ str_replace('flag=true', '', \Request::getRequestUri()) }}" >
                    &#x21bb; Refresh
                </a>
            </div>
            <hr>
            <div>
                {!! $question->title ?? '' !!}
            </div>
            <div class="grid grid-cols-2 gap-x-4">
                @foreach ($question->question_options as $question_option)
                <div>
                    {!! $question_option->title ?? '' !!}
                </div>
                @endforeach
            </div>
            <div class="font-semibold">
                Ans: <span class="select-all">{{ $question->answer_script ?? '' }}</span>
            </div>
        </div>
    </div>
    @endforeach
</div>

<div class="col-span-full sticky bottom-0 bg-gray-200 p-4 border-t -mx-4 -mb-4">
    {{ $questions->links('tailwind.components.search-method-paginator') }}
</div>