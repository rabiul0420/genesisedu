<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LectureSheetArticlePost extends Model
{
    protected $table = 'lecture_sheet_post';

    public function lecture_sheet_links()
    {
        return $this->belongsToMany('App\LectureSheetArticleBatch',['year','session_id','institute_id','course_id','topic_id'], ['year','session_id','institute_id','course_id','topic_id']);
    }

    public function institute()
    {
        return $this->belongsTo('App\Institutes','institute_id', 'id');
    }

    public function course()
    {
        return $this->belongsTo('App\Courses','course_id', 'id');
    }

    public function topic()
    {
        return $this->belongsTo('App\Topics','topic_id', 'id');
    }

    public function batch()
    {
        return $this->belongsTo('App\Batches','batch_id', 'id');
    }
    
}
