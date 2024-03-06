<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SmsYearSms extends Model
{
    protected $table = 'sms_year_sms';
    use SoftDeletes;

    public function sms()
    {
        return $this->belongsTo('App\Sms','sms_id','id');
    }

    public function year_sms()
    {
        return $this->belongsTo('App\SmsYear','sms_year_id','id');
    }

    public function sms_year()
    {
        return $this->belongsTo('App\SmsYear','sms_year_id','id');
    }

    public $timestamps = false;

    
}
