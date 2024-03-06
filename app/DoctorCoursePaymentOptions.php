<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DoctorCoursePaymentOptions extends Model
{
    //
    public $timestamps = null;
    protected $table = 'doctor_course_payment_options';
    use SoftDeletes;

}
