{{ $batch_shifts->links('tailwind.components.search-method-paginator') }}

<hr class="my-3 print:hidden">

<div class="grid gap-8 md:grid-cols-2 2xl:grid-cols-4 print:grid-cols-2">
    @foreach ($batch_shifts as $batch_shift)
    <div class="block rounded-xl shadow border p-3 {{ $batch_shift->shifted_at && $batch_shift->to_doctor_course_id ? 'bg-white' : 'bg-rose-50' }}">
        @include('tailwind.admin.batch-shifts.card', compact('batch_shift', 'search'))
    </div>
    @endforeach
</div>

<hr class="my-3 print:hidden">

{{ $batch_shifts->links('tailwind.components.search-method-paginator') }}