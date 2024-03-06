<?php

namespace App\Http\Controllers;

use App\Exam_question;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Doctors;
use App\Courses;
use App\DoctorsCourses;
use App\Faculty;
use App\LectureVideoBatch;
use App\LectureVideoBatchLectureVideo;
use App\OnlineLectureLink;
use App\Question;


class TrashController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
       // $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('home');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function trash_doctors_courses()
    {
        $current_date = Carbon::now();
        $date = Carbon::parse($current_date)->addDays(-1);
        DoctorsCourses::whereDate('created_at','<=',$date)->where('payment_status','No Payment')->update(['is_trash' => 1]);

    }



}
