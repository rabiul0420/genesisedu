<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DoctorQuestion extends Model
{
    protected $table = 'doctor_question';

    public function doctorname()
    {
        return $this->belongsTo('App\Doctors','doctor_id','id');
    }
    
    public function lecturename()
    {
        return $this->belongsTo('App\OnlineLectureAddress','lecture_id','id');
    }

     public function batchname()
    {
        return $this->belongsTo('App\Batches','batch_id','id');
    }

}
