<?php

namespace App\Http\Controllers\Auth;

use App\Doctors;
use App\DoctorsReviews;
use App\User;
use App\Http\Controllers\Controller;
use App\MedicalColleges;
use App\AvailableBatches;
use App\Advertisements;
use App\Photos;
use App\Courses;
use Carbon\Carbon;
use App\Result;
use App\BannerSlider;
use App\ExamBatchExam;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:doctors',
            'password' => 'required|string|min:6|confirmed',
        ]);
    }

    public function showRegistrationForm()
    {
        date_default_timezone_set('Asia/Dhaka');
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

        $data['medical_colleges'] = MedicalColleges::orderBy('name')->pluck('name','id');
        $data['doctors_reviews'] = DoctorsReviews::get();
        $data['unique_courses'] = array_unique(json_decode(AvailableBatches::pluck('course_name')));
        $data['advertisements'] = Advertisements::get();
        $data['photos'] = Photos::get();
        $data['courses'] = Courses::where('status',1)->orderBy('priority', 'asc')->get();
        $data['bannerSliders'] = BannerSlider::where('status', 1)->orderBy('priority', 'desc')->take(10)->get();
        $data['batches'] = AvailableBatches::orderBy('id', '')->get();
        //return view('home',$data);

        $data['batches'] = AvailableBatches::selectRaw("`id`,`course_name`,`course_type`,`batch_name`,`start_date`,`days`,`time`,`status`,`link`,`priority`,`batch_id`,`links`")
            ->where('status', 1)->with('batch')
            ->orderBy('id', 'desc')
            ->paginate(5);

        $data['all_batches'] = AvailableBatches::selectRaw("`id`,`course_name`,`course_type`,`batch_name`,`start_date`,`days`,`time`,`status`,`link`,`priority`,`batch_id`,`links`")
            ->where('status', 1)->with('batch')
            ->orderBy('id', 'desc')
            ->get();
        return view('home',$data);


    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        return Doctors::insert([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),

        ]);
    }

    
}
