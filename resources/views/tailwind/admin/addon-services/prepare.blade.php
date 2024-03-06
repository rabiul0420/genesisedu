@extends('tailwind.layouts.admin')

@section('content')
    <h1 class="py-2 text-center text-sm md:text-2xl text-sky-600">
        Assign <b id="contentCounter"></b> <i>'{{ $content_type }}'</i> in <b>"{{ $addon_service->name }}"</b>
    </h1>
    <hr class="my-3">
    <div class="flex justify-between items-center gap-2 p-2 max-w-5xl mx-auto">
        <div class="w-24 md:w-40 grow-0 shrink-0">
            <a href="{{ route('addon-services.show', $addon_service->id) }}" class="block py-2 bg-sky-400 rounded text-center text-white">
                <b>&#8592;</b> Go Back
            </a>
        </div>
        <div class="max-w-xs grow shrink">
            <input type="search" class="block w-full px-3 py-2 border border-gray-400 rounded focus:outline-0" id="search" placeholder="Search ..." value="{{ $search }}" oninput="search()" />
        </div>
    </div>

    <div class="grid gap-2 p-2 md:grid-cols-2 max-w-5xl mx-auto" id="dataContainer"></div>

    <script>
        function search(page = 1) {
            const url = `{{ route('addon-services.prepare', [$addon_service->id, $content_type]) }}?page=${page}`;
            const text = document.getElementById('search').value.trim();

            historyTitle = document.title;
            historyState = {}
            historyUrl = text ? `${url}&search=${text}` : url;
            // localStorage.setItem('historyUrl', historyUrl);
            window.history.pushState(historyState, historyTitle, historyUrl);

            axios.get(url, {
                params: {
                    search: text,
                    flag: true,
                }
            })
            .then((res) => {
                document.getElementById('dataContainer').innerHTML = res.data.html;
                document.getElementById('contentCounter').innerHTML = res.data.totalContent;
            })
            .catch((err) => {
                console.log(err);
            })
        }

        search(`{{ $page }}`);

        function selectContent(content) {
            const url = `{{ route('addon-services.assign', [$addon_service->id, $content_type]) }}`;

            axios.post(url, {
                content_id: content.value,
                checked: content.checked,
            })
            .then((res) => {
                document.getElementById(`contentId${content.value}`).innerHTML = res.data.message;
                document.getElementById('contentCounter').innerHTML = res.data.totalContent;

                setTimeout(() => {
                    document.getElementById(`contentId${content.value}`).innerHTML = '';  
                }, 1000);
            })
        }
    </script>
@endsection
