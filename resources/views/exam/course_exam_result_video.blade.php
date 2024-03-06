@extends('layouts.app')

@section('content')
            <div class="container">
                <div class=''>
                    Question: {!! $data->question_title !!}
                </div>

                <div class="panel panel-default pt-2">
                    <div class="panel_box w-100 bg-white rounded shadow-sm">
                        <div class="header text-center py-3">
                            <h2 class="h2 brand_color">Video Password : {{ $data->video_password ?? ''}}</h2>
                        </div>
                    </div>
                    <div class=''>
                        <div class="col-md-6">
                            @foreach ($data->question_video_links as $item )
                                <iframe width='100%' height='400' src='{{  $item->video_link ?? '' }}' frameborder='0' 
                                allow='accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture' allowfullscreen></iframe>
                            @endforeach 
                        </div>
                    </div> 
                </div>

            </div>
@endsection