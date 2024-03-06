<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LectureSheetArticleBatchLectureSheetArticle extends Model
{
    protected $table = 'lecture_sheet_article_batch_lecture_sheet_article';

    public function lecture_sheet_articles()
    {
        //return $this->belongsToMany('App\LectureSheet');
        return $this->hasMany('App\LectureSheetArticle',['year','session_id','institute_id','course_id','topic_id'], ['year','session_id','institute_id','course_id','topic_id']);
    }

    public function lecture_sheet_article()
    {
        return $this->belongsTo('App\lecture_sheet_article','lecture_sheet_article_id', 'id');
    }



    public $timestamps = false;
}
