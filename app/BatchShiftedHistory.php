<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Symfony\Component\Console\Helper\Table;

class BatchShiftedHistory extends Model
{
    protected $table = 'batch_shifted_history';
     
    public function doctor_course(){
       return $this->belongsTo(DoctorsCourses::class,'doctor_course_id','id');
    }
}
