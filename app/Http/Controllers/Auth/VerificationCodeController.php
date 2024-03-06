<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Doctors;
use App\SendSms;

class VerificationCodeController extends Controller
{
    use SendSms;
    //
        // use SendsPasswordResetEmails;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function get_verification_code()
    {
       return view('auth.get_verification_code');
    }

    // public function get_verification_code_submit(Request $request)
    // {
    
    //     // $doctor = Doctors::where( 'mobile_number', $request->phone_number )->where('status',1)->first();

    //     // if( !$doctor ) {
    //     //     return back()->with(['class'=>'alert-danger','message' => 'This Mobile number not Registered.']);
    //     // }

    //     // $mob = '88'.$request->phone_number;
    //     // $msg = "Dear Doctor, Your User ID: " . $doctor->bmdc_no . ", Password: " . $doctor->main_password . ".  Please login at " . url('https://www.genesisedu.info');
    //     // $msg = str_replace(' ', '%20', $msg);
    //     // $ch = curl_init();
    //     // curl_setopt($ch, CURLOPT_URL, "http://sms4.digitalsynapsebd.com/api/mt/SendSMS?user=genesispg&password=123321@12&senderid=8809612440402&channel=Normal&DCS=0&flashsms=0&number=$mob&text=$msg");
    //     // curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
    //     // curl_exec($ch);
    //     // curl_close($ch);
        
    //     return redirect ('/login')->with(['class'=>'alert-success', 'message' => 'Your User ID & Password sent Successfully.']);
    // }




}




