@extends('layouts.app')
@section('content')
<section id="batch_part">
    <style>
        @@media screen and (max-width: 600px){
            .py-2{
                width: 100%;
                margin-left: auto !important;
                margin-right: auto !important;
            }
        }
    </style>
    <div class="container px-sm-0">
        <div class="big-demo" data-js="hero-demo">
            <div class="row">
                <div class="col py-2">
                    <h2>FAQ</h2>
                </div>
            </div>

            <div class="row">
                <div class="table__responsive col py-2"
                    style="display: flex;
                    align-items: left;
                    text-align: left;
                    margin-left: 20%;
                    margin-right: 20%;">
                    <table class="table w-100 ">
                        <tbody>
                            @foreach ($faqs as $faq)
                            <tr class="residency">
                                <td style="border: none">
                                      <a href="{{ url('faq-details/'. $faq->id)}}">{!! $faq->title !!}
                                     </a>
                                </td>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection