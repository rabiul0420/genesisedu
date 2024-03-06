{{ $participants->links('tailwind.components.search-method-paginator') }}

<hr class="my-3 print:hidden">

<div class="grid gap-4 md:grid-cols-2 2xl:grid-cols-3 print:grid-cols-2">
    @foreach ($participants as $participant)
    <div class="block rounded border shadow-sm space-y-2 p-4 bg-white hover:bg-gray-100 transition-all ease-linear">
        @include('tailwind.admin.quiz-results.card', compact('participant', 'search'))
    </div>
    @endforeach
</div>

<hr class="my-3 print:hidden">

{{ $participants->links('tailwind.components.search-method-paginator') }}