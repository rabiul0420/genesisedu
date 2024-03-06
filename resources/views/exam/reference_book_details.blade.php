@extends('layouts.app')

@section('content')

    <h5 class="text-center mt-2">{{ $reference->reference_book->name ?? '' }}</h5>

    <div style="max-width: 576px; margin: 8px auto; border: 1px solid #ccc; border-radius: 10px;">
        <div class="row">
            <div class="px-4">
                <div class="pt-2 text-center ck-content">
                    {!! $reference->body !!}
                </div>
            </div>
        </div>
    </div>

    <div class="mb-4" style="display: flex; justify-content: center; align-items: center; gap: 16px;">

        @if ($previous)
            <a href="{{ route('reference-book-detail', [$previous->reference_book_id, $previous->page_no]) }}"
                class="btn btn-info rounded-pill" style="width: 38px">&#10094;</a>
        @else
            <div class="btn btn-secondary rounded-pill" style="width: 38px; cursor: not-allowed;">&#10094;</div>
        @endif

        @if ($next)
            <a href="{{ route('reference-book-detail', [$next->reference_book_id, $next->page_no]) }}"
                class="btn btn-info rounded-pill" style="width: 38px">&#10095;</a>
        @else
            <div class="btn btn-secondary rounded-pill" style="width: 38px; cursor: not-allowed;">&#10095;</div>
        @endif

    </div>

@endsection
