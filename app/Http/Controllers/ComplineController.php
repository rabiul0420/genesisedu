<?php

namespace App\Http\Controllers;

use App\Batches;
use App\Complain;
use App\ComplainRelated;
use App\CourseComplainRelated;
use App\CourseComplainType;
use App\DoctorComplainReply;
use App\Doctors;
use App\DoctorsCourses;
use App\SendSms;
use App\SmsLog;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth as FacadesAuth;
use Illuminate\Support\Str;
use Session;
use Validator;


class ComplineController extends Controller
{
    use SendSms;
    public function index(){
        if(Auth::guard('doctor')->id() != null){
            return redirect('complain-related');
        }else{
            return view('complain.give_mobile_number');
        }
    }

    public function submit_phone_number(Request $request){ 

        if (strlen($request->phone_number) != 11) {
            return response(
                'Dear doctor, Please input 11 digit mobile no.!!!'
            ,200);
        }

        $doctor = Doctors::where('mobile_number', $request->phone_number)->first();

        if( $doctor == null){

            $doctor = new Doctors();
            // $doctor->name = $request->name;
            $doctor->mobile_number = $request->phone_number;
            $doctor->bmdc_no = NULL;
            $password = $this->generatePassword();
            $doctor->main_password = $password;
            $doctor->save();

            $doctor_id = $doctor->id;
            $this->sendMessage($doctor_id);

            return view('complain.password',compact('doctor'));
        }

        return view('complain.password');

    }


    public function password_submit(Request $request){

        // return $request;
        $doctor = Doctors::where(['mobile_number'=> $request->phone_number,'main_password' => $request->password])->first();

        if($doctor != null){

            if(Doctors::where('mobile_number', $request->phone_number)->exists() && Auth::guard('doctor')->check() == false){
                Auth::guard('doctor')->login($doctor);
                $login_access_token = request()->session()->token();

                $doctor->update([
                    'name' => $request->name,
                    'login_access_token' => $login_access_token,
                ]);

                return response(['success' => true]);

            }
            if(Doctors::where('mobile_number', $request->phone_number)->exists() && Auth::guard('doctor')->check() == true){
                return response(['success' => true]);
            }


        }else{
            return response(['message' => 'this password or phone is wrong',]);
        }

    }

    public function complain_related(){
        if(Auth::guard('doctor')->check() == true){
           $complain_relateds = ComplainRelated::get();
            return view('complain.complain_related',compact('complain_relateds'));
        }else{
            return redirect('complain');
        }
    }


    //---------------------------after------------------------
    public function complain_related_topics(Request $request){

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

        if( $request->batch_id == null && ($request->complain_related_id == 3 || $request->complain_related_id == 4 || $request->complain_related_id == 5)){

            $course_complain_type_id =  CourseComplainType::where(['course_id' => '37' ,'complain_type_id' => $request->complain_related_id ])->pluck('id');
            $complains =  Complain::where(['course_complain_type_id' =>  $course_complain_type_id,'doctor_id' => $request->doctor_id])->with('complain_reply')->get();
        }else{
            $complains = null;
        }

        return view('complain.complain_related_batch',compact('batches','complain_related_id','complains'));

    }

    public function complain_details_new($id){

        $data['doc_info'] = Doctors::where('id', Auth::guard('doctor')->id())->first();
        $data['complain_details'] = DoctorComplainReply::where(['doctor_complain_id'=> $id, 'doctor_id' => Auth::guard('doctor')->id()])->orderBy('id', 'desc')->get()->chunk(30);
        $data['complain_details'] = $data['complain_details'][0]->reverse();
        $data['complain_id'] = $id;

        return view('complain.complain_details', $data);
    }



    public function complain_submit(Request $request){

        if($request->description){

            $data['doc_info'] = Doctors::where('id', Auth::guard('doctor')->id())->first();

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

            if(Complain::where([ 'doctor_id' => Auth::guard('doctor')->id(),'conversation_status' => NULL , 'batch_id' => $batch_id , 'course_complain_type_id' =>$course_complain_type_id])->exists()){

                $complain = Complain::where([ 'doctor_id' => Auth::guard('doctor')->id(),'conversation_status' => NULL ,  'batch_id' => $batch_id ,'course_complain_type_id' =>$course_complain_type_id])->first();
                $complain->last_reply_status = "No";
                $complain->last_reply_time = "No";
                $complain->last_reply_status = Carbon::now();
                $complain->push();

                $complain_submit = new DoctorComplainReply();
                $complain_submit->doctor_id = Auth::guard('doctor')->id();

                $complain_submit->user_id = 0;
                $complain_submit->message_by = 'doctor';
                $complain_submit->message = $request->description;
                $complain_submit->doctor_complain_id = $complain->id;
                $complain_submit->is_read = 'No';
                $complain_submit->save();


                return redirect('complain-details-new/'.$complain->id);
            }else{

                $complain = new Complain();
                $complain->doctor_id = Auth::guard('doctor')->id();
                $complain->course_complain_type_id = $course_complain_type_id;
                $complain->conversation_status = NULL;
                $complain->last_reply_status = "No";
                $complain->last_reply_time = Carbon::now();


                if($request->batch_id == null && ($request->complain_related_id == '3' || $request->complain_related_id == '4' || $request->complain_related_id == '5') ){
                    $complain->batch_id = NULL;
                }elseif($request->batch_id != null && ($request->complain_related_id == '3' || $request->complain_related_id == '4' || $request->complain_related_id == '5')){
                    $complain->batch_id = $request->batch_id;
                }else{
                    $complain->batch_id = $request->batch_id;
                }

                $complain->save();

                $complain_submit = new DoctorComplainReply();
                $complain_submit->doctor_id = Auth::guard('doctor')->id();
                $complain_submit->user_id = 0;
                $complain_submit->message_by = 'doctor';
                $complain_submit->message = $request->description;
                $complain_submit->doctor_complain_id = $complain->id;
                $complain_submit->is_read = 'No';
                $complain_submit->save();

                return redirect('complain-details-new/'.$complain->id);
                Session::flash('message', 'Complain submited successfully');
                return back();
            }

        }else{
            Session::flash('message', 'Complain NOT Submitted!');
            return back();
        }
    }



    public function all_comment(Request $request){
        if( $request->batch_id != null && $request->complain_related_id != null){

            $batch = Batches::where('id',$request->batch_id)->with('course')->first();
            $course_complain_type_id =  CourseComplainType::where(['course_id' => $batch->course->id ,'complain_type_id' => $request->complain_related_id ])->pluck('id');
            $data['complains'] =  Complain::where(['course_complain_type_id' =>  $course_complain_type_id,'doctor_id' => $request->doctor_id])->with('complain_reply')->get();
        }

        if( $request->batch_id == null && ($request->complain_related_id == 3 || $request->complain_related_id == 4 || $request->complain_related_id == 5)){

            $course_complain_type_id =  CourseComplainType::where(['course_id' => '37' ,'complain_type_id' => $request->complain_related_id ])->pluck('id');
            $data['complains'] =  Complain::where(['course_complain_type_id' =>  $course_complain_type_id,'doctor_id' => $request->doctor_id])->with('complain_reply')->get();
        }

        return  view('complain.complain_all',$data);

    }

    public function view_reply(){
        $complain_relateds = ComplainRelated::get();
        return view('complain.view_replay',compact('complain_relateds'));
    }

    protected function sendMessage($doctor_id)
    {

        $smsLog = new SmsLog();
        $response = null;

        $doctor = Doctors::where('id', $doctor_id)->first();

        $alternative_login = 'https://www.genesisedu.info';
        $mob = '88' . $doctor->mobile_number;
        $msg = 'your password ' . $doctor->main_password . ' ' . $alternative_login;

        // $ch = curl_init();
        // curl_setopt($ch, CURLOPT_URL, "http://sms4.digitalsynapsebd.com/api/mt/SendSMS?user=genesispg&password=123321@12&senderid=8809612440402&channel=Normal&DCS=0&flashsms=0&number=$mob&text=$msg");
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // $response = curl_exec($ch);
        // curl_close($ch);
        // $smsLog->set_response($response, $doctor->id)->set_event('Complain Box Registration')->save();

        $this->send_custom_sms($doctor,$msg,'Complain Box Registration',$isAdmin = false); 
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

    protected function doctorcourse($id){
      $doctor_course = DoctorsCourses::where(['doctor_id'=> $id , 'payment_status' =>'Completed'])
      ->with(['active_batch' => function($query){
        $query->where('status','1');
      }])->get();

      return $doctor_course;
    }

    public function complain_again_new( Request $request )
    {
        $complain = Complain::where('id',$request->complain_id)->first();

        if ($request->description ) {
            $complain_again = new DoctorComplainReply();
            $complain_again->doctor_id = Auth::guard('doctor')->id();
            $complain_again->user_id = 0;
            $complain_again->message_by = 'doctor';
            $complain_again->message = $request->description;
            $complain_again->doctor_complain_id = $request->complain_id;
            $complain_again->is_read = 'No';
            $complain_again->save();

            Complain::where('id',$complain_again->doctor_complain_id)
            ->update(['last_reply_status' => 'No','last_reply_time'=>Carbon::now()]);

            Session::flash('message', 'Complain Submit successfully');
            return back();
        } else {
            Session::flash('message', 'NOT Submitted!');
            return back();
        }
    }

    public function password_send_complain(){
        return view('complain.password_recovery');
    }
    public function password_recovery_complain(Request $request){
        $smsLog = new SmsLog();
        $response = null;

        $doctor = Doctors::where( 'mobile_number', $request->phone_number )->where('status',1)->first();

        if( !$doctor ) {
            return back()->with(['alert-class'=>'alert-danger','message' => 'This Mobile number not Registered.']);
        }

        $mob = '88'.$request->phone_number;
        $msg = "Dear Doctor, Your User ID: " . $doctor->bmdc_no . ", Password: " . $doctor->main_password . ".  Please login at " . url('https://www.genesisedu.info');
        // $msg = str_replace(' ', '%20', $msg);
        // $ch = curl_init();
        // curl_setopt($ch, CURLOPT_URL, "http://sms4.digitalsynapsebd.com/api/mt/SendSMS?user=genesispg&password=123321@12&senderid=8809612440402&channel=Normal&DCS=0&flashsms=0&number=$mob&text=$msg");
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        // $response = curl_exec($ch);
        // curl_close($ch);
        // $smsLog->set_response( $response,$doctor->id)->set_event('Forget Password')->save();
        $this->send_custom_sms($doctor,$msg,'Forget Password',$isAdmin = false); 
        Session::flash('message', 'Password will be sent to your mobile number within 1 minute. Please collect the password from SMS. Type your mobile number and click "NEXT" button & then input the password in the password box.');
        return redirect ('/complain');
    }


}
