@extends('tailwind.layouts.admin')

@section('content')
    {{ $addon_services->links('tailwind.components.paginator') }}
    
    <div class="max-w-xl">
        <div class="grid gap-4">
            <form method="POST" action="{{ route('addon-services.store') }}">
                {{ csrf_field() }}
                <div class="col-span-2 rounded overflow-hidden shadow">
                    <div class="items-center p-2">
                        <input name="name" class="w-full grow px-2 py-2 border rounded border-gray-200 focus:outline-none"
                            type="text" placeholder="Name" required>
                    </div>
                    <div class="flex items-center gap-3 p-2">
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
            {{ $addon_services->links('tailwind.components.paginator') }}
            <hr class="my-3">

            <div class="grid gap-4">
                @foreach ($addon_services as $index => $addon_service)
                    <div class="rounded overflow-hidden shadow p-2 border">
                        <div class="flex justify-between items-center gap-3 p-2">
                            <span class="text-sky-600 rounded px-2 py-1 border border-dashed border-sky-400">
                                ID: {{ $addon_service->id }}
                            </span>
                            <input class="shrink-0 px-3 py-1.5 rounded bg-sky-400 text-white focus:outline-none cursor-pointer"
                                type="button" value="Save"
                                @click="updateLink({{ $addon_service->id }},{{ $index }})">
                        </div>
                        <div class="p-2">
                            <input id="name__link__{{ $index }}"
                                class="w-full grow px-2 py-2 border rounded border-gray-200 focus:outline-none" type="text"
                                value="{{ $addon_service->name ?? '' }}" placeholder="Name">
                        </div>
                        <div class="p-2 space-y-2">
                            <h3 class="text-gray-400 font-bold">Contents</h3>
                            <div class="grid gap-4" id="addonContentContainer{{ $addon_service->id }}">
                                @foreach ($addon_service->addon_contents as $index => $addon_content)
                                <div class="flex items-center gap-2 border border-dashed border-sky-300 rounded p-2">
                                    <input class="grow px-2 py-2 border rounded border-gray-200  focus:outline-none" type="text" value="{{ $addon_content->name ?? '' }}">
                                    <svg onclick="removeContentSlot(this.parentElement)" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-500 cursor-pointer" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </div>
                                @endforeach
                            </div>
                            <div class="mt-6 flex items-center justify-center gap-4">
                                <button
                                    @click="addNewContentSlot({{ $addon_service->id }}, 'lecture')"
                                    type="button" 
                                    class="flex justify-center items-center px-2 py-1 rounded shrink-0 text-rose-600 cursor-pointer gap-1 border border-dashed border-rose-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span class="font-semibold text-xs">Add Lecture Slot</span>
                                </button>
                                <button
                                    @click="addNewContentSlot({{ $addon_service->id }}, 'exam')"
                                    type="button" 
                                    class="flex justify-center items-center px-2 py-1 rounded shrink-0 text-purple-600 cursor-pointer gap-1 border border-dashed border-purple-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span class="font-semibold text-xs">Add Exam Slot</span>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="__modal__container__ overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 w-full md:inset-0 h-modal md:h-full justify-center items-center bg-gray-400/50">
        <div class="relative w-full h-full flex justify-center items-center">
            <div class="w-full relative max-w-sm bg-white rounded-lg shadow dark:bg-gray-700 z-40">
                <div class="flex justify-end p-2">
                    <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-800 dark:hover:text-white" onclick="this.closest('.__modal__container__').classList.add('hidden')">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                    </button>
                </div>
                <form class="px-6 pb-4 space-y-6 lg:px-8 sm:pb-6 xl:pb-8" action="#">
                    <h3 class="text-xl font-medium text-gray-900 dark:text-white">Create Addon Service</h3>
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">Addon Service Name</label>
                        <input type="text" name="name" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white" required autocomplete="off">
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">Raguler Amount</label>
                        <input type="number" name="amount" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white" required autocomplete="off">
                    </div>
                    <button type="submit" class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                        Save
                    </button>
                </form>
            </div>
        </div>
    </div>


    {{ $addon_services->links('tailwind.components.paginator') }}

    <script>
        function addNewContentSlot(id, type) {
            let divElement = document.createElement("div");

            const slotTypeData = new Array();

            const slotTypeClass = new Array();

            slotTypeClass['lecture'] = "grid gap-2 border border-dashed border-rose-300 rounded p-2";

            slotTypeClass['exam'] = "grid gap-2 border border-dashed border-purple-300 rounded p-2";

            divElement.className = slotTypeClass[type];

            slotTypeData['lecture'] = `
                <div class="flex items-center gap-2">
                    <input class="grow px-2 py-2 border rounded border-rose-300 text-rose-600 focus:outline-none placeholder-rose-300" type="text" placeholder="Title">
                    <svg onclick="removeContentSlot(this.parentElement.parentElement)" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-500 cursor-pointer" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </div>
                <div class="flex items-center gap-2">
                    <div class="grow-0 shrink-0 w-8 h-8">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-full stroke-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="line-clamp-1">Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum</div>
                    <div class="grow-0 shrink-0 w-6 h-6 cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-full stroke-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                        </svg>
                    </div>
                </div>
            `;

            slotTypeData['exam'] = `
                <div class="flex items-center gap-2">
                    <input class="grow px-2 py-2 border rounded border-purple-300 text-purple-600 focus:outline-none placeholder-purple-300" type="text" placeholder="Title">
                    <svg onclick="removeContentSlot(this.parentElement.parentElement)" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-500 cursor-pointer" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </div>
                <div class="flex items-center gap-2">
                    <div class="grow-0 shrink-0 w-8 h-8">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-full stroke-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                    </div>
                    <div class="line-clamp-1">Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum</div>
                    <div class="grow-0 shrink-0 w-6 h-6 cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-full stroke-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                        </svg>
                    </div>
                </div>
            `;

            divElement.innerHTML = slotTypeData[type];

            document.getElementById(`addonContentContainer${id}`).appendChild(divElement);
        }

        function removeContentSlot(slot) {
            return slot.parentElement.removeChild(slot)
        }


        function updateLink(id, index) {
            // console.log(index);
            let name = document.getElementById(`name__link__${index}`).value;
            // console.log(name);
            let total_pages = document.getElementById(`total_pages__${index}`).value;
            // console.log(total_pages);
            let icon = document.getElementById(`bell__icon__${index}`);

            let data = axios.post(`/admin/addon-services/${id}`, {
                    name,
                    total_pages,
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
