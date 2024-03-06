@extends('layouts.app')

@section('content')
    <style>
        .page-breadcrumb {
            display: inline-block;
            float: left;
            padding: 8px;
            margin: 0;
            list-style: none;
        }

        .page-breadcrumb>li {
            display: inline-block;
        }

        .page-breadcrumb>li>a,
        .page-breadcrumb>li>span {
            color: #666;
            font-size: 14px;
            text-shadow: none;
        }

        .page-breadcrumb>li>i {
            color: #999;
            font-size: 14px;
            text-shadow: none;
        }

        .page-breadcrumb>li>i[class^="icon-"],
        .page-breadcrumb>li>i[class*="icon-"] {
            color: gray;
        }

        @media(max-width: 576px) {
            .coundown {
                font-size: 12px !important;
                margin-top: -10px !important;
            }

            .header_coundown {
                padding: 2px !important
            }
        }

    </style>

    <div class="container">


        <div class="row">

            @include('side_bar')

            <div class="col-md-9">
                <div class="panel panel-default pt-3">
                    <div class="panel_box w-100 bg-white rounded shadow-sm">
                        <div class=" header header_coundown text-center py-3">
                            <h2 class="h4 brand_color">
                                {{ $isZoomLink ? 'Zoom Link' : 'Video Password : ' . $link->password }} </h2>
                            {{-- <div class="coundown" style="font-size: 20px; color:red; margin-top: -10px">
                                Lecture will be expired : <span id="end_coundown"></span>
                            </div> --}}
                        </div>
                    </div>

                    <div class="panel-body">
                        @if (\App\BatchesSchedules::back_url())
                            @php $back_url = explode("schedule/new-schedule/",\App\BatchesSchedules::back_url())[1] ?? ''; @endphp
                            <div>
                                <a href="{{ \App\BatchesSchedules::back_url() }}" id="back_to_schedule"
                                    class="btn btn-success back-to-schedule" data-schedule-id="{{ $back_url }}"
                                    data-batch-system-driven="{{ $doctor_course->batch->system_driven ?? '' }}"
                                    data-doctor-system-driven="{{ $doctor_course->system_driven ?? '' }}"
                                    data-feedback="not_completed">Back to schedule</a>
                            </div>
                        @endif

                        {{-- @if (request()->has('back')) --}}
                        {{-- <div> --}}
                        {{-- <a href="{{ url( request()->query('back') )  }}" class="btn-success btn btn-sm">Back To Schedule</a> --}}
                        {{-- </div> --}}
                        {{-- @endif --}}

                        @if (session('status'))
                            <div class="alert alert-success">
                                {{ session('status') }}
                            </div>
                        @endif


                        @if ($isZoomLink)
                            <div class="col-md-12">

                                <div class="portlet">
                                    <div class="portlet-body">

                                        <div class="d-flex flex-column align-items-center justify-content-center"
                                            style="background-color: #fafcff;margin-top: 20px; border: dashed 2px #2d8cff; height: 65vh">

                                            <div><img style="height: 30px" src="{{ asset('images/ZoomLogo.png') }}"></div>

                                            <div style="margin-top: 20px">
                                                <a href="{{ $link->lecture_address }}" style="background-color: #0e72ed"
                                                    class="btn btn-success">Click to Open Class</a>
                                            </div>

                                            @if (!empty($link->password))
                                                <div style="margin-top: 15px">
                                                    <p>Password:
                                                        <span
                                                            class="text-green font-weight-bold">{{ $link->password }}</span>
                                                        <input type="text" style="display: none" readonly id="password-text"
                                                            value="{{ $link->password }}" />
                                                        <button id="password-copy" class="btn btn-sm btn-info">Copy</button>
                                                    </p>
                                                </div>
                                            @endif

                                        </div>

                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="col-md-12"
                                style="margin-bottom: 10px; margin-top: 20px; text-align: center; width: 100%">
                                <p id="message">Lecture video is loading...</p>
                            </div>
                            <div class="col-md-12">
                                <div class="portlet">
                                    <div class="portlet-body">

                                        <div class="row mx-0">
                                            <div class="col-md-12">
                                                <div class="page-breadcrumb">

                                                    {{-- <h5><b>Video :</b> {{ (isset($link->name))?$link->name:'' }}</h5> --}}

                                                </div>
                                            </div>
                                        </div>

                                        <div class="row mx-0">

                                            <div class="col-md-12">
                                                <div class="col-md-10">
                                                    @if ($browser == 'UCBrowser')
                                                        <p>Sorry this video does not support UC browser. Please use another
                                                            browser.</p>
                                                    @else
                                                        {{-- <button class="btn px-2 py-1 btn-primary venobox" --}}
                                                        {{-- @if ($lecture_video->password) --}}
                                                        {{-- title="{{ '<h6 class="text-warning"> Password: '. $lecture_video->password .'</h6>'  }}" --}}
                                                        {{-- @endif --}}
                                                        {{-- data-autoplay="true" data-gall="gallery01" data-vbtype="video" --}}
                                                        {{-- href="{{ $lecture_video->lecture_address }}"> --}}
                                                        {{-- Play --}}
                                                        {{-- </button> --}}

                                                        <iframe id="MainVideo" width='100%' height='400'
                                                            src='{{ $link->lecture_address }}' frameborder='0'
                                                            allow='accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture'
                                                            allowfullscreen></iframe>
                                                    @endif
                                                    <div style="font-size: 20px; color:red">
                                                        <span id="end_coundown"></span>
                                                    </div>
                                                    {{-- @if ($doctor_class_view)
                                                        @if ($doctor_class_view->end)
                                                            <script>
                                                                var countDownDate = new Date("{{ $doctor_class_view->end }}").getTime();

                                                                var x = setInterval(function() {
                                                                    var now = new Date().getTime();
                                                                    var distance = countDownDate - now;

                                                                    var days = Math.floor(distance / (1000 * 60 * 60 * 24));
                                                                    var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                                                                    var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                                                                    var seconds = Math.floor((distance % (1000 * 60)) / 1000);

                                                                    document.getElementById("end_coundown").innerHTML = days + "d " + hours + "h " + minutes + "m " +
                                                                        seconds + "s ";

                                                                    if (distance < 0) {
                                                                        clearInterval(x);
                                                                        window.location.reload();
                                                                    }
                                                                }, 1000);
                                                            </script>
                                                        @endif
                                                    @endif --}}
                                                </div>
                                                <div class="col-md-10">
                                                    <iframe width='100%' height='500'
                                                        src="{{ url('pdf/' . $link->pdf_file) }}"></iframe>
                                                    @php
                                                        $str = $link->lecture_address;
                                                        $explode = explode('/', rtrim($str, '/'));
                                                    @endphp
                                                    <iframe src="https://vimeo.com/event/{{ end($explode) }}/chat/"
                                                        width="100%" height="100%" frameborder="0"></iframe>
                                                </div>
                                            </div>

                                        </div>

                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Modal -->
            <div class="modal fade" id="system_driven" tabindex="-1" role="dialog"
                aria-labelledby="system_driven_header" aria-hidden="false">
                <div class="modal-dialog system_driven_dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="system_driven_header">System Driven</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="false"></span>
                            </button>
                        </div>
                        <div class="modal-body system_driven_body">
                            Please click "Yes" if you finish this class?

                            If you click "Yes" then You will not be able to watch this class again.

                            To watch the next class, you need to complete the video of this class.
                        </div>
                        <div class="modal-footer">
                            <label class="radio-inline"><input type="radio" name="completed" value="completed"> Yes
                            </label>
                            <label class="radio-inline"><input type="radio" name="completed" value="not_completed"> No
                            </label>
                            <button type="button" class="btn btn-xs btn-secondary closed" data-dismiss="modal"
                                disabled>Next</button>
                        </div>
                    </div>
                </div>
            </div>

        </div>


    </div>
@endsection
@section('js')
    <script type="text/javascript">
        window.onload = function() {
            if (typeof history.pushState === "function") {
                history.pushState("jibberish", null, null);
                window.onpopstate = function() {
                    history.pushState('newjibberish', null, null);
                    window.location.replace($(".back-to-schedule").attr('href'));
                };
            } else {
                var ignoreHashChange = true;
                window.onhashchange = function() {
                    if (!ignoreHashChange) {
                        ignoreHashChange = true;
                        window.location.hash = Math.random();
                        // Detect and redirect change here
                        // Works in older FF and IE9
                        // * it does mess with your hash symbol (anchor?) pound sign
                        // delimiter on the end of the URL
                    } else {
                        ignoreHashChange = false;
                    }
                };
            }
        };
        // DO NOT REMOVE : GLOBAL FUNCTIONS!
        $(document).ready(function() {


            var doctor_course_id = $(location).attr('href').split("/doctor-course-class/")[1].split('/')[1];
            var schedule_id = $('.back-to-schedule').data('schedule-id');
            var type = "Class";
            var class_or_exam_id = $(location).attr('href').split("/doctor-course-class/")[1].split('/')[0];

            var batch_system_driven = $('.back-to-schedule').data('batch-system-driven');
            var doctor_system_driven = $('.back-to-schedule').data('doctor-system-driven');
            if (batch_system_driven == "Mandatory" || (batch_system_driven == "Optional" && doctor_system_driven ==
                    "Yes")) {

                $.ajax({

                    type: "POST",
                    url: '/add-doctor-course-schedule-details',
                    dataType: 'HTML',
                    data: {
                        doctor_course_id: doctor_course_id,
                        schedule_id: schedule_id,
                        type: type,
                        class_or_exam_id: class_or_exam_id,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(data) {
                        var data = JSON.parse(data);
                        $('.back-to-schedule').attr('data-batch-system-driven', data[
                            'batch_system_driven']);
                        $('.back-to-schedule').attr('data-doctor-system-driven', data[
                            'doctor_system_driven']);
                        console.log(data);
                    }
                });
            }

            $("body").on("click", ".closed", function() {
                $('#system_driven').modal('hide');
                window.location.replace($(".back-to-schedule").attr('href'));
            });

            $("input[name='completed']").change(function() {

                $('.back-to-schedule').data('feedback', $(this).val());
                $(".closed").prop("disabled", false);
                set_doctor_system_driven();

            });

        });
    </script>

    <script>
        function set_doctor_system_driven() {
            var doctor_course_id = $(location).attr('href').split("/doctor-course-class/")[1].split('/')[1];
            var schedule_id = $('.back-to-schedule').data('schedule-id');
            var type = "Class";
            var class_or_exam_id = $(location).attr('href').split("/doctor-course-class/")[1].split('/')[0];
            var feedback = $('.back-to-schedule').data('feedback');

            $.ajax({
                type: "POST",
                url: '/set-doctor-system-driven-feedback',
                dataType: 'HTML',
                data: {
                    doctor_course_id: doctor_course_id,
                    schedule_id: schedule_id,
                    type: type,
                    class_or_exam_id: class_or_exam_id,
                    feedback: feedback,
                    _token: '{{ csrf_token() }}'
                },
                success: function(data) {
                    var data = JSON.parse(data);
                    console.log(data);
                }
            });
        }

        $("body").on("click", ".back-to-schedule", function(e) {

            var batch_system_driven = $('.back-to-schedule').data('batch-system-driven');
            var doctor_system_driven = $('.back-to-schedule').data('doctor-system-driven');
            if (batch_system_driven == "Mandatory" || (batch_system_driven == "Optional" && doctor_system_driven ==
                    "Yes")) {
                var feedback = $('.back-to-schedule').data('feedback');
                if (feedback == "not_completed") $("input[type='radio'][value='not_completed']").attr("checked",
                    true).trigger("change");
                $('#system_driven').modal('show');
                e.preventDefault();
            }

        });


        $('#MainVideo').on('load', function() {
            $("#message").hide();
        });

        if ($('.venobox').length > 0) {
            $('.venobox').venobox({
                cb_post_open: function(obj, gallIndex, thenext, theprev) {

                    $('.venoframe').on('load', function() {
                        console.log('PLAYER:QQ ', $('#player form'));
                    });

                    console.log('POST OPEN');
                    console.log('current gallery index: ' + gallIndex);
                    console.log(thenext);
                    console.log(theprev);
                },

            });
        }

        if (document.getElementById('password-copy')) {

            document.getElementById('password-copy')
                .addEventListener('click', function() {

                    const passText = document.getElementById('password-text');

                    passText.select();
                    passText.setSelectionRange(0, 99999); /* For mobile devices */

                    navigator.clipboard.writeText(passText.value);

                    document.getElementById('password-copy').innerHTML = 'Copied';

                    setTimeout(function() {
                        document.getElementById('password-copy').innerHTML = 'Copy';
                    }, 3000)

                });

        }
    </script>
@endsection
