<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Doctors;
use App\Http\Controllers\Controller;


use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Auth;
use Validator;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\TemporeEmail;
use Session;
use Hash;
use Illuminate\Support\Facades\Redis;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/admin';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function logincode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return redirect('admin/login')->with('postSignin_error_message', 'Invalid username or password!')->withErrors($validator)->withInput();
        }




        $u = User::where('email', $request->input('email'))->where(['type'=> 2,'status'=>'1'])->first();



         if( $u AND $u->two_factor=='No'){


             $this->validateLogin($request);

             // If the class is using the ThrottlesLogins trait, we can automatically throttle
             // the login attempts for this application. We'll key this by the username and
             // the IP address of the client making these requests into this application.
             if ($this->hasTooManyLoginAttempts($request)) {
                 $this->fireLockoutEvent($request);
                 return $this->sendLockoutResponse($request);
             }

             $user = User::where($this->username(), $request->{$this->username()})->first();

             if ($user) {
                 if ($this->attemptLogin($request)) {
                     //  return $this->sendLoginResponse($request);
                    return redirect('/admin');
                 }
             }



             // If the login attempt was unsuccessful we will increment the number of attempts
             // to login and redirect the user back to the login form. Of course, when this
             // user surpasses their maximum number of attempts they will get locked out.
             $this->incrementLoginAttempts($request);



             if ($user) {

                 if ($user->status === 'Pending') {
                     return $this->sendFailedLoginResponse($request, 'auth.pending_status');
                 } elseif ($user->status === 1) {
                     return $this->sendFailedLoginResponse($request, 'auth.pending_status');
                 }
             }

             return $this->sendFailedLoginResponse($request);

         }





        if ($u && Hash::check($request->input('password'), $u->password))
        {
            $length = 6;
            $characters = '0123456789';
            $charactersLength = strlen($characters);
            $randomString = '';
            for ($i = 0; $i < $length; $i++) {
                $randomString .= $characters[rand(0, $charactersLength - 1)];
            }


            //Save user name and password, randomString in seession

            session(['email'=>$request->input('email')]);
            session(['password'=>$request->input('password')]);
            session(['randomstring' => $randomString]);

            $udata['subject'] = 'Login Verification Code';
            $udata['message'] = 'Login Verification Code is : '.$randomString;

            Mail::to($request->input('email'))->send(new TemporeEmail($udata));


            return redirect('admin/login/getlogincode')->with('code_info', 'Please enter the code!');
        }else{
            Session::flash('message', 'Invalid username or password!');
            return redirect('admin/login')->with('postSignin_error_message', 'Invalid username or password!')->withErrors($validator)->withInput();
        }

    }

    public function showLoginForm()
    {
        return view('admin.auth.login');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        return redirect()->to('admin');
    }

    /* Function that load the verification screen to the customer */
    public function getLoginCode(){
        return view('admin.auth.confimcode');
    }

    public function login(Request $request)
    {
       //echo $request->email.$request->password;exit;
        if(session('randomstring') != $request->confirmation_code){
            Session::flash('message', 'Login verification code is not correct');
            return back();
        }



        $this->validateLogin($request);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);
            return $this->sendLockoutResponse($request);
        }

        $user = User::where($this->username(), session('email'))->first();



        if ($user) {
            if ($this->attemptLogin($request)) {
                return $this->sendLoginResponse($request);
            }
        }



        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);



        if ($user) {

            if ($user->status === 'Pending') {
                return $this->sendFailedLoginResponse($request, 'auth.pending_status');
            } elseif ($user->status === 1) {
                return $this->sendFailedLoginResponse($request, 'auth.pending_status');
            }
        }

        return $this->sendFailedLoginResponse($request);
    }

    protected function sendFailedLoginResponse(Request $request, $trans = 'auth.failed')
    {
        $errors = [$this->username() => trans($trans)];
        if ($request->expectsJson()) {
            return response()->json($errors, 422);
        }
        return redirect()->back()
            ->withInput($request->only($this->username(), 'remember'))
            ->withErrors($errors);
    }


    protected function credentials(Request $request)
    {
        return array_merge($request->only($this->username(), 'password',['type' => 2]));
    }




}
