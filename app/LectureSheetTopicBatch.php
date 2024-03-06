<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LectureSheetTopicBatch extends Model
{
    use SoftDeletes;
    
    protected $table = 'lecture_sheet_topic_batch';

    public function lecture_sheet_topics()
    {
        return $this->hasMany('App\LectureSheetTopicBatchLectureSheetTopic','lecture_sheet_topic_batch_id', 'id');
    }

    public function institute()
    {
        return $this->belongsTo('App\Institutes','institute_id', 'id');
    }

    public function branch()
    {
        return $this->belongsTo('App\Branch','branch_id', 'id');
    }



    public function course()
    {
        return $this->belongsTo('App\Courses','course_id', 'id');
    }

    public function faculty()
    {
        return $this->belongsTo('App\Faculty','faculty_id', 'id');
    }

    public function subject()
    {
        return $this->belongsTo('App\Subjects','subject_id', 'id');
    }

    public function topic()
    {
        return $this->belongsTo('App\Topics','topic_id', 'id');
    }

    public function batch()
    {
        return $this->belongsTo('App\Batches','batch_id', 'id');
    }

    public function session()
    {
        return $this->belongsTo('App\Sessions','session_id', 'id');
    }

    public function faculty_subject()
    {
        return $this->belongsTo('App\Subject','subject_id', 'id');
    }
}
