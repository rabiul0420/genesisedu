<?php

namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use App\Mail\TemporeEmail;
use App\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

use App\Doctors;
use App\DoctorsReviews;
use App\MedicalColleges;
use App\AvailableBatches;
use App\Advertisements;
use App\Photos;
use App\Courses;
use App\Result;
use App\ExamBatchExam;
use Carbon\Carbon;
use App\BannerSlider;
use App\Faqs;
use App\MedicalCollege;
use App\Quiz;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Session;
use Auth;
use Exception;
use Illuminate\Support\Facades\Cache;
use Validator;
use Illuminate\Support\Facades\Hash;
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
    //protected $redirectTo = '/home';
    protected $redirectTo = '/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        
    }

    public function username()
    {
        return request()->mobile_number ? 'mobile_number' : 'bmdc_no';
    }

    public function showLoginForm()
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
            ->latest()
            ->take(3)
            ->get();

        $data["quizzes"] = $quizzes->filter(function($quiz) {
            return $quiz->quiz_property->total_question == $quiz->quiz_questions->count();
        });
            
        return view('home',$data);
    }

    protected function guard(){
        return Auth::guard('doctor');
    }


    public function login(Request $request)
    {
        
        $this->validateLogin($request);
        
        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);
            
            return $this->sendLockoutResponse($request);
        }
        
        
        // if ($this->attemptLogin($request)) {
            //     return $this->sendLoginResponse($request);
            // }
            
            // If the login attempt was unsuccessful we will increment the number of attempts
            // to login and redirect the user back to the login form. Of course, when this
            // user surpasses their maximum number of attempts they will get locked out.
            $this->incrementLoginAttempts($request);
            
            $user = Doctors::where(function($query){
                if(request()->bmdc_no){
                    $query->where( 'bmdc_no', 'A'. request()->bmdc_no )
                    ->orWhere( 'bmdc_no', request()->bmdc_no );
                }
                else{
                    $query->where('mobile_number', request()->mobile_number);
                }
            
        })->where('main_password', $request->password)->where('status', 1)->first();

        //dd($user, 'A'.$request->bmdc_no,  $request->password);

        if ($user) { 
            if(Auth::guard('doctor')->check()) {
                Auth::guard('doctor')->logout();
            }
    
            Auth::guard('doctor')->login($user, true);
    
            $login_access_token= request()->session()->token();
    
            $user->update([
                'login_access_token' => $login_access_token
            ]);
            
            return redirect()->intended('/dashboard');
            return redirect('dashboard');

            if ($user->status == '0') {
                return $this->sendFailedLoginResponse($request, 'auth.pending_status');
            }
        }else{
            if(request()->mobile_number){
                Session::flash('message', "The mobile number or password that you've entered doesn't match any account.");
                Session::flash('alert-class', 'alert-danger' );
            }
            else if(request()->bmdc_no){
                Session::flash('message', "The BMDC No or password that you've entered doesn't match any account.");
                Session::flash('alert-class', 'alert-danger' );
            }
        }
        return $this->sendFailedLoginResponse($request);
    }

    public function logout(Request $request)
    {
        Auth::guard('doctor')->logout();

        return redirect('/');
    }

}
