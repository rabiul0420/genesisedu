@extends('layouts.app')
@section('content')
    <section id="batch_part">
        <div class="container px-sm-0">
            <div class="big-demo" data-js="hero-demo">
                <div class="row">
                    <div class="col py-2 text-uppercase">
                        <h2>Terms & Conditions</h2>
                    </div>
                </div>
                <div class="container">
                    <div class="row ">
                        <div class="col">
                            <div class="text px-lx-3 py-2 lh-lg" style="text-align: justify">
                                {!! $value !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
