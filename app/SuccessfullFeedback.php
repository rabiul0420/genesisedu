<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SuccessfullFeedback extends Model
{
    protected $table = "successfull_feedback";
    public function medical_college()
    {
        return $this->belongsTo('App\MedicalColleges','medical_college_id','id');
    }
      
}
    