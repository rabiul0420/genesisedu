@extends('layouts.app')
@section('content')

    <style>
        .class-or-exam-contents .btn {
            width: 100%;
        }

    </style>
    <div class="container card-top">
        <div class="card mt-5 mb-5">

            <div class="navbar">
                <div class="container-fluid">
                    <div>
                        <p class="navbar-brand pt-4">
                            <span>Full Schedule</span>
                            <button id="print" data-schedule-id="{{ $schedule_id }}"
                                data-query-string="{{ request()->getQueryString() }}">
                                <img src="{{ asset('images/print-icon.jpeg') }}" style="width: 35px">
                            </button>
                            {{-- <button type="button"  style="background-color:#ff9900; color:#404040;" class="btn  btn-sm"
                                data-toggle="modal" data-target="#exampleModal">Enroll Now</button> --}}
                                <a type="button" {{ Auth::guard('doctor')->check() ?  'href=/enroll-now/'.$schedule_id  : 'data-toggle=modal' }} style="background-color:#ff9900; color:#404040;" class="btn  btn-sm"
                                    data-target="#exampleModal">Enroll Now</a>
                        </p>
                        
                    </div>

                    <form class="top-form" id="search-form">

                        <input class="form-control " name="date" style="margin-right: 10px"
                            onchange="document.getElementById('search-form').submit()" placeholder="Date"
                            value="{{ $_GET['date'] ?? '' }}" type="search" aria-label="Search" id="schedule-date">

                        <input class="form-control " name="text" style="margin-right: 10px" placeholder="Class/Exam/Mentor"
                            value="{{ $_GET['text'] ?? '' }}" type="search" aria-label="Search">

                        <button class="btn btn-top" type="submit">Search</button>
                    </form>

                </div>
            </div>

            <hr>


            @foreach ($scheduleTimeSlots as $time_slot)

                @if (isset($time_slot->schedule_details) && $time_slot->schedule_details->count())

                    @php $date = $time_slot->datetime->format('Y-m-d') @endphp

                    <div class="container-fluid class-or-exam-contents">
                        <p class="row" style="margin-bottom: 25px">
                            <span class="col-12 col-md-6 col-lg-3"> <span class="badge bg-secondary">Date</span>
                                <a
                                    href="{{ url('new-schedule/' . $schedule_id . '?date=' . $date) }}">{{ $time_slot->datetime->format('d-M-Y') }}</a>
                            </span> {{-- 'l, d-M-Y' --}}
                        </p>


                        @foreach ($time_slot->schedule_details as $detail)

                            @if ($detail instanceof \App\ScheduleDetail)

                                @php
                                    //$html = $detail->data( $doctor_course_id );
                                    $feedback_or_solve_class = $detail->lectures[0] ?? new \App\ScheduleDetail();
                                    $feedback_or_solve_class_disabled = $detail->feedback_or_solve_class_disabled();
                                    $link_disabled = $detail->is_link_disabled();
                                    $rating_disabled = $detail->feedback_disabled();
                                @endphp


                                <div class="row" style="margin-bottom: 15px">

                                    <div style="display: flex; flex-direction: column; margin-bottom: 5px;">
                                        <div>
                                            <div class="badge bg-info" style="font-weight: bold">{{ $detail->type }}
                                            </div>
                                            <div class="text-info">
                                                @if ($detail->type == 'Class')
                                                    <a
                                                        href="{{ url('/new-schedule/' . $schedule_id . '?date=' . $date . '&text=' . urlencode($detail->video->name ?? '') ?? '') }}">{{ $detail->video->name ?? '' }}</a>
                                                @else
                                                    <a
                                                        href="{{ url('/new-schedule/' . $schedule_id . '?date=' . $date . '&text=' . urlencode($detail->exam->name ?? '') ?? '') }}">{{ $detail->exam->name ?? '' }}</a>
                                                @endif

                                            </div>
                                        </div>
                                    </div>

                                    @if ($feedback_or_solve_class->id)
                                        <div
                                            style="display: flex; flex-direction: column;margin-bottom: 5px; margin-top: 18px">
                                            <div>
                                                <div class="badge bg-info" style="font-weight: bold">
                                                    {{ $detail->type == 'Class' ? 'Feedback ' : 'Solve ' }} Class</div>
                                                <div class="text-info">
                                                    <a
                                                        href="{{ url('/new-schedule/' . $schedule_id . '?date=' . $date . '&text=' . urlencode($feedback_or_solve_class->video->name ?? '')) }}">
                                                        {{ $feedback_or_solve_class->video->name ?? '' }}
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                </div>
                            @endif
                        @endforeach

                        <hr class="mt-4 bottom">
                    </div>

                @endif
            @endforeach

            {{-- <pre>{{ print_r( $scheduleTimeSlots->toArray() ) }}</pre> --}}

            <div class="container-fluid">
                <div class="mt-3 next pb-3">
                    @if ($scheduleTimeSlots->previousPageUrl())
                        <a href="{{ $scheduleTimeSlots->previousPageUrl() }}" class="btn next-btn">Previous Page</a>
                    @endif
                    @if ($scheduleTimeSlots->nextPageUrl())
                        <a href="{{ $scheduleTimeSlots->nextPageUrl(['a' => 8]) }}" class="btn next-btn">Next Page</a>
                    @endif
                </div>
            </div>

            <div class="modal fade" id="rate-feedback-modal" tabindex="-1" role="dialog"
                aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog  modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Ratings on lecture video</h5>
                            <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>

                        <div class="modal-body" style="background-color: #f0fdf8">
                            <form id="ratting-submit-form" method="post" action=""
                                style="min-height: 180px;position: relative">

                                <div
                                    style="position: absolute; left: 0;right: 0; top:0; bottom: 0; display: flex; justify-content: center; align-items: center">
                                    <span>Loading...</span>
                                </div>

                            </form>
                            <div style=" font-size: 12px; color: green" id="modal-message"></div>
                        </div>

                        <div class="modal-footer">
                            <a href="javascript:void(0)" class="btn btn-secondary" id="close-button"
                                data-bs-dismiss="modal">Close</a>
                            <button type="button" class="btn btn-primary" id="submit-button">Submit</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Button trigger modal -->


            <!-- Modal -->
            <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Enroll Form</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="md-form mb-5">
                                <label data-error="wrong" data-success="right" for="orangeForm-name">Your Phone
                                    Number</label>

                                    <div class="position-relative w-100 prefix">
                                        <span class="position-absolute border-bottom text-dark border-info bg-warning" style="padding:9px 20px; left: 0px;">+88</span>
                                    </div>
                                <input oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                                type = "text"
                                maxlength = "11"
                                class="mobile_number  form-control" name="mobile_number" id="mobile_number" style="padding-left:76px">
                                <span id="span" ></span>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary phone_number_submit">Submit</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
 
        @endsection


        @section('css')
            <link rel="stylesheet"
                href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" />

            <style>
                .class-details {
                    color: gray;
                    border: none;
                    background: none;
                    outline: none;
                }

                .class-details:hover {
                    color: green;
                }


                #print {
                    margin-left: 15px;
                    border: none;
                    outline: none;
                    background: none;
                    opacity: 0.8;
                }

                #print:hover {
                    opacity: 1;
                }

                #print:focus {
                    outline: none;
                }

            </style>

        @endsection

        @section('js')
            <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"
                        type="text/javascript"></script>
            <script>
                $(document).ready(function() {

                    $("[name='mobile_number']").bind("change paste keyup", function() {
                        
                        if( $(this).val().length != 11 )
                        { 
                            $('#span').html('Please input 11 digit mobile no.');
                            $('.phone_number_submit').addClass('disabled');
                        }
                        else if($(this).val().length == 11)
                        {
                            $('#span').html('Thanks');
                            $('.phone_number_submit').removeClass('disabled');
                        }                        
                    });
                    
                });
                $('#schedule-date').datepicker({
                    todayHighlight: true,
                    format: 'yyyy-mm-dd',
                    zIndexOffset: 500000000
                });

                let loading =
                    '<div style="position: absolute; left: 0;right: 0; top:0; bottom: 0; display: flex; justify-content: center; align-items: center">';
                loading += '<span>Loading...</span>';
                loading += '</div>';

                let thankyou =
                    '<div style="position: absolute; left: 0;right: 0; top:0; bottom: 0; display: flex; justify-content: center; align-items: center">';
                thankyou += '<span style="color: green; font-size: 18px">Thank you for your feedback</span>';
                thankyou += '</div>';

                let watchFullClass =
                    '<div style="position: absolute; left: 0;right: 0; top:0; bottom: 0; display: flex; justify-content: center; align-items: center">';
                watchFullClass += '<span style="color: green; font-size: 18px">Did you watch the full class?</span>';
                watchFullClass += '</div>';


                $(document).ready(function() {
                    var rate_feedback_modal = new bootstrap.Modal(document.getElementById('rate-feedback-modal'), {
                        keyboard: false
                    })

                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        }
                    });
                    let details_id, course_id = "{{ $course_id }}";

                    document.getElementById('rate-feedback-modal').addEventListener('hide.bs.modal', function() {
                        $('#submit-button').attr('disabled', false).off('click').on('click', handleFullClassWatch);
                    })

                    function handleSubmit(e) {
                        e.preventDefault();
                        let num_of_criteria = 0;
                        let num_of_defined_criteria = 0;

                        $FORM.find('.criteria-list').each(function() {
                            const criteria = $(this).data('criteria');
                            const value = $(this).find('input[type="radio"]:checked').val();
                            // console.log( criteria, value );
                            if (value) num_of_defined_criteria++;
                            num_of_criteria++;
                        })

                        if (num_of_criteria != num_of_defined_criteria) {
                            $('#modal-message').css('color', 'darkred').html('Please check all criteria');
                        } else if ($('#feedback-text').val().trim() == '') {
                            $('#feedback-text').focus();
                            $('#feedback-text').css('border-color', 'red');
                            $('#modal-message').css('color', 'darkred').html('Please write a feedback about the class!');

                        } else {
                            $('#feedback-text').css('border-color', '#ced4da');
                            $FORM.submit();
                        }
                    }

                    function handleFullClassWatch() {

                        $('#close-button').click(function() {
                            $('#submit-button').attr('disabled', false).off('click').on('click',
                                handleFullClassWatch);
                            $('#modal-message').html('');
                        });

                        $('#submit-button').attr('disabled', true);
                        $FORM.html(loading);

                        $FORM.load('/doctor-ratting-modal?details_id=' + details_id + '&course_id=' + course_id,
                    function() {
                            $('#modal-message').css('color', 'green').html('Check all criteria and hit submit');
                            $('#close-button').html('Close').attr('disabled', true);
                            $('#submit-button').html('Submit').attr('disabled', false).off('click').on('click',
                                handleSubmit);
                        });
                    }


                    $('#submit-button').on('click', handleFullClassWatch);


                    let $FORM = $('#ratting-submit-form');

                    $FORM.submit(function(e) {
                        e.preventDefault();
                        $('#submit-button').attr('disabled', true);
                        if (details_id) {
                            const formData = new FormData(this);
                            formData.append('details_id', details_id);
                            formData.append('course_id', course_id);

                            $.ajax({
                                enctype: 'multipart/form-data',
                                type: "POST",
                                url: '/submit-doctor-ratting',
                                data: formData,
                                processData: false,
                                contentType: false,
                                dataType: "json",
                                success: function(data) {

                                    $('#submit-button').attr('disabled', false).off('click').on('click',
                                        handleFullClassWatch);
                                    $FORM.html(thankyou);

                                    $('#modal-message').html('');
                                    setTimeout(() => {
                                        rate_feedback_modal.hide();
                                    }, 1000)
                                },
                                error: function() {
                                    $('#submit-button').attr('disabled', false).on('click',
                                        handleFullClassWatch);
                                }
                            });
                        }
                    });

                    $('.rate-feedback').each(function() {

                        $(this).on('click', function(e) {
                            e.preventDefault();

                            details_id = $(this).data('detail-id');
                            rate_feedback_modal.show();

                            $FORM.html(watchFullClass);
                            $('#close-button').html('No').attr('disabled', false);
                            $('#submit-button').html('Yes').attr('disabled', false);

                            // End of form

                        }); // End of onclick

                    }); // End of Each

                    const base_url = "{{ url('') }}";

                    $("body").on("click", ".phone_number_submit", function() {
                        var mobile_number = $('#mobile_number').val();
                        var schedule_id = {{ $schedule_id }};
                        window.location = base_url + '/password/' + mobile_number + '/' + schedule_id
                    })

                });


                $('#print').click(function() {
                    var schedule_id = $(this).data('schedule-id');
                    var params = '';
                    if ($(this).data('query-string')) {
                        params = '?' + $(this).data('query-string');
                    }

                    var pw = window.open("/schedule-print/" + schedule_id + '' + params, "_blank",
                        "toolbar=yes,scrollbars=yes,resizable=yes,top=50,left=50,width=1000,height=800");
                    pw.print();
                });
            </script>
            {{-- <script src="{{ asset('js/popper.min.js') }}"></script> --}}
            {{-- <script src="{{asset('js/bootstrap.min.js')}}"></script> --}}

        @endsection
