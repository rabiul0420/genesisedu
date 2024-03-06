<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OnlineExamBatchOnlineExam extends Model
{
    protected $table = 'online_exam_batch_online_exam';

    public function exam()
    {
        return $this->belongsTo('App\OnlineExam','online_exam_id','id');
        //return $this->hasMany('App\OnlineExam',['year','session_id','institute_id','course_id','topic_id'], ['year','session_id','institute_id','course_id','topic_id']);
    }

    public $timestamps = false;

    
}
