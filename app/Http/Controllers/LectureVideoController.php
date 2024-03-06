<?php

namespace App\Http\Controllers;

use App\BatchesSchedules;
use App\DoctorClassView;
use App\Exam;
use App\Exam_question;

use App\Lecture_video_batch_lecture_video;
use App\LectureVideoDiscipline;
use App\LectureVideoFaculty;
use App\OnlineLectureAddress;
use App\OnlineLectureLink;
use App\OnlineExamCommonCode;
use App\OnlineExamLink;
use App\Page;
use App\Providers\AppServiceProvider;
use App\QuestionTypes;
use App\Result;
use App\Role;
use App\ScheduleDetail;
use App\Sessions;
use App\Subjects;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Doctors;
use App\Courses;
use App\Topics;
use App\DoctorsCourses;
use App\Faculty;
use App\LectureVideo;
use App\LectureVideoLink;
use App\LectureVideoTopics;
use Jenssegers\Agent\Agent;
use App\Notices;
use Carbon\Carbon;
use Illuminate\Cache\NullStore;
use Session;
use Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;


class LectureVideoController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:doctor');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function lecture_videos()
    {
        $doc_info = Doctors::where('id', Auth::guard('doctor')->id())->first();
        $doctor_courses = DoctorsCourses::where(['doctor_id' => $doc_info->id, 'is_trash' => '0', 'status' => '1'])->where('payment_status', '!=', 'No Payment')->get();

        //dd($doctor_courses);

        $data['doc_info'] = $doc_info;
        $data['doctor_courses'] = $doctor_courses;
        $video_link_ids = array();
        foreach ($doctor_courses as $key => $doctor_course) {

            if (LectureVideoLink::where(['year' => $doctor_course->year, 'session_id' => $doctor_course->session_id, 'lecture_video_batch.institute_id' => $doctor_course->institute_id, 'lecture_video_batch.course_id' => $doctor_course->course_id, 'batch_id' => $doctor_course->batch_id])->first()) {

                $video_link_ids[] = LectureVideoLink::where(['year' => $doctor_course->year, 'session_id' => $doctor_course->session_id, 'lecture_video_batch.institute_id' => $doctor_course->institute_id, 'lecture_video_batch.course_id' => $doctor_course->course_id, 'batch_id' => $doctor_course->batch_id])->value('id');
            }
        }

        // $lecture_video_batch = LectureVideoLink::whereIn('lecture_video_batch.id',$video_link_ids)
        //     ->join('lecture_video_batch_lecture_video','lecture_video_batch_lecture_video.lecture_video_batch_id','lecture_video_batch.id')
        //     ->join('lecture_video','lecture_video.id','lecture_video_batch_lecture_video.lecture_video_id')
        //     ->paginate(10);

        //$data['lecture_video_batch'] = $lecture_video_batch;

        $doctor_courses = DoctorsCourses::where(['doctor_id' => $doc_info->id, 'is_trash' => '0', 'status' => '1'])->with('batch', 'course')->where('payment_status', '!=', 'No Payment')->get();
        $doctor_courses_with_video = array();
        foreach ($doctor_courses as $key => $doctor_course) {

            if (LectureVideoLink::where(['year' => $doctor_course->year, 'session_id' => $doctor_course->session_id, 'lecture_video_batch.institute_id' => $doctor_course->institute_id, 'lecture_video_batch.course_id' => $doctor_course->course_id, 'batch_id' => $doctor_course->batch_id])->first()) {
                $doctor_courses_with_video[] = $doctor_course;
            }
        }

        $data['doctor_courses'] = $doctor_courses_with_video;
        // return $data['doctor_courses'];
        return view('lecture_video/lecture_topics', $data);
    }

    public function doctor_course_lecture_video($doctor_course_id)
    {

        $text = '';
        $doc_info = Doctors::where('id', Auth::guard('doctor')->id())->first();

        $doctor_courses = DoctorsCourses::where(['id' => $doctor_course_id, 'is_trash' => '0', 'status' => '1'])->where('payment_status', '!=', 'No Payment')->get();

        $data['doc_info'] = $doc_info;
        $data['doctor_courses'] = $doctor_courses;
        $video_link_ids = array();
        foreach ($doctor_courses as $key => $doctor_course) {

            if (LectureVideoLink::where(['deleted_at' => null, 'year' => $doctor_course->year, 'session_id' => $doctor_course->session_id, 'lecture_video_batch.institute_id' => $doctor_course->institute_id, 'lecture_video_batch.course_id' => $doctor_course->course_id, 'batch_id' => $doctor_course->batch_id])->first()) {

                $video_link_ids[] = LectureVideoLink::where(
                    [
                        'year' => $doctor_course->year, 'session_id' => $doctor_course->session_id,
                        'lecture_video_batch.institute_id' => $doctor_course->institute_id,
                        'lecture_video_batch.course_id' => $doctor_course->course_id,
                        'batch_id' => $doctor_course->batch_id,
                        'deleted_at' => null
                    ]
                )->value('id');
            }
        }


        if ($doctor_courses[0]->batch->fee_type == "Batch") {
            $lecture_video_batch = LectureVideoLink::select('lecture_video.*', 'lecture_video_batch_lecture_video.created_at as created_time')
                ->join('lecture_video_batch_lecture_video', 'lecture_video_batch_lecture_video.lecture_video_batch_id', 'lecture_video_batch.id')
                ->join('lecture_video', 'lecture_video.id', 'lecture_video_batch_lecture_video.lecture_video_id')
                ->orderBy('lecture_video_batch_lecture_video.id', 'desc')
                ->whereNull('lecture_video_batch_lecture_video.deleted_at')
                ->where('lecture_video.status', 1);

            if ($doctor_courses[0]->institute->id == AppServiceProvider::$COMBINED_INSTITUTE_ID) {

                $faculty_name = Faculty::where('id', $doctor_courses[0]->faculty_id)->value('name');
                $faculty_ids = Faculty::where('name', $faculty_name)->pluck('id');
                $bcps_subject_name = Subjects::where('id', $doctor_courses[0]->bcps_subject_id)->value('name');
                $bcps_subject_ids = Subjects::where('name', $bcps_subject_name)->pluck('id');

                $subject_video_ids = LectureVideoDiscipline::whereIn('subject_id', $bcps_subject_ids)
                    ->join('lecture_video_assigns', 'lecture_video_assigns.id', 'lecture_video_discipline.lecture_video_assign_id')
                    ->whereNull('lecture_video_assigns.deleted_at')
                    ->where('lecture_video_assigns.institute_id', AppServiceProvider::$COMBINED_INSTITUTE_ID)
                    ->pluck('lecture_video_discipline.lecture_video_id');

                $discipline_video_ids = LectureVideoFaculty::whereIn('faculty_id', $faculty_ids)
                    ->join('lecture_video_assigns', 'lecture_video_assigns.id', 'lecture_video_faculties.lecture_video_assign_id')
                    ->whereNull('lecture_video_assigns.deleted_at')
                    ->where('lecture_video_assigns.institute_id', AppServiceProvider::$COMBINED_INSTITUTE_ID)
                    ->pluck('lecture_video_faculties.lecture_video_id');

                $vid_ids = $subject_video_ids->merge($discipline_video_ids);

                $lecture_video_batch
                    ->join('lecture_video_assigns', 'lecture_video_assigns.lecture_video_id', 'lecture_video.id')
                    ->whereNull('lecture_video_batch_lecture_video.deleted_at')
                    ->whereNull('lecture_video_batch_lecture_video.deleted_at')
                    ->whereIn('lecture_video.id', $vid_ids)
                    ->whereIn('lecture_video_batch.id', $video_link_ids);
            } else {
                $lecture_video_batch->whereIn('lecture_video_batch.id', $video_link_ids);
            }
        } else if ($doctor_courses[0]->batch->fee_type == "Discipline_Or_Faculty") {
            if ($doctor_courses[0]->institute->type == 1) {
                $lecture_video_batch = LectureVideoLink::select('lecture_video.*', 'lecture_video_batch_lecture_video.created_at as created_time');
                $faculty_name = Faculty::where('id', $doctor_courses[0]->faculty_id)->value('name');
                $faculty_ids = Faculty::where('name', $faculty_name)->pluck('id');

                $lecture_video_batch->where('name', 'like', '%' . $text . '%')
                    ->whereIn('lecture_video_batch.id', $video_link_ids)
                    ->join('lecture_video_batch_lecture_video', 'lecture_video_batch_lecture_video.lecture_video_batch_id', 'lecture_video_batch.id')
                    ->join('lecture_video', 'lecture_video.id', 'lecture_video_batch_lecture_video.lecture_video_id')
                    ->join('lecture_video_faculties', 'lecture_video_faculties.lecture_video_id', 'lecture_video.id')
                    ->orderBy('lecture_video_batch_lecture_video.id', 'desc')
                    ->whereNull('lecture_video_batch_lecture_video.deleted_at')
                    ->whereNull('lecture_video_faculties.deleted_at')
                    ->whereIn('lecture_video_faculties.faculty_id', $faculty_ids)
                    ->where('lecture_video.status', 1);

                if ($doctor_courses[0]->institute->id == AppServiceProvider::$COMBINED_INSTITUTE_ID) {
                    $subject_name = Subjects::where('id', $doctor_courses[0]->bcps_subject_id)->value('name');
                    $subject_ids = Subjects::where('name', $subject_name)->pluck('id');
                    $lecture_video_batch
                        ->join('lecture_video_discipline', 'lecture_video_discipline.lecture_video_id', 'lecture_video.id')
                        ->whereIn('lecture_video_discipline.subject_id', $subject_ids);
                }
            } else {
                $subject_name = Subjects::where('id', $doctor_courses[0]->subject_id)->value('name');
                $subject_ids = Subjects::where('name', $subject_name)->pluck('id');
                $lecture_video_batch = LectureVideoLink::select('lecture_video.*', 'lecture_video_batch_lecture_video.created_at as created_time')
                    ->where('name', 'like', '%' . $text . '%')
                    ->whereIn('lecture_video_batch.id', $video_link_ids)
                    ->whereIn('lecture_video_discipline.subject_id', $subject_ids)
                    ->join('lecture_video_batch_lecture_video', 'lecture_video_batch_lecture_video.lecture_video_batch_id', 'lecture_video_batch.id')
                    ->join('lecture_video', 'lecture_video.id', 'lecture_video_batch_lecture_video.lecture_video_id')
                    ->join('lecture_video_discipline', 'lecture_video_discipline.lecture_video_id', 'lecture_video.id')
                    ->orderBy('lecture_video_batch_lecture_video.id', 'desc')
                    ->whereNull('lecture_video_batch_lecture_video.deleted_at')
                    ->whereNull('lecture_video_discipline.deleted_at')
                    ->where('lecture_video.status', 1);
            }
        }

        $data['lecture_video_batch'] = $lecture_video_batch->groupBy('lecture_video.id')->paginate(10);

        $doctor_courses = DoctorsCourses::where(['doctor_id' => $doc_info->id, 'is_trash' => '0', 'status' => '1'])->where('payment_status', '!=', 'No Payment')->get();
        $doctor_courses_with_video = array();
        foreach ($doctor_courses as $key => $doctor_course) {

            if (LectureVideoLink::where(['year' => $doctor_course->year, 'session_id' => $doctor_course->session_id, 'lecture_video_batch.institute_id' => $doctor_course->institute_id, 'lecture_video_batch.course_id' => $doctor_course->course_id, 'batch_id' => $doctor_course->batch_id])->first()) {
                $doctor_courses_with_video[] = $doctor_course;
            }
        }

        $data['doctor_courses'] = $doctor_courses_with_video;
        $data['doctor_course_id'] = $doctor_course_id;

        return view('lecture_video/lecture_topics', $data);
    }

    public function doctor_course_lecture_video_ajax(Request $request, $doctor_course_id)
    {
        $text = $request->text;

        $doc_info = Doctors::where('id', Auth::guard('doctor')->id())->first();

        $doctor_courses = DoctorsCourses::where(['id' => $doctor_course_id, 'is_trash' => '0', 'status' => '1'])->where('payment_status', '!=', 'No Payment')->get();

        $data['doc_info'] = $doc_info;
        $data['doctor_courses'] = $doctor_courses;
        $video_link_ids = array();
        foreach ($doctor_courses as $key => $doctor_course) {

            if (LectureVideoLink::where(['year' => $doctor_course->year, 'session_id' => $doctor_course->session_id, 'lecture_video_batch.institute_id' => $doctor_course->institute_id, 'lecture_video_batch.course_id' => $doctor_course->course_id, 'batch_id' => $doctor_course->batch_id])->first()) {

                $video_link_ids[] = LectureVideoLink::where(
                    [
                        'year' => $doctor_course->year, 'session_id' => $doctor_course->session_id,
                        'lecture_video_batch.institute_id' => $doctor_course->institute_id,
                        'lecture_video_batch.course_id' => $doctor_course->course_id,
                        'batch_id' => $doctor_course->batch_id
                    ]
                )->value('id');
            }
        }

        if ($doctor_courses[0]->batch->fee_type == "Batch") {
            $lecture_video_batch = LectureVideoLink::select('lecture_video.*', 'lecture_video_batch_lecture_video.created_at as created_time')
                ->where('name', 'like', '%' . $text . '%')
                ->whereIn('lecture_video_batch.id', $video_link_ids)
                ->join('lecture_video_batch_lecture_video', 'lecture_video_batch_lecture_video.lecture_video_batch_id', 'lecture_video_batch.id')
                ->join('lecture_video', 'lecture_video.id', 'lecture_video_batch_lecture_video.lecture_video_id')
                ->orderBy('lecture_video_batch_lecture_video.id', 'desc')
                ->whereNull('lecture_video_batch_lecture_video.deleted_at')
                ->where('lecture_video.status', 1);
        } else if ($doctor_courses[0]->batch->fee_type == "Discipline_Or_Faculty") {

            if ($doctor_courses[0]->institute->type == 1) {
                $faculty_name = Faculty::where('id', $doctor_courses[0]->faculty_id)->value('name');
                $faculty_ids = Faculty::where('name', $faculty_name)->pluck('id');
                $lecture_video_batch = LectureVideoLink::select('lecture_video.*', 'lecture_video_batch_lecture_video.created_at as created_time')
                    ->where('name', 'like', '%' . $text . '%')
                    ->whereIn('lecture_video_batch.id', $video_link_ids)
                    ->whereIn('lecture_video_faculties.faculty_id', $faculty_ids)
                    ->join('lecture_video_batch_lecture_video', 'lecture_video_batch_lecture_video.lecture_video_batch_id', 'lecture_video_batch.id')
                    ->join('lecture_video', 'lecture_video.id', 'lecture_video_batch_lecture_video.lecture_video_id')
                    ->join('lecture_video_faculties', 'lecture_video_faculties.lecture_video_id', 'lecture_video.id')
                    ->orderBy('lecture_video_batch_lecture_video.id', 'desc')
                    ->whereNull('lecture_video_batch_lecture_video.deleted_at')
                    ->where('lecture_video.status', 1);
            } else {
                $subject_name = Subjects::where('id', $doctor_courses[0]->subject_id)->value('name');
                $subject_ids = Subjects::where('name', $subject_name)->pluck('id');
                $lecture_video_batch = LectureVideoLink::select('lecture_video.*', 'lecture_video_batch_lecture_video.created_at as created_time')
                    ->where('name', 'like', '%' . $text . '%')
                    ->whereIn('lecture_video_batch.id', $video_link_ids)
                    ->whereIn('lecture_video_discipline.subject_id', $subject_ids)
                    ->join('lecture_video_batch_lecture_video', 'lecture_video_batch_lecture_video.lecture_video_batch_id', 'lecture_video_batch.id')
                    ->join('lecture_video', 'lecture_video.id', 'lecture_video_batch_lecture_video.lecture_video_id')
                    ->join('lecture_video_discipline', 'lecture_video_discipline.lecture_video_id', 'lecture_video.id')
                    ->orderBy('lecture_video_batch_lecture_video.id', 'desc')
                    ->whereNull('lecture_video_batch_lecture_video.deleted_at')
                    ->where('lecture_video.status', 1);
            }
        }

        $data['lecture_video_batch'] = $lecture_video_batch->get();

        $doctor_courses = DoctorsCourses::where(['doctor_id' => $doc_info->id, 'is_trash' => '0', 'status' => '1'])->where('payment_status', '!=', 'No Payment')->get();
        $doctor_courses_with_video = array();
        foreach ($doctor_courses as $key => $doctor_course) {
            if (LectureVideoLink::where(['year' => $doctor_course->year, 'session_id' => $doctor_course->session_id, 'lecture_video_batch.institute_id' => $doctor_course->institute_id, 'lecture_video_batch.course_id' => $doctor_course->course_id, 'batch_id' => $doctor_course->batch_id])->first()) {
                $doctor_courses_with_video[] = $doctor_course;
            }
        }

        $data['doctor_courses'] = $doctor_courses_with_video;

        return view('lecture_video/lecture_topics_ajax', $data);
    }

    public function lecture_video($course_id, $batch_id)
    {
        $doc_info = Doctors::where('id', Auth::guard('doctor')->id())->first();
        $doctor_courses = DoctorsCourses::where(['doctor_id' => $doc_info->id, 'course_id' => $course_id, 'batch_id' => $batch_id, 'is_trash' => '0'])->where('payment_status', '!=', 'No Payment')->get();

        //dd($doctor_courses);

        $data['doc_info'] = $doc_info;
        $data['doctor_courses'] = $doctor_courses;
        $video_link_ids = array();
        foreach ($doctor_courses as $key => $doctor_course) {

            if (LectureVideoLink::where(['year' => $doctor_course->year, 'session_id' => $doctor_course->session_id, 'lecture_video_batch.institute_id' => $doctor_course->institute_id, 'lecture_video_batch.course_id' => $doctor_course->course_id, 'batch_id' => $doctor_course->batch_id])->first()) {

                $video_link_ids[] = LectureVideoLink::where(['year' => $doctor_course->year, 'session_id' => $doctor_course->session_id, 'lecture_video_batch.institute_id' => $doctor_course->institute_id, 'lecture_video_batch.course_id' => $doctor_course->course_id, 'batch_id' => $doctor_course->batch_id])->value('id');
            }
        }

        //echo "<pre>";print_r($video_link_ids);exit;

        if ($doctor_courses[0]->batch->fee_type == "Batch") {
            $lecture_video_batch = LectureVideoLink::whereIn('lecture_video_batch.id', $video_link_ids)
                ->join('lecture_video_batch_lecture_video', 'lecture_video_batch_lecture_video.lecture_video_batch_id', 'lecture_video_batch.id')
                ->join('lecture_video', 'lecture_video.id', 'lecture_video_batch_lecture_video.lecture_video_id')
                ->paginate(10);
        } else if ($doctor_courses[0]->batch->fee_type == "Discipline") {
            $lecture_video_batch = LectureVideoLink::whereIn('lecture_video_batch.id', $video_link_ids)->where('lecture_video_discipline.subject_id', $doctor_courses[0]->subject_id)
                ->join('lecture_video_batch_lecture_video', 'lecture_video_batch_lecture_video.lecture_video_batch_id', 'lecture_video_batch.id')
                ->join('lecture_video', 'lecture_video.id', 'lecture_video_batch_lecture_video.lecture_video_id')
                ->join('lecture_video_discipline', 'lecture_video_discipline.lecture_video_id', 'lecture_video.id')
                //->join('batch_discipline_fees','batch_discipline_fees.batch_id','lecture_video_batch.batch_id')
                ->paginate(10);
        }

        //echo "<pre>";print_r($lecture_video_batch);exit;


        $data['lecture_video_batch'] = $lecture_video_batch;

        $doctor_courses = DoctorsCourses::where(['doctor_id' => $doc_info->id, 'is_trash' => '0'])->where('payment_status', '!=', 'No Payment')->get();
        $doctor_courses_with_video = array();
        foreach ($doctor_courses as $key => $doctor_course) {

            if (LectureVideoLink::where(['year' => $doctor_course->year, 'session_id' => $doctor_course->session_id, 'lecture_video_batch.institute_id' => $doctor_course->institute_id, 'lecture_video_batch.course_id' => $doctor_course->course_id, 'batch_id' => $doctor_course->batch_id])->first()) {
                $doctor_courses_with_video[] = $doctor_course;
            }
        }

        $data['doctor_courses'] = $doctor_courses_with_video;

        return view('lecture_video/lecture_topics', $data);
    }

    public function lecture_topics($id)
    {
        $data['doc_info'] = Doctors::where('id', Auth::guard('doctor')->id())->first();
        $doctor_course_info = DoctorsCourses::where('id', $id)->first();
        $data['doctor_course_info'] = $doctor_course_info;
        $lecture_video_batch_id = LectureVideoLink::where(['year' => $doctor_course->year, 'session_id' => $doctor_course->session_id, 'lecture_video_batch.institute_id' => $doctor_course->institute_id, 'lecture_video_batch.course_id' => $doctor_course->course_id, 'batch_id' => $doctor_course->batch_id])->value('id');
        $lecture_video_batch = LectureVideoLink::where(['year' => $doctor_course->year, 'session_id' => $doctor_course->session_id, 'lecture_video_batch.institute_id' => $doctor_course->institute_id, 'lecture_video_batch.course_id' => $doctor_course->course_id, 'batch_id' => $doctor_course->batch_id])
            ->join('lecture_video_topics', 'lecture_video_topics.lecture_video_batch_id', 'lecture_video_batch.id')
            ->join('topics', 'topics.id', 'lecture_video_topics.topic_id')
            ->join('lecture_video_post', 'lecture_video_post.topic_id', 'topics.id')
            ->paginate(10);
        $data['lecture_video_batch'] = $lecture_video_batch;
        $data['lecture_video_batch_id'] = $lecture_video_batch_id;
        $data['lecture_video_topics'] = LectureVideoTopics::where('lecture_video_batch_id', $lecture_video_batch_id)->join('topics', 'topics.id', 'lecture_video_topics.topic_id')->pluck('topics.name', 'topics.id');
        return view('lecture_topics', $data);
    }

    public function lecture_details($id, $doctor_course_id = null)
    {
        $data['doc_info'] = Doctors::where('id', Auth::guard('doctor')->id())->first();
        $doctor_course = DoctorsCourses::where('id', $data['doc_info']->id)->first();


        $data['doctor_course_info'] = $doctor_course;
        $data['link'] = LectureVideo::where('id', $id)->first();
        $agent =  new Agent();
        $data['browser'] = $agent->browser();

        $video_address = $data['link']->lecture_address;

        $isZoomLink = preg_match('/https?\:\/\/[0-9A-Za-z]*\.?zoom\.us/', $video_address);

        $data['isZoomLink'] = $isZoomLink;

        if ($doctor_course_id) {
            ScheduleDetail::update_doctor_class_view($doctor_course_id, $id);
        }


        $data['doctor_course'] = DoctorsCourses::where('id', $doctor_course_id)->first();

        // // return $id;/
        // $data['doctor_class_view'] = DoctorClassView::query()
        //     ->where(['doctor_course_id' => $doctor_course_id, 'lecture_video_id' => $id])
        //     ->where('status', 1)->first();

        // if ($data['doctor_class_view']) {
        //     if ($data['doctor_class_view']->end < Carbon::now()) {
        //         DoctorClassView::where([
        //             'doctor_course_id' => $doctor_course_id,
        //             'lecture_video_id' => $id,
        //         ])->update(
        //             [
        //                 'status'           => 0
        //             ]
        //         );
        //     }
        // }



        // $doctor_course_schedule_details = DoctorCourseScheduleDetails::where('doctor_course_id',$doctor_course_id)->first();
        // if(!isset($doctor_course_schedule_details))
        // {
        //     DoctorCourseScheduleDetails::insert(['doctor_course_id'=>$doctor_course_id,'schedule_details_id'=>$schedule_details_id])->first();
        // }

        return view('lecture_details', $data);
    }

    public function lecture_video_details($id)
    {
        $data['doc_info'] = Doctors::where('id', Auth::guard('doctor')->id())->first();
        $data['doctor_course_info'] = DoctorsCourses::where('id', $data['doc_info']->id)->first();
        $data['link'] = LectureVideo::where('id', $id)->first();
        $agent =  new Agent();
        $data['browser'] = $agent->browser();
        return view('lecture_video/lecture_details', $data);
    }

    public function topic_lecture_videos(Request $request)
    {
        $lecture_video_batch_id = $request->lecture_video_batch_id;
        $topic_id = $request->topic_id;

        $data['doc_info'] = Doctors::where('id', Auth::guard('doctor')->id())->first();

        $lecture_video_batch = LectureVideo::where(['topic_id' => $topic_id])
            ->paginate(10);
        $data['lecture_video_batch'] = $lecture_video_batch;
        $data['lecture_video_batch_id'] = $lecture_video_batch_id;
        $data['topic'] = Topics::where('id', $topic_id)->first();
        $data['lecture_video_topics'] = LectureVideoTopics::where('lecture_video_batch_id', $lecture_video_batch_id)->join('topics', 'topics.id', 'lecture_video_topics.topic_id')->pluck('topics.name', 'topics.id');
        //echo '<pre>';print_r($data['topic']);exit;
        return view('topic_lecture_videos', $data);
    }



    public function lecture_videooo()
    {

        $doc_info = Doctors::where('id', Auth::guard('doctor')->id())->first();
        $doctor_courses = DoctorsCourses::where('doctor_id', $doc_info->id)->get();

        $data['doc_info'] = $doc_info;
        $data['doctor_courses'] = $doctor_courses;
        $online_lecture_links = array();
        foreach ($doctor_courses as $key => $doctor_course) {
            $exam_comm_code_ids = OnlineLectureLink::where(['year' => $doctor_course->year, 'session_id' => $doctor_course->session_id, 'institute_id' => $doctor_course->institute_id, 'course_id' => $doctor_course->course_id, 'batch_id' => $doctor_course->batch_id])->pluck('lecture_address_id');

            foreach ($exam_comm_code_ids as $id) {
                $online_lecture_links[$doctor_course->reg_no][] =  OnlineLectureAddress::select('*')->where('id', $id)->get();
            }
        }
        $data['online_lecture_links'] = $online_lecture_links;
        $data['rc'] = '';
        $data['video_link'] = OnlineLectureAddress::select('*')->where('status', 1)->get();
        return view('lecture/lecture_video', $data);
    }
}
