<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DoctorComplainReply extends Model
{
    // protected $table = 'doctor_complain_reply';
    protected $table = 'complain_conversations';

    public function complain()
    {
        return $this->belongsTo('App\Complain','doctor_id','id');
    }

    public function doctor()
    {
        return $this->belongsTo('App\Doctors','doctor_id','id');
    }

    public function user()
    {
        return $this->belongsTo('App\User','user_id','id');
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
