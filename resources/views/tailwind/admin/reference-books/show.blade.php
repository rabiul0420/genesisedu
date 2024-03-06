@extends('tailwind.layouts.admin')

@section('content')
    <div class="max-w-xl mx-auto">
        <div class="mx-auto border p-4">
            <div class="form-group">
                <label class="text-xl">Book Name: {{ $reference_book->name }}</label>
            </div>
            <div class="form-group">
                <label class="text-xl">Toatal Pages: {{ $reference_book->total_pages }}</label>
            </div>
        </div>

        <div class="mx-auto mt-2">
            @if (session('message'))
                <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg dark:bg-green-200 dark:text-green-800"
                    role="alert">{{ session('message') }}
                </div>
            @endif
        </div>

        <hr class="my-3">

        <div class="w-full grid gap-4">
            <form method="POST" action="{{ route('reference-books.reference-book-pages.store', $reference_book->id) }}">
                {{ csrf_field() }}
                <div class="border rounded-md shadow bg-gray-200 p-2">
                    <div class="p-2">
                        <textarea name="body" id="body" class="w-full grow px-2 py-2 border rounded border-gray-200 focus:outline-none"
                            type="text" placeholder="Page Link"></textarea>
                    </div>
                    <div class="flex items-center gap-3 p-2">
                        <input name="page_no"
                            class="w-full grow px-2 py-2 border rounded border-gray-200 focus:outline-none" type="number"
                            placeholder="Page No" required>
                        <input class="shrink-0 px-3 py-2 rounded bg-sky-400 text-white focus:outline-none cursor-pointer"
                            type="submit" value="Add New">
                    </div>
                </div>
            </form>
        </div>
    </div>

    <hr class="my-3">

    {{ $reference_book_pages->links('tailwind.components.paginator') }}

    <hr class="my-3">

    <div class="max-w-6xl mx-auto">
        <div class="grid md:grid-cols-2 gap-4 md:gap-8">
            @foreach ($reference_book_pages as $index => $page)
                <div class="border rounded-md shadow p-2">
                    <div class="items-center p-2">
                        <textarea id="body__{{ $index }}"
                            class="w-full body grow px-2 py-2 border rounded border-gray-200 focus:outline-none"
                            type="text" value="">{{ $page->body ?? '' }} </textarea>
                    </div>
                    <div class="flex items-center gap-3 p-2">
                        <input id="page_no__{{ $index }}"
                            class="w-full grow px-2 py-2 border rounded border-gray-200 focus:outline-none" type="text"
                            value="{{ $page->page_no ?? '' }}">
                        <input class="shrink-0 px-3 py-2 rounded bg-sky-400 text-white focus:outline-none cursor-pointer"
                            type="button" value="Save" @click="updateLink({{ $page->id }}, {{ $index }})">
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <hr class="my-3">

    {{ $reference_book_pages->links('tailwind.components.paginator') }}


    <script src="https://cdn.ckeditor.com/ckeditor5/27.1.0/classic/ckeditor.js"></script>

    <script src="{{ asset('js/CkUploadAdapter.js') }}"></script>

    <script>
        CkUploadAdapter.uploadUrl = `{{ route('upload-image-get-link') }}`;

        CkUploadAdapter.csrfToken = `{{ csrf_token() }}`;

        function SimpleUploadAdapterPlugin(editor) {
            editor.plugins.get('FileRepository').createUploadAdapter = (loader) => {
                return new CkUploadAdapter(loader);
            };
        }

        ClassicEditor
            .create(document.querySelector('#body'), {
                extraPlugins: [SimpleUploadAdapterPlugin],
                mediaEmbed: {
                    previewsInData: true
                },
                toolbar: [
                    'heading',
                    'bold',
                    'italic',
                    'link',
                    'undo',
                    'redo',
                    '-',
                    'uploadImage',
                    'insertTable',
                ],

            }).then(editor => {
                window.editor = editor;
            })
            .catch(error => {
                console.error(error);
            });



        var allEditors = document.querySelectorAll('.body');
        for (var i = 0; i < allEditors.length; ++i) {
            ClassicEditor
                .create(allEditors[i], {
                    toolbar: [
                        'heading',
                        'bold',
                        'italic',
                        'link',
                        'undo',
                        'redo',
                        '-',
                        'uploadImage',
                        'insertTable',
                    ],
                })
                .catch(error => {
                    console.error(error);
                });
        }
    </script>
    <script>
        function updateLink(id, index) {
            // console.log(id);
            let page_no = document.getElementById(`page_no__${index}`).value;
            // console.log(name);
            let body = document.getElementById(`body__${index}`).value;
            // console.log(total_pages);
            // let icon = document.getElementById(`bell__icon__${index}`);

            axios.post(`/admin/reference-book-pages/${id}`, {
                    page_no,
                    body,
                    _method: 'PUT'
                })
                .then(function(response) {
                    // if (response.data.hasLink) {
                    //     icon.classList.remove('text-red-600')
                    //     icon.classList.add('text-green-600')
                    // } else {
                    //     icon.classList.remove('text-green-600')
                    //     icon.classList.add('text-red-600')
                    // }
                })
                .catch(function(error) {
                    console.log(error);
                });
        }
    </script>
@endsection
