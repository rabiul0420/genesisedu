@extends('tailwind.layouts.admin')

@section('content')
    <div class="max-w-full print:max-w-4xl">
        <div class="flex justify-center items-center print:hidden">
            <button type="button" onclick="window.print()" class="px-3 py-2 rounded bg-gray-500 text-white">Print</button>
        </div>
        <div class="max-w-4xl mx-auto">
            <div class="flex flex-col items-center justify-center">
                <span class="text-4xl">Genesis</span>
                @if (!$question_code)
                    <span class="text-xl print:text-lg">Chapter : {{ $chapter->chapter_name ?? '' }} </span>
                    <span class="text-xl print:text-md">Topic : {{ $topic->topic_name ?? '' }} </span>
                @endif
                @if ($question_code)
                    <span>Question Source : {{ $question_code->reference_code ?? '' }}</span>
                @endif
            </div>
            <div>
                @foreach ($questions as $question)
                    {{-- For Chapter and Topic --}}

                    @if (!$question->question_id)
                        <div class="p-2 text-sm">
                            <div class="flex">
                                <span class=" flex text-md font-bold">{{ $loop->iteration }}.&nbsp;</span>
                                <span>
                                    {!! $question->question_and_answers !!}
                                </span>
                            </div>
                            <div style="margin-left: 12px">
                                {!! $question->reference ?? '' !!}
                                {!! $question->discussion ?? '' !!}
                            </div>
                        </div>
                    @endif

                    {{-- For question sourece --}}

                    @if ($question->question_id)
                        <div class="p-2 text-sm">
                            <div class="flex">
                                <span class=" flex text-md font-bold">{{ $loop->iteration }}.&nbsp;</span>
                                <span>
                                    {!! $question->question_and_answers !!}
                                </span>
                            </div>
                            <div style="margin-left: 12px">
                                {!! $question->reference ?? '' !!}
                                {!! $question->discussion ?? '' !!}
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
@endsection
