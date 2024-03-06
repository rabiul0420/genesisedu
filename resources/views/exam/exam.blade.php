@extends('layouts.app')

@section('content')



    <div class="container">



        <div class="row">

            <div class="col-md-9 col-md-offset-0 px-0">
                <div class="panel panel-default">
                    <div class="panel-heading" style="background-color:#7fc9f6; color: #FFFFFF;">
                        <h3>{{ $exam->name }}</h3>
                    </div>

                    <div class="panel-body">

                        @if( Session::has('message') )
                            <div class="alert {{ ( Session::get('class') ) ? Session::get('class') : 'alert-success' }}" role="alert">
                                <p> {!! Session::get('message') !!} </p>
                            </div>
                        @endif

                        <div class="col-md-12">
                            <div class="portlet">
                                <div class="portlet-header">
                                    <span style="padding:9px;border-radius:10px;font-size:17px;font-weight:700;float:right;background-color: #915faa;color: white; margin: 0 5px;">
                                        Skip: <span id="totalSkip">{{ $totalSkip ?? '' }}</span>
                                    </span>
                                </div>
                                <div class="portlet-header">
                                    <span style="padding:9px;border-radius:10px;font-size:17px;font-weight:700;float:right;background-color: #915faa;color: white;">
                                        Time : <span id="timer"></span>
                                    </span>
                                    <div hidden id="duration">{{ $duration }}</div>
                                </div>
                                <div class="portlet-body">
                                    {{--@foreach($exam->exam_questions as $exam_question)--}}
                                    <div id="question">
                                        @if(isset($exam_question['question_title']))


                                            <input type="hidden" name="exam_question_id" value="{{ $exam_question['exam_question_id'] }}">
                                            <input type="hidden" name="exam_id" value="{{ $exam->id }}">
                                            <input type="hidden" name="exam_question_type" value="{{ $exam_question['question_type'] }}">

                                            <div>
                    {{--<h4 class='modal-title' id='myModalLabel'>{!! '('.($serial_no).' of '.$exam->exam_questions->count().' ) '.$exam_question['question_title'] !!}</h4>--}}
                                                <h4 class='modal-title' id='myModalLabel'>{!! '('.($serial_no).' of ' . $total_questions . ' ) '.$exam_question['question_title'] !!}</h4>
                                            </div>

                                            <table class="table table-borderless" style="table-layout: auto;">
                                                @if($exam_question['question_type'] == "1" || $exam_question['question_type'] == "3")
                                                    @foreach($exam_question['question_option'] as $k=>$answer)
                                                        @if($k < session('stamp'))
                                                            <tr>
                                                                <td>
                                                                    {!! isset( $answer[ 'option_title' ] )? $answer[ 'option_title' ] : '' !!}
                                                                </td>

                                                                <td style="width: 99px;">
                                                                    <label class='radio-inline'><input type='radio' name="{{ $options[$k] ?? $k  }}" value='T'  > T </label>
                                                                    <label class='radio-inline'><input type='radio' name="{{ $options[$k] ?? $k  }}" value='F'  > F </label>
                                                                </td>

                                                            </tr>
                                                        @endif
                                                    @endforeach
                                                @elseif( $exam_question['question_type'] == "2" || $exam_question['question_type'] == "4")
                                                    @foreach($exam_question['question_option'] as $k=>$answer)
                                                        @if($k < session('stamp'))
                                                            <tr>
                                                                <td>
                                                                    {!! isset($answer['option_title'])? $answer['option_title'] :'' !!}
                                                                </td>
                                                            </tr>
                                                        @endif
                                                    @endforeach
                                                    <tr>
                                                        <td>
                                                            <label class='radio-inline'><input type='radio' name='ans_sba' value='A' > A </label>
                                                            <label class='radio-inline'><input type='radio' name='ans_sba' value='B' > B </label>
                                                            <label class='radio-inline'><input type='radio' name='ans_sba' value='C' > C </label>
                                                            <label class='radio-inline'><input type='radio' name='ans_sba' value='D' > D </label>
                                                            @if(session('stamp')==5)
                                                                <label class='radio-inline'><input type='radio' name='ans_sba' value='E' > E </label>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endif
                                            </table>

                                            <div style="float:right;">
                                                <button id="id_button_skip" class='btn btn-warning' onclick='skip_question()' {{ ($exam_finish=='Finished') ? 'disabled' : '' }}>Skip</button>
                                                <button id="id_button_submit" class='btn btn-success' onclick='submit_answer()' {{ ($exam_finish=='Finished') ? 'disabled' : '' }}>{{ $exam_finish }}</button>
                                            </div>
                                        @endif
                                    </div>
                                    {{--@endforeach--}}
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>


    </div>
@endsection

@section('js')

    <script src="{{ asset("js/timer/alotimer.min.js") }}"></script>

    <script type="text/javascript">

       
        var doctor_course_id = '{{ Request::segment(2) }}';
        const schedule_id = '{{ $schedule_id ?? '' }}';

        window.addEventListener('load',exam_time);

        function disableBtns() {
            $("#id_button_skip").attr( 'disabled', true );
            $("#id_button_submit").attr( 'disabled', true );
        }

        function exam_time() {

            var duration = document.getElementById("duration").innerText;
            var span       = document.getElementById("timer"),
                timer      = new AloTimer(duration*1000+3000, ["hours", "minutes", "seconds"]), // 1 hr
                intervalCb = function () {
                    if (!timer.hasFinished) {
                        span.innerText = timer.toString();
                    } else {
                        span.innerText = "Exam time is over!!!";
                        clearInterval(interval);
                        submit_answer_and_terminate_exam();
                        document.getElementById("id_button_submit").setAttribute('disabled','disabled');
                        document.getElementById("id_button_submit").innerText = "Finished";
                    }
                },
                interval   = setInterval(intervalCb, 1000);

        }

        function submit_answer()
        {
            var exam_id = $("[name='exam_id']").val();
            var exam_question_id = $("[name='exam_question_id']").val();
            var exam_question_type = $("[name='exam_question_type']").val();

            if(exam_question_type == 1 || exam_question_type == 3)
            {
                var ans_a = $("[name='A']:checked").val();
                var ans_b = $("[name='B']:checked").val();
                var ans_c = $("[name='C']:checked").val();
                var ans_d = $("[name='D']:checked").val();
                var ans_e = $("[name='E']:checked").val();

                console.log(exam_id + "_" + exam_question_id + " = " +ans_a+" "+ans_b+" "+ans_c+" "+ans_d+" "+ans_e);

                disableBtns();

                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: '/submit-answer',
                    dataType: 'HTML',
                    data: {
                        doctor_course_id:doctor_course_id,
                        exam_id:exam_id,
                        exam_question_id : exam_question_id,
                        ans_a : ans_a,
                        ans_b : ans_b,
                        ans_c : ans_c,
                        ans_d : ans_d,
                        ans_e : ans_e,
                        schedule_id
                    },
                    success: function( data ) {
                        var data = JSON.parse(data);
                        $('#question').html('');
                        $('#question').html(data['question']);
                        $('#totalSkip').html(data['totalSkip']);
                        if(data['redirect'])
                        {
                            var loc = window.location;
                            window.location = loc.protocol+"//"+loc.hostname+":"+loc.port+data['redirect'];
                        }
                    }
                });

            }
            else if(exam_question_type == 2 || exam_question_type == 4)
            {
                var ans_sba = $("[name='ans_sba']:checked").val();

                console.log(exam_id + "_" + exam_question_id + " = " +ans_sba);

                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: '/submit-answer',
                    dataType: 'HTML',
                    data: {
                        doctor_course_id:doctor_course_id,
                        exam_id:exam_id,
                        exam_question_id : exam_question_id ,
                        ans_sba : ans_sba,
                        schedule_id
                    },
                    success: function( data ) {
                        var data = JSON.parse(data);
                        $('#question').html('');
                        $('#question').html(data['question']);
                        $('#totalSkip').html(data['totalSkip']);
                        if(data['redirect'])
                        {
                            var loc = window.location;
                            window.location = loc.protocol+"//"+loc.hostname+":"+loc.port+data['redirect'];

                        }

                    }
                });

            }

        }

        function submit_answer_and_terminate_exam() {

            disableBtns();

            var exam_id = $("[name='exam_id']").val();
            var exam_question_id = $("[name='exam_question_id']").val();
            var exam_question_type = $("[name='exam_question_type']").val();

            if ( exam_question_type == 1 || exam_question_type == 2 ) {
                var ans_a = $("[name='A']:checked").val();
                var ans_b = $("[name='B']:checked").val();
                var ans_c = $("[name='C']:checked").val();
                var ans_d = $("[name='D']:checked").val();
                var ans_e = $("[name='E']:checked").val();

                console.log(exam_id + "_" + exam_question_id + " = " + ans_a + " " + ans_b + " " + ans_c + " " + ans_d + " " + ans_e);

                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: '/submit-answer-and-terminate-exam',
                    dataType: 'HTML',
                    data: {
                        doctor_course_id: doctor_course_id,
                        exam_id: exam_id,
                        exam_question_id: exam_question_id,
                        ans_a: ans_a,
                        ans_b: ans_b,
                        ans_c: ans_c,
                        ans_d: ans_d,
                        ans_e: ans_e,
                        schedule_id
                    },
                    success: function (data) {
                        var data = JSON.parse(data);
                        $('#question').html('');
                        $('#question').html(data['question']);
                        if (data['redirect']) {
                            var loc = window.location;
                            window.location = loc.protocol + "//" + loc.hostname + ":" + loc.port + data['redirect'];
                        }
                    }
                });

            } else if (exam_question_type == 2 || exam_question_type == 4) {
                var ans_sba = $("[name='ans_sba']:checked").val();

                console.log(exam_id + "_" + exam_question_id + " = " + ans_sba);

                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: '/submit-answer-and-terminate-exam',
                    dataType: 'HTML',
                    data: {
                        doctor_course_id: doctor_course_id,
                        exam_id: exam_id,
                        exam_question_id: exam_question_id,
                        ans_sba: ans_sba,
                        schedule_id
                    },
                    success: function (data) {
                        var data = JSON.parse(data);
                        $('#question').html('');
                        $('#question').html(data['question']);
                        if (data['redirect']) {
                            var loc = window.location;
                            window.location = loc.protocol + "//" + loc.hostname + ":" + loc.port + data['redirect'];

                        }

                    }
                });

            }

        }

        function skip_question() {

            disableBtns();

            var exam_id = $("[name='exam_id']").val();
            var exam_question_id = $("[name='exam_question_id']").val();
            var exam_question_type = $("[name='exam_question_type']").val();

            if (exam_question_type == 1 || exam_question_type == 3) {
                var ans_a = $("[name='A']:checked").val();
                var ans_b = $("[name='B']:checked").val();
                var ans_c = $("[name='C']:checked").val();
                var ans_d = $("[name='D']:checked").val();
                var ans_e = $("[name='E']:checked").val();

                console.log(exam_id + "_" + exam_question_id + " = " + ans_a + " " + ans_b + " " + ans_c + " " + ans_d + " " + ans_e);

                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: '/skip-question',
                    dataType: 'HTML',
                    data: {
                        doctor_course_id: doctor_course_id,
                        exam_id: exam_id,
                        exam_question_id: exam_question_id,
                        ans_a: ans_a,
                        ans_b: ans_b,
                        ans_c: ans_c,
                        ans_d: ans_d,
                        ans_e: ans_e,
                        schedule_id
                    },
                    success: function (data) {
                        var data = JSON.parse(data);
                        $('#question').html('');
                        $('#question').html(data['question']);
                        $('#totalSkip').html(data['totalSkip']);
                        if (data['redirect']) {
                            var loc = window.location;
                            window.location = loc.protocol + "//" + loc.hostname + ":" + loc.port + data['redirect'];
                        }
                    }
                });

            } else if (exam_question_type == 2 || exam_question_type == 4) {
                var ans_sba = $("[name='ans_sba']:checked").val();

                console.log(exam_id + "_" + exam_question_id + " = " + ans_sba);

                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: '/skip-question',
                    dataType: 'HTML',
                    data: {
                        doctor_course_id: doctor_course_id,
                        exam_id: exam_id,
                        exam_question_id: exam_question_id,
                        ans_sba: ans_sba
                    },
                    success: function (data) {
                        var data = JSON.parse(data);
                        $('#question').html('');
                        $('#question').html(data['question']);
                        $('#totalSkip').html(data['totalSkip']);
                        if (data['redirect']) {
                            var loc = window.location;
                            window.location = loc.protocol + "//" + loc.hostname + ":" + loc.port + data['redirect'];

                        }

                    }
                });

            }

        }

    </script>


@endsection
