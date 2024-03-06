<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DoctorCoursePayment extends Model
{

    protected $casts = [
        'created_at' => 'datetime'
    ];

    protected $table = 'doctor_course_payment';

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
}
