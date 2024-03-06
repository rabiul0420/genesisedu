<?php

namespace App\Http\Controllers\Admin;

use App\Http\Helpers\DoctorLectureSheetHelper;
use App\LectureSheetTopic;
use App\LectureSheetTopicBatchLectureSheetTopic;
use App\LectureSheetTopicDiscipline;
use App\LectureSheetTopicFaculty;
use App\Providers\AppServiceProvider;
use App\User;
use App\BatchFacultyFee;
use App\Courier;
use App\Coming_by;
use App\ComingBy;
use App\Districts;
use App\Divisions;
use App\DoctorCoursePayment;
use App\Http\Controllers\Controller;

use App\MedicalColleges;
use App\Upazilas;
use Illuminate\Http\Request;
use App\Exports\UsersExport;
use App\Institutes;
use App\Courses;
use App\LectureSheet;
use App\DoctorCourseLectureSheet;
use App\LectureSheetTopicBatch;
use App\Faculty;
use App\Subjects;
use App\Batches;
use App\BatchDisciplineFee;
use App\BatchShift;
use App\Branch;
use App\CourseSessions;
use App\Doctors;
use App\Sessions;
use App\Discount;
use App\DoctorComplain;
use App\Service_packages;
use App\ServicePackages;
use App\DoctorsCourses;
use App\LectureSheetDeliveryStatus;
use App\BatchShiftedHistory;
use App\CourseYear;
use App\CourseYearSession;
use App\Exports\ResultExport;
use App\Result;
use App\SmsLog;
use Illuminate\Support\Collection;
use PhpParser\Comment\Doc;
use Session;
use Auth;
use Validator;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use Yajra\DataTables\CollectionDataTable;
use Carbon\Carbon;
use Excel;
use App\DoctorCourseScheduleDetails;
use App\Format;
use App\SendSms;

class DoctorsCoursesController extends Controller
{
    use SendSms;

    public function __construct()
    {
        //Auth::loginUsingId(1);
        //$this->middleware('auth');
    }

    public function index( )
    {  
        $title = 'SIF Admin : Doctors Courses List';
        $batches = Batches::get()->pluck('name', 'id');
        $courses= Courses::get()->pluck('name', 'id');
        $sessions = Sessions::get()->pluck('name', 'id');
        $subjects = Subjects::get()->pluck('name', 'id');
        $administrators = User::orderBy('name')->get(['id', 'name', 'phone_number']);
        $batchespayment = collect(array('In Progress'=>'In Progress','Completed'=>'Completed','Not Completed'=>'Not Completed','No Payment'=>'No Payment'));
        $years = array(''=>' -- Select year --');
        for($year = date("Y")+1;$year>=2017;$year--){
            $years[$year] = $year;
        }
        return view('admin.doctors_courses.list', compact(
            'title',
            'sessions',
            'batches',
            'years',
            'courses',
            'subjects',
            'administrators',
            'batchespayment'
        ));
    }

    public function doctors_courses_list(Request $request)
    {
        $users = User::pluck('name','id')->toArray();
        $year = $request->year;
        $session_id = $request->session_id;
        $course_id = $request->course_id;
        $batch_id = $request->batch_id;
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $lecture_sheet_status = $request->lecture_sheet_status;
        $subject_id = $request->subject_id;
        $payment_status = $request->payment_status;  
        $payment_completed_by_id = $request->payment_completed_by_id;

        $doctors_courses_list = DB::table('doctors_courses as d1' )
            ->leftjoin('doctors as d2', 'd1.doctor_id', '=','d2.id' )
            ->leftjoin('institutes as d3', 'd1.institute_id', '=','d3.id' )
            ->leftjoin('courses as d4', 'd1.course_id', '=','d4.id')
            ->leftjoin('faculties as d5', 'd1.faculty_id', '=','d5.id')
            ->leftjoin('subjects as d6', 'd1.subject_id', '=','d6.id')
            ->leftjoin('subjects as bs', 'd1.bcps_subject_id', '=','bs.id')
            ->leftjoin('batches as d7', 'd1.batch_id', '=','d7.id')
            ->leftjoin('sessions as d8', 'd1.session_id', '=','d8.id')
            ->leftjoin('branches as d10', 'd1.branch_id', '=','d10.id')
            ->whereNull('d1.deleted_at');

        if($year){
            $doctors_courses_list = $doctors_courses_list->where('d1.year', '=', $year);
        }
        if($session_id){
            $doctors_courses_list = $doctors_courses_list->where('d1.session_id', '=', $session_id);
        }
        if($course_id){
            $doctors_courses_list = $doctors_courses_list->where('d1.course_id', '=', $course_id);
        }
        if($batch_id){
            $doctors_courses_list = $doctors_courses_list->where('d1.batch_id', '=', $batch_id);
        }
       
        if($start_date && $end_date){
            $doctors_courses_list = $doctors_courses_list->whereBetween('d1.created_at', [$start_date, $end_date]);
        }

        if($lecture_sheet_status != 'all'){
            $doctors_courses_list = $doctors_courses_list->where('d1.lecture_sheet_delivery_status', '=', $lecture_sheet_status )->where('d1.include_lecture_sheet', '=', '1');
        }

        if($subject_id){
            $doctors_courses_list = $doctors_courses_list->where('d1.subject_id', '=', $subject_id);
        }

        if($payment_status){
            $doctors_courses_list = $doctors_courses_list->where('d1.payment_status', '=', $payment_status);
        }

        if($payment_completed_by_id){
            $doctors_courses_list = $doctors_courses_list->where('d1.payment_completed_by_id', '=', $payment_completed_by_id);
        }

        $doctors_courses_list = $doctors_courses_list->where('is_trash', '=', 0);

        $doctors_courses_list = $doctors_courses_list->select(
            'd1.id as id',
            'd1.lecture_sheet_delivery_status as lecture_sheet_delivery_status',
            'd1.reg_no as reg_no',
            'd1.roll as roll',
            'd1.include_lecture_sheet as include_lecture_sheet',
            'd1.year as year',
            'd1.payment_status as payment_status',
            'd1.batch_shifted as batch_shifted',
            'd1.payment_completed_by_id',
            'd1.created_at as created_at',
            'd1.status as status',
            'd2.id as doctor_id',
            'd2.name as doctor_name',
            'd2.mobile_number as mobile_number',
            'd2.main_password as main_password',
            'd2.bmdc_no as bmdc_no',
            'd3.name as institute_name',
            'd4.name as course_name',
            'd5.name as faculty_name',
            'd6.name as subject_name',
            'bs.name as bcps_subject_name',
            'd7.name as batch_name',
            'd8.name as session_name',
            'd10.name as branch_name'
        );
        //  dd($doctors_courses_list->toSql(), $batch_id, $year, $session_id);
        return Datatables::of($doctors_courses_list)
            ->addColumn('action', function ($doctors_courses_list) {

                if($doctors_courses_list->lecture_sheet_delivery_status){
                    $data['lecture_sheet_delivery_status'] = $doctors_courses_list->lecture_sheet_delivery_status;
                }else{
                    $data['lecture_sheet_delivery_status'] = $this->doctor_course_lecture_sheet_delivery_complete($doctors_courses_list->id);
                }
                $data['doctors_courses_list'] = $doctors_courses_list;
                

                return view('admin.doctors_courses.ajax_list',$data);
            })

            ->addColumn( 'doctor_name', function ($doctors_courses_list) {

                if(!Auth::user()->can('Go To Doctor Profile')) {
                    return $doctors_courses_list->doctor_name ?? '';
                }
                
                $url = route('go-to-doctor-profile', $doctors_courses_list->doctor_id);
                $doctor_name = strlen($doctors_courses_list->doctor_name) ? $doctors_courses_list->doctor_name : 'N/A';

                return "
                    <a title='{$doctor_name}' href='{$url}' target='_blank'>
                        {$doctor_name}
                    </a>
                ";
            })

            ->addColumn('admission_time', function ($doctors_courses_list) {
                return date('d M Y h:m a',strtotime($doctors_courses_list->created_at));
            })

            ->addColumn('payment_completed_by', function ($doctors_courses_list) use ($users) {
                
                if($doctors_courses_list->payment_status == "Completed" && isset($users[$doctors_courses_list->payment_completed_by_id]))
                {
                    return $users[$doctors_courses_list->payment_completed_by_id];
                }
                else if($doctors_courses_list->payment_status == "Completed")
                {
                    return "Online Payment";
                }
                else
                {
                    return "";
                }
                
            })

            // ->addColumn('batch_shifted',function($doctors_courses_list){
            //     $data['doctors_courses_list'] = $doctors_courses_list;
            //     return view('admin.doctors_courses.batch_shifted',$data);
            // })

             ->rawColumns(['doctor_name' , 'action'])

         ->make(true);
    }

    public function doctor_course_active_list()
    {  
        $title = 'SIF Admin : Doctors Courses Active List';
        $batches = Batches::get()->pluck('name', 'id');
        $courses= Courses::whereIn('id',CourseYear::where(['status'=>'1'])->pluck('course_id')->toArray())->pluck('name', 'id');
        $sessions = Sessions::get()->pluck('name', 'id');
        $subjects = Subjects::get()->pluck('name', 'id');
        $administrators = User::orderBy('name')->get(['id', 'name', 'phone_number']);
        $batchespayment = collect(array('In Progress'=>'In Progress','Completed'=>'Completed','Not Completed'=>'Not Completed','No Payment'=>'No Payment'));
        // $years = array(''=>' -- Select year --');
        // for($year = date("Y")+1;$year>=2017;$year--){
        //     $years[$year] = $year;
        // }
        $years = array(''=>'Select year');
        $course_years = CourseYear::where(['status'=>'1'])->pluck('year','year')->toArray();

        $years = array_replace($years, $course_years);

        // for($year = date("Y")+1;$year>=2017;$year--){
        //     $years[$year] = $year;
        // }

        return view('admin.doctors_courses.list_active', compact(
            'title',
            'sessions',
            'batches',
            'years',
            'courses',
            'subjects',
            'administrators',
            'batchespayment'
        ));
    }

    public function doctor_course_active_list_ajax(Request $request)
    {
        $users = User::pluck('name','id')->toArray();
        $year = $request->year;
        $session_id = $request->session_id;
        $course_id = $request->course_id;
        $batch_id = $request->batch_id;
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $lecture_sheet_status = $request->lecture_sheet_status;
        $subject_id = $request->subject_id;
        $payment_status = $request->payment_status;  
        $payment_completed_by_id = $request->payment_completed_by_id;

        $doctors_courses_list = DB::table('doctors_courses as d1')
            ->where(function($query){
                $course_years = CourseYear::where(['status'=>'1'])->get();
                foreach($course_years as $course_year)
                {
                    $query->orWhere(function ($query) use ($course_year){
                        $query->Where(['d1.course_id'=>$course_year->course_id,'d1.year'=>$course_year->year]);    
                    });
                }               
                
            })
            ->leftjoin('doctors as d2', 'd1.doctor_id', '=','d2.id' )
            ->leftjoin('institutes as d3', 'd1.institute_id', '=','d3.id' )
            ->leftjoin('courses as d4', 'd1.course_id', '=','d4.id')
            ->leftjoin('faculties as d5', 'd1.faculty_id', '=','d5.id')
            ->leftjoin('subjects as d6', 'd1.subject_id', '=','d6.id')
            ->leftjoin('subjects as bs', 'd1.bcps_subject_id', '=','bs.id')
            ->leftjoin('batches as d7', 'd1.batch_id', '=','d7.id')
            ->leftjoin('sessions as d8', 'd1.session_id', '=','d8.id')
            ->leftjoin('branches as d10', 'd1.branch_id', '=','d10.id')
            ;

        if($year){
            $doctors_courses_list = $doctors_courses_list->where('d1.year', '=', $year);
        }
        if($session_id){
            $doctors_courses_list = $doctors_courses_list->where('d1.session_id', '=', $session_id);
        }
        if($course_id){
            $doctors_courses_list = $doctors_courses_list->where('d1.course_id', '=', $course_id);
        }
        if($batch_id){
            $doctors_courses_list = $doctors_courses_list->where('d1.batch_id', '=', $batch_id);
        }
       
        if($start_date && $end_date){
            $doctors_courses_list = $doctors_courses_list->whereBetween('d1.created_at', [$start_date, $end_date]);
        }

        if($lecture_sheet_status != 'all'){
            $doctors_courses_list = $doctors_courses_list->where('d1.lecture_sheet_delivery_status', '=', $lecture_sheet_status )->where('d1.include_lecture_sheet', '=', '1');
        }

        if($subject_id){
            $doctors_courses_list = $doctors_courses_list->where('d1.subject_id', '=', $subject_id);
        }

        if($payment_status){
            $doctors_courses_list = $doctors_courses_list->where('d1.payment_status', '=', $payment_status);
        }

        if($payment_completed_by_id){
            $doctors_courses_list = $doctors_courses_list->where('d1.payment_completed_by_id', '=', $payment_completed_by_id);
        }

        $doctors_courses_list = $doctors_courses_list->where('is_trash', '=', 0);

        $doctors_courses_list = $doctors_courses_list->select(
            'd1.id as id',
            'd1.lecture_sheet_delivery_status as lecture_sheet_delivery_status',
            'd1.reg_no as reg_no',
            'd1.roll as roll',
            'd1.include_lecture_sheet as include_lecture_sheet',
            'd1.course_id',
            'd1.year as year',
            'd1.payment_status as payment_status',
            'd1.batch_shifted as batch_shifted',
            'd1.payment_completed_by_id',
            'd1.created_at as created_at',
            'd1.status as status',
            'd2.id as doctor_id',
            'd2.name as doctor_name',
            'd2.mobile_number as mobile_number',
            'd2.main_password as main_password',
            'd2.bmdc_no as bmdc_no',
            'd3.name as institute_name',
            'd4.name as course_name',
            'd5.name as faculty_name',
            'd6.name as subject_name',
            'bs.name as bcps_subject_name',
            'd7.name as batch_name',
            'd8.name as session_name',
            'd10.name as branch_name'
        );
        
        return $table =  Datatables::of($doctors_courses_list)
            ->addColumn('action', function ($doctors_courses_list) {

                if($doctors_courses_list->lecture_sheet_delivery_status){
                    $data['lecture_sheet_delivery_status'] = $doctors_courses_list->lecture_sheet_delivery_status;
                }else{
                    $data['lecture_sheet_delivery_status'] = $this->doctor_course_lecture_sheet_delivery_complete($doctors_courses_list->id);
                }
                $data['doctors_courses_list'] = $doctors_courses_list;
                

                return view('admin.doctors_courses.ajax_list',$data);
            })

            ->addColumn( 'doctor_name', function ($doctors_courses_list) {

                if(!Auth::user()->can('Go To Doctor Profile')) {
                    return $doctors_courses_list->doctor_name ?? '';
                }
                
                $url = route('go-to-doctor-profile', $doctors_courses_list->doctor_id);
                $doctor_name = strlen($doctors_courses_list->doctor_name) ? $doctors_courses_list->doctor_name : 'N/A';

                return "
                    <a title='{$doctor_name}' href='{$url}' target='_blank'>
                        {$doctor_name}
                    </a>
                ";
            })

            ->addColumn('admission_time', function ($doctors_courses_list) {
                return date('d M Y h:m a',strtotime($doctors_courses_list->created_at));
            })

            ->addColumn('payment_completed_by', function ($doctors_courses_list) use ($users) {
                
                if($doctors_courses_list->payment_status == "Completed" && isset($users[$doctors_courses_list->payment_completed_by_id]))
                {
                    return $users[$doctors_courses_list->payment_completed_by_id];
                }
                else if($doctors_courses_list->payment_status == "Completed")
                {
                    return "Online Payment";
                }
                else
                {
                    return "";
                }
                
            })

         ->rawColumns(['doctor_name' , 'action'])->make(true);
    }



    public function institute_change_in_installemnt_due_list(Request $request)
    {
        $institute = Institutes::where('id',$request->institute_id)->first();

        $data['courses'] = Courses::where(['institute_id'=>$institute->id])->pluck('name','id');
        
        return view('admin.doctors_courses.installment_payment.ajax.installment_payment_course',$data);

    }

    public function course_change_in_installemnt_due_list(Request $request)
    {
        $years = CourseYear::where('course_id',$request->course_id)->distinct()->orderBy('year','desc')->pluck('year');

        $custom_years = array();
        if(isset($years) && count($years))
        {
            foreach($years as $year)
            {
                $custom_years[$year] = $year;
            }
        }

        $data['years'] = collect($custom_years);
        
        return view('admin.doctors_courses.installment_payment.ajax.installment_payment_year',$data);

    }

    public function year_change_in_installemnt_due_list(Request $request)
    {

        $data['sessions'] = Sessions::join('course_year_session','course_year_session.session_id','sessions.id')
            ->join( 'course_year', 'course_year.id', 'course_year_session.course_year_id' )
            ->where('course_year.deleted_at',NULL)
            ->where('course_year_session.deleted_at',NULL)
            ->where('course_year.course_id',$request->course_id)
            ->where('course_year.year',$request->year)
            //->where('show_admission_form','yes')
            ->where('course_year.status',1)
            ->pluck('sessions.name',  'sessions.id');
        
        return view('admin.doctors_courses.installment_payment.ajax.installment_payment_session',$data);

    }

    public function session_change_in_installemnt_due_list(Request $request)
    {

        $data['batches'] = Batches::where('course_id',$request->course_id)
            ->where('year',$request->year)->where('session_id',$request->session_id)
            //->where('course_year.status',1)
            ->pluck('name',  'id');
        
        return view('admin.doctors_courses.installment_payment.ajax.installment_payment_batch',$data);

    }
    

    public function installment_due_list()
    {
        $data['institutes'] = Institutes::active()->pluck('name','id');
        $data['courses'] = Courses::active()->pluck('name','id');
        $data['years'] = CourseYear::distinct()->pluck('year','year');
        $session_ids = CourseYearSession::join('course_year','course_year_session.course_year_id','course_year.id')->pluck('session_id');
        $data['sessions'] = Sessions::whereIn('id',$session_ids)->pluck('name','id');
        $data['batches'] = Batches::pluck('name','id');

        return view('admin.doctors_courses.installment_payment.installment_payment_list',$data);

    }

    public function installment_due_ajax_list(Request $request)
    {
        
        $users = User::pluck('name','id')->toArray();
        $year = $request->year;
        $session_id = $request->session_id;
        $institute_id = $request->institute_id;
        $course_id = $request->course_id;
        $batch_id = $request->batch_id;
        $start_date = $request->start_date?date_create_from_format("Y-m-d", $request->start_date, new \DateTimeZone("Asia/Dhaka"))->getTimestamp():'';
        $end_date = $request->end_date?date_create_from_format("Y-m-d", $request->end_date, new \DateTimeZone("Asia/Dhaka"))->getTimestamp():'';
        
        // $doctors_courses_list = DoctorsCourses::where(['doctors_courses.is_trash'=>'0'])                                                                                  
        //                                     ->leftJoin('institutes as d2','d2.id','doctors_courses.institute_id')
        //                                     ->leftJoin('courses as d3','d3.id','doctors_courses.course_id')
        //                                     ->leftJoin('sessions as d4','d4.id','doctors_courses.session_id')
        //                                     ->leftJoin('batches as d5','d5.id','doctors_courses.batch_id')
        //                                     ->leftJoin('faculties as d6','d6.id','doctors_courses.faculty_id')
        //                                     ->leftJoin('subjects as d7','d7.id','doctors_courses.subject_id')
        //                                     ->leftJoin('doctors as d8','d8.id','doctors_courses.doctor_id')
        //                                     ->leftJoin('batches as d9','d9.id','doctors_courses.batch_id');

        // $doctors_courses_list = $doctors_courses_list->select(
        //                                     'doctors_courses.*',
        //                                     'd2.name as institute_name',
        //                                     'd3.name as course_name',
        //                                     'd4.name as session_name',
        //                                     'd5.name as batch_name',
        //                                     'd6.name as faculty_name',
        //                                     'd7.name as subject_name',
        //                                     'd8.name as doctor_name',
        //                                     'd8.mobile_number as phone_number',
        //                                     'd9.payment_times as batch_payment_times',
                                            
        //                                 );

        // if($institute_id){
        //     $doctors_courses_list = $doctors_courses_list->where('doctors_courses.institute_id', '=', $institute_id);
        // }
        // if($year){
        //     $doctors_courses_list = $doctors_courses_list->where('doctors_courses.year', '=', $year);
        // }
        // if($session_id){
        //     $doctors_courses_list = $doctors_courses_list->where('doctors_courses.session_id', '=', $session_id);
        // }
        // if($course_id){
        //     $doctors_courses_list = $doctors_courses_list->where('doctors_courses.course_id', '=', $course_id);
        // }
        // if($batch_id){
        //     $doctors_courses_list = $doctors_courses_list->where('doctors_courses.batch_id', '=', $batch_id);
        // }
        // if($start_date && $end_date){
        //     $doctors_courses_list = $doctors_courses_list->filter('next_installment_last_date', [$request->start_date, $request->end_date]);
        // }

        // $doctors_courses_list = $doctors_courses_list->Where('doctors_courses.payment_option','!=','single');
        // $doctors_courses_list = $doctors_courses_list->Where('doctors_courses.payment_status','!=','Completed');
        // $doctors_courses_list = $doctors_courses_list->Where('d9.payment_times','>','1');

        // $doctors_courses_list = $doctors_courses_list->orderBy('doctors_courses.id','desc');

        $doctors_courses_list = DoctorsCourses::with(['bcps_subject','doctor','batch','institute','course','session','faculty','subject'])
                                ->where(['is_trash'=>'0'])
                                ->where('doctors_courses.payment_status','!=','Completed')
                                ->where('doctors_courses.payment_option','!=','single')
                                ->whereHas('batch', function($q)
                                {
                                    $q->where('payment_times', '>', '1');                                
                                })                                
                                ->orderBy('doctors_courses.id','desc');
        if($institute_id){
            $doctors_courses_list = $doctors_courses_list->where('doctors_courses.institute_id', '=', $institute_id);
        }
        if($year){
            $doctors_courses_list = $doctors_courses_list->where('doctors_courses.year', '=', $year);
        }
        if($session_id){
            $doctors_courses_list = $doctors_courses_list->where('doctors_courses.session_id', '=', $session_id);
        }
        if($course_id){
            $doctors_courses_list = $doctors_courses_list->where('doctors_courses.course_id', '=', $course_id);
        }
        if($batch_id){
            $doctors_courses_list = $doctors_courses_list->where('doctors_courses.batch_id', '=', $batch_id);
        }

        return Datatables::of($doctors_courses_list)
            ->editColumn('doctors_courses.bcps_subject_id', function ($doctors_courses_list) {

                $subject = Subjects::where(['id'=>$doctors_courses_list->bcps_subject_id])->first();
                return $subject->name??'';
            })
            ->addColumn('installments', function ($doctors_courses_list) {

                $doctor_course = DoctorsCourses::where(['id'=>$doctors_courses_list->id])->first();
                $data['doctor_course'] = $doctor_course;               

                return view('admin.doctors_courses.installment_payment.installments',$data);
            })
            ->addColumn('next_installment_last_date', function ($doctors_courses_list) {

                return $doctors_courses_list->next_installment_last_date()??'';
                
            })
            ->addColumn('action', function ($doctors_courses_list) {

                $data['doctors_courses_list'] = $doctors_courses_list;               

                return view('admin.doctors_courses.installment_payment.installment_payment_ajax_list',$data);
            })
            ->rawColumns(['installments','payments','action'])
            ->make(true);
    }

    public function sms_to_installment_due_list_from_admin(Request $request)
    {
        $doctor_course = DoctorsCourses::where(['id'=>$request->doctor_course_id])->first();
        if(isset($doctor_course))
        {
            $msg = 'Dear Doctor, '.'Please pay your next installment for batch '.$doctor_course->batch->name.', reg no : '.$doctor_course->reg_no.' before '.$doctor_course->next_installment_last_date().' . Click to pay https://www.genesisedu.info/payment-details Thank you, GENESIS.';
            $this->send_custom_sms($doctor_course->doctor,$msg,'Installment Due Reminder',$isAdmin = true);
        }

        return  json_encode(array('message'=>"success",'doctor_course_id'=>$request->doctor_course_id,'mobile_number'=>$doctor_course->doctor->mobile_number), JSON_FORCE_OBJECT);

    }

    public function sms_to_installment_due_list()
    {
        $doctors_courses = DoctorsCourses::with('batch')->where(['is_trash'=>'0'])
                                        ->where('payment_option','!=','single')
                                        ->where('payment_status','!=','Completed')
                                        ->whereHas('batch', function($q){
                                            $q->where('payment_times', '>' , 1);
                                        })
                                        ->get()
                                        ;
        if(isset($doctors_courses) && count($doctors_courses))
        {
            $today = new \DateTime("now", new \DateTimeZone('Asia/Dhaka'));
            $today = $today->getTimestamp();
            $custom_doctor_courses = array();
            foreach($doctors_courses as $k=>$doctor_course)
            {
                if($doctor_course->next_installment_last_date())
                {
                    $payment_date = date_create_from_format("Y-m-d",$doctor_course->next_installment_last_date())->getTimestamp();
                    $diff = $payment_date - $today;
                    //if($doctor_course->id == "60370"){echo "<pre>";print_r($doctor_course->next_installment_last_date());exit;}
                    if($diff == 15 * 24 * 3600)
                    {
                        $custom_doctor_courses[] = $doctor_course;
                        $msg = 'Dear Doctor, '.'Please pay your next installment for batch '.$doctor_course->batch->name.', reg no : '.$doctor_course->reg_no.' before '.$doctor_course->next_installment_last_date().' . Thank you, GENESIS.';
                        $this->send_custom_sms($doctor_course->doctor,$msg,'Installment Due Reminder',$isAdmin = true); 
                    }
                    else if($diff == 7 * 24 * 3600)
                    {
                        $custom_doctor_courses[] = $doctor_course;
                        $msg = 'Dear Doctor, '.'Please pay your next installment for batch '.$doctor_course->batch->name.', reg no : '.$doctor_course->reg_no.' before '.$doctor_course->next_installment_last_date().' . Thank you, GENESIS.';
                        $this->send_custom_sms($doctor_course->doctor,$msg,'Installment Due Reminder',$isAdmin = true);
                    }
                    else if($diff == 3 * 24 * 3600)
                    {
                        $custom_doctor_courses[] = $doctor_course;
                        $msg = 'Dear Doctor, '.'Please pay your next installment for batch '.$doctor_course->batch->name.', reg no : '.$doctor_course->reg_no.' before '.$doctor_course->next_installment_last_date().' . Thank you, GENESIS.';
                        $this->send_custom_sms($doctor_course->doctor,$msg,'Installment Due Reminder',$isAdmin = true);
                    }
                    else if($diff == 1 * 24 * 3600)
                    {
                        $custom_doctor_courses[] = $doctor_course;
                        $msg = 'Dear Doctor, '.'Please pay your next installment for batch '.$doctor_course->batch->name.', reg no : '.$doctor_course->reg_no.' before '.$doctor_course->next_installment_last_date().' . Thank you, GENESIS.';
                        $this->send_custom_sms($doctor_course->doctor,$msg,'Installment Due Reminder',$isAdmin = true);
                    }
                    else if($diff >= 0 && $diff <= 1 * 24 * 3600)
                    {
                        $custom_doctor_courses[] = $doctor_course;
                        $msg = 'Dear Doctor, '.'Please pay your next installment for batch '.$doctor_course->batch->name.', reg no : '.$doctor_course->reg_no.' before '.$doctor_course->next_installment_last_date().' . Thank you, GENESIS.';
                        $this->send_custom_sms($doctor_course->doctor,$msg,'Installment Due Reminder',$isAdmin = true);
                    }                
                }
            }
            $data['doctors_courses'] = $custom_doctor_courses;
        }

        //echo "<pre>";print_r($doctors_courses);exit;

        $data['title'] = "Installment Due Reminder";

        return view('admin.doctors_courses.installment_payment.installment_due_reminder',$data);

    }

    public function html()
    {
        return $this->builder()
            ->columns([
                'id',
                'name',
                'email',
                'created_at',
                'updated_at',
            ])
            ->parameters([
                'dom' => 'Bfrtip',
                'buttons' => ['csv', 'excel', 'pdf', 'print', 'reset', 'reload'],
            ]);
    }

    public function doctor_course_lecture_sheet_delivery_complete($doctor_course_id)
    {
        $data['doctor_course'] = DoctorsCourses::where(['id' => $doctor_course_id])->first();
        $doctor_course = DoctorsCourses::where(['id' => $doctor_course_id])->first();

        $lecture_sheet_topic_batch = LectureSheetTopicBatch::where(['year' => $doctor_course->year, 'session_id' => $doctor_course->session_id, 'batch_id' => $doctor_course->batch_id])->first();


        if (isset($doctor_course->batch->fee_type) && isset($lecture_sheet_topic_batch->id)) {
            if ($doctor_course->batch->fee_type == "Batch") {
                $lecture_sheet_topics = LectureSheetTopicBatch::where('lecture_sheet_topic_batch.id', $lecture_sheet_topic_batch->id)
                    ->join('lecture_sheet_topic_batch_lecture_sheet_topic', 'lecture_sheet_topic_batch_lecture_sheet_topic.lecture_sheet_topic_batch_id', 'lecture_sheet_topic_batch.id')
                    ->join('lecture_sheet_topic', 'lecture_sheet_topic.id', 'lecture_sheet_topic_batch_lecture_sheet_topic.lecture_sheet_topic_id')

                    // ->whereNull(['lecture_sheet_topic_batch_lecture_sheet_topic.deleted_at' , 'lecture_sheet_topic.deleted_at'])
                    ->paginate(100)
                ;

            } else if ($doctor_course->batch->fee_type == "Discipline_Or_Faculty") {

                if ($doctor_course->institute->type == 1) {

                    $faculty_name = Faculty::where('id', $doctor_course->faculty_id)->value('name');
                    $faculty_ids = Faculty::where('name', $faculty_name)->pluck('id');
                    //dd($faculty_ids);
                    $lecture_sheet_topics = LectureSheetTopicBatch::select('lecture_sheet_topic.*')->where('lecture_sheet_topic_batch.id', $lecture_sheet_topic_batch->id)->whereIn('lecture_sheet_topic_faculty.faculty_id', $faculty_ids)
                        ->join('lecture_sheet_topic_batch_lecture_sheet_topic', 'lecture_sheet_topic_batch_lecture_sheet_topic.lecture_sheet_topic_batch_id', 'lecture_sheet_topic_batch.id')
                        ->join('lecture_sheet_topic', 'lecture_sheet_topic.id', 'lecture_sheet_topic_batch_lecture_sheet_topic.lecture_sheet_topic_id')
                        ->join('lecture_sheet_topic_faculty', 'lecture_sheet_topic_faculty.lecture_sheet_topic_id', 'lecture_sheet_topic.id')

                        // ->whereNull(['lecture_sheet_topic_batch_lecture_sheet_topic.deleted_at' , 'lecture_sheet_topic.deleted_at' ,'lecture_sheet_topic_faculty.deleted_at'])

                        ->paginate(100)
                    ;

                } else {
                    $subject_name = Subjects::where('id', $doctor_course->subject_id)->value('name');
                    $subject_ids = Subjects::where('name', $subject_name)->pluck('id');
                    //dd($subject_ids);
                    $lecture_sheet_topics = LectureSheetTopicBatch::select('lecture_sheet_topic.*')->where('lecture_sheet_topic_batch.id', $lecture_sheet_topic_batch->id)->whereIn('lecture_sheet_topic_discipline.subject_id', $subject_ids)
                        ->join('lecture_sheet_topic_batch_lecture_sheet_topic', 'lecture_sheet_topic_batch_lecture_sheet_topic.lecture_sheet_topic_batch_id', 'lecture_sheet_topic_batch.id')
                        ->join('lecture_sheet_topic', 'lecture_sheet_topic.id', 'lecture_sheet_topic_batch_lecture_sheet_topic.lecture_sheet_topic_id')
                        ->join('lecture_sheet_topic_discipline', 'lecture_sheet_topic_discipline.lecture_sheet_topic_id', 'lecture_sheet_topic.id')

                        // ->whereNull(['lecture_sheet_topic_batch_lecture_sheet_topic.deleted_at' , 'lecture_sheet_topic.deleted_at' ,'lecture_sheet_topic_discipline.deleted_at'])
                        ->paginate(100)
                    ;

                }

            }

        }

        //dd($lecture_sheet_topic_batch);

        if (isset($lecture_sheet_topics)) {

            $lecture_sheet_topic_ids = array();
            foreach ($lecture_sheet_topics as $lecture_sheet_topic) {
                $lecture_sheet_topic_ids[] = $lecture_sheet_topic->id;
            }

            $lecture_sheets = LectureSheet::join('lecture_sheet_topic_lecture_sheet','lecture_sheet_topic_lecture_sheet.lecture_sheet_id','=','lecture_sheet.id')->whereIn('lecture_sheet_topic_id',$lecture_sheet_topic_ids)
            ->whereNull('lecture_sheet_topic_lecture_sheet.deleted_at')
            ->get();

            $data['lecture_sheets'] = $lecture_sheets;
            //dd($lecture_sheets);

            $array_delivered_lecture_sheets = array();


            foreach ($lecture_sheets as $lecture_sheet) {
                if (DoctorCourseLectureSheet::where(['doctor_course_id' => $doctor_course->id, 'lecture_sheet_id' => $lecture_sheet->id])->first()) {
                    $array_delivered_lecture_sheets[] = $lecture_sheet->id; 
                }

            }

            $data['delivered_lecture_sheets'] = $array_delivered_lecture_sheets;

            if(count($array_delivered_lecture_sheets) == 0)
            {
                $doctor_courses = DoctorsCourses::where(['id' => $doctor_course_id])->first();
                $doctor_courses->lecture_sheet_delivery_status ="Not_Delivered";
                $doctor_courses->push();
                return "Not_Delivered";

            }
            else if(count($array_delivered_lecture_sheets)  == count($lecture_sheets))
            {
                $doctor_courses = DoctorsCourses::where(['id' => $doctor_course_id])->first();
                $doctor_courses->lecture_sheet_delivery_status ="Completed";
                $doctor_courses->push();
                return "Completed";

            }
            else
            {
                $doctor_courses = DoctorsCourses::where(['id' => $doctor_course_id])->first();
                $doctor_courses->lecture_sheet_delivery_status ="In_Progress";
                $doctor_courses->push();
                return "In_Progress";
            }

        }
        else
        {
            $doctor_courses = DoctorsCourses::where(['id' => $doctor_course_id])->first();
            $doctor_courses->lecture_sheet_delivery_status ="Not_Delivered";
            $doctor_courses->push();
            return "Not_Delivered";
        }



    }

    public function doctor_course_lecture_sheet_list( $doctor_course_id ){
        $data['couriers'] = Courier::pluck('name','id');
        $data['doctor_course'] = DoctorsCourses::where(['id' => $doctor_course_id])->first();
        $data['first_shipment'] = LectureSheetDeliveryStatus::where(['doctor_course_id' => $doctor_course_id, 'shipment'=> 'first'])->first();
        $data['second_shipment'] = LectureSheetDeliveryStatus::where(['doctor_course_id' => $doctor_course_id, 'shipment'=> 'second'])->first();
        $doctor_course = DoctorsCourses::where(['id' => $doctor_course_id])->first();
        $lecture_sheet_topic_batch = LectureSheetTopicBatch::where([
            'year' => $doctor_course->year ?? '',
            'session_id' => $doctor_course->session_id ?? '',
            'batch_id' => $doctor_course->batch_id ?? ''
        ])->first();

        $lecture_sheet_topic_ids = DoctorLectureSheetHelper::
                lectureSheetTopics( $doctor_course, $lecture_sheet_topic_batch->id ?? '')
                ->pluck('lecture_sheet_topic.id' );


        LectureSheet::$deliveredCourseId = $doctor_course_id;
        $lecture_sheets = LectureSheet::query( )
            ->with('doctor_delivered' )
            ->join('lecture_sheet_topic_lecture_sheet','lecture_sheet_topic_lecture_sheet.lecture_sheet_id','=','lecture_sheet.id')
            ->whereIn('lecture_sheet_topic_id',$lecture_sheet_topic_ids )
            ->whereNull('lecture_sheet_topic_lecture_sheet.deleted_at')
            ->select( 'lecture_sheet.*' )
            ->get();

        $data['lecture_sheets'] = $lecture_sheets;
        $data['count']  =$lecture_sheets->count();

        return view('admin.doctors_courses.lecture_sheet',$data);

    }

    public function doctor_course_lecture_sheet(Request $request)
    {

        $doctor_course = DoctorsCourses::with('batch')->where(['id'=>$request->doctor_course_id])->first();

        if ( DoctorCourseLectureSheet::where('doctor_course_id', $request->doctor_course_id)->first() ) {
            DoctorCourseLectureSheet::where('doctor_course_id', $request->doctor_course_id)->update( ['deleted_by' => Auth::id()]);
            DoctorCourseLectureSheet::where('doctor_course_id', $request->doctor_course_id)->delete();
        }

        $select_lecture_sheet=$request->lecture_sheet_id;
    
        
        if($select_lecture_sheet)
        {


            foreach ($select_lecture_sheet as $key => $value) {
                if( $value != null) {
                    DoctorCourseLectureSheet::withTrashed()->updateOrInsert([
                        'doctor_course_id' => $request->doctor_course_id,
                        'lecture_sheet_id' => $value
                    ],
                    [
                        'deleted_at' =>  null,
                        'deleted_by' =>  null,
                    ]);
                }
            }
        }

        if($select_lecture_sheet && count($select_lecture_sheet) < $request->lecture_sheet_number )
        {
            $doctor_course=DoctorsCourses::where(['id' => $request->doctor_course_id])->first();
            $doctor_course->lecture_sheet_delivery_status = "In_Progress";
            $doctor_course->push();

        }
        if($request->lecture_sheet_id && count($select_lecture_sheet) == $request->lecture_sheet_number )
        {
            $doctor_course=DoctorsCourses::where(['id' => $request->doctor_course_id])->first();
            $doctor_course->lecture_sheet_delivery_status = "Completed";
            $doctor_course->push();

        }
        
        if(LectureSheetDeliveryStatus::where('doctor_course_id',$doctor_course->id)->exists()){
            $lecture_sheet_delivery_status = new LectureSheetDeliveryStatus;
            $lecture_sheet_delivery_status->doctor_course_id = $doctor_course->id;
            $lecture_sheet_delivery_status->lecture_sheet_delivery_status = 'In_Progress';
            $lecture_sheet_delivery_status->shipment = 'second';
            $lecture_sheet_delivery_status->lecture_sheet_ids = json_encode($request->lecture_sheet_id) ??' ';
            $lecture_sheet_delivery_status->save();

        }else{
            $lecture_sheet_delivery_status = new LectureSheetDeliveryStatus;
            $lecture_sheet_delivery_status->doctor_course_id = $doctor_course->id;
            $lecture_sheet_delivery_status->lecture_sheet_delivery_status = 'In_Progress';
            $lecture_sheet_delivery_status->shipment = "first";
            $lecture_sheet_delivery_status->lecture_sheet_ids = json_encode($request->lecture_sheet_id) ??' ';
            $lecture_sheet_delivery_status->save();
        }

        if($request->submit_print == "Submit & Print")
        {
            return redirect('admin/doctor-lecture-sheet-delivery-print/'.$request->doctor_course_id);
        }
        else
        {
            return redirect('admin/doctor-course-lecture-sheet-list/'.$request->doctor_course_id);
        }
        
    }


    protected function message(Request $request){
        
        $doctor_course = DoctorsCourses::where(['id'=>$request->doctor_course_id])->first();

        $doctor_course->courier_id = $request->courier_id;
        $doctor_course->courier_memo_no = $request->courier_memo_no;
        $doctor_course->lecture_sheet_packet = $request->lecture_sheet_packet;
        $doctor_course->push();

        
        $lecture_sheet_delivery_story=LectureSheetDeliveryStatus::where(['doctor_course_id'=>$doctor_course->id,'shipment'=>'first'])->first();
        $lecture_sheet_delivery_story->courier_memo_no = $request->courier_memo_no;
        $lecture_sheet_delivery_story->courier_id = $request->courier_id;
        $lecture_sheet_delivery_story->lecture_sheet_delivery_status = 'In_Courier';
        $lecture_sheet_delivery_story->packet = $request->lecture_sheet_packet;
        $lecture_sheet_delivery_story->push();

        $admin_id = Auth::id();

        $smsLog = new SmsLog();
        $response = null;

        $doc_course=DoctorsCourses::find($request->doctor_course_id);
        $doctors=Doctors::where('id',$doc_course->doctor_id)->first();
        $courier_name= Courier::where('id',$request->courier_id)->value('name');
        $doctor_selected_batch = Batches::where(['id'=>$doc_course->batch_id])->first();
        $websitename='www.genesisedu.info';
        $mob = '88' . $doctors->mobile_number;
        $msg = 'Dear Doctor,'.' your lecture sheets/books for "'.$doctor_selected_batch->name.'" batch sent in "'.  $courier_name .'"  Memo No: '.$request->courier_memo_no.' ('.$request->lecture_sheet_packet .') '.$lecture_sheet_delivery_story->shipment.' shipment for further info : '.$doc_course->batch->lecture_sheet_mobile_no.'. '.$websitename.'. Thank you, GENESIS.';


        // $ch = curl_init();
        // curl_setopt($ch, CURLOPT_URL, "http://sms4.digitalsynapsebd.com/api/mt/SendSMS?user=genesispg&password=123321@12&senderid=8809612440402&channel=Normal&DCS=0&flashsms=0&number=$mob&text=$msg");
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        // $response = curl_exec($ch);
        // curl_close($ch);
        // $smsLog->set_response( $response,$doctors->id,$mob,$admin_id)->set_event('Lecture Sheets')->save();
        $this->send_custom_sms($doctors,$msg,'Lecture Sheets',$isAdmin = true); 
        return redirect('admin/doctor-course-lecture-sheet-list/'.$request->doctor_course_id);

    }

    protected function message2(Request $request){
        
        $doctor_course = DoctorsCourses::where(['id'=>$request->doctor_course_id])->first();
        
        $lecture_sheet_delivery_story=LectureSheetDeliveryStatus::where(['doctor_course_id'=>$doctor_course->id,'shipment'=>'Second'])->first();
        $lecture_sheet_delivery_story->courier_memo_no = $request->courier_memo_no;
        $lecture_sheet_delivery_story->courier_id = $request->courier_id;
        $lecture_sheet_delivery_story->lecture_sheet_delivery_status = 'In_Courier';
        $lecture_sheet_delivery_story->packet = $request->lecture_sheet_packet;
        $lecture_sheet_delivery_story->push();

        $admin_id = Auth::id();
        $smsLog = new SmsLog();
        $response = null;

        $doc_course=DoctorsCourses::find($request->doctor_course_id);
        $doctors=Doctors::where('id',$doc_course->doctor_id)->first();
        $courier_name= Courier::where('id',$request->courier_id)->value('name');
        $doctor_selected_batch = Batches::where(['id'=>$doc_course->batch_id])->first();
        $websitename='www.genesisedu.info';
        $mob = '88' . $doctors->mobile_number;
        $msg = 'Dear Doctor,'.' your lecture sheets/books for "'.$doctor_selected_batch->name.'" batch sent in "'.  $courier_name .'"  Memo No: '.$request->courier_memo_no.' ('.$request->lecture_sheet_packet .') '.$lecture_sheet_delivery_story->shipment.' shipment for further info : '.$doc_course->batch->lecture_sheet_mobile_no.'. '.$websitename.'. Thank you, GENESIS.';


        // $ch = curl_init();
        // curl_setopt($ch, CURLOPT_URL, "http://sms4.digitalsynapsebd.com/api/mt/SendSMS?user=genesispg&password=123321@12&senderid=8809612440402&channel=Normal&DCS=0&flashsms=0&number={$mob}&text=" . rawurlencode( $msg ) );
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        // $response = curl_exec($ch);
        // curl_close($ch);
        // $smsLog->set_response( $response,$doctors->id,$mob,$admin_id)->set_event('Lecture Sheets')->save();
        $this->send_custom_sms($doctors,$msg,'Lecture Sheets',$isAdmin = true);
        return redirect('admin/doctor-course-lecture-sheet-list/'.$request->doctor_course_id);

    }
        
    public function doctor_course_lecture_sheet_delivery_print($doctor_course_id)
    {
        $data['doctor_course'] = DoctorsCourses::where(['id' => $doctor_course_id])->first();
        $doctor_course = DoctorsCourses::where(['id' => $doctor_course_id])->first();
        
        $lecture_sheet_topic_batch = LectureSheetTopicBatch::where(['year' => $doctor_course->year, 'session_id' => $doctor_course->session_id, 'batch_id' => $doctor_course->batch_id])->first();



        $lecture_sheet_topic_ids = DoctorLectureSheetHelper::
            lectureSheetTopics( $doctor_course, $lecture_sheet_topic_batch->id ?? '')
            ->pluck('lecture_sheet_topic.id' );
        LectureSheet::$deliveredCourseId = $doctor_course_id;
        $lecture_sheets = LectureSheet::query( )
            ->with('doctor_delivered' )
            ->join('lecture_sheet_topic_lecture_sheet','lecture_sheet_topic_lecture_sheet.lecture_sheet_id','=','lecture_sheet.id')
            ->whereIn('lecture_sheet_topic_id',$lecture_sheet_topic_ids )
            ->whereNull('lecture_sheet_topic_lecture_sheet.deleted_at')
            ->select( 'lecture_sheet.*' )
            ->get();

        $data['lecture_sheets'] = $lecture_sheets;
        $data['count']  = $lecture_sheets->count();

        return view('admin.doctors_courses.print_lecture_sheet_delivery',$data);

        if ( isset( $doctor_course->batch->fee_type )   ) {



            if ($doctor_course->batch->fee_type == "Batch") {


                if( $doctor_course->institute->id == AppServiceProvider::$COMBINED_INSTITUTE_ID ) {

                    $lecture_sheet_topics = LectureSheetTopic::select('*');

                    $faculty_name = Faculty::where('id',$doctor_course->faculty_id)->value( 'name' );
                    $faculty_ids = Faculty::where('name',$faculty_name)->pluck('id');
                    $bcps_subject_name = Subjects::where('id',$doctor_course->bcps_subject_id)->value( 'name' );
                    $bcps_subject_ids = Subjects::where('name',$bcps_subject_name)->pluck( 'id' );


                    $subject_lecture_sheet_topic_ids = LectureSheetTopicDiscipline::whereIn( 'subject_id', $bcps_subject_ids )
                        ->join( 'lecture_sheet_topic_assign', 'lecture_sheet_topic_assign.id', 'lecture_sheet_topic_discipline.lecture_sheet_topic_assign_id' )
                        ->whereNull( 'lecture_sheet_topic_assign.deleted_at' )
                        ->where( 'lecture_sheet_topic_assign.institute_id', AppServiceProvider::$COMBINED_INSTITUTE_ID  )
                        ->pluck( 'lecture_sheet_topic_discipline.lecture_sheet_topic_id' );

                    $discipline_lecture_sheet_topic_ids = LectureSheetTopicFaculty::whereIn( 'faculty_id', $faculty_ids )
                        ->join( 'lecture_sheet_topic_assign', 'lecture_sheet_topic_assign.id', 'lecture_sheet_topic_faculty.lecture_sheet_topic_assign_id' )
                        ->whereNull( 'lecture_sheet_topic_assign.deleted_at' )
                        ->where( 'lecture_sheet_topic_assign.institute_id', AppServiceProvider::$COMBINED_INSTITUTE_ID  )
                        ->pluck( 'lecture_sheet_topic_faculty.lecture_sheet_topic_id' );

                    if( isset( $lecture_sheet_topic_batch->id ) ) {
                        $batch_lecture_sheet_topic_ids = LectureSheetTopicBatchLectureSheetTopic::where( 'lecture_sheet_topic_batch_id', $lecture_sheet_topic_batch->id )
                            ->pluck( 'lecture_sheet_topic_id' );
                        $subject_lecture_sheet_topic_ids->merge( $batch_lecture_sheet_topic_ids )->unique();
                    }

                    $topic_ids = $subject_lecture_sheet_topic_ids->merge( $discipline_lecture_sheet_topic_ids );
                    $lecture_sheet_topics = $lecture_sheet_topics->whereIn( 'lecture_sheet_topic.id', $topic_ids )->paginate( 100 );

                } else if( isset( $lecture_sheet_topic_batch->id ) ) {

                    $lecture_sheet_topics = LectureSheetTopicBatch::join('lecture_sheet_topic_batch_lecture_sheet_topic', 'lecture_sheet_topic_batch_lecture_sheet_topic.lecture_sheet_topic_batch_id', 'lecture_sheet_topic_batch.id')
                        ->join('lecture_sheet_topic', 'lecture_sheet_topic.id', 'lecture_sheet_topic_batch_lecture_sheet_topic.lecture_sheet_topic_id')
                        ->whereNull( 'lecture_sheet_topic_batch_lecture_sheet_topic.deleted_at' )
                        ->whereNull( 'lecture_sheet_topic.deleted_at' ) ;

                    $lecture_sheet_topics = $lecture_sheet_topics->where( 'lecture_sheet_topic_batch.id', $lecture_sheet_topic_batch->id )->paginate( 100 );
                }


            } else if ( $doctor_course->batch->fee_type == "Discipline_Or_Faculty" && isset( $lecture_sheet_topic_batch->id ) ) {

                if ($doctor_course->institute->type == 1) {

                    $faculty_name = Faculty::where('id', $doctor_course->faculty_id)->value('name');
                    $faculty_ids = Faculty::where('name', $faculty_name)->pluck('id');
                    $lecture_sheet_topics = LectureSheetTopicBatch::select('lecture_sheet_topic.*')->where('lecture_sheet_topic_batch.id', $lecture_sheet_topic_batch->id)->whereIn('lecture_sheet_topic_faculty.faculty_id', $faculty_ids)
                        ->join('lecture_sheet_topic_batch_lecture_sheet_topic', 'lecture_sheet_topic_batch_lecture_sheet_topic.lecture_sheet_topic_batch_id', 'lecture_sheet_topic_batch.id')
                        ->join('lecture_sheet_topic', 'lecture_sheet_topic.id', 'lecture_sheet_topic_batch_lecture_sheet_topic.lecture_sheet_topic_id')
                        ->join('lecture_sheet_topic_faculty', 'lecture_sheet_topic_faculty.lecture_sheet_topic_id', 'lecture_sheet_topic.id')
                        ->paginate(100)
                    ;

                } else {
                    $subject_name = Subjects::where('id', $doctor_course->subject_id)->value('name');
                    $subject_ids = Subjects::where('name', $subject_name)->pluck('id');
                    //dd($subject_ids);
                    $lecture_sheet_topics = LectureSheetTopicBatch::select('lecture_sheet_topic.*')->where('lecture_sheet_topic_batch.id', $lecture_sheet_topic_batch->id)
                        ->whereIn('lecture_sheet_topic_discipline.subject_id', $subject_ids)
                        ->join('lecture_sheet_topic_batch_lecture_sheet_topic', 'lecture_sheet_topic_batch_lecture_sheet_topic.lecture_sheet_topic_batch_id', 'lecture_sheet_topic_batch.id')
                        ->join('lecture_sheet_topic', 'lecture_sheet_topic.id', 'lecture_sheet_topic_batch_lecture_sheet_topic.lecture_sheet_topic_id')
                        ->join('lecture_sheet_topic_discipline', 'lecture_sheet_topic_discipline.lecture_sheet_topic_id', 'lecture_sheet_topic.id')
                        ->paginate(100)
                    ;

                }

            }

        }

        //dd($lecture_sheet_topic_batch);

        if (isset($lecture_sheet_topics)) {

            $lecture_sheet_topic_ids = array();
            foreach ($lecture_sheet_topics as $lecture_sheet_topic) {
                $lecture_sheet_topic_ids[] = $lecture_sheet_topic->id;
            }

            $lecture_sheets = LectureSheet::join('lecture_sheet_topic_lecture_sheet','lecture_sheet_topic_lecture_sheet.lecture_sheet_id','=','lecture_sheet.id')
            ->whereIn('lecture_sheet_topic_id',$lecture_sheet_topic_ids)
            ->whereNull('lecture_sheet_topic_lecture_sheet.deleted_at')
            ->get();

            $data['lecture_sheets'] = $lecture_sheets;

            $array_delivered_lecture_sheets = array();

            $lect_ids = DoctorCourseLectureSheet::where(['doctor_course_id' => $doctor_course->id])->get();

            foreach (  $lecture_sheets as $lecture_sheet  ) {
                if ( $lect_ids->where( 'lecture_sheet_id', $lecture_sheet->lecture_sheet_id )->count() > 0 ) {
                    $array_delivered_lecture_sheets[] = $lecture_sheet->lecture_sheet_id;
                }
            }

            $data['delivered_lecture_sheets'] = $array_delivered_lecture_sheets;


        }
        return view('admin.doctors_courses.print_lecture_sheet_delivery',$data);
    }

    public function print_courier_address($doctor_course_id)
    {
        $data['doctor_course'] = DoctorsCourses::where(['id'=>$doctor_course_id])->first();
        return view('admin.doctors_courses.print_courier_address',$data);
    }

    public function print_course_details($doctor_course_id)
    {
        // dd($doctor_course_id->amount);
        $data['doctor_course'] = DoctorsCourses::where(['id'=>$doctor_course_id])->first();
        $data['doctor_course_payments'] = DoctorCoursePayment::where(['doctor_course_id'=>$doctor_course_id])->get();
        $doctor_id = $data['doctor_course']->doctor_id;
        $batch_id = $data['doctor_course']->batch_id;
        $data['discounts'] = Discount::where([ 'doctor_id'=> $doctor_id, 'batch_id' => $batch_id])->first();
        return view('admin.doctors_courses.print_doctor_details',$data);
    }

    public function doctors_courses_trash()
    {
        /*  if(!$user->hasRole('Admin')){
              return abort(404);
          }*/

        $title = 'SIF Admin : Doctors Courses List';
        $batches = Batches::get()->pluck('name', 'id');


        // $sessions = DB::table('sessions')->join('course_session','course_session.session_id','=','sessions.id')->pluck(DB::raw('CONCAT(name, " (", duration,")") AS name'), 'sessions.id');

        $sessions = Sessions::
                        join('course_year_session','course_year_session.session_id','sessions.id')
                        ->join( 'course_year', 'course_year.id', 'course_year_session.course_year_id' )
                        ->where('sessions.status',1)
                        ->whereNull('course_year.deleted_at')
                        ->whereNull('course_year_session.deleted_at')
                        ->where('show_admission_form','yes')
                        // ->pluck(DB::raw('CONCAT(name, " (", duration,")") AS name'), 'sessions.id');
                        ->pluck( 'sessions.name',  'sessions.id');
        

        $years = array(''=>'Select year');
        for($year = date("Y")+1;$year>=2017;$year--){
            $years[$year] = $year;
        }
        return view('admin.doctors_courses.trash_list',['title'=>$title,'sessions'=>$sessions,'batches'=>$batches,'years'=>$years]);
    }

    public function doctors_courses_trash_list(Request $request)
    {

        $year = $request->year;
        $session_id = $request->session_id;
        $batch_id = $request->batch_id;

        $doctors_courses_list = DB::table('doctors_courses as d1')
            ->leftjoin('doctors as d2', 'd1.doctor_id', '=','d2.id')
            ->leftjoin('institutes as d3', 'd1.institute_id', '=','d3.id')
            ->leftjoin('courses as d4', 'd1.course_id', '=','d4.id')
            ->leftjoin('faculties as d5', 'd1.faculty_id', '=','d5.id')
            ->leftjoin('subjects as d6', 'd1.subject_id', '=','d6.id')
            ->leftjoin('batches as d7', 'd1.batch_id', '=','d7.id')
            ->leftjoin('sessions as d8', 'd1.session_id', '=','d8.id')
            ->leftjoin('service_packages as d9', 'd1.service_package_id', '=','d9.id')
            ->leftjoin('branches as d10', 'd1.branch_id', '=','d10.id')
            ->leftjoin('users as d11', 'd11.id', '=','d1.trash_by');

        if($year){
            $doctors_courses_list = $doctors_courses_list->where('year', '=', $year);
        }
        if($session_id){
            $doctors_courses_list = $doctors_courses_list->where('session_id', '=', $session_id);
        }
        if($batch_id){
            $doctors_courses_list = $doctors_courses_list->where('batch_id', '=', $batch_id);
        }

        $doctors_courses_list = $doctors_courses_list->where('is_trash', '=', 1);


        $doctors_courses_list = $doctors_courses_list->select('d1.*','d2.name as doctor_name','d2.mobile_number as mobile_number','d2.main_password as main_password','d2.bmdc_no as bmdc_no','d3.name as institute_name','d4.name as course_name','d5.name as faculty_name','d6.name as subject_name','d7.name as batche_name','d8.name as session_name','d9.name as service_package_name','d11.name as trash_by_name','d10.name as branch_name');
        return Datatables::of($doctors_courses_list)
            ->addColumn('action', function ($doctors_courses_list) {
                return view('admin.doctors_courses.trash_ajax_list',(['doctors_courses_list'=>$doctors_courses_list]));
            })

            ->addColumn( 'doctor_name', function ($doctors_courses_list) {

                if(!Auth::user()->can('Go To Doctor Profile')) {
                    return $doctors_courses_list->doctor_name ?? '';
                }
                
                $url = route('go-to-doctor-profile', $doctors_courses_list->doctor_id);
                $doctor_name = strlen($doctors_courses_list->doctor_name) ? $doctors_courses_list->doctor_name : 'N/A';

                return "
                    <a title='{$doctor_name}' href='{$url}' target='_blank'>
                        {$doctor_name}
                    </a>
                ";
            })

            ->addColumn('admission_time', function ($doctors_courses_list) {
                return date('d M Y h:m a',strtotime($doctors_courses_list->created_at));
            })

            ->rawColumns(['action', 'doctor_name'])

            ->make(true);
    }

    public function create()
    {
        // $user=DoctorsCourses::find(Auth::id());

        /*if(!$user->hasRole('Admin')){
            return abort(404);
        }*/

        $data['years'] = array(''=>'Select year');
        for($year = date("Y")+1;$year>=2017;$year--){
            $data['years'][$year] = $year;
        }

        $data['title'] = 'SIF Admin : Doctor Courses Create';
        $data['doctors'] = Doctors::select(DB::raw("CONCAT(name,' - ',bmdc_no) AS full_name"),'id')->orderBy('id', 'DESC')->pluck('full_name', 'id');
        $data['branches'] = Branch::where('status',1)->pluck('name', 'id');
        $data['institutes'] = Institutes::get()->pluck('name', 'id');

        //$data['courses'] = Courses::get()->pluck('name', 'id');
        //$data['faculties'] = Faculty::get()->pluck('name', 'id');
        //$data['subjects'] = Subjects::get()->pluck('name', 'id');
        //$data['batches'] = Batches::get()->pluck('name', 'id');
        $data['sessions'] = Sessions::get()->pluck('name', 'id');
        $data['service_packages'] = ServicePackages::get()->pluck('name', 'id');
        $data['coming_bys'] = ComingBy::get()->pluck('name', 'id');

        return view('admin.doctors_courses.create',$data);
    }
    
    // /**
    //  * Store a newly created resource in storage.
    //  *
    //  * @param  \Illuminate\Http\Request  $request
    //  * @return \Illuminate\Http\Response
    //  */
    // public function store(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'doctor_id' => ['required'],
    //         //'year' => ['required'],
    //         'session_id' => ['required'],
    //         'branch_id' => ['required'],
    //         'institute_id' => ['required'],
    //         'course_id' => ['required'],
    //         // 'subject_id' => ['required'],
    //         'batch_id' => ['required'],
    //         'reg_no_first_part' => ['required'],
    //         'reg_no_last_part' => ['required'],
    //         'status' => ['required']
    //     ]);


    //     if ($validator->fails()){
    //         Session::flash('class', 'alert-danger');
    //         session()->flash('message','Please enter proper input values!!!');
    //         return redirect()->action('Admin\DoctorsCoursesController@create')->withInput();
    //     }

    //     $batch = Batches::find( $request->batch_id );

    

    //     $doctor_course = new DoctorsCourses();
    //     $doctor_course->doctor_id =  $request->doctor_id;
    //     $doctor_course->year = $batch->year;
    //     $doctor_course->branch_id = $request->branch_id;
    //     $doctor_course->session_id = $request->session_id;
    //     $doctor_course->institute_id = $request->institute_id;
    //     $doctor_course->course_id = $request->course_id;
    //     $doctor_course->faculty_id = $request->faculty_id;
    //     $doctor_course->subject_id = $request->subject_id;
    //     $doctor_course->bcps_subject_id = $request->bcps_subject_id;
    //     $doctor_course->batch_id = $request->batch_id;
    //     $doctor_course->candidate_type = $request->candidate_type ?? '';
    //     $doctor_course->include_lecture_sheet = $request->include_lecture_sheet;
    //     $doctor_course->delivery_status = $request->delivery_status;
    //     $doctor_course->courier_address = $request->courier_address;
    //     $doctor_course->courier_upazila_id = $request->courier_upazila_id;
    //     $doctor_course->courier_district_id = $request->courier_district_id;
    //     $doctor_course->courier_division_id = $request->courier_division_id;

        
        
    //     $doctor_course_fee = $this->doctor_course_discount($request,$doctor_course);

    //     if(isset($doctor_course_fee) && $doctor_course_fee == "batch_admission_fee_not_set")
    //     {
    //         Session::flash('class', 'alert-danger');
    //         session()->flash('message','The selected batch Admission Fee type is not set yet...!!!');
    //         return redirect()->action('Admin\DoctorsCoursesController@create')->withInput();
    //     }
    //     else if(isset($doctor_course_fee) && $doctor_course_fee == "faculty_admission_fee_not_set")
    //     {
    //         Session::flash('class', 'alert-danger');
    //         session()->flash('message','The selected faculty admission fee is not set yet...!!!');
    //         return redirect()->action('Admin\DoctorsCoursesController@create')->withInput();
    //     }
    //     else if(isset($doctor_course_fee) && $doctor_course_fee == "discipline_admission_fee_not_set")
    //     {
    //         Session::flash('class', 'alert-danger');
    //         session()->flash('message','The selected discipline admission fee is not set yet...!!!');
    //         return redirect()->action('Admin\DoctorsCoursesController@create')->withInput();
    //     }
    //     else
    //     {
    //         $actual_course_price = $doctor_course_fee->actual_course_price;
    //         $course_price = $doctor_course_fee->course_price - ($request->discount_price ?? 0);
    //     }

    //     if($request->include_lecture_sheet && $request->delivery_status == 1 && $request->courier_upazila_id == 493)
    //     {
    //         $course_price+=200;
    //     }
    //     else if($request->include_lecture_sheet &&  $request->delivery_status == 1 && $request->courier_upazila_id != 493)
    //     {
    //         $course_price+=250;
    //     }

    //     $doctor_course->actual_course_price = $actual_course_price;
    //     $doctor_course->course_price = $course_price;
    //     $doctor_course->payment_status = "No Payment";

   
    //     if($request->discount_code && Discount::where(['discount_code' => $request->discount_code,  'doctor_id' =>$request->doctor_id, 'batch_id'=>$request->batch_id])->exists()){
    //        $discount= Discount::where(['discount_code' => $request->discount_code,  'doctor_id' =>$request->doctor_id, 'batch_id'=>$request->batch_id])->first();
    //        $doctor_course->course_price =  $course_price-$discount->amount;
    //        $discount->used = 1;
    //        $discount->push();
    //     }


    //     $doctor_course->reg_no = $request->reg_no_first_part.$request->reg_no_last_part;
    //     $doctor_course->reg_no_first_part = $request->reg_no_first_part;
    //     $doctor_course->reg_no_last_part = $request->reg_no_last_part;
    //     $doctor_course->reg_no_last_part_int = (int)$request->reg_no_last_part;

    //     $capacity= Batches::where('id',$batch->id)->pluck('capacity');

    //     if(Batches::where(['branch_id'=>$request->branch_id,'id'=>$request->batch_id])->first() === null)
    //     {
    //         Session::flash('class', 'alert-danger');
    //         session()->flash('message','This Batch does not exist in the selected Branch !!!');
    //         return redirect()->action('Admin\DoctorsCoursesController@create')->withInput();
    //     }

    //     if (DoctorsCourses::where(['doctor_id'=>$request->doctor_id,'year'=> $batch->year,'session_id'=>$request->session_id,'institute_id'=>$request->institute_id,'course_id'=>$request->course_id,'batch_id'=>$request->batch_id,'is_trash' => '0'])->where('course_price', '!=' , '0')->exists()){
    //         Session::flash('class', 'alert-danger');
    //         session()->flash('message','Dear doctor, you are already filled admission form or registered for this course!!!');
    //         return redirect()->action('Admin\DoctorsCoursesController@create')->withInput();
    //     }
     
   
      

    //         if(DoctorsCourses::where(['reg_no'=>$request->reg_no_first_part.$request->reg_no_last_part,'is_trash' => '0'])->where('course_price', '!=' , '0')->exists()){

    //             Session::flash('class', 'alert-danger');
    //             session()->flash('message','This Registration No already exists');
    //             return redirect()->action('Admin\DoctorsCoursesController@create')->withInput();
    //         }
    //         $doctor_course->created_by = Auth::id();
    //         $doctor_course->save();
    //         $payment_status = $this->get_payment_status($doctor_course);
    //         $doctor_course->payment_status = $payment_status;
    //         $doctor_course->push();



    //     Session::flash('status', 'Record has been added successfully');

    //     if( $doctor_course) 
    //     {
    //         $this->sendMessage($doctor_course,$request) ;
    //     }

    //     return redirect()->action('Admin\DoctorsCoursesController@index');

    // }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'doctor_id' => ['required'],
            //'year' => ['required'],
            'session_id' => ['required'],
            'branch_id' => ['required'],
            'institute_id' => ['required'],
            'course_id' => ['required'],
            // 'subject_id' => ['required'],
            'batch_id' => ['required'],
            'reg_no_first_part' => ['required'],
            'reg_no_last_part' => ['required'],
            'status' => ['required']
        ]);


        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            session()->flash('message','Please enter proper input values!!!');
            return redirect()->action('Admin\DoctorsCoursesController@create')->withInput();
        }

        // Update VIP Status
        $this->updateVipStatusOfDoctor($request, $request->doctor_id);

        $batch = Batches::find( $request->batch_id );    

        $doctor_course = new DoctorsCourses();
        $doctor_course->doctor_id =  $request->doctor_id;
        $doctor_course->year = $batch->year;
        $doctor_course->branch_id = $request->branch_id;
        $doctor_course->session_id = $request->session_id;
        $doctor_course->institute_id = $request->institute_id;
        $doctor_course->course_id = $request->course_id;
        $doctor_course->faculty_id = $request->faculty_id;
        $doctor_course->subject_id = $request->subject_id;
        $doctor_course->bcps_subject_id = $request->bcps_subject_id;
        $doctor_course->batch_id = $request->batch_id;
        $doctor_course->candidate_type = $request->candidate_type ?? '';
        $doctor_course->discount_code = $request->discount_code;
        $doctor_course->include_lecture_sheet = $request->include_lecture_sheet;
        $doctor_course->delivery_status = $request->delivery_status;
        $doctor_course->courier_address = $request->courier_address;
        $doctor_course->courier_upazila_id = $request->courier_upazila_id;
        $doctor_course->courier_district_id = $request->courier_district_id;
        $doctor_course->courier_division_id = $request->courier_division_id;

        $doctor_course->payment_status = "No Payment";

        $doctor_course->reg_no = $request->reg_no_first_part.$request->reg_no_last_part;
        $doctor_course->reg_no_first_part = $request->reg_no_first_part;
        $doctor_course->reg_no_last_part = $request->reg_no_last_part;
        $doctor_course->reg_no_last_part_int = (int)$request->reg_no_last_part;

        $capacity= Batches::where('id',$batch->id)->pluck('capacity');

        if(Batches::where(['branch_id'=>$request->branch_id,'id'=>$request->batch_id])->first() === null)
        {
            Session::flash('class', 'alert-danger');
            session()->flash('message','This Batch does not exist in the selected Branch !!!');
            return redirect()->action('Admin\DoctorsCoursesController@create')->withInput();
        }

        if (DoctorsCourses::where(['doctor_id'=>$request->doctor_id,'year'=> $batch->year,'session_id'=>$request->session_id,'institute_id'=>$request->institute_id,'course_id'=>$request->course_id,'batch_id'=>$request->batch_id,'is_trash' => '0'])->where('course_price', '!=' , '0')->exists()){
            Session::flash('class', 'alert-danger');
            session()->flash('message','Dear doctor, you are already filled admission form or registered for this course!!!');
            return redirect()->action('Admin\DoctorsCoursesController@create')->withInput();
        }

        if(DoctorsCourses::where(['reg_no'=>$request->reg_no_first_part.$request->reg_no_last_part,'is_trash' => '0'])->where('course_price', '!=' , '0')->exists())
        {
            Session::flash('class', 'alert-danger');
            session()->flash('message','This Registration No already exists');
            return redirect()->action('Admin\DoctorsCoursesController@create')->withInput();
        }

        $doctor_course->created_by = Auth::id();
        $doctor_course->save();

        $doctor_course->set_payment_status();

        if($doctor_course->batch->payment_times > 1)
        {
            $doctor_course->payment_option = "default";
            $doctor_course->push();
        }

        Session::flash('status', 'Record has been added successfully');

        if( $doctor_course) 
        {
            $this->sendMessage($doctor_course,$request) ;
        }

        return redirect()->action('Admin\DoctorsCoursesController@index');

    }

    protected function sendMessage( $doctor_course,$request)
    {

        $admin_id = Auth::id();

        $smsLog = new SmsLog();
        $response = null;

        $doc=$request->doctor_id;
        $doctors=Doctors::where('id',$doc)->first();
        $doctor_selected_batch = Batches::where(['id'=>$request->batch_id])->first();
        $doctor_course=Courses::where(['id'=>$request->course_id])->first();
        $websitename='https://www.genesisedu.info/';
        $mob = '88' . $doctors->mobile_number;
        $msg = 'Dear Doctor, Thanks for enrollment in ' .$doctor_selected_batch->name. ' for '.$doctor_course->name. ' preparation. Please pay within 24 hours to ensure your seat in the batch. Also have a look on refund and Batch shifting policy. "https://www.genesisedu.info/refund-policy" Thank you. Stay safe. ';

        // $ch = curl_init();
        // curl_setopt($ch, CURLOPT_URL, "http://sms4.digitalsynapsebd.com/api/mt/SendSMS?user=genesispg&password=123321@12&senderid=8809612440402&channel=Normal&DCS=0&flashsms=0&number=$mob&text=$msg");
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        // $response = curl_exec($ch);
        // curl_close($ch);
        // $smsLog->set_response( $response,$doctors->id,$mob,$admin_id)->set_event('Admission(Office)')->save();
        $this->send_custom_sms($doctors,$msg,'Admission(Office)',$isAdmin = true);
    }

    public function calculate_payable($doctor_course)
    {
        $total_payment = 0;
        if(DoctorCoursePayment::where(['doctor_course_id'=>$doctor_course->id])->first())
        {
            $doctor_course_payments = DoctorCoursePayment::where(['doctor_course_id'=>$doctor_course->id])->get();

            foreach ($doctor_course_payments as $doctor_course_payment)
            {
                $total_payment += $doctor_course_payment->amount;
            }

        }

        return $doctor_course->course_price -($request->discount_price ?? 0) - $total_payment;

    }

    public function doctor_course_payment_form($doctor_course_id)
    {
        $data['doctor_course'] = DoctorsCourses::where(['id'=>$doctor_course_id])->first();
        $doctor_course = DoctorsCourses::where(['id'=>$doctor_course_id])->first();
        $doctor_course_payment_count = 0;
        if(DoctorCoursePayment::where(['doctor_course_id'=>$doctor_course->id])->first()) {
            $doctor_course_payment_count = DoctorCoursePayment::where(['doctor_course_id' => $doctor_course->id])->count();
        }

        if(isset($doctor_course->batch->payment_times) && $doctor_course->batch->payment_times > 1 && $doctor_course_payment_count == 0)
        {
            $data['readonly'] = '';
            $data['amount'] = $doctor_course->course_price * $doctor_course->batch->minimum_payment / 100 ;
            $data['min'] = $data['amount'];
            $data['max'] = $doctor_course->course_price;
        }
        elseif(isset($doctor_course->batch->payment_times) && $doctor_course->batch->payment_times > 1 && $doctor_course_payment_count == 1)
        {
            $data['readonly'] = 'readonly';
            $data['amount'] = $this->calculate_payable($doctor_course);
            $data['min'] = '';
            $data['max'] = '';
        }
        else
        {
            $data['readonly'] = 'readonly';
            $data['amount'] = $this->calculate_payable($doctor_course);
            $data['min'] = '';
            $data['max'] = '';
            if($data['amount'] == 0 && $doctor_course->course_price !=0 )
            {
                Session::flash('class', 'alert-danger');
                session()->flash('message','Doctor Course Already Paid !!!');
                return redirect()->action('Admin\DoctorsCoursesController@edit',[$doctor_course->id]);
            }

        }


        return view('admin.doctors_courses.transaction',$data);
    }

    public function doctor_course_payment(Request $request)
    {
        if(DoctorCoursePayment::where(['trans_id'=>$request->trans_id])->first()) {
            Session::flash('class', 'alert-danger');
            Session::flash('message', 'This transaction ID already exist!!!');
            return redirect('doctor-course-payment-form/'.$request->doctor_course_id);
        }

        $doctor_course_payment_count = 0;
        if(DoctorCoursePayment::where(['doctor_course_id'=>$request->doctor_course_id])->first()) {
            $doctor_course_payment_count = DoctorCoursePayment::where(['doctor_course_id' => $request->doctor_course_id])->count();
        }
        $doctor_course_payment = new DoctorCoursePayment();
        $doctor_course_payment->doctor_course_id = $request->doctor_course_id;
        $doctor_course_payment->trans_id = $request->trans_id;
        $doctor_course_payment->amount = $request->amount;
        $doctor_course_payment->payment_serial = $doctor_course_payment_count+1;
        $doctor_course_payment->save();

        unset($doctor_course);
        $doctor_course = DoctorsCourses::find($request->doctor_course_id);

        $payment_status = $this->get_payment_status($doctor_course);

        $doctor_course->payment_status = $payment_status;
        if($payment_status == "Completed")$doctor_course->payment_completed_by_id = Auth::id();
        $doctor_course->updated_by = Auth::id();
        $doctor_course->push();

        if( $doctor_course) 
        {
            $this->sendMessage_office_admission_payment($doctor_course,$request) ;
        }
        return redirect()->action('Admin\DoctorsCoursesController@edit',[$request->doctor_course_id]);
    }

    public function sendMessage_office_admission_payment( $doctor_course,$request){
        $admin_id = Auth::id();
        $smsLog = new SmsLog();
        $response = null;

        $doc=$doctor_course->doctor_id;
        $doctors=Doctors::where('id',$doc)->first();
        $doctor_selected_batch = Batches::where(['id'=>$doctor_course->batch_id ])->first();
        $websitename='https://www.genesisedu.info/';
        $mob = '88' . $doctors->mobile_number;
        $msg = ' Dear ' .$doctors->name. ' , Welcome to ' .$websitename. ' Your payment has been received. Your batch is ' .$doctor_selected_batch->name. ' and registration number is ' . $doctor_course->reg_no .  ' Please check schedule and notice regularly. Thank you. Stay safe. GENESIS. ';
    
        // $ch = curl_init();
        // curl_setopt($ch, CURLOPT_URL, "http://sms4.digitalsynapsebd.com/api/mt/SendSMS?user=genesispg&password=123321@12&senderid=8809612440402&channel=Normal&DCS=0&flashsms=0&number=$mob&text=$msg");
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        // $response = curl_exec($ch);
        // curl_close($ch);
        // $smsLog->set_response( $response,$doctors->id,$mob,$admin_id)->set_event('Payment Completed (Office)')->save();
        $this->send_custom_sms($doctors,$msg,'Payment Completed (Office)',$isAdmin = true);
    }

    public function get_payment_statuus($doctor_course)
    {
        $doctor_course = DoctorsCourses::where(['id'=>$doctor_course->id])->first();
        if($this->calculate_payable($doctor_course) != 0 && $this->calculate_payable($doctor_course) == $doctor_course->course_price)
        {
            return "No Payment";
        }
        else if($this->calculate_payable($doctor_course) <= 0)
        {
            return "Completed";
        }
        else
        {
            return "In Progress";
        }
    }

    public function doctor_course_discount(Request $request,$doctor_course)
    {
        $doctor_selected_batch = Batches::where(['id'=>$request->batch_id])->first();
        //echo "<pre>";print_r($doctor_selected_batch);exit;
        //$doctor_selected_batch = $batch;

        if(isset($doctor_selected_batch->fee_type)==false)
        {
            return "batch_admission_fee_not_set";
        }

        if($doctor_selected_batch->fee_type == "Batch")
        {

            if($request->include_lecture_sheet)
            {

                if(DoctorsCourses::where(['doctor_id'=>Auth::id(),'batches.batch_type'=>'Regular','payment_status'=>'Completed','is_trash' => '0'])->where('course_price', '!=' , '0')->join('batches','doctors_courses.batch_id','=','batches.id')->first())
                {
                    $doctor_course->course_price = $doctor_selected_batch->admission_fee - $doctor_selected_batch->discount_from_regular + $doctor_selected_batch->lecture_sheet_fee;
                }
                else if(DoctorsCourses::where(['doctor_id'=>Auth::id(),'batches.batch_type'=>'Exam','payment_status'=>'Completed','is_trash' => '0'])->where('course_price', '!=' , '0')->join('batches','doctors_courses.batch_id','=','batches.id')->first())
                {
                    $doctor_course->course_price = $doctor_selected_batch->admission_fee - $doctor_selected_batch->discount_from_exam + $doctor_selected_batch->lecture_sheet_fee;
                }
                else
                {
                    $doctor_course->course_price = $doctor_selected_batch->admission_fee + $doctor_selected_batch->lecture_sheet_fee;
                }

            }
            else
            {
                if(DoctorsCourses::where(['doctor_id'=>Auth::id(),'batches.batch_type'=>'Regular','payment_status'=>'Completed','is_trash' => '0'])->where('course_price', '!=' , '0')->join('batches','doctors_courses.batch_id','=','batches.id')->first())
                {
                    $doctor_course->course_price = $doctor_selected_batch->admission_fee - $doctor_selected_batch->discount_from_regular;
                }
                else if(DoctorsCourses::where(['doctor_id'=>Auth::id(),'batches.batch_type'=>'Exam','payment_status'=>'Completed','is_trash' => '0'])->where('course_price', '!=' , '0')->join('batches','doctors_courses.batch_id','=','batches.id')->first())
                {
                    $doctor_course->course_price = $doctor_selected_batch->admission_fee - $doctor_selected_batch->discount_from_exam;
                }
                else
                {
                    $doctor_course->course_price = $doctor_selected_batch->admission_fee;
                }

            }

            $doctor_course->actual_course_price = $doctor_selected_batch->admission_fee;

        }
        else if($doctor_selected_batch->fee_type == "Discipline_Or_Faculty")
        {
            //echo $doctor_selected_batch->institute->type;exit;
            if($doctor_selected_batch->institute->type == 1)
            {

                $doctor_selected_batch_faculty = BatchFacultyFee::where(['batch_id'=>$request->batch_id,'faculty_id'=>$request->faculty_id])->first();
                if(isset($doctor_selected_batch_faculty->admission_fee)==false)
                {
                    return "faculty_admission_fee_not_set";
                }

                if($request->include_lecture_sheet)
                {
                    if(DoctorsCourses::where(['doctor_id'=>Auth::id(),'batches.batch_type'=>'Regular','payment_status'=>'Completed','is_trash' => '0'])->where('course_price', '!=' , '0')->join('batches','doctors_courses.batch_id','=','batches.id')->first())
                    {
                        $doctor_course->course_price = $doctor_selected_batch_faculty->admission_fee - $doctor_selected_batch_faculty->discount_from_regular + $doctor_selected_batch_faculty->lecture_sheet_fee;
                    }
                    else if(DoctorsCourses::where(['doctor_id'=>Auth::id(),'batches.batch_type'=>'Exam','payment_status'=>'Completed','is_trash' => '0'])->where('course_price', '!=' , '0')->join('batches','doctors_courses.batch_id','=','batches.id')->first())
                    {
                        $doctor_course->course_price = $doctor_selected_batch_faculty->admission_fee - $doctor_selected_batch_faculty->discount_from_exam + $doctor_selected_batch_faculty->lecture_sheet_fee;
                    }
                    else
                    {
                        $doctor_course->course_price = $doctor_selected_batch_faculty->admission_fee + $doctor_selected_batch_faculty->lecture_sheet_fee;

                    }
                }
                else
                {
                    if(DoctorsCourses::where(['doctor_id'=>Auth::id(),'batches.batch_type'=>'Regular','payment_status'=>'Completed','is_trash' => '0'])->where('course_price', '!=' , '0')->join('batches','doctors_courses.batch_id','=','batches.id')->first())
                    {
                        $doctor_course->course_price = $doctor_selected_batch_faculty->admission_fee - $doctor_selected_batch_faculty->discount_from_regular;
                    }
                    else if(DoctorsCourses::where(['doctor_id'=>Auth::id(),'batches.batch_type'=>'Exam','payment_status'=>'Completed','is_trash' => '0'])->where('course_price', '!=' , '0')->join('batches','doctors_courses.batch_id','=','batches.id')->first())
                    {
                        $doctor_course->course_price = $doctor_selected_batch_faculty->admission_fee - $doctor_selected_batch_faculty->discount_from_exam;
                    }
                    else
                    {
                        $doctor_course->course_price = $doctor_selected_batch_faculty->admission_fee;

                    }

                }

                $doctor_course->actual_course_price = $doctor_selected_batch_faculty->admission_fee;

            }
            else
            {

                $doctor_selected_batch_discipline = BatchDisciplineFee::where(['batch_id'=>$request->batch_id,'subject_id'=>$request->subject_id])->first();
                if(isset($doctor_selected_batch_discipline->admission_fee)==false)
                {
                    return "discipline_admission_fee_not_set";
                }

                if($request->include_lecture_sheet)
                {
                    if(DoctorsCourses::where(['doctor_id'=>Auth::id(),'batches.batch_type'=>'Regular','payment_status'=>'Completed','is_trash' => '0'])->where('course_price', '!=' , '0')->join('batches','doctors_courses.batch_id','=','batches.id')->first())
                    {
                        $doctor_course->course_price = $doctor_selected_batch_discipline->admission_fee - $doctor_selected_batch_discipline->discount_from_regular +  $doctor_selected_batch_discipline->lecture_sheet_fee;
                    }
                    else if(DoctorsCourses::where(['doctor_id'=>Auth::id(),'batches.batch_type'=>'Exam','payment_status'=>'Completed','is_trash' => '0'])->where('course_price', '!=' , '0')->join('batches','doctors_courses.batch_id','=','batches.id')->first())
                    {
                        $doctor_course->course_price = $doctor_selected_batch_discipline->admission_fee - $doctor_selected_batch_discipline->discount_from_exam +  $doctor_selected_batch_discipline->lecture_sheet_fee;
                    }
                    else
                    {
                        $doctor_course->course_price = $doctor_selected_batch_discipline->admission_fee +  $doctor_selected_batch_discipline->lecture_sheet_fee;

                    }
                }
                else
                {
                    if(DoctorsCourses::where(['doctor_id'=>Auth::id(),'batches.batch_type'=>'Regular','payment_status'=>'Completed','is_trash' => '0'])->where('course_price', '!=' , '0')->join('batches','doctors_courses.batch_id','=','batches.id')->first())
                    {
                        $doctor_course->course_price = $doctor_selected_batch_discipline->admission_fee - $doctor_selected_batch_discipline->discount_from_regular;
                    }
                    else if(DoctorsCourses::where(['doctor_id'=>Auth::id(),'batches.batch_type'=>'Exam','payment_status'=>'Completed','is_trash' => '0'])->where('course_price', '!=' , '0')->join('batches','doctors_courses.batch_id','=','batches.id')->first())
                    {
                        $doctor_course->course_price = $doctor_selected_batch_discipline->admission_fee - $doctor_selected_batch_discipline->discount_from_exam;
                    }
                    else
                    {
                        $doctor_course->course_price = $doctor_selected_batch_discipline->admission_fee;

                    }

                }

                $doctor_course->actual_course_price = $doctor_selected_batch_discipline->admission_fee;
            }

        }

        return $doctor_course;

    }

    public function show($id)
    {
        $user=DoctorsCourses::select('doctors_courses.*')
            ->find($id);
        return view('admin.doctors_courses.show',['user'=>$user]);
    }

    public function edit($id)
    {
        /* $user=DoctorsCourses::find(Auth::id());
         if(!$user->hasRole('Admin')){
             return abort(404);
         }*/

        $data['years'] = array(''=>'Select year');
        for($year = date("Y")+1;$year>=2017;$year--){
            $data['years'][$year] = $year;
        }

        // return
        $doctor_course = DoctorsCourses::query()
            ->with([
                'batch',
                'doctor',
                'doctor.doctor_courses' => function ($query) {
                    $query
                        ->select([
                            'id',
                            'doctor_id',
                            'reg_no',
                            'batch_id',
                        ])
                        ->where('is_trash', 0)
                        ->has('batch_shift_history', '<', 1)
                        ->has('batch_shift_from', '<', 1);
                },
                'batch_shift_history',
                'batch_shift_history.to_doctor_course:id,batch_id,reg_no',
                'batch_shift_history.to_doctor_course.batch:id,name',
                'batch_shift_history.admin:id,name,phone_number',
            ])
            ->find($id);


        $data['title'] = 'SIF Admin : Doctors Courses Edit';
        $data['doctor'] = Doctors::where('id',$doctor_course->doctor_id)->first();
        $data['doctor_course'] = $doctor_course;
        $data['service_packages'] = ServicePackages::get()->pluck('name', 'id');
        $data['coming_bys'] = ComingBy::get()->pluck('name', 'id');
        $data['branches'] = Branch::pluck('name', 'id');
        $data['institutes'] = Institutes::get()->pluck('name', 'id');

        $institute = Institutes::where('id',$doctor_course->institute_id)->first();
        Session(['institute_type'=> $institute->type ]);
        $data['url']  = ($institute->type)?'branches-courses-faculties-batches':'branches-courses-subjects-batches';
        $data['institute_type']= $institute->type;

        $data['courses'] = Courses::get()->where('institute_id',$doctor_course->institute_id)->pluck('name', 'id');

        $course = Courses::find( $doctor_course->course_id );

        $is_combined = $doctor_course->institute_id == AppServiceProvider::$COMBINED_INSTITUTE_ID;

        if($data['institute_type']==1) {
            $data['subjects'] = Subjects::where('faculty_id', $doctor_course->faculty_id)->pluck('name', 'id');

            if ( $is_combined ) {
                $data[ 'bcps_subjects' ] = $course ? $course->combined_disciplines()->pluck('name', 'id') : new Collection( );
                $data[ 'faculties' ] = $course ? $course->combined_faculties()->pluck( 'name', 'id' ) : new Collection( );
            } else {
                $data[ 'faculties' ] = Faculty::where('course_id', $doctor_course->course_id)->pluck('name', 'id');
            }

        }

        if( $institute->type == 0 || $institute->id == AppServiceProvider::$COMBINED_INSTITUTE_ID ){
            if( $is_combined ) {
                $data[ 'subjects' ] = Subjects::where( 'faculty_id', $doctor_course->faculty_id )->pluck('name', 'id');
            }else {
                $data['subjects'] = Subjects::where('course_id',$doctor_course->course_id)->pluck('name', 'id');
            }
        }


        $data['batches'] = Batches::get()->where( 'institute_id', $doctor_course->institute_id )
            ->where('course_id',$doctor_course->course_id)
            ->where('branch_id',$doctor_course->branch_id)
            ->pluck('name', 'id');

        /*echo '<pre>';
        print_r($data);exit;*/

        if($doctor_course->delivery_status == "1")
        {
            $data['divisions'] = Divisions::pluck('name','id');
            $data['districts'] = Districts::where(['division_id'=>$doctor_course->courier_division_id])->pluck('name','id');
            $data['upazilas'] = Upazilas::where(['district_id'=>$doctor_course->courier_district_id])->pluck('name','id');
        }

        $data['sessions'] = Sessions::join('course_year_session','course_year_session.session_id','sessions.id')
                ->join( 'course_year', 'course_year.id', 'course_year_session.course_year_id' )
                ->where('course_year.deleted_at',NULL)
                ->where('course_year_session.deleted_at',NULL)
                ->where('course_id',$doctor_course->course_id)
               
                ->pluck('name',  'sessions.id');

        return view('admin.doctors_courses.edit', $data);
    }

    public function update(Request $request, $id)
    { 
        $validator = Validator::make($request->all(), [
            'doctor_id' => ['required'],
            'session_id' => ['required'],
            'branch_id' => ['required'],
            'institute_id' => ['required'],
            'course_id' => ['required'],
            'batch_id' => ['required'],
            'reg_no_first_part' => ['required'],
            'reg_no_last_part' => ['required'],
            'status' => ['required']
        ]);

        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            session()->flash('message','Please enter proper input values!!!');
            return redirect()->action('Admin\DoctorsCoursesController@edit',[$id])->withInput();
        }

        // Update VIP Status
        $this->updateVipStatusOfDoctor($request, $request->doctor_id);

        $doctor_course = DoctorsCourses::with('doctor')->find($id);
        
        $doctor_course->status = $request->status;

        if($doctor_course->branch_id != $request->branch_id)
        {
            if(Batches::where(['branch_id'=>$request->branch_id,'id'=>$request->batch_id])->first() === null)
            {
                Session::flash('class', 'alert-danger');
                session()->flash('message','This Batch does not exist in the selected Branch !!!');
                return redirect()->action('Admin\DoctorsCoursesController@edit',[$id])->withInput();
            }
        }

        if($doctor_course->batch_id != $request->batch_id){
            if (DoctorsCourses::where(['reg_no'=>$request->reg_no_first_part.$request->reg_no_last_part,'is_trash'=>0])->exists()){
                Session::flash('class', 'alert-danger');
                session()->flash('message','This Registration already exists');
                return redirect()->action('Admin\DoctorsCoursesController@edit',[$id])->withInput();
            }

        }

    
        $batch = Batches::find( $request->batch_id  );

        if($doctor_course->batch_shifted == '1' &&  $request->batch_shifted == '0' ){
            $doctor_course->batch_shifted = $request->batch_shifted ;
            $doctor_course->batch_shifted_info = $request->batch_shifted_info;
            

            $batch_shift = BatchShift::where("from_doctor_course_id", $doctor_course->id)->first();

            if($batch_shift) {
                $histories = $batch_shift->histories ?? [];

                array_push($histories, [
                    "to_doctor_course_id"   => $batch_shift->to_doctor_course_id,
                    "shift_fee"             => $batch_shift->shift_fee,
                    "service_charge"        => $batch_shift->service_charge,
                    "payment_adjustment"    => $batch_shift->payment_adjustment,
                    "shifted_at"            => $batch_shift->shifted_at ? $batch_shift->shifted_at->format('d M Y') : '',
                    "note"                  => $batch_shift->note,
                    "shifted_by"            => $batch_shift->shifted_by,
                ]);

                $batch_shift->update([
                    "shifted_by"            => Auth::id(),
                    "histories"             => $histories,
                ]);
                
                BatchShift::where("from_doctor_course_id", $doctor_course->id)->delete();
            }
        }

        if($doctor_course->batch_shifted != '1' && $request->batch_shifted == '1'){
            $doctor_course->batch_shifted = $request->batch_shifted;
            $doctor_course->batch_shifted_info = $request->batch_shifted_info;
            $doctor_course->status = '0';

            $batch_shift = BatchShift::withTrashed()->updateOrCreate(
                [
                    "from_doctor_course_id" => $doctor_course->id,
                ],
                [
                    "to_doctor_course_id"   => $request->to_doctor_course_id,
                    "shift_fee"             => $request->shift_fee ?? 0,
                    "service_charge"        => $request->service_charge ?? 0,
                    "payment_adjustment"    => $request->payment_adjustment ?? 0,
                    "note"                  => $request->batch_shifted_info,
                    "shifted_at"            => date('Y-m-d'),
                    "shifted_by"            => Auth::id(),
                    "deleted_at"            => null,
                ]
            );

            $from_batch_name = $batch_shift->from_doctor_course->batch->name ?? '';
            $to_batch_name = $batch_shift->to_doctor_course->batch->name ?? '';

            $msg = Format::getMessage('BATCH_SHIFT_ON_PROCESS', [
                $doctor_course->doctor->name,
                $from_batch_name,
                $to_batch_name
            ]);

            if(!$msg) {
                $msg = "Dear Dr. {$doctor_course->doctor->name}, Your shifting request from {$from_batch_name} to {$to_batch_name} on process";
            }

            $this->send_custom_sms($doctor_course->doctor, $msg, 'Batch Shifted', $isAdmin = true);
        }

        $doctor_course->doctor_id =  $request->doctor_id;
        $doctor_course->year = $batch->year;
        $doctor_course->branch_id = $request->branch_id;
        $doctor_course->session_id = $request->session_id;
        $doctor_course->institute_id = $request->institute_id;
        $doctor_course->course_id = $request->course_id;
        $doctor_course->faculty_id = $request->faculty_id;
        $doctor_course->subject_id = $request->subject_id;
        $doctor_course->bcps_subject_id = $request->bcps_subject_id;
        $doctor_course->batch_id = $request->batch_id;
        $doctor_course->candidate_type = $request->candidate_type ?? null;
        $doctor_course->discount_code = $request->discount_code;
        $doctor_course->include_lecture_sheet = $request->include_lecture_sheet;
        $doctor_course->delivery_status = $request->delivery_status;
        $doctor_course->courier_address = $request->courier_address;
        $doctor_course->courier_upazila_id = $request->courier_upazila_id;
        $doctor_course->courier_district_id = $request->courier_district_id;
        $doctor_course->courier_division_id = $request->courier_division_id;
        
        if($doctor_course->session_id != $request->session_id || $doctor_course->year != $batch->year) {
            $doctor_course->reg_no_first_part = $request->reg_no_first_part;
            $doctor_course->reg_no_last_part = $request->reg_no_last_part;
            $doctor_course->reg_no_last_part_int = (int)$request->reg_no_last_part;
            $doctor_course->reg_no = $doctor_course->reg_no_first_part . $doctor_course->reg_no_last_part;
        }

        $doctor_course->roll = $request->roll ?? null;

        $doctor_course->updated_by = Auth::id();
        $doctor_course->push();

        $doctor_course->set_payment_status();
        
        Session::flash('message', 'Record has been updated successfully');

        return redirect()->action('Admin\DoctorsCoursesController@edit',[$id]);

    }

    
    public function quickEdit(DoctorsCourses $doctor_course)
    {
        // return
        
        $data['years'] = array(''=>'Select year');
        for($year = date("Y")+1;$year>=2017;$year--){
            $data['years'][$year] = $year;
        }

        $id = $doctor_course->id;

        // return
        $doctor_course = DoctorsCourses::query()
            ->with([
                'course:id,name',
                'institute:id,name,type',
                'batch:id,name',
                'batch.faculties' => function($query) {
                    $query->where('status', 1);
                },
                'batch.subjects' => function($query) {
                    $query->where('status', 1);
                },
                'doctor:id,name',
                'doctor.doctor_courses' => function ($query) {
                    $query
                        ->select([
                            'id',
                            'doctor_id',
                            'reg_no',
                            'batch_id',
                        ])
                        ->where('is_trash', 0)
                        ->has('batch_shift_history', '<', 1)
                        ->has('batch_shift_from', '<', 1);
                },
                'batch_shift_history',
                'batch_shift_history.to_doctor_course:id,batch_id,reg_no',
                'batch_shift_history.to_doctor_course.batch:id,name',
                'batch_shift_history.admin:id,name,phone_number',
            ])
            ->find($id);


        $data['title'] = 'SIF Admin : Doctors Courses Edit';
        $data['doctor'] = Doctors::where('id',$doctor_course->doctor_id)->first();
        $data['doctor_course'] = $doctor_course;
        $data['service_packages'] = ServicePackages::get()->pluck('name', 'id');
        $data['coming_bys'] = ComingBy::get()->pluck('name', 'id');
        $data['branches'] = Branch::pluck('name', 'id');
        $data['institutes'] = Institutes::get()->pluck('name', 'id');

        $institute = Institutes::where('id',$doctor_course->institute_id)->first();
        Session(['institute_type'=> $institute->type ]);
        $data['url']  = ($institute->type)?'branches-courses-faculties-batches':'branches-courses-subjects-batches';
        $data['institute_type']= $institute->type;

        $data['courses'] = Courses::get()->where('institute_id',$doctor_course->institute_id)->pluck('name', 'id');

        $course = Courses::find( $doctor_course->course_id );

        $is_combined = $doctor_course->institute_id == AppServiceProvider::$COMBINED_INSTITUTE_ID;

        if($data['institute_type']==1) {
            $data['subjects'] = Subjects::where('faculty_id', $doctor_course->faculty_id)->pluck('name', 'id');

            if ( $is_combined ) {
                $data[ 'bcps_subjects' ] = $course ? $course->combined_disciplines()->pluck('name', 'id') : new Collection( );
                $data[ 'faculties' ] = $course ? $course->combined_faculties()->pluck( 'name', 'id' ) : new Collection( );
            } else {
                $data[ 'faculties' ] = Faculty::where('course_id', $doctor_course->course_id)->pluck('name', 'id');
            }

        }

        if( $institute->type == 0 || $institute->id == AppServiceProvider::$COMBINED_INSTITUTE_ID ){
            if( $is_combined ) {
                $data[ 'subjects' ] = Subjects::where( 'faculty_id', $doctor_course->faculty_id )->pluck('name', 'id');
            }else {
                $data['subjects'] = Subjects::where('course_id',$doctor_course->course_id)->pluck('name', 'id');
            }
        }


        $data['batches'] = Batches::get()->where( 'institute_id', $doctor_course->institute_id )
            ->where('course_id',$doctor_course->course_id)
            ->where('branch_id',$doctor_course->branch_id)
            ->pluck('name', 'id');

        /*echo '<pre>';
        print_r($data);exit;*/

        if($doctor_course->delivery_status == "1")
        {
            $data['divisions'] = Divisions::pluck('name','id');
            $data['districts'] = Districts::where(['division_id'=>$doctor_course->courier_division_id])->pluck('name','id');
            $data['upazilas'] = Upazilas::where(['district_id'=>$doctor_course->courier_district_id])->pluck('name','id');
        }

        $data['sessions'] = Sessions::join('course_year_session','course_year_session.session_id','sessions.id')
            ->join( 'course_year', 'course_year.id', 'course_year_session.course_year_id' )
            ->where('course_year.deleted_at',NULL)
            ->where('course_year_session.deleted_at',NULL)
            ->where('course_id',$doctor_course->course_id)
            
            ->pluck('name',  'sessions.id');

        return view('admin.doctors_courses.quick_edit', $data);
    }

    public function quickUpdate(Request $request, $id)
    { 
        $validator = Validator::make($request->all(), [
            'doctor_id' => ['required'],
            'session_id' => ['required'],
            'branch_id' => ['required'],
            'institute_id' => ['required'],
            'course_id' => ['required'],
            'batch_id' => ['required'],
            'reg_no_first_part' => ['required'],
            'reg_no_last_part' => ['required'],
            'status' => ['required']
        ]);

        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            session()->flash('message','Please enter proper input values!!!');
            return redirect()->action('Admin\DoctorsCoursesController@edit',[$id])->withInput();
        }

        // Update VIP Status
        $this->updateVipStatusOfDoctor($request, $request->doctor_id);

        $doctor_course = DoctorsCourses::with('doctor')->find($id);
        
        $doctor_course->status = $request->status;

        if($doctor_course->branch_id != $request->branch_id)
        {
            if(Batches::where(['branch_id'=>$request->branch_id,'id'=>$request->batch_id])->first() === null)
            {
                Session::flash('class', 'alert-danger');
                session()->flash('message','This Batch does not exist in the selected Branch !!!');
                return redirect()->action('Admin\DoctorsCoursesController@edit',[$id])->withInput();
            }
        }

        if($doctor_course->batch_id != $request->batch_id){
            if (DoctorsCourses::where(['reg_no'=>$request->reg_no_first_part.$request->reg_no_last_part,'is_trash'=>0])->exists()){
                Session::flash('class', 'alert-danger');
                session()->flash('message','This Registration already exists');
                return redirect()->action('Admin\DoctorsCoursesController@edit',[$id])->withInput();
            }

        }

    
        $batch = Batches::find( $request->batch_id  );

        if($doctor_course->batch_shifted == '1' &&  $request->batch_shifted == '0' ){
            $doctor_course->batch_shifted = $request->batch_shifted ;
            $doctor_course->batch_shifted_info = $request->batch_shifted_info;

            $batch_shift = BatchShift::where("from_doctor_course_id", $doctor_course->id)->first();

            if($batch_shift) {
                $histories = $batch_shift->histories ?? [];

                array_push($histories, [
                    "to_doctor_course_id"   => $batch_shift->to_doctor_course_id,
                    "shift_fee"             => $batch_shift->shift_fee,
                    "service_charge"        => $batch_shift->service_charge,
                    "payment_adjustment"    => $batch_shift->payment_adjustment,
                    "shifted_at"            => $batch_shift->shifted_at ? $batch_shift->shifted_at->format('d M Y') : '',
                    "note"                  => $batch_shift->note,
                    "shifted_by"            => $batch_shift->shifted_by,
                ]);

                $batch_shift->update([
                    "shifted_by"            => Auth::id(),
                    "histories"             => $histories,
                ]);
                
                BatchShift::where("from_doctor_course_id", $doctor_course->id)->delete();
            }
        }

        if($doctor_course->batch_shifted != '1' && $request->batch_shifted == '1'){
            $doctor_course->batch_shifted = $request->batch_shifted;
            $doctor_course->batch_shifted_info = $request->batch_shifted_info;
            $doctor_course->status = '0';

            $batch_shift = BatchShift::withTrashed()->updateOrCreate(
                [
                    "from_doctor_course_id" => $doctor_course->id,
                ],
                [
                    "to_doctor_course_id"   => $request->to_doctor_course_id,
                    "shift_fee"             => $request->shift_fee ?? 0,
                    "service_charge"        => $request->service_charge ?? 0,
                    "note"                  => $request->batch_shifted_info,
                    "shifted_at"            => date('Y-m-d'),
                    "shifted_by"            => Auth::id(),
                    "deleted_at"            => null,
                ]
            );

            $batch_shift->load([
                'from_doctor_course.batch:id,name',
                'to_doctor_course:batch:id,name'
            ]);

            $doctor_name = $doctor_course->doctor->name ?? '';
            $from_batch_name = $batch_shift->from_doctor_course->batch->name ?? '';
            $to_batch_name = $batch_shift->to_doctor_course->batch->name ?? '';

            $msg = Format::getMessage('BATCH_SHIFT_ON_PROCESS', [
                $doctor_name,
                $from_batch_name,
                $to_batch_name
            ]);

            if(!$msg) {
                $msg = "Dear Dr. {$doctor_course->doctor->name}, Your shifting request from {$from_batch_name} to {$to_batch_name} on process";
            }

            $this->send_custom_sms($doctor_course->doctor, $msg, 'Batch Shifted', $isAdmin = true);
        }

        $doctor_course->doctor_id =  $request->doctor_id;
        $doctor_course->year = $batch->year;
        $doctor_course->branch_id = $request->branch_id;
        $doctor_course->session_id = $request->session_id;
        $doctor_course->institute_id = $request->institute_id;
        $doctor_course->course_id = $request->course_id;
        $doctor_course->faculty_id = $request->faculty_id;
        $doctor_course->subject_id = $request->subject_id;
        $doctor_course->bcps_subject_id = $request->bcps_subject_id;
        $doctor_course->batch_id = $request->batch_id;
        $doctor_course->candidate_type = $request->candidate_type ?? null;
        $doctor_course->discount_code = $request->discount_code;
        $doctor_course->include_lecture_sheet = $request->include_lecture_sheet;
        $doctor_course->delivery_status = $request->delivery_status;
        $doctor_course->courier_address = $request->courier_address;
        $doctor_course->courier_upazila_id = $request->courier_upazila_id;
        $doctor_course->courier_district_id = $request->courier_district_id;
        $doctor_course->courier_division_id = $request->courier_division_id;
        
        if($doctor_course->session_id != $request->session_id || $doctor_course->year != $batch->year) {
            $doctor_course->reg_no_first_part = $request->reg_no_first_part;
            $doctor_course->reg_no_last_part = $request->reg_no_last_part;
            $doctor_course->reg_no_last_part_int = (int)$request->reg_no_last_part;
            $doctor_course->reg_no = $doctor_course->reg_no_first_part . $doctor_course->reg_no_last_part;
        }

        $doctor_course->roll = $request->roll ?? null;

        $doctor_course->updated_by = Auth::id();
        $doctor_course->push();

        $doctor_course->set_payment_status();
        
        Session::flash('message', 'Record has been updated successfully');

        return redirect()->action('Admin\DoctorsCoursesController@edit',[$id]);

    }

    public function destroy($id)
    {
        /*$user=DoctorsCourses::find(Auth::id());
        if(!$user->hasRole('Admin')){
            return abort(404);
        }*/

        // DoctorsCourses::destroy($id); // 1 way
        DoctorsCourses::where('id', $id)->update(['is_trash' => 1, 'trash_by'=>Auth::id()]);
        Session::flash('message', 'Record has been deleted successfully');
        return redirect()->action('Admin\DoctorsCoursesController@index');
    }

    public function doctors_courses_untrash( $id )
    {
        /*$user=DoctorsCourses::find(Auth::id());
        if(!$user->hasRole('Admin')){
            return abort(404);
        }*/

        // DoctorsCourses::destroy($id); // 1 way
        DoctorsCourses::where('id', $id)->update(['is_trash' => 0]);
        Session::flash('message', 'Record has been untrash successfully');
        return redirect()->action('Admin\DoctorsCoursesController@index');
    }

    public function doctor_exams($doctor_course_id)
    {
        $data['message'] = '';
        $data['doctor_exams'] = \App\DoctorExam::where([ 'doctor_course_id' => $doctor_course_id] )->get();
        //echo "<pre>";print_r($data['doctor_exams']);exit;        
        return view('admin.doctors_courses.doctor_exams',$data);

    }

    public function doctor_exam_reopen($doctor_course_id,$exam_id)
    {
        $doctor_exam = \App\DoctorExam::where([ 'doctor_course_id' => $doctor_course_id, 'exam_id' => $exam_id ] )->first();
        $doctor_course = DoctorsCourses::where(['id'=>$doctor_course_id])->first();
        $exam = \App\Exam::where(['id'=>$exam_id])->first();
        
        if(isset($doctor_exam) && isset($doctor_course) && isset($exam))
        {
            $file_path = empty($doctor_exam->answer_file_link) ? public_path('exam_answers/' . $doctor_course->doctor->id ):$doctor_exam->answer_file_link;
            $file_name = $exam_id . '_' . $doctor_course_id;
            if( file_exists( $file = $file_path . '/' . $file_name . ".json" ) ) {
                $answer_file = fopen( $file, "w" ) or die("Unable to open file!");
                fwrite( $answer_file, '');
                fclose( $answer_file );
            }
    
            \App\DoctorExam::where(['doctor_course_id'=>$doctor_course_id,'exam_id'=>$exam_id])->delete();
            \App\DoctorAnswers::where(['doctor_course_id'=>$doctor_course_id,'exam_id'=>$exam_id])->delete();
            Result::where(['doctor_course_id'=>$doctor_course_id,'exam_id'=>$exam_id])->delete();
        }
        else if(isset($doctor_course) && isset($exam))
        {
            $file_path = public_path('exam_answers/' . $doctor_course->doctor->id );
            $file_name = $exam_id . '_' . $doctor_course_id;
            if( file_exists( $file = $file_path . '/' . $file_name . ".json" ) ) {
                $answer_file = fopen( $file, "w" ) or die("Unable to open file!");
                fwrite( $answer_file, '');
                fclose( $answer_file );
            }
    
            \App\DoctorExam::where(['doctor_course_id'=>$doctor_course_id,'exam_id'=>$exam_id])->delete();
            \App\DoctorAnswers::where(['doctor_course_id'=>$doctor_course_id,'exam_id'=>$exam_id])->delete();
            Result::where(['doctor_course_id'=>$doctor_course_id,'exam_id'=>$exam_id])->delete();
        }
        
        $data['message'] = 'Exam reopened successfully!!!';
        $data['doctor_exams'] = \App\DoctorExam::where([ 'doctor_course_id' => $doctor_course_id] )->get();        
        return view('admin.doctors_courses.doctor_exams',$data);
    }

    function batch_excel($paras=null)
    {

        session(['paras'=>$paras]);

        $params_array = explode('_',$paras);
        $batch = Batches::where('id',$params_array[2])->value('name');
        
        $file_name = str_replace([' ','/','\\'], '_', $batch).'_'.$params_array[0];

        return (new UsersExport(1))->download($file_name.'.xlsx');

        // $doctors = Doctors::take(100)->get();

        // $array = [];
        
        // foreach($doctors as $doctor){
        //     $array[] = [
        //         'id' => $doctor->id,
        //         'name' => $doctor->medicalcolleges->name ?? '',
        //     ];
        // }

        // return Excel::download(new ResultExport($array), 'download.xlsx');
    }

    function batch_excel_download($params)
    {                                

        
        $params = json_decode($params);
        $data = array();
        $data['year'] = $params->year ?? '';
        $data['course_id'] = $params->course_id ?? '';
        $data['session_id'] = $params->session_id ?? '';
        $data['batch_id'] = $params->batch_id ?? '';
        $data['subject_id'] = $params->subject_id ?? '';

        if(!empty($data['batch_id']))
        {
            $batch = Batches::where('id',$params->batch_id)->first();
            if(isset($batch))
            {
                $file_name = str_replace([' ','/','\\'], '_', $batch->name);
            }            
        }
        
        if(!isset($file_name))
        {
            $file_name = "custom-excel";
        }

        $string = ' where ( `is_trash`="0" ';
        if(!empty($data['year']))
        {
            $string.=' and `doctors_courses`.`year`="'.$data['year'].'"';
        }
        if(!empty($data['course_id']))
        {
            $string.=' and `doctors_courses`.`course_id`="'.$data['course_id'].'"';
        }
        if(!empty($data['session_id']))
        {
            $string.=' and `doctors_courses`.`session_id`="'.$data['session_id'].'"';
        }
        if(!empty($data['batch_id']))
        {
            $string.=' and `batch_id`="'.$data['batch_id'].'"';
        }
        if(!empty($data['subject_id']))
        {
            $string.=' and `doctors_courses`.`subject_id`="'.$data['subject_id'].'"';
        }
        $string.=" ) ";

        $raw = "select `doctors_courses`.`id`, `d2`.`name` as `doctor_name`, CONCAT('88',mobile_number) AS mobile_number, `d2`.`bmdc_no` as `bmdc_no`, `doctors_courses`.`reg_no`, `d3`.`name` as `institute_name`, `d4`.`name` as `course_name`, `d5`.`name` as `faculty_name`, `d6`.`name` as `subject_name`, `d7`.`name` as `batche_name`, `doctors_courses`.`year`, `d8`.`name` as `session_name`, `d10`.`name` as `branch_name`, `doctors_courses`.`created_at`, `doctors_courses`.`payment_status` from `doctors_courses` left join `doctors` as `d2` on `doctors_courses`.`doctor_id` = `d2`.`id` left join `institutes` as `d3` on `doctors_courses`.`institute_id` = `d3`.`id` left join `courses` as `d4` on `doctors_courses`.`course_id` = `d4`.`id` left join `faculties` as `d5` on `doctors_courses`.`faculty_id` = `d5`.`id` left join `subjects` as `d6` on `doctors_courses`.`subject_id` = `d6`.`id` left join `batches` as `d7` on `doctors_courses`.`batch_id` = `d7`.`id` left join `sessions` as `d8` on `doctors_courses`.`session_id` = `d8`.`id` left join `service_packages` as `d9` on `doctors_courses`.`service_package_id` = `d9`.`id` left join `branches` as `d10` on `doctors_courses`.`branch_id` = `d10`.`id` ".$string ;

        $result = DB::select(DB::raw($raw));
        
        return (new Collection($result))->downloadExcel(
            $filePath = $file_name.'.xlsx',
            $writerType = null,
            $headings = true
        );
        
        // $doctors = Doctors::take(100)->get();

        // $array = [];
        
        // foreach($doctors as $doctor){
        //     $array[] = [
        //         'id' => $doctor->id,
        //         'name' => $doctor->medicalcolleges->name ?? '',
        //     ];
        // }

        // return Excel::download(new ResultExport($array), 'download.xlsx');
    }
    



    public function course_result( $id )
    {
        
        $data['doctor_course'] = DoctorsCourses::findOrFail($id);
        $data['course_reg_no'] = DoctorsCourses::with('doctor')->select('*')->where('id', $data['doctor_course']->id)->first();
        $data['results'] = Result::with('doctor_course')->select('*')->where('doctor_course_id', $data['doctor_course']->id)->get();

        // foreach($data['results'] as $index => $result)
        // {
        //     $candidate_possition = $this->candidate_position( $data['doctor_course'] , $result->exam_id, $result->obtained_mark );
        // }

        // $data[ 'candidate_possitions' ] = $candidate_possition;
        // dd($data[ 'candidate_possitions' ]);

        return view('admin.doctors_courses.all_result', $data);
    }

    public function system_driven( $id )
    {        
        $data['doctor_course'] = DoctorsCourses::find($id);
        return view('admin.doctors_courses.system_driven', $data);
    }

    public function system_driven_save( Request $request )
    {
        $doctor_course = DoctorsCourses::where(['id'=>$request->doctor_course_id])->first();
        if($doctor_course->system_driven != $request->system_driven)
        {
            if(isset($doctor_course->batch->batch_schedule) && count($doctor_course->batch->batch_schedule))
            {
                foreach($doctor_course->batch->batch_schedule as $schedule)
                {
                    DoctorCourseScheduleDetails::where(['schedule_id'=>$schedule->id,'doctor_course_id'=>$doctor_course->id])->delete();
                }
            }
        }
        
        $doctor_course = DB::table('doctors_courses')->where(['id'=>$request->doctor_course_id])->update(['system_driven'=>$request->system_driven,'system_driven_count'=>$request->system_driven_count,'updated_by'=>Auth::id()]);
        Session::flash('message', 'Doctor System Driven Changed successfully');
        $data['doctor_course'] = DoctorsCourses::find($request->doctor_course_id);
        return view('admin.doctors_courses.system_driven', $data);
    }

    public function installment_option_save( Request $request )
    {
        if(count($request->payment_date)) {
            $previous_date = "19700101";
            $previous_amount = 0;
            for($i = 1; $i <= count($request->payment_date);$i++)
            {
                if( ( Date("Ymd",date_create_from_format("Y-m-d",$request->payment_date[$i])->getTimestamp()) < $previous_date) || ($request->amount[$i] < $previous_amount))
                {
                    Session::flash('class', 'alert-danger');
                    Session::flash('message', 'Data is not saved. Payment Date Or Installment amount is not increasing!!!');
                    return redirect('/admin/doctor-course/payment-option/'.$request->doctor_course_id)->withInput();
                }
            }

        }

        \App\DoctorCoursePaymentOptions::where(['doctor_course_id'=>$request->doctor_course_id])->update(['deleted_by'=>Auth::id()]);
        \App\DoctorCoursePaymentOptions::where(['doctor_course_id'=>$request->doctor_course_id])->delete();

        for($i = 1; $i <= count($request->payment_date);$i++)
        {
            \App\DoctorCoursePaymentOptions::insert(['doctor_course_id'=>$request->doctor_course_id,'payment_date'=>$request->payment_date[$i],'amount'=>$request->amount[$i],'created_by'=>Auth::id()]);
        }

        $data['doctor_course'] = DoctorsCourses::find($request->doctor_course_id);
        $data['doctor_course']->set_payment_status();
        $data['title'] = "Doctor Course Payment Option";

        return redirect()
            ->route('doctor_courses.payment.option', $request->doctor_course_id)
            ->with('message', 'Doctor Course installment option changed successfully');
    }

    public function doctor_course_payments($id)
    {
        $data['doctor_course'] = DoctorsCourses::where(['id'=>$id])->first();
        $data['title'] = "Doctor Course Payment";
        $data['readonly'] = "false";
        $data['doctor_course']->set_payment_status();
        $data['payment_info'] = $data['doctor_course']->get_payment_info();

        return view('admin.doctors_courses.payments', $data);
    }

    public function doctor_course_payments_save(Request $request)
    {

        if(DoctorCoursePayment::where(['trans_id'=>$request->trans_id])->first()) {
            Session::flash('class', 'alert-danger');
            Session::flash('message', 'This transaction ID already exist!!!');
            return redirect('/admin/doctor-course/payments/'.$request->doctor_course_id);
        }

        $doctor_course_payment_count = 0;
        if(DoctorCoursePayment::where(['doctor_course_id'=>$request->doctor_course_id])->first()) {
            $doctor_course_payment_count = DoctorCoursePayment::where(['doctor_course_id' => $request->doctor_course_id])->count();
        }
        $doctor_course_payment = new DoctorCoursePayment();
        $doctor_course_payment->doctor_course_id = $request->doctor_course_id;
        $doctor_course_payment->trans_id = $request->trans_id;
        $doctor_course_payment->note = $request->note;
        $doctor_course_payment->amount = $request->amount;
        $doctor_course_payment->payment_serial = $doctor_course_payment_count+1;
        $doctor_course_payment->save();
        
        unset($doctor_course);
        $doctor_course = DoctorsCourses::find($request->doctor_course_id);

        $doctor_course->set_payment_status();
        
        $doctor_course->payment_count = $doctor_course_payment->payment_serial;
        if($doctor_course->payment_status == "Completed")$doctor_course->payment_completed_by_id = Auth::id();
        $doctor_course->updated_by = Auth::id();
        $doctor_course->push();

        if( $doctor_course) 
        {
            $this->sendMessage_office_admission_payment_info($doctor_course,$request) ;
        }

        return redirect()->action('Admin\DoctorsCoursesController@edit',[$request->doctor_course_id]);
    }

    public function payment_history($id)
    {
        $data['doctor_course'] = DoctorsCourses::query()
            ->with([
                'batch_shift_from',
            ])
            ->findOrFail($id);

        $data['title'] = "Doctor Course Payment";
        $data['readonly'] = "false";
        $data['doctor_course']->set_payment_status();
        $data['payment_info'] = $data['doctor_course']->get_payment_info();

        return view('admin.doctors_courses.payment_history', $data);
    }

    
    public function sendMessage_office_admission_payment_info( $doctor_course, $request )
    {
        $admin_id = Auth::id();
        $smsLog = new SmsLog();
        $response = null;

        $doc=$doctor_course->doctor_id;
        $doctors=Doctors::where('id',$doc)->first();
        $doctor_selected_batch = Batches::where(['id'=>$doctor_course->batch_id ])->first();
        $websitename='https://www.genesisedu.info/';
        $mob = '88' . $doctors->mobile_number;
        $msg = ' Dear ' .$doctors->name. ' , Welcome to ' .$websitename. ' Your payment '.$request->amount.' BDT has been received. Your batch is ' .$doctor_selected_batch->name. ' and registration number is ' . $doctor_course->reg_no .  ' Please check schedule and notice regularly. Thank you. Stay safe. GENESIS. ';
    
        // $ch = curl_init();
        // curl_setopt($ch, CURLOPT_URL, "http://sms4.digitalsynapsebd.com/api/mt/SendSMS?user=genesispg&password=123321@12&senderid=8809612440402&channel=Normal&DCS=0&flashsms=0&number=$mob&text=$msg");
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        // $response = curl_exec($ch);
        // curl_close($ch); 
        // $smsLog->set_response( $response,$doctors->id,$mob,$admin_id)->set_event('Payment Received (Office)')->save();
        $this->send_custom_sms($doctors,$msg,'Payment Completed (Office)',$isAdmin = true);
    }

    protected function updateVipStatusOfDoctor($request, $doctor_id = null)
    {
        $doctor_id = $doctor_id ?? $request->doctor_id;

        if(isset($request->is_vip)) {
            Doctors::query()
                ->where('id', $doctor_id)
                ->update([
                    'vip' => $request->is_vip ? $request->vip : null,
                ]);
        }
    }

}
