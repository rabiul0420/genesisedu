<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DiscountRequest extends Model
{
    protected $table ='discount_request';

    public function doctor(){
        return $this->belongsTo('App\Doctors','doctor_id','id');
    }
    public function doctor_course(){
        return $this->belongsTo('App\DoctorsCourses','course_id','id');
    }
}
