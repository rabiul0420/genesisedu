<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DoctorAsks extends Model
{
    protected $table = 'doctor_asks';

    public function doctor()
    {
        return $this->belongsTo('App\Doctors','doctor_id','id');
    }
	
	public function doctorname()
    {
        return $this->belongsTo('App\Doctors','doctor_id','id');
    }

    public function doctor_course()
    {
        return $this->belongsTo('App\DoctorsCourses','doctor_course_id','id');
    }

    public function coursename()
    {
        return $this->belongsTo('App\Courses','doctor_course_id','id');
    }

    public function videoname()
    {
        return $this->belongsTo('App\LectureVideo','lecture_video_id','id');
    }

    public function video()
    {
        return $this->belongsTo('App\LectureVideo','lecture_video_id','id');
    }

}
