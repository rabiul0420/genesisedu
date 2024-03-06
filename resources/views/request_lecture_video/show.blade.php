@extends('layouts.app')

@section('content')

<div class="container">

    <div class="row">

        @include('side_bar')

        <div class="col-md-9 col-md-offset-0">
            <div class="panel panel-default pt-2">
                <div class="panel_box w-100 bg-white rounded shadow-sm">
                    <div class="header text-center py-3">
                        <h2 class="h2 brand_color">Pending Lecture Video</h2>
                    </div>
                </div>

                <div class="panel-body px-0">
                    @if (session('status'))
                    <div class="alert alert-success">
                        {{ session('status') }}
                    </div>
                    @endif

                    <div class="col-md-12 bg-white mt-2 rounded-lg">
                        <div class="portlet">
                            <div class="portlet-body">
                                <div class="p-3">
                                    <div class="p-3 rounded-lg" style="border: 1px dashed #ccc;">
                                        <h6 class="text-secondary">{{  $doctor_course->course->name ?? '' }}</h6>
                                        <h5 class="text-primary">{{  $doctor_course->batch->name ?? '' }}</h5>
                                    </div>
                                </div>

                                @if($current_lecture_video && $current_lecture_video->video->link)
                                <div class="p-3">
                                    <h5 class="pb-2 text-center">
                                        Password: {{ $current_lecture_video->video->password ?? '' }}
                                    </h5>
                                    <div class="rounded-lg" style="overflow: hidden; position: relative; border: 1px solid #f0f0f0;">
                                        <div class="w-100" style="padding:56.25% 0 0 0;position:relative;">
                                            <iframe src="{{ $current_lecture_video->video->link }}" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen="" style="position:absolute;top:0;left:0;width:100%;height:100%;"></iframe>
                                        </div>
                                        <script src="https://player.vimeo.com/api/player.js"></script>
                                    </div>
                                    <form action="{{ route('request-lecture-video.complete', [$doctor_course->id, $current_lecture_video->id]) }}" method="POST" class="mt-4">
                                        {{ csrf_field() }}
                                        <label class="block">
                                            <input type="checkbox" required> Complete This Video
                                        </label>
                                        <input type="submit" value="Confirm" class="btn btn-info btn-sm px-2" />
                                    </form>
                                </div>                        
                                @endif

                                @if($request_lecture_videos->count())
                                <div class="p-3">
                                    @foreach ($request_lecture_videos as $request_lecture_video)
                                    @if(($request_lecture_video->status == 1 || (!$has_current_watching_video && $request_lecture_video->pending_video->video->link)) && $request_lecture_video->status != 2)
                                    <a href="{{ route('request-lecture-video.show', [$doctor_course->id, $request_lecture_video->id]) }}" class="video__play__item">
                                        <span class="video__play__button" style="background: #dd097e;"></span>
                                        <div class="video__play__title__container">
                                            <div class="video__play__title">
                                                {{ $request_lecture_video->pending_video->video->name ?? '' }}
                                            </div>
                                            <div class="video__play__status">
                                                {!! $request_lecture_video->status_message ?? '' !!} <span id="end_coundown"></span>
                                            </div>
                                        </div>

                                        @if($request_lecture_video->end)
                                        <script>
                                            var countDownDate = new Date("{{ $request_lecture_video->end }}").getTime();

                                            var x = setInterval(function() {
                                                var now = new Date().getTime();
                                                var distance = countDownDate - now;

                                                var days = Math.floor(distance / (1000 * 60 * 60 * 24));
                                                var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                                                var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                                                var seconds = Math.floor((distance % (1000 * 60)) / 1000);

                                                document.getElementById("end_coundown").innerHTML = days + "d " + hours + "h " + minutes + "m " + seconds + "s ";

                                                if (distance < 0) {
                                                    clearInterval(x);
                                                    window.location.reload();
                                                }
                                            }, 1000);
                                        </script>
                                        @endif
                                    </a>
                                    @else
                                    <div class="video__play__item">
                                        <span class="video__play__button" style="background: #ddd;"></span>
                                        <div class="video__play__title__container">
                                            <div class="video__play__title">
                                                {{ $request_lecture_video->pending_video->video->name ?? '' }}
                                            </div>
                                            <div class="video__play__status">
                                                {!! $request_lecture_video->status_message ?? '' !!}
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                    @endforeach
                                </div>
                                @endif

                                @if($request_available)
                                <div class="p-3">
                                    @if($batch_pending_videos->count())
                                    @for ($i = 1; $i <= $request_available && $i <=$batch_pending_videos->count(); $i++)
                                    <form
                                        method="POST"
                                        action="{{ route('request-lecture-video.request', $doctor_course->id) }}"
                                        class="px-2 py-2 rounded-lg d-flex mb-2 align-items-center"
                                        style="border: 1px dashed #ccc;"
                                    >
                                        {{ csrf_field() }}
                                        <select name="pending_video_id" class="select2 form-select" required>
                                            <option value=""> --Select Lecture-- </option>
                                            @foreach ($batch_pending_videos as $batch_pending_video)
                                            <option value="{{ $batch_pending_video->id }}">{{ $batch_pending_video->video->name }}</option>
                                            @endforeach
                                        </select>
                                        <div class="pl-2">
                                            <input class="btn btn-sm btn-info" type="submit" value="Submit"/>
                                        </div>
                                    </form>
                                    @endfor
                                    @else
                                    <div class="text-center text-danger">
                                        <b>Please! Wait until upload lecture videos</b>
                                    </div>
                                    @endif
                                </div>
                                @endif

                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>


</div>
@endsection

@section( 'js' )
    <link href="{{ asset('css/select2.css') }}" type="text/css" rel="stylesheet"/>
    <script src="{{ asset('js/select2.js') }}" type="text/javascript"></script>

    <script>
        $(document).ready(function() {
            $('.select2').select2()
        })
    </script>
@endsection