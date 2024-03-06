<div id="formContainer{{ $label->id }}" class="__formContainer__ bg-gray-600/50 hidden fixed inset-0 overflow-y-auto z-50">
    <div class="max-w-xs m-4 md:mx-auto md:my-16 z-20 bg-white rounded p-4 space-y-3">
        <div class="flex justify-between items-center">
            <div class="text-indigo-500 text-lg bg-indigo-200 px-3 py-1 rounded-md">
                @if($label->id ?? false)
                <span>ID : {{ $label->id ?? 'New' }}</span>
                @else
                <span>Add New</span>
                @endif
            </div>
            <button 
                type="button"
                class="text-4xl text-rose-500 -mt-2 cursor-pointer" 
                onclick="this.closest('.__formContainer__').classList.add('hidden')"
            >
                &times;
            </button>
        </div>

        <form action="{{ route('labels.save', $label->id ?? '') }}" method="POST">
            {{ csrf_field() }}
        
            <div class="grid px-4 py-4 gap-4 border border-dashed border-sky-400 rounded-md">
                <div class="grid gap-1">
                    <label class="text-gray-400">Label Name</label>
                    <input type="text" name="name" value="{{ $label->name }}" class="px-2 py-2 border rounded block" autocomplete="off" required />
                </div>
                <div class="grid gap-1">
                    <label class="text-gray-400">Status</label>
                    <select name="status" class="px-2 py-2 border rounded block" required>
                        <option value="1" {{ $label->status == '1' ? 'selected' : '' }} >Active</option>
                        <option value="0" {{ $label->status == '0' ? 'selected' : '' }} >Inactive</option>
                    </select>
                </div>
            </div>
    
            <div class="flex justify-end items-center py-4">
                <button class="px-4 py-1.5 border rounded-md {{ $label->status != '2' ? 'bg-sky-500' : 'bg-rose-500' }} text-white">
                    Save
                </button>
            </div>
        </form>
    </div>
</div>