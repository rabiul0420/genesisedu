<?php

namespace App;

// use App\ConversationSms;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AutoReply extends Model
{

    use SoftDeletes;

    protected $table ='auto_reply_link';

    // public function question(){
    //     return $this->belongsTo(ConversationSms::class,'question_link_id','id');
    // }

  

}
