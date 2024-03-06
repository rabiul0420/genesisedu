@extends('tailwind.layouts.admin')

@section('content')

<div class="py-4">
    <button type="button" class="px-4 py-1.5 rounded bg-sky-500 text-white" onclick="document.getElementById('formContainer').classList.remove('hidden')">
        Add New Label
    </button>
</div>

@include('tailwind.admin.labels.form', ['label' => new \App\Label()])
    
{{ $labels->links('tailwind.components.paginator') }}
    
<hr class="my-3 print:hidden">
    
<div class="flex flex-wrap gap-4">
    @each('tailwind.admin.labels.card', $labels, 'label')
</div>

<hr class="my-3 print:hidden">

{{ $labels->links('tailwind.components.paginator') }}

@endsection