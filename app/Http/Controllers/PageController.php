<?php

namespace App\Http\Controllers;

use App\Page;
use App\Result;
use Illuminate\Http\Request;
use App\Doctors;
use App\DoctorsCourses;
use App\AvailableBatches;
use App\MedicalColleges;
use App\DoctorsReviews;
use App\Advertisements;
use App\BannerSlider;
use App\Batches;
use App\Photos;
use App\Courses;
use App\SmsLog;
use App\Faqs;
use App\NoticeBoard;
use App\SendSms;
use App\Setting;
use Session;
use Validator;
use Auth;
use Illuminate\Support\Facades\Redis;


class PageController extends Controller
{
    use SendSms;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth:doctor');
    }

    public function aboutus()
    {
        // $data['title'] = Page::where('id',1)->value('title');
        // $data['description'] = Page::where('id',1)->value('description');
        // $data['unique_courses'] = array_unique(json_decode(AvailableBatches::pluck('course_name')));
        return view('pages.aboutus');
    }

    public function successstories()
    {

        $data['title'] = Page::where('id', 2)->value('title');
        $data['description'] = Page::where('id', 2)->value('description');
        $data['unique_courses'] = array_unique(json_decode(AvailableBatches::pluck('course_name')));
        return view('successstories', $data);
    }

    public function contactus()
    {
        return view('pages.contactus');
    }



    public function faq()
    {
        $data['faqs'] = Faqs::orderBy('priority', 'asc')->get();
        $data['faqs'] = Faqs::where(['status' => 1])->get();
        return view('pages.faq', $data);
    }
    public function faq_details($id)
    {
        $data['faqs'] = Faqs::where(['status' => 1])->where('id', '!=', $id)->get();
        $data['faq_details'] = Faqs::where('id', $id)->first();
        return view('pages.faq_details', $data);
    }

    public function privacy_policy()
    {
        $data['title'] = Page::where('id', 5)->value('title');
        $data['description'] = Page::where('id', 5)->value('description');
        return view('pages.privacy_policy', $data);
    }
    public function terms_condition()
    {
        $data['name'] = Setting::where('id', 7)->value('name');
        $data['value'] = Setting::where('id', 7)->value('value');
        return view('pages.terms_condition', $data);
    }
    public function refund_policy()
    {
        $data['name'] = Setting::where('id', 8)->value('name');
        $data['value'] = Setting::where('id', 8)->value('value');
        return view('pages.refund_policy', $data);
    }


    public function gallery()
    {
        $data['photos'] = Photos::orderBy('id','desc')->get();
        return view('pages.gallery',$data);
    }

    protected function availableBatchQuery()
    {
        return AvailableBatches::query()
            ->with([
                'batch:id,name'
            ])
            ->orderBy('id', 'desc')
            ->where('status', 1)
            ->when(request()->search, function ($query, $text) {
                $query->where('batch_name', 'like', "%{$text}%");
            });
    }

    public function batch()
    {
        if(!request()->flag) {
            return view('pages.batch');
        }

        $batches = $this->availableBatchQuery()
            ->paginate(20);

        return view('pages.data.batch', compact('batches'));
    }


    public function fcps_p_1()
    {
        if(!request()->flag) {
            return view('pages.batch');
        }

        $data['batches'] = $this->availableBatchQuery()
            ->where('course_type', 1)
            ->paginate(20);

        return view('pages.data.batch', $data);
    }

    public function residency()
    {
        if(!request()->flag) {
            return view('pages.batch');
        }

        $data['batches'] = $this->availableBatchQuery()
            ->where('course_type', 2)
            ->paginate(20);
            
        return view('pages.data.batch', $data);
    }


    public function outlier()
    {
        if(!request()->flag) {
            return view('pages.batch');
        }

        $data['batches'] = $this->availableBatchQuery()
            ->where('course_type', 3)
            ->paginate(20);

        return view('pages.data.batch', $data);
    }


    public function diploma()
    {
        if(!request()->flag) {
            return view('pages.batch');
        }

        $data['batches'] = $this->availableBatchQuery()
            ->where('course_type', 4)
            ->paginate(20);

        return view('pages.data.batch', $data);
    }

    public function combined()
    {
        if(!request()->flag) {
            return view('pages.batch');
        }

        $data['batches'] = $this->availableBatchQuery()
            ->where('course_type', 5)
            ->paginate(20);

        return view('pages.data.batch', $data);
    }

    public function course()
    {

        $courses = Redis::get('PageCourses');

        if(!$courses){
            $courses = Courses::where('status', 1)->orderBy('priority', 'asc')->get();
            Redis::set('PageCourses', json_encode($courses, TRUE));          
        }
        $courses = json_decode ($courses); 

        return view('pages.course', compact('courses'));

    }

    public function batch_details($batch_id)
    {
        $data['available_batch'] = AvailableBatches::where('id', $batch_id)->first();
        $data['batch_id'] = $batch_id;

        return view('pages.batch_details', $data);
    }

    public function course_detail($id)
    {
        $data['medical_colleges'] = MedicalColleges::orderBy('name')->pluck('name', 'id');
        $data['batches'] = AvailableBatches::orderBy('priority', 'asc')->get();
        $data['doctors_reviews'] = DoctorsReviews::get();
        $data['unique_courses'] = array_unique(json_decode(AvailableBatches::pluck('course_name')));
        $data['courses'] = Courses::where('id', $id)->get();
        //echo $data['courses'];exit;
        return view('pages/course_detail', $data);
    }

    public function course_result($id)
    {
        $data['course_reg_no'] = DoctorsCourses::select('*')->where('id', $id)->first();
        $data['course_id'] = $id;
        $data['exam'] = Result::where('doctor_course_id', $id)->first();
        $results = Result::select('*')->where('doctor_course_id', $id)->get();
        foreach ($results as $row) {
            $row->overall_position = Result::select('id')->where('exam_id', $row->exam_id)->where('obtained_mark_decimal', '>=', $row->obtained_mark_decimal)->groupBy('obtained_mark_decimal')->get();
            $row->subject_position = Result::select('id')->where('exam_id', $row->exam_id)->where('subject_id', $row->subject_id)->where('obtained_mark_decimal', '>=', $row->obtained_mark_decimal)->groupBy('obtained_mark_decimal')->get();
            $row->batch_position = Result::select('id')->where('exam_id', $row->exam_id)->where('batch_id', $row->batch_id)->where('obtained_mark_decimal', '>=', $row->obtained_mark_decimal)->groupBy('obtained_mark_decimal')->get();
            $row->candidate_position = Result::select('id')->where('exam_id', $row->exam_id)->where('candidate_code', $row->candidate_code)->where('obtained_mark_decimal', '>=', $row->obtained_mark_decimal)->groupBy('obtained_mark_decimal')->get();
            $row->exam_highest = Result::where('exam_id', $row->exam_id)->orderBy('obtained_mark', 'desc')->value('obtained_mark');
        }
        //dd($results);
        $data['results'] = $results;

        return view('course_result', $data);
    }


    public function register(Request $request)
    {  
         $validator = Validator::make($request->all(), [
             'name' => 'required|string|max:255',
             'mobile_number' => 'required|string|max:11|min:11|unique:doctors', 
             'email' => 'required|string|email|max:255|unique:doctors',
             'medical_college_id' => 'required',
             'bmdc_no' => 'required|string|min:5|max:7|unique:doctors',
             'password' => 'required|string|min:6|confirmed',
             // 'photo' => 'required',
            ]);
        
        if ($validator->fails()) {
            Session::flash('class', 'alert-danger');
            Session::flash('message', implode(",",$validator->messages()->all())); 
            return redirect('register')->withErrors($validator)->withInput();
        }
              
        if (Doctors::where('bmdc_no', $request->bmdc_no)->orWhere('bmdc_no', 'A' .trim($request->bmdc_no) )->exists()) {
            Session::flash('class', 'alert-danger');
            Session::flash('message', 'Your BMDC number is exists!!!');
            return redirect('register')->withErrors($validator)->withInput();
        }

        //echo($request->password);exit;
        $doctor = new Doctors();
        $doctor->name = $request->name;
        $doctor->email = $request->email;
        $doctor->mobile_number = $request->mobile_number;
        $doctor->medical_college_id = $request->medical_college_id;
        $doctor->bmdc_no = 'A' . $request->bmdc_no;
        $doctor->main_password = $request->password;
        $doctor->password = bcrypt($request->password);
        // if($request->hasFile('photo')){
        //     $file = $request->file('photo');
        //     $extension = $file->getClientOriginalExtension();
        //     $filename = $doctor->bmdc_no.'_'.time().'.'.$extension;
        //     $file->move('upload/photo/',$filename);
        //     $doctor->photo = 'upload/photo/'.$filename;
        // }
        // else {
        //     $doctor->photo = '';
        // }
        //dd($doctor);
        $doctor->save();
        Auth::guard('doctor')->loginUsingId($doctor->id);

        if ($doctor) {
            $this->sendMessage($doctor);
        }

        return redirect('dashboard');
    }

    protected function sendMessage($doctor)
    {
        $smsLog = new SmsLog();
        $response = null;

        $websitename = 'https://www.genesisedu.info/';
        $mob = '88' . $doctor->mobile_number;

        $msg = 'Dear Doctor, Welcome to ' . $websitename . '. Your user ID ' . $doctor->bmdc_no . ' and Password ' . $doctor->main_password . ' Thank you. Stay safe. ' . $websitename;

        // $ch = curl_init();
        // curl_setopt($ch, CURLOPT_URL, "http://sms4.digitalsynapsebd.com/api/mt/SendSMS?user=genesispg&password=123321@12&senderid=8809612440402&channel=Normal&DCS=0&flashsms=0&number=$mob&text=$msg");
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // $response = curl_exec($ch);
        // curl_close($ch);
        // $smsLog->set_response($response, $doctor->id)->set_event('Registration')->save();
        $this->send_custom_sms($doctor,$msg,'Registration',$isAdmin = false); 
    }

    public function login_phone()
    {
        date_default_timezone_set('Asia/Dhaka');
        /*  $from = Carbon::now()->subDays(2);
        $to = Carbon::now()->subDays(1);
        $exams = ExamBatchExam::whereBetween('created_at', [$from, $to])->get();
        foreach($exams as $k => $value){
            $value->info = Result::where([
                'batch_id'=>$value->batch_exam->batch_id,
                'exam_id'=>$value->exam_id
            ])->orderBy('obtained_mark','desc')->first();
        }
        $data['exams'] = $exams;*/
        

        $data['medical_colleges'] = MedicalColleges::orderBy('name')->pluck('name', 'id');
        $data['batches'] = AvailableBatches::orderBy('id', 'asc')->get();
        $data['unique_courses'] = array_unique(json_decode(AvailableBatches::pluck('course_name')));
        $data['advertisements'] = Advertisements::get();
        $data['doctors_reviews'] = DoctorsReviews::get();
        $data['courses'] = Courses::where('status', 1)->orderBy('priority', 'asc')->get();
        $data['bannerSliders'] = BannerSlider::where('status', 1)->orderBy('priority', 'desc')->take(10)->get();
        $data['noticeBoards'] = NoticeBoard::where('status', 1)->latest()->take(4)->get();
        return view('login_phone', $data);
    }


    public function batch_admission_link($batch_id)
    {


        $data['available_batch'] = AvailableBatches::find($batch_id);

        $data['links'] = json_decode($data['available_batch']->links ?? '', true);

        return view('pages.batch_admission_link', $data);
    }
}
