<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ConversationSms extends Model
{
    protected $table = 'conversation_sms';
    public function question_link()
    {
        return $this->belongsTo(Questionlink::class,'question_link_id','id');
    }

    public function user()
    {
        return $this->belongsTo(User::class,'sms_sender','id');
    }
    public function doctor()
    {
        return $this->belongsTo('App\Doctors','doctor_id','id');
    }
}
