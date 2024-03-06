@extends('tailwind.layouts.admin')

@section('content')
    <div class="grid gap-4 md:grid-cols-2 2xl:grid-cols-3">
        <form method="POST" action="{{ route('reference-books.store') }}">
            {{ csrf_field() }}
            <div class="col-span-2 rounded overflow-hidden shadow">
                <div class="items-center p-2">
                    <input name="name" class="w-full grow px-2 py-2 border rounded border-gray-200 focus:outline-none"
                        type="text" placeholder="Book Name" required>
                </div>
                <div class="flex items-center gap-3 p-2">
                    <input name="total_pages" class="w-auto grow px-2 py-2 border rounded border-gray-200 focus:outline-none"
                        type="text" placeholder="Total Page">
                    <input class="shrink-0 px-3 py-2 rounded bg-sky-500 text-white focus:outline-none cursor-pointer"
                        type="submit" value="+ Add New">
                </div>
            </div>
        </form>
        <div class="mx-auto">
            @if (session('message'))
                <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg dark:bg-green-200 dark:text-green-800"
                    role="alert">
                    {{ session('message') }}
                </div>
            @endif
        </div>
    </div>

    <div>
        {{ $reference_books->links('tailwind.components.paginator') }}
        <hr class="my-3">

        <div class="grid gap-4 md:grid-cols-2 2xl:grid-cols-3">
            @foreach ($reference_books as $index => $reference_book)
                <div class="rounded overflow-hidden shadow p-2 border">
                    <div class="flex justify-between items-center gap-3 p-2">
                        <span class="text-sky-500">ID: {{ $reference_book->id }}</span>
                        <a href="{{ route('reference-books.show', $reference_book->id) }}"
                            class="shrink-0 px-2 py-1 rounded border border-sky-500 text-sky-500 hover:bg-sky-500 hover:text-white focus:outline-none cursor-pointer">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </a>
                    </div>
                    <div class="items-center p-2">
                        <input id="name__link__{{ $index }}"
                            class="w-full grow px-2 py-2 border rounded border-gray-200 focus:outline-none" type="text"
                            value="{{ $reference_book->name ?? '' }}">
                    </div>
                    <div class="flex items-center gap-3 p-2">
                        <input id="total_pages__{{ $index }}"
                            class="w-auto grow px-2 py-2 border rounded border-gray-200 focus:outline-none" type="text"
                            value="{{ $reference_book->total_pages ?? '' }}">
                        <input class="shrink-0 px-3 py-2 rounded bg-sky-400 text-white focus:outline-none cursor-pointer"
                            type="button" value="Save"
                            @click="updateLink({{ $reference_book->id }},{{ $index }})">
                    </div>
                </div>
            @endforeach
        </div>

        <hr class="my-3">
    </div>

    {{ $reference_books->links('tailwind.components.paginator') }}

    <script>
        function updateLink(id, index) {
            // console.log(index);
            let name = document.getElementById(`name__link__${index}`).value;
            // console.log(name);
            let total_pages = document.getElementById(`total_pages__${index}`).value;
            // console.log(total_pages);
            let icon = document.getElementById(`bell__icon__${index}`);

            axios.post(`/admin/reference-books/${id}`, {
                    name,
                    total_pages,
                    _method: 'PUT'
                })
                .then(function(response) {
                    console.log(response.data);
                })
                .catch(function(error) {
                    console.log(error);
                });
        }
    </script>
@endsection
