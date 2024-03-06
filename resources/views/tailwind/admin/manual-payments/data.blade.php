<!-- <div class="grid gap-4 md:grid-cols-3 2xl:grid-cols-5 print:grid-cols-3">
    <div class="px-2 py-2 border border-sky-400 text-sky-600 rounded text-center">
        Payment Total: <b> TK</b>
    </div>
</div>

<hr class="my-3"> -->

{{ $manual_payments->links('tailwind.components.search-method-paginator') }}

<hr class="my-3">

<div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3 my-4">
    @foreach($manual_payments as $manual_payment)
    <div class="block rounded border space-y-2 p-3 {{ $manual_payment->paymentable->is_paid ? 'bg-green-600' : 'bg-rose-600/80' }}">
        @include('tailwind.admin.manual-payments.card', compact('manual_payment'))
    </div>
    @endforeach
</div>

<hr class="my-3">

{{ $manual_payments->links('tailwind.components.search-method-paginator') }}