@extends('layouts.app')

@section('content')
<div class="container">

    <div class="row">

        {{--@include('side_bar')--}}

        <div class="col-md-9 col-md-offset-0">
            <div class="panel panel-default">


 
                            <div style="background-color:#7fc9f6; color: #FFFFFF; margin-bottom: 30px; padding: 10px 16px">
                                <div class="row">
                                    <div class="col-md-12" style="margin-top: 0 !important; align-items: center">
                                        <div class="row">
                                            <h3 class="col-md-5" style="">
                                                {{ isset($course_exam_result->doctor_course->course->name) ? $course_exam_result->doctor_course->course->name : '' }}
                                                Exam Result
                                            </h3>

                                            <div class="col-md-7">
                                                @if( isset( $solve_class_id ) && isset($schedule_id) && $schedule_id )
                                                    <a href="{{ url('doctor-course-class/'.$solve_class_id.'/'.($course_exam_result->doctor_course->id ?? 0)) }}" class="btn btn-info float-right" style="margin-left: 30px">
                                                        Solve Class
                                                    </a>
                                                @endif
                                                @if( \App\BatchesSchedules::back_url( ) )
                                                    <a href="{{ \App\BatchesSchedules::back_url() }}" class="btn btn-warning float-right">Back To
                                                        Schedule
                                                    </a>
                                                @endif
                                                <a href="{{ url('doctor-course-schedule/'.$course_exam_result->doctor_course->id) }}" class="btn btn-info float-right">Back To New
                                                        Schedule
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                <style>

                    .class-details {
                        color: gray;
                        border: none;
                        background: none;
                        outline: none;
                    }
                </style>

                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-striped table-bordered table-hover datatable">
                            <tr>
                                <th>Exam Name</th>
                                <td>{{ isset($course_exam_result->exam->name) ? $course_exam_result->exam->name : '' }}</td>
                            </tr>
                            <tr>
                                <th>Course</th>
                                <td>{{ isset($course_exam_result->doctor_course->course->name)?$course_exam_result->doctor_course->course->name:'' }}
                                </td>
                            </tr>
                            <tr>
                                <th>Session</th>
                                <td>{{ isset($course_exam_result->doctor_course->session->name)?$course_exam_result->doctor_course->session->name: ''}}</td>
                            </tr>
                            <tr>
                                <th>Batch</th>
                                <td>{{ isset($course_exam_result->batch->name)?$course_exam_result->batch->name: ''}}</td>
                            </tr>
                            <tr>
                                <th>Correct Mark</th>
                                <td>{{ isset($course_exam_result->correct_mark)?$course_exam_result->correct_mark:'' }}</td>
                            </tr>
                            <tr style="background-color: #ffb3b3">
                                <th>Wrong Answer</th>
                                <td>{{ isset($course_exam_result->wrong_answers)?$course_exam_result->wrong_answers:'' }}</td>
                            </tr>
                            <tr>
                                <th>Negative Mark</th>
                                <td>{{ isset($course_exam_result->negative_mark)?$course_exam_result->negative_mark:'' }}</td>
                            </tr>

                            <tr>
                                <th>Obtained Mark</th>
                                <td>{{ isset($course_exam_result->obtained_mark)?$course_exam_result->obtained_mark:'' }}</td>
                            </tr>
                            <tr>
                                <th>Highest Mark</th>
                                <td>{{ isset($result['highest_mark'])?$result['highest_mark']:'' }}</td>
                            </tr>

                            <tr style="background-color: #80ff80">
                                <th>Candidate Position 
                                    <button class="class-details" type="button" data-bs-toggle="modal" data-bs-target="#exampleModal">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" width="25px" height="25px">
                                            <path fill="currentColor" d="M44,24c0,11.045-8.955,20-20,20S4,35.045,4,24S12.955,4,24,4S44,12.955,44,24z"></path>
                                            <path fill="#fff" d="M22 22h4v11h-4V22zM26.5 16.5c0 1.379-1.121 2.5-2.5 2.5s-2.5-1.121-2.5-2.5S22.621 14 24 14 26.5 15.121 26.5 16.5z"></path>
                                        </svg>        
                                    </button>
                                                                        
                                      <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                          <div class="modal-content">
                                            <div class="modal-header">
                                              <h5 class="modal-title" id="exampleModalLabel">Result Position FAQ</h5>
                                              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div>
                                                    (১)Candidate Position:
                                                    যেমন ধরুন, আপনি  "নেফ্রোলজি" ডিসিপ্লিন সিলেক্ট করেছেন এবং ক্যান্ডিডেট টাইপ "প্রাইভেট" এডমিশন ফর্মে পূরণ করেছেন । এখন আপনি পরীক্ষা দেওয়ার পর যে পজিশন দেখতে পাচ্ছেন সেটা হচ্ছে আপনার মত যারা(ব্র্যাকেট এর মধ্যে দেয়া) "নেফ্রোলজি" ডিসিপ্লিন সিলেক্ট করেছিল এবং ক্যান্ডিডেট টাইপ প্রাইভেট দিয়েছিল তাদের মধ্যে আপনার পজিশন।
                                                    রেসিডেন্সি বা ডিপ্লোমা ফাইনাল পরীক্ষাতে এটা হিসাব করেই রেজাল্ট হয় । 
                                                </div>
                                                <div>
                                                    (২)Batch Position:
                                                    আপনি যে ব্যাচে ভর্তি হয়েছেন সেই ব্যাচের সকল পরীক্ষার্থীদের মধ্যে আপনার পজিশন । 
                                                </div>
                                                <div>
                                                    (৩)Overall Position:
                                                    রেসিডেন্সি / ডিপ্লোমা কোর্সে ভর্তিকৃত সকল ব্যাচের সব স্টুডেন্টদের মধ্যে আপনার পজিশন । 
                                                </div>
                                                <div>
                                                    বিশেষ দ্রষ্টব্য:
                                                    যেহেতু পরীক্ষার্থীরা বিভিন্ন সময় পরীক্ষা দিতে থাকেন তাই এ সকল পজিশন সবসময় আপডেট হতে থাকে । 
                                                    রেসিডেন্সি অথবা ডিপ্লোমা পরীক্ষার রেজাল্ট প্রসেসিং সম্পর্কে বিস্তারিত জানতে "Click Me" Button  ক্লিক করুন ।
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                             
                                             <a href="https://www.genesisonlineuniversity.com/blog/3" type="button" class="btn btn-primary">Click Me</a>
                                             <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Back</button>
                                            </div>
                                          </div>
                                        </div>
                                      </div>
                                </th>
                                @if($course_exam_result->exam->institute_id == 6 || $course_exam_result->exam->institute_id ==\App\Providers\AppServiceProvider::$COMBINED_INSTITUTE_ID)
                                    <td>
                                        {{ 
                                            $course_exam_result->doctor_course->candidate_type == "Autonomous/Private"
                                            ? "P" : ( substr( $course_exam_result->doctor_course->candidate_type ?? '',0,1 ) )
                                        }}-{{ isset($result['candidate_position'])?$result['candidate_position'][0]:'' }} 
                                        <span>({{'Total Examinee ' . ($result['candidate_position'][1] ?? '')}})</span>
                                    </td>
                                @endif
                            </tr>

                            <tr>
                                <th>Discipline Position</th>
                                <td>{{ isset($result['subject_position'])?$result['subject_position']:'' }}</td>
                            </tr>
                            <tr>
                                <th>Overall Position</th>
                                <td>{{ isset($result['overall_position'])?$result['overall_position']:'' }}</td>
                            </tr>
                            <tr>
                                <th>Batch Position</th>
                                <td>{{ isset($result['batch_position'])?$result['batch_position']:'' }}</td>
                            </tr>
                        </table>

                        <a href="{{ url('course-exam-doctor-answer/'.$course_exam_result->doctor_course->id.'/'.$course_exam_result->exam->id .'/'. $schedule_id ) }}"
                            class="btn btn-sm btn-primary my-1">View Answers</a>
                        <a target="blank"
                            href="{{ url('only-question-list/'.$course_exam_result->doctor_course->id.'/'.$course_exam_result->exam->id) }}"
                            class="btn btn-sm btn-primary my-1">Q Print</a>
                        <a target="blank"
                            href="{{ url('question-answer-list/'.$course_exam_result->doctor_course->id.'/'.$course_exam_result->exam->id) }}"
                            class="btn btn-sm btn-primary my-1">Q + A Print</a>

                    </div>
                </div>
            </div>
        </div>

    </div>

</div>
@endsection