@extends('tailwind.layouts.admin')

@section('content')

<div class="grid gap-4 md:grid-cols-3 my-4">
    <div class="block rounded border space-y-2 p-4 bg-white">
        @include('tailwind.admin.subscribers.card', compact('subscriber'))
    </div>
</div>

<hr class="my-3">

<div class="grid gap-4 md:grid-cols-3 my-4">
    @foreach($subscriber->subscription_orders as $order)
    <a href="{{ route('subscribers.orders.show', [$subscriber->id, $order->id]) }}" class="block rounded border space-y-2 p-4 bg-white">
        @include('tailwind.admin.subscribers.orders.card')
    </a>
    @endforeach
</div>

@endsection