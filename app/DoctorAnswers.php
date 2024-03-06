<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DoctorAnswers extends Model
{
    use SoftDeletes;
    protected $table = 'doctor_answers';

    public function doctor_courses()
    {
        return $this->belongsTo('App\DoctorsCourses','doctor_course_id', 'id');
    }

    public function doctor_course()
    {
        return $this->belongsTo('App\DoctorsCourses','doctor_course_id', 'id');
    }

    public function doctor_package()
    {
        return $this->belongsTo('App\DoctorPackage','doctor_package_id', 'id');
    }

    public function exam()
    {
        return $this->belongsTo('App\Exam','exam_id', 'id');
    }

    public function exam_question()
    {
        return $this->belongsTo('App\Exam_question','exam_question_id', 'id');
    }

}
