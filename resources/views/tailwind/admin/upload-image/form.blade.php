@extends('tailwind.layouts.admin')

@section('content')
<div id="responseContainer" class="hidden mt-4 max-w-md mx-auto space-y-6">
    <div class="w-full flex flex-col gap-4 justify-center items-center rounded-md border border-dashed border-sky-400">
        <div class="w-full flex items-center gap-4 p-4">
            <button onclick="copyLink(this)" class="w-10 text-5xl text-green-500 flex-grow-0 flex-shrink-0 cursor-pointer grid gap-1">
                &#10063;
            </button>
            <a target="_blank" href="" id="imageUrlLink" class="flex-grow flex-shrink break-all text-blue-700 underline"></a>
        </div>
    </div>
    <div class="w-full flex justify-center items-center">
        <a href="" class="rounded-lg cursor-pointer px-4 py-2 border border-sky-500 text-sky-500 text-xl">Re-Upload</a>
    </div>
    <div id="imagePreview" class="w-full rounded-md">
    </div>
</div>
<form onsubmit="return false;" id="imageSelectContainer">
    <label class="mt-4 max-w-xl mx-auto text-violet-600 border border-dashed border-violet-400 rounded-2xl py-10 cursor-pointer flex flex-col gap-4 justify-center items-center">
        <div class="w-1/2 flex justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-2/3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
        </div>
        <input id="uploadImageFile" onchange="uploadImage(this)" name="upload" type="file" accept="image/*" class="file:mr-4 file:py-3 file:px-6 file:rounded-full file:border-0 file:text-2xl file:font-semibold file:bg-violet-100 file:text-violet-700 hover:file:bg-violet-100 file:cursor-pointer" />
    </label>
</form>

{{ $upladed_image_links->links('tailwind.components.paginator') }}

<div class="my-6 columns-2 md:columns-4 bg-purple-500 gap-8 p-8 rounded">
    @foreach ($upladed_image_links as $upladed_image_link)
    <div class="{{ $loop->index ? 'mt-8' : '' }} block relative rounded-xl overflow-hidden bg-gray-300  min-h-[60px]">
        <div class="absolute w-full p-4 inset-0 z-20 bg-transparent hover:bg-white text-transparent hover:text-green-500">
            <div class="flex border border-dashed border-transparent hover:border-green-500 gap-4 p-4 rounded-lg">
                <span onclick="copyLink(this, this.nextElementSibling)" class="cursor-pointer text-4xl w-10">&#10063;</span>
                <a target="_blank" href="{{ $upladed_image_link->url ?? '' }}" id="imageUrlLink" class="flex-grow flex-shrink break-all underline">{{ $upladed_image_link->url ?? '' }}</a>
            </div>
        </div>
        <img src="{{ $upladed_image_link->url ?? '' }}" alt="image" class="cursor-pointer rounded shadow object-cover border max-h-96 mx-auto">
    </div>
    @endforeach
</div>

{{ $upladed_image_links->links('tailwind.components.paginator') }}

<script>
    function uploadImage(input) {
        let data = new FormData();

        let imageFile = document.getElementById('uploadImageFile').files[0];

        if(!imageFile) {
            return;
        }

        data.append('upload', imageFile);

        axios.post(`/admin/upload-image-get-link/storage/true`, data)
            .then((res) => {
                document.getElementById('imageSelectContainer').classList.add('hidden');
                document.getElementById('responseContainer').classList.remove('hidden');
                document.getElementById('imageUrlLink').innerText = res.data.url;
                document.getElementById('imageUrlLink').setAttribute('href', res.data.url);
                document.getElementById('imagePreview').innerHTML = `<img class="w-full rounded shadow" src="${res.data.url}" alt="${res.data.url}">`
            })
    }

    function copyLink(button, url = null) {
        url = url || document.getElementById("imageUrlLink");

        button.innerHTML = '&#10003;';
        
        setTimeout(() => {
            button.innerHTML = '&#10063;';
        }, 1000);

        navigator.clipboard.writeText(url.innerText);
    }
</script>
@endsection