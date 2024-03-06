<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DoctorCourseManualPayment extends Model
{
    use SoftDeletes;
    protected $table = 'doctor_course_manual_payment';

    public function course_info()
    {
        return $this->belongsTo(DoctorsCourses::class, 'doctor_course_id', 'id');
    }
    public function doctor()
    {
        return $this->belongsTo('App\Doctors', 'doctor_id', 'id');
    }
    public function doctor_course()
    {
        return $this->belongsTo('App\DoctorsCourses', 'doctor_course_id', 'id');
    }

    public function check_payment_validated()
    {
        $doctor_course_payment = DoctorCoursePayment::where(['doctor_course_id'=>$this->doctor_course_id,'trans_id'=>$this->trans_id,'amount'=>$this->amount])->first();
    
        $this->doctor_course->set_payment_status();
        if($this->doctor_course->payment_status == "Completed" || isset($doctor_course_payment) || $this->payment_validated )
        {
            return true;
        }
        else 
        {
            return false;
        }
    }
}
