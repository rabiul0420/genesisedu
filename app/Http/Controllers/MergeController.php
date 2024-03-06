<?php

namespace App\Http\Controllers;

use App\Exam_question;
use App\Permission;
use App\Permission_copy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Doctors;
use App\Courses;
use App\DoctorsCourses;
use App\Faculty;
use App\LectureVideoBatch;
use App\LectureVideoBatchLectureVideo;
use App\OnlineLectureLink;
use App\OnlineExamBatch;
use App\OnlineExamBatchOnlineExam;
use App\OnlineExamLink;
use App\OnlineExamLink_old;
use App\Batches;
use App\Question;


class MergeController extends Controller
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

    public function course()
    {

        $users = DoctorsCourses::where(['year'=>"2022",'course_id'=>"19",'session_id'=>" 8",'batch_id'=>"566"])->update(['session_id'=>'16']);
        

        $users = DoctorsCourses::where(['year'=>"2022",'course_id'=>"19",'session_id'=>" 8",'batch_id'=>"572"])->update(['session_id'=>'16']);
        

        $users = DoctorsCourses::where(['year'=>"2022",'course_id'=>"19",'session_id'=>" 8",'batch_id'=>"579"])->update(['session_id'=>'16']);
        dd($users);exit;

        $coures = Courses::select('*')
            ->get();

        foreach ($coures as $row){
            DoctorsCourses::where('course_i', $row->course_code)
                ->update(['course_id' => $row->id]);
        }

        echo 'success';exit;


        $doctors = DB::table('sif_admission')
            ->groupBy('bmdc_no')
            ->get();


        foreach ($doctors as $row){
            Doctors::insert([
                'name'=>$row->doc_name,
                'date_of_birth'=>$row->dob,
                'bmdc_no'=>$row->bmdc_no,
                'password'=>$row->password,
                'main_password'=>bcrypt($row->password),
                'mobile_number'=>$row->phone,
                'email'=>$row->email,
                'medical_college_id'=>$row->medical_col,
                'blood_group'=>$row->blood_gro,
                'facebook_id'=>$row->f_id,
                'father_name'=>$row->father_name,
                'mother_name'=>$row->mother_name,
                'spouse_name'=>$row->spouse_name,
                'job_description'=>$row->job_des,
                'nid'=>$row->n_id,
                'passport'=>$row->passport,
                'permanent_division_id'=>$row->per_divi,
                'permanent_district_id'=>$row->per_dist,
                'permanent_upazila_id'=>$row->per_thana,
                'permanent_address'=>$row->per_address,
                'present_division_id'=>$row->mai_divi,
                'present_district_id'=>$row->mai_dist,
                'present_upazila_id'=>$row->mai_thana,
                'present_address'=>$row->mai_address,
                'photo'=>$row->photo,
                'sign'=>$row->sign,
                'created_at'=>$row->created_at,
            ]);
        }





        echo 'thh';





        $faculty = Faculty::select('*')
            ->get();

        foreach ($faculty as $row){
            DoctorsCourses::where('faculty_id', $row->faculty_code)
                ->update(['faculty_id' => $row->id]);
        }

        exit;
        $coures = Courses::select('*')
            ->get();

        foreach ($coures as $row){
            DoctorsCourses::where('course_id', $row->course_code)
                ->update(['course_id' => $row->id]);
        }

        exit;

        $doctors = DB::table('sif_admission')
            ->get();

        foreach ($doctors as $row){
            DoctorsCourses::insert([
                'reg_no'=>$row->reg_no,
                'reg_type'=>$row->reg_type,
                'institute_id'=>$row->institute,
                'course_id'=>$row->course_code,
                'faculty_id'=>$row->faculty_code,
                'subject_id'=>$row->subject,
                'batch_id'=>$row->batch_code,
                'year'=>$row->year,
                'session_id'=>$row->session,
                'service_package_id'=>$row->service_pack_id,
                'admission_type'=>$row->admi_type,
                'coming_by_id'=>$row->answer_type,
                'created_at'=>$row->created_at,
                'bmdc_no'=>$row->bmdc_no,
            ]);
        }

        echo 'thh';
    }

    public function Update_doctor_id()
    {
        $doctors = Doctors::select('id','bmdc_no')
            ->get();
        foreach ($doctors as $row){
            DoctorsCourses::where('bmdc_no', $row->bmdc_no)
                ->update(['doctor_id' => $row->id]);
        }
    }


    public function course_code_batch_code()
    {
        $coures = Courses::select('*')
            ->get();

        foreach ($coures as $row){
            DoctorsCourses::where('course_i', $row->course_code)
                ->update(['course_id' => $row->id]);
        }

        $faculty = Faculty::select('*')
            ->get();

        foreach ($faculty as $row){
            DoctorsCourses::where('faculty_id', $row->faculty_code)
                ->update(['faculty_id' => $row->id]);
        }

    }


    public function questions()
    {

        $faculty = Faculty::select('*')
            ->get();

        foreach ($faculty as $row){
            DoctorsCourses::where('faculty_id', $row->faculty_code)
                ->update(['faculty_id' => $row->id]);
        }

        /*$doctors = Doctors::select('id','bmdc_no')
            ->get();
        foreach ($doctors as $row){
            DoctorsCourses::where('bmdc_no', $row->bmdc_no)
                ->update(['doctor_id' => $row->id]);
        }*/

    }



}
