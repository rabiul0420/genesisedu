<?php

namespace App\Http\Controllers;

use App\BatchesSchedules;
use App\Exam;
use App\Exam_question;

use App\ExamBatch;
use App\ExamDiscipline;
use App\ExamFaculty;
use App\OnlineLectureAddress;
use App\OnlineLectureLink;
use App\OnlineExamCommonCode;
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
use App\ExamBatchExam;
use App\Faculty;
use App\OnlineExam;
use App\OnlineExamLink;
use App\OnlineExamTopics;
use Jenssegers\Agent\Agent;
use App\Notices;
use Session;
use Validator;

use Auth;
use Illuminate\Support\Facades\Auth as FacadesAuth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;


class OnlineExamController extends Controller
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
    public function online_exams()
    
    {
        
        $doctor_courses = DoctorsCourses::with('course', 'batch')
            ->where(['doctor_id'=>Auth::guard('doctor')->id(), 'is_trash'=>'0', 'status'=>'1'])
            ->where('payment_status', '!=' , 'No Payment')
            ->get();
        return view('online_exam/lecture_topics', compact('doctor_courses'));
    }

    public function doctor_course_online_exam($doctor_course_id)
    {
        $text = '';
        $doc_info = Doctors::where('id', Auth::guard('doctor')->id())->first();
        $doctor_course = DoctorsCourses::where(['id'=>$doctor_course_id,'is_trash'=>'0','status'=>'1'])->where('payment_status', '!=' , 'No Payment')->first();
        $doctor_course = DoctorsCourses::where(['id'=>$doctor_course_id,'is_trash'=>'0','status'=>'1'])->first();
        $data['doc_info'] = $doc_info;
        $data['doctor_course'] = $doctor_course;
        $exam_batch_link_ids=array();



        // dd( $doctor_course );

        if($exam_batch = ExamBatch::where(['year'=>$doctor_course->year,'session_id'=>$doctor_course->session_id,'exam_batch.institute_id'=>$doctor_course->institute_id,'exam_batch.course_id'=>$doctor_course->course_id,'batch_id'=>$doctor_course->batch_id])->first()){
            $exam_batch_link_ids[] = ExamBatch::where(['year'=>$doctor_course->year,'session_id'=>$doctor_course->session_id,'exam_batch.institute_id'=>$doctor_course->institute_id,'exam_batch.course_id'=>$doctor_course->course_id,'batch_id'=>$doctor_course->batch_id])->value('id');
        }

        $exam = 'exam';

//        dd( $exam_batch_link_ids );

        if($doctor_course->batch->fee_type == "Batch")
        {
            $examList = ExamBatch::join('exam_batch_exam','exam_batch_exam.exam_batch_id','exam_batch.id')
                ->join($exam,$exam.'.id','exam_batch_exam.exam_id')
                ->orderBy('exam_batch_exam.id','desc')
                ->whereNull( 'exam_batch_exam.deleted_at');


            if( $doctor_course->institute->id == AppServiceProvider::$COMBINED_INSTITUTE_ID ) {

                $faculty_name = Faculty::where('id',$doctor_course->faculty_id)->value( 'name' );
                $faculty_ids = Faculty::where('name',$faculty_name)->pluck('id');

                $bcps_subject_name = Subjects::where('id',$doctor_course->bcps_subject_id)->value( 'name' );
                $bcps_subject_ids = Subjects::where('name',$bcps_subject_name)->pluck( 'id' );

                $subject_exam_ids = ExamDiscipline::whereIn( 'subject_id', $bcps_subject_ids )
                    ->join( 'exam_assigns', 'exam_assigns.id', 'exam_discipline.exam_assign_id' )
                    ->whereNull( 'exam_assigns.deleted_at' )
                    ->where( 'exam_assigns.institute_id', AppServiceProvider::$COMBINED_INSTITUTE_ID  )
                    ->pluck( 'exam_discipline.exam_id' );

                $discipline_exam_ids = ExamFaculty::whereIn( 'faculty_id', $faculty_ids )
                    ->join( 'exam_assigns', 'exam_assigns.id', 'exam_faculties.exam_assign_id' )
                    ->whereNull( 'exam_assigns.deleted_at' )
                    ->where( 'exam_assigns.institute_id', AppServiceProvider::$COMBINED_INSTITUTE_ID  )
                    ->pluck( 'exam_faculties.exam_id' );

                $batch_exam_ids = ExamBatchExam::whereIn( 'exam_batch_id', $exam_batch_link_ids )->pluck( 'exam_id' );

                $exam_ids = $subject_exam_ids->merge( $discipline_exam_ids )->unique( );

                $examList->whereIn( 'exam.id', $batch_exam_ids )->whereIn( 'exam.id', $exam_ids );

            } else {
                $examList->whereIn( 'exam_batch.id', $exam_batch_link_ids );
            }


        } else if( $doctor_course->batch->fee_type == "Discipline_Or_Faculty" ) {


            if($doctor_course->institute->type == 1)
            {
                $faculty_name = Faculty::where('id',$doctor_course->faculty_id)->value('name');
                $faculty_ids = Faculty::where('name',$faculty_name)->pluck('id');

                $examList = ExamBatch::select($exam.'.*')->whereIn('exam_batch.id',$exam_batch_link_ids)->whereIn($exam.'_faculties.faculty_id',$faculty_ids)
                    ->where('name', 'like', '%' . $text . '%')
                    ->join('exam_batch_exam','exam_batch_exam.exam_batch_id','exam_batch.id')
                    ->join($exam,$exam.'.id','exam_batch_exam.exam_id')
                    ->join( $exam.'_faculties',$exam.'_faculties.'.$exam.'_id',$exam.'.id')
                    ->orderBy( 'exam_batch_exam.id','desc' )
                    ->whereNull( 'exam_batch_exam.deleted_at' )
                    ->whereNull( $exam.'_faculties.deleted_at' );

            } else {

                $subject_name = Subjects::where('id',$doctor_course->subject_id)->value('name');
                $subject_ids = Subjects::where('name',$subject_name)->pluck('id');

                //dd( $subject_name );

                $examList = ExamBatch::select( $exam . '.*' )->whereIn('exam_batch.id',$exam_batch_link_ids)->whereIn($exam.'_discipline.subject_id',$subject_ids)
                    ->where('name', 'like', '%' . $text . '%')
                    ->join('exam_batch_exam','exam_batch_exam.exam_batch_id','exam_batch.id')
                    ->join($exam,$exam.'.id','exam_batch_exam.exam_id')
                    ->join($exam.'_discipline',$exam.'_discipline.'.$exam.'_id',$exam.'.id')
                    ->orderBy('exam_batch_exam.id','desc')
                    ->whereNull( 'exam_batch_exam.deleted_at' )
                    ->whereNull( $exam . '_discipline.deleted_at' );
            }

        }

        //New Schedule
        //dd( $exam_batch );
         //Master Copy

        $data['exam_batch'] = $examList->groupBy('exam.id')->paginate(10);

        $doctor_courses = DoctorsCourses::where(['doctor_id'=>$doc_info->id,'is_trash'=>'0','status'=>'1'])->where('payment_status', '!=' , 'No Payment')->get();
        $doctor_courses_with_exam = array();
        foreach($doctor_courses as $key=>$doctor_course){
            if(ExamBatch::where(['year'=>$doctor_course->year,'session_id'=>$doctor_course->session_id,'exam_batch.institute_id'=>$doctor_course->institute_id,'exam_batch.course_id'=>$doctor_course->course_id,'batch_id'=>$doctor_course->batch_id])->first()){
                $doctor_courses_with_exam[] = $doctor_course;
            }
        }
        $data['doctor_courses'] = $doctor_courses_with_exam;
        $data['doctor_course_id'] = $doctor_course_id;

        return view('online_exam/lecture_topics',$data);

    }
    public function doctor_course_online_exam_ajax(Request $request,$doctor_course_id)
    {
        $text = $request->text;
        $doc_info = Doctors::where('id', Auth::guard('doctor')->id())->first();
        $doctor_course = DoctorsCourses::where(['id'=>$doctor_course_id,'is_trash'=>'0','status'=>'1'])->where('payment_status', '!=' , 'No Payment')->first();
        $data['doc_info'] = $doc_info;

        $data['doctor_course'] = $doctor_course;

        $exam_batch_link_ids=array();


        if($exam_batch = ExamBatch::where(['year'=>$doctor_course->year,'session_id'=>$doctor_course->session_id,'exam_batch.institute_id'=>$doctor_course->institute_id,'exam_batch.course_id'=>$doctor_course->course_id,'batch_id'=>$doctor_course->batch_id])->first()){
            $exam_batch_link_ids[] = ExamBatch::where(['year'=>$doctor_course->year,'session_id'=>$doctor_course->session_id,'exam_batch.institute_id'=>$doctor_course->institute_id,'exam_batch.course_id'=>$doctor_course->course_id,'batch_id'=>$doctor_course->batch_id])->value('id');
        }

        $exam = 'exam';

        if($doctor_course->batch->fee_type == "Batch")
        {
            $exam_batch = ExamBatch::whereIn('exam_batch.id',$exam_batch_link_ids)
                ->where('name', 'like', '%' . $text . '%')
                ->join('exam_batch_exam','exam_batch_exam.exam_batch_id','exam_batch.id')
                ->join($exam,$exam.'.id','exam_batch_exam.exam_id')
                ->orderBy('exam_batch_exam.id','desc')
                ->whereNull( 'exam_batch_exam.deleted_at' )
                ->paginate(10);


        }else if($doctor_course->batch->fee_type == "Discipline_Or_Faculty") {


            if($doctor_course->institute->type == 1)
            {
                $faculty_name = Faculty::where('id',$doctor_course->faculty_id)->value('name');
                $faculty_ids = Faculty::where('name',$faculty_name)->pluck('id');

                $exam_batch = ExamBatch::select($exam.'.*')->whereIn('exam_batch.id',$exam_batch_link_ids)->whereIn($exam.'_faculties.faculty_id',$faculty_ids)
                    ->where('name', 'like', '%' . $text . '%')
                    ->join('exam_batch_exam','exam_batch_exam.exam_batch_id','exam_batch.id')
                    ->join($exam,$exam.'.id','exam_batch_exam.exam_id')
                    ->join( $exam.'_faculties',$exam.'_faculties.'.$exam.'_id',$exam.'.id')
                    ->orderBy( 'exam_batch_exam.id','desc' )
                    ->whereNull( 'exam_batch_exam.deleted_at' )
                    ->whereNull( $exam.'_faculties.deleted_at' )
                    ->paginate(10);

            } else {

                $subject_name = Subjects::where('id',$doctor_course->subject_id)->value('name');
                $subject_ids = Subjects::where('name',$subject_name)->pluck('id');

                //dd( $subject_name );

                $exam_batch = ExamBatch::select( $exam . '.*' )->whereIn('exam_batch.id',$exam_batch_link_ids)->whereIn($exam.'_discipline.subject_id',$subject_ids)
                     ->where('name', 'like', '%' . $text . '%')
                    ->join('exam_batch_exam','exam_batch_exam.exam_batch_id','exam_batch.id')
                    ->join($exam,$exam.'.id','exam_batch_exam.exam_id')
                    ->join($exam.'_discipline',$exam.'_discipline.'.$exam.'_id',$exam.'.id')
                    ->orderBy('exam_batch_exam.id','desc')
                    ->whereNull( 'exam_batch_exam.deleted_at' )
                    ->whereNull( $exam . '_discipline.deleted_at' )
                    ->paginate(10);
            }

        }

        //dd( $exam_batch );

        $data['exam_batch'] = $exam_batch;

        $doctor_courses = DoctorsCourses::where(['doctor_id'=>$doc_info->id,'is_trash'=>'0','status'=>'1'])->where('payment_status', '!=' , 'No Payment')->get();
        $doctor_courses_with_exam = array();
        foreach($doctor_courses as $key=>$doctor_course){
            if(ExamBatch::where(['year'=>$doctor_course->year,'session_id'=>$doctor_course->session_id,'exam_batch.institute_id'=>$doctor_course->institute_id,'exam_batch.course_id'=>$doctor_course->course_id,'batch_id'=>$doctor_course->batch_id])->first()){
                $doctor_courses_with_exam[] = $doctor_course;
            }
        }
        $data['doctor_courses'] = $doctor_courses_with_exam;

        return view('online_exam/lecture_topics_ajax',$data);

    }

    public function online_exam($course_id,$batch_id)
    {
        $doc_info = Doctors::where('id', Auth::guard('doctor')->id())->first();
        $doctor_courses = DoctorsCourses::where(['doctor_id'=>$doc_info->id,'course_id'=>$course_id,'batch_id'=>$batch_id,'is_trash'=>'0'])->where('payment_status', '!=' , 'No Payment')->get();

        $data['doc_info'] = $doc_info;
        $data['doctor_courses'] = $doctor_courses;
        $exam_link_ids = array();
        foreach($doctor_courses as $key=>$doctor_course){

            if(OnlineExamLink::where(['year'=>$doctor_course->year,'session_id'=>$doctor_course->session_id,'online_exam_batch.institute_id'=>$doctor_course->institute_id,'online_exam_batch.course_id'=>$doctor_course->course_id,'batch_id'=>$doctor_course->batch_id])->first()){

                $exam_link_ids[] = OnlineExamLink::where(['year'=>$doctor_course->year,'session_id'=>$doctor_course->session_id,'online_exam_batch.institute_id'=>$doctor_course->institute_id,'online_exam_batch.course_id'=>$doctor_course->course_id,'batch_id'=>$doctor_course->batch_id])->value('id');

            }

        }

        //echo "<pre>";print_r($exam_link_ids);exit;

        if($doctor_courses[0]->batch->fee_type == "Batch")
        {
            $online_exam_batch = OnlineExamLink::whereIn('online_exam_batch.id',$exam_link_ids)
                ->join('online_exam_batch_online_exam','online_exam_batch_online_exam.online_exam_batch_id','online_exam_batch.id')
                ->join('online_exam','online_exam.id','online_exam_batch_online_exam.online_exam_id')
                ->paginate(10);

        }
        else if($doctor_courses[0]->batch->fee_type == "Discipline")
        {
            $online_exam_batch = OnlineExamLink::whereIn('online_exam_batch.id',$exam_link_ids)->where('online_exam_discipline.subject_id',$doctor_courses[0]->subject_id)
                ->join('online_exam_batch_online_exam','online_exam_batch_online_exam.online_exam_batch_id','online_exam_batch.id')
                ->join('online_exam','online_exam.id','online_exam_batch_online_exam.online_exam_id')
                ->join('online_exam_discipline','online_exam_discipline.online_exam_id','online_exam.id')
                //->join('batch_discipline_fees','batch_discipline_fees.batch_id','online_exam_batch.batch_id')
                ->paginate(10);

        }




        //echo "<pre>";print_r($online_exam_batch);exit;


        $data['online_exam_batch'] = $online_exam_batch;

        $doctor_courses = DoctorsCourses::where(['doctor_id'=>$doc_info->id,'is_trash'=>'0'])->where('payment_status', '!=' , 'No Payment')->get();
        $doctor_courses_with_exam = array();
        foreach($doctor_courses as $key=>$doctor_course){

            if(OnlineExamLink::where(['year'=>$doctor_course->year,'session_id'=>$doctor_course->session_id,'online_exam_batch.institute_id'=>$doctor_course->institute_id,'online_exam_batch.course_id'=>$doctor_course->course_id,'batch_id'=>$doctor_course->batch_id])->first()){
                $doctor_courses_with_exam[] = $doctor_course;
            }

        }

        $data['doctor_courses'] = $doctor_courses_with_exam;

        return view('online_exam/lecture_topics',$data);

    }

    public function lecture_topics($id)
    {
        $data['doc_info'] = Doctors::where('id', Auth::guard('doctor')->id())->first();
        $doctor_course_info = DoctorsCourses::where('id', $id)->first();
        $data['doctor_course_info'] = $doctor_course_info;
        $online_exam_batch_id = OnlineExamLink::where(['year'=>$doctor_course->year,'session_id'=>$doctor_course->session_id,'online_exam_batch.institute_id'=>$doctor_course->institute_id,'online_exam_batch.course_id'=>$doctor_course->course_id,'batch_id'=>$doctor_course->batch_id])->value('id');
        $online_exam_batch = OnlineExamLink::where(['year'=>$doctor_course->year,'session_id'=>$doctor_course->session_id,'online_exam_batch.institute_id'=>$doctor_course->institute_id,'online_exam_batch.course_id'=>$doctor_course->course_id,'batch_id'=>$doctor_course->batch_id])
            ->join('online_exam_topics','online_exam_topics.online_exam_batch_id','online_exam_batch.id')
            ->join('topics','topics.id','online_exam_topics.topic_id')
            ->join('online_exam_post','online_exam_post.topic_id','topics.id')
            ->paginate(10);
        $data['online_exam_batch'] = $online_exam_batch;
        $data['online_exam_batch_id'] = $online_exam_batch_id;
        $data['online_exam_topics'] = OnlineExamTopics::where('online_exam_batch_id',$online_exam_batch_id)->join('topics','topics.id','online_exam_topics.topic_id')->pluck('topics.name', 'topics.id');
        return view('lecture_topics',$data);
    }

    public function lecture_details($id)
    {
        $data['doc_info'] = Doctors::where('id', Auth::guard('doctor')->id())->first();
        $data['doctor_course_info'] = DoctorsCourses::where('id', $id)->first();
        $data['online_exam'] = OnlineExam::where('id',$id)->first();
        $data['online_exams'] = OnlineExam::where('topic_id',$data['online_exam']->topic_id)->get();
        return view('lecture_details',$data);
    }

    public function online_exam_details($id)
    {
        $data['doc_info'] = Doctors::where('id', Auth::guard('doctor')->id())->first();
        $data['doctor_course_info'] = DoctorsCourses::where('id', $data['doc_info']->id)->first();
        $data['link'] = OnlineExam::where('id',$id)->first();
        $agent =  new Agent();
        $data['browser'] = $agent->browser();
        return view('online_exam/lecture_details',$data);
    }

    public function topic_online_exams(Request $request)
    {
        $online_exam_batch_id = $request->online_exam_batch_id;
        $topic_id = $request->topic_id;

        $data['doc_info'] = Doctors::where('id', Auth::guard('doctor')->id())->first();

        $online_exam_batch = OnlineExam::where(['topic_id'=>$topic_id])
            ->paginate(10);
        $data['online_exam_batch'] = $online_exam_batch;
        $data['online_exam_batch_id'] = $online_exam_batch_id;
        $data['topic'] = Topics::where('id',$topic_id)->first();
        $data['online_exam_topics'] = OnlineExamTopics::where('online_exam_batch_id',$online_exam_batch_id)->join('topics','topics.id','online_exam_topics.topic_id')->pluck('topics.name', 'topics.id');
        //echo '<pre>';print_r($data['topic']);exit;
        return view('topic_online_exams',$data);
    }



    public function online_examoo()
    {

        $doc_info = Doctors::where('id', Auth::guard('doctor')->id())->first();
        $doctor_courses = DoctorsCourses::where('doctor_id',$doc_info->id)->get();

        $data['doc_info'] = $doc_info;
        $data['doctor_courses'] = $doctor_courses;
        $online_lecture_links = array();
        foreach($doctor_courses as $key=>$doctor_course){
            $exam_comm_code_ids = OnlineLectureLink::where(['year'=>$doctor_course->year,'session_id'=>$doctor_course->session_id,'institute_id'=>$doctor_course->institute_id,'course_id'=>$doctor_course->course_id,'batch_id'=>$doctor_course->batch_id])->pluck('lecture_address_id');

            foreach ($exam_comm_code_ids as $id){
                $online_lecture_links[$doctor_course->reg_no][] =  OnlineLectureAddress::select('*')->where('id',$id)->get();
            }
        }
        $data['online_lecture_links'] = $online_lecture_links;
        $data['rc'] = '';
        $data['exam_link'] = OnlineLectureAddress::select('*')->where('status', 1)->get();
        return view('lecture/online_exam', $data);

    }




















}












