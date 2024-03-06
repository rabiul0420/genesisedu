<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LectureSheetTopicBatchLectureSheetTopic extends Model
{

    use SoftDeletes;
    protected $table = 'lecture_sheet_topic_batch_lecture_sheet_topic';

    public function lecture_sheet_topic()
    {
        return $this->belongsTo('App\LectureSheetTopic','lecture_sheet_topic_id','id');
        //return $this->hasMany('App\LectureSheet',['year','session_id','institute_id','course_id','topic_id'], ['year','session_id','institute_id','course_id','topic_id']);
    }

    public $timestamps = false;

    
}
