
<?php

use App\Http\Controllers\Controller;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Hash;
// use App\DoctorComplain;
// use App\DoctorComplainReply;
// use App\Doctors;
// use Illuminate\Support\Facades\DB;
// use Session;
// use Auth;
// use Validator;
// use Yajra\Datatables\Datatables;


class DoctorOfTheDay_PhotoController extends Controller
{
    

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //Auth::loginUsingId(1);
        //$this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('doctor_of_the_day');
    }




}
