<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DoctorComplain extends Model
{
    protected $table = 'doctor_complains';

    public function doctorname()
    {
        return $this->belongsTo('App\Doctors','doctor_id','id');
    }

    public function doctor()
    {
        return $this->belongsTo('App\Doctors','doctor_id','id');
    }

    public function doctor_complains_replies()
    {
        return $this->hasMany('App\DoctorComplainReply','doctor_complain_id','id');
    }

    public function count_unread()
    {
        return $this->hasMany('App\DoctorComplainReply','doctor_complain_id','id')->get()->where('is_read','No')->count();
    }
    
}
