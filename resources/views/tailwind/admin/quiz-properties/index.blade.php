@extends('tailwind.layouts.admin')

@section('content')

<div class="py-4">
    <button type="button" class="px-4 py-1.5 rounded bg-sky-500 text-white" onclick="document.getElementById('formContainer').classList.remove('hidden')">
        New Property
    </button>
</div>

@include('tailwind.admin.quiz-properties.form', ['quiz_property' => new \App\QuizProperty()])
    
{{ $quiz_properties->links('tailwind.components.paginator') }}
    
<hr class="my-3 print:hidden">
    
<div class="grid gap-4 md:grid-cols-2 2xl:grid-cols-3 print:grid-cols-2">
    @foreach ($quiz_properties as $quiz_property)
        @include('tailwind.admin.quiz-properties.card', compact('quiz_property'))
    @endforeach
</div>

<hr class="my-3 print:hidden">

{{ $quiz_properties->links('tailwind.components.paginator') }}

@endsection