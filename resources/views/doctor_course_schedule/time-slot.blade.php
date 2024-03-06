<div id="{{ $time_slot->id }}_time_slot_container">

    @foreach ($time_slot->schedule_details as $detail)
        @if ($detail instanceof \App\ScheduleDetail)
            @php
                
                $feedback_or_solve_class = $detail->lectures[0] ?? new \App\ScheduleDetail();
                
                $feedback_or_solve_class_disabled = $detail->feedback_or_solve_class_disabled();
                $link_disabled = $detail->is_link_disabled();
                $rating_disabled = $detail->feedback_disabled();
                $filter_text = $_GET['text'] ?? '';
            @endphp

            <div class="row" style="margin-bottom: 15px">

                <div style="display: flex; flex-direction: column; margin-bottom: 5px;">
                    <div>
                        <div class="badge bg-info" style="font-weight: bold">{{ $detail->type }}</div>
                        <div class="text-info">

                            {!! highlight_filter_text($detail->type == 'Class' ? $detail->video->name ?? '' : ($detail->type == 'Exam' ? $detail->exam->name ?? '' : ''), $filter_text) !!}


                            <button class="class-details"
                                onclick="alert(`{{ $detail->type == 'Class' ? $detail->video->description ?? '' : $detail->exam->description ?? '' }}`)">
                                @include('batch_schedule.info-icon')
                            </button>


                        </div>
                    </div>
                    <div>
                        <div class="badge bg-success" style="font-weight: bold; margin-top: 10px">Mentor</div>
                        <div class="text-success">
                            <div>{!! highlight_filter_text($detail->mentor->name ?? '', $filter_text) !!}</div>
                            <div style="font-size: 12px;color: #34d95a">{{ $detail->mentor->designation ?? '' }}</div>
                        </div>
                    </div>
                </div>

                @if ($feedback_or_solve_class->id)
                    <div style="display: flex; flex-direction: column;margin-bottom: 5px; margin-top: 18px">
                        <div>
                            <div class="badge bg-info" style="font-weight: bold">
                                {{ $detail->type == 'Class' ? 'Feedback ' : 'Solve ' }} Class</div>
                            <div class="text-info">
                                {!! highlight_filter_text($feedback_or_solve_class->video->name ?? '', $filter_text) !!}

                                {{-- <pre>{{ json_encode($feedback_or_solve_class->video) }}{{$feedback_or_solve_class->type  }}</pre> --}}

                                <button class="class-details"
                                    onclick=" alert(`{{ $feedback_or_solve_class->video->description ?? '' }}`) ">
                                    @include('batch_schedule.info-icon')
                                </button>
                            </div>
                        </div>
                        <div>
                            <div class="badge bg-success" style="font-weight: bold; margin-top: 10px">Mentor</div>
                            <div class="text-success">
                                <div>{!! highlight_filter_text($feedback_or_solve_class->mentor->name ?? '', $filter_text) !!}</div>
                                <div style="font-size: 12px;color: #34d95a">
                                    {{ $feedback_or_solve_class->mentor->designation ?? '' }}</div>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="btn-flex" style="margin-bottom: 15px; padding-left: 5px;padding-right: 5px;">

                    <div class="mt-1 px-2 py-1">

                        @if ($detail->type == 'Class')
                            @php $class = $detail->classLink( ); @endphp
                            <a href="{{ $class['url'] }}"
                                class=" btn btn-sm  px-4 py-2 box-1 {{ $class['disabled'] ? 'disabled' : '' }}">Enter
                                Class</a>

                            {{-- @if ($detail->doctor_class_view ? $detail->doctor_class_view->status == 1 : true)
                                @php $class = $detail->classLink( ); @endphp
                                <a href="{{ $class['url'] }}"
                                    class=" btn btn-sm  px-4 py-2 box-1 {{ $class['disabled'] ? 'disabled' : '' }}">Enter
                                    Class</a>
                            @else
                                <a class=" btn btn-sm  px-4 py-2 box-1 disabled">Enter Class</a>
                            @endif --}}
                        @elseif($detail->type == 'Exam')
                            @php $exam = $detail->examLink(); @endphp
                            <div class="position-relative">
                            <a href="{{ $exam['url'] }}"
                                @if (!$exam['completed']) onclick="return confirm( 'STEPS FOR EXAM : \n 1. Start now বক্স এ click করার পরপরই আপনার পরীক্ষা শুরু হয়ে যাবে। \n 2. প্রশ্ন MCQ type হলে প্রতিটি অপশন এর বিপরীতে T এবং F বৃত্ত রয়েছে। আপনি True যুক্তিযুক্ত মনে করলে T বৃত্ত অথবা False যুক্তিযুক্ত মনে করলে F বৃত্ত click করবেন। T বৃত্ত click করার পর পরিবর্তন করতে চাইলে F বৃত্ত click করার সুযোগ রয়েছে। (Vice versa). \n 3. প্রশ্ন SBA Type হলে যেকোনো একটি বৃত্ত click করুন। বৃত্ত পরিবর্তন করার সুযোগ রয়েছে। \n 4. পরবর্তী প্রশ্ন পেতে চাইলে Next বক্স এ click করুন। কোন প্রশ্ন এই মুহূর্তে answer না করে পরবর্তীতে আবার প্রশ্ন টি পেতে চাইলে TRY LATER বক্স এ click করুন। সিরিয়ালের শেষ প্রশ্নটি answer করার পর TRY LATER করে আসা প্রশ্ন গুলো answer করার সুযোগ পাবেন। \n উদাহরণস্বরূপ: 50 টি প্রশ্নের পরীক্ষায় আপনি যদি 7,10,15,25,32 নং প্রশ্ন TRY LATER করে থাকেন তাহলে সর্বশেষ 50 নং প্রশ্নটি answer করার পর পুনরায় 7,10,15,25,32 নং প্রশ্নগুলো answer করার সুযোগ পাবেন। \n 5. কোন প্রশ্ন এখন বা পরবর্তীতেও answer করার জন্য পেতে না চাইলে Next বক্স এ click করুন। এক্ষেত্রে প্রশ্নটি কাউন্ট হয়ে যাবে। \n 6. পরীক্ষা শেষ হলে Submit বক্স এ click করুন। \n 7. সময়ের দিকে খেয়াল রাখুন, নির্ধারিত সময়ের পর পরীক্ষাটি auto-submit হয়ে যাবে। \n 8. পরীক্ষা দেওয়ার পর পুরো প্রশ্নটিই আপনার উত্তর সহ আপনার প্রোফাইলে থাকবে। আপনি চাইলে পরবর্তীতে যেকোনো সময় এটি Answer details এ click করলে দেখতে পারবেন। \n 9. ব্যবহৃত ডিভাইসে (ল্যাপটপ, ট্যাবলেট, স্মার্ট ফোন) এ যথেষ্ট চার্জ করে রাখবেন যাতে পরীক্ষার পুরো সময়টিতে কোনো ব্যাঘাত না ঘটে। পরীক্ষা চলাকালীন সময়ে ব্যবহৃত ডিভাইসে কোন কল আদান-প্রদানে বিরত থাকুন। ইন্টারনেট কানেকশন নিশ্চিত রাখুন। একই সাথে একাধিক ডিভাইসে লগইন করা থেকে বিরত থাকুন।' )" @endif
                                class="btn btn-sm {{ $exam['completed'] ? 'bg-success' : '' }} {{ $exam['disabled'] ? 'disabled' : '' }}  px-4 py-2 box-1">
                                {{ $exam['label'] }}
                            </a>
                            @if($previous_exam_mendatory && !$exam['completed'] && $exam['url'] != $only_available_exam)
                            <div style="position: absolute; top: 0; left: 0; inset: 0; background: #333a; display: flex; flex-direction: column; justify-content: center; align-items: center; gap: 2px; border-radius: 5px; overflow: auto;">
                                <i class="fa fa-lock" style="font-size: 18px; color: #fff;"></i>
                                <span style="font-size: 11px; color: #fff; font-weight: 600;">Finish previous Exam</span>
                            </div>
                            @endif
                            </div>
                        @endif

                    </div>


                    @if ($detail->type == 'Class')
                        @if ($feedback_or_solve_class->id)
                            <div class="mt-1 px-2 py-1">

                                @php $class = $feedback_or_solve_class->classLink( ); @endphp

                                <a href="{{ $class['url'] }}"
                                    class=" btn btn-sm  px-4 py-2 box-2 {{ $class['disabled'] || $feedback_or_solve_class_disabled ? 'disabled' : '' }}">
                                    Feedback Class
                                </a>

                            </div>
                        @endif

                        <div class="mt-1 px-2 py-1">
                            @include('batch_schedule.add-question-btn', [
                                'schedule_id' => $schedule_id,
                                'doctor_course_id' => $doctor_course_id,
                                'disabled' => $link_disabled || $detail->doctor_class_view == null,
                                'class_id' => $detail->class_or_exam_id,
                            ])
                        </div>

                        @if ($feedback_or_solve_class->id)
                            <div class="dropdown mt-1 px-2 py-1">
                                <button class="btn btn-outline-info dropdown-toggle py-2  btn-sm py-2 box-2 "
                                    type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown"
                                    aria-expanded="false">
                                    Rate The Class
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                    <li><a class="dropdown-item rate-feedback {{ $rating_disabled ? 'disabled' : '' }}"
                                            data-detail-id="{{ $detail->id }}" data-course-id="{{ $course_id }}"
                                            data-slot-id="{{ $time_slot->id }}" href="#">Rate Main Class</a>
                                    </li>

                                    <li><a class="dropdown-item rate-feedback {{ $feedback_or_solve_class->doctor_class_view == null ? 'disabled' : '' }}"
                                            data-detail-id="{{ $feedback_or_solve_class->id ?? 0 }}"
                                            data-course-id="{{ $course_id }}" data-slot-id="{{ $time_slot->id }}"
                                            href="#">Rate Feedback Class</a>
                                    </li>
                                </ul>
                            </div>
                        @else
                            <div class="mt-1 px-2 py-1">
                                <button class="btn btn-sm py-2 box-2 rate-feedback" data-action="submit_rating"
                                    {{ $rating_disabled ? 'disabled' : '' }} data-detail-id="{{ $detail->id }}"
                                    data-course-id="{{ $course_id }}" data-slot-id="{{ $time_slot->id }}">
                                    Rate the Class
                                </button>
                            </div>
                        @endif
                    @else
                        @php $exam = $detail->examLink( ); @endphp

                        <div class="mt-1 px-2 py-1">
                            @php $class = $feedback_or_solve_class->classLink( ); @endphp

                            <a href="{{ $class['url'] }}"
                                class="btn btn-sm  px-4 py-2 box-2 {{ !$exam['completed'] ? 'disabled' : '' }}">Solve
                                Class
                            </a>
                        </div>

                        <div class="mt-1 px-2 py-1">
                            @include('batch_schedule.add-question-btn', [
                                'doctor_course_id' => $doctor_course_id,
                                'schedule_id' => $schedule_id,
                                'disabled' =>
                                    $link_disabled ||
                                    !$exam['completed'] ||
                                    $feedback_or_solve_class->id == null ||
                                    $feedback_or_solve_class->doctor_class_view == null,
                                'class_id' => $feedback_or_solve_class->class_or_exam_id,
                            ])
                        </div>

                        <div class="mt-1 px-2 py-1">
                            <button class="btn btn-sm py-2 box-2 rate-feedback" data-action="submit_rating"
                                {{ $rating_disabled || $feedback_or_solve_class->id == null ? 'disabled' : '' }}
                                data-detail-id="{{ $feedback_or_solve_class->id }}"
                                data-course-id="{{ $course_id }}" data-slot-id="{{ $time_slot->id }}">
                                Rate the Solve Class
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    @endforeach

</div>
