<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class LectureSheetTopic extends Model
{
    use SoftDeletes;

    protected $table = 'lecture_sheet_topic';

    public function institute()
    {
        return $this->belongsTo('App\Institutes','institute_id', 'id');
    }

    public function course()
    {
        return $this->belongsTo('App\Courses','course_id', 'id');
    }

    public function session()
    {
        return $this->belongsTo('App\Sessions','session_id', 'id');
    }

    public function lecture_sheets()
    {
        return $this->belongsToMany(LectureSheet::class,'lecture_sheet_topic_lecture_sheet','lecture_sheet_topic_id','lecture_sheet_id')
            ->whereNull('lecture_sheet_topic_lecture_sheet.deleted_at');
    }

    public function view_lecture_sheets( )
    {
        return $this->belongsToMany('App\LectureSheetTopicBatchLectureSheetTopic','lecture_sheet_topic_batch_id','id');
    }

    function lecture_sheet_topic_batches( ){
        return $this->belongsToMany(LectureSheetTopicBatch::class,'lecture_sheet_topic_batch_lecture_sheet_topic','lecture_sheet_topic_id','lecture_sheet_topic_batch_id')
            ->whereNull('lecture_sheet_topic_batch_lecture_sheet_topic.deleted_at');
    }

    //public $timestamps = false;
    
}
