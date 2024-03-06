<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Doctors;
use App\Batches;
use App\Courses;
class PaymentInfo extends Model
{
    protected $table = 'payment_info';

    public function doctor(){
        return $this->belongsTo('App\Doctors','doctor_id','id');
    }
    public function batch(){
        return $this->belongsTo('App\Batches','batch_id','id');
    }

    public function course(){
        return $this->belongsTo('App\Courses','course_id','id');
    }
}
