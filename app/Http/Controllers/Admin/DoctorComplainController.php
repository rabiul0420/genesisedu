<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Doctors;
use App\DoctorAsk;
use App\DoctorAsks;
use App\DoctorComplain;
use App\DoctorComplainReply;
use App\Sessions;
use Illuminate\Http\Request;
use App\Exam;
use App\Exam_question;
use App\Institutes;
use App\Courses;
use App\Faculty;
use App\Subjects;
use App\Batches;
use App\Branch;
use App\Complain;
use App\ComplainRelated;
use App\DoctorsCourses;
use App\ExecutiveCourse;
use App\ExecutivesStuffs;
use App\SmsLog;
use App\User;
use App\UserComplainAssign;
use App\CourseComplainType;
use App\SendSms;
use App\Setting;
use App\AutoReply;
use Session;
use Auth;
use Carbon\Carbon;
use Illuminate\Contracts\Session\Session as SessionSession;
use Validator;
use Illuminate\Support\Str;


use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;


class DoctorComplainController extends Controller
{
    use SendSms;
    //
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $data['module_name'] = 'Doctor Complain';
        $data['title'] = 'Doctor Complain List';
        $data['breadcrumb'] = explode('/', $_SERVER['REQUEST_URI']);

        return view('admin.doctor_complain.list', $data);
    }



    public function doctor_complain()
    {
        $doctor_complain = DoctorComplain::select('*');

        return DataTables::of($doctor_complain)
            ->addColumn('action', function ($doctor_complain) {
                return view('admin.doctor_complain.ajax_list', (['doctor_complain' => $doctor_complain]));
            })

            ->make(true);
    }

    public function doctor_complain_list()
    {
        $data['module_name'] = 'Doctors Complains';
        $data['title'] = 'Doctors Complains List';
        $data['breadcrumb'] = explode('/', $_SERVER['REQUEST_URI']);
        $data['submit_value'] = 'Submit';

        $data['batches'] = Batches::pluck('name', 'id');
        $data['courses']= Courses::pluck('name', 'id');
        $data['sessions'] = Sessions::pluck('name', 'id');
        $data['complain_types'] = ComplainRelated::pluck('name', 'id');
        $data['years'] = array(''=>'--Select year--');
        for($year = date("Y")+1;$year>=2017;$year--){
            $data['years'][$year] = $year;
        }


        return view('admin.doctor_complain.doctor_complain_list', $data);
    }

    public function last_reply_item(){
        return   [ 1 =>'Yes', 2 => 'No' ];
    }

    public function doctor_complain_ajax_list(Request $request)
    {
        $user = Auth::user();

        $year = $request->year;
        $session_id = $request->session_id;
        $course_id = $request->course_id;
        $batch_id = $request->batch_id;
        $complain_type_id = $request->complain_type_id;
        $reply = $request->reply ;
        $start_date = $request->start_date;
        $end_date = $request->end_date;

        $user_role_ids = DB::table('model_has_roles')->where('model_has_roles.model_id',$user->id)->pluck('model_has_roles.role_id');
        $executive_site_setup = Setting::where('name','executive_role_id')->first(); 

        if (in_array($executive_site_setup->value,$user_role_ids->toArray())) {

            $user_complain_assign = UserComplainAssign::where('user_id', $user->id)->first();
            
            $course_complain_type_ids = json_decode($user_complain_assign->course_complain_type_id);

            $doctor_complain = DB::table('complains as d1')
                ->leftjoin('doctors as d2', 'd1.doctor_id', '=', 'd2.id')
                ->leftjoin('complain_type_course as d3', 'd1.course_complain_type_id', '=', 'd3.id')
                ->leftjoin('batches as b','d1.batch_id' , '=' , 'b.id')
                ->leftjoin('courses as c','b.course_id' , '=' , 'c.id');

                if($year){
                    $doctor_complain = $doctor_complain->where('b.year', '=', $year);
                }
                if($session_id){
                    $doctor_complain = $doctor_complain->where('b.session_id', '=', $session_id);
                }
                if($course_id){
                    $doctor_complain = $doctor_complain->where('d3.course_id', '=', $course_id);
                }
                if($batch_id){
                    $doctor_complain = $doctor_complain->where('d1.batch_id', '=', $batch_id);
                }

                if($complain_type_id){
                    $doctor_complain = $doctor_complain->where('d3.complain_type_id', '=', $complain_type_id);
                }
                if($reply){
                    $doctor_complain = $doctor_complain->where('d1.last_reply_status', '=', $reply);
                }
                if($start_date && $end_date){
                    $doctor_complain = $doctor_complain->whereBetween('d1.created_at', [$start_date, $end_date]);
                }

                $doctor_complain = $doctor_complain->whereIn('d1.course_complain_type_id', $course_complain_type_ids);

                $doctor_complain = $doctor_complain->select(
                    'd1.*',
                    'd1.id as complain_id',
                    'd2.name as doctor_name',
                    'd2.bmdc_no as doctor_bmdc_no',
                    'd2.mobile_number as doctor_mobile_number',
                    'd3.complain_type_id as complain_type',
                    'b.name as batch_name',
                    'c.name as course_name',

                    )->orderBy('d1.last_reply_status', 'ASC')->orderBy('d1.created_at', 'DESC')->orderBy('d1.last_reply_time', 'DESC');
                

        } else {
            $doctor_complain = DB::table('complains as d1')
            ->leftjoin('doctors as d2', 'd1.doctor_id', '=', 'd2.id')
            ->leftjoin('complain_type_course as d3', 'd1.course_complain_type_id', '=', 'd3.id')
            ->leftjoin('batches as b','d1.batch_id' , '=' , 'b.id')
            ->leftjoin('courses as c','b.course_id' , '=' , 'c.id');


            if($year){
                $doctor_complain = $doctor_complain->where('b.year', '=', $year);
            }
            if($session_id){
                $doctor_complain = $doctor_complain->where('b.session_id', '=', $session_id);
            }
            if($course_id){
                $doctor_complain = $doctor_complain->where('d3.course_id', '=', $course_id);
            }
            if($batch_id){
                $doctor_complain = $doctor_complain->where('d1.batch_id', '=', $batch_id);
            }
            if($complain_type_id){
                $doctor_complain = $doctor_complain->where('d3.complain_type_id', '=', $complain_type_id);
            }
            if($reply){
                $doctor_complain = $doctor_complain->where('d1.last_reply_status', '=', $reply);
            }

            if($start_date && $end_date){
                $doctor_complain = $doctor_complain->whereBetween('d1.created_at', [$start_date, $end_date]);
            }    

            $doctor_complain = $doctor_complain->select(
                'd1.*',
                'd1.id as complain_id',
                'd2.name as doctor_name',
                'd2.bmdc_no as doctor_bmdc_no',
                'd2.mobile_number as doctor_mobile_number',
                'd3.complain_type_id as complain_type',
                'b.name as batch_name',
                'c.name as course_name',
                // 'u.name as user_name',

            )->orderBy('d1.last_reply_status', 'ASC')->orderBy('d1.created_at', 'DESC')->orderBy('d1.last_reply_time', 'DESC');
        }

    //    dd($doctor_complain);

        return DataTables::of($doctor_complain)
        
        ->addColumn('action', function ($doctor_complain) {
            return view('admin.doctor_complain.ajax_list', (['doctor_complain' => $doctor_complain]));
        })
        
        ->addColumn('complain_related', function ($doctor_complain) {
            return ($doctor_complain->complain_type == 1) ? 'Lecture Video/Exam Solve Video' :  (($doctor_complain->complain_type == "2") ? 'Exam Link' :  (($doctor_complain->complain_type == "3") ? 'Publications (Lecture Sheet/Books)' :  (($doctor_complain->complain_type == "4") ? 'Technical & Payment Issue' :  (($doctor_complain->complain_type == "5") ? 'Others' : ' '))));
        })

        ->addColumn('batch_name', function ($doctor_complain) {
            return ($doctor_complain->batch_name ?? ' ');
        })

        ->addColumn('complain_create_time', function ($doctor_complain) {
            return date('d M Y h:i a', strtotime($doctor_complain->created_at));
        })

        ->addColumn('last_reply_time', function ($doctor_complain) {
            return ( $doctor_complain->last_reply_time != null ? date('d M Y h:i a' , strtotime($doctor_complain->last_reply_time)) : 'No Reply');
        })

        ->make(true);
    }

    public function view_complain($id)
    {

        if (DoctorComplainReply::where('doctor_complain_id', $id)->count() > 0) {

            $data['module_name'] = 'Doctor Complain';
            $data['title'] = 'Doctor Complain';
            $data['breadcrumb'] = explode('/', $_SERVER['REQUEST_URI']);
            $data['submit_value'] = 'Submit';

            $doctor_complain_reply = DoctorComplainReply::where(['doctor_complain_id' => $id, 'user_id' => "0"])->update(['is_read' => 'Yes']);
            $data['doctor_complain_reply'] = $doctor_complain_reply;
            $data['user'] = User::find(Auth::id());
            $data['doctor_complain_replies'] = DoctorComplainReply::where('doctor_complain_id', $id)->get();
            $data['complain'] =  Complain::with('batch','complain_reply_new')->where('id', $id)->first();
            $data['question_tittle'] = AutoReply::pluck('title','question_link');
            // return  $data;
            return view('admin.doctor_complain.view_complain', $data);
        } else {
            Session::flash('class', 'alert-danger');
            Session::flash('message', 'The doctor did not complained');
            return redirect()->action('Admin\DoctorComplainController@doctor_complain_list');
        }
    }

    public function doctor_complain_message($mobile_number , $user_id)
    {

        $admin_id = Auth::id();
        $smsLog = new SmsLog();
        $doctor = Doctors::where('mobile_number', $mobile_number)->first();

        $response = null;
        $mob = '88' . $mobile_number;
        $complain_url = 'https://genesisedu.info/complain';

        $msg ='Dear doctor, your submitted complain checked and solved. Please goto ' . $complain_url . ' and click "VIEW REPLY". Thanks GENESIS';

        // $ch = curl_init();
        // curl_setopt($ch, CURLOPT_URL, "http://sms4.digitalsynapsebd.com/api/mt/SendSMS?user=genesispg&password=123321@12&senderid=8809612440402&channel=Normal&DCS=0&flashsms=0&number=$mob&text=$msg");
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // $response = curl_exec($ch);
        // curl_close($ch);
        // $smsLog->set_response($response, $doctor->id, $mobile_number, $admin_id)->set_event('Complain Reply')->save();

        $this->send_custom_sms($doctor,$msg,'Complain Reply',$isAdmin = true); 

        return redirect()->back();
    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function reply_complain(Request $request)
    {
        if($request->done_message == 1){

            // $validator = Validator::make($request->all(), [
            //     'done_message' => ['required'],
            //     // 'message' =>['required']
            // ]);
            // if ($validator->fails()) {
            //     Session::flash('class', 'alert-danger');
            //     Session::flash('message', "Please enter valid Data");
            //     return redirect()->action('Admin\DoctorComplainController@view_complain', [$request->doctor_complain_id])->withInput();
            // }

            Complain::where(['doctor_id' => $request->doctor_id, 'id' => $request->doctor_complain_id])->update([
                'conversation_status' => 'Completed',
                'last_reply_time' => Carbon::now()    
            ]);

            // $complain_reply = DoctorComplainReply::where(['doctor_id' => $request->doctor_id, 'doctor_complain_id' => $request->doctor_complain_id])->latest('id')->first();
            // $complain_reply->user_id = Auth::id();
            // $complain_reply->push();

            return redirect()->action('Admin\DoctorComplainController@view_complain', [$request->doctor_complain_id]);

        }else{
            $validator = Validator::make($request->all(), [
                'message' => ['required'],
                'doctor_id' => ['required'],
                //'doctor_name' => ['required'],
                'mobile_number' => ['required'],
                'doctor_complain_id' => ['required'],
                'message' => ['required'],
            ]);
            if ($validator->fails()) {
                Session::flash('class', 'alert-danger');
                Session::flash('message', "Please enter valid Data");
                return redirect()->action('Admin\DoctorComplainController@view_complain', [$request->doctor_complain_id])->withInput();
            }

            $doctor_complain_reply = new DoctorComplainReply();

            $doctor_complain_reply->doctor_id = $request->doctor_id;
            $doctor_complain_reply->user_id = Auth::id();
            $doctor_complain_reply->message_by = 'admin';
            $doctor_complain_reply->message = $request->message;
            $doctor_complain_reply->doctor_complain_id = $request->doctor_complain_id;
            $doctor_complain_reply->save();

            // DoctorComplain::where('doctor_id',$request->doctor_id)->update(['last_reply_status' => 'Yes','last_reply_time'=>Carbon::now()]);
            Complain::where(['doctor_id' => $request->doctor_id, 'id' => $request->doctor_complain_id])->update(['last_reply_status' => 'Yes', 'last_reply_time' => Carbon::now()]);

            Session::flash('message', 'Record has been added successfully');
            return redirect()->action('Admin\DoctorComplainController@view_complain', [$request->doctor_complain_id]);
        }
        

        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        /*$user=Subjects::find(Auth::id());
        if(!$user->hasRole('Admin')){
            return abort(404);
        }*/
        DoctorComplain::destroy($id); // 1 way
        Session::flash('message', 'Record has been deleted successfully');
        return redirect()->action('Admin\DoctorComplainController@index');
    }


    public function complain_create()
    {
        return view('admin.complain_new.create');
    }

    public function search_doctors_complain(Request $request)
    {
        $text =  $_GET['term'];
        $text = $text['term'];
        $data = Doctors::select(DB::raw("CONCAT(mobile_number) AS name_bmdc"), 'id')
            ->where('mobile_number', 'like', '%' . $text . '%')
            ->get();
        echo json_encode($data);
    }

    public function complain_related_topics(Request $request)
    {
        
        $doctor_course = $this->doctorcourse($request->doctor_id);

        $total_course = (count($doctor_course));
        $complain_related_id = $request->complain_related_id ;

        if($total_course <=  0 && ($request->complain_related_id == 1 || $request->complain_related_id == 2)){
            return  'please take admission in course';
        }

        if($total_course > 0){
            foreach($doctor_course as $course){
                if($course->active_batch == null){
                    continue;
                }else{
                    $batches[] = $course->active_batch;
                }
            
            }
        }else{
            $batches = null;
        }

        return view('admin.complain_new.complain_related_batch',compact('batches','complain_related_id'));

    }

    public function complain_quick_register()
    {
        return view('admin.complain_new.quick_register');
    }

    public function complain_quick_register_submit(Request $request)
    {
        if (Doctors::where('mobile_number', $request->mobile_number)->exists()) {
            Session::flash('class', 'alert-danger');
            Session::flash('message', 'Mobile Number Already Exists!');
            return back()->withInput();
        } else {

            $doctor = new Doctors();
            $doctor->mobile_number = $request->mobile_number;
            $doctor->main_password = $this->generatePassword();
            $doctor->password = bcrypt($doctor->main_password);
            $doctor->save();
            $this->sendMessage($doctor->id);

            Session::flash('class', 'alert-success');
            Session::flash('message', 'Doctor Registration Created Successfully!');
            return back()->withInput();
        }
    }

    protected function sendMessage($doctor_id)
    {

        $smsLog = new SmsLog();
        $response = null;
        $admin_id = Auth::id();

        $doctor = Doctors::where('id', $doctor_id)->first();

        $alternative_login = 'https://www.genesisedu.info/login-phone';
        $mob = '88' . $doctor->mobile_number;
        $msg = 'Dear doctor ' . $doctor->main_password . ' is your password . Please click alternative login or click ' . $alternative_login;

        // $ch = curl_init();
        // curl_setopt($ch, CURLOPT_URL, "http://sms4.digitalsynapsebd.com/api/mt/SendSMS?user=genesispg&password=123321@12&senderid=8809612440402&channel=Normal&DCS=0&flashsms=0&number=$mob&text=$msg");
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // $response = curl_exec($ch);
        // curl_close($ch);
        // $smsLog->set_response($response, $doctor->id , $mob , $admin_id)->set_event('Quick Registration')->save();
        $this->send_custom_sms($doctor,$msg,'Quick Registration',$isAdmin = true);
        
    }


    public function generatePassword()
    {
        $password = strtoupper(Str::random(6));

        $strings = "ABDEFGHLMNPQRTY";
        $numbers = "123456789";

        $hashOrStar = "#*@";

        $str_length = strlen($strings);
        $num_length = strlen($numbers);

        $output = "";

        $modes = 'shn';

        $mds = "";

        $char_added = 0;
        $number_added = 0;
        $hash_added = 0;


        $index = 0;


        while (($char_added + $number_added + $hash_added) < 6) {

            $mode = $modes[rand(0, strlen($modes) - 1)];
            $mds .= $mode;

            switch ($mode) {
                case 's':
                    $ind = rand(0, $str_length - 1);
                    $output .= $strings[$ind];
                    $char_added++;
                    $index++;
                    break;
                case 'n':
                    $ind = rand(0, $num_length - 1);
                    $output .= $numbers[$ind];
                    $number_added++;
                    $index++;
                case 'h':
                    if ($hash_added < 1 && $index > 0 && $index < 6) {
                        $output .= $hashOrStar[rand(0, 2)];
                        $hash_added++;
                        $index++;
                    }
            }
        }

        return $output;
    }


    protected function doctorcourse($id)
    {
        $doctor_course = DoctorsCourses::where(['doctor_id' => $id, 'payment_status' => 'Completed'])
            ->with(['active_batch' => function ($query) {
                $query->where('status', '1');
            }])->get();

        return $doctor_course;
    }

    public function complain_store(Request $request)
    {

        $validation = Validator::make($request->all(), [
            'doctor_id' => 'required',
            'complain_related_id' => 'required',
            'description' => 'required',
        ]);

        if ($validation->fails()) {
            Session::flash('class', 'alert-danger');
            Session::flash('message', 'Please Input proper data!');
            return back()->withInput();
        }

        $doctor = Doctors::where('id', $request->doctor_id)->first();

        if($request->batch_id){
            $batch = Batches::with('course')->where('id',$request->batch_id)->first();
            $batch_id = $batch->id;
            $course_id = $batch->course->id;
         }else{
             $course_id = '37';
             $batch_id = NULL;

         }

         if(CourseComplainType::where(['course_id' => $course_id , 'complain_type_id' => $request->complain_related_id])->exists()){
            $course_complain_type = CourseComplainType::where(['course_id' => $course_id ,'complain_type_id' => $request->complain_related_id])->first();
             $course_complain_type_id = $course_complain_type->id;
         }else{
             $course_complain_type = new CourseComplainType();
             $course_complain_type->course_id = $course_id;
             $course_complain_type->complain_type_id = $request->complain_related_id;
             $course_complain_type->save();
             $course_complain_type_id = $course_complain_type->id;
         }



        $complain = new Complain();
        $complain->doctor_id = $doctor->id;
        $complain->course_complain_type_id = $course_complain_type_id;
        $complain->conversation_status = NULL;

        if ($request->batch_id == null && ($request->complain_related_id == '3' || $request->complain_related_id == '4' || $request->complain_related_id == '5')) {
            $complain->batch_id = NULL;
        } elseif ($request->batch_id != null && ($request->complain_related_id == '3' || $request->complain_related_id == '4' || $request->complain_related_id == '5')) {
            $complain->batch_id = $batch_id;
        } else {
            $complain->batch_id = $batch_id;
        }

        $complain->save();

        $complain_submit = new DoctorComplainReply();
        $complain_submit->doctor_id =  $doctor->id;
        $complain_submit->user_id = 0;
        $complain_submit->message_by = 'doctor';
        $complain_submit->message = $request->description;
        $complain_submit->doctor_complain_id = $complain->id;
        $complain_submit->is_read = 'No';
        $complain_submit->save();

        Session::flash('message', 'Complain store successfully');
        $this->sendmessage_complain_store($doctor);
        return redirect('admin/doctor-complain-list');
    }

    protected function sendmessage_complain_store($doctor)
    {
        $admin_id = Auth::id();
        $smsLog = new SmsLog();
        $response = null;
        $mob = '88' . $doctor->mobile_number;
        $msg = 'Dear Doctor, complain submitted successfully. You will be replied within 24 hours. Thanks GENESIS.';

        // $ch = curl_init();
        // curl_setopt($ch, CURLOPT_URL, "http://sms4.digitalsynapsebd.com/api/mt/SendSMS?user=genesispg&password=123321@12&senderid=8809612440402&channel=Normal&DCS=0&flashsms=0&number=$mob&text=$msg");
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // $response = curl_exec($ch);
        // curl_close($ch);
        // $smsLog->set_response($response, $doctor->id , $mob, $admin_id)->set_event('Complain Create')->save();
        $this->send_custom_sms($doctor,$msg,'Complain Create',$isAdmin = true);
        
    }

    public function query_optimize(){
       $complains = Complain::where('id', '<' , '5557')->get();
       
       foreach($complains as $complain){
           
        if($complain->batch_id != null){
            $batch = Batches::with('course')->where('id',$complain->batch_id)->first();
            $batch_id = $batch->id;
            $course_id = $batch->course->id ?? '';
         }else{
             $course_id = '37';
             $batch_id = NULL;

         }

         if(CourseComplainType::where(['course_id' => $course_id , 'complain_type_id' => $complain->complain_type_id])->exists()){
            $course_complain_type = CourseComplainType::where(['course_id' => $course_id ,'complain_type_id' => $complain->complain_type_id])->first();
             $course_complain_type_id = $course_complain_type->id;
         }else{
             $course_complain_type = new CourseComplainType();
             $course_complain_type->course_id = $course_id;
             $course_complain_type->complain_type_id = $complain->complain_type_id;
             $course_complain_type->save();
             $course_complain_type_id = $course_complain_type->id;
         }

         $complain->course_complain_type_id = $course_complain_type_id;
         $complain->batch_id = $batch_id;
         $complain->push();
       }
    }
}
