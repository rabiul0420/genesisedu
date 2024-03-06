@extends('tailwind.layouts.admin')

@section('content')

<div class="grid gap-4 md:grid-cols-2 my-4">
    <div class="block rounded border space-y-2 p-4 bg-gradient-to-t from-cyan-500 to-blue-500 text-white">
        @include('tailwind.admin.subscribers.card', compact('subscriber'))
    </div>
</div>
@endsection