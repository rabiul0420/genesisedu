<?php

namespace App\Http\Controllers;

use App\BatchDisciplineFee;
use App\Batches;
use App\BatchesSchedules;
use App\BatchFacultyFee;
use App\BatchSchedules;
use App\DoctorCoursePayment;
use App\Doctors;
use App\DoctorsCourses;
use App\Faculty;
use App\Institutes;
use App\SendSms;
use App\Sessions;
use App\SmsLog;
use App\Subjects;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;
use Symfony\Component\HttpFoundation\RequestStack;
use Illuminate\Support\Str;


class BatchEnrollController extends Controller
{
    use SendSms;

    public function password($mobile_number, $schedule_id)
    {
        if (Doctors::where('mobile_number', $mobile_number)->exists()) {
           $doctor=  Doctors::where('mobile_number', $mobile_number)->first();
            return view('batch_enroll.password', compact('mobile_number', 'schedule_id','doctor'));
        } else {
            return view('batch_enroll.unregister-doctor', compact('mobile_number', 'schedule_id'));
        }
    }



    public function password_submit(Request $request)
    {

        $batch_schedule = BatchesSchedules::find($request->hidden_schedule_id);
        $batch = Batches::where('id', $batch_schedule->batch_id)->first();
        $doctor = Doctors::where('mobile_number', $request->hidden_mobile_number)->first();
        $mobile_number = $request->hidden_mobile_number;
        $schedule_id = $request->hidden_schedule_id;

        if ($doctor->main_password == $request->password) {
            
            if (DoctorsCourses::where(['doctor_id' => $doctor->id,'batch_id'=>$batch->id,'is_trash'=>'0'])->first()) {
                Session::flash('class', 'alert-danger');
                session()->flash('message', 'Dear doctor, you are already filled admission in this batch!!!');
                return redirect('batch');
            }
            
            if ($batch->is_show_lecture_sheet_fee == 'Yes') {
                return view('batch_enroll.lecture_sheet', compact('mobile_number', 'schedule_id'));
            } else {
                return redirect('/lecture-sheet-without-batch' . '/' . $mobile_number . '/' . $schedule_id);
            }
        } else {
            Session::flash('class', 'alert-danger');
            session()->flash('message', 'Please enter your correct password');
            return redirect('/password/' . $mobile_number . '/' . $schedule_id)->withInput();
        }
    }

    public function enroll_now($schedule_id)
    {

        $schedule = BatchesSchedules::find($schedule_id);
        $batch = Batches::where('id', $schedule->batch_id)->first();
        $doctor= Doctors::where('id',Auth::guard('doctor')->id())->first();
        $mobile_number = $doctor->mobile_number;

        if (DoctorsCourses::where(['doctor_id' => $doctor->id,'batch_id'=>$batch->id,'is_trash'=>'0'])->first()) {
            Session::flash('class', 'alert-danger');
            session()->flash('message', 'Dear doctor, you are already filled admission in this batch!!!');
            return redirect('batch');
        }
        
            if ($batch->is_show_lecture_sheet_fee == 'Yes') {
                return view('batch_enroll.lecture_sheet', compact('mobile_number', 'schedule_id'));
            } else {
                return redirect('/lecture-sheet-without-batch' . '/' . $mobile_number . '/' . $schedule_id);
            }
     
    }

    public function lecture_sheet_submit_batch(Request $request)
    {
        $schedule_id = $request->hidden_schedule_id;
        $mobile_number = $request->hidden_mobile_number;
        $include_lecture_sheet = $request->include_lecture_sheet;
        $data = array();
        $data['include_lecture_sheet'] = $request->include_lecture_sheet;
        $data['delivery_status'] = $request->delivery_status;
        $data['courier_address'] = $request->courier_address;
        $data['courier_upazila_id'] = $request->courier_upazila_id;
        $data['courier_district_id'] = $request->courier_district_id;
        $data['courier_division_id'] = $request->courier_division_id; 

        $batch_schedule = BatchesSchedules::find($schedule_id);
        $batch = Batches::with('course')->where('id', $batch_schedule->batch_id)->first();
        $batch_name = $batch->name;
        $course_name = $batch->course->name;
        $doctor = Doctors::where('mobile_number', $mobile_number)->first();
        
        if (Auth::guard('doctor')->check() == true) {

            if (DoctorsCourses::where(['doctor_id' => Auth::guard('doctor')->id(), 'year' => $batch_schedule->year, 'session_id' => $batch_schedule->session_id, 'institute_id' => $batch_schedule->institute_id, 'course_id' => $batch_schedule->course_id, 'batch_id' => $batch_schedule->batch_id, 'payment_status' => 'No Payment', 'faculty_id' => $batch_schedule->faculty_id,'is_trash' => '0'])->exists()) {
                Session::flash('class', 'alert-danger');
                session()->flash('message', 'Dear doctor, you already filled admission form in this faculty!!!');
                return redirect('payment-details')->withInput();
            }

            if (DoctorsCourses::where(['doctor_id' => Auth::guard('doctor')->id(), 'year' => $batch_schedule->year, 'session_id' => $batch_schedule->session_id, 'institute_id' => $batch_schedule->institute_id, 'course_id' => $batch_schedule->course_id, 'batch_id' => $batch_schedule->batch_id, 'payment_status' => 'No Payment', 'subject_id' => $batch_schedule->subject_id,'is_trash' => '0'])->exists()) {
                Session::flash('class', 'alert-danger');
                session()->flash('message', 'Dear doctor, you already filled admission form in this discipline!!!');
                return redirect('payment-details')->withInput();
            }

            if (DoctorsCourses::where(['doctor_id' => Auth::guard('doctor')->id(), 'year' => $batch_schedule->year, 'session_id' => $batch_schedule->session_id, 'institute_id' => $batch_schedule->institute_id, 'course_id' => $batch_schedule->course_id, 'batch_id' => $batch_schedule->batch_id, 'payment_status' => 'Completed', 'faculty_id' => $batch_schedule->faculty_id,'is_trash' => '0'])->exists()) {
                Session::flash('class', 'alert-danger');
                session()->flash('message', 'Dear doctor, you already filled admission form in this faculty!!!');
                return redirect('schedule')->withInput();
            }

            if (DoctorsCourses::where(['doctor_id' => Auth::guard('doctor')->id(), 'year' => $batch_schedule->year, 'session_id' => $batch_schedule->session_id, 'institute_id' => $batch_schedule->institute_id, 'course_id' => $batch_schedule->course_id, 'batch_id' => $batch_schedule->batch_id, 'payment_status' => 'Completed', 'subject_id' => $batch_schedule->subject_id,'is_trash' => '0'])->exists()) {
                Session::flash('class', 'alert-danger');
                session()->flash('message', 'Dear doctor, you already filled admission form in this discipline!!!');
                return redirect('schedule')->withInput();
            }

            if (DoctorsCourses::where(['doctor_id' => Auth::guard('doctor')->id(), 'year' => $batch_schedule->year, 'session_id' => $batch_schedule->session_id, 'institute_id' => $batch_schedule->institute_id, 'course_id' => $batch_schedule->course_id, 'batch_id' => $batch_schedule->batch_id, 'payment_status' => 'No Payment' , 'is_trash' => '0'])->exists()) {
                Session::flash('class', 'alert-danger');
                session()->flash('message', 'Dear doctor, you already filled admission form in this batch!!!');
                return redirect('payment-details')->withInput();
            }

            if (DoctorsCourses::where(['doctor_id' => Auth::guard('doctor')->id(), 'year' => $batch_schedule->year, 'session_id' => $batch_schedule->session_id, 'institute_id' => $batch_schedule->institute_id, 'course_id' => $batch_schedule->course_id, 'batch_id' => $batch_schedule->batch_id, 'payment_status' => 'Completed' ,'is_trash' => '0'])->exists()) {
                Session::flash('class', 'alert-danger');
                session()->flash('message', 'Dear doctor, you already filled admission form in this batch!!!');
                return redirect('schedule')->withInput();
            }

            $doctor_course =  $this->doctor_admission_automatically($schedule_id, $data);

            $alternative_login = 'https://www.genesisedu.info/login-phone';
            $mob = '88' . $doctor->mobile_number;
            $msg = 'Dear Doctor, Thanks for enrollment in '. $batch_name . ' for ' . $course_name . ' preparation. Please pay within 24 hours to ensure your seat in the batch. Also have a look on refund and Batch shifting policy. "https://www.genesisedu.info/refund-policy" Thank you. Stay safe.';     

            
            $this->send_custom_sms($doctor,$msg,'Quick Enroll',false);

            return redirect('payment-details');
        }

        if (Auth::guard('doctor')->check() == false) {

            Auth::guard('doctor')->login($doctor);
            $login_access_token = request()->session()->token();

            $doctor->update([
                'login_access_token' => $login_access_token
            ]);

            if (DoctorsCourses::where(['doctor_id' => Auth::guard('doctor')->id(), 'year' => $batch_schedule->year, 'session_id' => $batch_schedule->session_id, 'institute_id' => $batch_schedule->institute_id, 'course_id' => $batch_schedule->course_id, 'batch_id' => $batch_schedule->batch_id, 'payment_status' => 'No Payment', 'faculty_id' => $batch_schedule->faculty_id,'is_trash' => '0'])->exists()) {
                Session::flash('class', 'alert-danger');
                session()->flash('message', 'Dear doctor, you already filled admission form in this faculty!!!');
                return redirect('payment-details')->withInput();
            }

            if (DoctorsCourses::where(['doctor_id' => Auth::guard('doctor')->id(), 'year' => $batch_schedule->year, 'session_id' => $batch_schedule->session_id, 'institute_id' => $batch_schedule->institute_id, 'course_id' => $batch_schedule->course_id, 'batch_id' => $batch_schedule->batch_id, 'payment_status' => 'No Payment', 'subject_id' => $batch_schedule->subject_id,'is_trash' => '0'])->exists()) {
                Session::flash('class', 'alert-danger');
                session()->flash('message', 'Dear doctor, you already filled admission form in this discipline!!!');
                return redirect('payment-details')->withInput();
            }

            if (DoctorsCourses::where(['doctor_id' => Auth::guard('doctor')->id(), 'year' => $batch_schedule->year, 'session_id' => $batch_schedule->session_id, 'institute_id' => $batch_schedule->institute_id, 'course_id' => $batch_schedule->course_id, 'batch_id' => $batch_schedule->batch_id, 'payment_status' => 'Completed', 'faculty_id' => $batch_schedule->faculty_id,'is_trash' => '0'])->exists()) {
                Session::flash('class', 'alert-danger');
                session()->flash('message', 'Dear doctor, you already filled admission form in this faculty!!!');
                return redirect('schedule')->withInput();
            }

            if (DoctorsCourses::where(['doctor_id' => Auth::guard('doctor')->id(), 'year' => $batch_schedule->year, 'session_id' => $batch_schedule->session_id, 'institute_id' => $batch_schedule->institute_id, 'course_id' => $batch_schedule->course_id, 'batch_id' => $batch_schedule->batch_id, 'payment_status' => 'Completed', 'subject_id' => $batch_schedule->subject_id,'is_trash' => '0'])->exists()) {
                Session::flash('class', 'alert-danger');
                session()->flash('message', 'Dear doctor, you already filled admission form in this discipline!!!');
                return redirect('schedule')->withInput();
            }

            if (DoctorsCourses::where(['doctor_id' => Auth::guard('doctor')->id(), 'year' => $batch_schedule->year, 'session_id' => $batch_schedule->session_id, 'institute_id' => $batch_schedule->institute_id, 'course_id' => $batch_schedule->course_id, 'batch_id' => $batch_schedule->batch_id, 'payment_status' => 'No Payment','is_trash' => '0'])->exists()) {
                Session::flash('class', 'alert-danger');
                session()->flash('message', 'Dear doctor, you already filled admission form in this batch!!!');
                return redirect('payment-details')->withInput();
            }

            if (DoctorsCourses::where(['doctor_id' => Auth::guard('doctor')->id(), 'year' => $batch_schedule->year, 'session_id' => $batch_schedule->session_id, 'institute_id' => $batch_schedule->institute_id, 'course_id' => $batch_schedule->course_id, 'batch_id' => $batch_schedule->batch_id, 'payment_status' => 'Completed','is_trash' => '0'])->exists()) {
                Session::flash('class', 'alert-danger');
                session()->flash('message', 'Dear doctor, you already filled admission form in this batch!!!');
                return redirect('schedule')->withInput();
            }

            $doctor_course =  $this->doctor_admission_automatically($schedule_id, $data);

            $alternative_login = 'https://www.genesisedu.info/login-phone';
            $mob = '88' . $doctor->mobile_number;
            $msg = 'Dear Doctor, Thanks for enrollment in '. $batch_name . ' for ' . $course_name . ' preparation. Please pay within 24 hours to ensure your seat in the batch. Also have a look on refund and Batch shifting policy. "https://www.genesisedu.info/refund-policy" Thank you. Stay safe.';     

            
            $this->send_custom_sms($doctor,$msg,'Quick Enroll',false);

            return redirect('payment-details');
        }
    }

    public function lecture_sheet_without_batch($mobile_number, $schedule_id)
    {
        $batch_schedule = BatchesSchedules::find($schedule_id);
        $batch = Batches::with('course')->where('id', $batch_schedule->batch_id)->first();
        $doctor = Doctors::where('mobile_number', $mobile_number)->first();
        $batch_name = $batch->name;
        $course_name = $batch->course->name;

        if (Auth::guard('doctor')->check() == true) {

            if (DoctorsCourses::where(['doctor_id' => Auth::guard('doctor')->id(), 'year' => $batch_schedule->year, 'session_id' => $batch_schedule->session_id, 'institute_id' => $batch_schedule->institute_id, 'course_id' => $batch_schedule->course_id, 'batch_id' => $batch_schedule->batch_id, 'payment_status' => 'No Payment', 'faculty_id' => $batch_schedule->faculty_id,'is_trash' => '0'])->exists()) {
                Session::flash('class', 'alert-danger');
                session()->flash('message', 'Dear doctor, you are already filled admission in this faculty!!!');
                return redirect('payment-details')->withInput();
            }

            if (DoctorsCourses::where(['doctor_id' => Auth::guard('doctor')->id(), 'year' => $batch_schedule->year, 'session_id' => $batch_schedule->session_id, 'institute_id' => $batch_schedule->institute_id, 'course_id' => $batch_schedule->course_id, 'batch_id' => $batch_schedule->batch_id, 'payment_status' => 'No Payment', 'subject_id' => $batch_schedule->subject_id,'is_trash' => '0'])->exists()) {
                Session::flash('class', 'alert-danger');
                session()->flash('message', 'Dear doctor, you are already filled admission in this discipline!!!');
                return redirect('payment-details')->withInput();
            }

            if (DoctorsCourses::where(['doctor_id' => Auth::guard('doctor')->id(), 'year' => $batch_schedule->year, 'session_id' => $batch_schedule->session_id, 'institute_id' => $batch_schedule->institute_id, 'course_id' => $batch_schedule->course_id, 'batch_id' => $batch_schedule->batch_id, 'payment_status' => 'Completed', 'faculty_id' => $batch_schedule->faculty_id,'is_trash' => '0'])->exists()) {
                Session::flash('class', 'alert-danger');
                session()->flash('message', 'Dear doctor, you are already filled admission in this faculty!!!');
                return redirect('schedule')->withInput();
            }

            if (DoctorsCourses::where(['doctor_id' => Auth::guard('doctor')->id(), 'year' => $batch_schedule->year, 'session_id' => $batch_schedule->session_id, 'institute_id' => $batch_schedule->institute_id, 'course_id' => $batch_schedule->course_id, 'batch_id' => $batch_schedule->batch_id, 'payment_status' => 'Completed', 'subject_id' => $batch_schedule->subject_id,'is_trash' => '0'])->exists()) {
                Session::flash('class', 'alert-danger');
                session()->flash('message', 'Dear doctor, you are already filled admission in this discipline!!!');
                return redirect('schedule')->withInput();
            }


            if (DoctorsCourses::where(['doctor_id' => Auth::guard('doctor')->id(), 'year' => $batch_schedule->year, 'session_id' => $batch_schedule->session_id, 'institute_id' => $batch_schedule->institute_id, 'course_id' => $batch_schedule->course_id, 'batch_id' => $batch_schedule->batch_id, 'payment_status' => 'No Payment','is_trash' => '0'])->exists()) {
                Session::flash('class', 'alert-danger');
                session()->flash('message', 'Dear doctor, you are already filled admission in this batch!!!');
                return redirect('payment-details')->withInput();
            }

            if (DoctorsCourses::where(['doctor_id' => Auth::guard('doctor')->id(), 'year' => $batch_schedule->year, 'session_id' => $batch_schedule->session_id, 'institute_id' => $batch_schedule->institute_id, 'course_id' => $batch_schedule->course_id, 'batch_id' => $batch_schedule->batch_id, 'payment_status' => 'Completed','is_trash' => '0'])->exists()) {
                Session::flash('class', 'alert-danger');
                session()->flash('message', 'Dear doctor, you are already filled admission in this batch!!!');
                return redirect('schedule')->withInput();
            }

            $doctor_course =  $this->doctor_admission_automatically($schedule_id);
            $alternative_login = 'https://www.genesisedu.info/login-phone';
            $mob = '88' . $doctor->mobile_number;
            $msg = 'Dear Doctor, Thanks for enrollment in '. $batch_name . ' for ' . $course_name . ' preparation. Please pay within 24 hours to ensure your seat in the batch. Also have a look on refund and Batch shifting policy. "https://www.genesisedu.info/refund-policy" Thank you. Stay safe.' ;     

            
            $this->send_custom_sms($doctor,$msg,'Quick Enroll',false);
            return redirect('payment-details');
        }

        if (Auth::guard('doctor')->check() == false) {

            Auth::guard('doctor')->login($doctor);
            $login_access_token = request()->session()->token();

            $doctor->update([
                'login_access_token' => $login_access_token
            ]);

            if (DoctorsCourses::where(['doctor_id' => Auth::guard('doctor')->id(), 'year' => $batch_schedule->year, 'session_id' => $batch_schedule->session_id, 'institute_id' => $batch_schedule->institute_id, 'course_id' => $batch_schedule->course_id, 'batch_id' => $batch_schedule->batch_id, 'payment_status' => 'No Payment', 'faculty_id' => $batch_schedule->faculty_id,'is_trash' => '0'])->exists()) {
                Session::flash('class', 'alert-danger');
                session()->flash('message', 'Dear doctor, you are already filled admission in this faculty!!!');
                return redirect('payment-details')->withInput();
            }

            if (DoctorsCourses::where(['doctor_id' => Auth::guard('doctor')->id(), 'year' => $batch_schedule->year, 'session_id' => $batch_schedule->session_id, 'institute_id' => $batch_schedule->institute_id, 'course_id' => $batch_schedule->course_id, 'batch_id' => $batch_schedule->batch_id, 'payment_status' => 'No Payment', 'subject_id' => $batch_schedule->subject_id,'is_trash' => '0'])->exists()) {
                Session::flash('class', 'alert-danger');
                session()->flash('message', 'Dear doctor, you are already filled admission in this discipline!!!');
                return redirect('payment-details')->withInput();
            }

            if (DoctorsCourses::where(['doctor_id' => Auth::guard('doctor')->id(), 'year' => $batch_schedule->year, 'session_id' => $batch_schedule->session_id, 'institute_id' => $batch_schedule->institute_id, 'course_id' => $batch_schedule->course_id, 'batch_id' => $batch_schedule->batch_id, 'payment_status' => 'Completed', 'faculty_id' => $batch_schedule->faculty_id,'is_trash' => '0'])->exists()) {
                Session::flash('class', 'alert-danger');
                session()->flash('message', 'Dear doctor, you are already filled admission in this faculty!!!');
                return redirect('schedule')->withInput();
            }

            if (DoctorsCourses::where(['doctor_id' => Auth::guard('doctor')->id(), 'year' => $batch_schedule->year, 'session_id' => $batch_schedule->session_id, 'institute_id' => $batch_schedule->institute_id, 'course_id' => $batch_schedule->course_id, 'batch_id' => $batch_schedule->batch_id, 'payment_status' => 'Completed', 'subject_id' => $batch_schedule->subject_id,'is_trash' => '0'])->exists()) {
                Session::flash('class', 'alert-danger');
                session()->flash('message', 'Dear doctor, you are already filled admission in this discipline!!!');
                return redirect('schedule')->withInput();
            }

            if (DoctorsCourses::where(['doctor_id' => Auth::guard('doctor')->id(), 'year' => $batch_schedule->year, 'session_id' => $batch_schedule->session_id, 'institute_id' => $batch_schedule->institute_id, 'course_id' => $batch_schedule->course_id, 'batch_id' => $batch_schedule->batch_id, 'payment_status' => 'No Payment','is_trash' => '0'])->exists()) {
                Session::flash('class', 'alert-danger');
                session()->flash('message', 'Dear doctor, you are already filled admission in this batch!!!');
                return redirect('payment-details')->withInput();
            }

            if (DoctorsCourses::where(['doctor_id' => Auth::guard('doctor')->id(), 'year' => $batch_schedule->year, 'session_id' => $batch_schedule->session_id, 'institute_id' => $batch_schedule->institute_id, 'course_id' => $batch_schedule->course_id, 'batch_id' => $batch_schedule->batch_id, 'payment_status' => 'Completed','is_trash' => '0'])->exists()) {
                Session::flash('class', 'alert-danger');
                session()->flash('message', 'Dear doctor, you are already filled admission in this batch!!!');
                return redirect('schedule')->withInput();
            }

            $doctor_course =  $this->doctor_admission_automatically($schedule_id);
            $alternative_login = 'https://www.genesisedu.info/login-phone';
            $mob = '88' . $doctor->mobile_number;
            $msg ='Dear Doctor, Thanks for enrollment in '. $batch_name . ' for ' . $course_name . ' preparation. Please pay within 24 hours to ensure your seat in the batch. Also have a look on refund and Batch shifting policy. "https://www.genesisedu.info/refund-policy" Thank you. Stay safe.';     

            
            $this->send_custom_sms($doctor,$msg,'Quick Enroll',false);
            return redirect('payment-details');
        }
    }

    public function doctor_admission_automatically($schedule_id, $array_lecture_sheet = null)
    {

        $batch_schedule = BatchesSchedules::with('batch')->find($schedule_id);

        if($batch_schedule->batch){

            $doctor_course = new DoctorsCourses();
            $doctor_course->doctor_id = Auth::guard('doctor')->id();
            $doctor_course->institute_id =  $batch_schedule->batch->institute_id;
            $doctor_course->course_id =  $batch_schedule->batch->course_id;
            $doctor_course->session_id =  $batch_schedule->batch->session_id;
            $doctor_course->batch_id =  $batch_schedule->batch->id;
            $doctor_course->year =  $batch_schedule->batch->year;
            $doctor_course->branch_id =  $batch_schedule->batch->branch_id;

            if(is_array($array_lecture_sheet))
            {
                $include_lecture_sheet =  $array_lecture_sheet['include_lecture_sheet'];
                if ($array_lecture_sheet['include_lecture_sheet'] == 1) {
                    $doctor_course->include_lecture_sheet = $array_lecture_sheet['include_lecture_sheet'];
                } else {
                    $doctor_course->include_lecture_sheet = 0;
                }

                $doctor_course->include_lecture_sheet = $array_lecture_sheet['include_lecture_sheet'];
                $doctor_course->delivery_status = $array_lecture_sheet['delivery_status'];
                $doctor_course->courier_address = $array_lecture_sheet['courier_address'];
                $doctor_course->courier_upazila_id = $array_lecture_sheet['courier_upazila_id'];
                $doctor_course->courier_district_id = $array_lecture_sheet['courier_district_id'];
                $doctor_course->courier_division_id = $array_lecture_sheet['courier_division_id'];

            }
            else
            {
                $include_lecture_sheet = '';
                $doctor_course->include_lecture_sheet = 0;
            }
            
            $doctor_course->faculty_id =  $batch_schedule->faculty_id;
            $doctor_course->subject_id =  $batch_schedule->subject_id;
            $doctor_course->bcps_subject_id =  $batch_schedule->bcps_subject_id;
        
            $registration_no = $this->registration_no($batch_schedule);

            $doctor_course->reg_no = $registration_no['reg_no_first_part'] . $registration_no['reg_no_last_part'];

            $doctor_course->reg_no_first_part =  $registration_no['reg_no_first_part'];
            $doctor_course->reg_no_last_part =  $registration_no['reg_no_last_part'];
            $reg_no_last_part_int = (int)$registration_no['reg_no_last_part'];
            $doctor_course->reg_no_last_part_int = $reg_no_last_part_int;


            $doctor_course_fee = $this->doctor_course_discount($schedule_id, $doctor_course, $include_lecture_sheet);

            if (isset($doctor_course_fee) && $doctor_course_fee == "batch_admission_fee_not_set") {
                Session::flash('class', 'alert-danger');
                session()->flash('message', 'The selected batch Admission Fee type is not set yet...!!!');
                return redirect()->action('DoctorsAdmissionsController@doctor_admissions')->withInput();
            } else if (isset($doctor_course_fee) && $doctor_course_fee == "faculty_admission_fee_not_set") {
                Session::flash('class', 'alert-danger');
                session()->flash('message', 'The selected faculty admission fee is not set yet...!!!');
                return redirect()->action('DoctorsAdmissionsController@doctor_admissions')->withInput();
            } else if (isset($doctor_course_fee) && $doctor_course_fee == "discipline_admission_fee_not_set") {
                Session::flash('class', 'alert-danger');
                session()->flash('message', 'The selected discipline admission fee is not set yet...!!!');
                return redirect()->action('DoctorsAdmissionsController@doctor_admissions')->withInput();
            } else {
                $actual_course_price = $doctor_course_fee->actual_course_price;
                $course_price = $doctor_course_fee->course_price;
                $discount_old_student = $doctor_course_fee->discount_old_student;
            }

            $doctor_course->actual_course_price = $actual_course_price;
            $doctor_course->course_price = $course_price;
            $doctor_course->discount_old_student = $discount_old_student;

            $doctor_course->payment_status = "No Payment";

            $fee_type = Batches::where('id', $batch_schedule->batch_id)->value('fee_type');
            $admission_fee = Batches::where('id', $batch_schedule->batch_id)->value('admission_fee');

            if ($fee_type == 'Batch' && $admission_fee == 0) {
                $doctor_course->payment_status = "Completed";
            }

            $doctor_course->save();
            $doctor_course->set_payment_status();

            if($doctor_course->batch->payment_times > 1)
            {
                $doctor_course->payment_option = "default";
                $doctor_course->push();
            }

            return $doctor_course;
        }
    }

    public function registration_no($batch_schedule)
    {

        $YEAR = $batch_schedule->year;
        $year = substr($YEAR, -2);

        $session = Sessions::where('id', $batch_schedule->session_id)->pluck('session_code');
        $capacity = Batches::where('id', $batch_schedule->batch_id)->where('year', $YEAR)->value('capacity');

        $message = '';

        $reg_no_first_part = $year . $session[0];

        $doctor_course = DoctorsCourses::where(['reg_no_first_part' => $reg_no_first_part, 'is_trash' => '0'])->orderBy('reg_no_last_part_int', 'desc')->first();
        $reg_no_last_part = (isset($doctor_course->reg_no_last_part_int)) ? str_pad($doctor_course->reg_no_last_part_int + 1, 5, "0", STR_PAD_LEFT) : str_pad(1, 5, "0", STR_PAD_LEFT);

        $count_batch = DoctorsCourses::where(['year' => $YEAR, 'session_id' => $batch_schedule->session_id, 'batch_id' => $batch_schedule->batch_id, 'course_id' => $batch_schedule->course_id, 'is_trash' => '0'])->count();


        if ($count_batch >= $capacity) {
            $message = '<span style="color:red;">Dear Dr. , The batch you tried is filled up... please try another batch !!!</span>';
        }
        return  [
            'reg_no_first_part' => $reg_no_first_part,
            'reg_no_last_part' => $reg_no_last_part,
            'message' => $message,
            'is_lecture_sheet' => Batches::where('id', $batch_schedule->batch_id)->value('is_show_lecture_sheet_fee'),
        ];
    }


    public function doctor_course_discount($schedule_id, $doctor_course, $include_lecture_sheet = null)
    {
        $batch_schedule = BatchesSchedules::find($schedule_id);
        $doctor_selected_batch = Batches::where(['id' => $batch_schedule->batch_id])->first();
        if (isset($doctor_selected_batch->fee_type) == false) {
            return "batch_admission_fee_not_set";
        }

        if ($doctor_selected_batch->fee_type == "Batch") {
            if ($include_lecture_sheet == 1) {

                if (DoctorsCourses::where(['doctor_id' => Auth::guard('doctor')->id(), 'batches.batch_type' => 'Regular', 'payment_status' => 'Completed', 'is_trash' => '0'])->where('course_price', '!=' , '0')->join('batches', 'doctors_courses.batch_id', '=', 'batches.id')->first()) {
                    $doctor_course->course_price = $doctor_selected_batch->admission_fee - $doctor_selected_batch->discount_from_regular + $doctor_selected_batch->lecture_sheet_fee;
                    $doctor_course->discount_old_student = 'Yes';
                } else if (DoctorsCourses::where(['doctor_id' => Auth::guard('doctor')->id(), 'batches.batch_type' => 'Exam', 'payment_status' => 'Completed', 'is_trash' => '0'])->where('course_price', '!=' , '0')->join('batches', 'doctors_courses.batch_id', '=', 'batches.id')->first()) {
                    $doctor_course->course_price = $doctor_selected_batch->admission_fee - $doctor_selected_batch->discount_from_exam + $doctor_selected_batch->lecture_sheet_fee;
                    $doctor_course->discount_old_student = 'Yes';
                } else {
                    $doctor_course->course_price = $doctor_selected_batch->admission_fee + $doctor_selected_batch->lecture_sheet_fee;
                    $doctor_course->discount_old_student = 'No';
                }
            } else {
                if (DoctorsCourses::where(['doctor_id' => Auth::guard('doctor')->id(), 'batches.batch_type' => 'Regular', 'payment_status' => 'Completed', 'is_trash' => '0'])->where('course_price', '!=' , '0')->join('batches', 'doctors_courses.batch_id', '=', 'batches.id')->first()) {
                    $doctor_course->course_price = $doctor_selected_batch->admission_fee - $doctor_selected_batch->discount_from_regular;
                    $doctor_course->discount_old_student = 'Yes';
                } else if (DoctorsCourses::where(['doctor_id' => Auth::guard('doctor')->id(), 'batches.batch_type' => 'Exam', 'payment_status' => 'Completed', 'is_trash' => '0'])->where('course_price', '!=' , '0')->join('batches', 'doctors_courses.batch_id', '=', 'batches.id')->first()) {
                    $doctor_course->course_price = $doctor_selected_batch->admission_fee - $doctor_selected_batch->discount_from_exam;
                    $doctor_course->discount_old_student = 'Yes';
                } else {
                    $doctor_course->course_price = $doctor_selected_batch->admission_fee;
                    $doctor_course->discount_old_student = 'No';
                }
            }

            $doctor_course->actual_course_price = $doctor_selected_batch->admission_fee;
        } else if ($doctor_selected_batch->fee_type == "Discipline_Or_Faculty") {
            //echo $doctor_selected_batch->institute->type;exit;
            if ($doctor_selected_batch->institute->type == 1) {

                $doctor_selected_batch_faculty = BatchFacultyFee::where(['batch_id' => $batch_schedule->batch_id, 'faculty_id' => $batch_schedule->faculty_id])->first();
                if (isset($doctor_selected_batch_faculty->admission_fee) == false) {
                    return "faculty_admission_fee_not_set";
                }
                if ($include_lecture_sheet == 1) {

                    if (DoctorsCourses::where(['doctor_id' => Auth::guard('doctor')->id(), 'batches.batch_type' => 'Regular', 'payment_status' => 'Completed', 'is_trash' => '0'])->where('course_price', '!=' , '0')->join('batches', 'doctors_courses.batch_id', '=', 'batches.id')->first()) {
                        $doctor_course->course_price = $doctor_selected_batch_faculty->admission_fee - $doctor_selected_batch_faculty->discount_from_regular + $doctor_selected_batch_faculty->lecture_sheet_fee;
                        $doctor_course->discount_old_student = 'Yes';
                    } else if (DoctorsCourses::where(['doctor_id' => Auth::guard('doctor')->id(), 'batches.batch_type' => 'Exam', 'payment_status' => 'Completed', 'is_trash' => '0'])->where('course_price', '!=' , '0')->join('batches', 'doctors_courses.batch_id', '=', 'batches.id')->first()) {
                        $doctor_course->course_price = $doctor_selected_batch_faculty->admission_fee - $doctor_selected_batch_faculty->discount_from_exam + $doctor_selected_batch_faculty->lecture_sheet_fee;
                        $doctor_course->discount_old_student = 'Yes';
                    } else {
                        $doctor_course->course_price = $doctor_selected_batch_faculty->admission_fee + $doctor_selected_batch_faculty->lecture_sheet_fee;
                        $doctor_course->discount_old_student = 'No';
                    }
                } else {
                    if (DoctorsCourses::where(['doctor_id' => Auth::guard('doctor')->id(), 'batches.batch_type' => 'Regular', 'payment_status' => 'Completed', 'is_trash' => '0'])->where('course_price', '!=' , '0')->join('batches', 'doctors_courses.batch_id', '=', 'batches.id')->first()) {
                        $doctor_course->course_price = $doctor_selected_batch_faculty->admission_fee - $doctor_selected_batch_faculty->discount_from_regular;
                        $doctor_course->discount_old_student = 'Yes';
                    } else if (DoctorsCourses::where(['doctor_id' => Auth::guard('doctor')->id(), 'batches.batch_type' => 'Exam', 'payment_status' => 'Completed', 'is_trash' => '0'])->where('course_price', '!=' , '0')->join('batches', 'doctors_courses.batch_id', '=', 'batches.id')->first()) {
                        $doctor_course->course_price = $doctor_selected_batch_faculty->admission_fee - $doctor_selected_batch_faculty->discount_from_exam;
                        $doctor_course->discount_old_student = 'Yes';
                    } else {
                        $doctor_course->course_price = $doctor_selected_batch_faculty->admission_fee;
                        $doctor_course->discount_old_student = 'No';
                    }
                }

                $doctor_course->actual_course_price = $doctor_selected_batch_faculty->admission_fee;
            } else {

                $doctor_selected_batch_discipline = BatchDisciplineFee::where(['batch_id' => $batch_schedule->batch_id, 'subject_id' => $batch_schedule->subject_id])->first();
                if (isset($doctor_selected_batch_discipline->admission_fee) == false) {
                    return "discipline_admission_fee_not_set";
                }

                if ($include_lecture_sheet == 1) {
                    if (DoctorsCourses::where(['doctor_id' => Auth::guard('doctor')->id(), 'batches.batch_type' => 'Regular', 'payment_status' => 'Completed', 'is_trash' => '0'])->where('course_price', '!=' , '0')->join('batches', 'doctors_courses.batch_id', '=', 'batches.id')->first()) {
                        $doctor_course->course_price = $doctor_selected_batch_discipline->admission_fee - $doctor_selected_batch_discipline->discount_from_regular +  $doctor_selected_batch_discipline->lecture_sheet_fee;
                        $doctor_course->discount_old_student = 'Yes';
                    } else if (DoctorsCourses::where(['doctor_id' => Auth::guard('doctor')->id(), 'batches.batch_type' => 'Exam', 'payment_status' => 'Completed', 'is_trash' => '0'])->where('course_price', '!=' , '0')->join('batches', 'doctors_courses.batch_id', '=', 'batches.id')->first()) {
                        $doctor_course->course_price = $doctor_selected_batch_discipline->admission_fee - $doctor_selected_batch_discipline->discount_from_exam +  $doctor_selected_batch_discipline->lecture_sheet_fee;
                        $doctor_course->discount_old_student = 'Yes';
                    } else {
                        $doctor_course->course_price = $doctor_selected_batch_discipline->admission_fee +  $doctor_selected_batch_discipline->lecture_sheet_fee;
                        $doctor_course->discount_old_student = 'No';
                    }
                } else {
                    if (DoctorsCourses::where(['doctor_id' => Auth::guard('doctor')->id(), 'batches.batch_type' => 'Regular', 'payment_status' => 'Completed', 'is_trash' => '0'])->where('course_price', '!=' , '0')->join('batches', 'doctors_courses.batch_id', '=', 'batches.id')->first()) {
                        $doctor_course->course_price = $doctor_selected_batch_discipline->admission_fee - $doctor_selected_batch_discipline->discount_from_regular;
                        $doctor_course->discount_old_student = 'Yes';
                    } else if (DoctorsCourses::where(['doctor_id' => Auth::guard('doctor')->id(), 'batches.batch_type' => 'Exam', 'payment_status' => 'Completed', 'is_trash' => '0'])->where('course_price', '!=' , '0')->join('batches', 'doctors_courses.batch_id', '=', 'batches.id')->first()) {
                        $doctor_course->course_price = $doctor_selected_batch_discipline->admission_fee - $doctor_selected_batch_discipline->discount_from_exam;
                        $doctor_course->discount_old_student = 'Yes';
                    } else {
                        $doctor_course->course_price = $doctor_selected_batch_discipline->admission_fee;
                        $doctor_course->discount_old_student = 'No';
                    }
                }

                $doctor_course->actual_course_price = $doctor_selected_batch_discipline->admission_fee;
            }
        }

        return $doctor_course;
    }

    public function get_payment_status($doctor_course)
    {
        $doctor_course = DoctorsCourses::where(['id' => $doctor_course->id])->first();
        if ($this->calculate_payable($doctor_course) != 0 && $this->calculate_payable($doctor_course) == $doctor_course->course_price) {
            return "No Payment";
        } else if ($this->calculate_payable($doctor_course) <= 0) {
            return "Completed";
        } else {
            return "In Progress";
        }
    }

    public function calculate_payable($doctor_course)
    {
        $total_payment = 0;
        if (DoctorCoursePayment::where(['doctor_course_id' => $doctor_course->id])->first()) {
            $doctor_course_payments = DoctorCoursePayment::where(['doctor_course_id' => $doctor_course->id])->get();
            foreach ($doctor_course_payments as $doctor_course_payment) {
                $total_payment += $doctor_course_payment->amount;
            }
        }

        return $doctor_course->course_price - $total_payment;
    }


    public function register_name(Request $request)
    {
        $schedule_id = $request->hidden_schedule_id;
        $mobile_number = $request->hidden_mobile_number;

        if (Doctors::where('mobile_number', $request->hidden_mobile_number)->exists()) {
            Session::flash('class', 'alert-danger');
            session()->flash('message', 'Doctors are already exists');
            return redirect('batch')->withInput();
        }
        $doctor = new Doctors();
        $doctor->name = $request->name;
        $doctor->mobile_number = $request->hidden_mobile_number;
        $doctor->bmdc_no = NULL;
        $password = $this->generatePassword();
        $doctor->main_password = $password;
        $doctor->save();
        $doctor_id = $doctor->id;
        $alternative_login = 'https://www.genesisedu.info/login-phone';
        $mob = '88' . $doctor->mobile_number;
        $msg = 'Dear doctor,'. $doctor->main_password . ' is your password . Please click alternative login or click ' . $alternative_login;
        $this->send_custom_sms($doctor,$msg,'Quick Registration');
        //$this->sendMessage($doctor_id);
        return view('batch_enroll.password', compact('mobile_number', 'schedule_id','doctor'));
        //   return view('batch_enroll.unregister-doctor-password',compact('schedule_id','mobile_number','doctor_id'));
    }

    protected function sendMessage($doctor_id)
    {

        $smsLog = new SmsLog();
        $response = null;

        $doctor = Doctors::where('id', $doctor_id)->first();

        $alternative_login = 'https://www.genesisedu.info/login-phone';
        $mob = '88' . $doctor->mobile_number;
        $msg = 'Dear doctor,'. $doctor->main_password . ' is your password . Please click alternative login or click ' . $alternative_login;

        // $ch = curl_init();
        // curl_setopt($ch, CURLOPT_URL, "http://sms4.digitalsynapsebd.com/api/mt/SendSMS?user=genesispg&password=123321@12&senderid=8809612440402&channel=Normal&DCS=0&flashsms=0&number=$mob&text=$msg");
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // $response = curl_exec($ch);
        // curl_close($ch);
        // $smsLog->set_response($response, $doctor->id)->set_event('Quick Registration')->save();
        $this->send_custom_sms($doctor,$msg,'Quick Registration',$isAdmin = false); 
    }


    protected function sendMessageadmission($batch_name,$course_name,$doctor)
    {

        $smsLog = new SmsLog();
        $response = null;

        $doctor = Doctors::where('id', $doctor)->first();

        $alternative_login = 'https://www.genesisedu.info/login-phone';
        $mob = '88' . $doctor->mobile_number;
        $msg = 'Dear Doctor, Thanks for enrollment in '. $batch_name . ' for ' . $course_name . ' preparation. Please pay within 24 hours to ensure your seat in the batch. Also have a look on refund and Batch shifting policy. "https://www.genesisedu.info/refund-policy" Thank you. Stay safe.';     

        // $ch = curl_init();
        // curl_setopt($ch, CURLOPT_URL, "http://sms4.digitalsynapsebd.com/api/mt/SendSMS?user=genesispg&password=123321@12&senderid=8809612440402&channel=Normal&DCS=0&flashsms=0&number=$mob&text=$msg");
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // $response = curl_exec($ch);
        // curl_close($ch);
        // $smsLog->set_response($response, $doctor->id)->set_event('Quick Enroll')->save();
        $this->send_custom_sms($doctor,$msg,'Quick Enroll',$isAdmin = false); 
    }



    public function generatePassword()
    {
        $password = strtoupper(Str::random(6));

        $strings = "ABDEFGHLMNPQRTY";
        $numbers = "123456789";

        $hashOrStar = "gns";

        $str_length = strlen($strings);
        $num_length = strlen($numbers);

        $output = "";

        $modes = 'shn';

        $mds = "";

        $char_added = 0;
        $number_added = 0;
        $hash_added = 0;


        $index = 0;


        while (($char_added + $number_added + $hash_added) < 6) {

            $mode = $modes[rand(0, strlen($modes) - 1)];
            $mds .= $mode;

            switch ($mode) {
                case 's':
                    $ind = rand(0, $str_length - 1);
                    $output .= $strings[$ind];
                    $char_added++;
                    $index++;
                    break;
                case 'n':
                    $ind = rand(0, $num_length - 1);
                    $output .= $numbers[$ind];
                    $number_added++;
                    $index++;
                case 'h':
                    if ($hash_added < 1 && $index > 0 && $index < 6) {
                        $output .= $hashOrStar[rand(0, 2)];
                        $hash_added++;
                        $index++;
                    }
            }
        }

        return $output;
    }

    public function discipline_terms_condition(Request $request)
    {
        $data['doctor_course'] =  DoctorsCourses::with('course', 'faculty')->where(['batch_id' => $request->batch_id, 'doctor_id' => $request->doctor_id])->first();
        $doctor_course = $data['doctor_course'];
        $data['schedule'] = BatchesSchedules::where('id', $request->schedule_id)->first();
        
        if ($doctor_course->subject_id == NULL) {
            
            $institute = Institutes::where('id', $doctor_course->institute_id)->first();
            $data['institute_type'] = $institute->type;
            $data['is_combined'] = ($doctor_course) && $doctor_course->course && $doctor_course->course->isCombined();

            if ($data['institute_type'] == 1) {

                $data['subjects'] = Subjects::where('faculty_id', $doctor_course->faculty_id)->pluck('name', 'id');
                if ($data['is_combined']) {
                    $data['faculties'] =  $doctor_course->course->combined_faculties()->pluck('name', 'id');
                    $data['bcps_subjects'] = $doctor_course->course->combined_disciplines()->pluck('name', 'id');
                } else {
                    $data['faculties'] = Faculty::where('course_id', $doctor_course->course_id)->pluck('name', 'id');
                }
            } else {
                $data['subjects'] = Subjects::where('course_id', $doctor_course->course_id)->pluck('name', 'id');
            }

            return response(['view' => view('ajax.faculty_discipline_cadidate', $data)->render(), 'isset_faculty_discipline' => false]);
        } else {

            return response([
                'view' => view('ajax.terms_condition', $data)->render(),
                'isset_faculty_discipline' => true,
                'reload' => 'new-schedule/' . $request->schedule_id.'/'.$request->doctor_course_id,
                'title' => 'Terms And Conditions',
                'doctor_course_id' => $request->doctor_course_id,
            ]);
        }
    }

    public function doctor_course_information_update(Request $request)
    {
        $doctor_course =  DoctorsCourses::find($request->doctor_course_id);

        if ($doctor_course) {

            $doctor_course->subject_id = $request->subject_id;

            if ($request->candidate_type != null) {
                $doctor_course->candidate_type = $request->candidate_type;
            }
            if ($doctor_course->bcps_subject_id != null) {
                $doctor_course->bcps_subject_id = $request->bcps_subject_id;
            }
            $doctor_course->push();
        }


        $data['schedule'] = BatchesSchedules::where('id', $request->schedule_id)->first();
        $data['doctor_course_id'] = $request->doctor_course_id;

        return response(
            [
                'success' => true,
                'terms_and_condition' =>  view('ajax.terms_condition', $data)->render(),
                'title' => 'Terms And Conditions',
                'doctor_course_id' =>$request->doctor_course_id,
            ]
        );
    }

    public function password_request_from_available_batch($schedule_id){
        return view('batch_enroll.password_request',compact('schedule_id'));
    }


    public function password_submit_from_available_batch(Request $request){
        $smsLog = new SmsLog();
        $response = null;

        $doctor = Doctors::where( 'mobile_number', $request->phone_number )->where('status',1)->first();

        if( !$doctor ) {
            return back()->with(['alert-class'=>'alert-danger','message' => 'This Mobile number not Registered.']);
        }

        $mob = '88'.$request->phone_number;
        $msg = "Dear Doctor, Your User ID: " . $doctor->bmdc_no . ", Password: " . $doctor->main_password . ".  Please login at " . url('https://www.genesisedu.info');
       // $msg = str_replace(' ', '%20', $msg);
        // $ch = curl_init();
        // curl_setopt($ch, CURLOPT_URL, "http://sms4.digitalsynapsebd.com/api/mt/SendSMS?user=genesispg&password=123321@12&senderid=8809612440402&channel=Normal&DCS=0&flashsms=0&number=$mob&text=$msg");
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        // $response = curl_exec($ch);
        // curl_close($ch);
        // $smsLog->set_response( $response,$doctor->id)->set_event('Forget Password')->save();
        $this->send_custom_sms($doctor,$msg,'Forget Password',false);
        
        return redirect('/password/' . $request->phone_number . '/' . $request->schedule_id)->with(['alert-class'=>'alert-success', 'message' => 'Your User ID & Password sent Successfully.']);
    }

}
