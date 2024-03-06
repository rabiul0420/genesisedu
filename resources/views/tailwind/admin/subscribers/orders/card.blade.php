<div>
    <span class="text-gray-500 select-none">Order Id:</span>
    <b class="text-3xl select-all">{{ $order->id }}</b>
</div>
<div>
    <span class="text-gray-500 select-none">Payable:</span>
    <b class="text-3xl select-all">{{ $order->payable_amount }}</b>
    <span class="text-gray-500 select-none">TK</span>
</div>
<div>
    <span class="text-gray-500 select-none">Payment:</span>
    <b class="text-3xl select-all {{ $order->payment_status ? 'text-green-600' : 'text-rose-600' }}">
        {{ $order->payment_status ? 'Paid' : 'Unpaid' }}
    </b>
</div>
<div>
    <span class="text-gray-500 select-none">Total Items:</span>
    <b class="text-3xl select-all">{{ $order->items_count }}</b>
</div>
@if($order->expired_at)
<div>
    <span class="text-gray-500 select-none">Expired At:</span>
    <b class="text-2xl select-all">{{ $order->expired_at }}</b>
</div>
@endif