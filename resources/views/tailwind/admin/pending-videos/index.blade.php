@extends('tailwind.layouts.admin')

@section('content')

{{ $batches->links('tailwind.components.paginator') }}

<hr class="my-3">

<div class="grid gap-4 md:grid-cols-2 2xl:grid-cols-3">
    @foreach ($batches as $batch)
    <a href="{{ route('pending-videos.show', $batch->id) }}" class="block rounded border shadow-sm space-y-2 p-4 bg-white hover:bg-gray-100">
        <div class="flex items-center gap-2">
            <div class="shrink grow flex justify-start items-center gap-2">
                <div class="shrink-0 grow-0 text-indigo-600 bg-indigo-100 px-2 py-1 rounded">ID: {{ $batch->id }}</div>
                <div class="shrink-0 grow-0 text-gray-600 bg-gray-200 px-2 py-1 rounded font-semibold">{{ $batch->year ?? '' }}</div>
            </div>
            <div class="shrink-0 grow-0">
                <div class="shrink-0 grow-0 relative {{ $batch->pending_videos->count() ? 'text-green-600' : 'text-rose-600' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 scale-x-[1.2] stroke-[0.5]" fill="none" viewBox="2 5 20 14" stroke="currentColor" stroke-width="1">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                    <div class="absolute left-3.5 top-1/2 -translate-x-1/2 -translate-y-1/2">
                        {{ $batch->pending_videos->count() ?? 0 }}
                    </div>
                </div>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <div class="shrink-0 grow-0 text-gray-500 text-sm">Batch:</div>
            <div class="shrink grow font-semibold select-all text-sm text-sky-600">{{ $batch->name ?? '' }}</div>
        </div>
        <div class="flex items-center gap-2">
            <div class="shrink-0 grow-0 text-gray-500 text-sm">Course:</div>
            <div class="shrink grow font-semibold select-all text-sm">{{ $batch->course->name ?? '' }}</div>
        </div>
        <div class="flex items-center gap-2">
            <div class="shrink-0 grow-0 text-gray-500 text-sm">Session:</div>
            <div class="shrink grow select-all text-sm">{{ $batch->session->name ?? '' }}</div>
        </div>
        <!-- <div class="flex items-center gap-2">
            <div class="flex items-center gap-2 bg-gray-200 text-gray-700 rounded-full px-4 py-1">
                <div class="shrink-0 grow-0  text-sm">Faculty:</div>
                <div class="shrink grow text-sm">{{ $batch->faculties->count() ?? 0 }}</div>
            </div>
            <div class="flex items-center gap-2 bg-gray-200 text-gray-700 rounded-full px-4 py-1">
                <div class="shrink-0 grow-0  text-sm">Discipline:</div>
                <div class="shrink grow text-sm">{{ $batch->subjects->count() ?? 0 }}</div>
            </div>
        </div> -->
    </a>
    @endforeach
</div>

<hr class="my-3">

{{ $batches->links('tailwind.components.paginator') }}

@endsection