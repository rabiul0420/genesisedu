<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Doctors;

class RegistrationSatusController extends Controller
{
    //
        // use SendsPasswordResetEmails;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function registration_status()
    {
       return view('auth.registration_status');
    }

    public function registration_status_submit(Request $request)
    {
        $doctor = Doctors::where( 'mobile_number', $request->phone_number )->where('status',1)->first();

        if( !$doctor ) {
            $request->session()->flash ('class','alert-danger');
            $request->session()->flash ('message','Dear Doctor you are not registered.<br>Please <a href="'.url('register') .'" >Click here </a> to complete your registration.');

            return back();//->with(['class'=>'alert-danger','message' => 'Dear Doctor you are not registered please <a href="'.url('login') .'" >Click here </a> to compleate your registration.']);
        }

        return redirect ('registration-status')->with(['class'=>'alert-success', 'message' => 'Dear Doctor you are already registered.<br>Please <a href="'.url('password-send') .'" >Click here </a> to recovery your User ID & Password.']);
    }




}




