<!-- <div>
    <span class="text-gray-500 select-none">Doctor Id:</span>
    <b class="text-3xl select-all">{{ $subscriber->id }}</b>
</div> -->
<div class="flex gap-1 text-gray-800">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-6-3a2 2 0 11-4 0 2 2 0 014 0zm-2 4a5 5 0 00-4.546 2.916A5.986 5.986 0 0010 16a5.986 5.986 0 004.546-2.084A5 5 0 0010 11z" clip-rule="evenodd" />
    </svg>
    <a href="{{ route('go-to-doctor-profile', $subscriber->id) }}" target="_blank" class="underline text-sky-600 underline-offset-2">
        <b>{{ $subscriber->name ?? '' }}</b>
    </a>
</div>
<div class="flex flex-wrap gap-4">
    <div class="flex gap-1 text-gray-800">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
            <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
        </svg>
        <b>{{ $subscriber->phone ?? '' }}</b>
    </div>
    <div class="flex gap-1 text-gray-800">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
            <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z" />
            <path fill-rule="evenodd" d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z" clip-rule="evenodd" />
        </svg>
        <b>{{ $subscriber->bmdc_no ?? '' }}</b>
    </div>
</div>
<hr class="my-3">
<div class="grid grid-cols-2 gap-4 py-2">
    <div class="flex flex-col items-center justify-center bg-gray-300/30 p-3 rounded-xl">
        <span class="text-5xl select-all">
            {{ $subscriber->subscription_orders->where('payment_status', 1)->count() ?? 0 }}
        </span>
        <span class="text-sm">Paid Orders</span>
    </div>
    <div class="flex flex-col items-center justify-center bg-gray-300/30 p-3 rounded-xl">
        <span class="text-5xl select-all">
            {{ $subscriber->subscription_orders->where('payment_status', 0)->count() ?? 0 }}
        </span>
        <span class="text-sm">Pending Orders</span>
    </div>
</div>