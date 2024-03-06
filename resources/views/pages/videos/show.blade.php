@extends('layouts.app')

@section('content')
    <section class="bg-info">
        <div class="headign">
            <h3 class="text-center display-6 py-lg-5 py-2 text-white text-uppercase">{{ $video->name }}</h3>
        </div>
        <div class="container">
            <div style="text-align: center;">
                <div class="mx-auto" style="position: relative; width: 100%; height: 400px;">
                    <iframe src="{{ $video->url }}" style="position: absolute; width: 100%; height: 90%; top: 0; left: 0;" frameborder="0" webkitallowfullscreen="" mozallowfullscreen="" allowfullscreen=""></iframe>
                </div>
                <div style="height: 300px;"></div>
            </div>
        </div>
    </section>
@endsection
