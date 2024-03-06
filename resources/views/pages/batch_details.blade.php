
@extends('layouts.app')
@section('title', ($available_batch->batch_name ?? '') . ' | ' . 'GENESIS')
@section('meta')
<meta name="title" content="{{ $available_batch->batch_name ?? '' }}" />

    <!-- Facebook -->
    <meta property="og:url" content="{{ url()->current() }}" />
    <meta property="og:title" content="{{ $available_batch->batch_name ?? '' }}" />
    <meta property="og:description" content="" />
    <meta property="og:image" content="{{ $available_batch->meta_banner ?? 'https://gen-file.s3.ap-southeast-1.amazonaws.com/storage/2022/07/19/8asLUM1CDyuBYUc584Wh.jpg' }}" />

@endsection
@section('content')
    <style>
        @media screen and (max-width: 600px) {
            .media-screen{
                width: 100% !important;
            }
            h1,h2,
            h3,h4,
            h5,h6{
                width: 100%;
                overflow: auto;
                font-size: 16px;
            }

        }

    </style>
    <div class="container rounded media-screen" style="display: flex; justify-content:center;margin-top:50px; width:50%;background:#fff;margin-bottom: 20px;">
    <div class="align-left w-100 " style=" margin-top:50px; font-family:Verdana, Geneva, Tahoma, sans-serif;margin-bottom: 20px;">
        @if($available_batch->details ?? false)
            <style>
                p{
                    padding: 10px
                }
            </style>         
            {!! $available_batch->details !!}
            
        @else
          <span class="text-muted">No Data</span>
        @endif
     
        <a href="{{ url( 'admission-link/'. $batch_id ) }}" class="pull-right btn btn-primary" style="" target="_blank">Admisson Link</a>
        <a style="margin: 0 20px" class="btn btn-warning pull-right {{($available_batch->batch->id ?? 0) ? '' : 'disabled'}}"
           href="{{ url('view-batch-schedule/'.($available_batch->batch->id ?? 0))}}">View Schedule</a>
    </div>
    </div>

@endsection
