@extends('tailwind.layouts.admin')

@section('content')

{{ $subscribers->links('tailwind.components.paginator') }}
    
<hr class="my-3 print:hidden">
    
<div class="grid gap-4 md:grid-cols-3 2xl:grid-cols-5 print:grid-cols-2">
    @foreach ($subscribers as $subscriber)
    <a href="{{ route('subscribers.orders.index', $subscriber->id) }}" class="block rounded border space-y-2 p-4 bg-gradient-to-t from-cyan-500 to-blue-500 hover:to-cyan-500 hover:from-blue-500 text-white transition-all ease-linear">
        @include('tailwind.admin.subscribers.card', compact('subscriber'))
    </a>
    @endforeach
</div>

<hr class="my-3 print:hidden">

{{ $subscribers->links('tailwind.components.paginator') }}

@endsection