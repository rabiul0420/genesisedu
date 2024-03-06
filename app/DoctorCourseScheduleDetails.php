<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DoctorCourseScheduleDetails extends Model
{
    use  SoftDeletes;
    protected $table = 'doctor_course_schedule_details';
    public $timestamps = false;

    public function doctor_course()
    {
        return $this->belongsTo('App\DoctorsCourses','doctor_course_id','id');
    }

    public function schedule_details()
    {
        return $this->belongsTo('App\ScheduleDetails','schedule_details_id','id');
    }

    
    
}
