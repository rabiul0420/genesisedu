<button 
    type="button" 
    onclick="document.getElementById('formContainer{{ $label->id }}').classList.remove('hidden')" 
    class="flex rounded border px-6 py-4 text-indigo-600 hover:bg-gray-100 transition-all ease-linear {{ $label->status ? 'bg-green-200' : 'bg-rose-200' }}"
>
    {{ $label->name ?? '' }}
</button>

@include('tailwind.admin.labels.form', compact('label'))