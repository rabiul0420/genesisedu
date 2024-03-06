{{ $formats->links('tailwind.components.search-method-paginator') }}

<hr class="my-3">

<div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3 my-4">
    @foreach($formats as $format)
    <div class="block rounded-lg border space-y-2 p-4 bg-white">
        @include('tailwind.admin.formats.card', compact('format'))
    </div>
    @endforeach
</div>

<hr class="my-3">

{{ $formats->links('tailwind.components.search-method-paginator') }}