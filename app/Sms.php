<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sms extends Model
{
    protected $table = 'sms';
    use SoftDeletes;

    public function user()
    {
        return $this->belongsTo('App\User','created_by','id');
    }

    public function doctor_smss()
    {
        return $this->hasMany('App\DoctorSmss','sms_id','id');
    }  
    
    public function faculties()
    {
        return $this->hasMany('App\SmsFaculty','sms_id','id');
    } 

    public function disciplines()
    {
        return $this->hasMany('App\SmsDiscipline','sms_id','id');
    }
    
    public function sms_event()
    {
        return $this->belongsTo('App\SmsEvent','sms_event_id','id');
    }
    
}
