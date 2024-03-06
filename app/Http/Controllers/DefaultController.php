<?php

namespace App\Http\Controllers;

use App\DoctorsCourses;
use App\SendSms;
use App\Video;
use Illuminate\Http\Request;

class DefaultController extends Controller
{
    use SendSms;
    public function sms_to_installment_due_list()
    {
        $doctors_courses = DoctorsCourses::with('batch')->where(['is_trash'=>'0'])
                                        ->where('payment_option','!=','single')
                                        ->where('payment_status','!=','Completed')
                                        ->whereHas('batch', function($q){
                                            $q->where('payment_times', '>' , 1);
                                        })
                                        ->get()
                                        ;
        if(isset($doctors_courses) && count($doctors_courses))
        {
            $today = new \DateTime("now", new \DateTimeZone('Asia/Dhaka'));
            $today = $today->getTimestamp();
            $custom_doctor_courses = array();
            foreach($doctors_courses as $k=>$doctor_course)
            {
                if($doctor_course->next_installment_last_date())
                {
                    $payment_date = date_create_from_format("Y-m-d",$doctor_course->next_installment_last_date())->getTimestamp();
                    $diff = $payment_date - $today;
                    //if($doctor_course->id == "60370"){echo "<pre>";print_r($doctor_course->next_installment_last_date());exit;}
                    if($diff == 15 * 24 * 3600)
                    {
                        $custom_doctor_courses[] = $doctor_course;
                        $msg = 'Dear Doctor, '.'Please pay your next installment for batch '.$doctor_course->batch->name.', reg no : '.$doctor_course->reg_no.' before '.$doctor_course->next_installment_last_date().' . Thank you, GENESIS.';
                        $this->send_custom_sms($doctor_course->doctor,$msg,'Installment Due Auto Reminder',$isAdmin = false); 
                    }
                    else if($diff == 7 * 24 * 3600)
                    {
                        $custom_doctor_courses[] = $doctor_course;
                        $msg = 'Dear Doctor, '.'Please pay your next installment for batch '.$doctor_course->batch->name.', reg no : '.$doctor_course->reg_no.' before '.$doctor_course->next_installment_last_date().' . Thank you, GENESIS.';
                        $this->send_custom_sms($doctor_course->doctor,$msg,'Installment Due Auto Reminder',$isAdmin = false);
                    }
                    else if($diff == 3 * 24 * 3600)
                    {
                        $custom_doctor_courses[] = $doctor_course;
                        $msg = 'Dear Doctor, '.'Please pay your next installment for batch '.$doctor_course->batch->name.', reg no : '.$doctor_course->reg_no.' before '.$doctor_course->next_installment_last_date().' . Thank you, GENESIS.';
                        $this->send_custom_sms($doctor_course->doctor,$msg,'Installment Due Auto Reminder',$isAdmin = false);
                    }
                    else if($diff == 1 * 24 * 3600)
                    {
                        $custom_doctor_courses[] = $doctor_course;
                        $msg = 'Dear Doctor, '.'Please pay your next installment for batch '.$doctor_course->batch->name.', reg no : '.$doctor_course->reg_no.' before '.$doctor_course->next_installment_last_date().' . Thank you, GENESIS.';
                        $this->send_custom_sms($doctor_course->doctor,$msg,'Installment Due Auto Reminder',$isAdmin = false);
                    }
                    else if($diff >= 0 && $diff <= 1 * 24 * 3600)
                    {
                        $custom_doctor_courses[] = $doctor_course;
                        $msg = 'Dear Doctor, '.'Please pay your next installment for batch '.$doctor_course->batch->name.', reg no : '.$doctor_course->reg_no.' before '.$doctor_course->next_installment_last_date().' . Thank you, GENESIS.';
                        $this->send_custom_sms($doctor_course->doctor,$msg,'Installment Due Auto Reminder',$isAdmin = false);
                    }                
                }
            }
            $data['doctors_courses'] = $custom_doctor_courses;
        }

        //echo "<pre>";print_r($doctors_courses);exit;

        $data['title'] = "Installment Due Auto Reminder";

        return 'success';
        return view('admin.doctors_courses.installment_payment.installment_due_reminder',$data);

    }
}
