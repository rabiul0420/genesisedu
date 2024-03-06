<?php

namespace App\Http\Controllers;


use App\BatchesFaculties;
use App\BatchesSubjects;
use App\Institutes;
use App\Batches;
use App\Providers\AppServiceProvider;
use App\Question_ans;
use App\Result;
use App\Sessions;
use App\Subjects;
use App\Chapters;
use App\DoctorsCourses;
use App\DoctorAnswers;
use App\DoctorExam;
use App\Exam;
use App\Exam_question;
use App\Courses;
use App\Discount;
use App\Faculty;
use App\LectureSheet;
use App\OnlineLectureLink;
use App\LectureVideoBatch;
use App\Divisions;
use App\Districts;
use App\MedicalCollege;
use App\MedicalColleges;
use App\Upazilas;
use App\CourierChargePackage;
use App\CourseYearSession;
use App\QuestionTypes;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use PhpParser\Comment\Doc;
use Session;
use Auth;
use View;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Jenssegers\Agent\Agent;





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
        //$this->middleware('auth:doctor')->except('search_college');
    }


    public function institute_courses( Request $request )
    {
        $institute_id = $request->institute_id;
        $institute_type = Institutes::where('id',$institute_id)->first()->type;
        Session(['institute_id'=> $institute_id]);
        $url  = ($institute_id==4)?'course-sessions-subjects':(($institute_id==6 || AppServiceProvider::$COMBINED_INSTITUTE_ID = $institute_id)?'course-sessions-faculties':'');

        $courses = Courses::where(['status' => 1, 'institute_id' => $institute_id])->pluck('name', 'id');
        return view('ajax.institute_courses',['courses'=>$courses,'url'=>$url]);
    }

    public function course_changed(Request $request)
    {       
        //$course_id = $request->course_id;
        $course = Courses::where(['id'=>$request->course_id])->first();

        if(isset($course->institute->type) && $course->institute->type == '0')
        {
            // $sessions = Sessions::join( 'course_session', 'course_session.session_id', '=', 'sessions.id' )
            //     ->where('course_session.deleted_at',NULL)
            //     ->where('course_id',$course->id)
            //     ->pluck('name',  'sessions.id');

            $sessions = DB::table('course_year_session')
                        ->join('course_year','course_year_session.course_year_id','course_year.id')
                        ->join('sessions','course_year_session.session_id','sessions.id')
                        ->where('course_year.deleted_at',NULL)
                        ->where('course_year_session.deleted_at',NULL)
                        ->where('course_year.course_id',$request->course_id)
                        // ->where('sessions.show_admission_form','yes')
                        ->where('course_year.status',1)       
                        ->pluck('sessions.name',  'sessions.id');
   
            $subjects = Subjects::where('course_id',$course->id)->where('status' , 1)->pluck('name', 'id');
   
            return view('ajax.course_sessions_subjects',['subjects'=>$subjects,'sessions'=>$sessions]);

        }  else {
            $course_id = $request->course_id;
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

            //return $faculties;

            // $sessions = Sessions::join('course_session','course_session.session_id','=','sessions.id')
            //     ->whereNull('course_session.deleted_at')->where('course_id', $course->id )->pluck('name',  'sessions.id');

            $sessions = DB::table('course_year_session')
                        ->join('course_year','course_year_session.course_year_id','course_year.id')
                        ->join('sessions','course_year_session.session_id','sessions.id')
                        ->where('course_year.deleted_at',NULL)
                        ->where('course_year_session.deleted_at',NULL)
                        ->where('course_year.course_id',$request->course_id)
                        // ->where('sessions.show_admission_form','yes')
                        ->where('course_year.status',1) 
                        ->pluck('sessions.name',  'sessions.id');

            return view('ajax.course_sessions_faculties', [
                'faculties' => $faculties,
                'sessions' => $sessions,
                'combined_institute_id' => AppServiceProvider::$COMBINED_INSTITUTE_ID,
                'bcps_subjects' => $bcps_subjects
            ]);

        }

    }


    public function course_changed_in_package_purchase(Request $request)
    {
        //$course_id = $request->course_id;
        $course = Courses::where(['id'=>$request->course_id])->first();
        if(isset($course->institute->type) && $course->institute->type == '0')
        {
            $subjects = Subjects::where('course_id',$course->id)->pluck('name', 'id');
            return view('ajax.subjects',['subjects'=>$subjects]);

        }
        else
        {
            $faculties = Faculty::get()->where('course_id',$course->id)->pluck('name', 'id');
            return view('ajax.faculties',['faculties'=>$faculties]);

        }

    }

    public function faculty_subjects_in_admission(Request $request)
    {
        $faculty_id = $request->faculty_id;
        $subjects = Subjects::where( 'faculty_id', $faculty_id )
            ->pluck('name', 'id');
        return view( 'ajax.faculty_subjects_in_admission', [ 'subjects' => $subjects ]);
    }


    /*   for bsmmu  */
    public function course_sessions_faculties(Request $request)
    {
        $course_id = $request->course_id;
        $faculties = Faculty::get()->where('course_id',$course_id)->pluck('name', 'id');
        $sessions = Sessions::join('course_session','course_session.session_id','=','sessions.id')->where('course_id',$course_id)->pluck('name',  'sessions.id');
        return view('ajax.course_sessions_faculties',['faculties'=>$faculties,'sessions'=>$sessions]);
    }

    /*   for bcps  */
    public function course_sessions_subjects(Request $request)
    {
        $course_id = $request->course_id;
        $subjects = Subjects::get()->where('course_id',$course_id)->pluck('name', 'id');
        $sessions = Sessions::join('course_session','course_session.session_id','=','sessions.id')->where('course_id',$course_id)->pluck('name',  'sessions.id');
        return view('ajax.course_sessions_subjects',['subjects'=>$subjects,'sessions'=>$sessions]);
    }

    /*   for bsmmu  */
    public function faculty_subjects(Request $request)
    {
        $faculty_id = $request->faculty_id;
        $is_combined = $request->institute_id == AppServiceProvider::$COMBINED_INSTITUTE_ID;
        $subjects = Subjects::where( 'faculty_id', $faculty_id )
            ->pluck('name', 'id');
        return view( 'ajax.faculty_subjects', [ 'subjects'=>$subjects, 'is_combined' => $is_combined ]);
    }

    public function courses_branches_subjects_batches(Request $request)
    {
        $institute_id = $request->institute_id;
        $course_id = $request->course_id;
        $subject_id = $request->subject_id;
        $branch_id = $request->branch_id;

        if($institute_id == 4){
            $batches = Batches::where([ 'course_id'=>$course_id,'branch_id'=>$branch_id,'subject_id'=>$subject_id ])->pluck('name', 'id');
        }
        else {
            $batches = Batches::where([ 'course_id'=>$course_id,'branch_id'=>$branch_id ])->pluck('name', 'id');
        }

        return  json_encode(array('batches'=>view('ajax.batches',['batches'=>$batches])->render(),), JSON_FORCE_OBJECT);

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
            'is_show_admission'=>'Yes',
        ]);


        if( $request->is_combined != 'yes' ) {
            if( $faculty_id )
                $batches->whereIn( 'id', BatchesFaculties::select(['batch_id'])->where( 'faculty_id', $faculty_id )->whereNull('batches_faculties.deleted_at') );

            if( $subject_id )
                $batches->whereIn( 'id', BatchesSubjects::select(['batch_id'])->where( 'subject_id', $subject_id )->whereNull('batches_subjects.deleted_at') );
        }


        return  json_encode(array('batches'=>view('ajax.batches',['batches'=> $batches->pluck('name', 'id') ])->render(),), JSON_FORCE_OBJECT);

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
        return  json_encode(array('faculties'=>view('admin.ajax.faculties',['faculties'=>$faculties,'batches'=>$batches])->render(),'batches'=>view('admin.ajax.courses_batches',['faculties'=>$faculties,'batches'=>$batches])->render(),), JSON_FORCE_OBJECT);


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


public function reg_no(Request $request)
    {
        $YEAR = Batches::where('id',$request->batch_id)->value('year');
        $year = substr( $YEAR, -2);

        $session = Sessions::where('id',$request->session_id)->pluck('session_code');
        $capacity = Batches::where('id',$request->batch_id)->where('year',$YEAR)->value('capacity');

        $message = '';

        $reg_no_first_part = $year.$session[0];

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

    public function change_include_lecture_sheet(Request $request)
    {
        return view('admin.ajax.change_include_lecture_sheet');
    }

    public function change_lecture_sheet_collection(Request $request)
    {
        $data['divisions'] = Divisions::pluck('name', 'id');
        return view('admin.ajax.courier_division',$data);
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

    // public function batch_details(Request $request)
    // {

    //     $course_id = $request->course_id;
    //     $batch_id = $request->batch_id;

    //     $batch = Batches::where(['course_id'=>$course_id,'id'=>$batch_id])->first();
    //     //echo '<pre>';print_r($lecture_sheets);exit;
    //     return  json_encode(array('batch_details'=>view('ajax.batch_details',['batch'=>$batch])->render(),'lecture_sheet'=>view('ajax.lecture_sheet',['batch'=>$batch])->render()), JSON_FORCE_OBJECT);

    // }

    public function batch_details_modal(Request $request){
        $batch_id = $request->batch_id;
        $batch = Batches::where(['id'=>$batch_id])->first();
        return view('ajax.batch_details_modal',[
            'batch'=>$batch,
        ]);
    }

    public function ajax_lecture_sheets(Request $request)
    {
        $year = $request->year;
        $session_id = $request->session_id;
        $institute_id = $request->institute_id;
        $course_id = $request->course_id;
        $topic_id = $request->topic_id;

        $lecture_sheets = LectureSheet::where(['year'=>$year,'session_id'=>$session_id,'institute_id'=>$institute_id,'course_id'=>$course_id,'topic_id'=>$topic_id])->get();
        //echo '<pre>';print_r($lecture_sheets);exit;
        return  json_encode(array('lecture_sheets'=>view('lecture_sheet.ajax_lecture_sheets',['lecture_sheets'=>$lecture_sheets])->render(),), JSON_FORCE_OBJECT);

    }

    public function batch_lecture(Request $request)
    {
        $batch_id = $request->batch_id;
        $lecture_id = OnlineLectureLink::select('*')->where('batch_id',$batch_id)->get();
        return view('admin.ajax.batch_lecture',['lectures'=>$lecture_id]);

    }

    public function course_batch(Request $request)
    {
        $course_id = $request->course_id;
        $batch = Batches::select('*')->where('course_id',$course_id)->get()->pluck('name', 'id');
        return view('ajax.course_batch',['batch'=>$batch]);

    }

    public function batch_lecture_video(Request $request)
    {
        $batch_id = $request->batch_id;
        $video = LectureVideoBatch::select('*')->where('batch_id',$batch_id)->first();
        return view('ajax.batch_lecture_video',['video'=>$video, 'doctor_course_id'=>'']);
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

    public function division_district(Request $request)
    {
        $division_id = $request->division_id;
        $districts = Districts::get()->where('division_id',$division_id)->pluck('name', 'id');
        return view('ajax.division_district',['districts'=>$districts]);
    }

    // public function district_upazila(Request $request)
    // {
    //     $district_id = $request->district_id;
    //     $upazilas = Upazilas::get()->where('district_id',$district_id)->pluck('name', 'id');
    //     return view('ajax.district_upazila',['upazilas'=>$upazilas]);
    // }

    public function district_upazila(Request $request)
    {
        $district_id = $request->district_id;
        $batch = Batches::where('id' , $request->batch_id)->first();
        $package_id =  $batch->package_id;
        $courier_charge=CourierChargePackage::Where('id',$package_id)->first();
        if( $request->district_id == 1){
            $courier= $courier_charge->inside_dhaka ?? '150' ;
        }else{
            $courier= $courier_charge->outside_dhaka ?? '150' ;
        }
        $upazilas = Upazilas::get()->where('district_id',$district_id)->pluck('name', 'id');
        return json_encode(array('upazila'=>(String)View::make('ajax.district_upazila',['upazilas'=>$upazilas]),'courier'=>$courier));
    }


    private function last_question( DoctorExam &$doctor_exam, $skip = false ){

        $skips =  json_decode( $doctor_exam->skips, true ) ?? [];

        $skip_first = isset( $skips[0] ) ? $skips[0] : $doctor_exam->last_question;

        if( $doctor_exam->status == 'Skip-Running' ) {
            array_shift($skips );
        }

        $doctor_exam->last_question = isset( $skips[0] ) ? $skips[0] : $doctor_exam->last_question;

        if( $skip ) {
            $skips[] = $skip_first;
        }

        $skips = array_unique( $skips );

        $doctor_exam->skips = json_encode( $skips );
        $doctor_exam->save();

        return $doctor_exam->last_question;
    }


    protected function update_answer_file( DoctorExam $doctor_exam, $question_id, $exam_question_id, $type = 1, $ans = null ) {


        $answers = ExamController::get_exam_answers( $doctor_exam, $file);



        if( $file ) {
            $answers[ $question_id ] = [
                'exam_question_id' => $exam_question_id,
                'answer' => $ans,
                'question_type' => $type,
            ];

            $answer_file = fopen( $file, "w" ) or die("Unable to open file!");
            $txt = json_encode( $answers );
            fwrite( $answer_file, $txt );
            fclose( $answer_file );
        }

        //dd( '__________END_OF_FUNCTION_____________');

    }

    public function submit_answer( Request $request ){

        $redirect = '';
        $doctor_course_id = $request->doctor_course_id;
        $exam_question_id = $request->exam_question_id;

        $exam_id = $request->exam_id;
        $data['exam_finish'] = "Next";
        $data['doctor_course_id'] = $doctor_course_id;
        $data['exam_id'] = $exam_id;
        $data['options'] = [ 'A', 'B', 'C', 'D', 'E', 'F' ];
        //$data['total_questions'] = ExamController::total_question( $exam_id );


        $doctor_exam = DoctorExam::where(['exam_id' => $exam_id, 'doctor_course_id' => $doctor_course_id ])->first();
        $exam_question = ExamController::get_question( $exam_id, $doctor_exam->last_question - 1, $data['total_questions'] );
        $answers = ExamController::get_exam_answers( $doctor_exam, $file);

        //dd( $answers, $exam_question );

        if ( $doctor_exam->last_question - 1 < $data['total_questions'] ) {

            if ( isset( $exam_question['exam_question_id'] ) && !isset($answers[$exam_question['id']]) ) {

                if ($exam_question['question_type'] == 1 || $exam_question['question_type'] == 3) {

                    $ans_a = isset($request->ans_a) ? $request->ans_a : '.';
                    $ans_b = isset($request->ans_b) ? $request->ans_b : '.';
                    $ans_c = isset($request->ans_c) ? $request->ans_c : '.';
                    $ans_d = isset($request->ans_d) ? $request->ans_d : '.';
                    $ans_e = isset($request->ans_e) ? $request->ans_e : '.';

                    $answer = $ans_a . $ans_b . $ans_c . $ans_d . $ans_e;

                    $this->update_answer_file( $doctor_exam, $exam_question['id'], $exam_question['exam_question_id'], $exam_question['question_type'], $answer );
                    //DoctorAnswers::insert([ 'exam_id' => $exam_id, 'exam_question_id' => $exam_question['exam_question_id'], 'doctor_course_id' => $doctor_course_id, 'answer' => $answer ]);
                } else if ($exam_question['question_type'] == 2 || $exam_question['question_type'] == 4) {
                    $ans_sba = isset($request->ans_sba) ? $request->ans_sba : '.';
                    $this->update_answer_file( $doctor_exam, $exam_question['id'], $exam_question['exam_question_id'], $exam_question['question_type'], $ans_sba );
                    //DoctorAnswers::insert([ 'exam_id' => $exam_id, 'exam_question_id' => $exam_question['exam_question_id'], 'doctor_course_id' => $doctor_course_id, 'answer' => $ans_sba ]);
                }
            }
        }


        $skips = json_decode( $doctor_exam->skips, true ) ?? [];
        $num_of_questions  = $data['total_questions'];
        $last_question = $doctor_exam->last_question;

        if( $doctor_exam->status == 'Skip-Running' ) {

//            dd( $skips );
            if( count( $skips ) > 0 ) {
                $last_question = $this->last_question( $doctor_exam );
                $skips = json_decode($doctor_exam->skips, true) ?? [];
            }

            if( count( $skips ) == 1 ) {
                $data[ 'exam_finish' ] = "Finish";
            }elseif (count($skips) == 0 ) {
                $data['exam_finish'] = "Finished";
            }else {
                $data['exam_finish'] = "Next";
            }


            $data['serial_no'] = $doctor_exam->last_question;
            $current_question = ExamController::get_question( $exam_id, $doctor_exam->last_question - 1 );

        } else {

            $data['exam_finish'] =
                ($doctor_exam->status == 'Skip-Running' && count( $skips ) == 1  ) ? "Finish" : $data['exam_finish'];

            if ( $num_of_questions == $doctor_exam->last_question ) {
                if( count( $skips ) ) {
                    $last_question = $this->last_question( $doctor_exam );
                    $skips = json_decode($doctor_exam->skips, true) ?? [];
                    $doctor_exam->status = 'Skip-Running';
                } else {
                    $data[ 'exam_finish' ] = 'Finished';
                }
            }else {
                if( $num_of_questions - 1 == $doctor_exam->last_question && $doctor_exam->status == 'Running' && count( $skips ) == 0 ) {
                    $data['exam_finish'] = 'Finish';
                }
                $doctor_exam->last_question++;
                $last_question = $doctor_exam->last_question;
            }


//            $current_question = Exam_question::where( 'exam_id', $exam_id )->limit(1)->offset( $last_question - 1 )->first();
            $current_question = ExamController::get_question( $exam_id, $last_question - 1 );
            $data['serial_no'] = $doctor_exam->last_question;
        }


        $data['exam_question'] = $current_question;
        $doctor_exam->save( );


        $return_data = array('question' =>
            view('ajax.exam', $data)->render(),
            'current' => $current_question ?? [],
            'totalSkip' => count($skips) ?? 0,
        );


        if( $data['exam_finish'] === 'Finished' ) {
            $return_data[ 'redirect' ] = "/course-exam-result-submit/" . $doctor_course_id . "/" . $exam_id . '/'.($request->schedule_id ?? 0);
        }


        return  json_encode( $return_data, JSON_FORCE_OBJECT );

    }

    public function skip_question(Request $request)
    {

        $redirect = '';
        $data['exam_finish'] = "Next";
        $doctor_course_id = $request->doctor_course_id;
        $exam_question_id = $request->exam_question_id;
        $exam_id = $request->exam_id;
        $data['total_questions'] = ExamController::total_question( $exam_id );
        $data['options'] = [ 'A', 'B', 'C', 'D', 'E', 'F' ];
        $data['exam_id'] = $exam_id;

        $doctor_exam = DoctorExam::where(['exam_id' => $exam_id, 'doctor_course_id' => $doctor_course_id ])->first();

        $num_of_questions  = $data['total_questions'];

        $last_question = $doctor_exam->last_question;
        $skips = json_decode( $doctor_exam->skips, true ) ?? [];

        if($doctor_exam->status == 'Skip-Running' ) {
            if( count( $skips ) > 0 ) {
                $last_question = $this->last_question( $doctor_exam, true );
                $data[ 'exam_finish' ] = count( $skips) === 1 ?  "Finish" : $data[ 'exam_finish' ];
                $data[ 'serial_no'] = $last_question;
            }
            $current_question = ExamController::get_question( $exam_id, $last_question - 1 );;
        } else {

            $skips[] = $last_question;
            $doctor_exam->skips = json_encode( $skips );

            if( $num_of_questions == $doctor_exam->last_question ) {
                $last_question = $this->last_question( $doctor_exam, true );
                $doctor_exam->status = 'Skip-Running';
            } else {
                $doctor_exam->last_question++;
                $last_question = $doctor_exam->last_question;
            }

            $doctor_exam->save();

            $skips = json_decode($doctor_exam->skips, true) ?? [];
            $data['exam_finish'] = ( $num_of_questions == $last_question && count( $skips) == 0 ) ? "Finish" : $data['exam_finish'];
            $data['serial_no'] = $doctor_exam->last_question;
            $current_question = ExamController::get_question( $exam_id, $last_question - 1 );;
        }

        $data['exam_question'] = $current_question;
        $data['doctor_course_id'] = $doctor_course_id;

        return  json_encode(
            array(
                'question'  => view('ajax.exam', $data)->render(),
                'current'   => $data['exam_question'],
                'totalSkip' => count($skips) ?? 0,
            ),
            JSON_FORCE_OBJECT
        );

    }


    public function submit_answer_and_terminate_exam(Request $request)
    {
        $doctor_course_id = $request->doctor_course_id;
        $exam_question_id = $request->exam_question_id;
        $exam_id = $request->exam_id;
        $exam_question = Exam_question::find($exam_question_id);
        $exam = Exam::find($exam_id);

        $doctor_exam = DoctorExam::where(['exam_id' => $exam_id, 'doctor_course_id' => $doctor_course_id ])->first();
        $exam_question = ExamController::get_question( $exam_id, $doctor_exam->last_question - 1, $total_questions );

        if ( $doctor_exam->last_question - 1 < $total_questions ) {

            if ( isset( $exam_question['exam_question_id'] ) && !isset($answers[$exam_question['id']]) ) {

                if ($exam_question['question_type'] == 1 || $exam_question[ 'question_type' ] == 3) {

                    $ans_a = isset($request->ans_a) ? $request->ans_a : '.';
                    $ans_b = isset($request->ans_b) ? $request->ans_b : '.';
                    $ans_c = isset($request->ans_c) ? $request->ans_c : '.';
                    $ans_d = isset($request->ans_d) ? $request->ans_d : '.';
                    $ans_e = isset($request->ans_e) ? $request->ans_e : '.';

                    $answer = $ans_a . $ans_b . $ans_c . $ans_d . $ans_e;

                    $this->update_answer_file( $doctor_exam, $exam_question['id'], $exam_question['exam_question_id'], $exam_question[ 'question_type' ], $answer );
                } else if ($exam_question['question_type'] == 2 || $exam_question[ 'question_type' ] == 4) {
                    $ans_sba = isset($request->ans_sba) ? $request->ans_sba : '.';
                    $this->update_answer_file( $doctor_exam, $exam_question['id'], $exam_question['exam_question_id'], $exam_question[ 'question_type' ], $ans_sba );
                }
            }
        }

        $this->course_exam_result_submit($doctor_course_id,$exam_id);
        return  json_encode(array('redirect'=>'/course-exam-result/'.$doctor_course_id.'/'.$exam->id .'/'. ( $request->schedule_id ?? 0 ) ), JSON_FORCE_OBJECT);
    }

    public function course_exam_result_submit( $doctor_course_id, $exam_id ) {

        $exam = Exam::find( $exam_id );
        if( !$exam ) return;

        $exam = $exam->prepare_result( $doctor_course_id );
//        $exam = new Exam();

        $result = Result::where( [ 'exam_id'=>$exam_id,'doctor_course_id'=> $doctor_course_id ])->first();

        $doctorResultData = [
            'exam_id' => $exam_id,
            'doctor_course_id'=> $doctor_course_id,
            'subject_id'=> $exam->doctor_course->subject_id ?? '',
            'batch_id'=> $exam->doctor_course->batch_id ?? '',
            'correct_mark'=> $exam->getCorrectMark( ),
            'negative_mark'=> $exam->getNegativeMark( ),
            'obtained_mark'=> $exam->getObtainedMark( ),
            'obtained_mark_percent'=> $exam->getObtainedMarkPercent(),
            'obtained_mark_decimal'=> $exam->getObtainedMark( ) * 10,
            'wrong_answers'=> $exam->getWrongAnswerCount( ),
        ];

        if( !isset($result) ) {
            Result::insert( $doctorResultData );
        } elseif(isset($result)) {
            Result::where(['exam_id'=>$exam_id,'doctor_course_id'=> $doctor_course_id ])->update( $doctorResultData );
        }

        $this->update_exam_status($doctor_course_id,$exam_id,$status="Completed");
    }


    public function update_exam_status($doctor_course_id, $exam_id, $status)
    {
        DoctorExam::where(['exam_id' => $exam_id, 'doctor_course_id' => $doctor_course_id])->update(['status' => $status]);
    }

    public function apply_discount_code(Request $request)  
    {
        $d = Discount::where([ 'discount_code'=> $request->discount_code, 'batch_id' => $request->batch_id, 'doctor_id' => Auth::guard('doctor')->id(), 'used'=>0 ,'status'=> 1 ] );
        if( $d->exists( ) ) {
            $discount_code =$d->first( );
            if(strtotime("now") - strtotime($discount_code->created_at ) < ($discount_code->code_duration * 3600))
            {
                return response( [ 'amount' => $discount_code->amount, 'valid' => true ] );
            }
        }

        return response( [ 'amount' => 0, 'valid' => false ] );

        // return response( [ 'a' => $request->batch_id, 'coupon_code' => $request->coupon_code ] );
    }


    public function search_college(Request $request)
    {
        $text =  $_GET['term'];
        $text = $text['term'];

        $data = MedicalColleges::select(DB::raw("CONCAT(name,' - ') AS name"),'id')
            ->where('name', 'like', '%'.$text.'%')
            ->orWhere('id', 'like', '%'.$text.'%')
            ->get();
        //$data = DB::table('institution')->where('institution_type_id',$content_section_id)->where('name', 'like', $text.'%')->get();
        echo json_encode( $data);
    }


}
