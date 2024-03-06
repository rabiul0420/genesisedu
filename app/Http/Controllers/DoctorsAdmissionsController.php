<?php

namespace App\Http\Controllers;

use App\BatchFacultyFee;
use App\Coming_by;
use App\ComingBy;
use App\Http\Controllers\Controller;

use App\MedicalColleges;
use App\Providers\AppServiceProvider;
use Illuminate\Http\Request;

use App\Institutes;
use App\Courses;
use App\Faculty;
use App\Subjects;
use App\Batches;
use App\Branch;
use App\Doctors;
use App\Sessions;
use App\DoctorCoursePayment;
use App\BatchDisciplineFee;
use App\ServicePackages;
use App\DoctorsCourses;
use Session;
use Auth;
use Validator;
use App\SmsLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use App\BatchesSchedules;
use App\SendSms;
use phpDocumentor\Reflection\Types\Null_;

class DoctorsAdmissionsController extends Controller
{
    use SendSms;
    //

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //Auth::loginUsingId(1);
        $this->middleware('auth:doctor');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['doctors_courses'] = DoctorsCourses::where('doctor_id',Auth::guard('doctor')->id())->get();
        $data['title'] = 'SIF Doctor : Doctors Courses List';
        return view('doctors_courses.list',$data);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function doctor_admissions()
    {
        $data['doc_info'] = Doctors::where('id', Auth::guard('doctor')->id())->first();
        $data['years'] = array(''=>'Select year');
        for($year = date("Y")+1;$year>=2020;$year--){
            $data['years'][$year] = $year;
        }

        $data['title'] = 'SIF Doctor : Doctor Courses Create';
        $data['branches'] = Branch::where('status',1)->pluck('name', 'id');
        $data['institutes'] = Institutes::where('status', 1)->pluck('name', 'id');

        return view('doctor_admissions',$data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function doctor_admission_submit(Request $request)
    {
        $validator = Validator::make($request->all(), [
//            'year' => ['required'],
            'session_id' => ['required'],
            'branch_id' => ['required'],
            'institute_id' => ['required'],
            'course_id' => ['required'],
            'subject_id' => ['required'],
            'batch_id' => ['required'],
        ]);

        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            session()->flash('message','Please enter proper input values!!!');
            return redirect()->action('DoctorsAdmissionsController@doctor_admission_submit')->withInput();
        }

        $YEAR = Batches::where( 'id', $request->batch_id )->value( 'year' );

        $doctor_course = new DoctorsCourses();
        $doctor_course->doctor_id =  Auth::guard('doctor')->id();
//        $doctor_course->year = $request->year;
        $doctor_course->year = $YEAR;


//        dd( $request->faculty_id, $request->subject_id, $request->bcps_subject_id);


        $doctor_course->branch_id = $request->branch_id;
        $doctor_course->session_id = $request->session_id;
        $doctor_course->institute_id = $request->institute_id;
        $doctor_course->course_id = $request->course_id;
        $doctor_course->faculty_id = $request->faculty_id;
        $doctor_course->subject_id = $request->subject_id;
        $doctor_course->bcps_subject_id = $request->bcps_subject_id;
        $doctor_course->batch_id = $request->batch_id;
        $doctor_course->candidate_type = $request->candidate_type ?? '';
        $doctor_course->discount_code = $request->discount_code;
        $doctor_course->include_lecture_sheet = $request->include_lecture_sheet;
        $doctor_course->delivery_status = $request->delivery_status;
        $doctor_course->courier_address = $request->courier_address;
        $doctor_course->courier_upazila_id = $request->courier_upazila_id;
        $doctor_course->courier_district_id = $request->courier_district_id;
        $doctor_course->courier_division_id = $request->courier_division_id;       

        $doctor_course->payment_status = "No Payment";

        $fee_type = Batches::where('id',$request->batch_id)->value('fee_type');
        $admission_fee = Batches::where('id',$request->batch_id)->value('admission_fee');

        if( $fee_type == 'Batch' && $admission_fee == 0 ) {
            $doctor_course->payment_status = "Completed";
        }

       $reg_no_last_part_int = DoctorsCourses::where('reg_no_first_part',$request->reg_no_first_part)->orderBy('reg_no_last_part_int','desc')->value('reg_no_last_part_int');
       
       $reg_no_last_part_int = ($reg_no_last_part_int!=null)?$reg_no_last_part_int+1:1;

       $reg_no_last_part = str_pad($reg_no_last_part_int,5,"0",STR_PAD_LEFT);
      

        
        $doctor_course->reg_no = $request->reg_no_first_part.$reg_no_last_part;
        $doctor_course->reg_no_first_part = $request->reg_no_first_part;
        $doctor_course->reg_no_last_part = $reg_no_last_part;
        $doctor_course->reg_no_last_part_int = $reg_no_last_part_int;

        $capacity = Batches::where('id',$request->batch_id)->value('capacity');



        if (DoctorsCourses::where(['doctor_id'=>Auth::guard('doctor')->id(),'year'=>$YEAR,'session_id'=>$request->session_id,'institute_id'=>$request->institute_id,'course_id'=>$request->course_id,'batch_id'=>$request->batch_id,'is_trash'=>'0'])->exists()){
            Session::flash('class', 'alert-danger');
            session()->flash('message','Dear doctor, you are already filled admission form or registered for this course!!!');
            return redirect()->action('DoctorsAdmissionsController@doctor_admissions')->withInput();
        }


/*
        if(DoctorsCourses::where(['reg_no'=>$request->reg_no_first_part.$request->reg_no_last_part])->exists()){
            Session::flash('class', 'alert-danger');
            session()->flash('message','This Registration No already exists');
            return redirect()->action('DoctorsAdmissionsController@doctor_admissions')->withInput();
        }*/


        $doctor_course->save();

        $doctor_course->set_payment_status();
        if($doctor_course->batch->payment_times > 1)
        {
            $doctor_course->payment_option = "default";
            $doctor_course->push();
        }        
        
        Session::flash('status', 'Doctor Admitted successfully');


        if( $doctor_course)
        {
            $this->sendMessage($doctor_course,$request) ;
        }

        return redirect('payment-details');


    }

    protected function sendMessage( $doctor_course,$request){
        
        $smsLog = new SmsLog();
        $response = null;

        $doc=Auth::guard('doctor')->id();
        $doctor=Doctors::where('id',$doc)->first();
        $doctor_selected_batch = Batches::where(['id'=>$request->batch_id])->first();
        $doctor_course=Courses::where(['id'=>$request->course_id])->first();
        $websitename='https://www.genesisedu.info/';
        $mob = '88' . $doctor->mobile_number;
        $msg = 'Dear Doctor, Thanks for enrollment in ' .$doctor_selected_batch->name. ' batch for '.$doctor_course->name. ' preparation. Please pay within 24 hours to ensure your seat in the batch. Also have a look on refund and Batch shifting policy. "https://www.genesisedu.info/refund-policy" Thank you. Stay safe.';

        // $ch = curl_init();
        // curl_setopt($ch, CURLOPT_URL, "http://sms4.digitalsynapsebd.com/api/mt/SendSMS?user=genesispg&password=123321@12&senderid=8809612440402&channel=Normal&DCS=0&flashsms=0&number=$mob&text=$msg");
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        // $response = curl_exec($ch);
        // curl_close($ch);
        // $smsLog->set_response( $response,$doctor->id)->set_event('Admission')->save();
        $this->send_custom_sms($doctor,$msg,'Admission',$isAdmin = false); 

    }

    public function doctor_course_discount(Request $request,$doctor_course)
    {
        $doctor_selected_batch = Batches::where(['id'=>$request->batch_id])->first();
        //echo "<pre>";print_r($doctor_selected_batch);exit;
        //$doctor_selected_batch = $batch;
        if(isset($doctor_selected_batch->fee_type)==false)
        {
            return "batch_admission_fee_not_set";
        }

        if($doctor_selected_batch->fee_type == "Batch")
        {

            if($request->include_lecture_sheet)
            {

                if(DoctorsCourses::where(['doctor_id'=>Auth::guard('doctor')->id(),'batches.batch_type'=>'Regular','payment_status'=>'Completed','is_trash'=>'0'])->where('course_price', '!=' , '0')->join('batches','doctors_courses.batch_id','=','batches.id')->first())
                {
                    $doctor_course->course_price = $doctor_selected_batch->admission_fee - $doctor_selected_batch->discount_from_regular + $doctor_selected_batch->lecture_sheet_fee;
                    $doctor_course->discount_old_student = 'Yes';
                }
                else if(DoctorsCourses::where(['doctor_id'=>Auth::guard('doctor')->id(),'batches.batch_type'=>'Exam','payment_status'=>'Completed','is_trash'=>'0'])->where('course_price', '!=' , '0')->join('batches','doctors_courses.batch_id','=','batches.id')->first())
                {
                    $doctor_course->course_price = $doctor_selected_batch->admission_fee - $doctor_selected_batch->discount_from_exam + $doctor_selected_batch->lecture_sheet_fee;
                    $doctor_course->discount_old_student = 'Yes';

                }
                else
                {
                    $doctor_course->course_price = $doctor_selected_batch->admission_fee + $doctor_selected_batch->lecture_sheet_fee;
                    $doctor_course->discount_old_student = 'No';

                }

            }
            else
            {
                if(DoctorsCourses::where(['doctor_id'=>Auth::guard('doctor')->id(),'batches.batch_type'=>'Regular','payment_status'=>'Completed','is_trash'=>'0'])->where('course_price', '!=' , '0')->join('batches','doctors_courses.batch_id','=','batches.id')->first())
                {
                    $doctor_course->course_price = $doctor_selected_batch->admission_fee - $doctor_selected_batch->discount_from_regular;
                    $doctor_course->discount_old_student = 'Yes';

                }
                else if(DoctorsCourses::where(['doctor_id'=>Auth::guard('doctor')->id(),'batches.batch_type'=>'Exam','payment_status'=>'Completed','is_trash'=>'0'])->where('course_price', '!=' , '0')->join('batches','doctors_courses.batch_id','=','batches.id')->first())
                {
                    $doctor_course->course_price = $doctor_selected_batch->admission_fee - $doctor_selected_batch->discount_from_exam;
                    $doctor_course->discount_old_student = 'Yes';

                }
                else
                {
                    $doctor_course->course_price = $doctor_selected_batch->admission_fee;
                    $doctor_course->discount_old_student = 'No';

                }

            }

            $doctor_course->actual_course_price = $doctor_selected_batch->admission_fee;

        }
        else if($doctor_selected_batch->fee_type == "Discipline_Or_Faculty")
        {
            //echo $doctor_selected_batch->institute->type;exit;
            if($doctor_selected_batch->institute->type == 1)
            {

                $doctor_selected_batch_faculty = BatchFacultyFee::where(['batch_id'=>$request->batch_id,'faculty_id'=>$request->faculty_id])->first();
                if(isset($doctor_selected_batch_faculty->admission_fee)==false)
                {
                    return "faculty_admission_fee_not_set";
                }

                if($request->include_lecture_sheet)
                {
                    if(DoctorsCourses::where(['doctor_id'=>Auth::guard('doctor')->id(),'batches.batch_type'=>'Regular','payment_status'=>'Completed','is_trash'=>'0'])->where('course_price', '!=' , '0')->join('batches','doctors_courses.batch_id','=','batches.id')->first())
                    {
                        $doctor_course->course_price = $doctor_selected_batch_faculty->admission_fee - $doctor_selected_batch_faculty->discount_from_regular + $doctor_selected_batch_faculty->lecture_sheet_fee;
                        $doctor_course->discount_old_student = 'Yes';

                    }
                    else if(DoctorsCourses::where(['doctor_id'=>Auth::guard('doctor')->id(),'batches.batch_type'=>'Exam','payment_status'=>'Completed','is_trash'=>'0'])->where('course_price', '!=' , '0')->join('batches','doctors_courses.batch_id','=','batches.id')->first())
                    {
                        $doctor_course->course_price = $doctor_selected_batch_faculty->admission_fee - $doctor_selected_batch_faculty->discount_from_exam + $doctor_selected_batch_faculty->lecture_sheet_fee;
                        $doctor_course->discount_old_student = 'Yes';

                    }
                    else
                    {
                        $doctor_course->course_price = $doctor_selected_batch_faculty->admission_fee + $doctor_selected_batch_faculty->lecture_sheet_fee;
                        $doctor_course->discount_old_student = 'No';


                    }
                }
                else
                {
                    if(DoctorsCourses::where(['doctor_id'=>Auth::guard('doctor')->id(),'batches.batch_type'=>'Regular','payment_status'=>'Completed','is_trash'=>'0'])->where('course_price', '!=' , '0')->join('batches','doctors_courses.batch_id','=','batches.id')->first())
                    {
                        $doctor_course->course_price = $doctor_selected_batch_faculty->admission_fee - $doctor_selected_batch_faculty->discount_from_regular;
                        $doctor_course->discount_old_student = 'Yes';

                    }
                    else if(DoctorsCourses::where(['doctor_id'=>Auth::guard('doctor')->id(),'batches.batch_type'=>'Exam','payment_status'=>'Completed','is_trash'=>'0'])->where('course_price', '!=' , '0')->join('batches','doctors_courses.batch_id','=','batches.id')->first())
                    {
                        $doctor_course->course_price = $doctor_selected_batch_faculty->admission_fee - $doctor_selected_batch_faculty->discount_from_exam;
                        $doctor_course->discount_old_student = 'Yes';

                    }
                    else
                    {
                        $doctor_course->course_price = $doctor_selected_batch_faculty->admission_fee;
                        $doctor_course->discount_old_student = 'No';


                    }

                }

                $doctor_course->actual_course_price = $doctor_selected_batch_faculty->admission_fee;

            }
            else
            {

                $doctor_selected_batch_discipline = BatchDisciplineFee::where(['batch_id'=>$request->batch_id,'subject_id'=>$request->subject_id])->first();
                if(isset($doctor_selected_batch_discipline->admission_fee)==false)
                {
                    return "discipline_admission_fee_not_set";
                }

                if($request->include_lecture_sheet)
                {
                    if(DoctorsCourses::where(['doctor_id'=>Auth::guard('doctor')->id(),'batches.batch_type'=>'Regular','payment_status'=>'Completed','is_trash'=>'0'])->where('course_price', '!=' , '0')->join('batches','doctors_courses.batch_id','=','batches.id')->first())
                    {
                        $doctor_course->course_price = $doctor_selected_batch_discipline->admission_fee - $doctor_selected_batch_discipline->discount_from_regular +  $doctor_selected_batch_discipline->lecture_sheet_fee;
                        $doctor_course->discount_old_student = 'Yes';

                    }
                    else if(DoctorsCourses::where(['doctor_id'=>Auth::guard('doctor')->id(),'batches.batch_type'=>'Exam','payment_status'=>'Completed','is_trash'=>'0'])->where('course_price', '!=' , '0')->join('batches','doctors_courses.batch_id','=','batches.id')->first())
                    {
                        $doctor_course->course_price = $doctor_selected_batch_discipline->admission_fee - $doctor_selected_batch_discipline->discount_from_exam +  $doctor_selected_batch_discipline->lecture_sheet_fee;
                        $doctor_course->discount_old_student = 'Yes';

                    }
                    else
                    {
                        $doctor_course->course_price = $doctor_selected_batch_discipline->admission_fee +  $doctor_selected_batch_discipline->lecture_sheet_fee;
                        $doctor_course->discount_old_student = 'No';


                    }
                }
                else
                {
                    if(DoctorsCourses::where(['doctor_id'=>Auth::guard('doctor')->id(),'batches.batch_type'=>'Regular','payment_status'=>'Completed','is_trash'=>'0'])->where('course_price', '!=' , '0')->join('batches','doctors_courses.batch_id','=','batches.id')->first())
                    {
                        $doctor_course->course_price = $doctor_selected_batch_discipline->admission_fee - $doctor_selected_batch_discipline->discount_from_regular;
                        $doctor_course->discount_old_student = 'Yes';

                    }
                    else if(DoctorsCourses::where(['doctor_id'=>Auth::guard('doctor')->id(),'batches.batch_type'=>'Exam','payment_status'=>'Completed','is_trash'=>'0'])->where('course_price', '!=' , '0')->join('batches','doctors_courses.batch_id','=','batches.id')->first())
                    {
                        $doctor_course->course_price = $doctor_selected_batch_discipline->admission_fee - $doctor_selected_batch_discipline->discount_from_exam;
                        $doctor_course->discount_old_student = 'Yes';

                    }
                    else
                    {
                        $doctor_course->course_price = $doctor_selected_batch_discipline->admission_fee;
                        $doctor_course->discount_old_student = 'No';


                    }

                }

                $doctor_course->actual_course_price = $doctor_selected_batch_discipline->admission_fee;

            }




        }

        return $doctor_course;

    }

    public function calculate_payable($doctor_course)
    {
        $total_payment = 0;
        if(DoctorCoursePayment::where(['doctor_course_id'=>$doctor_course->id])->first())
        {
            $doctor_course_payments = DoctorCoursePayment::where(['doctor_course_id'=>$doctor_course->id])->get();
            foreach ($doctor_course_payments as $doctor_course_payment)
            {
                $total_payment += $doctor_course_payment->amount;
            }

        }

        return $doctor_course->course_price - $total_payment;

    }

    public function get_payment_status($doctor_course)
    {
        $doctor_course = DoctorsCourses::where(['id'=>$doctor_course->id])->first();
        if($this->calculate_payable($doctor_course) != 0 && $this->calculate_payable($doctor_course) == $doctor_course->course_price)
        {
            return "No Payment";
        }
        else if($this->calculate_payable($doctor_course) <= 0)
        {
            return "Completed";
        }
        else
        {
            return "In Progress";
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit_doctor_course_discipline($doctor_course_id)
    {
        $data['doc_info'] = Doctors::where('id', Auth::guard('doctor')->id())->first();
        $data['doctor_course'] = DoctorsCourses::with('course')->where('id',$doctor_course_id)->first();
        $doctor_course = $data['doctor_course'];
        
        //echo '<pre>';print_r($doctor_course);exit;

        $data['institutes'] = Institutes::get()->pluck('name', 'id');

        $data['courses'] = Courses::where('institute_id',$doctor_course->course->institute->id)->pluck('name', 'id');

        $institute = Institutes::where('id',$doctor_course->institute_id)->first();
        $data[ 'institute_type' ] = $institute->type;
        $data[ 'is_combined' ] = ($doctor_course) && $doctor_course->course && $doctor_course->course->isCombined();

        if( $data['institute_type'] == 1 ){

            $data['subjects'] = Subjects::where('faculty_id',$doctor_course->faculty_id)->pluck('name', 'id');

            if( $data[ 'is_combined' ]  ) {
                $data['faculties'] =  $doctor_course->course->combined_faculties( )->pluck( 'name', 'id' );
                $data['bcps_subjects'] = $doctor_course->course->combined_disciplines( )->pluck( 'name', 'id' );
            }else {
                $data['faculties'] = Faculty::where('course_id',$doctor_course->course_id)->pluck('name', 'id');
            }

        } else {
            $data['subjects'] = Subjects::where('course_id',$doctor_course->course_id)->pluck('name', 'id');
        }

        //echo '<pre>';print_r($data['subjects']);exit;

        $data['title'] = 'SIF Doctor : Doctor Courses Descipline Edit';


        return view('edit_doctor_course_discipline',$data);
    }

    public function edit_doctor_course_candidate($doctor_course_id)
    {
        $data['doc_info'] = Doctors::where('id', Auth::guard('doctor')->id())->first();
        $data['doctor_course'] = DoctorsCourses::find($doctor_course_id);
        $data['title'] = 'SIF Doctor : Doctor Courses Descipline Edit';


        return view('edit_doctor_course_candidate',$data);
    }

    public function update_doctor_course_candidate(Request $request)
    {
        $table = DoctorsCourses::find($request->doctor_course_id);
        $table->candidate_type = $request->candidate_type;
        $table->push();

        Session::flash('message', 'Record has been updated successfully');
        return redirect()->action('DoctorsAdmissionsController@edit_doctor_course_candidate',[$request->doctor_course_id]);

    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update_doctor_course_discipline(Request $request)
    {

        $validationData = [
            'doctor_course_id' => ['required'],
            'institute_id' => ['required'],
            'course_id' => ['required'],
        ];

        if( $request->institute_id == AppServiceProvider::$COMBINED_INSTITUTE_ID) {
            $validationData[ 'bcps_subject_id' ] = ['required'];
        }

        $validator = Validator::make($request->all(), $validationData);

        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            session()->flash('message','Please enter proper input values!!!');
            return redirect()->action('DoctorsAdmissionsController@edit_doctor_course_discipline',[$request->doctor_course_id])->withInput();
        }

        $doctor_course = DoctorsCourses::find($request->doctor_course_id);
        $doctor_course->institute_id = $request->institute_id;
        $doctor_course->course_id = $request->course_id;
        $doctor_course->faculty_id = $request->faculty_id;
        $doctor_course->subject_id = $request->subject_id;
        $doctor_course->bcps_subject_id = $request->bcps_subject_id;
        $doctor_course->is_discipline_changed = '1';

        $doctor_course->updated_by = Auth::guard('doctor')->id();

        $doctor_course->push();

        Session::flash('message', 'Record has been updated successfully');

        return redirect()->action('DoctorsAdmissionsController@edit_doctor_course_discipline',[$request->doctor_course_id]);



    }







    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        /* $user=DoctorsCourses::find(Auth::guard('doctor')->id());

         if(!$user->hasRole('Admin')){
             return abort(404);
         }*/


         $data['years'] = array(''=>'Select year');
         for($year = date("Y")+1;$year>=2017;$year--){
             $data['years'][$year] = $year;
         }

         $doctor_course = DoctorsCourses::find($id);

         //echo "<pre>";print_r($doctor_course);exit;

         $data['title'] = 'SIF Doctor : Doctors Courses Edit';
         $data['doctor'] = Doctors::where('id',$doctor_course->doctor_id)->first();
         $data['doctor_course'] = $doctor_course;
         $data['sessions'] = Sessions::get()->pluck('name', 'id');
         $data['service_packages'] = ServicePackages::get()->pluck('name', 'id');
         $data['coming_bys'] = ComingBy::get()->pluck('name', 'id');
         $data['branches'] = Branch::pluck('name', 'id');
         $data['institutes'] = Institutes::get()->pluck('name', 'id');

         $institute_type = Institutes::where('id',$doctor_course->institute_id)->first()->type;
         Session(['institute_type'=> $institute_type]);
         //$data['url']  = ($institute_type)?'branches-courses-faculties-batches':'branches-courses-subjects-batches';
         $data['url']  = ($institute_type)?'course-sessions-faculties':'course-sessions-subjects';
         $data['institute_type']= $institute_type;

         $data['courses'] = Courses::get()->where('institute_id',$doctor_course->institute_id)->pluck('name', 'id');

         if($data['institute_type']==1){
             $data['faculties'] = Faculty::where('course_id',$doctor_course->course_id)->pluck('name', 'id');
             $data['subjects'] = Subjects::where('faculty_id',$doctor_course->faculty_id)->pluck('name', 'id');
         }else{
             $data['subjects'] = Subjects::where('course_id',$doctor_course->course_id)->pluck('name', 'id');
         }

         $data['batches'] = Batches::get()->where('institute_id',$doctor_course->institute_id)
             ->where('course_id',$doctor_course->course_id)
             ->where('branch_id',$doctor_course->branch_id)
             ->pluck('name', 'id');

         $start_index = Batches::where('id',$doctor_course->batch_id)->value('start_index');
         $end_index = Batches::where('id',$doctor_course->batch_id)->value('end_index');
         $data['range'] = 'Batch Range : ( '.$start_index.' - '.$end_index.' ) ';
         /*echo '<pre>';
         print_r($data);exit;*/

        return view('doctors_courses.edit', $data);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $validator = Validator::make($request->all(), [
            'doctor_id' => ['required'],
            /*'service_package_id' => ['required'],*/
            //'coming_by_id' => ['required'],
            'year' => ['required'],
            'session_id' => ['required'],
            'branch_id' => ['required'],
            'institute_id' => ['required'],
            'course_id' => ['required'],
            'batch_id' => ['required'],
            'reg_no_first_part' => ['required'],
            //'reg_no_last_part' => ['required'],
            //'status' => ['required']
        ]);

        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            session()->flash('message','Please enter proper input values!!!');
            return redirect()->action('DoctorsCoursesController@edit',[$id])->withInput();
        }

        $start_index = Batches::where('id',$request->batch_id)->pluck('start_index');
        $end_index = Batches::where('id',$request->batch_id)->pluck('end_index');

        $doctor_course = DoctorsCourses::find($id);

        if($doctor_course->branch_id != $request->branch_id)
        {
            if(Batches::where(['branch_id'=>$request->branch_id,'id'=>$request->batch_id])->first() === null)
            {
                Session::flash('class', 'alert-danger');
                session()->flash('message','This Batch does not exist in the selected Branch !!!');
                return redirect()->action('DoctorsCoursesController@edit',[$id])->withInput();

            }

        }

        if($doctor_course->doctor_id != $request->doctor_id || $doctor_course->year != $request->year || $doctor_course->session_id != $request->session_id || $doctor_course->institute_id != $request->institute_id || $doctor_course->course_id != $request->course_id){

            if (DoctorsCourses::where(['doctor_id'=>$request->doctor_id,'year'=>$request->year,'session_id'=>$request->session_id,'institute_id'=>$request->institute_id,'course_id'=>$request->course_id])->exists()){
                Session::flash('class', 'alert-danger');
                session()->flash('message','Dear doctor, you are already filled admission form or registered for this course!!!');
                return redirect()->action('DoctorsCoursesController@edit',[$id])->withInput();
            }

        }

        $doctor_course->doctor_id = $request->doctor_id;
        $doctor_course->service_package_id = $request->service_package_id;
        $doctor_course->coming_by_id = $request->coming_by_id;
        $doctor_course->year = $request->year;
        $doctor_course->session_id = $request->session_id;
        $doctor_course->branch_id = $request->branch_id;
        $doctor_course->institute_id = $request->institute_id;
        $doctor_course->course_id = $request->course_id;
        $doctor_course->faculty_id = $request->faculty_id;
        $doctor_course->subject_id = $request->subject_id;
        $doctor_course->batch_id = $request->batch_id;
        $doctor_course->bcps_subject_id = $request->bcps_subject_id;
        //$doctor_course->reg_no = $request->reg_no_first_part.$request->reg_no_last_part;
        $doctor_course->reg_no_first_part = $request->reg_no_first_part;
        //$doctor_course->reg_no_last_part = $request->reg_no_last_part;

        $doctor_course->status = $request->status;
        $doctor_course->created_by = Auth::guard('doctor')->id();

        $doctor_course->push();

        Session::flash('message', 'Record has been updated successfully');

        return redirect()->action('DoctorsCoursesController@edit',[$id]);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        /*$user=DoctorsCourses::find(Auth::guard('doctor')->id());

        if(!$user->hasRole('Admin')){
            return abort(404);
        }*/

        //DoctorsCourses::destroy($id); // 1 way
        Session::flash('message', 'Record has been deleted successfully');
        return redirect()->action('DoctorsCoursesController@index');
    }

}
