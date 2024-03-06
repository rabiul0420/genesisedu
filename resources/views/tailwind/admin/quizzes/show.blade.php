@extends('tailwind.layouts.admin')

@section('content')
<div class="max-w-6xl mx-auto flex items-center gap-4">
    <button type="button" class="px-4 py-1.5 rounded bg-sky-500 text-white" onclick="document.getElementById('formContainer{{ $quiz->id }}').classList.remove('hidden')">
       Quiz Edit
    </button>
</div>

<div class="max-w-6xl mx-auto grid gap-4 md:grid-cols-2 my-4">
    <div class="block rounded border space-y-2 p-4 bg-white">
        @include('tailwind.admin.quizzes.card', [
            'quiz_properties'   => $quiz_properties,
            'quiz'              => $quiz,
        ])
    </div>
    @if(in_array($quiz->status, [1]))
    <div class="rounded border space-y-2.5 px-4 py-2 bg-white flex flex-col justify-start items-start">
        <h3>Quiz Participant Link</h3>
        <div class="select-all px-2 py-1 border rounded break-all text-blue-500">
            {{ route('on-spot-quiz.show', $quiz->key) }}
        </div>
        <div class="border border-black p-px">
            {{ $qr_code_image }}
        </div>
    </div>
    @endif
</div>

@include('tailwind.admin.quizzes.form', [
    'quiz' => $quiz,
    'quiz_properties' => $quiz_properties,
])

<hr class="my-3">

<div class="max-w-6xl mx-auto">
    @foreach(\App\Question::$question_type_array as $question_type => $question_type_text)
    @if($number_of_question_by_question_type[$question_type] ?? false)
    <div class="mt-6 p-4 bg-gray-200 text-center flex items-center justify-between">
        <div class="text-3xl">
            ({{ $quiz_questions->where('question_type', $question_type)->count() }}) {{ $question_type_text }}
        </div>
        @if(!$quiz->status)
        <div>
            <a href="{{ route('quizzes.assign', [$quiz->id, $question_type]) }}" class="px-4 py-1.5 rounded bg-sky-500 text-white flex gap-2 items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                </svg>
                <span>Assign</span>
            </a>
        </div>
        @endif
    </div>
    <div class="">
        <div class="grid gap-3 md:grid-cols-2 print:grid-cols-2 bg-gray-100 p-3">
            @foreach ($quiz_questions->where('question_type', $question_type) as $quiz_question)
            <div class="px-2 py-1.5 rounded-lg border border-dashed space-y-1.5 bg-blue-100 border-sky-500">
                <div class="flex justify-between items-center">
                    <div class="flex gap-2 items-center">
                        <div class="w-8 h-8 rounded-full flex justify-center items-center bg-white text-sky-500">
                            {{ $quiz_question->serial }}
                        </div>
                    </div>
                    @if(!$quiz->status)
                    <form action="{{ route('quizzes.assign', [$quiz->id, $question_type]) }}" method="POST">
                        {{ csrf_field() }}
                        <input hidden name="question_id" value="{{ $quiz_question->question_id }}" />
                        <input hidden name="redirect" value="back" />
                        <button type="submit">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 cursor-pointer p-1.5 rounded-full  bg-white text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </form>
                    @endif
                </div>
                <div class="border-t border-white"></div>
                <div>
                    <div class="flex gap-2">
                        <div>
                            {!! $quiz_question->question->title !!}
                        </div>
                    </div>
                    <div class="grid md:grid-cols-2 gap-x-3 px-3">
                        @foreach ($quiz_question->question->question_options as $option)
                        <div>
                            {!! $option['title'] !!}
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="font-bold">
                    Ans: <span class="select-all">{{ $quiz_question->question->answer_script }}</span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
    @endforeach
</div>
@endsection