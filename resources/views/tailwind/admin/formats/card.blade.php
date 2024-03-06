<div class="grid">
    <div class="flex justify-between items-center">
        <h3 class="font-bold">
            {!! highlight_filter_text($format->property, request()->search) !!}
        </h3>
        <button type="button" class="px-4 py-1.5 rounded bg-sky-500 text-white" onclick="document.getElementById('formContainer{{ $format->id }}').classList.remove('hidden')">
            Edit
        </button>
    </div>
    <hr class="my-1">
    <div>
        {!! highlight_filter_text($format->body, request()->search) !!}
    </div>
</div>

@include('tailwind.admin.formats.form', compact('format'))