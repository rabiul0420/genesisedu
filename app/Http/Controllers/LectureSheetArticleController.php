<?php

namespace App\Http\Controllers;

use App\BatchesSchedules;
use App\DoctorCourseLectureSheet;
use App\Exam;
use App\Exam_question;
use App\Http\Helpers\DoctorLectureSheetHelper;
use App\LectureSheet;
use App\LectureSheetTopic;
use App\LectureSheetTopicBatch;
use App\LectureSheetTopicBatchLectureSheetTopic;
use App\LectureSheetTopicDiscipline;
use App\LectureSheetTopicFaculty;
use App\OnlineLectureAddress;
use App\OnlineLectureLink;
use App\OnlineExamCommonCode;
use App\OnlineExamLink;
use App\Page;
use App\Providers\AppServiceProvider;
use App\QuestionTypes;
use App\Result;
use App\Role;
use App\Sessions;
use App\Subjects;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Doctors;
use App\Courses;
use App\Topics;
use App\DoctorsCourses;
use App\Faculty;
use App\LectureSheetArticle;
use App\LectureSheetArticleBatch;
use App\LectureSheetArticleTopics;
use App\Notices;
use Session;
use Validator;

use Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;


class LectureSheetArticleController extends Controller
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

    public function doctor_course_lecture_sheet_delivery_print($doctor_course_id)
    {

        $doctor_course = DoctorsCourses::where(['id' => $doctor_course_id])->first();
        $lecture_sheet_topic_batch = LectureSheetTopicBatch::where(['year' => $doctor_course->year, 'session_id' => $doctor_course->session_id, 'batch_id' => $doctor_course->batch_id])->first();

        $lecture_sheet_topic_ids =
            DoctorLectureSheetHelper::lectureSheetTopics( $doctor_course, $lecture_sheet_topic_batch->id ?? '')
            ->pluck('lecture_sheet_topic.id' );


        //LectureSheet::$deliveredCourseId = $doctor_course_id;

        $lecture_sheets = LectureSheet::query( )
            ->with('doctor_delivered' )
            ->join('lecture_sheet_topic_lecture_sheet','lecture_sheet_topic_lecture_sheet.lecture_sheet_id','=','lecture_sheet.id')
            ->whereIn('lecture_sheet_topic_id',$lecture_sheet_topic_ids )
            ->whereNull('lecture_sheet_topic_lecture_sheet.deleted_at')
            ->select( 'lecture_sheet.*' )
            ->get();

        $data['doctor_course'] = $doctor_course;
        $data['lecture_sheets'] = $lecture_sheets;
        $data['count']  =$lecture_sheets->count();

        return view('lecture_sheet_article/print_lecture_sheet_delivery', $data);

        if (isset($doctor_course->batch->fee_type)) {

            if ($doctor_course->batch->fee_type == "Batch") {


                if ($doctor_course->institute->id == AppServiceProvider::$COMBINED_INSTITUTE_ID) {

                    $lecture_sheet_topics = LectureSheetTopic::select('*');

                    $faculty_name = Faculty::where('id', $doctor_course->faculty_id)->value('name');
                    $faculty_ids = Faculty::where('name', $faculty_name)->pluck('id');
                    $bcps_subject_name = Subjects::where('id', $doctor_course->bcps_subject_id)->value('name');
                    $bcps_subject_ids = Subjects::where('name', $bcps_subject_name)->pluck('id');


                    $subject_lecture_sheet_topic_ids = LectureSheetTopicDiscipline::whereIn('subject_id', $bcps_subject_ids)
                        ->join('lecture_sheet_topic_assign', 'lecture_sheet_topic_assign.id', 'lecture_sheet_topic_discipline.lecture_sheet_topic_assign_id')
                        ->whereNull('lecture_sheet_topic_assign.deleted_at')
                        ->where('lecture_sheet_topic_assign.institute_id', AppServiceProvider::$COMBINED_INSTITUTE_ID)
                        ->pluck('lecture_sheet_topic_discipline.lecture_sheet_topic_id');

                    $discipline_lecture_sheet_topic_ids = LectureSheetTopicFaculty::whereIn('faculty_id', $faculty_ids)
                        ->join('lecture_sheet_topic_assign', 'lecture_sheet_topic_assign.id', 'lecture_sheet_topic_faculty.lecture_sheet_topic_assign_id')
                        ->whereNull('lecture_sheet_topic_assign.deleted_at')
                        ->where('lecture_sheet_topic_assign.institute_id', AppServiceProvider::$COMBINED_INSTITUTE_ID)
                        ->pluck('lecture_sheet_topic_faculty.lecture_sheet_topic_id');

                    if (isset($lecture_sheet_topic_batch->id)) {
                        $batch_lecture_sheet_topic_ids = LectureSheetTopicBatchLectureSheetTopic::where('lecture_sheet_topic_batch_id', $lecture_sheet_topic_batch->id)
                            ->pluck('lecture_sheet_topic_id');
                        $subject_lecture_sheet_topic_ids->merge($batch_lecture_sheet_topic_ids)->unique();
                    }

                    $topic_ids = $subject_lecture_sheet_topic_ids->merge($discipline_lecture_sheet_topic_ids);
                    $lecture_sheet_topics = $lecture_sheet_topics->whereIn('lecture_sheet_topic.id', $topic_ids)->paginate(100);
                } else if (isset($lecture_sheet_topic_batch->id)) {

                    $lecture_sheet_topics = LectureSheetTopicBatch::join('lecture_sheet_topic_batch_lecture_sheet_topic', 'lecture_sheet_topic_batch_lecture_sheet_topic.lecture_sheet_topic_batch_id', 'lecture_sheet_topic_batch.id')
                        ->join('lecture_sheet_topic', 'lecture_sheet_topic.id', 'lecture_sheet_topic_batch_lecture_sheet_topic.lecture_sheet_topic_id')
                        ->whereNull('lecture_sheet_topic_batch_lecture_sheet_topic.deleted_at')
                        ->whereNull('lecture_sheet_topic.deleted_at');

                    $lecture_sheet_topics = $lecture_sheet_topics->where('lecture_sheet_topic_batch.id', $lecture_sheet_topic_batch->id)->paginate(100);
                }
            } else if ($doctor_course->batch->fee_type == "Discipline_Or_Faculty" && isset($lecture_sheet_topic_batch->id)) {

                if ($doctor_course->institute->type == 1) {

                    $faculty_name = Faculty::where('id', $doctor_course->faculty_id)->value('name');
                    $faculty_ids = Faculty::where('name', $faculty_name)->pluck('id');
                    $lecture_sheet_topics = LectureSheetTopicBatch::select('lecture_sheet_topic.*')->where('lecture_sheet_topic_batch.id', $lecture_sheet_topic_batch->id)->whereIn('lecture_sheet_topic_faculty.faculty_id', $faculty_ids)
                        ->join('lecture_sheet_topic_batch_lecture_sheet_topic', 'lecture_sheet_topic_batch_lecture_sheet_topic.lecture_sheet_topic_batch_id', 'lecture_sheet_topic_batch.id')
                        ->join('lecture_sheet_topic', 'lecture_sheet_topic.id', 'lecture_sheet_topic_batch_lecture_sheet_topic.lecture_sheet_topic_id')
                        ->join('lecture_sheet_topic_faculty', 'lecture_sheet_topic_faculty.lecture_sheet_topic_id', 'lecture_sheet_topic.id')
                        ->paginate(100);
                } else {
                    $subject_name = Subjects::where('id', $doctor_course->subject_id)->value('name');
                    $subject_ids = Subjects::where('name', $subject_name)->pluck('id');
                    //dd($subject_ids);
                    $lecture_sheet_topics = LectureSheetTopicBatch::select('lecture_sheet_topic.*')->where('lecture_sheet_topic_batch.id', $lecture_sheet_topic_batch->id)
                        ->whereIn('lecture_sheet_topic_discipline.subject_id', $subject_ids)
                        ->join('lecture_sheet_topic_batch_lecture_sheet_topic', 'lecture_sheet_topic_batch_lecture_sheet_topic.lecture_sheet_topic_batch_id', 'lecture_sheet_topic_batch.id')
                        ->join('lecture_sheet_topic', 'lecture_sheet_topic.id', 'lecture_sheet_topic_batch_lecture_sheet_topic.lecture_sheet_topic_id')
                        ->join('lecture_sheet_topic_discipline', 'lecture_sheet_topic_discipline.lecture_sheet_topic_id', 'lecture_sheet_topic.id')
                        ->paginate(100);
                }
            }
        }

        //        dd( $lecture_sheet_topics );

        if (isset($lecture_sheet_topics)) {

            $lecture_sheet_topic_ids = array();
            foreach ($lecture_sheet_topics as $lecture_sheet_topic) {
                $lecture_sheet_topic_ids[] = $lecture_sheet_topic->id;
            }

            $lecture_sheets = LectureSheet::join('lecture_sheet_topic_lecture_sheet', 'lecture_sheet_topic_lecture_sheet.lecture_sheet_id', '=', 'lecture_sheet.id')
                ->whereIn('lecture_sheet_topic_id', $lecture_sheet_topic_ids)
                ->whereNull('lecture_sheet_topic_lecture_sheet.deleted_at')
                ->get();

            $data['lecture_sheets'] = $lecture_sheets;
            //dd($lecture_sheets);

            $array_delivered_lecture_sheets = array();

            $lect_ids = DoctorCourseLectureSheet::where(['doctor_course_id' => $doctor_course->id])->get();

            foreach ($lecture_sheets as $lecture_sheet) {
                if ($lect_ids->where('lecture_sheet_id', $lecture_sheet->lecture_sheet_id)->count() > 0) {
                    $array_delivered_lecture_sheets[] = $lecture_sheet->lecture_sheet_id;
                }
            }

            //return $array_delivered_lecture_sheets;

            $data['delivered_lecture_sheets'] = $array_delivered_lecture_sheets;
        }

        return view('lecture_sheet_article/print_lecture_sheet_delivery', $data);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function lecture_sheet_article()
    {
        $data['doc_info'] = Doctors::where('id', Auth::guard('doctor')->id())->first();
        $data['doctor_courses'] = DoctorsCourses::where(['doctor_id' => $data['doc_info']->id, 'is_trash' => '0', 'status' => '1'])
            ->where('payment_status', '!=', 'No Payment')->get();
        return view('lecture_sheet_article/lecture_sheet_article', $data);
    }

    public function lecture_sheet_article_topics($id)
    {
        $data['doc_info'] = Doctors::where('id', Auth::guard('doctor')->id())->first();
        $doctor_course_info = DoctorsCourses::where(['id' => $id, 'is_trash' => '0', 'status' => '1'])->where('payment_status', '!=', 'No Payment')->first();
        $data['doctor_course_info'] = $doctor_course_info;
        $lecture_sheet_article_batch_id = LectureSheetArticleBatch::where([
            'year' => $doctor_course_info->year,
            'session_id' => $doctor_course_info->session_id,
            'lecture_sheet_article_batch.institute_id' => $doctor_course_info->institute_id,
            'lecture_sheet_article_batch.course_id' => $doctor_course_info->course_id,
            'batch_id' => $doctor_course_info->batch_id
        ])->value('id');


        $lecture_sheet_batch = LectureSheetArticleBatch::where([
            'lecture_sheet_article_batch.year' => $doctor_course_info->year,
            'lecture_sheet_article_batch.session_id' => $doctor_course_info->session_id,
            'lecture_sheet_article_batch.institute_id' => $doctor_course_info->institute_id,
            'lecture_sheet_article_batch.course_id' => $doctor_course_info->course_id,
            'batch_id' => $doctor_course_info->batch_id
        ])->join('lecture_sheet_article_topics', 'lecture_sheet_article_topics.lecture_sheet_article_batch_id', 'lecture_sheet_article_batch.id')
            ->join('topics', 'topics.id', 'lecture_sheet_article_topics.topic_id')
            ->join('lecture_sheet_article', 'lecture_sheet_article.topic_id', 'topics.id')
            ->paginate(10);


        $data['lecture_sheet_article_batch'] = $lecture_sheet_batch;
        $data['lecture_sheet_article_batch_id'] = $lecture_sheet_article_batch_id;

        $data['lecture_sheet_article_topics'] = LectureSheetArticleTopics::where('lecture_sheet_article_batch_id', $lecture_sheet_article_batch_id)
            ->join('topics', 'topics.id', 'lecture_sheet_article_topics.topic_id')->pluck('topics.name', 'topics.id');
        return view('lecture_sheet_article/lecture_sheet_article_topics', $data);
    }

    public function lecture_sheet_article_details($id)
    {
        $data['doc_info'] = Doctors::where('id', Auth::guard('doctor')->id())->first();
        $data['doctor_course_info'] = DoctorsCourses::where(['id' => $id, 'is_trash' => '0', 'status' => '1'])->where('payment_status', '!=', 'No Payment')->first();
        $data['lecture_sheet'] = LectureSheetArticle::where('id', $id)->first();
        $data['lecture_sheets'] = LectureSheetArticle::where('topic_id', $data['lecture_sheet']->topic_id)->get();
        return view('lecture_sheet_article/lecture_sheet_article_details', $data);
    }

    public function topic_lecture_sheet_articles(Request $request)
    {
        $lecture_sheet_article_batch_id = $request->lecture_sheet_article_batch_id;
        $topic_id = $request->topic_id;

        $data['doc_info'] = Doctors::where('id', Auth::guard('doctor')->id())->first();

        $lecture_sheet_article_batch = LectureSheetArticle::where(['topic_id' => $topic_id])
            ->paginate(10);
        $data['lecture_sheet_article_batch'] = $lecture_sheet_article_batch;
        $data['lecture_sheet_article_batch_id'] = $lecture_sheet_article_batch_id;
        $data['topic'] = Topics::where('id', $topic_id)->first();
        $data['lecture_sheet_article_topics'] = LectureSheetArticleTopics::where('lecture_sheet_article_batch_id', $lecture_sheet_article_batch_id)->join('topics', 'topics.id', 'lecture_sheet_article_topics.topic_id')->pluck('topics.name', 'topics.id');
        //echo '<pre>';print_r($data['topic']);exit;
        return view('lecture_sheet_article/topic_lecture_sheet_articles', $data);
    }



    public function lecture_video()
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
