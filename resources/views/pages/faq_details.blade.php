@extends('layouts.app')
@section('content')
<div class="container">
        <div class="row faq">
            <div class="col-md-8 bg-white p-3 rounded mt-3"> 
                @if($faq_details->description)
                    <style>
                        p{
                            padding: 10px;
                        }
                    </style>
                    <h4>{!! $faq_details->title !!}</h4>
                    <br>
                    {!! $faq_details->description !!}
                    
                @else
                <span class="text-muted">Plase Enter the answare</span>
                @endif
               
            </div>
            <div class="col-md-4 rounded">
                <table class="table bg-white rounded mt-3">
                    <tbody>
                        @foreach ($faqs as $faq)
                                <tr class="residency">
                                    <td style="border: none">
                                    <a href="{{ url('faq-details/'. $faq->id)}}">{!! $faq->title !!}</a>
                                    </td>
                                @endforeach
                    </tbody>
                </table>
            </div>
        </div>
</div>

@endsection
