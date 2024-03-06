<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SmsEvent extends Model
{
    protected $table = 'sms_event';
    use SoftDeletes;

    public function smss()
    {
        $this->hasMany("App\Sms",'sms_event_id','id');
    }

}
