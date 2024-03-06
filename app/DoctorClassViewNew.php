<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class DoctorClassViewNew extends Model
{

    protected $table = 'doctor_class_views';
    
    public function lecture_video()
    {
        return $this->belongsTo('App\LectureVideo','lecture_video_id', 'id');
    }
    public function lecture_video_name()
    {
        return $this->lecture_video()->name??'';
    }
    public function teacher()  
    {
        return $this->lecture_video()->teacher->name??'';
    }
    public function doctor_course()
    { 
        return $this->belongsTo('App\DoctorsCourses','doctor_course_id', 'id');
    }

    public function doctor()
    { 
        return $this->doctor_course()->doctor->name??'';
    }

    public function bmdc_no()
    { 
        return $this->doctor_course()->doctor->bmdc_no??'';
    }
    

}
