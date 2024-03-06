<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Doctors;
use App\SendSms;
use App\SmsLog;
use DB;
use Auth;

class PasswordSendController extends Controller
{
    use SendSms;
    //
        // use SendsPasswordResetEmails;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function password_send()
    {
       return view('auth.password_send');
    }

    public function password_submit(Request $request)
    {


        $lastTwoMinActivity = SmsLog::join('doctors', 'doctors.id', '=', 'sms_logs.doctor_id')
        ->where( 'doctors.mobile_number', $request->phone_number )
        ->where( 'sms_logs.event_type', 12 )
        ->orderBy( 'sms_logs.created_at', 'DESC' )
        ->select( 'sms_logs.*' )
        ->first();

        if($lastTwoMinActivity && $lastTwoMinActivity->created_at && $lastTwoMinActivity->created_at->diffInMinutes() < 2 ){ 
            return redirect ('/login')->with([ 
                'alert-class'=>'alert-danger',  
                'message' => 'Dear Doctor, Your previous "Forgot Password" is on process.Please wait for a while. Thank You.'
            ]);   
        }

        $admin_id = Auth::id();
        $smsLog = new SmsLog();
        $response = null;
        $doctor = Doctors::where( 'mobile_number', $request->phone_number )->where('status',1)->first();
        if( !$doctor ) {
            return back()->with(['alert-class'=>'alert-danger','message' => 'This Mobile number not Registered.']);
        }

        $mob = '88'.$request->phone_number;
        $msg = "Dear Doctor, Your User ID: " . $doctor->bmdc_no . ", Password: " . $doctor->main_password . ".  Please login at " . url('https://www.genesisedu.info');
        $msg =   $msg;
        // $ch = curl_init();
        // curl_setopt($ch, CURLOPT_URL, "http://sms4.digitalsynapsebd.com/api/mt/SendSMS?user=genesispg&password=123321@12&senderid=8809612440402&channel=Normal&DCS=0&flashsms=0&number=$mob&text=$msg");
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        // $response = curl_exec($ch);
        // curl_close($ch);
        // $smsLog->set_response( $response,$doctor->id,$mob,$admin_id)->set_event('Forget Password')->save();
        $this->send_custom_sms($doctor,$msg,'Forget Password',false);

        return redirect ('/login')->with(['alert-class'=>'alert-success', 'message' => 'Your User ID & Password sent Successfully.']);
    }




}



