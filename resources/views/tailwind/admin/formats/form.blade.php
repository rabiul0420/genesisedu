<div id="formContainer{{ $format->id }}" class="__formContainer__ bg-gray-600/50 hidden fixed inset-0 overflow-y-auto z-50">
    <div class="max-w-xl m-4 md:mx-auto md:my-16 z-20 bg-white rounded p-4 space-y-3">
        <div class="flex justify-between items-center">
            <div class="text-indigo-500 text-lg bg-indigo-200 px-3 py-1 rounded-md">
                <h3 class="font-bold">{{ $format->property }}</h3>
            </div>
            <button 
                type="button"
                class="text-4xl text-rose-500 -mt-2 cursor-pointer" 
                onclick="this.closest('.__formContainer__').classList.add('hidden')"
            >
                &times;
            </button>
        </div>

        <div>
            <form action="{{ route('formats.update', $format->id ?? '') }}" method="POST">
                {{ csrf_field() }}
                {{ method_field('PUT') }}
                <div class="grid gap-4">
                    <div class="border border-dashed border-sky-400 rounded-md p-3 grid col-span-full gap-4">
                        <div class="grid gap-x-2 gap-y-1">
                            <label class="text-sm text-gray-500">Dynamic Value</label>
                            <div class="flex justify-start items-center flex-wrap gap-1">
                                @foreach($format->keys as $key)
                                <span class="px-2 py-1 bg-gray-200 select-all rounded-lg">{{ $key }}</span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="border border-dashed border-sky-400 rounded-md p-3 grid col-span-full gap-4">
                        <div class="grid gap-x-2 gap-y-1">
                            <label class="text-sm text-gray-500">{{ \App\Format::TYPES[$format->type] }}</label>
                            <textarea name="body" class="px-2 py-1.5 border rounded w-full" autocomplete="off" rows="5" required>{!! $format->body !!}</textarea>
                        </div>
                    </div>
                </div>
        
                <div class="flex justify-end items-center py-4">
                    <button class="px-4 py-1.5 border rounded-md bg-sky-500 text-white">
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>