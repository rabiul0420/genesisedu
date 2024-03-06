<div id="formContainer" class="__formContainer__ bg-gray-600/50 hidden fixed inset-0 overflow-y-auto z-50">
    <div class="max-w-xl m-4 md:mx-auto md:my-16 z-20 bg-white rounded p-4 space-y-3">
        <div class="flex justify-end items-center">
            <button 
                type="button"
                class="text-4xl text-rose-500 -mt-2 cursor-pointer" 
                onclick="this.closest('.__formContainer__').classList.add('hidden')"
            >
                &times;
            </button>
        </div>

        <div>
            <form action="{{ route('subscriptions.orders.payments.store', [$order->id]) }}" method="POST">
                {{ csrf_field() }}
                <div class="grid gap-4">
                    <div class="border border-dashed border-sky-400 rounded-md p-3 grid md:grid-cols-2 col-span-full gap-4">
                        <div class="grid gap-x-2">
                            <label class="text-sm text-gray-500">Trans ID (*)</label>
                            <input type="text" name="trans_id" class="px-2 py-1.5 border rounded w-full"  autocomplete="off" required />
                        </div>
                        <div class="grid gap-x-2">
                            <label class="text-sm text-gray-500">Amount (*)</label>
                            <input type="number" name="amount" class="px-2 py-1.5 border rounded w-full"  autocomplete="off" required />
                        </div>
                        <div class="grid gap-x-2 col-span-full">
                            <label class="text-sm text-gray-500">Note Box</label>
                            <input type="text" name="note_box" class="px-2 py-1.5 border rounded w-full"  autocomplete="off" />
                        </div>
                    </div>
                </div>
        
                <div class="flex justify-between items-center py-4">
                    <label class="flex gap-2 items-center">
                        <input type="checkbox" required />
                        আমি ঘোষণা করছি যে সমস্ত তথ্য সঠিক
                    </label>
                    <button type="submit" class="px-4 py-1.5 border rounded-md bg-sky-500 text-white">
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>