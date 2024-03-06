<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DoctorCourseLectureSheet extends Model
{
    use SoftDeletes;

    protected $table = 'doctor_course_lecture_sheet';
    public $timestamps = false;


    public function doctor_course()
    {
        return $this->belongsTo('App\DoctorsCourses','doctor_course_id', 'id');
    }

    public function lecture_sheet()
    {
        return $this->belongsTo('App\LectureSheet','lecture_sheet_id', 'id');
    }


}
