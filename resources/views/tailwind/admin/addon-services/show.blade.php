@extends('tailwind.layouts.admin')

@section('content')
    <div class="max-w-xl mx-auto space-y-4">
        <h1 class="rounded overflow-hidden border p-4 text-sky-600 text-center font-bold text-sm md:text-2xl">
            {{ $addon_service->name ?? '' }}
        </h1>
        <div class="rounded overflow-hidden p-2 border">
            <div class="p-3 space-y-4">
                @if($addon_service->addon_contents->count())
                <div class="__drag__and__drop__container__ grid gap-4" id="addonContentContainer">
                    @foreach ($addon_service->addon_contents as $index => $addon_content)
                    <div draggable="true" class="__box__ __slot__container__ flex items-center gap-2">
                        <div class="relative grow shrink flex items-center gap-1 border border-dashed rounded-md px-2 py-3 cursor-move">
                            <div class="grow-0 shrink-0 w-6 h-6">
                                @if($addon_content->contentable_type == 'App\Exam')
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-full stroke-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                </svg>
                                @endif
                                @if($addon_content->contentable_type == 'App\LectureVideo')
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-full stroke-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                @endif
                            </div>
                            <div class="grow shrink break-all line-clamp-1 text-gray-600">
                                {{ $addon_content->contentable->name ?? '' }}
                            </div>
                            <div class="__data__id__ absolute inset-0 z-10" data-id="{{ $addon_content->id }}"></div>
                        </div>
                        <div class="grow-0 shrink-0 w-6 h-6">
                            @if($addon_content->contentable_type == 'App\Exam')
                            <svg onclick="removeContentSlot(`{{ $addon_content->contentable_id }}`, 'exam', this.closest('.__slot__container__'))" xmlns="http://www.w3.org/2000/svg" class="w-full text-red-500 cursor-pointer" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            @endif
                            @if($addon_content->contentable_type == 'App\LectureVideo')
                            <svg onclick="removeContentSlot(`{{ $addon_content->contentable_id }}`, 'lecture', this.closest('.__slot__container__'))" xmlns="http://www.w3.org/2000/svg" class="w-full text-red-500 cursor-pointer" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif

                <div id="__assign__button__container__">
                    <div class="flex items-center justify-center gap-4 px-2">
                        <a
                            href="{{ route('addon-services.prepare', [$addon_service->id, 'lecture']) }}" 
                            class="w-1/2 flex justify-center items-center px-2 py-2 rounded shrink-0 text-rose-600 cursor-pointer gap-1 border border-rose-600 hover:bg-rose-600 hover:text-white"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                            </svg>
                            <span class="font-semibold text-sm">Assign Lecture</span>
                        </a>
                        <a
                            href="{{ route('addon-services.prepare', [$addon_service->id, 'exam']) }}"
                            class="w-1/2 flex justify-center items-center px-2 py-2 rounded shrink-0 text-purple-600 cursor-pointer gap-1 border border-purple-600 hover:bg-purple-600 hover:text-white"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                            </svg>
                            <span class="font-semibold text-sm">Assign Exam</span>
                        </a>
                    </div>
                </div>

                <div id="__message__container__" class="text-center text-2xl text-sky-600"></div>

                <div id="__sort__button__container__" class="hidden">
                    <div class="flex items-center justify-center gap-4 px-2">
                        <a
                            href="{{ route('addon-services.show', [$addon_service->id]) }}" 
                            class="w-1/2 flex justify-center items-center px-2 py-2 rounded shrink-0 text-gray-600 cursor-pointer gap-1 border border-gray-600 hover:bg-gray-600 hover:text-white"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            <span class="font-semibold text-sm">Reset</span>
                        </a>
                        <button
                            onclick="saveSorting()"
                            type="button" 
                            class="w-1/2 flex justify-center items-center px-2 py-2 rounded shrink-0 text-green-600 cursor-pointer gap-1 border border-green-600 hover:bg-green-600 hover:text-white"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                            <span class="font-semibold text-sm">Save Sorting</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function removeContentSlot(contentId, type, slot) {
            if(!confirm('Are you sure?')) {
                return;
            }

            let url;

            if(type == 'exam') {
                url = `{{ route('addon-services.assign', [$addon_service->id, 'exam']) }}`;                
            }
            if(type == 'lecture') {
                url = `{{ route('addon-services.assign', [$addon_service->id, 'lecture']) }}`;                
            }

            axios.post(url, {
                content_id: contentId,
            })
            .then((res) => {
                slot.innerHTML = res.data.message;

                setTimeout(() => {
                    slot.parentElement.removeChild(slot);
                }, 1000);
            })
        }

        function saveSorting() {
            const data = [];
            let index = 1;

            document.querySelectorAll('.__drag__and__drop__container__ .__box__ .__data__id__').forEach( element => {
                data.push({
                    id: Number (element.getAttribute('data-id')),
                    priority: index++,
                });
            })

            axios.post('/admin/sort--data', {
                model: 'addon-content',
                data,
            })
            .then((res) => {
                document.getElementById('__sort__button__container__').classList.add('hidden')
                
                document.getElementById('__message__container__').innerHTML = res.data.message;
                
                setTimeout(() => {
                    document.getElementById('__message__container__').innerHTML = "";
                    document.getElementById('__assign__button__container__').classList.remove('hidden');
                }, 1000);
            })
        }

        function handleDragStart(e) {
            this.style.opacity = '0.4';
            dragSrcEl = this;
            e.dataTransfer.effectAllowed = 'move';
            e.dataTransfer.setData('text/html', this.innerHTML);
        }

        function handleDragEnd(e) {
            this.style.opacity = '1';
        }

        let items = document.querySelectorAll('.__drag__and__drop__container__ .__box__');
        items.forEach(function (item) {
            item.addEventListener('dragstart', handleDragStart);
            item.addEventListener('dragend', handleDragEnd);
        });

        document.addEventListener('DOMContentLoaded', (event) => {

            function handleDragStart(e) {
                this.style.opacity = '0.4';
            }

            function handleDragEnd(e) {
                this.style.opacity = '1';

                items.forEach(function (item) {
                    item.classList.remove('__over__');
                });
            }

            function handleDragOver(e) {
                e.preventDefault();
                return false;
            }

            function handleDragEnter(e) {
                this.classList.add('__over__');
            }

            function handleDragLeave(e) {
                this.classList.remove('__over__');
            }

            let items = document.querySelectorAll('.__drag__and__drop__container__ .__box__');
            items.forEach(function(item) {
                item.addEventListener('dragstart', handleDragStart);
                item.addEventListener('dragover', handleDragOver);
                item.addEventListener('dragenter', handleDragEnter);
                item.addEventListener('dragleave', handleDragLeave);
                item.addEventListener('dragend', handleDragEnd);
                item.addEventListener('drop', handleDrop);
            });
        });

        function handleDrop(e) {
            e.stopPropagation(); // stops the browser from redirecting.
            if (dragSrcEl !== this) {
                dragSrcEl.innerHTML = this.innerHTML;
                this.innerHTML = e.dataTransfer.getData('text/html');

                document.getElementById('__assign__button__container__').classList.add('hidden')
                document.getElementById('__sort__button__container__').classList.remove('hidden')
            }


            return false;
        }
    </script>
@endsection
