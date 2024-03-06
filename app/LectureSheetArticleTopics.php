<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LectureSheetArticleTopics extends Model
{
    protected $table = 'lecture_sheet_article_topics';

    public function topic()
    {
        return $this->belongsTo('App\Topics','topic_id','id');
        //return $this->hasMany('App\LectureSheet',['year','session_id','institute_id','course_id','topic_id'], ['year','session_id','institute_id','course_id','topic_id']);
    }

    public $timestamps = false;

    
}
