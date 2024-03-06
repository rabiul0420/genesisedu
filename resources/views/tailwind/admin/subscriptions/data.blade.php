<div class="grid gap-4 md:grid-cols-3 2xl:grid-cols-5 print:grid-cols-3">
    <div class="px-2 py-2 border border-sky-400 text-sky-600 rounded text-center">
        Payment Total: <b>{{ $total_amount }} TK</b>
    </div>
    <div class="hidden md:block px-2 py-2 border border-sky-400 text-sky-600 rounded text-center">
        From Web: <b>{{ $total_amount - $total_amount_by_app }} TK</b>
    </div>
    <div class="hidden md:block px-2 py-2 border border-sky-400 text-sky-600 rounded text-center">
        From App: <b>{{ $total_amount_by_app }} TK</b>
    </div>
</div>

<hr class="my-3">

{{ $orders->links('tailwind.components.search-method-paginator') }}

<hr class="my-3">

<div class="grid gap-4 md:grid-cols-4 lg:grid-cols-5 my-4">
    @foreach($orders as $order)
    <a href="{{ route('subscribers.orders.show', [$order->doctor_id, $order->id]) }}" class="block rounded border space-y-2 p-3 bg-sky-600">
        @include('tailwind.admin.subscriptions.card', compact('order'))
    </a>
    @endforeach
</div>

<hr class="my-3">

{{ $orders->links('tailwind.components.search-method-paginator') }}