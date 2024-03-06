<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PaymentVerificationNote extends Model
{
    //
    const UPDATED_AT = NULL;
    public function doctor(){
        return $this->belongsTo('App\User','verified_by','id');
    }
    
    public function doctor_course_payment(){
        return $this->belongsTo('App\DoctorCoursePayment','course_payment_id','id');
    }
}
