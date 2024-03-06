<?php

namespace App\Http\Controllers\Admin;


use App\BatchesSchedules;
use App\BatchesSchedulesSlots;
use App\Divisions;
use App\DoctorCoursePayment;
use App\DoctorsCourses;
use App\Exam_question;
use App\InstituteAllocation;
use App\InstituteAllocationCourses;
use App\InstituteAllocationSeat;
use App\InstituteDisciplinesAllocationInstitutes;
use App\LectureSheetTopic;
use App\Exam;
use App\MentorTopic;
use App\Notice;
use App\Http\Controllers\Controller;
use App\Providers\AppServiceProvider;
use App\QuestionTopic;
use App\ReferenceCourse;
use App\ReferenceFaculty;
use App\ReferenceSession;
use App\ReferenceSubject;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Response;
use App\LectureSheet;

use App\CourseSessions;
use App\PaymentVerificationNote;
use App\Institutes;
use App\Batches;
use App\BatchesFaculties;
use App\BatchesSubjects;
use App\BatchSchedules;
use App\BatchShiftedHistory;
use App\Districts;
use App\Question;
use App\Sessions;
use App\Topics;
use App\Teacher;
use App\Upazilas;
use App\Subjects;
use App\Chapters;
use App\Doctors;
use App\WeekDays;
use App\LectureVideo;
use App\OnlineExam;
use App\Discount;

use App\Courses;
use App\CourseYear;
use App\CourseYearSession;
use App\DiscountHistory;
use App\DoctorAsk;
use App\Faculty;
use App\LectureVideoBatch;
use App\QuestionTypes;
use App\Notices;
use App\Profile_Edit_History;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Session;
use Auth;






class AjaxController extends Controller
{
    //

    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public function __construct()
    {
        //$this->middleware('auth');

    }

    public function class_course_search(Request $request){
        $course_ids = CourseYear::where('year', $request->year)->pluck('course_id');
        $courses= Courses::whereIn('id',$course_ids)->get();
        $institutes = Institutes::query()
            ->with('courses')
            ->whereHas('courses', function ($query) use ($course_ids) {
                $query->whereIn('id',$course_ids);
            })
            ->get();
        
        return view('admin.ajax.course_search',compact('courses', 'institutes'));

    }

    public function schedule_course_search(Request $request){
        $course_ids = CourseYear::where('year', $request->year)->where('course_year.status',1)->pluck('course_id');

        $institutes = Institutes::query()
            ->with([
                'courses' => function($query) use ($course_ids) {
                    $query->whereIn('id', $course_ids);
                }
            ])
            ->whereHas('courses', function ($query) use ($course_ids) {
                $query->whereIn('id', $course_ids);
            })
            ->get();
        
        return view('admin.ajax.schedule_search_courses', compact('institutes'));

    }

    public function courseSearchByYear(Request $request){
        $course_ids = CourseYear::where('year', $request->year)->where('course_year.status',1)->pluck('course_id');

        $institutes = Institutes::query()
            ->with([
                'courses' => function($query) use ($course_ids) {
                    $query->whereIn('id', $course_ids);
                }
            ])
            ->whereHas('courses', function ($query) use ($course_ids) {
                $query->whereIn('id', $course_ids);
            })
            ->get();
        
        return view('admin.ajax.course-search-by-year', compact('institutes'));
    }

    public function class_search_session(Request $request){

        $data['sessions'] = DB::table('sessions')
            ->join('course_year_session', 'sessions.id', '=', 'course_year_session.session_id')
            ->join('course_year', 'course_year.id', '=', 'course_year_session.course_year_id')
            ->where(['course_year.year'=>$request->year,'course_year.course_id'=>$request->course_id])
            ->where('course_year_session.deleted_at',null)
            ->where('course_year.deleted_at',null)
            ->pluck('sessions.name', 'sessions.id');

        return view('admin.ajax.course_sessions',$data);
    }


    public function schedule_search_session(Request $request){

        $data['sessions'] = DB::table('sessions')
            ->join('course_year_session', 'sessions.id', '=', 'course_year_session.session_id')
            ->join('course_year', 'course_year.id', '=', 'course_year_session.course_year_id')
            ->where('course_year.status',1)
            ->where(['course_year.year'=>$request->year,'course_year.course_id'=>$request->course_id])
            ->where('course_year_session.deleted_at',null)
            ->where('course_year.deleted_at',null)
            ->pluck('sessions.name', 'sessions.id');

        return view('admin.ajax.schedule_sessions',$data);
    }

    public function marked_subscription_video_update(Request $request){

                 $lecture_ids = trim($request->video_id);

                 $lecture_ids = explode (",", $lecture_ids); 

                 DB::table('lecture_video')
                 ->whereIn ('id',($lecture_ids))
                ->update(['is_show_subscription' => $request->subscription]);
    }



    public function course_class_change(Request $request)
    {

        $course = Courses::where(['id'=>$request->course_id])->first();

        if(isset($course->institute->type) && $course->institute->type == '0')
        {

            $subjects = Subjects::where('course_id',$course->id)->pluck('name', 'id');

            return view('admin.ajax.course_subject',['subjects'=>$subjects]);

        }  else {
            $course_id = $course->id;
            $bcps_subjects = new Collection( );

            $is_combined = AppServiceProvider::$COMBINED_INSTITUTE_ID == $course->institute_id;

            if( $is_combined ) {

                $bcps_subjects = Subjects::where( 'course_id', AppServiceProvider::$FCPSP1_COURSE_ID )
                    ->where( ['status' =>  1, 'show_in_combined' => 1] )
                    ->pluck( 'name', 'id' );


                $course_id = AppServiceProvider::$MPH_DIPLOMA_COURSE_ID;
            }

            $faculties = Faculty::where( 'course_id', $course_id )->where( 'status', 1 );

            if( $is_combined ) {
                $faculties->where( 'show_in_combined', 1 );
            }

            $faculties = $faculties->pluck( 'name', 'id' );

            return view('admin.ajax.courses_faculties', [
                'faculties' => $faculties,
                'combined_institute_id' => AppServiceProvider::$COMBINED_INSTITUTE_ID,
                'bcps_subjects' => $bcps_subjects
            ]);

        }

    }


    public function load_view( Request $request ){
        $view_name = $request->view;

        if( view()->exists($view_name ) ) {
            return view($view_name );
        }

        return "view {$view_name} not found!";

    }

    public function allocation_institute_discipline( Request $request ){
        $data = [];

        $data[ 'instituteAllocations' ] =  InstituteAllocation::whereIn( 'id',
                InstituteDisciplinesAllocationInstitutes::select( 'institute_id' )
                ->where( 'discipline_id', $request->dicipline_id )->whereNull( 'deleted_at' )
            )->orderBy( 'name', 'asc' )->pluck( 'name', 'id' );

        $data[ 'instituteAllocationSeat' ] = new InstituteAllocationSeat();

        return view('admin.ajax.institute_allocation_select', $data );
    }

    public function allocation_courses( Request $request ){
        $data = [];
        $data[ 'allocationCourses' ] = Courses::where([ 'status' => 1, 'institute_id' => 6 ])
            ->whereIn( 'id',
                ( InstituteAllocationCourses::select('course_id') ->where( 'allocation_id', $request->institute_allocation_id )->whereNull( 'deleted_at' ) )
            )
            ->pluck( 'name', 'id' );
        $data[ 'instituteAllocationSeat' ] = new InstituteAllocationSeat();
        return view('admin.ajax.course_allocation', $data );
    }


    public function notice_type( Request $request )
    {
        $type = $request->type;
        if($type=='I') {
            $data['doctors'] = Doctors::select(DB::raw("CONCAT(name,' - ',bmdc_no) AS full_name"),'id')->orderBy('id', 'DESC')->pluck('full_name', 'id');
            $data['type']=$type;
            return view('admin.ajax.notice_type_individual', $data);
        }

    }

    public function doctors_courses_payemnt_details( Request $request )
    {
        $doctor_course_id = $request->doctor_course_id;
        // $batch_id = $request->batch_id;
        $data['doctor_course'] = DoctorsCourses::where(['id'=>$doctor_course_id])->first();
        $doctor_id = $data['doctor_course']->doctor_id;
        $batch_id = $data['doctor_course']->batch_id;
        $data['doctor_course_payments'] = DoctorCoursePayment::where(['doctor_course_id'=>$doctor_course_id])->get();
        $data['discounts'] = Discount::where([ 'doctor_id'=> $doctor_id, 'batch_id' => $batch_id])->first();
        return view('admin.ajax.doctor_course_payment_details', $data);

    }
    public function doctors_courses_details( Request $request )
    {
        $doctor_course_id = $request->doctor_course_id;
        // $batch_id = $request->batch_id;
        $data['doctor_course'] = DoctorsCourses::where(['id'=>$doctor_course_id])->first();
        $doctor_id = $data['doctor_course']->doctor_id;
        $batch_id = $data['doctor_course']->batch_id;
        $data['doctor_course_payments'] = DoctorCoursePayment::where(['doctor_course_id'=>$doctor_course_id])->get();
        $data['discounts'] = Discount::where([ 'doctor_id'=> $doctor_id, 'batch_id' => $batch_id])->first();
        return view('admin.ajax.doctor_course_details', $data);

    }

    public function discount_edit_history( Request $request )
    {
        $discount_id = $request->discount_id;
        $data['discount_history'] = DiscountHistory::where(['discount_code_id'=>$discount_id])->get();

        return view('admin.ajax.discount_edit_history', $data);

    }
    public function profile_edit_history( Request $request )
    {
        $doctor_id = $request->doctor_id;
        $data['profile_edit_history'] = Profile_Edit_History::where(['doctor_id'=>$doctor_id])->get();
        // return  $doctor_id;

        return view('admin.ajax.profile_edit_history', $data);

    }
    public function payment_note( Request $request )
    {
        $payment_id = $request->payment_id;
        $data['payment_verificationss'] = PaymentVerificationNote::with('doctor')->where(['course_payment_id'=>$payment_id])->get();
        $data['payment_id'] = $payment_id;
        return view('admin.ajax.payment_note', $data);
    }

    public function notice_search_doctors(Request $request)
    {
        $text =  $_GET['term'];
        $text = $text['term'];

        $data = Doctors::select(DB::raw("CONCAT(name,' - ',bmdc_no) AS name_bmdc"),'id')
            ->where('name', 'like', '%'.$text.'%')
            ->orWhere('bmdc_no', 'like', '%'.$text.'%')
            ->get();
        //$data = DB::table('institution')->where('institution_type_id',$content_section_id)->where('name', 'like', $text.'%')->get();
        echo json_encode( $data);
    }

    public function notice_institute_course(Request $request)
    {
        $institute_id = $request->institute_id;
        $courses = Courses::get()->where('institute_id',$institute_id)->pluck('name', 'id');
        return view('admin.ajax.notice_institute_course',['courses'=>$courses]);
    }

    public function notice_course_batch(Request $request)
    {
        $course_id = $request->course_id;
        $batches = Batches::get()->where('course_id',$course_id)->pluck('name', 'id');
        return view('admin.ajax.notice_course_batch',['batches'=>$batches]);
    }

    public function check_bmdc_no(Request $request)
    {
        $bmdc_no = $request->bmdc_no;
        $bmdc_status = Doctors::where('bmdc_no',$bmdc_no)->first();
        if ($bmdc_status){
            $bmdc_status = 1;
            return view('admin.ajax.check_bmdc_no',['bmdc_status'=>$bmdc_status]);
        } else {
            $bmdc_status = 0;
            return view('admin.ajax.check_bmdc_no',['bmdc_status'=>$bmdc_status]);
        }
    }

    public function check_phone_no(Request $request)
    {
        $mobile_number = $request->mobile_number;
        $mobile_number = Doctors::where('mobile_number',$mobile_number)->first();

        if ($mobile_number){
            $bmdc_status = 1;
            return view('admin.ajax.check_phone_no',['bmdc_status'=>$bmdc_status]);
        } elseif(strlen($request->mobile_number) < 11) {
            $bmdc_status = 0;
            return view('admin.ajax.check_phone_no',['bmdc_status'=>$bmdc_status]);
        }
    }

    public function check_email(Request $request)
    {
        $email = $request->email;
        $email_status = Doctors::where('email',$email)->first();
        if ($email_status){
            $email_status = 1;
            return view('admin.ajax.check_email',['email_status'=>$email_status]);
        } else {
            $email_status = 0;
            return view('admin.ajax.check_email',['email_status'=>$email_status]);
        }
    }

    public function sif_only(Request $request)
    {
        if($request->sif_only==0){
            $data['question_type'] = QuestionTypes::get()->pluck('title', 'id');
            return view('admin.ajax.question_type', $data);
        }

    }



    public function question_type(Request $request)
    {
        $type_id = $request->question_type;
        $type_id = QuestionTypes::get()->where('id',$type_id)->pluck('subject_name', 'id');
        return view('admin.ajax.book_subject',[ 'subjects' => $subjects ]);
    }



    public function faculty_subjects_in_admission(Request $request)
    {
        $faculty_id = $request->faculty_id;
        $is_combined = $request->institute_id == AppServiceProvider::$COMBINED_INSTITUTE_ID ? 'yes':'no';


        $subjects = Subjects::where('faculty_id',$faculty_id)
            ->pluck('name', 'id');
        return view('ajax.faculty_subjects_in_admission',[ 'subjects'=>$subjects, 'is_combined' => $is_combined ]);
    }



    public function course_changed(Request $request)
    {
        // return $dd = CourseYear::where('status',1)->get();

        $course = Courses::where(['id'=>$request->course_id])->first();

        if(isset($course->institute->type) && $course->institute->type == '0')
        {

            $sessions = Sessions::join('course_year_session','course_year_session.session_id','sessions.id')
                ->join( 'course_year', 'course_year.id', 'course_year_session.course_year_id' )
                ->where('course_year.deleted_at',NULL)
                ->where('course_year_session.deleted_at',NULL)
                ->where('course_id',$request->course_id)
                // ->where('show_admission_form','yes')
                ->where('course_year.status',1)
                ->pluck('name',  'sessions.id');     
                
            $subjects = Subjects::where('course_id',$course->id)->where('status' , 1)->pluck('name', 'id');
    
            return view('admin.ajax.course_sessions_subjects',['subjects'=>$subjects,'sessions'=>$sessions]);

        }  else {
            $course_id = $course->id;
            $bcps_subjects = new Collection( );

            $is_combined = AppServiceProvider::$COMBINED_INSTITUTE_ID == $course->institute_id;

            if( $is_combined ) {

                $bcps_subjects = Subjects::where( 'course_id', AppServiceProvider::$FCPSP1_COURSE_ID )
                    ->where( ['status' =>  1, 'show_in_combined' => 1] )
                    ->pluck( 'name', 'id' );


                $course_id = AppServiceProvider::$MPH_DIPLOMA_COURSE_ID;
            }

            $faculties = Faculty::where( 'course_id', $course_id )->where( 'status', 1 );

            if( $is_combined ) {
                $faculties->where( 'show_in_combined', 1 );
            }

            $faculties = $faculties->pluck( 'name', 'id' );
      
            $sessions = Sessions::join('course_year_session','course_year_session.session_id','sessions.id')
                ->join( 'course_year', 'course_year.id', 'course_year_session.course_year_id')
                ->where('course_year.deleted_at',NULL)
                ->where('course_year_session.deleted_at',NULL)
                ->where('course_id',$request->course_id)
                // ->where('show_admission_form','yes')
                ->where('course_year.status',1)
                ->pluck('name',  'sessions.id');

            return view('admin.ajax.course_sessions_faculties', [
                'faculties' => $faculties,
                'sessions' => $sessions,
                'combined_institute_id' => AppServiceProvider::$COMBINED_INSTITUTE_ID,
                'bcps_subjects' => $bcps_subjects
            ]);

        }

    }

    
    public function courses_branches_batches(Request $request)
    {


        $institute_id = $request->institute_id;
        $course_id = $request->course_id;
        $branch_id = $request->branch_id;
        $faculty_id =  $request->faculty_id ?? null;
        $subject_id = $request->subject_id ?? null;
        $session_id = $request->session_id ?? null;

        $batches = Batches::where([
            'institute_id'=>$institute_id,
            'course_id'=>$course_id,
            'branch_id'=>$branch_id,
            'session_id' => $session_id,
            'status'=>'1'

        ]);

        if( $request->is_combined != 'yes' ) {
            if( $faculty_id )
                $batches->whereIn( 'id', BatchesFaculties::select(['batch_id'])->where( 'faculty_id', $faculty_id )->whereNull('batches_faculties.deleted_at') );

            if( $subject_id )
                $batches->whereIn( 'id', BatchesSubjects::select(['batch_id'])->where( 'subject_id', $subject_id )->whereNull('batches_subjects.deleted_at') );
        }

        return  json_encode(array('batches'=>view('ajax.batches',['batches'=> $batches->pluck('name', 'id') ])->render(),), JSON_FORCE_OBJECT); 
    }

    public function batch_details(Request $request)
    {

        $course_id = $request->course_id;
        $batch_id = $request->batch_id;

        $batch = Batches::where(['course_id'=>$course_id,'id'=>$batch_id])->first();
        //echo '<pre>';print_r($lecture_sheets);exit;
        return  json_encode(array('batch_details'=>view('ajax.batch_details',['batch'=>$batch])->render(),), JSON_FORCE_OBJECT);

    }

    public function registration_no(Request $request)
    {

        $YEAR = Batches::where('id',$request->batch_id)->value('year');
        $year = substr( $YEAR, -2);

        $session = Sessions::where('id',$request->session_id)->pluck('session_code');
        $capacity = Batches::where('id',$request->batch_id)->where('year',$YEAR)->value('capacity');

        $message = '';

        $reg_no_first_part = $year.$session[0];
        // $doctor_course = DoctorsCourses::where(['year'=> $YEAR ,'session_id'=>$request->session_id,'is_trash'=>'0'])->orderBy('reg_no_last_part_int','desc')->first();
        $doctor_course = DoctorsCourses::where(['reg_no_first_part'=> $reg_no_first_part ,'is_trash'=>'0'])->orderBy('reg_no_last_part_int','desc')->first();
        $reg_no_last_part = (isset($doctor_course->reg_no_last_part_int))?str_pad($doctor_course->reg_no_last_part_int+1,5,"0",STR_PAD_LEFT):str_pad(1,5,"0",STR_PAD_LEFT);

        $count_batch=DoctorsCourses::where(['year'=> $YEAR ,'session_id'=>$request->session_id,'batch_id'=>$request->batch_id,'course_id'=>$request->course_id,'is_trash'=>'0'])->count();

        if ($count_batch >= $capacity){
            $message = '<span style="color:red;">Dear Dr. , The batch you tried is filled up... please try another batch !!!</span>';
        }
        return  json_encode(array(
            'reg_no_first_part'=>$reg_no_first_part,
            'reg_no_last_part'=>$reg_no_last_part,
            'message'=>$message,
            'is_lecture_sheet'=>Batches::where('id',$request->batch_id)->value('is_show_lecture_sheet_fee'),
        ));
    }

    public function course_subject(Request $request)
    {
        $course_id = $request->course_id;
        $subject = Subjects::get()->where('course_id',$course_id)->pluck('name', 'id');
        return view('admin.ajax.faculty_subject',['subject'=>$subject]);
    }

    public function course_subjects(Request $request)
    {
        $course_id = $request->course_id;
        $subjects = Subjects::get()->where('course_id',$course_id)->pluck('name', 'id');
        return view('admin.ajax.subjects',['subjects'=>$subjects]);
    }

    public function course_topics(Request $request)
    {
        $course_id = $request->course_id;
        $topics = Topics::get()->where('course_id',$course_id)->pluck('name', 'id');
        return view('admin.ajax.course_topics',['topics'=>$topics]);
    }

    public function course_topic(Request $request)
    {
        $course_id = $request->course_id;
        $topics = Topics::get()->where('course_id',$course_id)->pluck('name', 'id');
        return view('admin.ajax.topics',['topics'=>$topics]);
    }

    public function course_faculty(Request $request)
    {
        if(Session('institute_type')) {
            $course_id = $request->course_id;
            $faculty = Faculty::get()->where('course_id', $course_id)->pluck('name', 'id');
            return view('admin.ajax.course_faculty', ['faculty' => $faculty]);
        }
    }


    /*   for course sessions  */
    public function course_sessions(Request $request)
    {



        $sessions = Sessions::join('course_year_session','course_year_session.session_id','sessions.id')
                ->join( 'course_year', 'course_year.id', 'course_year_session.course_year_id' )
                 ->where('course_year.year',$request->year)
                ->where('course_year.status',1)
                ->where('course_year.deleted_at',NULL)
                ->where('course_year_session.deleted_at',NULL)
                ->where('course_id',$request->course_id)
                // ->where('show_admission_form','yes')
                ->pluck('name',  'sessions.id');

                return view('admin.ajax.batch_session',[
                    'sessions'=>$sessions, 
                ]);

    }   

    public function faculty_subject(Request $request)
    {
        $faculty_id = $request->faculty_id;
        $subject = Subjects::get()->where('faculty_id',$faculty_id)->pluck('name', 'id');
        return view('admin.ajax.faculty_subject',['subject'=>$subject]);
    }

    public function institutes_courses(Request $request)
    {
        $institute_id = $request->institute_id;

        $institute_type = Institutes::where('id',$institute_id)->first()->type;
        Session(['institute_type'=> $institute_type]);
        $url  = ($institute_type)?'courses-faculties':'courses-subjects';

        $courses = Courses::get()->where('institute_id',$institute_id)->where('status',1)->pluck('name', 'id');
        return view('admin.ajax.institutes_courses',['courses'=>$courses,'url'=>$url]);
    }

    public function institute_course(Request $request)
    {
        $institute_id = $request->institute_id;
        $institute_type = Institutes::where('id',$institute_id)->first()->type;
        Session(['institute_type'=> $institute_type]);
        $url  = ($institute_type)?'course-faculty':'course-subject';
        $course = Courses::get()->where('institute_id',$institute_id)->pluck('name', 'id');
        return view('admin.ajax.institute_course',['course'=>$course,'url'=>$url]);
    }

    public function institute_courses(Request $request)
    {
        $institute_id = $request->institute_id;

        $institute_type = Institutes::where('id',$institute_id)->first()->type;
        Session(['institute_type'=> $institute_type]);
        $url  = ($institute_type)?'courses-faculties-batches':'courses-subjects-batches';

        $courses = Courses::get()->where('institute_id',$institute_id)->where('status',1)->pluck('name', 'id');
        return view('admin.ajax.institute_courses',['courses'=>$courses,'url'=>$url]);
    }

    public function institute_courses_in_online_exams(Request $request)
    {
        $institute_id = $request->institute_id;

        $institute_type = Institutes::where('id',$institute_id)->first()->type;
        Session(['institute_type'=> $institute_type]);
        $url  = ($institute_type)?'courses-faculties-batches':'courses-subjects-batches';

        $courses = Courses::get()->where('institute_id',$institute_id)->pluck('name', 'id');
        return view('admin.ajax.institute_courses_in_online_exams',['courses'=>$courses,'url'=>$url]);
    }

    public function branch_institute_courses(Request $request)
    {
        $institute_id = $request->institute_id;

        $institute_type = Institutes::where('id',$institute_id)->first()->type;
        Session(['institute_type'=> $institute_type]);
        $url  = ($institute_type)?'branches-courses-faculties-batches':'branches-courses-subjects-batches';

        $courses = Courses::get()->where('institute_id',$institute_id)->pluck('name', 'id');
        return view('admin.ajax.institute_courses',['courses'=>$courses,'url'=>$url]);
    }

    public function institute_courses_for_topics_batches(Request $request)
    {
        $institute_id = $request->institute_id;

        $institute_type = Institutes::where('id',$institute_id)->first()->type;
        Session(['institute_type'=> $institute_type]);
        $url  = ($institute_type)?'courses-faculties-topics-batches':'courses-subjects-topics-batches';

        $courses = Courses::get()->where('institute_id',$institute_id)->pluck('name', 'id');
        return view('admin.ajax.institute_courses',['courses'=>$courses,'url'=>$url]);
    }

    public function institute_courses_for_lectures_topics_batches(Request $request)
    {
        $institute_id = $request->institute_id;

        $institute_type = Institutes::where('id',$institute_id)->first()->type;
        Session(['institute_type'=> $institute_type]);
        $url  = ($institute_type)?'courses-faculties-topics-batches-lectures':'courses-subjects-topics-batches-lectures';

        $courses = Courses::get()->where('institute_id',$institute_id)->pluck('name', 'id');
        return view('admin.ajax.institute_courses',['courses'=>$courses,'url'=>$url]);
    }

    public function institute_courses_for_lectures_videos(Request $request)
    {
        $institute_id = $request->institute_id;

        $institute_type = Institutes::where('id',$institute_id)->first()->type;
        Session(['institute_type'=> $institute_type]);
        $url  = ($institute_type)?'courses-faculties-batches-lectures-videos':'courses-subjects-batches-lectures-videos';

        $courses = Courses::get()->where('institute_id',$institute_id)->pluck('name', 'id');
        return view('admin.ajax.institute_courses',['courses'=>$courses,'url'=>$url]);
    }

    public function courses_faculties(Request $request)
    {
        $course_id = $request->course_id;
        $faculties = Faculty::get()->where('course_id',$course_id)->pluck('name', 'id');
        $batches = Batches::get()->where('course_id',$course_id)->pluck('name', 'id');
        return view('admin.ajax.courses_faculties',['faculties'=>$faculties,'batches'=>$batches]);
    }


    public function courses_subjects(Request $request)
    {
        $course_id = $request->course_id;
        $subjects = Subjects::get()->where('course_id',$course_id)->pluck('name', 'id');
        $batches = Batches::get()->where('course_id',$course_id)->pluck('name', 'id');
        return view('admin.ajax.faculties_subjects',['subjects'=>$subjects,'batches'=>$batches]);
    }

    public function courses_batches_multiple(Request $request){
        return $this->courses_batches( $request, true );
    }

    public function courses_batches( Request $request, $multiple =  false )
    {
        $course_id = $request->course_id;
        $batches = Batches::get()->where( 'course_id', $course_id )->pluck( 'name', 'id' );
        return $multiple ? view('admin.ajax.courses_batches_multiple', [ 'batches'=>$batches ] ):
            view('admin.ajax.courses_batches', [ 'batches' => $batches ] );
    }

    public function faculties_subjects(Request $request)
    {
        $faculty_id = $request->faculty_id;
        $subjects = Subjects::get()->where('faculty_id',$faculty_id)->pluck('name', 'id');
        return view('admin.ajax.faculties_subjects',['subjects'=>$subjects]);
    }

    public function faculty_subjects( Request $request )
    {
        $institute_id = $request->institute_id;
        $course_id = $request->course_id;
        $faculty_id = $request->faculty_id;
        $subjects = Subjects::where('institute_id',$institute_id)
            ->where('course_id',$course_id)
            ->where('faculty_id',$faculty_id)
            ->pluck('name', 'id');
        return view('admin.ajax.subjects',[ 'subjects' => $subjects ]);
    }

    public function batch_subjects(Request $request)
    {
        $batch = Batches::where('id',$request->batch_id)->first();

        $subjects = Subjects::where('institute_id',$batch->institute_id)
            ->where('course_id',$batch->course_id)
            ->pluck('name', 'id');

        return  json_encode(array('subjects'=>view('admin.ajax.subjects',['subjects'=>$subjects])->render(),), JSON_FORCE_OBJECT);

    }

    public function batch_faculties(Request $request)
    {
        $batch = Batches::where('id',$request->batch_id)->first();

        $faculties = Faculty::where('institute_id',$batch->institute_id)
            ->where('course_id',$batch->course_id)
            ->pluck('name', 'id');

        return  json_encode(array('faculties'=>view('admin.ajax.faculties',['faculties'=>$faculties])->render(),), JSON_FORCE_OBJECT);

    }

    public function courses_faculties_batches(Request $request)
    {
        $institute_id = $request->institute_id;
        $course_id = $request->course_id;

        $faculties = Faculty::get()->where('institute_id',$institute_id)
            ->where('course_id',$course_id)
            ->pluck('name', 'id');

        $batches = Batches::get()->where('institute_id',$institute_id)
            ->where('course_id',$course_id)
            ->pluck('name', 'id');

        //return view('admin.ajax.courses_faculties_batches',['faculties'=>$faculties,'batches'=>$batches]);
        return  json_encode(array('faculties'=>view('admin.ajax.faculties',['faculties'=>$faculties,'batches'=>$batches])->render(),'batches'=>view('admin.ajax.courses_batches',['faculties'=>$faculties,'batches'=>$batches])->render(),), JSON_FORCE_OBJECT);


    }

    public function courses_subjects_batches(Request $request)
    {
        $institute_id = $request->institute_id;
        $course_id = $request->course_id;

        $subjects = Subjects::get()->where('institute_id',$institute_id)
            ->where('course_id',$course_id)
            ->pluck('name', 'id');

        $batches = Batches::get()->where('institute_id',$institute_id)
            ->where('course_id',$course_id)
            ->pluck('name', 'id');


        return  json_encode(array('subjects'=>view('admin.ajax.subjects',['subjects'=>$subjects,'batches'=>$batches])->render(),'batches'=>view('admin.ajax.courses_batches',['subjects'=>$subjects,'batches'=>$batches])->render(),), JSON_FORCE_OBJECT);

    }

    public function branches_courses_faculties_batches(Request $request)
    {
        $branch_id = $request->branch_id;
        $institute_id = $request->institute_id;
        $course_id = $request->course_id;

        $faculties = Faculty::get()->where('institute_id',$institute_id)
            ->where('course_id',$course_id)
            ->pluck('name', 'id');

        $batches = Batches::get()->where('institute_id',$institute_id)
            ->where('course_id',$course_id)
            ->where('branch_id',$branch_id)
            ->pluck('name', 'id');

        //return view('admin.ajax.courses_faculties_batches',['faculties'=>$faculties,'batches'=>$batches]);
        return  json_encode(array('faculties'=>view('admin.ajax.faculties',['faculties'=>$faculties,'batches'=>$batches])->render(),
            'batches'=>view('admin.ajax.courses_batches',['faculties'=>$faculties,'batches'=>$batches])->render(),), JSON_FORCE_OBJECT);


    }

    public function branches_courses_subjects_batches(Request $request)
    {
        $branch_id = $request->branch_id;
        $institute_id = $request->institute_id;
        $course_id = $request->course_id;

        $subjects = Subjects::get()->where('institute_id',$institute_id)
            ->where('course_id',$course_id)
            ->pluck('name', 'id');

        $batches = Batches::get()->where('institute_id',$institute_id)
            ->where('course_id',$course_id)
            ->where('branch_id',$branch_id)
            ->pluck('name', 'id');


        return  json_encode(array('subjects'=>view('admin.ajax.subjects',['subjects'=>$subjects,'batches'=>$batches])->render(),'batches'=>view('admin.ajax.courses_batches',['subjects'=>$subjects,'batches'=>$batches])->render(),), JSON_FORCE_OBJECT);

    }

    public function courses_faculties_topics_batches(Request $request)
    {
        $branch_id = $request->branch_id;
        $institute_id = $request->institute_id;
        $course_id = $request->course_id;

        $faculties = Faculty::get()->where('institute_id',$institute_id)
            ->where('course_id',$course_id)
            ->pluck('name', 'id');

        $topics = Topics::get()->where('course_id',$course_id)->pluck('name', 'id');

        $batches = Batches::get()->where('institute_id',$institute_id)
            ->where('course_id',$course_id)
            ->where('branch_id',$branch_id)
            ->pluck('name', 'id');


        //return view('admin.ajax.courses_faculties_batches',['faculties'=>$faculties,'batches'=>$batches]);
        return  json_encode(array('faculties'=>view('admin.ajax.faculties',['faculties'=>$faculties,'batches'=>$batches])->render(),'topics'=>view('admin.ajax.topics',['topics'=>$topics])->render(),'batches'=>view('admin.ajax.courses_batches',['faculties'=>$faculties,'batches'=>$batches])->render(),), JSON_FORCE_OBJECT);


    }

    public function courses_subjects_topics_batches(Request $request)
    {
        $branch_id = $request->branch_id;
        $institute_id = $request->institute_id;
        $course_id = $request->course_id;

        $subjects = Subjects::get()->where('institute_id',$institute_id)
            ->where('course_id',$course_id)
            ->pluck('name', 'id');

        $topics = Topics::get()->where('course_id',$course_id)->pluck('name', 'id');

        $batches = Batches::get()->where('institute_id',$institute_id)
            ->where('course_id',$course_id)
            ->where('branch_id',$branch_id)
            ->pluck('name', 'id');


        return  json_encode(array('subjects'=>view('admin.ajax.subjects',['subjects'=>$subjects,'batches'=>$batches])->render(),'topics'=>view('admin.ajax.topics',['topics'=>$topics])->render(),'batches'=>view('admin.ajax.courses_batches',['subjects'=>$subjects,'batches'=>$batches])->render(),), JSON_FORCE_OBJECT);

    }

    public function courses_faculties_topics_batches_lectures(Request $request)
    {
        $branch_id = $request->branch_id;
        $institute_id = $request->institute_id;
        $course_id = $request->course_id;

        $faculties = Faculty::get()->where('institute_id',$institute_id)
            ->where('course_id',$course_id)
            ->pluck('name', 'id');

        $topics = Topics::get()->where('course_id',$course_id)->pluck('name', 'id');

        $batches = Batches::get()->where('institute_id',$institute_id)
            ->where('course_id',$course_id)
            ->where('branch_id',$branch_id)
            ->pluck('name', 'id');


        //return view('admin.ajax.courses_faculties_batches',['faculties'=>$faculties,'batches'=>$batches]);
        return  json_encode(array('faculties'=>view('admin.ajax.faculties',['faculties'=>$faculties,'batches'=>$batches])->render(),'topics'=>view('admin.ajax.topics_for_lecture_sheet_batches',['topics'=>$topics])->render(),'batches'=>view('admin.ajax.courses_batches',['faculties'=>$faculties,'batches'=>$batches])->render(),), JSON_FORCE_OBJECT);


    }

    public function lecture_videos(Request $request)
    {
        $branch_id = $request->branch_id;
        $institute_id = $request->institute_id;
        $course_id = $request->course_id;

        $institute_type = Institutes::where('id',$institute_id)->first()->type;
        if($institute_type)
        {
            $faculties = Faculty::where(['institute_id'=>$institute_id,'course_id'=>$course_id])->pluck('name', 'id');

            //$lecture_videos = LectureVideo::where(['institute_id'=>$institute_id,'course_id'=>$course_id])->pluck('name', 'id');

            $lecture_videos = LectureVideo::pluck('name', 'id');

            $batches = Batches::where(['institute_id'=>$institute_id,'course_id'=>$course_id,'branch_id'=>$branch_id])->where('course_id',$course_id)->pluck('name', 'id');

            return  json_encode(array('faculties'=>view('admin.ajax.faculties',['faculties'=>$faculties])->render(),'lecture_videos'=>view('admin.ajax.lecture_videos',['lecture_videos'=>$lecture_videos])->render(),'batches'=>view('admin.ajax.courses_batches',['batches'=>$batches])->render(),), JSON_FORCE_OBJECT);

        }
        else
        {
            $subjects = Subjects::where(['institute_id'=>$institute_id,'course_id'=>$course_id])->pluck('name', 'id');

            //$lecture_videos = LectureVideo::where(['institute_id'=>$institute_id,'course_id'=>$course_id])->pluck('name', 'id');

            $lecture_videos = LectureVideo::pluck('name', 'id');

            $batches = Batches::where(['institute_id'=>$institute_id,'course_id'=>$course_id,'branch_id'=>$branch_id])->where('course_id',$course_id)->pluck('name', 'id');

            return  json_encode(array('subjects'=>view('admin.ajax.subjects',['subjects'=>$subjects])->render(),'lecture_videos'=>view('admin.ajax.lecture_videos',['lecture_videos'=>$lecture_videos])->render(),'batches'=>view('admin.ajax.courses_batches',['batches'=>$batches])->render(),), JSON_FORCE_OBJECT);


        }

    }


    public function multiple_batch_changee_in_schedule( $batch_ids, Request $request, Institutes $institute ){

        $institute_id = $request->institute_id;
        $course_id = $request->course_id;
        $batches = Batches::whereIn( 'id', $batch_ids )->get();

        $faculties = null;
        $subjects = null;

        foreach ( $batches as $batch ) {
            $data = $this->schedule_batch_faculties_subjects( $batch, $request );

            if( $data[ 'faculties' ] ) {
                $faculties = !$faculties ? new Collection( ) : $faculties;
                $faculties =  $faculties->merge( $data[ 'faculties' ] );
            }

            if( $data[ 'subjects' ] ) {
                $subjects = !$subjects ? new Collection( ) : $subjects;
                $subjects =  $subjects->merge( $data[ 'subjects' ] );
            }
        }

        return  json_encode(array(
                'faculties' => view('admin.ajax.faculties_multiple',    [   'faculties'=> $faculties, 'faculty_label' => $institute->faculty_label() ]  )->render(),
                'subjects'=>view('admin.ajax.subjects_multiple',        [   'subjects'=> $subjects, 'discipline_label' => $institute->discipline_label() ]  )->render()
            ), JSON_FORCE_OBJECT);

    }

    private function schedule_batch_faculties_subjects( Batches  $batch, Request  $request ){
        $institute_id = $request->institute_id;
        $course_id = $request->course_id;
        $institute = Institutes::where('id',$institute_id)->first();
        if($batch->fee_type == "Discipline_Or_Faculty" || $institute->id == AppServiceProvider::$COMBINED_INSTITUTE_ID)
        {

            $faculties = null;

            if( $institute->type ==  1)
            {
                $faculties = Faculty::where(['institute_id'=>$institute_id,'course_id'=>$course_id])->pluck('name', 'id');
            }

            $subjects = null;

            if($institute->type == 0 || $institute->id == AppServiceProvider::$COMBINED_INSTITUTE_ID )
            {

                $subjects = Subjects::where(['institute_id'=>$institute_id,'course_id'=>$course_id])->pluck('name', 'id');

                if( $institute->id == AppServiceProvider::$COMBINED_INSTITUTE_ID ) {

                    $course = Courses::find( $course_id );

                    $faculties = $course->combined_faculties( )->pluck('name', 'id');
                    $subjects = $course->combined_disciplines( )->pluck('name', 'id');

                }
            }

            return [
                'subjects' => $subjects,
                'faculties' => $faculties,
            ];

        }

        return [
            'subjects' => null,
            'faculties' => null,
        ];
    }

    public function batch_changed_in_schedule(Request $request)
    {
        $institute_id = $request->institute_id;
        $course_id = $request->course_id;
        $batch_id = $request->batch_id;
        $institute = Institutes::where('id',$institute_id)->first();
        $is_combined = AppServiceProvider::$COMBINED_INSTITUTE_ID == $institute->id;


        if( is_array( $batch_id ) &&  count( $batch_id ) > 1 )
            return $this->multiple_batch_changee_in_schedule( $batch_id, $request, $institute  );
        else
            $batch_id = $batch_id[0] ?? $batch_id;

        $batch = Batches::where('id',$batch_id)->first();

        if( $batch && $batch instanceof Batches) {


            $data = $this->schedule_batch_faculties_subjects( $batch, $request );

            if( $data['subjects'] === null && $data['faculties'] === null )
                return  json_encode( array('batch'=>'batch',), JSON_FORCE_OBJECT );

            $sub = $data[ 'subjects' ] ? view('admin.ajax.subjects_multiple',[ 'subjects' => $data[ 'subjects' ] , 'discipline_label' => $institute->discipline_label() ])->render():'';
            $fac = $data[ 'faculties' ] ? view('admin.ajax.faculties_multiple',[ 'faculties' => $data[ 'faculties' ], 'faculty_label' => $institute->faculty_label() ])->render():'';

            return  json_encode([

                'faculties' => $fac,
                'subjects'  => $sub

            ], JSON_FORCE_OBJECT);


/*
            if($batch->fee_type == "Discipline_Or_Faculty" || $institute->id == AppServiceProvider::$COMBINED_INSTITUTE_ID)
            {

                $faculties = null;

                if( $institute->type ==  1)
                {
                    $faculties = Faculty::where(['institute_id'=>$institute_id,'course_id'=>$course_id])->pluck('name', 'id');
                }

                $subjects = null;

                if($institute->type == 0 || $institute->id == AppServiceProvider::$COMBINED_INSTITUTE_ID )
                {
                    $subjects = Subjects::where(['institute_id'=>$institute_id,'course_id'=>$course_id])->pluck('name', 'id');
                }

                $sub = $subjects ? view('admin.ajax.subjects_multiple',['subjects'=>$subjects])->render():'';
                $fac = $faculties ? view('admin.ajax.faculties_multiple',['faculties'=>$faculties])->render():'';

                return  json_encode([

                        'faculties' => $fac,
                        'subjects'  => $sub

                ], JSON_FORCE_OBJECT);

            }
            else
            {
                return  json_encode(array('batch'=>'batch',), JSON_FORCE_OBJECT);
            }
*/
        }

         return  json_encode( [], JSON_FORCE_OBJECT );
    }

    public function faculty_changed_in_schedule(Request $request)
    {

        $institute_id = $request->institute_id;
        $course_id = $request->course_id;
        $faculty_id = $request->faculty_id;
        $subjects = Subjects::where('institute_id',$institute_id)
            ->where('course_id',$course_id)
            ->where('faculty_id',$faculty_id)
            ->pluck('name', 'id');

        return  json_encode(array('subjects'=>view('admin.ajax.subjects_multiple',['subjects'=>$subjects])->render(),), JSON_FORCE_OBJECT);

    }

    public function online_exams(Request $request)
    {
        $branch_id = $request->branch_id;
        $institute_id = $request->institute_id;
        $course_id = $request->course_id;

        $institute_type = Institutes::where('id',$institute_id)->first()->type;
        if($institute_type)
        {
            $faculties = Faculty::where(['institute_id'=>$institute_id,'course_id'=>$course_id])->pluck('name', 'id');

            //$lecture_videos = LectureVideo::where(['institute_id'=>$institute_id,'course_id'=>$course_id])->pluck('name', 'id');

            if(isset($request->online_exam_batch_id) && $request->online_exam_batch_id <= 200 )
            {
                $online_exams = OnlineExam::pluck('name', 'id');
            }
            else
            {
                $online_exams = Exam::where(['institute_id'=>$institute_id,'course_id'=>$course_id,'sif_only'=>'No'])->pluck('name', 'id');
            }

            $online_exams = OnlineExam::pluck('name', 'id');

            $batches = Batches::where(['institute_id'=>$institute_id,'course_id'=>$course_id,'branch_id'=>$branch_id])->where('course_id',$course_id)->pluck('name', 'id');

            return  json_encode(array('faculties'=>view('admin.ajax.faculties',['faculties'=>$faculties])->render(),'online_exams'=>view('admin.ajax.online_exams',['online_exams'=>$online_exams])->render(),'batches'=>view('admin.ajax.courses_batches',['batches'=>$batches])->render(),), JSON_FORCE_OBJECT);

        }
        else
        {
            $subjects = Subjects::where(['institute_id'=>$institute_id,'course_id'=>$course_id])->pluck('name', 'id');

            //$lecture_videos = LectureVideo::where(['institute_id'=>$institute_id,'course_id'=>$course_id])->pluck('name', 'id');

            //$online_exams = OnlineExam::pluck('name', 'id');
            if(isset($request->online_exam_batch_id) && $request->online_exam_batch_id <= 200 )
            {
                $online_exams = OnlineExam::pluck('name', 'id');
            }
            else
            {
                $online_exams = Exam::where(['institute_id'=>$institute_id,'course_id'=>$course_id,'sif_only'=>'No'])->pluck('name', 'id');
            }

            $online_exams = OnlineExam::pluck('name', 'id');

            $batches = Batches::where(['institute_id'=>$institute_id,'course_id'=>$course_id,'branch_id'=>$branch_id])->where('course_id',$course_id)->pluck('name', 'id');

            return  json_encode(array('subjects'=>view('admin.ajax.subjects',['subjects'=>$subjects])->render(),'online_exams'=>view('admin.ajax.online_exams',['online_exams'=>$online_exams])->render(),'batches'=>view('admin.ajax.courses_batches',['batches'=>$batches])->render(),), JSON_FORCE_OBJECT);


        }

    }

    public function ajax_lecture_sheet_topics(Request $request)
    {
        $branch_id = $request->branch_id;
        $institute_id = $request->institute_id;
        $course_id = $request->course_id;
        $institute_type = Institutes::where('id',$institute_id)->first()->type;
        if($institute_type)
        {
            $faculties = Faculty::where(['institute_id'=>$institute_id,'course_id'=>$course_id])->pluck('name', 'id');

            //$lecture_sheet_topics = LectureSheetTopic::where(['institute_id'=>$institute_id,'course_id'=>$course_id])->join('lecture_sheet_topic_assign','lecture_sheet_topic_assign.lecture_sheet_topic_id','=','lecture_sheet_topic.id')->pluck('name', 'lecture_sheet_topic.id');
            $lecture_sheet_topics = LectureSheetTopic::where(['institute_id'=>$institute_id])->join('lecture_sheet_topic_assign','lecture_sheet_topic_assign.lecture_sheet_topic_id','=','lecture_sheet_topic.id')->pluck('name', 'lecture_sheet_topic.id');
            $batches = Batches::where(['institute_id'=>$institute_id,'course_id'=>$course_id,'branch_id'=>$branch_id])->where('course_id',$course_id)->pluck('name', 'id');

            return  json_encode(array('faculties'=>view('admin.ajax.faculties',['faculties'=>$faculties])->render(),'lecture_sheet_topics'=>view('admin.ajax.lecture_sheet_topics_multiple_not_required',['lecture_sheet_topics'=>$lecture_sheet_topics])->render(),'batches'=>view('admin.ajax.courses_batches',['batches'=>$batches])->render(),), JSON_FORCE_OBJECT);

        }
        else
        {
            $subjects = Subjects::where(['institute_id'=>$institute_id,'course_id'=>$course_id])->pluck('name', 'id');

            //$lecture_sheet_topics = LectureSheetTopic::where(['institute_id'=>$institute_id,'course_id'=>$course_id])->join('lecture_sheet_topic_assign','lecture_sheet_topic_assign.lecture_sheet_topic_id','=','lecture_sheet_topic.id')->pluck('name', 'lecture_sheet_topic.id');
            $lecture_sheet_topics = LectureSheetTopic::where(['institute_id'=>$institute_id])->join('lecture_sheet_topic_assign','lecture_sheet_topic_assign.lecture_sheet_topic_id','=','lecture_sheet_topic.id')->pluck('name', 'lecture_sheet_topic.id');

            $batches = Batches::where(['institute_id'=>$institute_id,'course_id'=>$course_id,'branch_id'=>$branch_id])->where('course_id',$course_id)->pluck('name', 'id');

            return  json_encode(array('subjects'=>view('admin.ajax.subjects',['subjects'=>$subjects])->render(),'lecture_sheet_topics'=>view('admin.ajax.lecture_sheet_topics_multiple_not_required',['lecture_sheet_topics'=>$lecture_sheet_topics])->render(),'batches'=>view('admin.ajax.courses_batches',['batches'=>$batches])->render(),), JSON_FORCE_OBJECT);


        }

    }

    public function course_branch_changed_in_exam_batch(Request $request)
    {
        $branch_id = $request->branch_id;
        $institute_id = $request->institute_id;
        $course_id = $request->course_id;

        $institute_type = Institutes::where('id',$institute_id)->first()->type;

        if( $institute_type )
        {
            $faculties = Faculty::where(['institute_id'=>$institute_id,'course_id'=>$course_id])->pluck('name', 'id');

            $online_exams = Exam::where(['institute_id'=>$institute_id,'course_id'=>$course_id,'sif_only'=>'No'])->pluck('name', 'id');

            $batches = Batches::where(['institute_id'=>$institute_id,'course_id'=>$course_id,'branch_id'=>$branch_id])
                ->where('course_id',$course_id)->pluck('name', 'id');

            return  json_encode(array('faculties'=>view('admin.ajax.faculties',['faculties'=>$faculties])->render(),
                'online_exams'=>view('admin.ajax.exams',['online_exams'=>$online_exams])->render(),
                'batches'=>view('admin.ajax.courses_batches',['batches'=>$batches])->render(),), JSON_FORCE_OBJECT);

        }
        else
        {
            $subjects = Subjects::where(['institute_id'=>$institute_id,'course_id'=>$course_id])->pluck('name', 'id');

            $online_exams = Exam::where(['institute_id'=>$institute_id,'course_id'=>$course_id,'sif_only'=>'No'])->pluck('name', 'id');

            $batches = Batches::where(['institute_id'=>$institute_id,'course_id'=>$course_id,'branch_id'=>$branch_id])
                ->where('course_id',$course_id)->pluck('name', 'id');

            return  json_encode(array('subjects'=>view('admin.ajax.subjects',['subjects'=>$subjects])->render(),
                'online_exams'=>view('admin.ajax.exams',['online_exams'=>$online_exams])->render(),
                'batches'=>view('admin.ajax.courses_batches',['batches'=>$batches])->render(),), JSON_FORCE_OBJECT);

        }
    }

    public function course_branch_changed_in_notice_batch( Request $request )
    {
        $branch_id = $request->branch_id;
        $institute_id = $request->institute_id;
        $course_id = $request->course_id;

        $institute_type = Institutes::where('id',$institute_id)->first()->type;
        if($institute_type)
        {
            $faculties = Faculty::where(['institute_id'=>$institute_id,'course_id'=>$course_id])->pluck('name', 'id');

            $notices = Notice::pluck('title', 'id');

            $batches = Batches::where(['institute_id'=>$institute_id,'course_id'=>$course_id,'branch_id'=>$branch_id])->where('course_id',$course_id)->pluck('name', 'id');

            return  json_encode(array('faculties'=>view('admin.ajax.faculties',['faculties'=>$faculties])->render(),'notices'=>view('admin.ajax.notices',['notices'=>$notices])->render(),'batches'=>view('admin.ajax.courses_batches',['batches'=>$batches])->render(),), JSON_FORCE_OBJECT);

        }
        else
        {
            $subjects = Subjects::where(['institute_id'=>$institute_id,'course_id'=>$course_id])->pluck('name', 'id');

            $notices = Notice::pluck('title', 'id');

            $batches = Batches::where(['institute_id'=>$institute_id,'course_id'=>$course_id,'branch_id'=>$branch_id])->where('course_id',$course_id)->pluck('name', 'id');

            return  json_encode(array('subjects'=>view('admin.ajax.subjects',['subjects'=>$subjects])->render(),'notices'=>view('admin.ajax.notices',['notices'=>$notices])->render(),'batches'=>view('admin.ajax.courses_batches',['batches'=>$batches])->render(),), JSON_FORCE_OBJECT);

        }
    }

    public function course_changed_in_batch_discipline_fee(Request $request)
    {
        $course = Courses::where('id',$request->course_id)->first();

        $institute_type = Institutes::where('id',$course->institute_id)->first()->type;
        if($institute_type)
        {
            $faculties = Faculty::where(['institute_id'=>$course->institute_id,'course_id'=>$course->id])->pluck('name', 'id');
            $batches = Batches::where('course_id',$course->id)->pluck('name', 'id');
            return  json_encode(array('faculties'=>view('admin.ajax.faculties',['faculties'=>$faculties])->render(),'batches'=>view('admin.ajax.batches',['batches'=>$batches])->render(),), JSON_FORCE_OBJECT);

        }
        else
        {
            $subjects = Subjects::where(['institute_id'=>$course->institute_id,'course_id'=>$course->id])->pluck('name', 'id');
            $batches = Batches::where('course_id',$course->id)->pluck('name', 'id');
            return  json_encode(array('subjects'=>view('admin.ajax.subjects',['subjects'=>$subjects])->render(),'batches'=>view('admin.ajax.batches',['batches'=>$batches])->render(),), JSON_FORCE_OBJECT);

        }

    }

    public function faculty_changed_in_batch_discipline_fee(Request $request)
    {
        $institute_id = $request->institute_id;
        $course_id = $request->course_id;
        $faculty_id = $request->faculty_id;
        $subjects = Subjects::where('institute_id',$institute_id)
            ->where('course_id',$course_id)
            ->where('faculty_id',$faculty_id)
            ->pluck('name', 'id');
        return  json_encode(array('subjects'=>view('admin.ajax.subjects',[ 'subjects'=>$subjects ])->render(),), JSON_FORCE_OBJECT);
    }

    public function doctor_group_search_course( Request $request ){

        $batches = Batches::with('course' )
            ->where( 'status', 1)
            ->where( 'year', $request->year )
            ->where( 'course_id', $request->course_id )->get( [ 'name', 'id' , 'is_special' ] );

        // $sessions = Sessions::join( 'course_session', 'course_session.session_id', '=' , 'sessions.id' ) 
        //     ->where( 'course_session.deleted_at', NULL )
        //     ->where( 'course_id', $request->course_id )
        //     ->pluck( 'name',  'sessions.id' );  
        $sessions = Sessions::join('course_year_session','course_year_session.session_id','sessions.id')
        ->join( 'course_year', 'course_year.id', 'course_year_session.course_year_id' )
        ->where('course_year.deleted_at',NULL)
        ->where('course_year_session.deleted_at',NULL)
        ->where('course_year.course_id',$request->course_id)
        ->where('course_year.year',$request->year)
        ->where('course_year.status',1)
        ->pluck('sessions.name',  'sessions.id');

        return view('admin.ajax.doctor_group_batch', [
            'batches' => $batches->pluck('name','id' ),
            'special_batches' => $batches->where('is_special', 'Yes' )->pluck('name','id' ) ,
            'sessions' => $sessions ] );
    }

    public function course_changed_in_lecture_videos(Request $request)
    {
        $course = Courses::where('id',$request->course_id)->first();

        $institute_type = Institutes::where('id',$course->institute_id)->first()->type;
        if($institute_type)
        {
            $faculties = Faculty::where(['institute_id'=>$course->institute_id,'course_id'=>$course->id])->pluck('name', 'id');
            $subjects = null;

            $is_combined = $course->institute_id == AppServiceProvider::$COMBINED_INSTITUTE_ID;

            if( $is_combined ) {

                $faculties = Faculty::where([
                    'institute_id'=> AppServiceProvider::$BSMMU_INSTITUTE_ID,
                    'course_id'=> AppServiceProvider::$MPH_DIPLOMA_COURSE_ID ])->pluck('name', 'id');

                $subjects = Subjects::where([
                    'institute_id'=> AppServiceProvider::$BCPS_INSTITUTE_ID,
                    'course_id'=> AppServiceProvider::$FCPSP1_COURSE_ID])->pluck('name', 'id');

            }

            $topics = Topics::where('course_id',$course->id)->pluck('name', 'id');
            return  json_encode(array('faculties'=>view('admin.ajax.faculties_multiple',
                [
                    'faculties' =>  $faculties,
                    'subjects'  =>  $subjects,
                    'is_combined'  =>  $is_combined,
                ]
            )->render(),
                'topics'=>view('admin.ajax.topics',['topics'=>$topics])->render(),
            ), JSON_FORCE_OBJECT);

        }
        else
        {
            $subjects = Subjects::where(['institute_id'=>$course->institute_id,'course_id'=>$course->id])->pluck('name', 'id');
            $topics = Topics::where('course_id',$course->id)->pluck('name', 'id');
            return  json_encode(
                array(
                    'subjects'=>view('admin.ajax.subjects_multiple',['subjects'=>$subjects])->render( ),
                    'topics'=>view('admin.ajax.topics',['topics'=>$topics])->render( ),
                ), JSON_FORCE_OBJECT);

        }

    }

    public function disciplines_by_multiple_faculties(Request $request )
    {
        //dd( $request->faculty_ids );

        $course = Courses::where( 'id', $request->course_id )->first( );
        $subjects = Subjects::where( ['institute_id'=>$course->institute_id,'course_id'=>$course->id] );
        if( is_array( $request->faculty_ids ) ) {
            $subjects->whereIn( 'faculty_id', $request->faculty_ids );
        }

        $subjects = $subjects->pluck( 'name', 'id' );

        return  json_encode( [ 'subjects'=>view('admin.ajax.subjects_multiple',['subjects'=>$subjects])->render() ], JSON_FORCE_OBJECT);
    }

    public function faculty_changed_in_lecture_videos( Request $request )
    {
        $institute_id = $request->institute_id;
        $course_id = $request->course_id;
        $faculty_id = $request->faculty_id;

        if( $institute_id == AppServiceProvider::$COMBINED_INSTITUTE_ID ) {
            $where = [
                'institute_id'=> AppServiceProvider::$BCPS_INSTITUTE_ID,
                'course_id'=> AppServiceProvider::$FCPSP1_COURSE_ID ];
        } else {
            $where = [
                'institute_id'=> $institute_id,
                'course_id'=> $course_id ];
        }

        $subjects = Subjects::where('institute_id',$institute_id)
            ->where( $where )
            ->pluck('name', 'id');
        return  json_encode(array('subjects'=>view('admin.ajax.subjects_multiple',['subjects'=>$subjects])->render(),), JSON_FORCE_OBJECT);
    }

    public function course_changed_in_online_exams(Request $request)
    {
        $course = Courses::where('id',$request->course_id)->first();

        $institute_type = Institutes::where('id',$course->institute_id)->first()->type;
        if($institute_type)
        {
            $faculties = Faculty::where(['institute_id'=>$course->institute_id,'course_id'=>$course->id])->pluck('name', 'id');
            $topics = Topics::where('course_id',$course->id)->pluck('name', 'id');
            return  json_encode(array('faculties'=>view('admin.ajax.faculties_multiple',['faculties'=>$faculties])->render(),'topics'=>view('admin.ajax.topics',['topics'=>$topics])->render(),), JSON_FORCE_OBJECT);

        }
        else
        {
            $subjects = Subjects::where(['institute_id'=>$course->institute_id,'course_id'=>$course->id])->pluck('name', 'id');
            $topics = Topics::where('course_id',$course->id)->pluck('name', 'id');
            return  json_encode(array('subjects'=>view('admin.ajax.subjects_multiple',['subjects'=>$subjects])->render(),'topics'=>view('admin.ajax.topics',['topics'=>$topics])->render(),), JSON_FORCE_OBJECT);

        }

    }

    public function faculty_changed_in_online_exams(Request $request)
    {
        $institute_id = $request->institute_id;
        $course_id = $request->course_id;
        $faculty_id = $request->faculty_id;
        $subjects = Subjects::where('institute_id',$institute_id)
            ->where('course_id',$course_id)
            ->where('faculty_id',$faculty_id)
            ->pluck('name', 'id');
        return  json_encode(array('subjects'=>view('admin.ajax.subjects_multiple',['subjects'=>$subjects])->render(),), JSON_FORCE_OBJECT);
    }




    public function courses_subjects_topics_batches_lectures(Request $request)
    {
        $branch_id = $request->branch_id;
        $institute_id = $request->institute_id;
        $course_id = $request->course_id;

        $subjects = Subjects::get()->where('institute_id',$institute_id)
            ->where('course_id',$course_id)
            ->pluck('name', 'id');

        $topics = Topics::get()->where('course_id',$course_id)->pluck('name', 'id');

        $batches = Batches::get()->where('institute_id',$institute_id)
            ->where('course_id',$course_id)
            ->where('branch_id',$branch_id)
            ->pluck('name', 'id');


        return  json_encode(array('subjects'=>view('admin.ajax.subjects',['subjects'=>$subjects,'batches'=>$batches])->render(),'topics'=>view('admin.ajax.topics_for_lecture_sheet_batches',['topics'=>$topics])->render(),'batches'=>view('admin.ajax.courses_batches',['subjects'=>$subjects,'batches'=>$batches])->render(),), JSON_FORCE_OBJECT);

    }

    public function courses_faculties_subjects_batches(Request $request)
    {
        $institute_id = $request->institute_id;
        $course_id = $request->course_id;
        // $faculty_id = $request->faculty_id;
        // $subject_id = $request->subject_id;

        $faculties = Faculty::get()->where('institute_id',$institute_id)
            ->where('course_id',$course_id)
            ->pluck('name', 'id');

        $subjects = Subjects::get()->where('institute_id',$institute_id)
            ->where('course_id',$course_id)
            ->pluck('name', 'id');

        $batches = Batches::get()->where('institute_id',$institute_id)
            ->where('course_id',$course_id)
            //  ->where('faculty_id',$faculty_id)
            //  ->where('subject_id',$subject_id)
            ->pluck('name', 'id');
        return array('batches'=>view('admin.ajax.courses_faculties_subjects_batches',['batches'=>$batches]));
    }

    public function reg_no(Request $request)
    {
        $year = $request->year;
        $session = $request->session_id;
        $session = Sessions::where('id',$request->session_id)->pluck('session_code');
        $course_code = Courses::where('id',$request->course_id)->pluck('course_code');
        $batch_code = Batches::where('id',$request->batch_id)->pluck('batch_code');
        
        $start_index = Batches::where('id',$request->batch_id)->value('start_index');
        $end_index = Batches::where('id',$request->batch_id)->value('end_index');
        $range = 'Batch Range : ( '.$start_index.' - '.$end_index.' ) ';

        $reg_no_first_part = $year.$session[0].substr($course_code[0], 0, 1).substr($batch_code[0], 0, 1);


        return  json_encode(array('reg_no_first_part'=>$reg_no_first_part,'range'=>$range), JSON_FORCE_OBJECT);

    }

    public function add_schedule_row(Request $request)
    {
        //echo 'Bismillah';exit;
        $data['d'] = $request->d;
        $data['r'] = $request->r;
        $id = $request->schedule_details_id;

        $data['topics_list']= Topics::get()->where('course_id',BatchesSchedules::find($id)->course_id)->sortBy('name')->pluck('name','id');
        $data['teachers_list'] = Teacher::get()->pluck('name','id');
        $data['batch_slots'] = BatchesSchedulesSlots::where('schedule_id',$id)->get();
        $count_slots =  0;
        $m_slot_lists = array();
        $count_rows = count($data['topics_list']);
        $count_slots_all = count($data['batch_slots']);
        foreach ($data['batch_slots'] as $value){
            if ($value['slot_type']){
                $m_slot_lists[++$count_slots] = $value['slot_type'];
            }
        }
        $data['count_slots'] = count($m_slot_lists);
        $data['m_slot_lists'] = $m_slot_lists;
        //echo '<pre>';print_r($data['m_slot_lists']);exit;
        return view('admin.ajax.schedule_row_add',$data);
    }

    public function set_weekday(Request $request){

        $initial_date = date('l',date_create_from_format('Y-m-d',$request->initial_date)->getTimestamp());
        $data['week_days'] = WeekDays::get()->pluck('name', 'wd_id');
        $data['week_day'] = WeekDays::get()->where('name',$initial_date)->pluck('wd_id');
        return view('admin.ajax.select_initial_week_day',$data);

    }

    public function get_transaction(Request $request)
    {
        $data['doctor_course'] = DoctorsCourses::where(['id'=>$request->doctor_course_id])->first();

        return view('admin.ajax.transaction',$data);
    }

    public function courier_division_district(Request $request)
    {
        $division_id = $request->courier_division_id;
        $districts = Districts::get()->where('division_id',$division_id)->pluck('name', 'id');
        return view('admin.ajax.courier_division_district',['districts'=>$districts]);
    }

    public function courier_district_upazila(Request $request)
    {
        $district_id = $request->courier_district_id;
        $upazilas = Upazilas::get()->where('district_id',$district_id)->pluck('name', 'id');
        return  json_encode(array('upazilas'=>view('admin.ajax.courier_district_upazila',['upazilas'=>$upazilas])->render(),'courier_address'=>view('admin.ajax.courier_address')->render()), JSON_FORCE_OBJECT);

    }

    public function permanent_division_district(Request $request)
    {
        $division_id = $request->permanent_division_id;
        $districts = Districts::get()->where('division_id',$division_id)->pluck('name', 'id');
        return view('admin.ajax.permanent_division_district',['districts'=>$districts]);
    }

    public function permanent_district_upazila(Request $request)
    {
        $district_id = $request->permanent_district_id;
        $upazilas = Upazilas::get()->where('district_id',$district_id)->pluck('name', 'id');
        return view('admin.ajax.permanent_district_upazila',['upazilas'=>$upazilas]);
    }

    public function present_division_district(Request $request)
    {
        $division_id = $request->present_division_id;
        $districts = Districts::get()->where('division_id',$division_id)->pluck('name', 'id');
        return view('admin.ajax.present_division_district',['districts'=>$districts]);
    }

    public function present_district_upazila(Request $request)
    {
        $district_id = $request->present_district_id;
        $upazilas = Upazilas::get()->where('district_id',$district_id)->pluck('name', 'id');
        return view('admin.ajax.present_district_upazila',['upazilas'=>$upazilas]);
    }

    public function search_doctors(Request $request)
    {
        $text = $request->term['term'];

        return Doctors::query()
            ->where(function($query) use ($text) {
                $query->where('name', 'like', "%{$text}%")
                    ->orWhere('bmdc_no', 'like', "%{$text}%")
                    ->orWhere('mobile_number', 'like', "%{$text}%");
            })
            ->get([
                'id',
                'name',
                'mobile_number',
                'bmdc_no'
            ]);
    }
    public function search_classes(Request $request)
    {
        $text =  $_GET['term'] ?? null;
        $text = $text['term'] ?? '';



        $data = Topics::select(DB::raw("CONCAT(name) AS name"),'id');

        if( $text ) {
            $data->where( 'name', 'like', '%'.$text.'%' );
        }

        if( $request->has('year') ) {
            $data->where( 'year', $request->year );
        }

        if( $request->has('session_id') ) {
            $data->where( 'session_id', $request->session_id );
        }

        if( $request->has('course_id') ) {
            $data->where( 'course_id', $request->course_id );
        }

        if( $request->has('institute_id') ) {
            $data->where( 'institute_id', $request->institute_id );
        }


        $data = $data->get();

        echo json_encode( $data);
    }

    public function search_batches(Request $request)
    {
        $text =  $_GET['term'];
        $text = $text['term'];

        $data = Batches::select(DB::raw("CONCAT(name) AS batch_name"),'id')
            ->where('name', 'like', '%'.$text.'%')
            ->orWhere('name', 'like', '%'.$text.'%')
            ->get();
        //$data = DB::table('institution')->where('institution_type_id',$content_section_id)->where('name', 'like', $text.'%')->get();
        echo json_encode( $data);
    }

    public function batch_search_payment(Request $request){
        $batches=Batches::where(['year'=>$request->year,'session_id'=>$request->session_id,'course_id'=>$request->course_id])->pluck('name','id');
        return view('admin.ajax.batch_search_payment',[
            'batches'=>$batches,
            ]);
    }

    public function session_search_payment( Request $request ){

        $sessions = Sessions::join('course_year_session','course_year_session.session_id','sessions.id')
                ->join( 'course_year', 'course_year.id', 'course_year_session.course_year_id' )
                ->where('sessions.status',1)
                ->where('course_year.deleted_at',NULL)
                ->where('course_year.year',$request->year)  
                ->where('course_year_session.deleted_at',NULL)
                ->pluck('name',  'sessions.id');
        return view('admin.ajax.session_search_payment',['sessions'=>$sessions]);
    }

    public function exam_search_batches(Request $request){
        $batches=Batches::where(['year'=>$request->year])->pluck('name','id');
        return view('admin.ajax.exam_year_batch',[
            'batches'=>$batches,
            ]);
    }
    public function view_result_search_batch(Request $request){
        $batches = Batches::where('year',$request->year)->where('session_id',$request->session_id)->pluck('name','id');
        return view('admin.ajax.view_result_search_batch',['batches'=>$batches]);
    }


    public function search_year(Request $request)
    {
        $doctor_courses=DoctorsCourses::with('course')->where('year',$request->year)->groupBy('course_id')->get();
        return view('admin.ajax.search_course',[
            'doctor_courses' => $doctor_courses->where('course', '!=', null),
        ]);
    }


    public function session_batch(Request $request)
    {
        $batchs = Batches::where('course_id',$request->course_id)->select('name','id')->get();
        $sessions = CourseSessions::with('session')->where('course_id', $request->course_id)->get();
        return view('admin.ajax.session_batch',[
            'batchs'=>$batchs,
            'sessions'=>$sessions
            ]);


    }

    public function search_questions(Request $request)
    {
        $text =  $_GET['term'];
        $text = $text['term'];
        $type =  $_GET['type'];

        $user = Auth::user( );
        $user_id = $user->id;



        $userRole = [];


        //dd( $topics );

        $data = Question::with('quest_subject:id,subject_name as name', 'quest_topic:id,topic_name as name', 'quest_chapter:id,chapter_name as name')
            ->select('question_title' ,'id', 'subject_id', 'topic_id', 'chapter_id' )
            ->where('type', $type)
            ->where(function($query) use ($text) {
                $query->where('question_title', 'like', '%'.$text.'%')
                    ->orWhere('id', 'like', '%'.$text.'%');
            });

        if( $user->need_to_filter_question_topic( $topic_ids, $subject_ids, $chpater_ids  ) ) {


            $data = $data->where( function( $query ) use ($topic_ids, $subject_ids, $chpater_ids) {

                $query->whereIn( 'topic_id', $topic_ids );

                if( $subject_ids->count() ) {
                    $query->orWhereIn( 'subject_id', $subject_ids );
                }

                if( $chpater_ids->count() ) {
                    $query->orWhereIn( 'chapter_id', $chpater_ids );
                }

            });
        }

        $data = $data->get();

        echo json_encode( $data );
    }

    public function search_questions_2(Request $request)
    {
        $text =  $_GET['term'];
        $text = $text['term'];
        //$type =  $_GET['type'];

        $data = Question::select('question_title' ,'id')
            ->where('question_title', 'like', '%'.$text.'%')
            //->where('type', $type)
            ->get();
        //$data = DB::table('institution')->where('institution_type_id',$content_section_id)->where('name', 'like', $text.'%')->get();
        echo json_encode( $data);
    }


    public function question_type_mcq_sba(Request $request)
    {
        $question_type_id =  $request->question_type_id;
        $exam_id =  $request->exam_id;

        $data['question_type'] = QuestionTypes::where('id', $question_type_id)->first();


        if( $exam_id ) {

            $data['mcqs_ids'] = Exam_question::where(['exam_id' => $exam_id, 'question_type' => 1])->pluck('question_id');
            //$data['mcqs'] = DB::table('questions')->whereIn('id', $data['mcqs_ids'])->pluck(DB::raw('CONCAT(question_title, " (", id,")") AS question_title'), 'id');
            $data['mcqs'] = DB::table('questions')->whereIn('id', $data['mcqs_ids'])->pluck(DB::raw('CONCAT(question_title, " (", id,")") AS question_title'), 'id');
            
            $data['sbas_ids'] = Exam_question::where(['exam_id' => $exam_id, 'question_type' => 2])->pluck('question_id');
            $data['sbas'] = DB::table('questions')->whereIn('id', $data['sbas_ids'])->pluck(DB::raw('CONCAT(question_title, " (", id,")") AS question_title'), 'id');

            $data['mcq2s_ids'] = Exam_question::where(['exam_id' => $exam_id, 'question_type' => 3])->pluck('question_id');
            $data['mcq2s'] = DB::table('questions')->whereIn('id', $data['mcq2s_ids'])->pluck(DB::raw('CONCAT(question_title, " (", id,")") AS question_title'), 'id');

        }
        //echo "<pre>";print_r($data);

        return view('admin.ajax.question_type_mcq_sba', $data);
    }

    public function question_info(Request $request)
    {
        $question_type_id =  $request->question_type_id;

        $question_info = QuestionTypes::where('id', $question_type_id)->first();

        $data['batch_type'] = $question_info->batch_type;

        $data['total_mcq'] = $question_info->mcq_number;
        $data['total_sba'] = $question_info->sba_number;
        $data['total_mcq2'] = $question_info->mcq2_number;

        $data['total_mark'] = $question_info->full_mark;
        $data['negative_mark'] = $question_info->negative_mark;
        $data['duration'] = $question_info->duration;

//        dd($data);
        return view('admin.ajax.question_info', $data);
    }

    public function institute_courses_in_package(Request $request)
    {
        $courses = Courses::where('institute_id',$request->institute_id)->pluck('name', 'id');
        return view('admin.ajax.courses_single_required',['courses'=>$courses]);
    }

    public function course_changed_in_package(Request $request)
    {
        //$course_id = $request->course_id;
        $course = Courses::where(['id'=>$request->course_id])->first();
        if(isset($course->institute->type) && $course->institute->type == '0')
        {
            $subjects = Subjects::where('course_id',$course->id)->pluck('name', 'id');
            return view('admin.ajax.subjects_single_required',['subjects'=>$subjects]);
        }
        else
        {
            $faculties = Faculty::get()->where('course_id',$course->id)->pluck('name', 'id');
            return view('admin.ajax.faculties_single_required',['faculties'=>$faculties]);
        }

    }

    public function faculty_changed_in_package(Request $request)
    {
        $faculty_id = $request->faculty_id;
        $subjects = Subjects::where('faculty_id',$faculty_id)
            ->pluck('name', 'id');
        return view('admin.ajax.subjects_single_required',['subjects'=>$subjects]);
    }

    public function institute_changed_in_question_reference_exam(Request $request)
    {
        $reference_courses = ReferenceCourse::where('institute_id',$request->institute_id)->pluck('name', 'id');
        return view('admin.ajax.reference_course_single_required',['reference_courses'=>$reference_courses]);
    }

    public function course_changed_in_question_reference_exam(Request $request)
    {
        $course = ReferenceCourse::where('id',$request->course_id)->first();
        if(isset($course) && $course->type == 1)
        {
            $reference_faculties = ReferenceFaculty::where('course_id',$request->course_id)->orderBy('name','asc')->pluck('name', 'id');
            return  json_encode(array('reference_faculties'=>view('admin.ajax.reference_faculty_single_required',['reference_faculties'=>$reference_faculties])->render(),), JSON_FORCE_OBJECT);

        }
        else
        {
            $reference_subjects = ReferenceSubject::where('course_id',$request->course_id)->orderBy('name','asc')->pluck('name', 'id');
            $reference_sessions = ReferenceSession::where('course_id',$request->course_id)->pluck('name', 'id');
            return  json_encode(array('reference_subjects'=>view('admin.ajax.reference_subject_single_required',['reference_subjects'=>$reference_subjects])->render(),'reference_sessions'=>view('admin.ajax.reference_session_single_required',['reference_sessions'=>$reference_sessions])->render(),), JSON_FORCE_OBJECT);

        }

    }

    public function search_chapter_list(Request $request){
      $chapters =  Chapters::where('subject_id',$request->subject_id)->pluck('chapter_name','id');
      return view('admin.ajax.mcq_search.search_chapta',compact('chapters'));
    }
    public function search_topic_list(Request $request){
      $topics =  QuestionTopic::where(['subject_id' => $request->subject_id ,'chapter_id'=>$request->chapter_id])->pluck('topic_name','id');
      return view('admin.ajax.mcq_search.search_topics',compact('topics'));
    }

    public function search_source_course(Request $request){
      $source_courses =  ReferenceCourse::whereIn('institute_id',$request->source_institute_id)->pluck('name','id');
      return view('admin.ajax.mcq_search.search_source_course',compact('source_courses'));
    }

    public function search_source_faculty(Request $request){
      $source_faculties =  ReferenceFaculty::where('course_id',$request->source_course_id)->pluck('name','id');
      return view('admin.ajax.mcq_search.search_source_faculty',compact('source_faculties'));
    }
    public function search_source_subject(Request $request){
        $source_subjects =  ReferenceSubject::where('course_id',$request->source_course_id)->pluck('name','id');
        return view('admin.ajax.mcq_search.search_source_subjects',compact('source_subjects'));
      }

    public function change_include_lecture_sheet(Request $request)
    {
        return view('admin.ajax.change_include_lecture_sheet');
    }

    public function change_lecture_sheet_collection(Request $request)
    {
        $data['divisions'] = Divisions::pluck('name', 'id');
        return view('admin.ajax.courier_division',$data);
    }

    public function institute_changed_in_question_search_options(Request $request)
    {
        $question_source_courses = ReferenceCourse::where('institute_id',$request->institute_id)->pluck('name', 'id');
        return view('admin.ajax.question_source_course_single_not_required',['question_source_courses'=>$question_source_courses]);
    }

    public function course_changed_in_question_search_options(Request $request)
    {
        $course = ReferenceCourse::where('id',$request->course_id)->first();
        if(isset($course) && $course->type == 1)
        {
            $question_source_faculties = ReferenceFaculty::where('course_id',$request->course_id)->orderBy('name','asc')->pluck('name', 'id');
            return  json_encode(array('reference_faculties'=>view('admin.ajax.question_source_faculty_single_not_required',['question_source_faculties'=>$question_source_faculties])->render(),), JSON_FORCE_OBJECT);

        }
        else
        {
            $question_source_subjects = ReferenceSubject::where('course_id',$request->course_id)->orderBy('name','asc')->pluck('name', 'id');
            $question_source_sessions = ReferenceSession::where('course_id',$request->course_id)->pluck('name', 'id');
            return  json_encode(array('reference_subjects'=>view('admin.ajax.question_source_subject_single_not_required',['question_source_subjects'=>$question_source_subjects])->render(),'reference_sessions'=>view('admin.ajax.question_source_session_single_not_required',['question_source_sessions'=>$question_source_sessions])->render(),), JSON_FORCE_OBJECT);

        }

    }




    public function search_session(Request $request){
        
        // $sessions = CourseSessions::with('session')->where('course_id', $request->course_id)->get();
        // dd($sessions);
        // return view('admin.ajax.course_session',[
        //     'sessions'=>$sessions
        //     ]);

       $courseYear =  CourseYear::with('course_year_session','course_year_session.session')->where(['year' => $request->year,'course_id' => $request->course_id])->first();
            return view('admin.ajax.course_session',[
            'courseYear'=>$courseYear
            ]);
    }

    public function session_course_search(Request $request){
        
        $sessions = DB::table('sessions')
            ->join('course_year_session', 'sessions.id', '=', 'course_year_session.session_id')
            ->join('course_year', 'course_year.id', '=', 'course_year_session.course_year_id')
          
            ->pluck('name', 'sessions.id');
          
        return view('admin.ajax.session_course_search',[
            'sessions'=>$sessions
            ]);
    }

    public function course_session_search(Request $request){
        
        $sessions = DB::table('course_year_session')
            ->join('sessions', 'sessions.id', '=', 'course_year_session.session_id')
            ->join('course_year', 'course_year.id', '=', 'course_year_session.course_year_id')
            ->where(['course_year.course_id' => $request->course_id,'course_year.year'=>$request->year])
            ->pluck('name', 'sessions.id');
          
        return view('admin.ajax.course_session_search',[
            'sessions'=>$sessions
            ]);
    }

    public function search_subject(Request $request)
    {
       $subjects =  Subjects::where(['course_id' => $request->course_id])->pluck('name','id');
        return view('admin.ajax.search_subjects',[
        'subjects'=>$subjects
        ]);
    }


    public function search_batch(Request $request){
        $batches = Batches::where('course_id',$request->course_id)->where('year',$request->year)->where('session_id',$request->session_id)->pluck('name','id');
        return view('admin.ajax.course_batch',['batches'=>$batches]);   
    }
  
    public function doctor_course_search(Request $request){
        $sessions = CourseSessions::with('session')->where('course_id', $request->course_id)->get();
        return view('admin.ajax.course_session',[
            'sessions'=>$sessions

            ]);
    }

    public function batch_search(Request $request){ 
        $batches = Batches::where(['year'=>$request->year, 'course_id'=>$request->course_id, 'session_id'=> $request->session_id, 'status' => 1])->pluck('name','id');
        return view('admin.ajax.batches_search',['batches'=>$batches]);
    }

    public function batchSearchGetMultiSelect(Request $request){
        $batches = Batches::query()
            ->when($request->course_id, function ($query, $course_id) {
                $query->where('course_id', $course_id);
            })
            ->when($request->year, function ($query, $year) {
                $query->where('year', $year);
            })
            ->when($request->session_id, function ($query, $session_id) {
                $query->where('session_id', $session_id);
            })
            ->pluck('name','id');

        return view('admin.ajax.multi-select-batches',['batches'=>$batches]);
    }

    public function doctor_batch_search(Request $request){
        $batches = Batches::query()
            ->when($request->course_id, function ($query, $course_id) {
                $query->where('course_id', $course_id);
            })
            ->when($request->year, function ($query, $year) {
                $query->where('year', $year);
            })
            ->when($request->session_id, function ($query, $session_id) {
                $query->where('session_id', $session_id);
            })
            ->pluck('name','id');

        return view('admin.ajax.course_batch',['batches'=>$batches]);
    }

    public function doctor_video_search( Request $request ){

        $doctor_ask_reply = DB::table( 'doctor_asks as da' )
            ->leftjoin( 'doctors as d', 'da.doctor_id', '=', 'd.id' )
            ->leftjoin( 'doctors_courses as dc', 'da.doctor_course_id', '=', 'dc.id' )
            ->leftjoin( 'courses as c', 'c.id', '=', 'dc.course_id' )
            ->leftjoin( 'batches as b', 'b.id', '=', 'dc.batch_id' )
            ->leftjoin( 'sessions as s', 's.id', '=', 'dc.session_id' )
            ->leftjoin( 'lecture_video as lv', 'lv.id', '=', 'da.lecture_video_id' )
           ->rightJoin( 'doctor_ask_reply as dar','dar.doctor_ask_id','=','da.id');

        $doctor_ask_reply->select(
            'lv.name as lecture_video',
            'lv.id as lecture_video_id',
        );

        if($year = $request->year) {
            $doctor_ask_reply->where('dc.year', $year);
        }

        if($batch_id = $request->batch_id) {
            $doctor_ask_reply->where('dc.batch_id', $batch_id);
        }

        if($course_id = $request->course_id) {
            $doctor_ask_reply->where('dc.course_id', $course_id);
        }

        if($session_id = $request->session_id) {
            $doctor_ask_reply->where('dc.session_id', $session_id);
        }

        if($faculty_id = $request->faculty_id) {
            $doctor_ask_reply->where('dc.faculty_id', $faculty_id);
        }

        if($subject_id = $request->subject_id) {
            $doctor_ask_reply->where('dc.subject_id', $subject_id);
        }

        $data = ["-- Select Video --"];

        foreach($doctor_ask_reply->get() as $item) {
            $data[$item->lecture_video_id] = $item->lecture_video ?? '';
        }
        
        return view('admin.ajax.course_video',['lecture_videos' => $data]);
    }



     public function session_batch_sarching(Request $request)
    {
        $sessions = Sessions::query()
            ->whereHas('course_years', function ($query) use ($request) {
                $query
                    ->where('status', 1)
                    ->when($request->course_id, function ($query, $course_id) {
                        $query->where('course_id', $course_id);
                    })
                    ->when($request->year, function ($query, $year) {
                        $query->where('year', $year);
                    });
            })
            ->pluck('name',  'sessions.id');

        return view('admin.ajax.session_batch',['sessions'=>$sessions]);
    }

     public function discount_batch(Request $request)
    {

        $batches = Batches::where(['course_id'=>$request->course_id , 'session_id' => $request->session_id , 'year' => $request->year])->get();

        return view('admin.ajax.discount_batch',[
            'batches'=>$batches,

            ]);

    }


    public function apply_discount_code(Request $request)
    {

        $d = Discount::where([ 'discount_code'=> $request->discount_code, 'batch_id' => $request->batch_id, 'doctor_id' => $request->doctor_id, 'used'=>0 ,'status'=> 1 ] );
        if( $d->exists( ) ) {
            $discount_code =$d->first( );
            if(strtotime("now") - strtotime($discount_code->created_at ) < ($discount_code->code_duration * 3600))
            {
                return response( [ 'amount' => $discount_code->amount, 'valid' => true ] );
            }
        }

        return response( [ 'amount' => 0, 'valid' => false ] );

    }

    public function shipment(Request $request){
        return view('admin.ajax.shipment');
    }

    public function doctors_courses_batch_shifted_details(Request $request){
         $doctor_course_id = $request->doctor_course_id;
        // $batch_shift_historys = BatchShiftedHistory::with('doctor_course')->where('doctor_course_id',$doctor_course_id)->get();
        // if($batch_shift_historys != null){
        //     $from_batches=[];
        //     $to_batches=[];
        //     foreach($batch_shift_historys as $batch_shift_history ){
        //         $from_batch = Batches::where('id',$batch_shift_history->from_batch_id)->first();
        //         $from_batches[]=$from_batch;
        //         $to_batch = Batches::where('id',$batch_shift_history->to_batch_id)->first();
        //         $to_batches[]=$to_batch;
        //     }
      $batch_shifted_info = DoctorsCourses::where('id',$doctor_course_id)->first();
             return view('admin.ajax.batch_shifted',['batch_shifted_info'=>$batch_shifted_info]);

        }
    
}


