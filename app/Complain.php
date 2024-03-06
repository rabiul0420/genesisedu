<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Complain extends Model
{
    protected $table = 'complains';

    public function complain_reply(){
        return $this->hasMany('App\DoctorComplainReply','doctor_complain_id','id');
    }
    
    public function batch(){
        return $this->belongsTo('App\Batches','batch_id','id');
    }
    
    public function complain_reply_new(){
        return $this->hasOne(CourseComplainType::class,'id','course_complain_type_id');
    }


}
