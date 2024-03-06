@extends('tailwind.layouts.admin')

@section('content')

<div class="py-4">
    <button type="button" class="px-4 py-1.5 rounded bg-sky-500 text-white" onclick="document.getElementById('formContainer').classList.remove('hidden')">
        New Quiz
    </button>
</div>

@include('tailwind.admin.quizzes.form', [
    'quiz' => new \App\Quiz(),
    'quiz_properties' => $quiz_properties,
    'quiz_participants_count' => 0,
])
    
{{ $quizzes->links('tailwind.components.paginator') }}
    
<hr class="my-3 print:hidden">
    
<div class="grid gap-4 md:grid-cols-2 2xl:grid-cols-3 print:grid-cols-2">
    @foreach ($quizzes as $quiz)
    <a href="{{ route('quizzes.show', $quiz->id) }}" class="block rounded border space-y-2 p-4 bg-white hover:bg-gray-100 transition-all ease-linear">
        @include('tailwind.admin.quizzes.card', [
            'quiz_properties'   => $quiz_properties,
            'quiz'              => $quiz,
        ])
    </a>
    @endforeach
</div>

<hr class="my-3 print:hidden">

{{ $quizzes->links('tailwind.components.paginator') }}

@endsection