<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DoctorAskReply extends Model
{
    protected $table = 'doctor_ask_reply';

    public function doctor()
    {
        return $this->belongsTo('App\Doctors','doctor_id','id');
    }

    public function doctor_ask()
    {
        return $this->belongsTo('App\DoctorAsks','doctor_ask_id','id');
    }

    public function user()
    {
        return $this->belongsTo('App\User','user_id','id');
    }
  
    public function doctorname()
    {
        return $this->belongsTo('App\Doctors','doctor_id','id');
    }
}
