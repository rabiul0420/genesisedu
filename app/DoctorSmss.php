<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DoctorSmss extends Model
{
    use  SoftDeletes;
    protected $table = 'doctor_sms';
    public $timestamps = false;

    public function doctor()
    {
        return $this->belongsTo('App\Doctors','doctor_id','id');
    }

    public function sms()
    {
        return $this->belongsTo('App\Sms','sms_id','id');
    }

    
    
}
