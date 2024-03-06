<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SmsYear extends Model
{
    //protected $table = 'sms_links';
    protected $table = 'sms_year';
    use SoftDeletes;

    public function smss()
    {
        return $this->hasMany('App\SmsYearSms','sms_year_id','id');
    }

    public function institute()
    {
        return $this->belongsTo('App\Institutes','institute_id', 'id');
    }

    public function faculty()
    {
        return $this->belongsTo('App\Faculty','faculty_id', 'id');
    }

    public function subject()
    {
        return $this->belongsTo('App\Subjects','subject_id', 'id');
    }

    public function topic()
    {
        return $this->belongsTo('App\Topics','topic_id', 'id');
    }

    public function session()
    {
        return $this->belongsTo('App\Sessions','session_id', 'id');
    }

    public function branch()
    {
        return $this->belongsTo('App\Branch','branch_id', 'id');
    }

    public function faculty_subject()
    {
        return $this->belongsTo('App\Subject','subject_id', 'id');
    }
}
