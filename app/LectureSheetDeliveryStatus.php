<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LectureSheetDeliveryStatus extends Model
{
    protected $table = 'lecture_sheet_delivery_status';

    public function courier(){
        return $this->belongsTo('App\Courier','courier_id','id');
    }

    public function doctor_course(){
        return $this->belongsTo('App\DoctorsCourses','doctor_course_id','id');
    }
   
}
