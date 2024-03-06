@extends('layouts.app')
@section('content')
<section>
    <div class="container py-lg-5 py-3">
        <div class="row row-cols-lg-2 row-cols-1 g-4">
            @foreach($photos as $photo)
            @if($photo['image'])
            <div class="col">
                <img class="img-fluid w-100" src="{{asset($photo['image'])}}" alt=""
                    style="height: 240px; object-fit: cover; border-radius: 10px;" loading="lazy">
            </div>
            @endif
            @endforeach
        </div>
    </div>
</section>
@endsection
