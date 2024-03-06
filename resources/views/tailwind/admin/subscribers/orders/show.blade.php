@extends('tailwind.layouts.admin')

@section('content')

<div class="grid gap-4 md:grid-cols-3 my-4">
    <div class="block rounded border shadow space-y-2 p-4 bg-white">
        @include('tailwind.admin.subscribers.orders.card', compact('order'))
    </div>
    <div class="block rounded border space-y-2 p-4 bg-white hover:bg-gray-50  transition-all ease-linear">
        @include('tailwind.admin.subscribers.card', ['subscriber' => $order->doctor])
        <a 
            href="{{ route('subscribers.orders.index', $order->doctor_id) }}"
            class="block text-center py-2 px-3 border rounded-lg bg-sky-600 hover:bg-sky-700 text-white"
        >
            See All Order
        </a>
    </div>
</div>

<hr class="my-3">

<h3 class="text-xl font-bold">Items</h3>

<div class="grid gap-4 md:grid-cols-3 my-1">
    @foreach($order->subscriptions as $subscription)
    <div class="border rounded shadow px-4 py-3 bg-white">
        <div class="flex justify-between">
            <div>
                Id: <b class="text-2xl">{{ $subscription->subscriptionable_id }}</b>
            </div>
            <div>
                Price: <b class="text-2xl">{{ $subscription->price }}</b>TK
            </div>
        </div>
        <hr class="my-3">
        <div class="break-all select-all">
            <b>{{ $subscription->subscriptionable->name ?? '' }}</b>
        </div>
    </div>
    @endforeach
</div>

<hr class="my-3">

<h3 class="text-xl font-bold">Payments</h3>

<div class="grid gap-4 md:grid-cols-3 my-1">
    @if(!$order->payment_status)
    <button 
        type="button"
        onclick="document.getElementById('formContainer').classList.remove('hidden')"
        class="border-2 border-sky-600 text-sky-600 border-dashed rounded px-2 py-8 flex justify-center items-center gap-1 cursor-pointer"
    >
        <svg class="w-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <span>New Payment</span>
    </button>

    @include('tailwind.admin.subscribers.orders.payments.form', compact('subscriber', 'order'))
    @endif

    @foreach($order->payments as $payment)
    <div class="border rounded shadow px-2 py-4 bg-white text-center space-y-2">
        <div>
            <span class="text-5xl">{{ $payment->amount ?? '' }}</span> TK
        </div>
        <div>
            <span class="text-gray-500 select-none">TrxID:</span>
            <span class="select-all font-semibold">{{ $payment->trans_id ?? '' }}</span>
        </div>
        @if($payment->note_box)
        <hr class="my-2">
        <div class="text-base">
            <i>{{ $payment->note_box ?? '' }}</i>
        </div>
        @endif
    </div>
    @endforeach
</div>

<hr class="my-3">

<h3 class="text-xl font-bold">Manual Payments</h3>

<div class="grid gap-4 md:grid-cols-3 my-1">
    @foreach($order->manual_payments as $manual_payment)
    <div class="border rounded shadow px-2 py-4 bg-white text-center space-y-2">
        <div>
            <span class="text-5xl">{{ $manual_payment->amount ?? '' }}</span> TK
        </div>
        <div>
            <span class="text-gray-500 select-none">TrxID:</span>
            <span class="select-all font-semibold">{{ $manual_payment->trx_id ?? '' }}</span>
        </div>
        @if($manual_payment->note)
        <hr class="my-2">
        <div class="text-base">
            <i>{{ $manual_payment->note ?? '' }}</i>
        </div>
        @endif
    </div>
    @endforeach
</div>

@endsection