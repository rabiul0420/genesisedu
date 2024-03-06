@extends('tailwind.layouts.admin')

@section('content')

{{ $pending_lectures->links('tailwind.components.paginator') }}

<hr class="my-3">

<div class="grid gap-4 md:grid-cols-2 2xl:grid-cols-3">
    @foreach ($pending_lectures as $index => $pending_lecture) 
    <div class="flex flex-col rounded shadow border border-gray-100 gap-3 p-4">
        <div class="grow flex items-center gap-3">
            <div class="shrink-0 w-10 h-10 relative">
                <svg xmlns="http://www.w3.org/2000/svg" id="bell__icon__{{ $index }}" class="h-10 w-10 {{ $pending_lecture->link ? 'text-green-600' : 'text-red-600' }}" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z" />
                </svg>
                <span class="absolute top-2 left-1/2 -translate-x-1/2 text-sm text-white">
                    {{ count($pending_lecture->request_lecture_videos ?? []) }}
                </span>
            </div>
            <div class="grow shrink text-gray-700 text-xs font-bold md:text-sm">
                {{ $pending_lecture->title ?? '' }}
            </div>
        </div>
        <div class="flex items-center gap-3">
            <input id="video__link__{{ $index }}" class="grow px-2 py-2 border rounded border-gray-200 focus:outline-none" type="text" value="{{ $pending_lecture->link ?? '' }}">
        </div>
        <div class="flex items-center gap-3">
            <input id="video__password__{{ $index }}" class="grow px-2 py-2 border rounded border-gray-200 focus:outline-none" type="text" value="{{ $pending_lecture->password ?? '' }}">
            <input class="shrink-0 px-3 py-2 rounded bg-sky-400 text-white focus:outline-none cursor-pointer" type="button" value="Save" @click="updateLink({{ $pending_lecture->id }},{{ $index }})">
        </div>
    </div>
    @endforeach
</div>

<hr class="my-3">

{{ $pending_lectures->links('tailwind.components.paginator') }}

<script>
    function updateLink (id, index) {
        let link = document.getElementById(`video__link__${index}`).value;
        let password = document.getElementById(`video__password__${index}`).value;
        let icon = document.getElementById(`bell__icon__${index}`);

        axios.post(`/admin/request-lecture-videos/${id}`, {
            link,
            password,
            _method: 'put'
        })
        .then(function (response) {
            if(response.data.hasLink) {
                icon.classList.remove('text-red-600')
                icon.classList.add('text-green-600')
            } else {
                icon.classList.remove('text-green-600')
                icon.classList.add('text-red-600')
            }
        })
        .catch(function (error) {
            console.log(error);            
        });
    }
</script>
@endsection