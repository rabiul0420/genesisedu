<?php

namespace App\Http\Controllers;

use App\BatchesSchedules;
use App\DoctorCourseLectureSheet;
use App\Exam;
use App\Notice;
use App\NoticeCourse;
use App\NoticeYear;
use App\Exam_question;
use App\LectureSheetTopicBatch;
use App\OnlineLectureAddress;
use App\OnlineLectureLink;
use App\OnlineExamCommonCode;
use App\OnlineExamLink;
use App\Page;
use App\Providers\AppServiceProvider;
use App\QuestionTypes;
use App\Result;
use App\Sessions;
use App\SmsLog;
use App\Subjects;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Doctors;
use App\Batches;
use App\Courses;
use App\DoctorsCourses;
use App\DoctorQuestion;
use App\DoctorQuestionReply;
use App\DoctorComplain;
use App\DoctorComplainReply;
use App\Faculty;
use App\Notices;
use App\DoctorNotices;
use App\DoctorNoticeView;
use App\NoticeBatch;
use App\Otp;
use App\PaymentInfo;
use App\Institutes;
use App\LectureVideo;
use App\Divisions;
use App\Districts;
use App\Upazilas;
use App\DoctorsReviews;
use App\AvailableBatches;
use App\MedicalColleges;
use App\Advertisements;
use Carbon\Carbon;
use App\ExamBatchExam;
use App\BannerSlider;
use Jenssegers\Agent\Agent;
use Validator;
use Auth;
use Illuminate\Support\Facades\Hash;
use App\DoctorAsks;
use App\DoctorAskReply;
use App\DoctorCoursePayment;
use App\NoticeBoard;
use App\DiscountRequest;
use App\Discount;
use App\DiscountRequestNumber;
use App\DoctorCourseManualPayment;
use App\DoctorCourseScheduleDetails;
use App\LectureSheetDeliveryStatus;
use App\MedicalCollege;
use App\Quiz;
use App\ScheduleDetails;
use App\SendSms;
use App\Setting;
use App\SiteSetup;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Session;
use PDF;
use PhpParser\Comment\Doc;
use Illuminate\Support\Facades\Session as Sess;

class HomeController extends Controller
{
    use SendSms;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:doctor')->except( 'payment_details_pdf_public' );
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['courses'] = Cache::rememberForever(self::HOME_PAGE_COURSE, function () {
            return Courses::where('status',1)->orderBy('priority', 'asc')->get();
        });

        $data['medical_colleges'] = Cache::rememberForever(self::HOME_PAGE_MEDICAL_COLLEGE, function () {
            return MedicalColleges::orderBy('name')->pluck('name','id');
        });

        $data['batches'] = Cache::rememberForever('Home_Page_AvailableBatches', function () {
            return AvailableBatches::query()
                ->selectRaw("`id`,`course_name`,`batch_name`,`start_date`,`days`,`time`,`batch_id`")
                ->with('batch:id,name')
                ->where('status', 1)
                ->orderBy('id', 'desc')
                ->limit(5)
                ->get();
        });

        $data['noticeBoards'] = NoticeBoard::where('status', 1)->latest()->take(4)->get();

        $quizzes = Quiz::query()
            ->with([
                'quiz_property',
                'quiz_questions:id,quiz_id',
                'quiz_participants' => function($query) {
                    $query
                        ->select([
                            'id',
                            'doctor_id',
                            'quiz_id',
                            'obtained_mark',
                            'coupon',
                        ])
                        ->whereNotNull('doctor_id')
                        ->where('doctor_id', Auth::guard('doctor')->id());
                }
            ])
            ->published()
            ->latest()
            ->take(3)
            ->get();

        $data["quizzes"] = $quizzes->filter(function($quiz) {
            return $quiz->quiz_property->total_question == $quiz->quiz_questions->count();
        });
        
        return view('home', $data);
    }

    public function doctor_of_the_day()
    {

        $from = Carbon::now()->subDays(2);
        $to = Carbon::now()->subDays(1);
        $exams = ExamBatchExam::whereBetween('created_at', [$from, $to])->get();
        foreach($exams as $k => $value){
            $value->info = Result::where([
                'batch_id'=>$value->batch_exam->batch_id,
                'exam_id'=>$value->exam_id
            ])->orderBy('obtained_mark','desc')->first();
        }
        $data['exams'] = $exams;

        dd($data['exams']);


        exit;



        $data = DB::table('doctor_of_the_day')
                ->get();
        return view ('doctor_of_the_day',['data'=>$data]);
    }

    public function dashboard()
    {
        $count = 0;

        
        $doc_info = DB::table('doctors')
        ->join('doctors_courses','doctors_courses.doctor_id','doctors.id')
        ->join('batches','doctors_courses.batch_id','batches.id')
        ->join('lecture_sheet_delivery_status','lecture_sheet_delivery_status.doctor_course_id','doctors_courses.id')
        ->where('doctors.id', Auth::guard('doctor')->id())
        ->where('lecture_sheet_delivery_status.lecture_sheet_delivery_status','In_Courier')
        ->select('doctors_courses.*','doctors.*','lecture_sheet_delivery_status.*','batches.name','batches.shipment')
        ->orderby('doctors_courses.id','desc')
        ->first();
        $data['doc_info'] = $doc_info;
        $data['medical_colleges'] = MedicalCollege::pluck('name','id');
        
        if( $data['doc_info'] ) {
            $data['first_shipment'] = LectureSheetDeliveryStatus::with('courier')->where(['doctor_course_id'=>$doc_info->doctor_course_id,'shipment'=>'first'])->first();
            $data['second_shipment'] = LectureSheetDeliveryStatus::with('courier')->where(['doctor_course_id'=>$doc_info->doctor_course_id,'shipment'=>'second'])->first();
        }
        // else {
        //     $data['first_shipment'] = new LectureSheetDeliveryStatus();
        //     $data['second_shipment'] = new LectureSheetDeliveryStatus();
        // }



        
        $count = 0;
        $data['count'] = $count;
        $data['doc_info'] = $doc_info;

        $doctor_id = Auth::guard('doctor')->id();
        $data['doctor'] =  Doctors::where(['id' => $doctor_id , 'bmdc_no' => null])->first();

        $data['modal_add_image'] = Setting::property('dashboard_modal_image')->value('value') ?? '';

        return view('dashboard', $data);
    }

    public function my_profile()
    {
        $doc_info = Doctors::where('id', Auth::guard('doctor')->id())->first();
        foreach($doc_info->doctorcourses as $single){
            $doc_info->schedule_id = BatchesSchedules::where(['year'=>$single->year,'session_id'=>$single->session_id,'course_id'=>$single->course_id])->value('id');
        }
        $data['doc_info'] = $doc_info;
        return view('my_profile', $data);
    }

    public function edit_profile($id)
    {
        $data['doc_info'] = Doctors::where('id', Auth::guard('doctor')->id())->first();
        $data['divisions'] = Divisions::pluck('name', 'id');
        $data['present_districts'] = Districts::where(['division_id'=>$data['doc_info']->present_division_id])->pluck('name', 'id');
        $data['present_upazilas'] = Upazilas::where(['district_id'=>$data['doc_info']->present_district_id])->pluck('name', 'id');
        return view('edit_profile', $data);
    }

    public function update_profile(Request $request)
    {
        $profile = Doctors::find(Auth::guard('doctor')->id());
        $profile->name=$request->doc_name;
        $profile->bmdc_no=$request->bmdc_no;
        $profile->father_name=$request->father_name;
        $profile->mother_name=$request->mother_name;
        $profile->mobile_number=$request->mobile_number;
        $profile->email=$request->email;

        $profile->facebook_id=$request->facebook_id;
        $profile->date_of_birth=$request->date_of_birth;
        $profile->job_description=$request->job_description;
        $profile->nid=$request->nid;
        $profile->passport=$request->passport;
        $profile->main_password=$request->main_password;
        $profile->password=Hash::make($request->password);
        $profile->present_division_id = $request->present_division_id;
        $profile->present_district_id = $request->present_district_id;
        $profile->present_upazila_id = $request->present_upazila_id;
        $profile->present_address = $request->present_address;
        if($request->hasFile('photo')){
            $file = $request->file('photo');
            $extension = $file->getClientOriginalExtension();
            $filename = $profile->bmdc_no.'_'.time().'.'.$extension;
            $file->move('upload/photo/',$filename);
            $profile->photo = 'upload/photo/'.$filename;
        }
        $profile->push();
        Session::flash('message', 'Record has been updated successfully');
        //return back();

        $doc_info = Doctors::where('id', Auth::guard('doctor')->id())->first();
        foreach($doc_info->doctorcourses as $single){
            $doc_info->schedule_id = BatchesSchedules::where(['year'=>$single->year,'session_id'=>$single->session_id,'course_id'=>$single->course_id])->value('id');
        }
        $data['doc_info'] = $doc_info;
        return view('my_profile', $data);
    }

    public function my_courses( Request $request )
    {
        $doc_info = Doctors::with(['doctorcourses' => function($query){
            $query->where('status','=','1');
        }]
            , 'doctorcourses.course'


            
            , 'doctorcourses.faculty'
            , 'doctorcourses.bcps_subject'
            , 'doctorcourses.session'
            , 'doctorcourses.batch' )
            ->where('id', Auth::guard('doctor')->id() )->first();
        foreach($doc_info->doctorcourses as $single){
            $doc_info->schedule_id = BatchesSchedules::where(['year'=>$single->year,'session_id'=>$single->session_id,'course_id'=>$single->course_id,'batch_id'=>$single->batch_id])->value('id');
        }
        $data['doc_info'] = $doc_info;
        //echo "<pre>";print_r($data);exit;
        return view('my_courses', $data);
    }




    public function my_orders($id)
    {

        $doctor_course = DoctorsCourses::with('batch')->where(['include_lecture_sheet'=>'1','id'=>$id])->first();

        if( $doctor_course ) {
            $first_shipment = LectureSheetDeliveryStatus::with('courier')->where(['doctor_course_id'=>$doctor_course->id,'shipment'=>'first'])->first();
            $second_shipment = LectureSheetDeliveryStatus::with('courier')->where(['doctor_course_id'=>$doctor_course->id,'shipment'=>'second'])->first();
        }else {
            $first_shipment = new LectureSheetDeliveryStatus();
            $second_shipment = new LectureSheetDeliveryStatus();
        }




        return view('my_orders',compact('doctor_course','first_shipment','second_shipment'));
    }

    public function lecture_sheet_delivery_feedback(Request $request){
        $lecture_sheet_feedback=LectureSheetDeliveryStatus::find($request->lecture_sheet_delivery_status_id);

        if($request->feedback == "লেকচার সীট সংগ্রহ করেছি"){
            $lecture_sheet_feedback->lecture_sheet_delivery_status = 'Completed';
            $lecture_sheet_feedback->feedback = $request->feedback;
            $lecture_sheet_feedback->push();
        }else{
            $lecture_sheet_feedback->feedback = $request->feedback;
            $lecture_sheet_feedback->push();
        }
        return redirect('lecture-sheet-article');
    }

    public function  edit_doctor_course_discipline($doctor_course_id){


        $data['doctor_course'] = DoctorsCourses::where('id',$doctor_course_id)->get();
        $doctor_course = $data['doctor_course'];


        $ins = new Institutes( );

        $institute = Institutes::where('id',$doctor_course->institute_id)->first();

//        dd( $institute );

        $data['institute_type'] = $institute->type;
        $data['is_combined'] = $institute->id == AppServiceProvider::$COMBINED_INSTITUTE_ID;

        if( $data[ 'institute_type' ] == 1 ){

            $data[ 'subjects' ] = Subjects::where('faculty_id', $doctor_course->faculty_id)->pluck( 'name', 'id' );

            if(  $data['is_combined'] ) {
                $data[ 'faculties' ] = Faculty::where('course_id', $doctor_course->course_id)->pluck( 'name', 'id' );
                $data[ 'bcps_subjects' ] = Subjects::where('faculty_id', $doctor_course->bcps_subject_id)->pluck( 'name', 'id' );
            }else {
                $data[ 'faculties' ] = Faculty::where('course_id', $doctor_course->course_id)->pluck( 'name', 'id' );
                $data[ 'bcps_subjects' ] = Subjects::where('faculty_id', $doctor_course->bcps_subject_id)->pluck( 'name', 'id' );
            }

        }else{
            $data['subjects'] = Subjects::where('course_id',$doctor_course->course_id)->pluck('name', 'id');
        }

        return view('edit_doctor_course_discipline',$data);

    }

    public function  update_doctor_course_discipline(Request $request, $id){

            $validator = Validator::make($request->all(), [
                'doctor_id' => ['required'],
                'institute_id' => ['required'],
                'course_id' => ['required'],
            ]);

            if ($validator->fails()){
                Session::flash('class', 'alert-danger');
                session()->flash('message','Please enter proper input values!!!');
                return redirect()->action('HomeController@edit_doctor_course_discipline',[$id])->withInput();
            }

            $doctor_course = DoctorsCourses::find($id);

            $doctor_course->doctor_id = $request->doctor_id;

            $doctor_course->institute_id = $request->institute_id;
            $doctor_course->course_id = $request->course_id;
            $doctor_course->faculty_id = $request->faculty_id;
            $doctor_course->subject_id = $request->subject_id;

            $doctor_course->updated_by = Auth::guard('doctor')->id();

            $doctor_course->push();

            Session::flash('message', 'Record has been updated successfully');

            return redirect()->action('HomeController@edit_doctor_course_discipline',[$id]);

        }

    public function payment_details()
    {


        
        $doc_info = Doctors::where('id', Auth::guard('doctor')->id())->first();
        $data['doc_info'] = $doc_info;
        $data['paid_amount'] = 0;
        $data['total_row'] = 0;
        $data['doc_info'] = $doc_info;
        $data['course_info'] = DoctorsCourses::where(['doctor_id'=>$doc_info->id])->orderBy('id','desc')->get();
        $last_reg = DoctorsCourses::select('*')->where('doctor_id', $doc_info->id)->orderBy('id', 'desc')->limit(1)->first();
        $data['last_reg'] = $last_reg;

        if(isset($last_reg->batch_id)){
            $data['last_reg_pay'] = Batches::select('*')->where('id', $last_reg->batch_id)->first();
        } else {
            $data['last_reg_pay'] = '';
        }

        foreach($data['course_info'] as $doctor_course)
        {
            if(isset($doctor_course->batch->fee_type))
            {
                $doctor_course->set_payment_status();
            }
            
            $discounts = $doctor_course->doctor->discount;
            if(isset($discounts) && count($discounts))
            {
                foreach($discounts as $discount)
                {
                    if($discount->batch_id == $doctor_course->batch_id && $discount->doctor_id == $doctor_course->doctor->id)
                    $doctor_course->discount = $discount->amount;
                    else
                    $doctor_course->discount = "";
                }
            }
            else
            {
                $doctor_course->discount = "";
            }
        }

        
        return view('payment_details', $data);
    }

    public function get_full_payment_waiver(Request $request)
    {
        //return "Bismillah";
        $data['doctor_course'] = DoctorsCourses::where('id',$request->doctor_course_id)->first();
        $data['batch'] = Batches::where('id', $data['doctor_course']->batch->id)->first();
        return view('payment_option_select', $data);
    }

    public function evaluate_teacher()
    {
        $data['doc_info'] = Doctors::where('id', Auth::guard('doctor')->id())->first();
        return view('evaluate_teacher', $data);
    }

    public function online_exam()

    {

        $doc_info = Doctors::where('id', Auth::guard('doctor')->id())->first();
        $doctor_courses = DoctorsCourses::where(['doctor_id'=>$doc_info->id,'is_trash'=>'0','status'=>'1'])->where('payment_status', '!=' , 'No Payment')->get();
        //echo '<pre>';print_r($doc_courses);exit;
        foreach($doc_info->doctorcourses as $single){
            $doc_info->schedule_id = BatchesSchedules::where(['year'=>$single->year,'session_id'=>$single->session_id,'course_id'=>$single->course_id])->value('id');
        }
        $data['doc_info'] = $doc_info;
        $data['doctor_courses'] = $doctor_courses;
        $online_exam_links = array();
        foreach($doctor_courses as $key=>$doctor_course){
            $exam_comm_code_ids = OnlineExamLink::where(['year'=>$doctor_course->year,'session_id'=>$doctor_course->session_id,'institute_id'=>$doctor_course->institute_id,'course_id'=>$doctor_course->course_id,'batch_id'=>$doctor_course->batch_id])->pluck('exam_comm_code_id');
            foreach ($exam_comm_code_ids as $id){
                $online_exam_links[$doctor_course->reg_no][] =  OnlineExamCommonCode::where('id',$id)->value('exam_comm_code');
            }
        }
        $data['online_exam_links'] = $online_exam_links;
        return view('online_exam', $data);

    }

    public function online_lecture()
    {

        $doc_info = Doctors::where('id', Auth::guard('doctor')->id())->first();
        $doctor_courses = DoctorsCourses::where(['doctor_id'=>$doc_info->id,'is_trash'=>'0','status'=>'1'])->where('payment_status', '!=' , 'No Payment')->get();

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
        $data['video_link'] = OnlineLectureAddress::select('*')->where('status', 1)->get();
        $agent =  new Agent();
        $data['browser'] = $agent->browser();
        return view('online_lecture', $data);

    }

    public function doctor_admission()
    {
        $data['doc_info'] = Doctors::where('id', Auth::guard('doctor')->id())->first();
        return view('doctor_admission', $data);
    }

    public function doctor_result()
    {
        $data['doc_info'] = Doctors::where('id', Auth::guard('doctor')->id())->first();
        $data['course_info'] = DoctorsCourses::with('doctor_exams')
            ->where('status','1')
            ->where('doctor_id', Auth::guard('doctor')->id())
            ->whereHas('doctor_exams', function ($query) {
                $query->where('status', 'Completed');
            })
            ->get();
        return view('result', $data);
    }

    public function course_result( $id )
    {
        $data['doctor_course'] = DoctorsCourses::findOrFail($id);
        $data['course_reg_no'] = DoctorsCourses::select('*')->where('id', $data['doctor_course']->id)->first();
        $data['results'] = Result::with('doctor_course')->select('*')->where('doctor_course_id', $data['doctor_course']->id)->get();
        foreach($data['results'] as $index => $result)
        {
            $candidate_possition[] = $this->candidate_position( $data['doctor_course'] , $result->exam_id, $result->obtained_mark );
        }

        $data[ 'candidate_possitions' ] = $candidate_possition;
        return view('course_result', $data);
    }

    function candidate_position( DoctorsCourses $doctor_course, $exam_id, $obtained_mark_single)
    {

        $subject_id = $doctor_course->subject_id;
        $candidate_type = $doctor_course->candidate_type;
        $subject_name = Subjects::where( 'id', $subject_id )->value( 'name' );
        $results = Result::with('doctor_course')
            ->join('subjects', 'subjects.id', '=', 'results.subject_id')
            ->where(['subjects.name' => $subject_name,  'exam_id' => $exam_id])
            ->orderBy('obtained_mark', 'desc')->get();
        $candidate_results = $results->where('doctor_course.candidate_type', $candidate_type );

        $obtained_mark = 0;
        $possition = 0;
        $i = 0;

        foreach ($candidate_results as $k => $row) {
            if ($obtained_mark != $row->obtained_mark) {
                $p = ($i + 1);
                $th = ($p == 1) ? 'st' : (($p == 2) ? 'nd' : (($p == 3) ? 'rd' : 'th'));
                $pos = [$exam_id,$p . $th];
                $i++;
            } else {
                // $pos = $possition;
                $pos =[$exam_id , $possition];
            }

            $obtained_mark = $row->obtained_mark;
            $possition = $row->possition;

            if($row->obtained_mark == $obtained_mark_single){
                return $pos;
            }
        }
    }

    public function result()
    {
        $data['doc_info'] = Doctors::where('id', Auth::guard('doctor')->id())->first();
        return view('result', $data);
    }

    public function schedule()
    {
        $doc_info = Doctors::where('id', Auth::guard('doctor')->id())->first();

        foreach($doc_info->doctorcourses as $single){
            $single->schedule = BatchesSchedules::where(['year'=>$single->year,'session_id'=>$single->session_id,'course_id'=>$single->course_id,'batch_id'=>$single->batch_id])->first();
        }
        $data['doc_info'] = $doc_info;
        return view('schedule', $data);
    }

    public function unread_notices()
    {
        $data['doc_info'] = Doctors::where('id', Auth::guard('doctor')->id())->first();

        $all_notices = array();

        $doctor_notice = Notice::where('doctor_notice.doctor_id', Auth::guard('doctor')->id())
                                    ->join('doctor_notice','doctor_notice.notice_id','notice.id')
                                    ->whereNull('doctor_notice.deleted_at')
                                    ->get();
        if(count($doctor_notice))
        {
            $all_notices[] = $doctor_notice;
        }
        $doctor_courses = DoctorsCourses::where(['doctor_id'=>Auth::guard('doctor')->id()])->get();
        $notice_batch_id = '';

        foreach($doctor_courses as $doctor_course)
        {
            if(NoticeBatch::where(['year'=>$doctor_course->year,'session_id'=>$doctor_course->session_id,'notice_batch.institute_id'=>$doctor_course->institute_id,'notice_batch.course_id'=>$doctor_course->course_id,'batch_id'=>$doctor_course->batch_id])->first()){

                $notice_batch_id = NoticeBatch::where(['year'=>$doctor_course->year,'session_id'=>$doctor_course->session_id,'notice_batch.institute_id'=>$doctor_course->institute_id,'notice_batch.course_id'=>$doctor_course->course_id,'batch_id'=>$doctor_course->batch_id])->value('id');

            }

            //dd($doctor_course);

            if(isset($doctor_course->batch->fee_type))
            {
                if($doctor_course->batch->fee_type == "Batch")
                {
                    $notice_batch = NoticeBatch::select('notice.*','notice_batch_notice.created_at as created_time')->where('notice.type','B')->where('notice_batch.id', $notice_batch_id)
                                    ->join('notice_batch_notice','notice_batch_notice.notice_batch_id','notice_batch.id')
                                    ->join('notice','notice.id','notice_batch_notice.notice_id')
                                    ->orderBy('notice_batch_notice.id','desc')
                                    ->whereNull( 'notice_batch_notice.deleted_at' )
                                    ->whereNull( 'notice.deleted_at' )
                                    ->paginate(10);

                    if(count($notice_batch))
                    {
                        $all_notices[] = $notice_batch;
                    }

                }
                else if($doctor_course->batch->fee_type == "Discipline_Or_Faculty")
                {

                    if($doctor_course->institute->type == 1)
                    {

                        $faculty_name = Faculty::where('id',$doctor_course->faculty_id)->value('name');
                        $faculty_ids = Faculty::where('name',$faculty_name)->pluck('id');
                        //dd($subject_ids);
                        $fac_notices = NoticeBatch::select('notice.*','notice_batch_notice.created_at as created_time')->where('notice.type','B')->where('notice_batch.id',$notice_batch_id)->whereIn('notice_faculties.faculty_id',$faculty_ids)
                            ->join('notice_batch_notice','notice_batch_notice.notice_batch_id','notice_batch.id')
                            ->join('notice','notice.id','notice_batch_notice.notice_id')
                            ->join('notice_faculties','notice_faculties.notice_id','notice.id')
                            ->orderBy('notice_batch_notice.id','desc')
                            ->whereNull( 'notice_batch_notice.deleted_at' )
                            ->whereNull( 'notice_faculties.deleted_at' )
                            ->whereNull( 'notice.deleted_at' )
                            //->join('batch_discipline_fees','batch_discipline_fees.batch_id','notice_batch.batch_id')
                            ->paginate(10);

                        if(count($fac_notices))
                        {
                            $all_notices[] = $fac_notices;
                        }

                    }
                    else
                    {
                        $subject_name = Subjects::where('id',$doctor_course->subject_id)->value('name');
                        $subject_ids = Subjects::where('name',$subject_name)->pluck('id');
                        //dd($subject_ids);
                        $dis_notices = NoticeBatch::select('notice.*','notice_batch_notice.created_at as created_time')->where('notice.type','B')->where('notice_batch.id',$notice_batch_id)->whereIn('notice_disciplines.subject_id',$subject_ids)
                            ->join('notice_batch_notice','notice_batch_notice.notice_batch_id','notice_batch.id')
                            ->join('notice','notice.id','notice_batch_notice.notice_id')
                            ->join('notice_disciplines','notice_disciplines.notice_id','notice.id')
                            ->orderBy('notice_batch_notice.id','desc')
                            ->whereNull( 'notice_batch_notice.deleted_at' )
                            ->whereNull( 'notice_disciplines.deleted_at' )
                            ->whereNull( 'notice.deleted_at' )
                            //->join('batch_discipline_fees','batch_discipline_fees.batch_id','notice_batch.batch_id')
                            ->paginate(10);

                        if(count($dis_notices))
                        {
                            $all_notices[] = $dis_notices;
                        }

                    }

                }
            }

            unset($notice_course);
            if(NoticeCourse::where(['year'=>$doctor_course->year,'session_id'=>$doctor_course->session_id,'notice_course.institute_id'=>$doctor_course->institute_id,'notice_course.course_id'=>$doctor_course->course_id])->first()){

                $notice_course = NoticeCourse::where(['year'=>$doctor_course->year,'session_id'=>$doctor_course->session_id,'notice_course.institute_id'=>$doctor_course->institute_id,'notice_course.course_id'=>$doctor_course->course_id])->first();

            }

            if( isset($notice_course) && $doctor_course->course->id == $notice_course->course_id)
            {

                if($doctor_course->institute->type == 1)
                {

                    $faculty_name = Faculty::where('id',$doctor_course->faculty_id)->value('name');
                    $faculty_ids = Faculty::where('name',$faculty_name)->pluck('id');
                    //dd($subject_ids);
                    unset($fac_notices);
                    $fac_notices = NoticeCourse::select('notice.*','notice_course_notice.created_at as created_time')->where('notice.type','C')->where('notice_course.id',$notice_course->id)->whereIn('notice_faculties.faculty_id',$faculty_ids)
                        ->join('notice_course_notice','notice_course_notice.notice_course_id','notice_course.id')
                        ->join('notice','notice.id','notice_course_notice.notice_id')
                        ->join('notice_faculties','notice_faculties.notice_id','notice.id')
                        ->orderBy('notice_course_notice.id','desc')
                        ->whereNull( 'notice_faculties.deleted_at' )
                        ->whereNull( 'notice.deleted_at' )
                        //->join('batch_discipline_fees','batch_discipline_fees.batch_id','notice_batch.batch_id')
                        ->paginate(10);

                    if(count($fac_notices))
                    {
                        $all_notices[] = $fac_notices;
                    }

                }
                else
                {
                    $subject_name = Subjects::where('id',$doctor_course->subject_id)->value('name');
                    $subject_ids = Subjects::where('name',$subject_name)->pluck('id');
                    //dd($subject_ids);
                    unset($dis_notices);
                    $dis_notices = NoticeCourse::select('notice.*','notice_course_notice.created_at as created_time')->where('notice.type','C')->where('notice_course.id',$notice_course->id)->whereIn('notice_disciplines.subject_id',$subject_ids)
                        ->join('notice_course_notice','notice_course_notice.notice_course_id','notice_course.id')
                        ->join('notice','notice.id','notice_course_notice.notice_id')
                        ->join('notice_disciplines','notice_disciplines.notice_id','notice.id')
                        ->orderBy('notice_course_notice.id','desc')
                        ->whereNull( 'notice_disciplines.deleted_at' )
                        ->whereNull( 'notice.deleted_at' )
                        //->join('batch_discipline_fees','batch_discipline_fees.batch_id','notice_batch.batch_id')
                        ->paginate(10);

                    if(count($dis_notices))
                    {
                        $all_notices[] = $dis_notices;
                    }

                }

            }

            unset($notice_year);
            if(NoticeYear::where(['year'=>$doctor_course->year])->first()){

                $notice_year = NoticeYear::where(['year'=>$doctor_course->year])->first();

            }

            if( isset($notice_year) && $doctor_course->year == $notice_year->year)
            {
                unset($notice_all);
                $notice_all = Notice::select('notice.*')->where(['notice.status'=>1,'notice.type'=>'A','notice_year.year'=>$doctor_course->year])
                                ->join('notice_year_notice','notice_year_notice.notice_id','notice.id')
                                ->join('notice_year','notice_year.id','notice_year_notice.notice_year_id')
                                ->get();

                if(count($notice_all))
                {
                    $all_notices[] = $notice_all;
                }

            }

        }

        $data['notice_read'] = DoctorNoticeView::where('doctor_id', Auth::guard('doctor')->id())->pluck('notice_id')->toArray();

        $unread_notices = array();
        $read_notices = array();
        $notice_ids = array();
        foreach($all_notices as $notices)
        {
            foreach($notices as $notice)
            {
                if(!in_array($notice->id,$notice_ids))
                {
                    if(in_array($notice->id,$data['notice_read']))
                    {
                        $read_notices[] = $notice;
                    }
                    else
                    {
                        $unread_notices[] = $notice;
                    }

                }

                $notice_ids[] = $notice->id;

            }
        }

        $data['all_notices'] = array_merge($unread_notices,$read_notices);

        return count($unread_notices);
    }

    public function notice()
    {
        $data['doc_info'] = Doctors::where('id', Auth::guard('doctor')->id())->first();

        $all_notices = array();

        $doctor_notice = Notice::select('notice.*')->where('doctor_notice.doctor_id', Auth::guard('doctor')->id())
                                    ->join('doctor_notice','doctor_notice.notice_id','notice.id')
                                    ->whereNull('doctor_notice.deleted_at')
                                    ->get();

        if(count($doctor_notice))
        {
            $all_notices[] = $doctor_notice;
        }
        $doctor_courses = DoctorsCourses::where(['doctor_id'=>Auth::guard('doctor')->id()])->get();
        $notice_batch_id = '';

        foreach($doctor_courses as $doctor_course)
        {
            unset($notice_batch);
            if(NoticeBatch::where(['year'=>$doctor_course->year,'session_id'=>$doctor_course->session_id,'notice_batch.institute_id'=>$doctor_course->institute_id,'notice_batch.course_id'=>$doctor_course->course_id,'batch_id'=>$doctor_course->batch_id])->first()){

                $notice_batch = NoticeBatch::where(['year'=>$doctor_course->year,'session_id'=>$doctor_course->session_id,'notice_batch.institute_id'=>$doctor_course->institute_id,'notice_batch.course_id'=>$doctor_course->course_id,'batch_id'=>$doctor_course->batch_id])->first();

            }

            if(isset($doctor_course->batch->fee_type) && isset($notice_batch) && $doctor_course->batch->id == $notice_batch->batch_id)
            {

                if($doctor_course->batch->fee_type == "Batch")
                {
                    $notice_batch = NoticeBatch::select('notice.*','notice_batch_notice.created_at as created_time')->where('notice.type','B')->where('notice_batch.id', $notice_batch->id)
                                    ->join('notice_batch_notice','notice_batch_notice.notice_batch_id','notice_batch.id')
                                    ->join('notice','notice.id','notice_batch_notice.notice_id')
                                    ->orderBy('notice_batch_notice.id','desc')
                                    ->whereNull( 'notice_batch_notice.deleted_at' )
                                    ->whereNull( 'notice.deleted_at' )
                                    ->paginate(10);

                    if(count($notice_batch))
                    {
                        $all_notices[] = $notice_batch;
                    }

                }
                else if($doctor_course->batch->fee_type == "Discipline_Or_Faculty")
                {

                    if($doctor_course->institute->type == 1)
                    {

                        $faculty_name = Faculty::where('id',$doctor_course->faculty_id)->value('name');
                        $faculty_ids = Faculty::where('name',$faculty_name)->pluck('id');
                        //dd($subject_ids);
                        $fac_notices = NoticeBatch::select('notice.*','notice_batch_notice.created_at as created_time')->where('notice.type','B')->where('notice_batch.id',$notice_batch->id)->whereIn('notice_faculties.faculty_id',$faculty_ids)
                            ->join('notice_batch_notice','notice_batch_notice.notice_batch_id','notice_batch.id')
                            ->join('notice','notice.id','notice_batch_notice.notice_id')
                            ->join('notice_faculties','notice_faculties.notice_id','notice.id')
                            ->orderBy('notice_batch_notice.id','desc')
                            ->whereNull( 'notice_batch_notice.deleted_at' )
                            ->whereNull( 'notice_faculties.deleted_at' )
                            ->whereNull( 'notice.deleted_at' )
                            //->join('batch_discipline_fees','batch_discipline_fees.batch_id','notice_batch.batch_id')
                            ->paginate(10);

                        if(count($fac_notices))
                        {
                            $all_notices[] = $fac_notices;
                        }

                    }
                    else
                    {
                        $subject_name = Subjects::where('id',$doctor_course->subject_id)->value('name');
                        $subject_ids = Subjects::where('name',$subject_name)->pluck('id');
                        //dd($subject_ids);
                        $dis_notices = NoticeBatch::select('notice.*','notice_batch_notice.created_at as created_time')->where('notice.type','B')->where('notice_batch.id',$notice_batch->id)->whereIn('notice_disciplines.subject_id',$subject_ids)
                            ->join('notice_batch_notice','notice_batch_notice.notice_batch_id','notice_batch.id')
                            ->join('notice','notice.id','notice_batch_notice.notice_id')
                            ->join('notice_disciplines','notice_disciplines.notice_id','notice.id')
                            ->orderBy('notice_batch_notice.id','desc')
                            ->whereNull( 'notice_batch_notice.deleted_at' )
                            ->whereNull( 'notice_disciplines.deleted_at' )
                            ->whereNull( 'notice.deleted_at' )
                            //->join('batch_discipline_fees','batch_discipline_fees.batch_id','notice_batch.batch_id')
                            ->paginate(10);

                        if(count($dis_notices))
                        {
                            $all_notices[] = $dis_notices;
                        }

                    }

                }
            }

            unset($notice_course);
            if(NoticeCourse::where(['year'=>$doctor_course->year,'session_id'=>$doctor_course->session_id,'notice_course.institute_id'=>$doctor_course->institute_id,'notice_course.course_id'=>$doctor_course->course_id])->first()){

                $notice_course = NoticeCourse::where(['year'=>$doctor_course->year,'session_id'=>$doctor_course->session_id,'notice_course.institute_id'=>$doctor_course->institute_id,'notice_course.course_id'=>$doctor_course->course_id])->first();

            }

            if( isset($notice_course) && $doctor_course->course->id == $notice_course->course_id)
            {

                if($doctor_course->institute->type == 1)
                {

                    $faculty_name = Faculty::where('id',$doctor_course->faculty_id)->value('name');
                    $faculty_ids = Faculty::where('name',$faculty_name)->pluck('id');
                    //dd($subject_ids);
                    unset($fac_notices);
                    $fac_notices = NoticeCourse::select('notice.*','notice_course_notice.created_at as created_time')->where('notice.type','C')->where('notice_course.id',$notice_course->id)->whereIn('notice_faculties.faculty_id',$faculty_ids)
                        ->join('notice_course_notice','notice_course_notice.notice_course_id','notice_course.id')
                        ->join('notice','notice.id','notice_course_notice.notice_id')
                        ->join('notice_faculties','notice_faculties.notice_id','notice.id')
                        ->orderBy('notice_course_notice.id','desc')
                        ->whereNull( 'notice_faculties.deleted_at' )
                        ->whereNull( 'notice.deleted_at' )
                        //->join('batch_discipline_fees','batch_discipline_fees.batch_id','notice_batch.batch_id')
                        ->paginate(10);

                    if(count($fac_notices))
                    {
                        $all_notices[] = $fac_notices;
                    }

                }
                else
                {
                    $subject_name = Subjects::where('id',$doctor_course->subject_id)->value('name');
                    $subject_ids = Subjects::where('name',$subject_name)->pluck('id');
                    //dd($subject_ids);
                    unset($dis_notices);
                    $dis_notices = NoticeCourse::select('notice.*','notice_course_notice.created_at as created_time')->where('notice.type','C')->where('notice_course.id',$notice_course->id)->whereIn('notice_disciplines.subject_id',$subject_ids)
                        ->join('notice_course_notice','notice_course_notice.notice_course_id','notice_course.id')
                        ->join('notice','notice.id','notice_course_notice.notice_id')
                        ->join('notice_disciplines','notice_disciplines.notice_id','notice.id')
                        ->whereNull( 'notice_disciplines.deleted_at' )
                        ->orderBy('notice_course_notice.id','desc')
                        ->whereNull( 'notice.deleted_at' )
                        //->join('batch_discipline_fees','batch_discipline_fees.batch_id','notice_batch.batch_id')
                        ->paginate(10);

                    if(count($dis_notices))
                    {
                        $all_notices[] = $dis_notices;
                    }

                }

            }

            unset($notice_year);
            if(NoticeYear::where(['year'=>$doctor_course->year])->first()){

                $notice_year = NoticeYear::where(['year'=>$doctor_course->year])->first();

            }

            if( isset($notice_year) && $doctor_course->year == $notice_year->year)
            {
                unset($notice_all);
                $notice_all = Notice::select('notice.*')->where(['notice.status'=>1,'notice.type'=>'A','notice_year.year'=>$doctor_course->year])
                                ->join('notice_year_notice','notice_year_notice.notice_id','notice.id')
                                ->join('notice_year','notice_year.id','notice_year_notice.notice_year_id')
                                ->get();

                if(count($notice_all))
                {
                    $all_notices[] = $notice_all;
                }

            }

        }

        $data['notice_read'] = DoctorNoticeView::where('doctor_id', Auth::guard('doctor')->id())->pluck('notice_id')->toArray();

        $unread_notices = array();
        $read_notices = array();
        $notice_ids = array();
        foreach($all_notices as $notices)
        {
            foreach($notices as $notice)
            {
                if(!in_array($notice->id,$notice_ids))
                {
                    if(in_array($notice->id,$data['notice_read']))
                    {
                        $read_notices[] = $notice;
                    }
                    else
                    {
                        $unread_notices[] = $notice;
                    }

                }

                $notice_ids[] = $notice->id;

            }
        }

        $data['all_notices'] = array_merge($unread_notices,$read_notices);

        return view('notice', $data);
    }

    public function notice_details($id)
    {
        $data['doc_info'] = Doctors::where('id', Auth::guard('doctor')->id())->first();
        $data['id'] = $id;
        $notice = Notice::where('id', $id)->first();
        $data['notice'] = $notice;
        //$data['notices'] = Notice::with('sessionname','institutename','coursename','batchname')->where('id', $id)->first();
        if ($notice->type=='I'){
            $data['doctors'] = DoctorNotices::where('notice_id', $id)->where('doctor_id', Auth::guard('doctor')->id())->get();
        }
        $read_notice = DoctorNoticeView::where(['doctor_id' => Auth::guard('doctor')->id(), 'notice_id' => $id])->first();
        if($read_notice){
        }else{
            DoctorNoticeView::insert([
                'doctor_id' => Auth::guard('doctor')->id(),
                'notice_id' => $id,
            ]);
        }
        return view('notice_details', $data);
    }

    public function change_password()
    {
        $data['doc_info'] = Doctors::where('id', Auth::guard('doctor')->id())->first();
        $data['notices'] = Notice::where('status', 1)->orderBy('id', 'desc')->get();
        return view('change_password', $data);
    }

    public function update_password(Request $request)
    {
        if($request->new_password==$request->confirm_password){
            $profile = Doctors::find(Auth::guard('doctor')->id());
            $profile->main_password=$request->new_password;
            $profile->password=Hash::make($request->new_password);
            $profile->push();
            Session::flash('message', 'Password has been updated successfully');

            return back();
        } else {
            Session::flash('message', 'Password NOT updated!');

            return back();
        }


    }

    public function question_box()
    {
        $data['doc_info'] = Doctors::where('id', Auth::guard('doctor')->id())->first();
        $data['question_info'] = DoctorAsks::where('doctor_id', Auth::guard('doctor')->id())->get();
        return view('question_box', $data);
    }

    public function question_add()
    {

        $doc_info = Doctors::where('id', Auth::guard('doctor')->id())->first();
        foreach($doc_info->doctorcourses as $single){
            $doc_info->schedule_id = BatchesSchedules::where(['year'=>$single->year,'session_id'=>$single->session_id,'course_id'=>$single->course_id,'batch_id'=>$single->batch_id])->value('id');
        }
        $data['doc_info'] = $doc_info;

        return view('question_add', $data);

        // $data['doc_info'] = Doctors::where('id', Auth::guard('doctor')->id())->first();
        // $data['courses'] = Courses::get()->pluck('name', 'id');
        // $data['batch'] = Batches::get()->pluck('name', 'id');
        // return view('question_add', $data);
    }

    public function submit_question(Request $request)
    {
        if ($request->description) {
            $question = new DoctorQuestion();
            $question->doctor_id = Auth::guard('doctor')->id();
            $question->batch_id = $request->batch_id;
            $question->lecture_id = $request->lecture_id;
            $question->question = $request->description;
            $question->status = 1;
            $question->save();
            Session::flash('message', 'Question Uploaded successfully');
            return back();
        } else {
            Session::flash('message', 'Question NOT Uploaded!');
            return back();
        }
    }

    public function question_delete($id)
    {
        if($id){
            $question = DoctorQuestion::find($id);
            $question->status=0;
            $question->push();
            Session::flash('message', 'Question Deleted successfully');
            return back();
        } else {
            Session::flash('message', 'NOT Deleted!');
            return back();
        }
    }

    public function complain_box()
    {
        $data['doc_info'] = Doctors::where('id', Auth::guard('doctor')->id())->first();
        $data['complain_info'] = DoctorComplain::where('doctor_id', Auth::guard('doctor')->id())->orderBy('id', 'asc')->get();
            if (DoctorComplain::where(['doctor_id'=>Auth::guard('doctor')->id()])->exists()){
                $complain_id = DoctorComplain::where(['doctor_id'=>Auth::guard('doctor')->id()])->first();
                return redirect('complain-details/'.$complain_id->id);
            }
        return view('complain_box', $data);
    }

    public function submit_complain(Request $request)
    {
        if ($request->description) {

            if (DoctorComplain::where(['doctor_id'=>Auth::guard('doctor')->id()])->exists()){
                $complain_id = DoctorComplain::where(['doctor_id'=>Auth::guard('doctor')->id()])->first();

                $complain_submit = new DoctorComplainReply();
                $complain_submit->doctor_id = Auth::guard('doctor')->id();
                $complain_submit->user_id = 0;
                $complain_submit->message_by = 'doctor';
                $complain_submit->message = $request->description;
                $complain_submit->doctor_complain_id = $complain_id->id;
                $complain_submit->is_read = 'No';
                $complain_submit->save();

                return redirect('complain-details/'.$complain_id->id);
            }

            $complain = new DoctorComplain();
            $complain->doctor_id = Auth::guard('doctor')->id();
            $complain->save();

            $complain_submit = new DoctorComplainReply();
            $complain_submit->doctor_id = Auth::guard('doctor')->id();
            $complain_submit->user_id = 0;
            $complain_submit->message_by = 'doctor';
            $complain_submit->message = $request->description;
            $complain_submit->doctor_complain_id = $complain->id;
            $complain_submit->is_read = 'No';
            $complain_submit->save();

            Session::flash('message', 'Complain submited successfully');
            return back();
        } else {
            Session::flash('message', 'Complain NOT submited!');
            return back();
        }
    }

    public function complain_details($id)
    {
        $data['doc_info'] = Doctors::where('id', Auth::guard('doctor')->id())->first();
        //$data['reply_info'] = DoctorComplainReply::where('doctor_complain_id', $id)->where('user_id', '!=', 0)->get();
        $data['complain_details'] = DoctorComplainReply::where('doctor_complain_id', $id)->orderBy('id', 'desc')->get()->chunk(30);
        $data['complain_details'] = $data['complain_details'][0]->reverse();
        // dd($data['complain_details']);
        $data['complain_id'] = $id;

        return view('complain_details', $data);
    }

    public function complain_again( Request $request )
    {


        if ($request->description ) {
            $complain_again = new DoctorComplainReply();
            $complain_again->doctor_id = Auth::guard('doctor')->id();
            $complain_again->user_id = 0;
            $complain_again->message_by = 'doctor';
            $complain_again->message = $request->description;
            $complain_again->doctor_complain_id = $request->complain_id;
            $complain_again->is_read = 'No';
            $complain_again->save();

            DoctorComplain::where('id',$complain_again->doctor_complain_id)
            ->update(['last_reply_status' => 'No','last_reply_time'=>Carbon::now()]);

            Session::flash('message', 'Complain Submit successfully');
            return back();
        } else {
            Session::flash('message', 'NOT Submited!');
            return back();
        }
    }

    public function send_otp(Request $request)
    {
        if ( Auth::guard('doctor')->id() ) {
            $data['doc_info'] = Doctors::where('id', Auth::guard('doctor')->id())->first();
            if(($request->doctor_id!='') && ($request->video_id!='')){
                $otp_info = Otp::where('doctor_id', $request->doctor_id)->where('video_id', $request->video_id)->first();
                $smsLog = new SmsLog();
                $response = null;

                if( $otp_info ) {
                    if(($otp_info->status==1) || ($otp_info->status==2) || ($otp_info->status==3)){
                        echo 1;
                        $otp_pass=rand(1234,9876);
                        $otp_upd = Otp::find($otp_info->id);
                        $otp_upd->otp=$otp_pass;
                        $otp_upd->push();

                        $mob = '88'.$request->phone;
                        $msg = 'Your OTP is : '.$otp_pass;
                        $msg = str_replace(' ', '%20', $msg);
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, "http://sms4.digitalsynapsebd.com/api/mt/SendSMS?user=genesispg&password=123321&senderid=8801833307423&channel=Normal&DCS=0&flashsms=0&number=$mob&text=$msg");
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
                        $response = curl_exec($ch);
                        curl_close($ch);
                        $data['video_id'] = $request->video_id;
                        $data['msg'] = 'OTP Sent to Your Mobile ('.$request->phone.') by SMS';
                        return view('otp_view', $data);

                    } else {
                        echo "You have Used OTP 3 times for this video.";
                    }


                } else {

                    $otp_pass=rand(1234,9876);
                    $otp = new Otp();
                    $otp->doctor_id = $request->doctor_id;
                    $otp->video_id = $request->video_id;
                    $otp->otp = $otp_pass;
                    $otp->status = 1;
                    $otp->save();

                    $mob = '88'.$request->phone;
                    $msg = 'Your OTP is : '.$otp_pass;
                    $msg = str_replace(' ', '%20', $msg);

                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, "http://sms4.digitalsynapsebd.com/api/mt/SendSMS?user=genesispg&password=123321&senderid=8801833307423&channel=Normal&DCS=0&flashsms=0&number=$mob&text=$msg");
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
                    $response = curl_exec($ch);
                    curl_close($ch);




                    $data['video_id'] = $request->video_id;
                    $data['msg'] = 'OTP Sent to Your Mobile ('.$request->phone.') by SMS';
                    return view('otp_view', $data);
                }

                if( $response ) {
                    $smsLog->set_response( $response, $request->doctor_id )->save( );
                }

            } else {
                Session::flash('message', 'Data Missing!');
                return back();
            }
        } else {
            return view('/home');
        }

    }

    public function submit_otp(Request $request)
    {
        if (Auth::guard('doctor')->id()) {
            $data['doc_info'] = Doctors::where('id', Auth::guard('doctor')->id())->first();

            if(($request->doctor_id!='') && ($request->video_id!='') && ($request->otp!='')){

                $data['doctor_id']  = $request->doctor_id;
                $data['video_id']   = $request->video_id;
                $otp                = $request->otp;

                $otp_info = Otp::where('doctor_id', $request->doctor_id)->where('video_id', $request->video_id)->where('otp', $request->otp)->orderBy('id', 'DESC')->first();

                if ($otp_info){
                    if(($otp_info->status==1) || ($otp_info->status==2) || ($otp_info->status==3)){
                        $data['otp_status']=1;
                        $data['video_info'] = OnlineLectureAddress::select('*')->where('id', $request->video_id)->get();
                        if($otp_info->status==1){
                            $otp_sts=2;
                        } elseif ($otp_info->status==2) {
                            $otp_sts=3;
                        } else {
                            $otp_sts=0;
                        }
                        $otp_upd = Otp::find($otp_info->id);
                        $otp_upd->status=$otp_sts;
                        $otp_upd->push();
                    } else {
                        $data['otp_status']=0;
                    }
                } else {
                    $data['otp_status']=4;
                }

                return view('otp_submit', $data);

            } else {
                Session::flash('message', 'Data Missing');
                return back();
            }

        } else {
            return view('/home');
        }

    }

    public function pay_now(Request $request)
    {
        if ($request->amount) {
            $time = substr(time(), -4);
            $trans_id = date('ymd').'_'.$time.rand(12,34).rand(56,78);
            $pay_now = new PaymentInfo();
            $pay_now->doctor_id = Auth::guard('doctor')->id();
            $pay_now->doctor_course_id = $request->doctor_course_id;
            $pay_now->trans_id = $trans_id;
            $pay_now->amount = $request->amount;
            $pay_now->status = 0;
            $pay_now->save();
            $payment_id = $pay_now->id;
            $doc_info = Doctors::where('id', Auth::guard('doctor')->id())->first();
            echo $link = "https://banglamedexam.com/user-login-sif-payment?
            name=$doc_info->name&
            password=123456&
            email=$doc_info->email&
            bmdc=$doc_info->bmdc_no&
            phone=$doc_info->mobile_number&
            doctor_id=$doc_info->id&
            regi_no=$request->reg_no&
            trans_id=$trans_id&
            payment_id=$payment_id&
            amount=$request->amount";
            return redirect($link);

        } else {
            Session::flash('message', ' NOT submited!');
            return back();
        }
    }

    public function question_submit(Request $request)
    {

        $schedule_id = $_GET['schedule-id'] ?? null;

        if ($request->lecture_video_id) {
            if (DoctorAsks::where(['doctor_id'=>Auth::guard('doctor')->id(),'lecture_video_id'=>$request->lecture_video_id])->exists()){
                $ask_id = DoctorAsks::where(['doctor_id'=>Auth::guard('doctor')->id(),'lecture_video_id'=>$request->lecture_video_id])->first();
                return redirect('view-answer/'.$ask_id->id . ( $schedule_id ? '?schedule-id='. $schedule_id : '' ));
            } else {
                $question = new DoctorAsks();
                $question->doctor_id = Auth::guard('doctor')->id();
                $question->doctor_course_id = $request->doctor_course_id;
                $question->lecture_video_id = $request->lecture_video_id;
                $question->save();

                $data_id = $question->id;

                if( $schedule_id ) {
                    return redirect('view-answer/'.$data_id );
                }else {
                    return redirect('view-answer/'.$data_id . ( $schedule_id ? '?schedule-id='. $schedule_id : '' ) );
                }
                //return redirect('question-answer/'.$data_id);
            }

        } else {
            Session::flash('message', 'NOT Submited!');
            return back();
        }
    }

    public function question_answer($id)
    {
        if (Auth::guard('doctor')->id()) {
            $data['doc_info'] = Doctors::where('id', Auth::guard('doctor')->id())->first();
            $data['doctor_ask_id'] = $id;
            return view('question_answer', $data);
        }
    }

    public function question_submit_final(Request $request)
    {

        if ( $request->description ) {
            $question = new DoctorAskReply( );

            $question->doctor_id = Auth::guard('doctor')->id();
            $question->user_id = 0;
            $question->message_by = 'doctor';
            $question->message = $request->description;
            $question->doctor_ask_id = $request->ask_id;
            $question->is_read = 'No';

            $question->save( );

            Session::flash('message', 'Question Submit successfully');
            return back();
        } else {
            Session::flash('message', 'NOT Submited!');
            return back();
        }
    }

    public function view_answer($id)
    {
        if (Auth::guard('doctor')->id()) {
            $data['doc_info'] = Doctors::where('id', Auth::guard('doctor')->id())->first();
            $data['answer_info'] = DoctorAskReply::select('*')->where('doctor_ask_id', $id)->get();
            $data['ask_id'] = $id;
            $data['ask_info'] = DoctorAsks::select('*')->where('id', $id)->first();
            $data['schedule_id'] = $_GET['schedule-id'] ?? null;
            return view('view_answer', $data);
        }
    }

    public function question_again(Request $request)
    {

        if ($request->description) {
            $question = new DoctorAskReply();
            $question->doctor_id = Auth::guard('doctor')->id();
            $question->user_id = 0;
            $question->message_by = 'doctor';
            $question->message = $request->description;
            $question->doctor_ask_id = $request->ask_id;
            $question->is_read = 'No';
            $question->save();

            Session::flash('message', 'Question Submit successfully');
            return back();
        } else {
            Session::flash('message', 'NOT Submited!');
            return back();
        }
    }

    public function promo_code($doctor_course_id)
    {
        $data['doctor_course'] = DoctorsCourses::where(['id'=>$doctor_course_id])->first();
        if($data['doctor_course']->payment_status != "Completed")
        {
            return view('promo_code',$data);
        }
        else
        {
            return back();
        }
        
    }

    public function apply_promo_code(Request $request)
    {
        $doctor_course = DoctorsCourses::where(['id'=>$request->doctor_course_id])->first();
        if(isset($doctor_course))
        {
            
            $promo = $doctor_course->apply_discount_code($request->discount_code);
            if($promo)
            {
                $doctor_course->set_payment_status();
                Session::flash('status', 'Promo code is applied successfully!!!');
                return redirect('/payment/'.$doctor_course->id)->withInput();
            }
            else
            {   
                Session::flash('status', 'This promo code is not applicable or promo code is expired!!!');
                return redirect('/promo-code/'.$doctor_course->id);
            }

        }
        Session::flash('status', 'This promo code is not applicable or promo code is expired!!!');
        return redirect('/promo-code/'.$doctor_course->id);
        
    }

    public function payment_detail($doctor_course_id)
    {
        $data['doctor_course'] = DoctorsCourses::where(['id'=>$doctor_course_id])->first();
        $data['doctor_course']->set_payment_status();
                
        return view('payment_detail',$data);
    }

    public function payment_option($doctor_course_id)
    {
        $data['doctor_course'] = DoctorsCourses::where(['id'=>$doctor_course_id])->first();
        $data['doctor_course']->set_payment_status();
                
        return view('payment_detail',$data);
    }

    public function set_payment_option(Request $request)
    {
        $data['doctor_course'] = DoctorsCourses::where(['id'=>$request->doctor_course_id])->first();
        DoctorsCourses::where(['id'=>$request->doctor_course_id])->update(['payment_option'=>$request->payment_option]);
        $data['doctor_course']->set_payment_status();
                
        return "success";
    }

    public function installment_payment($doctor_course_id)
    {
        $data['doctor_course'] = DoctorsCourses::where(['id'=>$doctor_course_id])->first();
        $data['doctor_course']->set_payment_status();
                
        return view('installment_payment',$data);
    }

    public function payment_history($doctor_course_id)
    {
        $data['doctor_course'] = DoctorsCourses::where(['id'=>$doctor_course_id])->first();        
        return view('payment_history',$data);
    }

    public function payment($course_id)
    {
        $data['doc_info'] = Doctors::where('id', Auth::guard('doctor')->id())->first();
        $data['doctor'] = Doctors::where('id', Auth::guard('doctor')->id())->first();
        $doctor = Doctors::where('id', Auth::guard('doctor')->id())->first();
        
        $data['course_info'] = DoctorsCourses::with('course.site_setup', 'batch')->where(['id'=>$course_id])->first();
        $data['lecture_sheet'] = $data['course_info']->include_lecture_sheet;
        $data['lecture_sheet'] = $data['course_info']->include_lecture_sheet;

        // $data['doctor'] = Auth::user();
        $doctor_course = DoctorsCourses::with('batch', 'course', 'institute')->findOrFail($course_id);
        $data['doctor_course_number'] = count(DoctorsCourses::where('doctor_id',$doctor->id)->get());
        // return $data['doctor_course_number'];
        // $data['doctor_course_totals'] = DoctorsCourses::where('doctor_id',$doctor->id)->ordeBy('id','DESC')->get();
        $data['course_info'] = $doctor_course;
        $data['divisions'] = Divisions::get()->pluck('name', 'id');
        return view('payment',$data);
    }

    public function payment_create(Request $request,$doctor_id,$course_id,$lecture_sheet = '')
    {

        $data =DoctorsCourses::find($course_id);
        $doctor_course =DoctorsCourses::find($course_id);
        // $data['doctor_id']=Auth::guard('doctor')->id();

        // if($data->include_lecture_sheet==1){

        //     if($request->checkbox){
        //         $data['courier_division_id']= Auth::user()->present_division_id;
        //         $data['courier_district_id']= Auth::user()->present_district_id;
        //         $data['courier_upazila_id']= Auth::user()->present_upazila_id;
        //         $data['courier_address']= Auth::user()->present_address;
        //     }else{
        //         $data['courier_division_id']= $request->division_id;
        //         $data['courier_district_id']= $request->district_id;
        //         $data['courier_upazila_id']= $request->upazila_id;
        //         $data['courier_address']= $request->address;
        //     }

        //     $data->push();
        //     $data['course_info'] = $data;

        //     if($request->delevary_status==1){
        //         $data['total'] = $request->total;
        //     }else{
        //         $data['total'] = $request->amount;
        //     }
        // }else{
        //     $data['total'] = $request->amount;
        // }

        // $data['delevary_status']= ($request->delevary_status)?$request->delevary_status:'0';

        // if(isset($data['delevary_status']))DoctorsCourses::where('id',$course_id)->update(['delivery_status'=>$data['delevary_status']]);

        $data['delevary_status']= ($doctor_course->delevary_status)?$doctor_course->delevary_status:'0';
        $data['total'] = $request->amount;
        $data['doctor_id'] =Auth::guard('doctor')->id();

        $payment_serial = DoctorCoursePayment::where('doctor_course_id',$course_id)->count();
        $data['payment_serial'] = $payment_serial+1;


        if($request->pament_type){
            $this->payment_success($course_id, $request->tr_id, $request->amount, ($payment_serial+1));
            $doctor_course->set_payment_status();
            return redirect('my-courses')->with('status', 'Please wait for admin verification');
           }

        $data['emi'] = $request->emi == 'on' ? 1 : 0;


        return view('payment_redirect', $data);

    }

    public function payment_manual(Request $request,$doctor_course_id)
    {
        $data['doctor_course'] = DoctorsCourses::find($doctor_course_id);

        $data['doctor_course']->set_payment_status();

        return view('payment_manual', $data);

    }

    public function payment_manual_installment(Request $request,$doctor_course_id)
    {
        $data['doctor_course'] = DoctorsCourses::find($doctor_course_id);

        $data['doctor_course']->set_payment_status();

        return view('payment_manual_installment', $data);

    }

    public function payment_manual_save(Request $request)
    {

        $doctor_course_manual_payment = new DoctorCourseManualPayment();

        $doctor_course_manual_payment->doctor_course_id = $request->doctor_course_id;
        $doctor_course_manual_payment->trans_id = $request->trans_id;
        $doctor_course_manual_payment->amount = $request->amount;
        $doctor_course_manual_payment->save();

        //Session::flush('message','Thank you for your payment. We will confirm your payment within 3 hours with confirmation SMS.');
        return view('payment_manual_message');
        //return redirect('payment-details');

    }

    public function schedule_a()
    {

        return view('schedule_a');
  
    }

    public function payment_success($course_id, $card_no, $amount, $payment_serial)
    {
        $data = new DoctorCoursePayment;

        $data['doctor_course_id'] =$course_id;
        $data['trans_id'] =$card_no;
        $data['amount'] =$amount;
        $data['payment_serial'] =$payment_serial;
        $data->save();
        $doctor_course = DoctorsCourses::where(['id'=>$course_id])->first();
        $doctor_course->set_payment_status();

    }

    // discount request

    public function discount_request($course_id){
        return view('discount_request',compact('course_id'));
    }
    
    public function discount_request_submit(Request $request){
        $validator = Validator::make($request->all(),[
            'previous_batch_name' => ['required'],
            'previous_reg_no' => ['required'],
        ]);

        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            session()->flash('message','Please enter proper input values!!!');
            return redirect()->back()->withInput();
        }

        if(DiscountRequest::where('previous_reg_no', $request->previous_reg_no)->exists()){
            Session::flash('class', 'alert-danger');
            session()->flash('message','Discount request already sent');
            return redirect()->back()->withInput();
        }


       $discount_request = new DiscountRequest();
       $discount_request->doctor_id = Auth::guard('doctor')->id();
       $discount_request->course_id = $request->course_id;
       $discount_request->previous_batch_name = $request->previous_batch_name;
       $discount_request->previous_reg_no = $request->previous_reg_no;
       $discount_request->note = $request->note;
       $discount_request->save();


       Session::flash('status', 'Discount Request has been sent successfully. If you are eligible for discount code, you will get a SMS within 30 minutes Thank You');

       if($discount_request){
           $this->sendMessage($request->course_id);
       }

       return redirect('payment-details');

    }

    protected function sendMessage( $course_id){
        
        $smsLog = new SmsLog();
        $response = null;
        $discount_number = DiscountRequestNumber::where('status','1')->first();
        $doctor_id=Auth::guard('doctor')->id();
        $doctor=Doctors::where('id',$doctor_id)->first();
        $doctor_course = DoctorsCourses::with('batch')->where(['doctor_id' => $doctor_id,'id' =>$course_id])->orderby('id','DESC')->first();
        $mob = '88' . $discount_number->mobile_number;
        $msg = ' " ' .  $doctor->bmdc_no . ' " '.'Request for a discount code ' . $doctor_course->batch->name;

        // $ch = curl_init();
        // curl_setopt($ch, CURLOPT_URL, "http://sms4.digitalsynapsebd.com/api/mt/SendSMS?user=genesispg&password=123321@12&senderid=8809612440402&channel=Normal&DCS=0&flashsms=0&number=$mob&text=$msg");
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        // $response = curl_exec($ch);
        // curl_close($ch);
        // $smsLog->set_response( $response,$doctor->id)->set_event('Request Discount')->save();
        $this->send_custom_sms($doctor,$msg,'Request Discount',$isAdmin = false); 
    }

    
    public function bmdc_email_medical(Request $request){
        $bmdc_no = 'A'.$request->bmdc_no;
        if(Doctors::where(['bmdc_no' => $bmdc_no])->orWhere(['bmdc_no' => $request->bmdc_no])->orWhere(['email' => $request->email])->exists()){
            return response()->json(["message"=>"Bmdc Number Or Email Already Exists","success"=>false]);
        }
        else{
            $doctor_id = Auth::guard('doctor')->id();
            $doctor =  Doctors::where(['id' => $doctor_id , 'bmdc_no' => null])->first();
            $doctor->bmdc_no = $bmdc_no;
            $doctor->medical_college_id = $request->medical_college;
            $doctor->email = $request->email;
            $doctor->push();
            return response()->json(["message"=>"","success"=>true]);
        }
        
    }

    public function payment_details_pdf_public( $id ){
        return $this->payment_details_pdf( $id );
    }

    public function payment_details_pdf($id){

        
        $doctor_course = DoctorsCourses::with(
            'batch.subject_fees', 
            'batch.faculty_fees', 
            'course.institute', 
            'batch.batch_schedule', 
            'doctor', 
            'session', 
            'transactions' 
        )->find($id);
        
           $terms_and_conditions = Setting::where('name', 'terms_conditions')->first();
        //return $doctor_course->batch;

        $institute = $doctor_course->batch->institute;

        $admissionFee = 0;
        $lectureSheetFee = 0;

        if( $doctor_course->batch && $doctor_course->batch->fee_type == 'Batch' ) {
            $admissionFee = $doctor_course->batch->admission_fee;
            $lectureSheetFee = $doctor_course->batch->lecture_sheet_fee;
        } else if( $doctor_course->course ) {

            
            if( ($doctor_course->course->institute->type ?? '') == 0 ) {

                
                $subject_fees = $doctor_course->batch->subject_fees;

                $fee = $subject_fees->where( 'subject_id', $doctor_course->subject_id )->first();
                $admissionFee = $fee->admission_fee ?? 0;
                $lectureSheetFee = $fee->lecture_sheet_fee ?? 0;

            }else {
                $faculty_fees = $doctor_course->batch->faculty_fees;
                $fee = $faculty_fees->where( 'faculty_id', $doctor_course->faculty_id )->first();
                $admissionFee = $fee->admission_fee ?? 0;
                $lectureSheetFee = $fee->lecture_sheet_fee ?? 0;
                
            }

        }

        $course_price =  $doctor_course->course_price > ( $admissionFee + $lectureSheetFee )
            ? $doctor_course->course_price 
            : $admissionFee + $lectureSheetFee;

        $transactions = $doctor_course->transactions()->orderBy('created_at', 'DESC')->get();

        $paid_amount = $doctor_course->transactions->sum('amount');
        
        $last_transaction = $transactions->first();

        $discount = $course_price > $paid_amount ? $course_price - $paid_amount : 0;
        
        $data = [ 
            'doctor_course' => $doctor_course,
            'course_price' => $course_price,
            'paid_amount' => $paid_amount, 
            'discount' => $discount,
            'last_transaction' => $last_transaction,
            'terms_and_conditions' => $terms_and_conditions
        ];

       $pdf= PDF::loadView('pdf.invoice.course_payment',$data);

       $pdf->download('course_details.pdf');
       return $pdf->stream('document.pdf');
    }

    public function system_driven(Request $request)
    {
        $data['batch'] = Batches::where(['id'=>$request->batch_id])->first();
        $data['doctor_course'] = DoctorsCourses::where(['id'=>$request->doctor_course_id])->first();
             
        return view( 'system_driven', $data );
    }

    public function add_system_driven(Request $request)
    {
        
        $data['batch_id'] = $request->batch_id;
        $data['doctor_course_id'] = $request->doctor_course_id;
        $batch = Batches::where(['id'=>$request->batch_id])->first();
        $message = 'Dear Dr. Please contact with authority...!!! You have crossed maximum limit of changes of SYSTEM DRIVEN.';
        if($request->operation == "insert")
        {
            $doctor_course = DoctorsCourses::where(['id'=>$request->doctor_course_id])->first();
            if($batch->system_driven_change_count_max > $doctor_course->system_driven_count)
            {
                $system_driven_count = (int)$doctor_course->system_driven_count+1;
                DoctorsCourses::where(['id'=>$request->doctor_course_id])->update(['system_driven' => "Yes",'system_driven_count'=>$system_driven_count]);
                if(isset($doctor_course->batch->batch_schedule) && count($doctor_course->batch->batch_schedule))
                {
                    foreach($doctor_course->batch->batch_schedule as $schedule)
                    {
                        DoctorCourseScheduleDetails::where(['schedule_id'=>$schedule->id,'doctor_course_id'=>$doctor_course->id])->delete();
                    }
                }
                
                $data['success_status'] = "insert_success";
                $data['message'] = '';
                $sms = array();  
                $sms['sms'] = "Dear Dr. You have accepted SYSTEM DRIVEN Option for batch ".$doctor_course->batch->name." , Your Reg No ".$doctor_course->reg_no.' Thanks. GENESIS';
                $sms['sms_event']['id'] = "35";
                $sms['sms_event']['name'] = "Doctor System Driven Accepted";
                $this->sendSystemDrivenMessage($sms,$doctor_course->doctor);
                
            }
            else
            {
                $data['success_status'] = "insert_completed";
                $data['message'] = $message;
            }                       

        }
        else if($request->operation == "delete")
        {
            $doctor_course = DoctorsCourses::where(['id'=>$request->doctor_course_id])->first();
            if($batch->system_driven_change_count_max > $doctor_course->system_driven_count)
            {
                $system_driven_count = (int)$doctor_course->system_driven_count+1;            
                DoctorsCourses::where(['id'=>$request->doctor_course_id])->update(['system_driven' => "No",'system_driven_count'=>$system_driven_count]);
                if(isset($doctor_course->batch->batch_schedule) && count($doctor_course->batch->batch_schedule))
                {
                    foreach($doctor_course->batch->batch_schedule as $schedule)
                    {
                        DoctorCourseScheduleDetails::where(['schedule_id'=>$schedule->id,'doctor_course_id'=>$doctor_course->id])->delete();
                    }
                }            
                $data['success_status'] = "delete_success";
                $data['message'] = '';
                $sms = array();  
                $sms['sms'] = "Dear Dr. You have denied SYSTEM DRIVEN Option for batch ".$doctor_course->batch->name." , Your Reg No ".$doctor_course->reg_no.' Thanks. GENESIS';
                $sms['sms_event']['id'] = "36";
                $sms['sms_event']['name'] = "Doctor System Driven Denied";

                $this->sendSystemDrivenMessage($sms,$doctor_course->doctor);
            
            }
            else
            {
                $data['success_status'] = "insert_completed";
                $data['message'] = $message;
            }     

        }
        
        return response()->json($data);

    }

    public function check_doctor_system_driven(Request $request)
    {
        $data['batch_id'] = $request->batch_id;
        $data['doctor_course_id'] = $request->doctor_course_id;
        $batch = Batches::where(['id'=>$request->batch_id])->first();
        $doctor_course = DoctorsCourses::where(['id'=>$request->doctor_course_id])->first();

        if($doctor_course->system_driven == "Yes")
        {
            $data['success_status'] = "Yes";
            $data['message'] = '';
        }
        else if($doctor_course->system_driven == "No")
        {
            $data['success_status'] = "No";
            $data['message'] = '';
        }
        else
        {
            $data['success_status'] = "Empty";
            $data['message'] = '';
        }

        return response()->json($data);

    }

    public function confirm_doctor_course_view(Request $request)
    {
        $data['batch_id'] = $request->batch_id;
        $data['doctor_course_id'] = $request->doctor_course_id;
        $batch = Batches::where(['id'=>$request->batch_id])->first();
        $doctor_course = DoctorsCourses::where(['id'=>$request->doctor_course_id])->first();

        if($doctor_course->system_driven == "Yes")
        {
            $data['success_status'] = "Yes";
            $data['message'] = '';
        }
        else if($doctor_course->system_driven == "No")
        {
            $data['success_status'] = "No";
            $data['message'] = '';
        }
        else
        {
            $data['success_status'] = "Empty";
            $data['message'] = '';
        }

        return response()->json($data);

    }

    public function add_doctor_course_schedule_details(Request $request)
    {
        $doctor_course =  DoctorsCourses::where(['id'=>$request->doctor_course_id])->first();
        $data['batch_system_driven'] = $doctor_course->batch->system_driven;
        $data['doctor_system_driven'] = $doctor_course->system_driven;
        $batch_schedule = BatchesSchedules::where(['id'=>$request->schedule_id])->first();
        $batch_schedule_time_slots = $batch_schedule->time_slots;
        foreach($batch_schedule_time_slots as $time_slot)
        {
            foreach($time_slot->schedule_details as $schedule_details)
            {
                if($schedule_details->type == $request->type && $schedule_details->class_or_exam_id == $request->class_or_exam_id)
                {
                    $data['schedule_details_id']= $schedule_details->id;
                }
                                
            }
        }

        $doctor_course_schedule_details = DoctorCourseScheduleDetails::where(['doctor_course_id'=>$request->doctor_course_id,'schedule_id'=>$request->schedule_id,'schedule_details_id'=>$data['schedule_details_id']])->first();
        if(isset($doctor_course_schedule_details))
        {
            $data['message'] = "Doctor already visited to this link";
            return response()->json($data);
        }
        else
        {
            $insert_id = DoctorCourseScheduleDetails::insert(['doctor_course_id'=>$request->doctor_course_id,'schedule_id'=>$request->schedule_id,'schedule_details_id'=>$data['schedule_details_id']]);
            if($insert_id)
            {
                $data['message'] = "Inserted Successfully";
                return response()->json($data);
            }
            else
            {
                $data['message'] = "Error Occurs";
                return response()->json($data);
            }

        }
        
        
    }

    public function set_doctor_system_driven_feedback(Request $request)
    {
        $doctor_course =  DoctorsCourses::where(['id'=>$request->doctor_course_id])->first();
        $data['batch_system_driven'] = $doctor_course->batch->system_driven;
        $data['doctor_system_driven'] = $doctor_course->system_driven;
        $batch_schedule = BatchesSchedules::where(['id'=>$request->schedule_id])->first();
        $batch_schedule_time_slots = $batch_schedule->time_slots;
        foreach($batch_schedule_time_slots as $time_slot)
        {
            foreach($time_slot->schedule_details as $schedule_details)
            {
                if($schedule_details->type == $request->type && $schedule_details->class_or_exam_id == $request->class_or_exam_id)
                {
                    $data['schedule_details_id'] = $schedule_details->id;
                }
                                
            }
        }

        $doctor_course_schedule_details = DoctorCourseScheduleDetails::where(['doctor_course_id'=>$request->doctor_course_id,'schedule_id'=>$request->schedule_id,'schedule_details_id'=>$data['schedule_details_id']])->first();
        if(isset($doctor_course_schedule_details))
        {
            $update = DoctorCourseScheduleDetails::where(['doctor_course_id'=>$request->doctor_course_id,'schedule_id'=>$request->schedule_id,'schedule_details_id'=>$data['schedule_details_id']])->update(['feedback'=>$request->feedback]);
            if($update)
            {
                $data['message'] = "Updated Successfully";
                return response()->json($data);
            }
            else
            {
                $data['message'] = "Already given feedback or error occured";
                return response()->json($data);
            }
        }
        else
        {
            $insert_id = DoctorCourseScheduleDetails::insert(['doctor_course_id'=>$request->doctor_course_id,'schedule_id'=>$request->schedule_id,'schedule_details_id'=>$data['schedule_details_id']]);
            if($insert_id)
            {
                $update = DoctorCourseScheduleDetails::where(['doctor_course_id'=>$request->doctor_course_id,'schedule_id'=>$request->schedule_id,'schedule_details_id'=>$data['schedule_details_id']])->update(['feedback'=>$request->feedback]);
                if($update)
                {
                    $data['message'] = "Updated Successfully";
                    return response()->json($data);
                }
                else
                {
                    $data['message'] = "Already given feedback or error occured";
                    return response()->json($data);
                }
            }
            else
            {
                $data['message'] = "Error Occurs";
                return response()->json($data);
            }

        }
        
        
    }

    protected function sendSystemDrivenMessage( $sms,$doctor){
        
        $admin_id = "null";
        $response = null;
        $mob = '88' . $doctor->mobile_number;               
        $ch = curl_init();
        $msg = $sms['sms']; 
        
        $this->send_custom_sms($doctor,$msg,$sms['sms_event']['name'],$isAdmin = false);
     
        // $msg = str_replace(' ', '%20', $msg);
        // $ch = curl_init();
        // curl_setopt($ch, CURLOPT_URL, "http://sms4.digitalsynapsebd.com/api/mt/SendSMS?user=genesispg&password=123321@12&senderid=8809612440402&channel=Normal&DCS=0&flashsms=0&number=$mob&text=$msg");
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        // $response = curl_exec($ch);
        // curl_close($ch);
       
        // $response = json_decode( $response, true );
        // if( is_array( $response ) && isset( $response['JobId'] ) ) {
        //     $job_id = $response['JobId'];
        //     $doctor_id = $doctor->id;

        //    if($mob != null){
        //     $mobile_number =preg_replace('/^88/','',$mob);
        //    }

        //    $event_type = $sms['sms_event']['id'] ?? 0;
        //    $event = $sms['sms_event']['name'] ?? 0;
        //    $delivery_status = "";
        //    $cUrl = curl_init( );
        //     curl_setopt( $cUrl, CURLOPT_URL, "http://sms4.digitalsynapsebd.com/api/mt/GetDelivery?user=genesispg&password=123321@12&jobid=$job_id");
        //     curl_setopt( $cUrl, CURLOPT_RETURNTRANSFER,1);
        //     curl_setopt( $cUrl,CURLOPT_HTTPHEADER, [
        //             'Content-Type: application/json',
        //             'Accept: application/json',
        //         ]
        //     );

        //     $response = curl_exec( $cUrl );
        //     curl_close( $cUrl );
        //     $response = json_decode( $response, true );

        //     if( isset( $response['DeliveryReports'] ) && is_array($response['DeliveryReports']) ) {
        //         $data = $response['DeliveryReports'][0] ?? null;
        //         if( $data ) {
        //             $delivery_status = $data["DeliveryStatus"];
        //         }
        //     }

        //     $created_at = Carbon::now();

        //     $sms_log = SmsLog::insert([
        //         'doctor_id'=>$doctor->id,
        //         'job_id'=>$job_id,
        //         'mobile_no'=>$mobile_number,
        //         'event_type'=>$event_type,
        //         'event'=>$event,
        //         'delivery_status' => $delivery_status,
        //         'admin_id' => $admin_id
        //     ]);
        //     return $sms_log;
        // }
        // else return false;
    }

    public function doctor_course_list_in_schedule()
    {
        $doctor = Doctors::where(['id'=>Auth::guard('doctor')->id()])->first();

        $doctor_courses = DoctorsCourses::where(['doctor_id'=>$doctor->id,'is_trash'=>'0','status'=>'1'])->get();

        if(isset($doctor_courses) && count($doctor_courses))
        {
            $batch_ids = $doctor_courses->pluck('batch_id')->toArray();
        }

        $array_doctor_courses = array();

        foreach($doctor_courses as $doctor_course)
        {
            if(isset($doctor_course->batch->fee_type))
            {
                $doctor_course->set_payment_status();
            }
            
            if(($doctor_course->payment_status == "Completed" || $doctor_course->payment_status == "In Progress") && $doctor_course->eligibility())
            {
                $doctor_course_slots = $doctor_course->slots();
                if(count($doctor_course_slots))
                {
                    $array_doctor_courses[] = $doctor_course;
                }
            }
            
        }

        $data['doctor_courses'] = $array_doctor_courses;

        return view('doctor_course_schedule.doctor_course_list_in_schedule', $data);
    }

    public function doctor_course_schedule($doctor_course_id)
    {
        $data['doctor_course'] = DoctorsCourses::where(['id'=>$doctor_course_id])->first();
        
        return view('doctor_course_schedule.doctor_course_schedule', $data);
    }

    public function doctor_course_schedule_lecture_video($lecture_video_id,$doctor_course_id)
    {
        $data['lecture_video'] = LectureVideo::where(['id'=>$lecture_video_id])->first();
        $data['doctor_course'] = DoctorsCourses::where(['id'=>$doctor_course_id])->first();

        $agent =  new Agent();
        $data['browser'] = $agent->browser();
        
        return view('doctor_course_schedule.doctor_course_schedule_lecture_video', $data);
    }

    public function doctor_course_schedule_exam($exam_id,$doctor_course_id)
    {
        $data['exam'] = Exam::where(['id'=>$exam_id])->first();
        $data['doctor_course'] = DoctorsCourses::where(['id'=>$doctor_course_id])->first();

        $agent =  new Agent();
        $data['browser'] = $agent->browser();
        
        return view('doctor_course_schedule.doctor_course_schedule_lecture_video', $data);
    }

}

