@extends('layouts.app')
@section('content')
    <section id="banner_part">
        <div class="container px-sm-0">
            <div class="row align-items-center gy-4">
                <div class="mx-auto col-xl-5 col-lg-6">
                    <div class="box shadow rounded-lg h-100">
                        <div id="carouselExampleControls" class="carousel slide" data-ride="carousel">

                            <div>
                                <h4 class="text-center px-3 pt-2" style="color: #ffffff">
                                    Welcome
                                </h4>
                                <h5 class="text-center px-3 pb-1" style="color: #ffffff">
                                    to <br> <b>GENESIS</b> career counselling & psychological support center.
                                </h5>
                                <h5 class="text-center px-3 py-2" style="color: #ffffff">
                                    <b>Appointment will be started <br> from 15-01-2022</b>
                                </h5>

                                <a href="https://gcpsc.info/" class="btn btn-info btn-xs my-2">
                                    Click for Appointment
                                </a>

                                {{-- <a href="https://forms.gle/znp3bX8vUK95zHtn8" class="btn btn-info btn-xs my-2">
                                Click for Appointment
                            </a> --}}
                            </div>

                            <div class="carousel-inner rounded-lg"
                                style="height: 400px; background: #fff; position: relative;">
                                @foreach ($images as $image)
                                    <div class="carousel-item @if ($loop->first) active @endif"
                                        style="background: #fff; object-fit: cover">
                                        <img src="{{ asset($image) }}" class="d-block w-100">
                                    </div>
                                @endforeach
                            </div>

                            <a class="carousel-control-prev" href="#carouselExampleControls" role="button"
                                data-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="sr-only">Previous</span>
                            </a>
                            <a class="carousel-control-next" href="#carouselExampleControls" role="button"
                                data-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="sr-only">Next</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
