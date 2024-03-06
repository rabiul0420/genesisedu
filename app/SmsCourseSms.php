<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SmsCourseSms extends Model
{
    protected $table = 'sms_course_sms';
    use  SoftDeletes;

    public function sms()
    {
        return $this->belongsTo('App\Sms','sms_id','id');
    }

    public function course_sms()
    {
        return $this->belongsTo('App\SmsCourse','sms_course_id','id');
    }

    public function sms_course()
    {
        return $this->belongsTo('App\SmsCourse','sms_course_id','id');
    }

    public $timestamps = false;

    
}
